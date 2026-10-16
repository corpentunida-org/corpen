<?php

namespace App\Listeners\Rsv;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RegistrarAuditoriaReserva implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Escucha eventos genéricos del módulo para registrar la traza de auditoría.
     */
    public function handle(object $event): void
    {
        $nombreEvento = class_basename($event);
        $reserva = $event->reserva ?? null;

        if ($reserva) {
            Log::notice("RSV AUDITORÍA [{$nombreEvento}]: Transacción registrada para la reserva #{$reserva->id}", [
                'evento'         => $nombreEvento,
                'codigo_reserva' => $reserva->codigo_reserva,
                'usuario_id'     => Auth::id() ?? $reserva->id_user ?? 'SISTEMA',
                'timestamp'      => now()->toDateTimeString(),
            ]);
        }
    }
}
