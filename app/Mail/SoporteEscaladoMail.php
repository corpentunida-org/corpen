<?php

namespace App\Mail;

use App\Models\Soportes\ScpSoporte;
use App\Models\Soportes\ScpObservacion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Address;

class SoporteEscaladoMail extends Mailable
{
    public ScpSoporte $soporte;
    public ?ScpObservacion $observacion;
    public string $destinatarioTipo;

    public function __construct(ScpSoporte $soporte, string $destinatarioTipo = 'escalado', ?ScpObservacion $observacion = null)
    {
        $this->soporte = $soporte;
        $this->destinatarioTipo = $destinatarioTipo;
        $this->observacion = $observacion;
    }

    public function envelope(): Envelope
    {
        return new Envelope(from: new Address(config('mail.from.address'), config('mail.from.name')), subject: 'Soporte escalado #' . $this->soporte->id);
    }

    public function content(): Content
    {
        // Antes SIEMPRE mandaba 'escalado_usuario' (la vista pensada para quien RECIBE el ticket),
        // incluso al avisarle al creador que SU ticket fue escalado — existía una plantilla
        // dedicada (escalado_creador) que nunca se usaba porque $destinatarioTipo nunca se leía.
        $vista = $this->destinatarioTipo === 'creador'
            ? 'emails.soportes.escalado_creador'
            : 'emails.soportes.escalado_usuario';

        return new Content(view: $vista);
    }
}
