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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessHwrEintragungSelfService implements ShouldQueue
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
        $typ = Typ::where('form_identifier', 'hwreintrag-selfservice')->first();
        if($this->uuid) {
            $webhook = $typ->webhooks()->latest()->wherePivot('formwerk_uuid', $this->uuid)->first()->pivot;
            Log::info('ProcessHwrEintragungSelfService - Found webhhok with uuid: ' . $this->uuid);
        } else {
            $webhook = $typ->webhooks()->latest()->wherePivot('identifier', $this->identifier)->first()->pivot;
            Log::info('ProcessHwrEintragungSelfService - Found webhhok with identifier: ' . $this->identifier);
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

        // E-Mail Body extrahieren und Download-Links aus der HTML-E-Mail auslesen
        $emailBody = $mail->getBody()->getContent();

        // Download-Links aus der HTML-E-Mail auslesen
        $downloadLinks = $this->extractDownloadLinks($emailBody);
        Log::info('Extrahierte Download-Links: ' . count($downloadLinks));

        foreach ($downloadLinks as $downloadLink) {
            $filename = $downloadLink['filename'];
            Log::info("Verarbeite Download-Link: {$filename}");
            
            // Datei herunterladen
            $response = Http::get($downloadLink['url']);
            
            if ($response->successful()) {
                // Schlagwort ermitteln
                $schlagwort = app(SchlagwortService::class)->findByFilename($filename);

                // Dokument erstellen
                $dokument = Dokument::create([
                    'vorgang_id' => $vorgang->id,
                    'schlagwort_id' => $schlagwort->id,
                ]);
                
                // Datei-Content direkt an MediaLibrary übergeben
                $dokument->addMediaFromString($response->body())
                    ->usingFileName($filename)
                    ->toMediaCollection('default');
                
                Log::info("Download-Link als Dokument gespeichert: ID={$dokument->id}, Datei={$filename}");
            } else {
                Log::error("Fehler beim Herunterladen von {$downloadLink['url']}: HTTP {$response->status()}");
            }
        }

        Log::info('ProcessHwrEintragungSelfService abgeschlossen', [
            'subject' => $mail->getSubject(),
            'identifier' => $this->identifier,
            'uuid' => $this->uuid,
            'vorgang_id' => $vorgang->id,
        ]);
    }

    /**
     * Extrahiert Download-Links aus HTML-E-Mail-Body nach dem Text "auch direkt aufrufen:"
     *
     * @param  string  $htmlBody  Der HTML-Inhalt der E-Mail
     * @return array Array mit gefundenen Links [['url' => '...', 'filename' => '...'], ...]
     */
    private function extractDownloadLinks($htmlBody)
    {
        $links = [];

        if (empty($htmlBody)) {
            Log::warning('HTML Body ist leer');

            return $links;
        }

        // HTML-Entitäten dekodieren
        $decodedBody = html_entity_decode($htmlBody, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        Log::info('Dekodierter Body (erste 500 Zeichen): '.substr($decodedBody, 0, 500));

        // Verbesserte Suche nach dem Text "auch direkt aufrufen:"
        // Flexiblerer Pattern, der verschiedene HTML-Strukturen berücksichtigt
        $patterns = [
            // Pattern 1: Exakter Text aus Ihrem Beispiel mit "auch direkt aufrufen:"
            '/auch\s+direkt\s+aufrufen:\s*<\/?\w*>?\s*<ul[^>]*>(.*?)<\/ul>/is',
            // Pattern 2: Flexibler - "auch direkt aufrufen" mit optional ":"
            '/auch\s+direkt\s+aufrufen:?\s*.*?<ul[^>]*>(.*?)<\/ul>/is',
            // Pattern 3: Noch robuster - behandle HTML-Entitäten und Tags
            '/auch[^<]*direkt[^<]*aufrufen[^<]*<[^>]*>.*?<ul[^>]*>(.*?)<\/ul>/is',
            // Pattern 4: Suche nur nach <ul> wenn der Text davor steht
            '/hinzugef.*?aufrufen.*?<ul[^>]*>(.*?)<\/ul>/is',
            // Pattern 5: Einfachster Pattern - erste <ul> nach "aufrufen"
            '/aufrufen.*?<ul[^>]*>(.*?)<\/ul>/is',
        ];

        $ulContent = null;
        foreach ($patterns as $index => $pattern) {
            if (preg_match($pattern, $decodedBody, $matches)) {
                $ulContent = $matches[1];
                Log::info("Pattern $index erfolgreich - UL Content gefunden: ".substr($ulContent, 0, 200));
                break;
            }
        }

        if ($ulContent === null) {
            Log::warning('Kein UL-Content nach "auch direkt aufrufen" gefunden');
            // Debug: Schaue ob der Text überhaupt existiert
            if (stripos($decodedBody, 'auch direkt aufrufen') !== false) {
                Log::info('Text "auch direkt aufrufen" wurde gefunden, aber keine UL-Liste danach');
            } else {
                Log::warning('Text "auch direkt aufrufen" wurde gar nicht gefunden');
            }

            return $links;
        }

        // Verbesserte Link-Extraktion - verschiedene Patterns probieren
        $linkPatterns = [
            // Pattern 1: Standard <li> mit <a> Tag - erlaubt Leerzeichen um href
            '/<li[^>]*>.*?<a[^>]*href\s*=\s*["\']([^"\']+)["\'][^>]*>([^<]+)<\/a>.*?<\/li>/is',
            // Pattern 2: Direkter <a> Tag ohne umschließende Struktur
            '/<a[^>]*href\s*=\s*["\']([^"\']+)["\'][^>]*>([^<]+)<\/a>/is',
            // Pattern 3: Extrem flexibel - jeder <a> Tag mit href
            '/<a[^>]*href\s*=\s*["\']([^"\']*)["\'][^>]*>([^<]*)<\/a>/is',
            // Pattern 4: Speziell für Ihr Beispiel - mit target="_blank"
            '/<a\s+target="_blank"\s+href=["\']([^"\']+)["\'][^>]*>([^<]+)<\/a>/is',
        ];

        foreach ($linkPatterns as $index => $linkPattern) {
            if (preg_match_all($linkPattern, $ulContent, $linkMatches, PREG_SET_ORDER)) {
                Log::info("Link Pattern $index erfolgreich - ".count($linkMatches).' Links gefunden');

                foreach ($linkMatches as $linkMatch) {
                    $url = trim($linkMatch[1]);
                    $filename = trim(strip_tags($linkMatch[2]));

                    Log::info("Gefundener Link: URL=$url, Filename=$filename");

                    if (! empty($url) && ! empty($filename)) {
                        $links[] = [
                            'url' => $url,
                            'filename' => $filename,
                        ];
                    }
                }
                break; // Wenn ein Pattern funktioniert, stoppe
            }
        }

        if (empty($links)) {
            Log::warning('Keine Links extrahiert. UL Content für Debug: '.$ulContent);
        }

        return $links;
    }
}
