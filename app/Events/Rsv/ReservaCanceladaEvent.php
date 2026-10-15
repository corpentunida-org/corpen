<?php

namespace App\Events\Rsv;

use App\Models\Rsv\Reserva;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservaCanceladaEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Reserva $reserva;
    public ?string $motivo;

    public function __construct(Reserva $reserva, ?string $motivo = null)
    {
        $this->reserva = $reserva;
        $this->motivo = $motivo;
    }
}
