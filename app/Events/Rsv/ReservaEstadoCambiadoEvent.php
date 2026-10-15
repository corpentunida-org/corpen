<?php

namespace App\Events\Rsv;

use App\Models\Rsv\Reserva;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservaEstadoCambiadoEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Reserva $reserva;
    public ?int $estadoAnterior;
    public int $nuevoEstado;
    public ?string $comentario;

    public function __construct(Reserva $reserva, ?int $estadoAnterior, int $nuevoEstado, ?string $comentario = null)
    {
        $this->reserva = $reserva;
        $this->estadoAnterior = $estadoAnterior;
        $this->nuevoEstado = $nuevoEstado;
        $this->comentario = $comentario;
    }
}
