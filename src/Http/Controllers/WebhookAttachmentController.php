<?php

namespace Hwkdo\IntranetAppFormwerk\Http\Controllers;

use Hwkdo\IntranetAppFormwerk\Models\TypHasWebhook;
use Hwkdo\MsGraphLaravel\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Microsoft\Graph\Generated\Models\FileAttachment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WebhookAttachmentController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $webhookId): StreamedResponse
    {
        $attachmentId = $request->query('id');

        abort_if(! $attachmentId, 404, 'Anhang-ID fehlt');

        $webhook = TypHasWebhook::findOrFail($webhookId);

        abort_if(! $webhook->ms_graph_mail_resource, 404, 'Mail resource nicht gefunden');

        $resource = $webhook->ms_graph_mail_resource;
        $upn = str($resource)->after('Users/')->before('/Messages/')->value();
        $messageId = str($resource)->after('/Messages/')->value();

        // Erstelle eine Instanz des MailService
        $mailService = new MailService;

        // Hole die Anhänge erneut, um das vollständige FileAttachment-Objekt zu bekommen
        $attachmentsResponse = $mailService::listAttachmentsByUpnAndId($upn, $messageId);
        $attachmentsList = $attachmentsResponse?->getValue() ?? [];

        // Finde den spezifischen Anhang (verwende getId() Methode, nicht id Property)
        $attachment = collect($attachmentsList)->first(fn ($a) => $a->getId() === $attachmentId);

        abort_if(! $attachment || ! ($attachment instanceof FileAttachment), 404, 'Anhang nicht gefunden');

        // Hole den Inhalt und erstelle einen temporären Download
        $content = $mailService::getAttachmentContent($attachment);
        $filename = $attachment->getName();

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, [
            'Content-Type' => $attachment->getContentType() ?? 'application/octet-stream',
        ]);
    }
}

