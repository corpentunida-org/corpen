<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Carbon\Carbon;
use Throwable;

// Modelos PARTE_1: "Configuración Propiedad"
use App\Models\Rsv\CatalogoInmueble;
use App\Models\Rsv\TransaccionFinanciera;
use App\Models\Rsv\TarifaTemporada;
use App\Models\Rsv\BloqueoCalendario;
use App\Models\Rsv\TipoInmueble;
use App\Models\Rsv\InmuebleMultimedia;

// Modelos PARTE_2: "Gestión y Auditoría"
use App\Models\Rsv\Reserva;
use App\Models\Rsv\Pasarela;
use App\Models\Rsv\AuditLog;
use App\Models\Rsv\HistorialEstado;
use App\Models\Rsv\OrigenReserva;
use App\Models\Rsv\ReservaHuesped;
use App\Models\Rsv\Status;

/**
 * Controlador Maestro del Panel de Administración (Orquestador).
 * Gestiona la carga principal del dashboard, cálculo de KPIs y fragmentos AJAX.
 */
class AdminDashboardController extends Controller
{
    // =========================================================================
    // NIVEL 1: DASHBOARD PRINCIPAL (Carga completa inicial)
    // =========================================================================

    /**
     * Renderiza la vista principal o devuelve datos JSON (Globales o por Inmueble).
     */
    public function index(Request $request): View|JsonResponse
    {
        try {
            // 1. Selector maestro (Frontend)
            $listaInmuebles = CatalogoInmueble::select('id', 'name', 'active', 'city')
                ->orderBy('name', 'asc')
                ->get();

            // 2. Inicialización de ecosistema de variables
            $inmueble = null;
            $inmueblesGrid = null;
            $reservas = null;
            $transacciones = null;

            // KPIs Globales
            $tabTotalInmuebles = 0;
            $tabReservas       = 0;
            $tabBloqueos       = 0;
            $tabFinanzas       = 0;
            $tabAuditoria      = 0;

            // KPIs Específicos
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

            // 3. Variables base
            $pasarelas = Pasarela::all();
            $auditoria = AuditLog::latest()->paginate(10, ['*'], 'page_auditoria');
            $inmuebleId = $request->input('inmueble_id', 'todos');

            // 4. Lógica de Filtrado (Global vs Específico)
            if ($inmuebleId === 'todos') {
                // --- A. VISTA GLOBAL ---
                $inmueblesGrid = CatalogoInmueble::with([
                        'multimedia' => fn($q) => $q->orderBy('es_portada', 'desc')->orderBy('orden', 'asc'),
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

                // KPIs Globales
                $tabTotalInmuebles = $inmueblesGrid->total();
                $tabReservas = class_exists(Reserva::class) ? Reserva::count() : 0;

                $clsBloqueo = null;
                foreach (['App\\Models\\Rsv\\BloqueoCalendario', 'App\\Models\\Rsv\\BloqueosCalendario', 'App\\Models\\Rsv\\CalendarioBloqueo'] as $clsTry) {
                    if (class_exists($clsTry)) { $clsBloqueo = $clsTry; break; }
                }
                $tabBloqueos = $clsBloqueo ? $clsBloqueo::count() : 0;

                foreach (['precio_total', 'total', 'monto_total', 'valor_total'] as $colTry) {
                    try {
                        $tabFinanzas = Reserva::sum($colTry);
                        break;
                    } catch (\Throwable $e) { continue; }
                }

                $tabAuditoria = method_exists($auditoria, 'total') ? $auditoria->total() : $auditoria->count();

            } else {
                // --- B. VISTA ESPECÍFICA (Por Inmueble) ---
                $inmueble = CatalogoInmueble::with([
                        'multimedia' => fn($q) => $q->orderBy('es_portada', 'desc')->orderBy('orden', 'asc'),
                        'tarifasTemporadas' => fn($q) => $q->orderBy('fecha_inicio', 'asc'),
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
                    ->whereHas('reserva', fn($q) => $q->where('id_rsv_catalogo_inmueble', $inmuebleId))
                    ->orderBy('created_at', 'desc')
                    ->paginate(10, ['*'], 'page_finanzas');

                // KPIs Específicos
                $kpiNoche      = optional($inmueble->latestTarifa)->precio_noche;
                $kpiMinReserva = optional($inmueble->latestTarifa)->precio_minimo_reserva;
                $reservasCol   = method_exists($inmueble, 'reservas') ? ($inmueble->reservas ?? collect()) : collect();

                $hoy       = Carbon::today();
                $inicioMes = $hoy->copy()->startOfMonth();
                $finMes    = $hoy->copy()->endOfMonth();

                foreach ($reservasCol as $r) {
                    foreach (['precio_total', 'total', 'monto_total', 'valor_total'] as $colTry) {
                        if (isset($r->{$colTry}) && is_numeric($r->{$colTry})) {
                            $ingresosInmueble += (float) $r->{$colTry};
                            break;
                        }
                    }

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

                $mediaCol  = method_exists($inmueble, 'multimedia') ? ($inmueble->multimedia ?? collect()) : collect();
                $kpiFotos  = $mediaCol->where('tipo_multimedia', 'imagen')->count();
                $kpiVideos = $mediaCol->where('tipo_multimedia', 'video')->count();

                $auditoriaCount = method_exists($auditoria, 'total') ? $auditoria->total() : $auditoria->count();
            }

            // 5. Respuesta JSON (AJAX)
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'inmueble'      => $inmueble,
                        'inmueblesGrid' => $inmueblesGrid,
                        'reservas'      => $reservas,
                        'transacciones' => $transacciones,
                        'auditoria'     => $auditoria,
                        'kpis' => [
                            'ocupacion' => $kpiOcupacion,
                            'ingresos'  => $ingresosInmueble,
                            'rating'    => $kpiRating
                        ]
                    ]
                ]);
            }

            // 6. Respuesta HTML (Vista Blade)
            return view('rsv.admin.dashboard', compact(
                'listaInmuebles', 'inmueble', 'inmueblesGrid', 'reservas', 'transacciones', 'pasarelas', 'auditoria', 'inmuebleId',
                'tabTotalInmuebles', 'tabReservas', 'tabBloqueos', 'tabFinanzas', 'tabAuditoria',
                'kpiNoche', 'kpiMinReserva', 'kpiProximas', 'kpiProximaEntrada', 'kpiOcupacion',
                'kpiResenasCount', 'kpiRating', 'kpiFotos', 'kpiVideos', 'ingresosInmueble',
                'auditoriaCount', 'nochesOcupadas', 'diasDelMes'
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

            return view('rsv.admin.dashboard', [
                'listaInmuebles' => CatalogoInmueble::select('id', 'name', 'active', 'city')->get(),
                'inmueblesGrid'  => null, 'inmueble' => null, 'reservas' => null, 'transacciones'  => null,
                'pasarelas'      => Pasarela::all(), 'auditoria' => null, 'inmuebleId' => 'todos',
                'error'          => 'Ocurrió un error al cargar los datos del panel. Por favor, recargue la página.'
            ]);
        }
    }


    // =========================================================================
    // TAB INICIO / RESUMEN
    // =========================================================================

    /**
     * [NIVEL 2] - TAB INICIO
     * Refresca la vista resumen principal (partials/tab-inicio.blade.php).
     */
    public function inicioFragmento(Request $request)
    {
        try {
            $inmuebleId = $request->query('inmueble_id', 'todos');
            $inmueble = null;
            if ($inmuebleId !== 'todos' && is_numeric($inmuebleId)) {
                $inmueble = CatalogoInmueble::with(['latestTarifa', 'multimedia'])->find($inmuebleId);
            }

            return response()
                ->view('rsv.admin.partials.tab-inicio', compact('inmueble', 'inmuebleId'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (\Throwable $e) {
            Log::error('inicioFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }


    // =========================================================================
    // SECCIÓN 1/5: INMUEBLES (Catálogo y Configuraciones)
    // =========================================================================

    /**
     * [NIVEL 2] - TAB INMUEBLES
     * Refresca el contenedor principal de inmuebles (partials/tab-inmuebles.blade.php).
     */
    public function inmueblesFragmento(Request $request)
    {
        try {
            $inmuebleId = $request->query('inmueble_id', 'todos');
            $pagina     = max(1, (int) $request->query('page_inmuebles', 1));
            $inmueblesGrid = null;
            $inmueble      = null;

            if ($inmuebleId === 'todos') {
                $inmueblesGrid = CatalogoInmueble::with([
                        'multimedia' => fn($q) => $q->orderBy('es_portada', 'desc')->orderBy('orden', 'asc'),
                        'latestTarifa', 'tipoInmueble',
                    ])
                    ->withCount(['reservas', 'bloqueosCalendario'])
                    ->orderBy('name', 'asc')
                    ->paginate(12, ['*'], 'page_inmuebles', $pagina)
                    ->appends($request->query());
            } elseif (is_numeric($inmuebleId)) {
                $inmueble = CatalogoInmueble::with([
                        'multimedia' => fn($q) => $q->orderBy('es_portada', 'desc')->orderBy('orden', 'asc'),
                        'tarifasTemporadas' => fn($q) => $q->orderBy('fecha_inicio', 'asc'),
                        'latestTarifa', 'bloqueosCalendario', 'tipoInmueble',
                    ])
                    ->withCount(['reservas', 'bloqueosCalendario'])
                    ->findOrFail($inmuebleId);
            }

            return response()
                ->view('rsv.admin.partials.tab-inmuebles', compact('inmueblesGrid', 'inmueble', 'inmuebleId'))
                ->header('Cache-Control', 'no-store, max-age=0');

        } catch (\Throwable $e) {
            Log::error('inmueblesFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /** [NIVEL 3] - partials/inmuebles/catalogo.blade.php */
    public function inmueblesCatalogoFragmento(Request $request)
    {
        try {
            $inmuebleId = $request->query('inmueble_id', 'todos');
            $pagina     = max(1, (int) $request->query('page_inmuebles', 1));
            
            $inmueblesGrid = CatalogoInmueble::with(['multimedia', 'latestTarifa', 'tipoInmueble'])
                                ->withCount(['reservas', 'bloqueosCalendario'])
                                ->orderBy('name', 'asc')
                                ->paginate(12, ['*'], 'page_inmuebles', $pagina)
                                ->appends($request->query());

            return response()
                ->view('rsv.admin.partials.inmuebles.catalogo', compact('inmueblesGrid', 'inmuebleId'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (\Throwable $e) {
            Log::error('inmueblesCatalogoFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /** [NIVEL 3] - partials/inmuebles/tarifa.blade.php */
    public function inmueblesTarifaFragmento(Request $request)
    {
        try {
            $inmuebleId = $request->query('inmueble_id');
            $tarifas = TarifaTemporada::when($inmuebleId && $inmuebleId !== 'todos', fn($q) => $q->where('id_rsv_catalogo_inmueble', $inmuebleId))
                        ->orderBy('fecha_inicio', 'desc')->get();

            return response()
                ->view('rsv.admin.partials.inmuebles.tarifa', compact('tarifas', 'inmuebleId'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (\Throwable $e) {
            Log::error('inmueblesTarifaFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /** [NIVEL 3] - partials/inmuebles/galeria.blade.php */
    public function inmueblesGaleriaFragmento(Request $request)
    {
        try {
            $inmuebleId = $request->query('inmueble_id');
            $multimedia = InmuebleMultimedia::when($inmuebleId && $inmuebleId !== 'todos', fn($q) => $q->where('id_rsv_catalogo_inmueble', $inmuebleId))
                        ->orderBy('es_portada', 'desc')->orderBy('orden', 'asc')->get();

            return response()
                ->view('rsv.admin.partials.inmuebles.galeria', compact('multimedia', 'inmuebleId'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (\Throwable $e) {
            Log::error('inmueblesGaleriaFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /** [NIVEL 3] - partials/inmuebles/tipo.blade.php */
    public function inmueblesTipoFragmento(Request $request)
    {
        try {
            $tipos = TipoInmueble::withCount('inmuebles')->orderBy('name', 'asc')->get();
            return response()
                ->view('rsv.admin.partials.inmuebles.tipo', compact('tipos'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (\Throwable $e) {
            Log::error('inmueblesTipoFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }


    // =========================================================================
    // SECCIÓN 2/5: RESERVAS (Núcleo Transaccional)
    // =========================================================================

    /**
     * [NIVEL 2] - TAB RESERVAS
     * Refresca el contenedor principal de reservas (partials/tab-reservas.blade.php).
     */
    public function reservasFragmento(Request $request)
    {
        try {
            $probadas = [];
            $cortas   = [];

            /* ── CIERRES-AYUDA (todo dentro del método: un solo bloque que pegar) ── */

            /* Extrae el bloque <div id="rvTabWrap">…</div> por balance de <div>.
            Devuelve [bloque, offsetDelInicio] o null si no es balanceable. */
            $extraerWrap = function ($html) {
                if (!is_string($html) || $html === '') { return null; }
                if (!preg_match('/<div\b[^>]*\bid=(["\'])rvTabWrap\1[^>]*>/i', $html, $mA, PREG_OFFSET_CAPTURE)) { return null; }
                $ini         = $mA[0][1];
                $finApertura = $ini + strlen($mA[0][0]);
                if (!preg_match_all('/<div\b|<\/div\s*>/i', $html, $mT, PREG_OFFSET_CAPTURE)) { return null; }
                $prof = 0;
                foreach ($mT[0] as $t) {
                    if ($t[1] < $finApertura) { continue; }
                    if ($t[0][1] === '/') {
                        if ($prof === 0) {
                            $fin = $t[1] + strlen($t[0]);
                            return [substr($html, $ini, $fin - $ini), $ini];
                        }
                        $prof--;
                    } else {
                        $prof++;
                    }
                }
                return null;
            };

            /* El candidato ya trae su wrap (comillas dobles o simples) */
            $traeWrap = function ($html) {
                return is_string($html)
                    && (stripos($html, 'id="rvTabWrap"') !== false
                        || stripos($html, "id='rvTabWrap'") !== false);
            };

            /* El candidato NO es el SHOW de una reserva */
            $noEsShow = function ($html) {
                return is_string($html) && stripos($html, 'rsvShowRoot') === false;
            };

            /* Respuesta 200 uniforme + rastro en el log para verificar
            🆕 v15-AGD · agenda_fecha en el log (flujo de agenda por fragmento) */
            $servir = function ($htmlServir, $modo, $detalle) use ($request) {
                Log::info('reservasFragmento: SERVIDO · modo=' . $modo . ' · ' . $detalle
                    . ' · bytes=' . strlen($htmlServir)
                    . ' · inmueble_id=' . $request->query('inmueble_id', 'todos')
                    . ' · agenda_fecha=' . $request->query('agenda_fecha', 'hoy'));
                return response($htmlServir, 200)
                    ->header('Content-Type', 'text/html; charset=UTF-8')
                    ->header('Cache-Control', 'no-store, max-age=0');
            };

            /* Prueba UNA vista con el contrato completo (RED 1 y RED 2).
            Devuelve un Response 200 si vale; null si hay que seguir. */
            $probarVista = function ($nombreVista, $origen) use ($extraerWrap, $traeWrap, $noEsShow, $servir, &$probadas, &$cortas) {
                $corta = implode('.', array_slice(explode('.', $nombreVista), -2));
                if (!view()->exists($nombreVista)) {
                    $probadas[] = $nombreVista . ' → NO EXISTE';
                    $cortas[]   = $corta . ':no-existe';
                    return null;
                }
                try {
                    $html = view($nombreVista)->render();
                } catch (\Throwable $eR) {
                    $probadas[] = $nombreVista . ' → ERROR RENDER: ' . $eR->getMessage();
                    $cortas[]   = $corta . ':error-render';
                    Log::warning('reservasFragmento: ' . $origen . ' falló al renderizar → ' . $nombreVista . ' · ' . $eR->getMessage());
                    return null;
                }
                if (!$traeWrap($html)) {
                    $probadas[] = $nombreVista . ' → descartada: sin #rvTabWrap propio';
                    $cortas[]   = $corta . ':sin-wrap';
                    Log::warning('reservasFragmento: ' . $origen . ' descartada (sin #rvTabWrap) → ' . $nombreVista);
                    return null;
                }
                if (!$noEsShow($html)) {
                    $probadas[] = $nombreVista . ' → descartada: ES el SHOW (#rsvShowRoot)';
                    $cortas[]   = $corta . ':es-show';
                    Log::warning('reservasFragmento: ' . $origen . ' descartada (es el SHOW) → ' . $nombreVista);
                    return null;
                }
                $ext = $extraerWrap($html);
                if ($ext === null) {
                    /* Sin bloque balanceable: solo se sirve entera si es un
                    parcial pequeño; una página completa NUNCA. */
                    if (strlen($html) < 30000) {
                        return $servir($html, 'vista-completa', $origen . '=' . $nombreVista . ' · parcial sin bloque balanceable');
                    }
                    $probadas[] = $nombreVista . ' → contrato OK pero bloque no balanceable (' . strlen($html) . ' bytes): NO se sirve para no pintar de más';
                    $cortas[]   = $corta . ':no-balanceable';
                    return null;
                }
                $bloque   = $ext[0];
                $preludio = substr($html, 0, $ext[1]);
                /* Si el preludio parece "página" (head/html/doc) o es grande
                (dashboard con KPIs, agenda…), se entrega SOLO el bloque. */
                $esPagina = stripos($preludio, '<head') !== false
                    || stripos($preludio, '<html') !== false
                    || stripos($preludio, '<!doctype') !== false
                    || strlen($preludio) > 4000;
                $htmlServir = $esPagina ? $bloque : ($preludio . $bloque);
                return $servir($htmlServir, 'vista', $origen . '=' . $nombreVista
                    . ' · bloque=' . strlen($bloque) . ' bytes'
                    . ' · preludio=' . ($esPagina ? '0 (solo bloque)' : strlen($preludio) . ' bytes'));
            };

            /* ── RED 1 · CANDIDATAS EXPLÍCITAS (baratas: exists + contrato) ──
            🆕 v15-AGD · PRIMERA: el nombre CORRECTO del archivo actual
            (tab-reservas.blade.php v28-DBG). El typo histórico v14
            ('tab-resrvas') queda como fallback por si el archivo viejo
            aún vive en el servidor: si existe el correcto, gana el correcto. */
            $candidatas1a = [
                'rsv.admin.partials.tab-reservas',
                'rsv.admin.partials.reservas.tab-reservas',
                'rsv.admin.partials.tab-resrvas',
                'rsv.admin.reservas.tab-reservas',
                'rsv.admin.partials.reservas.tab_reservas',
                'rsv.admin.partials.tab_reservas',
                'rsv.admin.reservas.tab_reservas',
            ];
            foreach ($candidatas1a as $v) {
                $r = $probarVista($v, 'candidata');
                if ($r !== null) { return $r; }
            }

            /* ── RED 2 · HALLAZGO POR CONTENIDO ──
            Prefiltro en el FUENTE (lectura de archivo, sin render): solo se
            renderizan (máx 4) los .blade.php que mencionan rvTabWrap. */
            $base = str_replace('\\', '/', resource_path('views'));
            $hallados = [];
            try {
                foreach (\Illuminate\Support\Facades\File::allFiles($base) as $f) {
                    $rutaFs = str_replace('\\', '/', $f->getPathname());
                    if (substr($f->getFilename(), -10) !== '.blade.php') { continue; }
                    $src = @file_get_contents($rutaFs);
                    if ($src === false || stripos($src, 'rvTabWrap') === false) { continue; }
                    $hallados[] = $rutaFs;
                    if (count($hallados) >= 20) { break; }
                }
            } catch (\Throwable $eScan) {
                Log::warning('reservasFragmento: escaneo falló: ' . $eScan->getMessage());
                $probadas[] = 'escaneo de resources/views falló: ' . $eScan->getMessage();
            }
            if (empty($hallados)) {
                $probadas[] = 'hallazgo: ningún .blade.php menciona rvTabWrap bajo resources/views';
                $cortas[]   = 'views:sin rvTabWrap';
            } else {
                /* Prioridad: vistas bajo /rsv/ y rutas más cortas (parciales
                antes que páginas completas) */
                usort($hallados, function ($a, $b) {
                    $pa = (stripos($a, '/rsv/') === false ? '1' : '0') . str_pad(strlen($a), 6, '0', STR_PAD_LEFT) . $a;
                    $pb = (stripos($b, '/rsv/') === false ? '1' : '0') . str_pad(strlen($b), 6, '0', STR_PAD_LEFT) . $b;
                    return strcmp($pa, $pb);
                });
                $probadas[] = 'hallazgo: archivos con rvTabWrap en fuente → ' . implode(' | ', array_slice($hallados, 0, 5));
                foreach (array_slice($hallados, 0, 4) as $rutaFs) {
                    if (strpos($rutaFs, $base . '/') !== 0) { continue; }
                    $nombre = str_replace('/', '.', substr($rutaFs, strlen($base) + 1, -10));
                    if (in_array($nombre, $candidatas1a, true)) { continue; }
                    $r = $probarVista($nombre, 'hallazgo');
                    if ($r !== null) { return $r; }
                }
            }

            /* ── RED 3 · PLAN MAESTRO: el PROPIO método del dashboard ──
            index() (o dashboard()) construye los datos y renderiza la vista
            que SÍ trae el #rvTabWrap poblado. Se extrae SOLO el bloque. */
            foreach (['index', 'dashboard'] as $metodoDash) {
                if (!method_exists($this, $metodoDash)) {
                    $probadas[] = 'método ' . $metodoDash . '() no existe en este controlador';
                    continue;
                }
                try {
                    $respDash = app()->call([$this, $metodoDash]);
                } catch (\Throwable $eIdx) {
                    Log::warning('reservasFragmento: llamada interna a ' . $metodoDash . '() falló: ' . $eIdx->getMessage());
                    $probadas[] = $metodoDash . '() → EXCEPCIÓN: ' . $eIdx->getMessage();
                    $cortas[]   = $metodoDash . ':excepcion';
                    continue;
                }
                if ($respDash instanceof \Illuminate\View\View) {
                    $htmlDash = $respDash->render();
                } elseif (is_object($respDash) && method_exists($respDash, 'getContent')) {
                    $htmlDash = $respDash->getContent();
                } else {
                    $probadas[] = $metodoDash . '() devolvió ' . (is_object($respDash) ? get_class($respDash) : gettype($respDash)) . ' (se esperaba View/Response)';
                    continue;
                }
                if (!is_string($htmlDash) || $htmlDash === '') {
                    $probadas[] = $metodoDash . '() devolvió HTML vacío';
                    continue;
                }
                if (!$traeWrap($htmlDash)) {
                    $probadas[] = $metodoDash . '(): el dashboard (' . strlen($htmlDash) . ' bytes) no trae #rvTabWrap';
                    continue;
                }
                $extDash = $extraerWrap($htmlDash);
                if ($extDash === null) {
                    $probadas[] = $metodoDash . '(): #rvTabWrap presente pero bloque no balanceable — NO se sirve';
                    $cortas[]   = $metodoDash . ':no-balanceable';
                    continue;
                }
                $bloqueDash = $extDash[0];
                if (!$noEsShow($bloqueDash)) {
                    $probadas[] = $metodoDash . '(): el bloque del tab contiene rsvShowRoot — NO se sirve';
                    $cortas[]   = $metodoDash . ':bloque-es-show';
                    continue;
                }
                return $servir($bloqueDash, 'dashboard-' . $metodoDash,
                    'bloque extraído del HTML del método ' . $metodoDash . '()'
                    . ' · html=' . strlen($htmlDash) . ' bytes · bloque=' . strlen($bloqueDash) . ' bytes');
            }

            /* ── RED 4 · MODO COMPATIBLE v10: envolver el parcial de lista ── */
            $candidatasWrap = [
                'rsv.admin.partials.reservas.lista',
                'rsv.admin.partials.reservas.listado',
                'rsv.admin.partials.reservas.tabla',
                'rsv.admin.partials.reservas.tab',
                'rsv.admin.partials.reservas.index',
                'rsv.admin.partials.reservas.lista_reservas',
                'rsv.admin.partials.reservas.lista-reservas',
                'rsv.admin.partials.reservas.reservas-tab',
                'rsv.admin.reservas.lista',
                'rsv.admin.partials.lista-reservas',
                'rsv.admin.partials.reservas-tab',
            ];
            foreach ($candidatasWrap as $v) {
                if (!view()->exists($v)) {
                    $probadas[] = $v . ' → NO EXISTE';
                    continue;
                }
                try {
                    $html = view($v)->render();
                } catch (\Throwable $eR4) {
                    $probadas[] = $v . ' → ERROR RENDER: ' . $eR4->getMessage();
                    $cortas[]   = implode('.', array_slice(explode('.', $v), -2)) . ':error-render';
                    Log::warning('reservasFragmento: candidata wrap falló al renderizar → ' . $v . ' · ' . $eR4->getMessage());
                    continue;
                }
                if (!$noEsShow($html)) {
                    $probadas[] = $v . ' → ES el SHOW (#rsvShowRoot): NO se toca';
                    $cortas[]   = implode('.', array_slice(explode('.', $v), -2)) . ':es-show';
                    Log::warning('reservasFragmento: candidata wrap descartada (es el SHOW) → ' . $v);
                    continue;
                }
                if ($traeWrap($html)) {
                    $probadas[] = $v . ' → ya trae #rvTabWrap propio (no se envuelve dos veces)';
                    continue;
                }
                if (strlen($html) < 200 || strlen($html) > 30000) {
                    $probadas[] = $v . ' → tamaño sospechoso (' . strlen($html) . ' bytes): NO se envuelve';
                    $cortas[]   = implode('.', array_slice(explode('.', $v), -2)) . ':tamaño';
                    continue;
                }
                return $servir('<div id="rvTabWrap">' . $html . '</div>', 'wrap',
                    'vista=' . $v . ' · envuelta en el wrap (modo compatible v10)');
            }

            /* ── Nada válido → fallo LIMPIO, visible en el panel 🐛 ── */
            $msg = 'error: fragmento v14: sin fragmento del tab de reservas (detalle: '
                . implode(' · ', array_slice($cortas, 0, 6))
                . '). Todo el detalle está en laravel.log · "reservasFragmento". Si persiste, pega el @include del tab del dashboard.';
            Log::error('reservasFragmento: ninguna red entregó el tab', ['probadas' => $probadas]);

            return response($msg, 500)
                ->header('Content-Type', 'text/plain; charset=UTF-8');

        } catch (\Throwable $e) {
            /* [DEBUG-FIX] Log con archivo/línea/trace + body con la causa */
            Log::error('reservasFragmento: ' . $e->getMessage(), [
                'archivo' => $e->getFile(),
                'linea'   => $e->getLine(),
                'trace'   => substr($e->getTraceAsString(), 0, 1500),
            ]);
            return response('error: ' . $e->getMessage() . ' @ ' . basename($e->getFile()) . ':' . $e->getLine(), 500)
                ->header('Content-Type', 'text/plain; charset=UTF-8');
        }
    }


    /**
     * [NIVEL 3] - Devuelve el fragmento de la vista de detalle/lista de reservas
     * partials/reservas/reservas.blade.php
     */
    public function reservasListaFragmento(Request $request)
    {
        try {
            $inmuebleId = $request->get('inmueble_id');
            $inmueble = $inmuebleId ? CatalogoInmueble::find($inmuebleId) : null;

            // Ajusta la ruta de la vista según la estructura de carpetas de tu proyecto
            return response()
                ->view('rsv.admin.partials.reservas.reservas', compact('inmueble', 'inmuebleId'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (\Throwable $e) {
            Log::error('reservasListaFragmento Error: ' . $e->getMessage());
            return response('Error 500 al cargar el fragmento: ' . $e->getMessage(), 500)
                ->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /** [NIVEL 3] - partials/reservas/historial_estado.blade.php */
    public function historialEstadoFragmento(Request $request)
    {
        try {
            $historiales = HistorialEstado::with(['reserva.inmueble:id,name', 'statusNuevo', 'statusAnterior'])
                            ->orderBy('created_at', 'desc')
                            ->take(50)->get();

            return response()
                ->view('rsv.admin.partials.reservas.historial_estado', compact('historiales'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (\Throwable $e) {
            Log::error('historialEstadoFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /** [NIVEL 3] - partials/reservas/origen_reserva.blade.php */
    public function origenReservaFragmento(Request $request)
    {
        try {
            $origenes = OrigenReserva::orderBy('nombre', 'asc')->get();
            return response()
                ->view('rsv.admin.partials.reservas.origen_reserva', compact('origenes'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (\Throwable $e) {
            Log::error('origenReservaFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /** [NIVEL 3] - partials/reservas/reserva_huespedt.blade.php */
    public function reservaHuespedesFragmento(Request $request)
    {
        try {
            $inmuebleId = $request->get('inmueble_id');
            $rhGlobal = empty($inmuebleId) || $inmuebleId === 'todos';

            // Consulta idéntica a tu ReservaHuespedController
            $query = ReservaHuesped::query()
                ->with(['reserva:id,codigo_reserva,id_rsv_catalogo_inmueble', 'reserva.inmueble:id,name', 'registrador:id,name']);

            if (!$rhGlobal && is_numeric($inmuebleId)) {
                $query->whereHas('reserva', function ($qq) use ($inmuebleId) {
                    $qq->where('id_rsv_catalogo_inmueble', $inmuebleId);
                });
            }

            $huespedes = $query->orderBy('created_at', 'desc')->get();

            // Opciones para el select del modal
            $rhReservasOpt = collect();
            if (class_exists(\App\Models\Rsv\Reserva::class)) {
                $rq = \App\Models\Rsv\Reserva::query()->select('id', 'codigo_reserva');
                if (!$rhGlobal && is_numeric($inmuebleId)) { $rq->where('id_rsv_catalogo_inmueble', $inmuebleId); }
                $rhReservasOpt = $rq->orderBy('created_at', 'desc')->limit(500)->get()
                    ->map(fn ($r) => ['id' => $r->id, 'cod' => $r->codigo_reserva])->values();
            }

            return response()
                ->view('rsv.admin.partials.reservas.reserva_huespedt', compact('huespedes', 'rhReservasOpt', 'inmuebleId'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (\Throwable $e) {
            Log::error('reservaHuespedesFragmento: ' . $e->getMessage());
            return response('error: ' . $e->getMessage(), 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /** [NIVEL 3] - partials/reservas/status.blade.php */
    public function statusFragmento(Request $request)
    {
        try {
            // Se usa orderBy('name') igual que en la vista y se cargan los conteos
            $stTotalesGlobales = collect();
            if (class_exists(\App\Models\Rsv\Reserva::class)) {
                $stTotalesGlobales = \App\Models\Rsv\Reserva::query()
                    ->selectRaw('id_rsv_statuses, COUNT(*) as c')
                    ->groupBy('id_rsv_statuses')
                    ->pluck('c', 'id_rsv_statuses');
            }

            $statuses = Status::withCount('reservas')->orderBy('name', 'asc')->get();

            return response()
                ->view('rsv.admin.partials.reservas.status', compact('statuses'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (\Throwable $e) {
            Log::error('statusFragmento: ' . $e->getMessage());
            return response('error 500: ' . $e->getMessage(), 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }
    /**
     * [AUXILIAR RESERVAS] - Autocompletado de Usuarios
     * Busca por cédula, nombre o email para el modal de nueva reserva o endoso.
     */
    public function buscarUsuarios(Request $request)
    {
        try {
            $q = trim((string) $request->query('q'));
            if (mb_strlen($q) < 2) {
                return response()->json(['success' => true, 'data' => []]);
            }

            $tieneNid   = \Illuminate\Support\Facades\Schema::hasColumn('users', 'nid');
            $tieneEmail = \Illuminate\Support\Facades\Schema::hasColumn('users', 'email');
            $campos = array_values(array_filter(['id', 'name', $tieneNid ? 'nid' : null, $tieneEmail ? 'email' : null]));

            $users = \App\Models\User::query()
                ->where(function ($w) use ($q, $tieneNid, $tieneEmail) {
                    $like = '%' . $q . '%';
                    $w->where('name', 'like', $like);
                    if ($tieneEmail) $w->orWhere('email', 'like', $like);
                    if ($tieneNid)   $w->orWhere('nid', 'like', $like);
                })
                ->orderBy('name')
                ->limit(10)
                ->get($campos);

            $data = $users->map(function ($u) use ($tieneNid, $tieneEmail) {
                return [
                    'id'    => $u->id,
                    'name'  => (string) $u->name,
                    'nid'   => $tieneNid   ? (string) ($u->nid ?? '')   : '',
                    'email' => $tieneEmail ? (string) ($u->email ?? '') : '',
                ];
            })->values();

            return response()->json(['success' => true, 'data' => $data]);

        } catch (\Throwable $e) {
            Log::error('buscarUsuarios: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'error', 'data' => []]);
        }
    }


    // =========================================================================
    // SECCIÓN 3/5: CALENDARIO Y BLOQUEOS
    // =========================================================================

    /**
     * [NIVEL 2] - TAB CALENDARIO
     * Refresca el contenedor principal del calendario (partials/tab-calendario.blade.php).
     */
    public function calendarioFragmento(Request $request)
    {
        try {
            $inmuebleId = $request->query('inmueble_id');
            $listaInmuebles = CatalogoInmueble::select('id', 'name')->orderBy('name', 'asc')->get();

            $inmueble = null;
            if ($inmuebleId && $inmuebleId !== 'todos' && is_numeric($inmuebleId)) {
                $inmueble = CatalogoInmueble::find($inmuebleId);
            }

            return response()
                ->view('rsv.admin.partials.tab-calendario', compact('listaInmuebles', 'inmueble', 'inmuebleId'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('calendarioFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /**
     * [NIVEL 3] - partials/calendario/bloqueos.blade.php
     */
    public function bloqueosFragmento(Request $request)
    {
        try {
            $inmuebleId = $request->query('inmueble_id');
            $inmuebles = CatalogoInmueble::orderBy('name')->get(['id', 'name']);
            $query = BloqueoCalendario::with('inmueble:id,name');

            if ($inmuebleId && $inmuebleId !== 'todos' && is_numeric($inmuebleId)) {
                $query->where('id_rsv_catalogo_inmueble', $inmuebleId);
            }
            $bloqueos = $query->orderBy('fecha_inicio', 'desc')->get();

            return response()
                ->view('rsv.admin.partials.calendario.bloqueos', compact('bloqueos', 'inmuebles', 'inmuebleId'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('bloqueosFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }


    // =========================================================================
    // SECCIÓN 4/5: FINANZAS Y PAGOS
    // =========================================================================

    /**
     * [NIVEL 2] - TAB FINANZAS
     * Refresca el contenedor principal de finanzas (partials/tab-finanzas.blade.php).
     */
    public function finanzasFragmento(Request $request)
    {
        try {
            $inmuebleId = $request->query('inmueble_id');
            $pagina     = max(1, (int) $request->query('page_finanzas', 1));
            $query = TransaccionFinanciera::with(['reserva.inmueble:id,name', 'pasarela']);

            if ($inmuebleId && $inmuebleId !== 'todos' && is_numeric($inmuebleId)) {
                $query->whereHas('reserva', fn($q) => $q->where('id_rsv_catalogo_inmueble', $inmuebleId));
            }

            $transacciones = $query->orderByDesc('created_at')
                ->paginate(10, ['*'], 'page_finanzas', $pagina)
                ->appends($request->query());

            $pasarelas = Pasarela::orderBy('nombre')->get();

            return response()
                ->view('rsv.admin.partials.tab-finanzas', compact('transacciones', 'pasarelas'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('finanzasFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /**
     * [NIVEL 3] - partials/finanzas/transacciones.blade.php
     */
    public function transaccionesFragmento(Request $request)
    {
        try {
            $inmuebleId = $request->query('inmueble_id');
            $pagina     = max(1, (int) $request->query('page_finanzas', 1));
            $query = TransaccionFinanciera::with(['reserva.inmueble:id,name', 'pasarela']);

            if ($inmuebleId && $inmuebleId !== 'todos' && is_numeric($inmuebleId)) {
                $query->whereHas('reserva', fn($q) => $q->where('id_rsv_catalogo_inmueble', $inmuebleId));
            }

            $transacciones = $query->orderByDesc('created_at')
                ->paginate(10, ['*'], 'page_finanzas', $pagina)
                ->appends($request->query());
                
            $pasarelas = Pasarela::orderBy('nombre')->get();

            return response()
                ->view('rsv.admin.partials.finanzas.transacciones', compact('transacciones', 'pasarelas'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('transaccionesFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /**
     * [NIVEL 3] - partials/finanzas/pasarela.blade.php
     */
    public function pasarelasFragmento()
    {
        try {
            $pasarelas = Pasarela::orderBy('nombre')->get();
            return response()
                ->view('rsv.admin.partials.finanzas.pasarela', compact('pasarelas'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('pasarelasFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }


    // =========================================================================
    // SECCIÓN 5/5: AUDITORÍA Y LOGS
    // =========================================================================

    /**
     * [NIVEL 2] - TAB AUDITORÍA
     * Refresca el contenedor principal de auditoría (partials/auditoria-tab.blade.php).
     */
    public function auditoriaFragmento(Request $request)
    {
        try {
            $pagina = max(1, (int) $request->query('page_auditoria', 1));
            $auditoria = AuditLog::latest()
                            ->paginate(10, ['*'], 'page_auditoria', $pagina)
                            ->appends($request->query());

            return response()
                ->view('rsv.admin.partials.auditoria-tab', compact('auditoria'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (\Throwable $e) {
            Log::error('auditoriaFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /**
     * [NIVEL 3] - partials/auditorias/informes.blade.php
     */
    public function auditoriaInformesFragmento(Request $request)
    {
        try {
            $pagina = max(1, (int) $request->query('page_auditoria', 1));
            $auditoria = AuditLog::latest()
                            ->paginate(10, ['*'], 'page_auditoria', $pagina)
                            ->appends($request->query());

            return response()
                ->view('rsv.admin.partials.auditorias.informes', compact('auditoria'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (\Throwable $e) {
            Log::error('auditoriaInformesFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

}