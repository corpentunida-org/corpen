<?php

namespace App\Mail\Interacciones;

use Carbon\Carbon;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Collection;

class InteraccionesInactividadMail extends Mailable
{
    public string $area;

    public Collection $usuariosInactivos;

    public Carbon $fecha;

    /** @param Collection<int, \App\Models\User> $usuariosInactivos */
    public function __construct(string $area, Collection $usuariosInactivos, Carbon $fecha)
    {
        $this->area = $area;
        $this->usuariosInactivos = $usuariosInactivos;
        $this->fecha = $fecha;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Alerta: '.$this->usuariosInactivos->count().' usuario(s) de '.strtoupper($this->area).' sin registrar hoy en Daytrack',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.interacciones.inactividad-diaria');
    }
}
