<?php

namespace App\Mail;

use App\Models\Reserva\Res_condicion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservaInmueble extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre;
    public $texto;
    public $asunto;
    public $condiciones;
    public $lista_condiciones;

    /**
     * Create a new message instance.
     */
    public function __construct(string $nombre, string $texto, string $asunto = 'Sistema de reservas Corpentunida', string $condiciones = null)
    {
        $this->nombre = $nombre;
        $this->texto = $texto;
        $this->asunto = $asunto;
        $this->condiciones = $condiciones;
        $this->lista_condiciones = Res_condicion::all();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->asunto,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reserva',
        );
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
