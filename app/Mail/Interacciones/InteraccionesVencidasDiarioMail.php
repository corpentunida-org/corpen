<?php

namespace App\Mail\Interacciones;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Collection;

class InteraccionesVencidasDiarioMail extends Mailable
{
    public User $usuario;

    public Collection $vencidas;

    public function __construct(User $usuario, Collection $vencidas)
    {
        $this->usuario = $usuario;
        $this->vencidas = $vencidas;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Tienes '.$this->vencidas->count().' interacción(es) vencida(s) — Daytrack',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.interacciones.vencidas-diario');
    }
}
