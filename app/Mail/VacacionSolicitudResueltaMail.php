<?php

namespace App\Mail;

use App\Models\Sgrh\VacacionSolicitud;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class VacacionSolicitudResueltaMail extends Mailable
{
    public VacacionSolicitud $solicitud;

    public function __construct(VacacionSolicitud $solicitud)
    {
        $this->solicitud = $solicitud;
    }

    public function envelope(): Envelope
    {
        $estado = $this->solicitud->estado === 'aprobada' ? 'aprobada' : 'rechazada';

        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: "Tu solicitud de vacaciones fue {$estado}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.sgrh.vacacion-solicitud-resuelta');
    }
}
