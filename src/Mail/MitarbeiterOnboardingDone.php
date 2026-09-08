<?php

declare(strict_types=1);

namespace Hwkdo\IntranetAppFormwerk\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MitarbeiterOnboardingDone extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Model $user,
        public ?string $pdfUrl = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Onboarding abgeschlossen',
        );
    }

    public function content(): Content
    {
        $name = trim((string) (($this->user->vorname ?? '').' '.($this->user->nachname ?? '')));
        if ($name === '') {
            $name = (string) ($this->user->username ?? $this->user->email ?? 'Unbekannt');
        }

        return new Content(
            markdown: 'intranet-app-formwerk::emails.onboarding-done',
            with: [
                'name' => $name,
                'username' => $this->user->username ?? null,
                'pdfUrl' => $this->pdfUrl,
            ],
        );
    }
}
