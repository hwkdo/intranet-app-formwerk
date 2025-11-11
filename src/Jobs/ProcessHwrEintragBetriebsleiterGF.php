<?php

namespace Hwkdo\IntranetAppFormwerk\Jobs;

use Hwkdo\IntranetAppFormwerk\Models\Typ;
use Hwkdo\IntranetAppHwro\Models\Dokument;
use Hwkdo\IntranetAppHwro\Models\Vorgang;
use Hwkdo\IntranetAppHwro\Services\SchlagwortService;
use Hwkdo\MsGraphLaravel\Interfaces\MsGraphMailServiceInterface;
use Hwkdo\MsGraphLaravel\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessHwrEintragBetriebsleiterGF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $mailResource,
        public string $identifier,
        public ?string $uuid = null
        )
    {
        //
    }

    public function handle()
    {
        $mailService = app(MsGraphMailServiceInterface::class);
        $mail = $mailService->get($this->mailResource);
        $typ = Typ::where('form_identifier', 'hwreintrag-betriebsleiter-gf')->first();
        if($this->uuid) {
            $webhook = $typ->webhooks()->latest()->wherePivot('formwerk_uuid', $this->uuid)->first()->pivot;
            Log::info('ProcessHwrEintragBetriebsleiterGF - Found webhhok with uuid: ' . $this->uuid);
        } else {
            $webhook = $typ->webhooks()->latest()->wherePivot('identifier', $this->identifier)->first()->pivot;
            Log::info('ProcessHwrEintragBetriebsleiterGF - Found webhhok with identifier: ' . $this->identifier);
        }

        // Vorgang ermitteln
        $vorgang = Vorgang::where('vorgangsnummer', $this->identifier)->first();
        
        if (!$vorgang) {
            Log::warning("Vorgang mit Vorgangsnummer '{$this->identifier}' nicht gefunden. Vorgang wird erstellt.");
            $vorgang = Vorgang::create([
                'vorgangsnummer' => $this->identifier,
            ]);
        }
        
        // E-Mail-Anhänge verarbeiten
        $upn = str($this->mailResource)->after("Users/")->before("/Messages/")->value();
        $messageId = str($this->mailResource)->after("/Messages/")->value();
        
        $attachments = MailService::listAttachmentsByUpnAndId($upn, $messageId);
        
        if ($attachments && $attachments->getValue()) {
            Log::info("Anzahl E-Mail-Anhänge: " . count($attachments->getValue()));
            
            foreach ($attachments->getValue() as $attachment) {
                if ($attachment instanceof \Microsoft\Graph\Generated\Models\FileAttachment) {
                    $filename = $attachment->getName();
                    Log::info("Verarbeite E-Mail-Anhang: {$filename}");
                    
                    // Schlagwort ermitteln
                    $schlagwort = app(SchlagwortService::class)->findByFilename($filename);
                    
                    // Dokument erstellen
                    $dokument = Dokument::create([
                        'vorgang_id' => $vorgang->id,
                        'schlagwort_id' => $schlagwort->id,
                    ]);
                    
                    // Datei-Content holen und speichern
                    $content = MailService::getAttachmentContent($attachment);
                    $dokument->addMediaFromString($content)
                        ->usingFileName($filename)
                        ->toMediaCollection('default');
                    
                    Log::info("E-Mail-Anhang als Dokument gespeichert: ID={$dokument->id}, Datei={$filename}");
                }
            }
        } else {
            Log::info("Keine E-Mail-Anhänge gefunden.");
        }

        Log::info('ProcessHwrEintragBetriebsleiterGF abgeschlossen', [
            'subject' => $mail->getSubject(),
            'identifier' => $this->identifier,
            'uuid' => $this->uuid,
            'vorgang_id' => $vorgang->id,
        ]);
    }
}
