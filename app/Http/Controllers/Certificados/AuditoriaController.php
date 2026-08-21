<?php

namespace App\Http\Controllers\Certificados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

// Importación de los modelos de Auditoría
use App\Models\Certificados\CarSiaOperacionLog;
use App\Models\Certificados\CarSiaOrigenEvento;
use App\Models\Certificados\CarSiaEventoAuditoria;

class AuditoriaController extends Controller
{
    /**
     * 1. BITÁCORA DE AUDITORÍA: Muestra el historial completo de operaciones
     */
    public function index(Request $request)
    {
        try {
            // Carga los logs con sus relaciones para visualizar quién hizo qué y desde dónde
            $logs = CarSiaOperacionLog::with([
                'origenEvento',
                'eventoAuditoria',
                'usuario',
                'lineaOperacion'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

            // Consulta de IDs de orígenes y eventos para los filtros de búsqueda técnica
            $origenes = CarSiaOrigenEvento::orderBy('nombre')->get();
            $eventos  = CarSiaEventoAuditoria::orderBy('nombre')->get();

            return view('certificados.auditoria.index', compact('logs', 'origenes', 'eventos'));

        } catch (\Exception $e) {
            Log::error('SIA Auditoría - Error al cargar la bitácora: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al cargar los registros de auditoría.');
        }
    }

    /**
     * 2. REGISTRA TRANSACCIONES JSON: Guarda un nuevo registro en la bitácora
     * (Este método puede ser llamado internamente por otros controladores o vía API)
     */
    public function registrarTransaccion(Request $request)
    {
        $request->validate([
            'numero_bloque'                 => 'required|string|max:50',
            'id_car_sia_operaciones_lineas' => 'nullable|exists:car_sia_operaciones_lineas,id',
            'id_car_sia_origenes_evento'    => 'required|exists:car_sia_origenes_evento,id',
            'id_car_sia_eventos_auditoria'  => 'required|exists:car_sia_eventos_auditoria,id',
            'detalles_ejecucion'            => 'nullable|array' // Se valida como array para que Eloquent lo guarde como JSONB
        ]);

        try {
            DB::transaction(function () use ($request) {
                CarSiaOperacionLog::create([
                    'numero_bloque'                 => $request->numero_bloque,
                    'id_car_sia_operaciones_lineas' => $request->id_car_sia_operaciones_lineas,
                    'id_car_sia_origenes_evento'    => $request->id_car_sia_origenes_evento,
                    'id_car_sia_eventos_auditoria'  => $request->id_car_sia_eventos_auditoria,
                    // Si hay un usuario logueado lo captura, si no queda en null (ej. CRON jobs)
                    'id_user'                       => Auth::check() ? Auth::id() : null,
                    // Captura automáticamente la IP desde donde se disparó la acción
                    'ip'                            => $request->ip(),
                    'detalles_ejecucion'            => $request->detalles_ejecucion,
                ]);
            });

            // Si es una petición web, redirige. Si es una API, retorna JSON.
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Log registrado correctamente'], 201);
            }
            return redirect()->back()->with('success', 'Registro de auditoría guardado exitosamente.');

        } catch (\Exception $e) {
            Log::error('SIA Auditoría - Error al registrar transacción: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['error' => 'No se pudo guardar el log de auditoría'], 500);
            }
            return redirect()->back()->with('error', 'Ocurrió un error al intentar guardar la transacción.');
        }
    }

    /**
     * 3. GESTIONA CATÁLOGOS TÉCNICOS: Crea un nuevo Origen de Evento (Ej. Web, API, Cron)
     */
    public function storeOrigenEvento(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:car_sia_origenes_evento,nombre'
        ]);

        try {
            CarSiaOrigenEvento::create(['nombre' => $request->nombre]);
            return redirect()->back()->with('success', 'Origen de evento creado correctamente.');
        } catch (\Exception $e) {
            Log::error('SIA Auditoría - Error al guardar origen: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo registrar el origen de evento.');
        }
    }

    /**
     * 4. GESTIONA CATÁLOGOS TÉCNICOS: Crea un nuevo Evento de Auditoría (Ej. Cambio de Estado, Inyección)
     */
    public function storeEventoAuditoria(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:car_sia_eventos_auditoria,nombre'
        ]);

        try {
            CarSiaEventoAuditoria::create(['nombre' => $request->nombre]);
            return redirect()->back()->with('success', 'Evento de auditoría creado correctamente.');
        } catch (\Exception $e) {
            Log::error('CERTIFICADOS Auditoría - Error al guardar evento de auditoría: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo registrar el evento de auditoría.');
        }
    }
}
