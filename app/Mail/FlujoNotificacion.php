<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FlujoNotificacion extends Mailable
{
    use Queueable, SerializesModels;
    public $idCorrespondencia;

    public function __construct($idCorrespondencia, $nombreProceso)
    {
        $this->idCorrespondencia = $idCorrespondencia;
        $this->nombreProceso = $nombreProceso;
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.flujoNotificacion',
            with: [
                'idCorrespondencia' => $this->idCorrespondencia,
                'nombreProceso' => $this->nombreProceso,
            ],
        );
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(subject: "Correspondencia pendiente #{$this->idCorrespondencia}");
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
