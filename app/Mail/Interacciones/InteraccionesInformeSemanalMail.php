<?php

namespace App\Mail\Interacciones;

use Carbon\Carbon;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Collection;

class InteraccionesInformeSemanalMail extends Mailable
{
    public string $area;

    public Collection $filas;

    public Carbon $desde;

    public Carbon $hasta;

    /** @param Collection<int, object{usuario: \App\Models\User, total: int, vencidas: int}> $filas */
    public function __construct(string $area, Collection $filas, Carbon $desde, Carbon $hasta)
    {
        $this->area = $area;
        $this->filas = $filas;
        $this->desde = $desde;
        $this->hasta = $hasta;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Informe semanal de Interacciones — '.strtoupper($this->area),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.interacciones.informe-semanal');
    }
}
