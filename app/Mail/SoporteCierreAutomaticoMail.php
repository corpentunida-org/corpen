<?php

namespace App\Mail;

use App\Models\Soportes\ScpSoporte;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Address;

/**
 * Aviso de que un soporte se cerró SOLO, por vencimiento de 5 días en "En Revisión" (ver
 * CierreAutomaticoService) — no porque alguien lo haya resuelto de verdad. Dos destinatarios
 * posibles, cada uno con su propio mensaje (mismo patrón que SoporteEscaladoMail):
 *  - 'revisor': quien tenía el soporte asignado y no llegó a moverlo del estado Revisión.
 *  - 'creador': quien lo reportó — para que sepa que el cierre fue automático, no una solución
 *    real, y pueda reabrirlo si su problema sigue sin resolver.
 */
class SoporteCierreAutomaticoMail extends Mailable
{
    public ScpSoporte $soporte;
    public string $destinatarioTipo;
    public int $diasEspera;

    public function __construct(ScpSoporte $soporte, string $destinatarioTipo = 'creador', int $diasEspera = 5)
    {
        $this->soporte = $soporte;
        $this->destinatarioTipo = $destinatarioTipo;
        $this->diasEspera = $diasEspera;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Soporte #'.$this->soporte->id.' cerrado automáticamente por vencimiento de tiempos',
        );
    }

    public function content(): Content
    {
        $vista = $this->destinatarioTipo === 'revisor'
            ? 'emails.soportes.cierre_automatico_revisor'
            : 'emails.soportes.cierre_automatico_creador';

        return new Content(view: $vista);
    }
}
