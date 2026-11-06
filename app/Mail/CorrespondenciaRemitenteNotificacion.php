<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Lo que recibe el remitente cuando se marca "Notificar al Remitente" en una gestión. No lleva
 * enlace al sistema: el remitente es un tercero y normalmente no tiene usuario.
 */
class CorrespondenciaRemitenteNotificacion extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $idCorrespondencia,
        public $asunto,
        public $nombreRemitente,
        public $nombreEstado,
        public $observacion,
        public array $rutasArchivos = [],
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Actualización de su radicado #{$this->idCorrespondencia}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.correspondenciaRemitente');
    }

    /**
     * Los mismos documentos adjuntados a la gestión, leídos de S3 al enviar.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return array_map(
            fn ($ruta) => Attachment::fromStorageDisk('s3', $ruta)->as(basename($ruta)),
            $this->rutasArchivos,
        );
    }
}
