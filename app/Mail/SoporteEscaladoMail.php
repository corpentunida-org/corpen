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
    public ScpObservacion $observacion;
    public string $destinatarioTipo;

    public function __construct(ScpSoporte $soporte, string $destinatarioTipo = 'escalado')
    {
        $this->soporte = $soporte;
        $this->destinatarioTipo = $destinatarioTipo;
    }

    public function envelope(): Envelope
    {
        return new Envelope(from: new Address(config('mail.from.address'), config('mail.from.name')), subject: 'Soporte escalado #' . $this->soporte->id);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.soportes.escalado_usuario');
    }
}
