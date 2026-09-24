<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Interacciones\IntAlertaOmitido;
use App\Models\Soportes\ScpAlertaConfig;
use App\Services\Soportes\AlertasSoportesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Alertas de Soportes asignados a mí sin cerrar — réplica de
 * InteractionController::alertas()/registrarDecisionAlerta() para el Centro de Soportes.
 */
class ScpAlertaController extends Controller
{
    public function pendientes(AlertasSoportesService $servicio)
    {
        $userId = Auth::id();
        $pendientes = $servicio->pendientesDeUsuario($userId);

        return response()->json([
            'pendientes_count' => $pendientes->count(),
            'pendientes' => $pendientes->take(8)->map(fn ($s) => [
                'id' => $s->id,
                'detalle' => \Illuminate\Support\Str::limit($s->detalles_soporte, 80),
                'estado' => $s->estadoSoporte->nombre ?? '—',
                'prioridad' => $s->prioridad->nombre ?? '—',
                'url' => route('soportes.soportes.show', $s->id),
            ])->values(),
            'aviso_intervalo_horas' => ScpAlertaConfig::actual()->aviso_intervalo_horas,
            // Lista compartida con Interacciones (Admin → Configuración de Alertas → Agentes
            // Omitidos) — "pantalla" tapa el modal forzado en los dos módulos por igual.
            'pantalla_omitida' => IntAlertaOmitido::estaOmitido($userId, 'pantalla'),
        ]);
    }

    public function registrarDecision(Request $request, AlertasSoportesService $servicio)
    {
        $request->validate(['decision' => 'required|in:responder,posponer']);

        return response()->json($servicio->registrarDecision(Auth::id(), $request->input('decision')));
    }
}
