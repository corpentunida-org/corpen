<?php

namespace App\Events\Rsv;

use App\Models\Rsv\Reserva;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservaCreadaEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Reserva $reserva;

    public function __construct(Reserva $reserva)
    {
        $this->reserva = $reserva;
    }
}
