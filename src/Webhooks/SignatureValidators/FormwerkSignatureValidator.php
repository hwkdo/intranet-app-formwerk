<?php

declare(strict_types=1);

namespace Hwkdo\IntranetAppFormwerk\Webhooks\SignatureValidators;

use Hwkdo\IntranetAppFormwerk\Models\Typ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

class FormwerkSignatureValidator implements SignatureValidator
{
    /**
     * @var list<string>
     */
    private const ALTERNATE_SIGNATURE_HEADERS = [
        'x-signature',
        'signature',
        'x-formwerk-signature',
        'x-webhook-signature',
        'authorization',
    ];

    public function isValid(Request $request, WebhookConfig $config): bool
    {
        $formwerkConfig = collect(config('webhook-client.configs'))->where('name', 'formwerk')->first();
        $signatureHeaderName = $formwerkConfig['signature_header_name'] ?? 'x-signature';
        $signature = $request->header($signatureHeaderName);

        Log::info('FormwerkSignatureValidator', [$signature]);

        if (filled($signature)) {
            foreach (Typ::all() as $typ) {
                if (Hash::check($typ->token, $signature)) {
                    Log::info('FormwerkSignatureValidator', ['Typ found: '.$typ->name]);

                    return true;
                }

                Log::info('FormwerkSignatureValidator', ['Typ not valid for signature: '.$typ->name]);
            }
        }

        $this->dumpIncomingRequest($request, $signatureHeaderName, $signature);

        Log::info('FormwerkSignatureValidator', ['No Typ found']);

        return false;
    }

    private function dumpIncomingRequest(Request $request, string $expectedHeader, mixed $signature): void
    {
        $alternateHeaders = [];
        foreach (self::ALTERNATE_SIGNATURE_HEADERS as $header) {
            $value = $request->header($header);
            if ($value !== null) {
                $alternateHeaders[$header] = $value;
            }
        }

        $payload = $request->all();
        $payloadFields = [];
        $signatureLikePayload = [];

        if (is_array($payload)) {
            foreach ($payload as $index => $field) {
                if (! is_array($field)) {
                    $payloadFields[] = [
                        'index' => $index,
                        'type' => gettype($field),
                        'preview' => is_scalar($field) ? mb_substr((string) $field, 0, 80) : null,
                    ];

                    continue;
                }

                $name = (string) ($field['name'] ?? '');
                $value = $field['value'] ?? null;
                $payloadFields[] = [
                    'index' => $index,
                    'name' => $name,
                    'value_type' => gettype($value),
                    'value_preview' => is_scalar($value) ? mb_substr((string) $value, 0, 120) : (is_array($value) ? 'array('.count($value).')' : null),
                ];

                if ($name !== '' && preg_match('/sign|token|secret|auth/i', $name) === 1) {
                    $signatureLikePayload[] = [
                        'name' => $name,
                        'value_preview' => is_scalar($value) ? mb_substr((string) $value, 0, 200) : json_encode($value),
                    ];
                }
            }
        }

        Log::warning('FormwerkSignatureValidator dump (invalid/missing signature)', [
            'expected_header' => $expectedHeader,
            'expected_header_value' => $signature,
            'all_headers' => $request->headers->all(),
            'alternate_signature_headers' => $alternateHeaders,
            'content_type' => $request->header('content-type'),
            'method' => $request->method(),
            'path' => $request->path(),
            'query' => $request->query(),
            'payload_field_count' => is_array($payload) ? count($payload) : 0,
            'payload_fields' => $payloadFields,
            'signature_like_payload_fields' => $signatureLikePayload,
            'raw_content_preview' => mb_substr($request->getContent(), 0, 2000),
        ]);
    }
}
