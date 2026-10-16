<?php

namespace App\Listeners\Rsv;

use App\Events\Rsv\ReservaEstadoCambiadoEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class NotificarCambioEstadoAdmin implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ReservaEstadoCambiadoEvent $event): void
    {
        $reserva = $event->reserva;

        Log::info("RSV ALERTA: Estado cambiado en reserva {$reserva->codigo_reserva}", [
            'reserva_id'      => $reserva->id,
            'estado_anterior' => $event->estadoAnterior,
            'estado_nuevo'    => $event->nuevoEstado,
            'comentario'      => $event->comentario,
        ]);

        // Aquí puedes integrar notificaciones Push, Telegram, WhatsApp o Email al Administrador
    }
}
