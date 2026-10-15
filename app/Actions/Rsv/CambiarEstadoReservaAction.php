<?php

namespace App\Actions\Rsv;

use App\Events\Rsv\ReservaCanceladaEvent;
use App\Events\Rsv\ReservaEstadoCambiadoEvent;
use App\Models\Rsv\HistorialEstado;
use App\Models\Rsv\Reserva;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CambiarEstadoReservaAction
{
    /**
     * Cambia el estado de una reserva de forma justificada.
     */
    public function execute(Reserva $reserva, int $nuevoEstadoId, ?string $comentario = null, int $estadoCanceladoId = 5): Reserva
    {
        $estadoAnterior = $reserva->id_rsv_statuses;

        if ($estadoAnterior === $nuevoEstadoId) {
            throw new InvalidArgumentException('La reserva ya se encuentra en el estado indicado.');
        }

        return DB::transaction(function () use ($reserva, $estadoAnterior, $nuevoEstadoId, $comentario, $estadoCanceladoId) {
            $reserva->update(['id_rsv_statuses' => $nuevoEstadoId]);

            $comentarioHistorial = $comentario ?? 'Cambio manual de estado.';

            HistorialEstado::create([
                'id_rsv_reservas'          => $reserva->id,
                'id_rsv_statuses_anterior' => $estadoAnterior,
                'id_rsv_status_nuevo'      => $nuevoEstadoId,
                'id_user'                  => Auth::id() ?? $reserva->id_user,
                'comentario'               => $comentarioHistorial,
            ]);

            $reserva->load(['status', 'inmueble', 'user']);

            // Disparar eventos según el caso
            event(new ReservaEstadoCambiadoEvent($reserva, $estadoAnterior, $nuevoEstadoId, $comentarioHistorial));

            if ($nuevoEstadoId === $estadoCanceladoId) {
                event(new ReservaCanceladaEvent($reserva, $comentarioHistorial));
            }

            return $reserva;
        });
    }
}
