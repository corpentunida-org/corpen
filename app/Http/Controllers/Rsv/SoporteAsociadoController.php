<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\Reserva;
use App\Services\Rsv\RsvAcceso;
use App\Services\Rsv\RsvSoporteAsociado;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Soporte dentro de la reserva (Portal Asociado): si hay agente en línea se habilita el chat;
 * si no, se le ofrece al asociado crear un ticket en la plataforma Soportes.
 */
class SoporteAsociadoController extends Controller
{
    /** GET: ¿hay algún agente de soporte RSV en línea? (el portal lo consulta al abrir la pestaña) */
    public function estado(): JsonResponse
    {
        $n = RsvSoporteAsociado::agentesEnLinea(Auth::id())->count();

        return response()->json([
            'en_linea' => $n > 0,
            'agentes'  => $n,
        ]);
    }

    /** POST: crea un ticket en scp_soportes asociado a la reserva del titular. */
    public function crearTicket(Request $request): JsonResponse
    {
        $data = $request->validate([
            // Opcional: desde la pestaña Soporte del portal puede ser una consulta general sin reserva
            'id_rsv_reservas' => 'nullable|integer|exists:rsv_reservas,id',
            'descripcion'     => 'required|string|min:10|max:2000',
            'adjunto'         => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:10240',
        ], [
            'descripcion.required' => 'Describe el problema.',
            'descripcion.min'      => 'Cuéntanos un poco más (mínimo 10 caracteres).',
            'adjunto.mimes'        => 'El adjunto debe ser PDF, JPG o PNG.',
            'adjunto.max'          => 'El adjunto no puede pesar más de 10 MB.',
        ]);

        $reserva = !empty($data['id_rsv_reservas']) ? Reserva::findOrFail($data['id_rsv_reservas']) : null;
        if ($reserva && !RsvAcceso::esTitular($reserva)) {
            return RsvAcceso::denegado('Solo puedes crear tickets de tus reservas.');
        }

        try {
            $ticket = RsvSoporteAsociado::crearTicket($reserva, Auth::user(), $data['descripcion'], $request->file('adjunto'));
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('RSV ticket: error al crear', ['reserva' => $reserva?->id, 'err' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'No se pudo crear el ticket. Intenta de nuevo.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ticket #' . $ticket->id . ' creado. Nuestro equipo lo atenderá y te contactará.',
            'ticket'  => ['id' => $ticket->id, 'estado' => 'Sin asignar'],
        ], 201);
    }
}
