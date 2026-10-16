<?php

namespace App\Actions\Rsv;

use App\Events\Rsv\ReservaEstadoCambiadoEvent;
use App\Models\Rsv\HistorialEstado;
use App\Models\Rsv\Reserva;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActualizarReservaAction
{
    /**
     * Actualiza la información de la reserva y detecta cambios de estado.
     */
    public function execute(Reserva $reserva, array $data): Reserva
    {
        return DB::transaction(function () use ($reserva, $data) {
            $estadoAnterior = $reserva->id_rsv_statuses;
            $nuevoEstado = $data['id_rsv_statuses'] ?? $estadoAnterior;

            $reserva->update($data);

            // Si el estado cambió en la actualización general
            if ($estadoAnterior !== $nuevoEstado) {
                $comentario = 'Actualización de estado en modificación general de reserva.';

                HistorialEstado::create([
                    'id_rsv_reservas'          => $reserva->id,
                    'id_rsv_statuses_anterior' => $estadoAnterior,
                    'id_rsv_status_nuevo'      => $nuevoEstado,
                    'id_user'                  => Auth::id() ?? $reserva->id_user,
                    'comentario'               => $comentario,
                ]);

                event(new ReservaEstadoCambiadoEvent($reserva, $estadoAnterior, $nuevoEstado, $comentario));
            }

            $reserva->load(['inmueble', 'user', 'status', 'origen']);

            return $reserva;
        });
    }
}
