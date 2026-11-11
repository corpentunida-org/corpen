<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Carbon\Carbon;
use Throwable;

// Modelos PARTE_1: "Configuración Propiedad"
use App\Models\Rsv\CatalogoInmueble;
use App\Models\Rsv\TarifaTemporada;
use App\Models\Rsv\BloqueoCalendario;
use App\Models\Rsv\TipoInmueble;
use App\Models\Rsv\InmuebleMultimedia;

// Modelos PARTE_2: "Gestión y Cliente"
use App\Models\Rsv\Reserva;
use App\Models\Rsv\Pasarela;
use App\Models\Rsv\ReservaHuesped;
use App\Models\Rsv\HistorialEstado;
use App\Models\Rsv\HistorialEndoso;
use App\Models\Rsv\ItinerarioEvento;
use App\Models\Rsv\Mensaje;
use App\Models\Rsv\Review;
use App\Models\Rsv\TipoReceptor;
use App\Models\Rsv\TransaccionFinanciera;

/**
 * Controlador Maestro del Portal de Autogestión del Cliente (Orquestador).
 * Gestiona la carga principal del portal, cálculo de KPIs y fragmentos AJAX
 * (NIVEL 2 = tabs · NIVEL 3 = micro-fragmentos).
 *
 * Diferencia clave con AdminDashboardController: TODA consulta queda
 * SCOPEADA al usuario autenticado (id_user). El cliente jamás ve datos ajenos.
 */
class ClientePortalController extends Controller
{
    /** Mapa whitelisted de fragmentos AJAX (tab => vista). CONTRATO HISTÓRICO INTACTO. */
    private const FRAGMENTOS = [
        'nueva-buscador'       => 'rsv.cliente.partials.nueva-reserva.buscador',
        'nueva-checkout'       => 'rsv.cliente.partials.nueva-reserva.checkout',
        'reservas-activas'     => 'rsv.cliente.partials.mis-reservas.activas-pasadas',
        'reservas-itinerario'  => 'rsv.cliente.partials.mis-reservas.itinerario',
        'reservas-huespedes'   => 'rsv.cliente.partials.mis-reservas.huespedes',
        'historial-estados'    => 'rsv.cliente.partials.historial.estados',
        'historial-endosos'    => 'rsv.cliente.partials.historial.endosos',
        'chat-mensajes'        => 'rsv.cliente.partials.chat.mensajes',
        'chat-receptores'      => 'rsv.cliente.partials.chat.receptores',
        'pagos-transacciones'  => 'rsv.cliente.partials.pagos.transacciones',
        'pagos-pasarelas'      => 'rsv.cliente.partials.pagos.pasarelas',
        'pagos-reviews'        => 'rsv.cliente.partials.pagos.reviews',
    ];

    /** Delegación a métodos dedicados. */
    private const FRAGMENTOS_METODOS = [
        // NIVEL 2 · tabs
        'nueva-reserva'        => 'nuevaReservaFragmento',
        'mis-reservas'         => 'misReservasFragmento',
        'historial'            => 'historialFragmento',
        'chat'                 => 'chatFragmento',
        'pagos'                => 'pagosFragmento',
        // NIVEL 3 · micro-fragmentos
        'nueva-buscador'       => 'nuevaReservaBuscadorFragmento',
        'nueva-checkout'       => 'nuevaReservaCheckoutFragmento',
        'reservas-activas'     => 'misReservasActivasFragmento',
        'reservas-itinerario'  => 'misReservasItinerarioFragmento',
        'reservas-huespedes'   => 'misReservasHuespedesFragmento',
        'historial-estados'    => 'historialEstadosFragmento',
        'historial-endosos'    => 'historialEndososFragmento',
        'chat-mensajes'        => 'chatMensajesFragmento',
        'chat-receptores'      => 'chatReceptoresFragmento',
        'pagos-transacciones'  => 'pagosTransaccionesFragmento',
        'pagos-pasarelas'      => 'pagosPasarelasFragmento',
        'pagos-reviews'        => 'pagosReviewsFragmento',
    ];

    /** Cache en-memoria de sondeos de columnas. */
    private static array $cacheColumnas = [];


    // =========================================================================
    // NIVEL 1: PORTAL PRINCIPAL (Carga completa inicial + KPIs)
    // =========================================================================

    /**
     * Renderiza la vista principal del cliente (KPIs).
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();
        $hoy    = Carbon::today()->startOfDay();

        // 1. Inicialización de ecosistema de variables
        $kpiTotal = $kpiActivas = $kpiAbonado = $kpiSaldo = 0;
        $kpiMensajes = $kpiReviews = $kpiHistorial = 0;
        $kpiProximo  = null;
        $misReservas = collect();
        $error       = null;

        // 2. Lógica y KPIs Scopeados
        try {
            $misReservas = Reserva::query()
                ->with($this->conRelaciones(Reserva::class, ['inmueble', 'status', 'transacciones']))
                ->where($this->colReservaUser(), $userId)
                ->orderByDesc('created_at')
                ->get();

            $kpiTotal = $misReservas->count();

            foreach ($misReservas as $r) {
                [$fi, $ff] = $this->fechasDeReserva($r);

                if ($fi && $ff && ($hoy->betweenIncluded($fi, $ff) || $fi->gte($hoy))) { $kpiActivas++; }
                if ($fi && $fi->gte($hoy) && (!$kpiProximo || $fi->lt($kpiProximo)))  { $kpiProximo = $fi; }

                $kpiSaldo += $this->montoDeReserva($r);

                foreach ($this->transaccionesDeReserva($r) as $tx) {
                    $edo = mb_strtolower($this->estadoDeTransaccion($tx));
                    if (str_contains($edo, 'anulad')) { continue; }
                    if (str_contains($edo, 'complet') || str_contains($edo, 'aprob') || str_contains($edo, 'pagad')) {
                        $kpiAbonado += $this->montoDeTransaccion($tx);
                    }
                }
            }
            $kpiSaldo = max(0, $kpiSaldo - $kpiAbonado);

            $idsReservas = $misReservas->pluck('id')->all();

            try {
                $kpiMensajes = (class_exists(Mensaje::class) && !empty($idsReservas))
                    ? Mensaje::whereIn($this->colFkReserva(Mensaje::class), $idsReservas)->count()
                    : 0;
            } catch (Throwable $e) { Log::warning('index: KPI mensajes falló → ' . $e->getMessage()); }

            try {
                $kpiReviews = class_exists(Review::class)
                    ? Review::where($this->colResenaAutor(), $userId)->count()
                    : 0;
            } catch (Throwable $e) { Log::warning('index: KPI reviews falló → ' . $e->getMessage()); }

            $kpiHistorial = $kpiTotal;

        } catch (Throwable $e) {
            $error = 'Ocurrió un error al calcular tus datos. Por favor, recargue la página.';
            Log::error('ClientePortalController@index: ' . $e->getMessage(), [
                'archivo' => $e->getFile(), 'linea' => $e->getLine()
            ]);
        }

        return view('rsv.cliente.portal', compact(
            'kpiTotal', 'kpiActivas', 'kpiProximo', 'kpiAbonado', 'kpiSaldo',
            'kpiMensajes', 'kpiReviews', 'kpiHistorial', 'misReservas', 'error'
        ));
    }

    /**
     * Dispatcher AJAX (Orquestador de fragmentos).
     */
    public function fragmento(Request $request)
    {
        $key = (string) $request->query('tab', '');

        // RED 1: delegación a métodos dedicados
        $metodo = self::FRAGMENTOS_METODOS[$key] ?? null;
        if ($metodo && method_exists($this, $metodo)) {
            try {
                $respuesta = app()->call([$this, $metodo]);
                if (is_object($respuesta) && method_exists($respuesta, 'getStatusCode') && $respuesta->getStatusCode() === 200) {
                    return $respuesta;
                }
            } catch (Throwable $e) {
                Log::warning('fragmento: delegación a ' . $metodo . ' falló → ' . $e->getMessage());
            }
        }

        // RED 2: modo legacy (sin datos)
        try {
            if (isset(self::FRAGMENTOS[$key]) && view()->exists(self::FRAGMENTOS[$key])) {
                return response()
                    ->view(self::FRAGMENTOS[$key])
                    ->header('Cache-Control', 'no-store, max-age=0');
            }
        } catch (Throwable $e) {
            Log::error('fragmento (legacy): ' . $e->getMessage());
        }

        return response('<div class="alert alert-warning m-3">Fragmento no disponible.</div>', 200);
    }


    // =========================================================================
    // SECCIÓN 1/5: NUEVA RESERVA (Buscador público + Checkout)
    // =========================================================================

    /** [NIVEL 2] - TAB NUEVA RESERVA */
    public function nuevaReservaFragmento(Request $request)
    {
        try {
            $inmuebles = $this->inmueblesActivos();
            $destinos  = $this->destinosDisponibles($inmuebles);
            $tipos     = $this->tiposInmuebleActivos();
            $filtros   = $this->filtrosBusqueda($request);

            $vista = $this->primeraVista([
                'rsv.cliente.partials.nueva-reserva-tab',
                'rsv.cliente.partials.nueva_reserva-tab',
                'rsv.cliente.partials.nueva-reserva.tab',
            ]);
            if (!$vista) { throw new \RuntimeException('No existe partials/nueva-reserva-tab.blade.php'); }

            return response()
                ->view($vista, compact('inmuebles', 'destinos', 'tipos', 'filtros'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('nuevaReservaFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /** [NIVEL 3] - partials/nueva-reserva/buscador.blade.php */
    public function nuevaReservaBuscadorFragmento(Request $request)
    {
        try {
            $inmuebles = $this->inmueblesActivos();
            $destinos  = $this->destinosDisponibles($inmuebles);
            $tipos     = $this->tiposInmuebleActivos();
            $filtros   = $this->filtrosBusqueda($request);

            $vista = $this->primeraVista(['rsv.cliente.partials.nueva-reserva.buscador']);
            if (!$vista) { throw new \RuntimeException('No existe partials/nueva-reserva/buscador.blade.php'); }

            return response()
                ->view($vista, compact('inmuebles', 'destinos', 'tipos', 'filtros'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('nuevaReservaBuscadorFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /** [NIVEL 3] - partials/nueva-reserva/checkout.blade.php */
    public function nuevaReservaCheckoutFragmento(Request $request)
    {
        try {
            $inmuebleId = $request->query('inmueble_id') ?? $request->query('id_inmueble');
            if (!$inmuebleId || !is_numeric($inmuebleId)) {
                return response('<div class="alert alert-warning m-3">Selecciona un inmueble del buscador para continuar.</div>', 200)
                    ->header('Cache-Control', 'no-store, max-age=0');
            }

            $inmueble = CatalogoInmueble::with([
                    'multimedia' => fn ($q) => $q->orderBy('es_portada', 'desc')->orderBy('orden', 'asc'),
                    'latestTarifa', 'tipoInmueble',
                ])->findOrFail($inmuebleId);

            $filtros = $this->filtrosBusqueda($request);
            $fInicio = $filtros['fecha_inicio'];
            $fFin    = $filtros['fecha_fin'];
            $noches  = 0;
            $precioNoche   = optional($inmueble->latestTarifa)->precio_noche;
            $estimadoTotal = null;

            if ($fInicio && $fFin) {
                $ini = Carbon::parse($fInicio)->startOfDay();
                $fin = Carbon::parse($fFin)->startOfDay();
                if ($fin->gt($ini)) { $noches = (int) ceil($ini->diffInDays($fin)); }
            }
            if ($noches > 0 && is_numeric($precioNoche)) { $estimadoTotal = (float) $precioNoche * $noches; }

            // Disponibilidad y Bloqueos
            $colFi  = $this->colReservaFechaInicio();
            $colFf  = $this->colReservaFechaFin();
            $colInm = $this->colReservaInmueble();

            $reservasOcupan = Reserva::query()->where($colInm, $inmueble->id)
                ->when($fInicio && $fFin, fn($q) => $q->where($colFi, '<', $fFin)->where($colFf, '>', $fInicio))
                ->orderBy($colFi)->limit(50)->get();

            $colBloqInm = $this->colTabla((new BloqueoCalendario)->getTable(), ['id_rsv_catalogo_inmueble', 'inmueble_id', 'id_inmueble'], 'id_rsv_catalogo_inmueble');
            $colBloqFi  = $this->colTabla((new BloqueoCalendario)->getTable(), ['fecha_inicio', 'inicio', 'desde'], 'fecha_inicio');
            $colBloqFf  = $this->colTabla((new BloqueoCalendario)->getTable(), ['fecha_fin', 'fin', 'hasta'], 'fecha_fin');

            $bloqueos = BloqueoCalendario::query()->where($colBloqInm, $inmueble->id)
                ->when($fInicio && $fFin, fn($q) => $q->where($colBloqFi, '<', $fFin)->where($colBloqFf, '>', $fInicio))
                ->orderBy($colBloqFi)->get();

            $pasarelas = $this->pasarelasActivas();
            $cliente   = Auth::user();

            $vista = $this->primeraVista(['rsv.cliente.partials.nueva-reserva.checkout']);
            if (!$vista) { throw new \RuntimeException('No existe partials/nueva-reserva/checkout.blade.php'); }

            return response()
                ->view($vista, compact('inmueble', 'filtros', 'noches', 'precioNoche', 'estimadoTotal', 'reservasOcupan', 'bloqueos', 'pasarelas', 'cliente'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('nuevaReservaCheckoutFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /** ENDPOINT API: OBTENER DISPONIBILIDAD (Calendario JS Frontend) */
    public function getDisponibilidadInmueble(Request $request, $id): JsonResponse
    {
        try {
            $mes  = $request->query('month', Carbon::now()->month);
            $anio = $request->query('year', Carbon::now()->year);

            $inicioMes = Carbon::createFromDate($anio, $mes, 1)->startOfMonth();
            $finMes    = Carbon::createFromDate($anio, $mes, 1)->endOfMonth();
            $fechasOcupadas = [];

            // Reservas
            $colFi  = $this->colReservaFechaInicio();
            $colFf  = $this->colReservaFechaFin();
            $reservas = Reserva::query()->where($this->colReservaInmueble(), $id)
                ->where(function($query) use ($inicioMes, $finMes, $colFi, $colFf) {
                    $query->whereBetween($colFi, [$inicioMes, $finMes])
                          ->orWhereBetween($colFf, [$inicioMes, $finMes])
                          ->orWhere(fn($q) => $q->where($colFi, '<', $inicioMes)->where($colFf, '>', $finMes));
                })->get();

            foreach ($reservas as $res) {
                $fiCarbon = Carbon::parse($res->{$colFi})->startOfDay();
                $ffCarbon = Carbon::parse($res->{$colFf})->startOfDay();
                for ($d = $fiCarbon->copy(); $d->lte($ffCarbon); $d->addDay()) {
                    $fechasOcupadas[] = $d->format('Y-m-d');
                }
            }

            // Bloqueos
            $colBFi = $this->colTabla((new BloqueoCalendario)->getTable(), ['fecha_inicio', 'inicio'], 'fecha_inicio');
            $colBFf = $this->colTabla((new BloqueoCalendario)->getTable(), ['fecha_fin', 'fin'], 'fecha_fin');
            $bloqueos = BloqueoCalendario::query()->where($this->colTabla((new BloqueoCalendario)->getTable(), ['id_rsv_catalogo_inmueble', 'inmueble_id'], 'id_rsv_catalogo_inmueble'), $id)
                ->where(function($query) use ($inicioMes, $finMes, $colBFi, $colBFf) {
                    $query->whereBetween($colBFi, [$inicioMes, $finMes])
                          ->orWhereBetween($colBFf, [$inicioMes, $finMes])
                          ->orWhere(fn($q) => $q->where($colBFi, '<', $inicioMes)->where($colBFf, '>', $finMes));
                })->get();

            foreach ($bloqueos as $blq) {
                $fiCarbon = Carbon::parse($blq->{$colBFi})->startOfDay();
                $ffCarbon = Carbon::parse($blq->{$colBFf})->startOfDay();
                for ($d = $fiCarbon->copy(); $d->lte($ffCarbon); $d->addDay()) {
                    $fechasOcupadas[] = $d->format('Y-m-d');
                }
            }

            return response()->json([
                'success' => true, 'inmueble_id' => $id, 'mes' => $mes, 'anio' => $anio,
                'ocupadas' => array_values(array_unique($fechasOcupadas))
            ]);
        } catch (Throwable $e) {
            Log::error('getDisponibilidadInmueble: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Error al consultar disponibilidad'], 500);
        }
    }


    // =========================================================================
    // SECCIÓN 2/5: MIS RESERVAS
    // =========================================================================

    /** [NIVEL 2] - TAB MIS RESERVAS */
    public function misReservasFragmento(Request $request)
    {
        try {
            [$todas, $activas, $pasadas] = $this->reservasDelCliente();
            $vista = $this->primeraVista([
                'rsv.cliente.partials.mis-reservas-tab',
                'rsv.cliente.partials.mis_reservas-tab',
                'rsv.cliente.partials.mis-reservas.tab',
            ]);
            if (!$vista) { throw new \RuntimeException('No existe partials/mis-reservas-tab.blade.php'); }

            return response()
                ->view($vista, compact('todas', 'activas', 'pasadas'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('misReservasFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /** [NIVEL 3] - partials/mis-reservas/activas-pasadas.blade.php */
    public function misReservasActivasFragmento(Request $request)
    {
        try {
            [$todas, $activas, $pasadas] = $this->reservasDelCliente();
            $vista = $this->primeraVista(['rsv.cliente.partials.mis-reservas.activas-pasadas']);
            if (!$vista) { throw new \RuntimeException('No existe partials/mis-reservas/activas-pasadas.blade.php'); }

            return response()
                ->view($vista, compact('todas', 'activas', 'pasadas'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('misReservasActivasFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /** [NIVEL 3] - partials/mis-reservas/itinerario.blade.php */
    public function misReservasItinerarioFragmento(Request $request)
    {
        try {
            $reservaId = $request->query('reserva_id') ?? $request->query('id_reserva');
            if (!$reservaId || !is_numeric($reservaId)) {
                return response('<div class="alert alert-warning m-3">Selecciona una reserva para ver su itinerario.</div>', 200)
                    ->header('Cache-Control', 'no-store, max-age=0');
            }

            $reserva = Reserva::query()
                ->with($this->conRelaciones(Reserva::class, ['inmueble', 'status', 'transacciones']))
                ->where($this->colReservaUser(), Auth::id())
                ->findOrFail($reservaId);

            $eventos = collect();
            if (method_exists($reserva, 'itinerarioEventos')) {
                try { $eventos = $reserva->itinerarioEventos()->get(); }
                catch (Throwable $e) { Log::warning('itinerario: relación falló → ' . $e->getMessage()); }
            } elseif (class_exists(ItinerarioEvento::class)) {
                try {
                    $colEvRes   = $this->colFkReserva(ItinerarioEvento::class);
                    $colEvOrden = $this->colTabla((new ItinerarioEvento)->getTable(), ['fecha_evento', 'fecha', 'fecha_inicio', 'created_at'], 'fecha_evento');
                    $eventos    = ItinerarioEvento::query()->where($colEvRes, $reserva->id)->orderBy($colEvOrden)->get();
                } catch (Throwable $e) { }
            }

            $vista = $this->primeraVista(['rsv.cliente.partials.mis-reservas.itinerario']);
            if (!$vista) { throw new \RuntimeException('No existe partials/mis-reservas/itinerario.blade.php'); }

            return response()
                ->view($vista, compact('reserva', 'eventos'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response('<div class="alert alert-danger m-3">La reserva solicitada no existe o no te pertenece.</div>', 200)
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('misReservasItinerarioFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /** [NIVEL 3] - partials/mis-reservas/huespedes.blade.php */
    public function misReservasHuespedesFragmento(Request $request)
    {
        try {
            [$todas, $activas, $pasadas] = $this->reservasDelCliente();
            $ids = $todas->pluck('id')->all();

            $huespedes = collect();
            if (!empty($ids) && class_exists(ReservaHuesped::class)) {
                try {
                    $huespedes = ReservaHuesped::query()
                        ->with($this->conRelaciones(ReservaHuesped::class, ['reserva.inmueble:id,name', 'registrador:id,name']))
                        ->whereIn($this->colFkReserva(ReservaHuesped::class), $ids)
                        ->orderByDesc('created_at')
                        ->get();
                } catch (Throwable $e) { }
            }

            $reservasOpt = $todas->map(fn ($r) => ['id' => $r->id, 'cod' => $r->codigo_reserva ?? ('#' . $r->id)])->values();
            $vista = $this->primeraVista(['rsv.cliente.partials.mis-reservas.huespedes']);
            if (!$vista) { throw new \RuntimeException('No existe partials/mis-reservas/huespedes.blade.php'); }

            return response()
                ->view($vista, compact('huespedes', 'reservasOpt', 'activas', 'pasadas'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('misReservasHuespedesFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }


    // =========================================================================
    // SECCIÓN 3/5: HISTORIAL
    // =========================================================================

    /** [NIVEL 2] - TAB HISTORIAL */
    public function historialFragmento(Request $request)
    {
        try {
            [$historiales, $endosos] = $this->historialDelCliente();
            $vista = $this->primeraVista([
                'rsv.cliente.partials.historial-tab',
                'rsv.cliente.partials.historial_tab',
                'rsv.cliente.partials.historial.tab',
            ]);
            if (!$vista) { throw new \RuntimeException('No existe partials/historial-tab.blade.php'); }

            return response()
                ->view($vista, compact('historiales', 'endosos'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('historialFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /** [NIVEL 3] - partials/historial/estados.blade.php */
    public function historialEstadosFragmento(Request $request)
    {
        try {
            [$historiales, $endosos] = $this->historialDelCliente();
            $vista = $this->primeraVista(['rsv.cliente.partials.historial.estados']);
            if (!$vista) { throw new \RuntimeException('No existe partials/historial/estados.blade.php'); }

            return response()
                ->view($vista, compact('historiales'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('historialEstadosFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /** [NIVEL 3] - partials/historial/endosos.blade.php */
    public function historialEndososFragmento(Request $request)
    {
        try {
            [$historiales, $endosos] = $this->historialDelCliente();
            $vista = $this->primeraVista(['rsv.cliente.partials.historial.endosos']);
            if (!$vista) { throw new \RuntimeException('No existe partials/historial/endosos.blade.php'); }

            return response()
                ->view($vista, compact('endosos'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('historialEndososFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }


    // =========================================================================
    // SECCIÓN 4/5: CHAT
    // =========================================================================

    /** [NIVEL 2] - TAB CHAT */
    public function chatFragmento(Request $request)
    {
        try {
            $mensajes      = $this->mensajesDelCliente();
            $tiposReceptor = $this->tiposReceptorActivos();
            [$todas]       = $this->reservasDelCliente();
            $misReservas   = $this->reservasComoOpciones($todas);

            $vista = $this->primeraVista([
                'rsv.cliente.partials.chat-tab',
                'rsv.cliente.partials.chat-tab.blade',
                'rsv.cliente.partials.chat_tab',
            ]);
            if (!$vista) { throw new \RuntimeException('No existe partials/chat-tab.blade.php'); }

            return response()
                ->view($vista, compact('mensajes', 'tiposReceptor', 'misReservas'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('chatFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /** [NIVEL 3] - partials/chat/mensajes.blade.php */
    public function chatMensajesFragmento(Request $request)
    {
        try {
            $mensajes      = $this->mensajesDelCliente();
            $tiposReceptor = $this->tiposReceptorActivos();
            $vista = $this->primeraVista(['rsv.cliente.partials.chat.mensajes']);
            if (!$vista) { throw new \RuntimeException('No existe partials/chat/mensajes.blade.php'); }

            return response()
                ->view($vista, compact('mensajes', 'tiposReceptor'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('chatMensajesFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /** [NIVEL 3] - partials/chat/receptores.blade.php */
    public function chatReceptoresFragmento(Request $request)
    {
        try {
            $tiposReceptor = $this->tiposReceptorActivos();
            [$todas]       = $this->reservasDelCliente();
            $misReservas   = $this->reservasComoOpciones($todas);
            $vista = $this->primeraVista(['rsv.cliente.partials.chat.receptores']);
            if (!$vista) { throw new \RuntimeException('No existe partials/chat/receptores.blade.php'); }

            return response()
                ->view($vista, compact('tiposReceptor', 'misReservas'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('chatReceptoresFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }


    // =========================================================================
    // SECCIÓN 5/5: PAGOS
    // =========================================================================

    /** [NIVEL 2] - TAB PAGOS */
    public function pagosFragmento(Request $request)
    {
        try {
            $transacciones = $this->transaccionesDelCliente($request, true);
            $resumen       = $this->resumenTransacciones($this->transaccionesDelCliente($request, false));
            $pasarelas     = $this->pasarelasActivas();

            $vista = $this->primeraVista([
                'rsv.cliente.partials.pagos-tab',
                'rsv.cliente.partials.pagos_tab',
                'rsv.cliente.partials.pagos.tab',
            ]);
            if (!$vista) { throw new \RuntimeException('No existe partials/pagos-tab.blade.php'); }

            return response()
                ->view($vista, compact('transacciones', 'resumen', 'pasarelas'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('pagosFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /** [NIVEL 3] - partials/pagos/transacciones.blade.php */
    public function pagosTransaccionesFragmento(Request $request)
    {
        try {
            $transacciones = $this->transaccionesDelCliente($request, true);
            $resumen       = $this->resumenTransacciones($this->transaccionesDelCliente($request, false));
            $vista = $this->primeraVista(['rsv.cliente.partials.pagos.transacciones']);
            if (!$vista) { throw new \RuntimeException('No existe partials/pagos/transacciones.blade.php'); }

            return response()
                ->view($vista, compact('transacciones', 'resumen'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('pagosTransaccionesFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /** [NIVEL 3] - partials/pagos/pasarelas.blade.php */
    public function pagosPasarelasFragmento(Request $request)
    {
        try {
            $pasarelas = $this->pasarelasActivas();
            $vista = $this->primeraVista(['rsv.cliente.partials.pagos.pasarelas']);
            if (!$vista) { throw new \RuntimeException('No existe partials/pagos/pasarelas.blade.php'); }

            return response()
                ->view($vista, compact('pasarelas'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('pagosPasarelasFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }

    /** [NIVEL 3] - partials/pagos/reviews.blade.php */
    public function pagosReviewsFragmento(Request $request)
    {
        try {
            $reviews = collect();
            if (class_exists(Review::class)) {
                try {
                    $reviews = Review::query()
                        ->with($this->conRelaciones(Review::class, ['reserva', 'inmueble', 'user', 'autor']))
                        ->where($this->colResenaAutor(), Auth::id())
                        ->orderByDesc('created_at')
                        ->take(50)
                        ->get();
                } catch (Throwable $e) { }
            }

            $colRevRes    = class_exists(Review::class) ? $this->colResenaReserva() : 'id_rsv_reservas';
            $idsResenadas = $reviews->pluck($colRevRes)->filter()->all();

            [, $activas, $pasadas] = $this->reservasDelCliente();
            $porResenar = $pasadas->reject(fn ($r) => in_array($r->id, $idsResenadas))->values();

            $vista = $this->primeraVista(['rsv.cliente.partials.pagos.reviews']);
            if (!$vista) { throw new \RuntimeException('No existe partials/pagos/reviews.blade.php'); }

            return response()
                ->view($vista, compact('reviews', 'porResenar'))
                ->header('Cache-Control', 'no-store, max-age=0');
        } catch (Throwable $e) {
            Log::error('pagosReviewsFragmento: ' . $e->getMessage());
            return response('error', 500)->header('Cache-Control', 'no-store, max-age=0');
        }
    }


    // =========================================================================
    // HELPERS PRIVADOS (Lógica Defensiva de Columnas)
    // =========================================================================

    private function colTabla(string $tabla, array $candidatas, ?string $defecto = null): ?string
    {
        $clave = $tabla . '|' . implode(',', $candidatas);
        if (array_key_exists($clave, self::$cacheColumnas)) {
            return self::$cacheColumnas[$clave];
        }
        $elegida = $defecto;
        try {
            foreach ($candidatas as $col) {
                if (Schema::hasColumn($tabla, $col)) { $elegida = $col; break; }
            }
        } catch (Throwable $e) {}
        return self::$cacheColumnas[$clave] = $elegida;
    }

    private function colReservaUser(): string
    {
        try {
            $col = $this->colTabla((new Reserva)->getTable(), ['id_user', 'user_id', 'id_users'], 'id_user');
            return (string) ($col ?? 'id_user');
        } catch (Throwable $e) { return 'id_user'; }
    }

    private function colReservaInmueble(): string
    {
        try {
            $col = $this->colTabla((new Reserva)->getTable(), ['id_rsv_catalogo_inmueble', 'inmueble_id', 'id_inmueble'], 'id_rsv_catalogo_inmueble');
            return (string) ($col ?? 'id_rsv_catalogo_inmueble');
        } catch (Throwable $e) { return 'id_rsv_catalogo_inmueble'; }
    }

    private function colReservaFechaInicio(): string
    {
        try {
            $col = $this->colTabla((new Reserva)->getTable(), ['fecha_inicio', 'fecha_entrada', 'check_in', 'fecha_llegada'], 'fecha_inicio');
            return (string) ($col ?? 'fecha_inicio');
        } catch (Throwable $e) { return 'fecha_inicio'; }
    }

    private function colReservaFechaFin(): string
    {
        try {
            $col = $this->colTabla((new Reserva)->getTable(), ['fecha_fin', 'fecha_salida', 'check_out'], 'fecha_fin');
            return (string) ($col ?? 'fecha_fin');
        } catch (Throwable $e) { return 'fecha_fin'; }
    }

    private function colFkReserva(string $modelo): string
    {
        try {
            $col = $this->colTabla((new $modelo)->getTable(), ['id_rsv_reservas', 'reserva_id', 'id_reserva'], 'id_rsv_reservas');
            return (string) ($col ?? 'id_rsv_reservas');
        } catch (Throwable $e) { return 'id_rsv_reservas'; }
    }

    private function colResenaAutor(): string
    {
        try {
            if (!class_exists(Review::class)) { return 'id_user_autor'; }
            $col = $this->colTabla((new Review)->getTable(), ['id_user_autor', 'user_id', 'id_user', 'autor_id'], 'id_user_autor');
            return (string) ($col ?? 'id_user_autor');
        } catch (Throwable $e) { return 'id_user_autor'; }
    }

    private function colResenaReserva(): string
    {
        return $this->colFkReserva(Review::class);
    }

    private function conRelaciones(string $modelo, array $deseadas): array
    {
        $validas = [];
        foreach ($deseadas as $rel) {
            if (method_exists($modelo, explode('.', $rel)[0])) { $validas[] = $rel; }
        }
        return $validas;
    }

    private function primeraVista(array $candidatas): ?string
    {
        foreach ($candidatas as $vista) {
            if (view()->exists($vista)) { return $vista; }
        }
        return null;
    }

    private function inmueblesActivos()
    {
        $consulta = CatalogoInmueble::query()
            ->with([
                'multimedia' => fn ($q) => $q->orderBy('es_portada', 'desc')->orderBy('orden', 'asc'),
                'latestTarifa',
                'tipoInmueble',
            ]);

        $colActivo = $this->colTabla((new CatalogoInmueble)->getTable(), ['active', 'activo', 'activa', 'publicado', 'visible'], null);
        if ($colActivo) { $consulta->where($colActivo, 1); }

        return $consulta->orderBy('name', 'asc')->get();
    }

    private function destinosDisponibles($inmuebles)
    {
        try {
            $colCity = $this->colTabla((new CatalogoInmueble)->getTable(), ['city', 'ciudad'], null);
            if (!$colCity) { return collect(); }
            return $inmuebles->pluck($colCity)->filter()->unique()->sort()->values();
        } catch (Throwable $e) { return collect(); }
    }

    private function tiposInmuebleActivos()
    {
        if (!class_exists(TipoInmueble::class)) { return collect(); }
        try {
            $colNombre = $this->colTabla((new TipoInmueble)->getTable(), ['nombre', 'name'], 'nombre');
            return TipoInmueble::orderBy((string) $colNombre)->get();
        } catch (Throwable $e) { return collect(); }
    }

    private function tiposReceptorActivos()
    {
        if (!class_exists(TipoReceptor::class)) { return collect(); }
        try {
            $colNombre = $this->colTabla((new TipoReceptor)->getTable(), ['nombre', 'name', 'titulo'], 'nombre');
            return TipoReceptor::orderBy((string) $colNombre)->get();
        } catch (Throwable $e) { return collect(); }
    }

    private function pasarelasActivas()
    {
        try {
            $consulta  = Pasarela::query();
            $colActiva = $this->colTabla((new Pasarela)->getTable(), ['activa', 'activo', 'active', 'habilitada', 'enabled'], null);
            if ($colActiva) { $consulta->where($colActiva, 1); }
            $colNombre = (string) ($this->colTabla((new Pasarela)->getTable(), ['nombre', 'name'], 'nombre') ?? 'nombre');
            try { return $consulta->orderBy($colNombre)->get(); }
            catch (Throwable $e) { return $consulta->get(); }
        } catch (Throwable $e) { return collect(); }
    }

    private function filtrosBusqueda(Request $request): array
    {
        return [
            'ciudad'       => (string) ($request->query('ciudad') ?? $request->query('destino') ?? ''),
            'fecha_inicio' => (string) ($request->query('fecha_inicio') ?? $request->query('check_in') ?? ''),
            'fecha_fin'    => (string) ($request->query('fecha_fin') ?? $request->query('check_out') ?? ''),
            'huespedes'    => max(1, (int) $request->query('huespedes', 1)),
        ];
    }

    private function reservasDelCliente(): array
    {
        $userId = Auth::id();
        $hoy    = Carbon::today()->startOfDay();

        $todas = Reserva::query()
            ->with($this->conRelaciones(Reserva::class, ['inmueble', 'status', 'transacciones', 'huespedes', 'itinerarioEventos']))
            ->where($this->colReservaUser(), $userId)
            ->orderByDesc('created_at')
            ->get();

        $activas = $todas->filter(fn ($r) => $this->reservaActiva($r, $hoy))->values();
        $pasadas = $todas->reject(fn ($r) => $this->reservaActiva($r, $hoy))->values();

        return [$todas, $activas, $pasadas];
    }

    private function idsReservasDelCliente(): array
    {
        try {
            return Reserva::query()->where($this->colReservaUser(), Auth::id())->pluck('id')->all();
        } catch (Throwable $e) { return []; }
    }

    private function historialDelCliente(): array
    {
        $ids = $this->idsReservasDelCliente();

        $historiales = collect();
        if (!empty($ids) && class_exists(HistorialEstado::class)) {
            try {
                $historiales = HistorialEstado::query()
                    ->with($this->conRelaciones(HistorialEstado::class, ['reserva.inmueble:id,name', 'statusNuevo', 'statusAnterior', 'status']))
                    ->whereIn($this->colFkReserva(HistorialEstado::class), $ids)
                    ->orderByDesc('created_at')
                    ->take(100)->get();
            } catch (Throwable $e) {}
        }

        $endosos = collect();
        if (!empty($ids) && class_exists(HistorialEndoso::class)) {
            try {
                $endosos = HistorialEndoso::query()
                    ->with($this->conRelaciones(HistorialEndoso::class, ['reserva', 'usuarioAutor', 'user', 'autor']))
                    ->whereIn($this->colFkReserva(HistorialEndoso::class), $ids)
                    ->orderByDesc('created_at')
                    ->take(100)->get();
            } catch (Throwable $e) {}
        }

        return [$historiales, $endosos];
    }

    private function mensajesDelCliente()
    {
        $ids = $this->idsReservasDelCliente();
        $mensajes = collect();
        if (!empty($ids) && class_exists(Mensaje::class)) {
            try {
                $mensajes = Mensaje::query()
                    ->with($this->conRelaciones(Mensaje::class, ['reserva.inmueble:id,name', 'receptor', 'tipoReceptor', 'user', 'emisor', 'autor']))
                    ->whereIn($this->colFkReserva(Mensaje::class), $ids)
                    ->orderByDesc('created_at')
                    ->take(200)->get()->reverse()->values();
            } catch (Throwable $e) {}
        }
        return $mensajes;
    }

    private function transaccionesDelCliente(Request $request, bool $paginada = false)
    {
        $pagina  = max(1, (int) $request->query('page_pagos', 1));
        $colUser = $this->colReservaUser();

        $consulta = TransaccionFinanciera::query()
            ->with($this->conRelaciones(TransaccionFinanciera::class, ['reserva.inmueble:id,name', 'pasarela']))
            ->orderByDesc('created_at');

        if (method_exists(TransaccionFinanciera::class, 'reserva')) {
            $consulta->whereHas('reserva', fn ($q) => $q->where($colUser, Auth::id()));
        } else {
            $ids = $this->idsReservasDelCliente();
            $consulta->whereIn($this->colFkReserva(TransaccionFinanciera::class), $ids);
        }

        if ($paginada) {
            return $consulta->paginate(10, ['*'], 'page_pagos', $pagina)->appends($request->query());
        }
        return $consulta->limit(100)->get();
    }

    private function transaccionesDeReserva($reserva)
    {
        try {
            if (method_exists($reserva, 'transacciones')) {
                return $reserva->relationLoaded('transacciones') ? $reserva->transacciones : $reserva->transacciones()->get();
            }
        } catch (Throwable $e) {}
        return collect();
    }

    private function reservaActiva($reserva, $hoy): bool
    {
        [$fi, $ff] = $this->fechasDeReserva($reserva);
        return $fi && $ff && ($hoy->betweenIncluded($fi, $ff) || $fi->gte($hoy));
    }

    private function fechasDeReserva($reserva): array
    {
        $fi = null; $ff = null;
        try {
            $crudaFi = $reserva->fecha_inicio ?? $reserva->fecha_entrada ?? $reserva->check_in ?? $reserva->fecha_llegada ?? null;
            $crudaFf = $reserva->fecha_fin   ?? $reserva->fecha_salida  ?? $reserva->check_out ?? null;
            if ($crudaFi) { $fi = Carbon::parse($crudaFi)->startOfDay(); }
            if ($crudaFf) { $ff = Carbon::parse($crudaFf)->startOfDay(); }
        } catch (Throwable $e) {}
        return [$fi, $ff];
    }

    private function montoDeReserva($reserva): float
    {
        foreach (['monto_total', 'precio_total', 'total', 'valor_total', 'precio'] as $colTry) {
            if (isset($reserva->{$colTry}) && is_numeric($reserva->{$colTry})) { return (float) $reserva->{$colTry}; }
        }
        return 0.0;
    }

    private function montoDeTransaccion($tx): float
    {
        foreach (['monto', 'importe', 'valor', 'amount', 'monto_pagado', 'monto_transaccion'] as $colTry) {
            if (isset($tx->{$colTry}) && is_numeric($tx->{$colTry})) { return (float) $tx->{$colTry}; }
        }
        return 0.0;
    }

    private function estadoDeTransaccion($tx): string
    {
        foreach (['estado_pago', 'estado', 'status_pago', 'estado_transaccion', 'status'] as $colTry) {
            if (isset($tx->{$colTry}) && (is_string($tx->{$colTry}) || is_numeric($tx->{$colTry}))) { return (string) $tx->{$colTry}; }
        }
        return '';
    }

    private function resumenTransacciones($transacciones): array
    {
        $resumen = ['pagado' => 0.0, 'pendiente' => 0.0, 'anulado' => 0.0];
        try {
            foreach ($transacciones as $tx) {
                $edo   = mb_strtolower($this->estadoDeTransaccion($tx));
                $monto = $this->montoDeTransaccion($tx);
                if (str_contains($edo, 'anulad') || str_contains($edo, 'cancel') || str_contains($edo, 'fallid') || str_contains($edo, 'rechaz')) {
                    $resumen['anulado'] += $monto;
                } elseif (str_contains($edo, 'complet') || str_contains($edo, 'aprob') || str_contains($edo, 'pagad')) {
                    $resumen['pagado'] += $monto;
                } else {
                    $resumen['pendiente'] += $monto;
                }
            }
        } catch (Throwable $e) {}
        return $resumen;
    }

    private function reservasComoOpciones($reservas)
    {
        return $reservas->map(fn ($r) => [
            'id'     => $r->id,
            'codigo' => $r->codigo_reserva ?? ('#' . $r->id),
            'texto'  => ($r->codigo_reserva ?? ('#' . $r->id)) . ' · ' . ($r->relationLoaded('inmueble') ? (optional($r->inmueble)->name ?? '') : ''),
        ])->values();
    }
}
