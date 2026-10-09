<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

// Modelos PARTE_1: "Configuración Propiedad"
use App\Models\Rsv\CatalogoInmueble;
use App\Models\Rsv\TransaccionFinanciera;
use App\Models\Rsv\TarifaTemporada;

// Modelos PARTE_2: "Gestión y Auditoría"
use App\Models\Rsv\Reserva;
use App\Models\Rsv\Pasarela;
use App\Models\Rsv\AuditLog;

/**
 * Controlador Maestro del Panel de Administración (Orquestador).
 * Se encarga de cargar todo el ecosistema necesario para dashboard.blade.php
 * y distribuir los datos a sus respectivos partials (inmuebles-tab, show, etc.).
 */
class AdminDashboardController extends Controller
{
    /**
     * Punto de entrada principal del Dashboard.
     * Carga el estado global o el estado específico de un inmueble.
     */
    public function index(Request $request): View|JsonResponse
    {
        try {
            // 1. SELECTOR MAESTRO: Ligero, solo ID y Nombre para llenar el <select> del frontend
            $listaInmuebles = CatalogoInmueble::select('id', 'name')->orderBy('name', 'asc')->get();

            // 2. INICIALIZAR ECOSISTEMA: Variables vacías por defecto
            $inmueble = null;      // Para partials/inmuebles/show.blade.php
            $inmueblesGrid = null; // Para partials/inmuebles-tab.blade.php (El catálogo general)
            $reservas = null;
            $transacciones = null;

            // 3. VARIABLES GLOBALES
            $pasarelas = Pasarela::all();
            $auditoria = AuditLog::latest()->paginate(10, ['*'], 'page_auditoria');

            // 4. FILTRADO CONECTOR: Comportamiento según el selector maestro
            $inmuebleId = $request->input('inmueble_id', 'todos'); // Por defecto carga 'todos' si no se envía nada

            if ($inmuebleId === 'todos') {
                // B. VISTA GLOBAL (inmuebles-tab.blade.php)
                // Cargamos los inmuebles con paginación y sus relaciones básicas para las tarjetas/tablas
                $inmueblesGrid = CatalogoInmueble::with(['multimedia' => function($q) {
                        $q->orderBy('es_portada', 'desc') // Pone la portada de primera si la hay
                        ->orderBy('orden', 'asc');     // Luego respeta el orden configurado
                    }, 'latestTarifa'])
                    ->orderBy('name', 'asc')
                    ->paginate(12, ['*'], 'page_inmuebles');

                // Reservas y finanzas globales
                $reservas = Reserva::with(['user', 'status', 'inmueble:id,name'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10, ['*'], 'page_reservas');

                $transacciones = TransaccionFinanciera::with(['reserva.inmueble:id,name', 'pasarela'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10, ['*'], 'page_finanzas');

            } else {
                // B. VISTA ESPECÍFICA POR INMUEBLE (partials/inmuebles/show.blade.php)
                // Carga el Inmueble Maestro y TODAS sus relaciones para permitir edición completa
                $inmueble = CatalogoInmueble::with([
                    'multimedia' => function($q) {
                        $q->orderBy('orden', 'asc'); // Respetamos el orden configurado en el InmuebleMultimediaController
                    },
                    'tarifasTemporadas' => function($q) {
                        $q->orderBy('fecha_inicio', 'asc');
                    },
                    'latestTarifa',
                    'bloqueosCalendario'
                ])->findOrFail($inmuebleId);

                // Filtrar Reservas SOLO de ESTE inmueble específico
                $reservas = Reserva::with(['user', 'status'])
                    ->where('id_rsv_catalogo_inmueble', $inmuebleId)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10, ['*'], 'page_reservas');

                // Filtrar Finanzas ligadas a las reservas de ESTE inmueble
                $transacciones = TransaccionFinanciera::with(['reserva.inmueble:id,name', 'pasarela'])
                    ->whereHas('reserva', function ($query) use ($inmuebleId) {
                        $query->where('id_rsv_catalogo_inmueble', $inmuebleId);
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate(10, ['*'], 'page_finanzas');
            }

            // 5. RESPUESTA DUAL (AJAX / JSON)
            // Muy útil si quieres que el selector del dashboard actualice las tablas sin recargar la página
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'inmueble'      => $inmueble,
                        'inmueblesGrid' => $inmueblesGrid,
                        'reservas'      => $reservas,
                        'transacciones' => $transacciones,
                        'auditoria'     => $auditoria
                    ]
                ]);
            }

            // 6. RENDERIZADO WEB
            return view('rsv.admin.dashboard', compact(
                'listaInmuebles',
                'inmueble',
                'inmueblesGrid',
                'reservas',
                'transacciones',
                'pasarelas',
                'auditoria',
                'inmuebleId' // Pasamos el ID seleccionado para mantener el <select> en su estado correcto
            ));

        } catch (Throwable $e) {
            Log::error('Error Crítico en AdminDashboardController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al cargar el panel de control.'
                ], 500);
            }

            // Retorno de emergencia a la vista base en caso de fallo, para no romper la pantalla blanca
            return view('rsv.admin.dashboard', [
                'listaInmuebles' => CatalogoInmueble::select('id', 'name')->get(),
                'inmueblesGrid'  => null,
                'inmueble'       => null,
                'reservas'       => null,
                'transacciones'  => null,
                'pasarelas'      => Pasarela::all(),
                'auditoria'      => null,
                'inmuebleId'     => 'todos',
                'error'          => 'Ocurrió un error al cargar los datos del panel. Por favor, recargue la página.'
            ]);
        }
    }
}
