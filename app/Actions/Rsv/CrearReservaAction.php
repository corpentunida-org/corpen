<?php

namespace App\Actions\Rsv;

use App\Events\Rsv\ReservaCreadaEvent;
use App\Models\Rsv\HistorialEstado;
use App\Models\Rsv\Reserva;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CrearReservaAction
{
    /**
     * Ejecuta la creación atómica de una reserva con su historial inicial.
     */
    public function execute(array $data): Reserva
    {
        return DB::transaction(function () use ($data) {
            // Generar código único si no fue proporcionado
            $data['codigo_reserva'] = $data['codigo_reserva'] ?? 'RSV-' . strtoupper(uniqid());

            $reserva = Reserva::create($data);

            // Registrar el estado inicial en el historial
            HistorialEstado::create([
                'id_rsv_reservas'          => $reserva->id,
                'id_rsv_statuses_anterior' => null,
                'id_rsv_status_nuevo'      => $reserva->id_rsv_statuses,
                'id_user'                  => Auth::id() ?? $reserva->id_user,
                'comentario'               => $data['comentario_reserva'] ?? 'Creación inicial de la reserva.',
            ]);

            $reserva->load(['inmueble', 'user', 'status', 'origen']);

            // Disparar evento de dominio
            event(new ReservaCreadaEvent($reserva));

            return $reserva;
        });
    }
}
