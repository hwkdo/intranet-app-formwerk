<?php

namespace Hwkdo\IntranetAppFormwerk\Webhooks\SignatureValidators;

use Hwkdo\IntranetAppFormwerk\Models\Typ;
use Illuminate\Http\Request;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class FormwerkSignatureValidator implements SignatureValidator
{
    public function isValid(Request $request, WebhookConfig $config): bool
    {        
        $config = collect(config('webhook-client.configs'))->where('name','formwerk')->first();        
        Log::info('FormwerkSignatureValidator', [$request->header($config['signature_header_name'])]);
        foreach(Typ::all() as $typ) {
            if(Hash::check($typ->token,$request->header($config['signature_header_name']))) {
                Log::info('FormwerkSignatureValidator', ['Typ found: ' . $typ->name]);
                return true;
            } else {
                Log::info('FormwerkSignatureValidator', ['Typ not valid for signature: ' . $typ->name]);
            }
        }
        Log::info('FormwerkSignatureValidator', ['No Typ found']);
        return false;
    }
}