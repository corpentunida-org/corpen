<?php

namespace App\Mail\Soportes;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class SoportesEscalacionPosponerMail extends Mailable
{
    public User $usuario;

    public int $diasConsecutivos;

    public function __construct(User $usuario, int $diasConsecutivos)
    {
        $this->usuario = $usuario;
        $this->diasConsecutivos = $diasConsecutivos;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: "Alerta: {$this->usuario->name} lleva {$this->diasConsecutivos} días seguidos posponiendo sus soportes",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.soportes.escalacion-posponer');
    }
}
