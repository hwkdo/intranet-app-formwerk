<?php

declare(strict_types=1);

namespace Hwkdo\IntranetAppFormwerk\Jobs;

use Hwkdo\IntranetAppFormwerk\Mail\MitarbeiterOnboardingDone;
use Hwkdo\IntranetAppFormwerk\Models\Typ;
use Hwkdo\IntranetAppFormwerk\Models\TypHasWebhook;
use Hwkdo\IntranetAppFormwerk\Support\FormwerkModels;
use Hwkdo\MsGraphLaravel\Interfaces\MsGraphMailServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Microsoft\Graph\Generated\Models\FileAttachment;
use Throwable;

/**
 * Verarbeitet die Formwerk-Onboarding-PDF-Mail (Legacy: ProcessMaOnboarding).
 *
 * Erwarteter Betreff (Formwerk-Konvention):
 *   onboarding {username} ##{formwerk_uuid}##
 *
 * Ablauf: ProcessFormwerkMail → dispatch(mailResource, identifier=username, uuid)
 */
class ProcessMaOnboarding implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $mailResource,
        public string $identifier,
        public ?string $uuid = null,
    ) {}

    public function handle(): void
    {
        $typ = Typ::query()->where('form_identifier', 'onboarding')->first();
        if (! $typ) {
            Log::error('ProcessMaOnboarding: Typ onboarding nicht gefunden');

            return;
        }

        $mailService = app(MsGraphMailServiceInterface::class);
        $mail = $mailService->get($this->mailResource);

        if (! $mail->getHasAttachments()) {
            Log::warning('ProcessMaOnboarding: Mail ohne Anhänge', [
                'identifier' => $this->identifier,
                'uuid' => $this->uuid,
            ]);

            return;
        }

        $upn = str($this->mailResource)->after('Users/')->before('/Messages/')->value();
        $messageId = str($this->mailResource)->after('/Messages/')->value();
        $attachments = $mailService->listAttachmentsByUpnAndId($upn, $messageId);

        $savedPdf = null;
        $targetDir = rtrim((string) $typ->filepath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        $targetFilename = strtolower($this->identifier).'.pdf';

        if ($attachments?->getValue()) {
            foreach ($attachments->getValue() as $attachment) {
                if (! $attachment instanceof FileAttachment) {
                    continue;
                }

                $contentType = (string) ($attachment->getContentType() ?? '');
                $name = (string) ($attachment->getName() ?? '');
                $isPdf = str_contains(strtolower($contentType), 'pdf')
                    || str_ends_with(strtolower($name), '.pdf');

                if (! $isPdf) {
                    continue;
                }

                File::ensureDirectoryExists($targetDir);
                $savedPdf = $mailService->saveAttachment($attachment, $targetDir, $targetFilename);
                Log::info('ProcessMaOnboarding: PDF gespeichert', [
                    'path' => $savedPdf,
                    'identifier' => $this->identifier,
                ]);
                break;
            }
        }

        if ($savedPdf === null) {
            Log::error('ProcessMaOnboarding: Kein PDF-Anhang gefunden', [
                'identifier' => $this->identifier,
                'uuid' => $this->uuid,
            ]);

            return;
        }

        $userClass = FormwerkModels::user();
        $user = $userClass::query()->where('username', $this->identifier)->first();
        if (! $user) {
            Log::error('ProcessMaOnboarding: User nicht gefunden', [
                'username' => $this->identifier,
            ]);

            return;
        }

        $user->forceFill(['onboarding_dokumente' => true])->save();

        try {
            $mailService->setRead($upn, $messageId);
        } catch (Throwable $e) {
            Log::warning('ProcessMaOnboarding: setRead fehlgeschlagen', [
                'message' => $e->getMessage(),
            ]);
        }

        $config = is_array($typ->config) ? $typ->config : [];
        if (($config['send_success_mail'] ?? false) && filled($config['mail_empfaenger'] ?? null)) {
            $pdfUrl = $this->resolvePdfViewUrl();
            Mail::to($config['mail_empfaenger'])
                ->queue(new MitarbeiterOnboardingDone($user, $pdfUrl));
        }

        Log::info('ProcessMaOnboarding: abgeschlossen', [
            'username' => $this->identifier,
            'user_id' => $user->getKey(),
            'uuid' => $this->uuid,
            'pdf' => $savedPdf,
        ]);
    }

    private function resolvePdfViewUrl(): ?string
    {
        if ($this->uuid === null || $this->uuid === '') {
            return null;
        }

        $pivot = TypHasWebhook::query()
            ->where('formwerk_uuid', $this->uuid)
            ->latest('id')
            ->first();

        if (! $pivot) {
            return null;
        }

        return route('apps.formwerk.webhooks.show-mail', $pivot->id);
    }
}
