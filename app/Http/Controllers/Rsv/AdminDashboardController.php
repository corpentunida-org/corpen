<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Importar todos los modelos necesarios para las pestañas
use App\Models\Rsv\CatalogoInmueble;
use App\Models\Rsv\Reserva;
use App\Models\Rsv\TransaccionFinanciera;
use App\Models\Rsv\Pasarela;
use App\Models\Rsv\AuditLog;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. SELECTOR MAESTRO: Cargar todos los inmuebles por defecto para la lista desplegable
        $listaInmuebles = CatalogoInmueble::orderBy('name', 'asc')->get();

        // 2. INICIALIZAR ECOSISTEMA: Variables vacías por defecto
        $inmueble = null;
        $reservas = null;
        $transacciones = null;

        // 3. VARIABLES GLOBALES: Datos que no dependen de un apartamento específico
        $pasarelas = Pasarela::all();
        $auditoria = AuditLog::latest()->paginate(10, ['*'], 'page_auditoria');

        // 4. FILTRADO CONECTOR: Si hay un valor seleccionado en el selector
        if ($request->filled('inmueble_id')) {
            $inmuebleId = $request->inmueble_id;

            if ($inmuebleId === 'todos') {
                // A. VISTA GLOBAL: Cargar inmuebles precargando multimedia y tarifa base para velocidad máxima
                $listaInmuebles = CatalogoInmueble::with(['multimedia', 'latestTarifa'])
                    ->orderBy('name', 'asc')
                    ->get();

                // Cargar todas las reservas y transacciones de manera general
                $reservas = Reserva::with(['user', 'status', 'inmueble'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10, ['*'], 'page_reservas');

                $transacciones = TransaccionFinanciera::with(['reserva.inmueble', 'pasarela'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10, ['*'], 'page_finanzas');
            } else {
                // B. VISTA ESPECÍFICA POR INMUEBLE: Cargar el Inmueble Maestro y sus relaciones
                $inmueble = CatalogoInmueble::with([
                    'multimedia',
                    'tarifasTemporadas',
                    'latestTarifa',
                    'bloqueosCalendario'
                ])->findOrFail($inmuebleId);

                // Filtrar Reservas: Traer SOLO las reservas de ESTE inmueble específico
                $reservas = Reserva::with(['user', 'status'])
                    ->where('id_rsv_catalogo_inmueble', $inmuebleId)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10, ['*'], 'page_reservas');

                // Filtrar Finanzas: Traer SOLO las transacciones ligadas a las reservas de ESTE inmueble
                $transacciones = TransaccionFinanciera::with(['reserva.inmueble', 'pasarela'])
                    ->whereHas('reserva', function ($query) use ($inmuebleId) {
                        $query->where('id_rsv_catalogo_inmueble', $inmuebleId);
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate(10, ['*'], 'page_finanzas');
            }
        }

        // 5. RENDERIZADO: Retornar la vista principal con todas las variables compactadas
        return view('rsv.admin.dashboard', compact(
            'listaInmuebles',
            'inmueble',
            'reservas',
            'transacciones',
            'pasarelas',
            'auditoria'
        ));
    }
}
