<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Carbon\Carbon; // Importante para el cálculo de fechas de ocupación
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
 * y calcular todos los KPIs (Globales y Específicos) antes de enviarlos a la vista.
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
            // 1. SELECTOR MAESTRO: Ligero para llenar el <select> del frontend
            // Añadimos 'active' y 'city' porque se usan en el blade
            $listaInmuebles = CatalogoInmueble::select('id', 'name', 'active', 'city')
                ->orderBy('name', 'asc')
                ->get();

            // 2. INICIALIZAR ECOSISTEMA: Variables vacías por defecto
            $inmueble = null;      // Para partials/inmuebles/show.blade.php
            $inmueblesGrid = null; // Para partials/inmuebles-tab.blade.php (El catálogo general)
            $reservas = null;
            $transacciones = null;

            // Inicializamos las variables de KPIs Globales (para evitar undefined variables)
            $tabTotalInmuebles = 0;
            $tabReservas       = 0;
            $tabBloqueos       = 0;
            $tabFinanzas       = 0;
            $tabAuditoria      = 0;

            // Inicializamos las variables de KPIs Específicos (para evitar undefined variables)
            $kpiNoche          = null;
            $kpiMinReserva     = null;
            $kpiProximas       = 0;
            $kpiProximaEntrada = null;
            $kpiOcupacion      = 0;
            $kpiResenasCount   = 0;
            $kpiRating         = null;
            $kpiFotos          = 0;
            $kpiVideos         = 0;
            $ingresosInmueble  = 0;
            $auditoriaCount    = 0;
            $nochesOcupadas    = 0;
            $diasDelMes        = Carbon::today()->daysInMonth;

            // 3. VARIABLES GLOBALES
            $pasarelas = Pasarela::all();
            $auditoria = AuditLog::latest()->paginate(10, ['*'], 'page_auditoria');

            // 4. FILTRADO CONECTOR: Comportamiento según el selector maestro
            $inmuebleId = $request->input('inmueble_id', 'todos'); // Por defecto carga 'todos'

            if ($inmuebleId === 'todos') {
                // ==============================================================================
                // A. VISTA GLOBAL Y SUS KPIs
                // ==============================================================================

                $inmueblesGrid = CatalogoInmueble::with([
                        'multimedia' => function($q) {
                            $q->orderBy('es_portada', 'desc')
                              ->orderBy('orden', 'asc');
                        },
                        'latestTarifa',
                        'tipoInmueble',
                    ])
                    ->withCount(['reservas', 'bloqueosCalendario'])
                    ->orderBy('name', 'asc')
                    ->paginate(12, ['*'], 'page_inmuebles');

                $reservas = Reserva::with(['user', 'status', 'inmueble:id,name'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10, ['*'], 'page_reservas');

                $transacciones = TransaccionFinanciera::with(['reserva.inmueble:id,name', 'pasarela'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10, ['*'], 'page_finanzas');

                // --- CÁLCULOS DE KPIS GLOBALES (Movidos del Blade) ---
                $tabTotalInmuebles = $inmueblesGrid->total();
                $tabReservas = class_exists(Reserva::class) ? Reserva::count() : 0;

                // Manejo dinámico del modelo BloqueoCalendario
                $clsBloqueo = null;
                foreach (['App\\Models\\Rsv\\BloqueoCalendario', 'App\\Models\\Rsv\\BloqueosCalendario', 'App\\Models\\Rsv\\CalendarioBloqueo'] as $clsTry) {
                    if (class_exists($clsTry)) { $clsBloqueo = $clsTry; break; }
                }
                $tabBloqueos = $clsBloqueo ? $clsBloqueo::count() : 0;

                // Cálculo total financiero (busca la columna correcta dinámicamente)
                foreach (['precio_total', 'total', 'monto_total', 'valor_total'] as $colTry) {
                    try {
                        $tabFinanzas = Reserva::sum($colTry);
                        break;
                    } catch (\Throwable $e) { continue; }
                }

                $tabAuditoria = method_exists($auditoria, 'total') ? $auditoria->total() : $auditoria->count();

            } else {
                // ==============================================================================
                // B. VISTA ESPECÍFICA POR INMUEBLE Y SUS KPIs
                // ==============================================================================

                $inmueble = CatalogoInmueble::with([
                        'multimedia' => function($q) {
                            $q->orderBy('es_portada', 'desc')
                              ->orderBy('orden', 'asc');
                        },
                        'tarifasTemporadas' => function($q) {
                            $q->orderBy('fecha_inicio', 'asc');
                        },
                        'latestTarifa',
                        'bloqueosCalendario',
                        'tipoInmueble',
                    ])
                    ->withCount(['reservas', 'bloqueosCalendario'])
                    ->findOrFail($inmuebleId);

                $reservas = Reserva::with(['user', 'status'])
                    ->where('id_rsv_catalogo_inmueble', $inmuebleId)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10, ['*'], 'page_reservas');

                $transacciones = TransaccionFinanciera::with(['reserva.inmueble:id,name', 'pasarela'])
                    ->whereHas('reserva', function ($query) use ($inmuebleId) {
                        $query->where('id_rsv_catalogo_inmueble', $inmuebleId);
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate(10, ['*'], 'page_finanzas');

                // --- CÁLCULOS DE KPIS ESPECÍFICOS (Movidos del Blade) ---
                $kpiNoche      = optional($inmueble->latestTarifa)->precio_noche;
                $kpiMinReserva = optional($inmueble->latestTarifa)->precio_minimo_reserva;

                $reservasCol = method_exists($inmueble, 'reservas') ? ($inmueble->reservas ?? collect()) : collect();

                $hoy       = Carbon::today();
                $inicioMes = $hoy->copy()->startOfMonth();
                $finMes    = $hoy->copy()->endOfMonth();

                foreach ($reservasCol as $r) {
                    // Cálculo de Ingresos
                    foreach (['precio_total', 'total', 'monto_total', 'valor_total'] as $colTry) {
                        if (isset($r->{$colTry}) && is_numeric($r->{$colTry})) {
                            $ingresosInmueble += (float) $r->{$colTry};
                            break;
                        }
                    }

                    // Cálculo de Fechas y Ocupación
                    $fe = $r->fecha_entrada ?? $r->check_in  ?? $r->fecha_inicio  ?? $r->fecha_llegada ?? null;
                    $fs = $r->fecha_salida  ?? $r->check_out ?? $r->fecha_fin     ?? null;

                    if ($fe) {
                        try {
                            $feC = Carbon::parse($fe);
                            if ($feC->gte($hoy)) {
                                $kpiProximas++;
                                if (!$kpiProximaEntrada || $feC->lt($kpiProximaEntrada)) {
                                    $kpiProximaEntrada = $feC;
                                }
                            }

                            if ($fs) {
                                $ini = Carbon::parse($fe);
                                $fin = Carbon::parse($fs);
                                $desde = $ini->gt($inicioMes) ? $ini : $inicioMes;
                                $hasta = $fin->lt($finMes) ? $fin : $finMes;

                                if ($hasta->gt($desde)) {
                                    $nochesOcupadas += (int) ceil($desde->diffInDays($hasta));
                                }
                            }
                        } catch (\Throwable $e) {}
                    }
                }

                $nochesOcupadas = min($nochesOcupadas, $diasDelMes);
                $kpiOcupacion = $diasDelMes > 0 ? (int) round(($nochesOcupadas / $diasDelMes) * 100) : 0;

                // Cálculo de Reseñas / Rating
                $resenasCol = collect();
                foreach (['resenas', 'reseñas', 'reviews', 'calificaciones', 'valoraciones'] as $kpiRel) {
                    if (method_exists($inmueble, $kpiRel)) {
                        $resenasCol = $inmueble->{$kpiRel};
                        break;
                    }
                }

                $kpiResenasCount = $resenasCol ? $resenasCol->count() : 0;
                if ($kpiResenasCount > 0) {
                    $suma = 0; $validas = 0;
                    foreach ($resenasCol as $res) {
                        $v = $res->calificacion ?? $res->puntaje ?? $res->puntuacion ?? $res->rating ?? $res->estrellas ?? null;
                        if (!is_null($v) && is_numeric($v)) {
                            $suma += (float) $v;
                            $validas++;
                        }
                    }
                    if ($validas > 0) $kpiRating = round($suma / $validas, 1);
                }

                // Cálculo de Multimedia
                $mediaCol  = method_exists($inmueble, 'multimedia') ? ($inmueble->multimedia ?? collect()) : collect();
                $kpiFotos  = $mediaCol->where('tipo_multimedia', 'imagen')->count();
                $kpiVideos = $mediaCol->where('tipo_multimedia', 'video')->count();

                // Auditoría Específica
                $auditoriaCount = method_exists($auditoria, 'total') ? $auditoria->total() : $auditoria->count();
            }

            // 5. RESPUESTA DUAL (AJAX / JSON)
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'inmueble'      => $inmueble,
                        'inmueblesGrid' => $inmueblesGrid,
                        'reservas'      => $reservas,
                        'transacciones' => $transacciones,
                        'auditoria'     => $auditoria,
                        // Añadimos métricas al JSON por si el Frontend en Vue/React/JS los requiere
                        'kpis' => [
                            'ocupacion' => $kpiOcupacion,
                            'ingresos'  => $ingresosInmueble,
                            'rating'    => $kpiRating
                        ]
                    ]
                ]);
            }

            // 6. RENDERIZADO WEB CON TODOS LOS KPIs INYECTADOS
            return view('rsv.admin.dashboard', compact(
                'listaInmuebles',
                'inmueble',
                'inmueblesGrid',
                'reservas',
                'transacciones',
                'pasarelas',
                'auditoria',
                'inmuebleId',

                // KPIs Globales Inyectados
                'tabTotalInmuebles',
                'tabReservas',
                'tabBloqueos',
                'tabFinanzas',
                'tabAuditoria',

                // KPIs Específicos Inyectados
                'kpiNoche',
                'kpiMinReserva',
                'kpiProximas',
                'kpiProximaEntrada',
                'kpiOcupacion',
                'kpiResenasCount',
                'kpiRating',
                'kpiFotos',
                'kpiVideos',
                'ingresosInmueble',
                'auditoriaCount',
                'nochesOcupadas',
                'diasDelMes'
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

            // Retorno de emergencia en caso de fallo crítico
            return view('rsv.admin.dashboard', [
                'listaInmuebles' => CatalogoInmueble::select('id', 'name', 'active', 'city')->get(),
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
