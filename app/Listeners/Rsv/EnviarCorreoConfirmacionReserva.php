<?php

namespace App\Listeners\Rsv;

use App\Events\Rsv\ReservaCreadaEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class EnviarCorreoConfirmacionReserva implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ReservaCreadaEvent $event): void
    {
        $reserva = $event->reserva;

        Log::info("RSV NOTIFICACIÓN: Enviando correo de confirmación para la reserva {$reserva->codigo_reserva}", [
            'reserva_id' => $reserva->id,
            'cliente'    => $reserva->user->email ?? 'N/A',
            'monto'      => $reserva->monto_total,
        ]);

        // Ejemplo de envío de Mail Mailable:
        // Mail::to($reserva->user->email)->send(new ConfirmacionReservaMail($reserva));
    }
}
