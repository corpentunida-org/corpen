<?php

namespace App\Mail;

use App\Models\Sgrh\VacacionSolicitud;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class VacacionSolicitudCreadaMail extends Mailable
{
    public VacacionSolicitud $solicitud;

    public function __construct(VacacionSolicitud $solicitud)
    {
        $this->solicitud = $solicitud;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Nueva solicitud de vacaciones de ' . $this->solicitud->empleado->nombre_completo,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.sgrh.vacacion-solicitud-creada');
    }
}
