<?php

namespace App\Mail\Interacciones;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class InteraccionesEscalacionPosponerMail extends Mailable
{
    public User $usuario;

    public string $area;

    public int $diasConsecutivos;

    public function __construct(User $usuario, string $area, int $diasConsecutivos)
    {
        $this->usuario = $usuario;
        $this->area = $area;
        $this->diasConsecutivos = $diasConsecutivos;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: "Alerta: {$this->usuario->name} lleva {$this->diasConsecutivos} días seguidos posponiendo sus vencidas",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.interacciones.escalacion-posponer');
    }
}
