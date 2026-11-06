<?php

/**
 * ============================================================================
 *  ReservaController · App\Http\Controllers\Rsv · v31
 * ============================================================================
 *
 *  Controller del módulo de Reservas (RSV).
 *
 *  Métodos públicos:
 *  ────────────────────────────────────────────────────────────────────────
 *   · index()             → Listado con filtros (Web/AJAX/JSON)
 *   · create()            → 405 (no implementado para API REST)
 *   · store()             → Endpoint RESTful estándar (StoreReservaRequest)
 *   · storeAjax()         → Creación desde el modal de tab-reservas (AJAX)
 *   · show()              → Detalle con carga profunda de relaciones
 *   · edit()              → 405 (no implementado)
 *   · update()            → Actualización con detección de cambio de estado
 *   · destroy()           → Soft delete (Eliminación visual, preserva BD)
 *   · cambiarEstado()     → Cambio de estado justificado + historial
 *   · fechasOcupadasAjax() → Disponibilidad por inmueble (para mini-calendario)
 *
 *  Helpers privados (motor de reglas):
 *  ────────────────────────────────────────────────────────────────────────
 *   · getEstadosNoBloqueantesIds()  → IDs de estados Cancelada/Rechazada/Anulada.
 *   · getTarifaAplicable()        🆕 v29 → Tarifa ACTIVA del inmueble que rige
 *                                   el rango (si cubre todo el rango se usa
 *                                   esa; si el rango cruza temporadas, rige
 *                                   la tarifa activa del CHECK-IN).
 *   · getLimiteAnualDesdeTarifa() 🆕 v29 → Límite anual leído del MODELO
 *                                   (max_reservas_anuales_cliente).
 *                                   Sin tarifa / null / 0 → 1 (legacy v28).
 *   · validarLimiteAnualCliente() 🆕 v29 → wrapper del motor: devuelve solo
 *                                   el mensaje (o null). El motor único es
 *                                   evaluarLimiteAnual() desde v30-EXC.
 *   · evaluarLimiteAnual()        🆕 v30 → motor ÚNICO de la regla del año:
 *                                   mensaje + contexto completo para auditar.
 *   · resolverExcepcionLimite()   🆕 v31 → decide el salto del bloque con
 *                                   motivo obligatorio (revalidación
 *                                   server-side). SIN validar usuario ni
 *                                   rol: quien corre el controlador YA ES
 *                                   admin (lo garantiza la ruta).
 *   · textoAuditoriaExcepcion()   🆕 v31 → texto de auditoría completo
 *                                   (por qué + contexto del límite saltado)
 *                                   guardado en el HistorialEstado, en la
 *                                   MISMA transacción (sin tabla extra).
 *   · respuesta422Excepcion()     🆕 v30 → 422 uniforme con datos de la
 *                                   excepción para el front.
 *
 *  Reglas de negocio críticas (NO REMOVER):
 *  ────────────────────────────────────────────────────────────────────────
 *   1. storeAjax() SIEMPRE asigna estado "Pendiente" (ignora el del form).
 *   2. 🆕 v29 · LÍMITE ANUAL DE RESERVAS POR ASOCIADO — YA NO ESTÁ QUEMADO:
 *      · Ventana ROLLING de ±1 año alrededor del check-in nuevo (se mantiene
 *        la lógica v28/v28.1: whereBetween + exclusión de Cancelada/
 *        Rechazada/Anulada). El check-in (fecha_inicio) define la ventana y
 *        se cuentan reservas ACTIVAS de CUALQUIER inmueble (como en v28).
 *      · El MÁXIMO permitido se lee del modelo TarifaTemporada:
 *        max_reservas_anuales_cliente de la TARIFA ACTIVA del inmueble que
 *        cubre el check-in. Si no hay tarifa activa aplicable o el campo es
 *        null / 0 / vacío → límite 1 (retrocompatible con v28).
 *      · Se pasa de exists() a COUNT(): el límite ahora puede ser 1, 2, 3...
 *      · La regla vive SOLO en validarLimiteAnualCliente() (antes estaba
 *        duplicada en store(), update() y storeAjax() con mensajes distintos).
 *   2b. 🆕 v29 · SOLO TARIFAS ACTIVAS (active = true) APLICAN:
 *      · Una tarifa DESACTIVADA deja de gobernar monto mínimo, días máximos
 *        y límite anual de las reservas nuevas.
 *      · fechasOcupadasAjax() deja de entregar tarifas inactivas al
 *        mini-calendario.
 *      · Semántica del catálogo: un inmueble puede tener muchas temporadas
 *        al año pero solo UNA activa por período; pueden coexistir dos
 *        activas de distinto año (la fecha del check-in decide cuál rige).
 *   3. storeAjax() valida SOLAPAMIENTO de fechas contra reservas existentes
 *      del mismo inmueble + bloqueos de calendario + día de alistamiento.
 *   4. storeAjax() valida monto mínimo según tarifa aplicable
 *      (precio_minimo_reserva) o 200.000 si no hay tarifa.
 *   5. storeAjax() valida noches máximas según tarifa aplicable (dias_maximos).
 *   6. Toda creación/actualización/cambio de estado registra HistorialEstado.
 *      🆕 v29 · storeAjax() deja en el historial el límite anual aplicado.
 *   7. La creación/actualización va envuelta en DB::transaction para rollback.
 *   8. fechasOcupadasAjax() incluye datos del TITULAR.
 *   9. 🆕 v29 · update() valida límite anual y solapamiento aunque el form
 *      solo envíe UNA de las dos fechas (v28 se saltaba TODAS las
 *      validaciones si faltaba una; la faltante se toma de la reserva actual).
 *  10. 🆕 v30-EXC · EXCEPCIONES A POLÍTICAS CON AUDITORÍA OBLIGATORIA
 *      (🆕 v31 · SIN validación de rol y SIN tabla dedicada):
 *      · El bloque del LÍMITE ANUAL (y SOLO ese) puede saltarse con
 *        justificación: la alerta 422 sale igual; si la petición envía
 *        motivo_excepcion (obligatorio, 10-1000 caracteres) la operación
 *        continúa y se guarda TODA la auditoría.
 *      · 🆕 v31 · Quien ejecuta este controlador YA ES admin (lo garantiza
 *        la ruta/middleware): NO se valida usuario, rol ni permiso. La
 *        única condición server-side es el motivo válido (revalidado).
 *      · Auditoría ATÓMICA: el texto de auditoría (justificación + quién +
 *        contexto del límite saltado) va en el comentario del
 *        HistorialEstado, DENTRO de la misma transacción: si falla se
 *        revierte TODO (sin auditoría no hay excepción).
 *      · NO son excepcionables: solapamiento, bloqueos de calendario,
 *        monto mínimo y días máximos (reglas físicas/contrato).
 *      · Trazabilidad: rsv_historial_estados ya guarda quién (id_user),
 *        cuándo (created_at) y por qué (comentario) — no hace falta una
 *        tabla rsv_excepciones_politicas (retirada en v31).
 *
 *  🆕 v31 · SIN TABLA DEDICADA DE AUDITORÍA (rsv_excepciones_politicas
 *  retirada a petición del admin):
 *  ────────────────────────────────────────────────────────────────────────
 *  La auditoría de la excepción vive en rsv_historial_estados (ya existe):
 *  el comentario del historial guarda el código de política, la
 *  justificación OBLIGATORIA y el contexto del límite saltado (conteo,
 *  límite, ventana, tarifa, fechas). Quién y cuándo ya los guarda la
 *  propia tabla (id_user, created_at). Cero migraciones nuevas.
 *
 *  CONTRATO PARA EL FRONT (modal de tab-reservas):
 *  ────────────────────────────────────────────────────────────────────────
 *  · 422 con límite alcanzado: { ok|success: false, message: <alerta v29>,
 *      excepcion: { disponible: true, requiere_motivo: true,
 *      error_motivo: null|"...", campo: "motivo_excepcion",
 *      min_caracteres: 10, max_caracteres: 1000,
 *      codigo_politica: "LIMITE_ANUAL_ASOCIADO" } }
 *    (🆕 v31 · el bloque "excepcion" va SIEMPRE: no hay validación de rol.)
 *  · Reintento: mismo form + campo "motivo_excepcion" (textarea).
 *  · Éxito con excepción: la respuesta de éxito NO cambia; la auditoría
 *    completa queda en el HistorialEstado de la reserva.
 *  ============================================================================
 */

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rsv\StoreReservaRequest;
use App\Http\Requests\Rsv\UpdateReservaRequest;
use App\Models\Rsv\CatalogoInmueble;
use App\Models\Rsv\HistorialEstado;
use App\Models\Rsv\OrigenReserva;
use App\Models\Rsv\Reserva;
use App\Models\Rsv\Status;
use App\Models\Rsv\TarifaTemporada;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class ReservaController extends Controller
{
    private const MONTO_MIN_BASE = 200000;

    /* ═══ 🆕 v31-EXC · EXCEPCIONES A POLÍTICAS · SIN VALIDAR ROL, SIN TABLA ═══
       Quien ejecuta este controlador YA ES admin (lo garantiza la ruta):
       NO se valida usuario, rol ni permiso. La alerta 422 sale igual, pero
       si la petición envía motivo_excepcion (10-1000 caracteres), la
       operación continúa y TODA la auditoría se guarda en el comentario del
       HistorialEstado DENTRO de la misma transacción (si el historial
       falla, la reserva NO se guarda). Sin tabla dedicada. */
    private const CODIGO_POLITICA_LIMITE_ANUAL = 'LIMITE_ANUAL_ASOCIADO';
    private const MOTIVO_EXC_MIN               = 10;
    private const MOTIVO_EXC_MAX               = 1000;

    private function getEstadosNoBloqueantesIds(): array
    {
        try {
            if (!class_exists(Status::class)) {
                return [];
            }
            return Status::where(function ($q) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%cancela%'])
                  ->orWhereRaw('LOWER(name) LIKE ?', ['%rechaz%'])
                  ->orWhereRaw('LOWER(name) LIKE ?', ['%anulad%']);
            })->pluck('id')->toArray();
        } catch (\Throwable $e) {
            Log::warning('getEstadosNoBloqueantesIds: ' . $e->getMessage());
            return [];
        }
    }

    /* ═══════════════════════════════════════════════════════════════════════
     * 🆕 v29 · MOTOR DE TARIFA APLICABLE Y LÍMITE ANUAL (antes quemado en v28)
     * ═══════════════════════════════════════════════════════════════════════ */

    /**
     * 🆕 v29 · Tarifa ACTIVA del inmueble que rige el rango solicitado.
     *
     * 1) Preferida: tarifa activa que cubre TODO el rango de la estadía.
     * 2) Fallback: si el rango cruza temporadas (o ninguna cubre todo), rige
     *    la tarifa ACTIVA que cubre el CHECK-IN (fecha_inicio).
     *
     * Nunca devuelve tarifas inactivas: active = false queda fuera siempre.
     */
    private function getTarifaAplicable($idInmueble, \Illuminate\Support\Carbon $fi, \Illuminate\Support\Carbon $ff): ?TarifaTemporada
    {
        try {
            if (!class_exists(TarifaTemporada::class)) {
                return null;
            }

            // Preferida: cubre TODO el rango
            $tarifa = TarifaTemporada::where('id_rsv_catalogo_inmueble', $idInmueble)
                ->where('active', true)
                ->whereDate('fecha_inicio', '<=', $fi->toDateString())
                ->whereDate('fecha_fin', '>=', $ff->toDateString())
                ->orderBy('fecha_inicio', 'desc')
                ->first();

            // Fallback v29: rango que cruza temporadas → rige la del CHECK-IN
            if (!$tarifa) {
                $tarifa = TarifaTemporada::where('id_rsv_catalogo_inmueble', $idInmueble)
                    ->where('active', true)
                    ->whereDate('fecha_inicio', '<=', $fi->toDateString())
                    ->whereDate('fecha_fin', '>=', $fi->toDateString())
                    ->orderBy('fecha_inicio', 'desc')
                    ->first();
            }

            return $tarifa;
        } catch (Throwable $e) {
            Log::warning('getTarifaAplicable: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 🆕 v29 · Límite anual por asociado leído del MODELO (ya no está quemado).
     *
     * max_reservas_anuales_cliente de la tarifa aplicable.
     * Sin tarifa / null / 0 / vacío → 1 (comportamiento legacy de v28).
     */
    private function getLimiteAnualDesdeTarifa(?TarifaTemporada $tarifa): int
    {
        if (!$tarifa) {
            return 1;
        }
        $valor = (int) ($tarifa->max_reservas_anuales_cliente ?? 0);
        return $valor > 0 ? $valor : 1;
    }

    /**
     * 🆕 v30-EXC · MOTOR ÚNICO de la regla del año (store/update/storeAjax).
     * Comportamiento EXACTO al validador v29 (no cambia la regla):
     * ventana ROLLING ±1 año alrededor del check-in (whereBetween inclusiva),
     * COUNT() de reservas ACTIVAS de cualquier inmueble excluyendo
     * Cancelada/Rechazada/Anulada y, en update(), la propia reserva; máximo
     * leído de la tarifa ACTIVA (max_reservas_anuales_cliente; null/0 → 1).
     *
     * Devuelve UN array con TODO lo necesario para validar Y auditar:
     *   mensaje (null si cumple) · conteo · limite · ventana_desde/hasta
     *   tarifa_id · tarifa_nombre · limite_origen
     */
    private function evaluarLimiteAnual($idUsuario, \Illuminate\Support\Carbon $fi, ?int $excluirReservaId = null, ?TarifaTemporada $tarifa = null): array
    {
        $limite = $this->getLimiteAnualDesdeTarifa($tarifa);

        $limiteInferior = $fi->copy()->subYear()->format('Y-m-d');
        $limiteSuperior = $fi->copy()->addYear()->format('Y-m-d');

        $estadosNoBloqueantes = $this->getEstadosNoBloqueantesIds();

        $query = Reserva::where('id_user', $idUsuario)
            ->whereBetween('fecha_inicio', [$limiteInferior, $limiteSuperior]);

        if (!empty($estadosNoBloqueantes)) {
            $query->whereNotIn('id_rsv_statuses', $estadosNoBloqueantes);
        }
        if (!empty($excluirReservaId)) {
            $query->where('id', '!=', $excluirReservaId);
        }

        $conteo = (int) $query->count();

        // 🆕 v30-EXC · origen del límite, tal cual lo informa el mensaje v29
        $limiteOrigen = $tarifa
            ? 'Tarifa "' . $tarifa->nombre_temporada . '" (max_reservas_anuales_cliente)'
            : 'Sistema: 1 reserva/año (sin tarifa activa aplicable)';

        $base = [
            'conteo'        => $conteo,
            'limite'        => $limite,
            'ventana_desde' => $limiteInferior,
            'ventana_hasta' => $limiteSuperior,
            'tarifa_id'     => $tarifa->id ?? null,
            'tarifa_nombre' => $tarifa->nombre_temporada ?? null,
            'limite_origen' => $limiteOrigen,
        ];

        if ($conteo < $limite) {
            $base['mensaje'] = null;
            return $base;
        }

        $origenLimite = $tarifa
            ? 'La temporada "' . $tarifa->nombre_temporada . '" permite un máximo de ' . $limite . ' reserva(s) por año por asociado.'
            : 'Se aplica el límite del sistema: 1 reserva por año (sin tarifa activa aplicable a la fecha del check-in).';

        $base['mensaje'] = 'El asociado ya tiene ' . $conteo . ' reserva(s) ACTIVA(S) en menos de un año de diferencia '
            . '(ventana ' . $limiteInferior . ' → ' . $limiteSuperior . '; se cuentan reservas de cualquier inmueble y el check-in define la ventana). '
            . $origenLimite . ' '
            . 'Si canceló una reserva, verifique que su estado sea "Cancelada", "Rechazada" o "Anulada".';
        return $base;
    }

    /**
     * 🆕 v29 · Wrapper de compatibilidad: devuelve SOLO el mensaje (o null).
     * Desde v30-EXC el motor real es evaluarLimiteAnual(); la regla y el
     * mensaje son IDÉNTICOS (una sola implementación, cero duplicación).
     */
    private function validarLimiteAnualCliente($idUsuario, \Illuminate\Support\Carbon $fi, ?int $excluirReservaId = null, ?TarifaTemporada $tarifa = null): ?string
    {
        return $this->evaluarLimiteAnual($idUsuario, $fi, $excluirReservaId, $tarifa)['mensaje'];
    }

    /* ═══════════════ 🆕 v31-EXC · EXCEPCIONES A POLÍTICAS CON AUDITORÍA ═══════════════ */
    /* 🆕 v31 · esAdminRsv() ELIMINADO: ya NO se valida usuario, rol ni
       permiso — quien corre este controlador YA ES admin por la ruta. */

    /**
     * 🆕 v31-EXC · Decide si el bloque del límite anual se salta o no.
     *
     * Revalidación SERVER-SIDE del motivo: la excepción SOLO se concede con
     * un motivo_excepcion enviado y válido (10-1000 caracteres).
     * 🆕 v31 · NO se valida usuario, rol ni permiso: quien ejecuta este
     * controlador YA ES admin (lo garantiza la ruta).
     *
     * mensaje null → no hubo bloque: proceder sin excepción.
     * Devuelve:
     *   proceder         bool → continuar la operación
     *   motivo           ?str → justificación validada (para la auditoría)
     *   requiere_motivo  bool → la alerta salió y se espera la justificación
     *   error_motivo     ?str → motivo enviado pero inválido (corto/largo)
     */
    private function resolverExcepcionLimite(Request $request, ?string $mensaje): array
    {
        if ($mensaje === null) {
            return ['proceder' => true, 'motivo' => null, 'requiere_motivo' => false, 'error_motivo' => null];
        }

        $motivo = trim((string) $request->input('motivo_excepcion', ''));
        $largo  = mb_strlen($motivo);

        if ($motivo === '') {
            return ['proceder' => false, 'motivo' => null, 'requiere_motivo' => true, 'error_motivo' => null];
        }

        if ($largo < self::MOTIVO_EXC_MIN || $largo > self::MOTIVO_EXC_MAX) {
            return ['proceder' => false, 'motivo' => null, 'requiere_motivo' => true,
                'error_motivo' => 'La justificación debe tener entre ' . self::MOTIVO_EXC_MIN . ' y ' . self::MOTIVO_EXC_MAX . ' caracteres (enviada: ' . $largo . ').'];
        }

        return ['proceder' => true, 'motivo' => $motivo, 'requiere_motivo' => false, 'error_motivo' => null];
    }

    /**
     * 🆕 v30-EXC · 422 uniforme con datos de la excepción para el front.
     * $campoEstado = 'ok' (storeAjax) | 'success' (store/update) para
     * respetar el formato de respuesta de cada endpoint.
     * 🆕 v31 · El bloque "excepcion" va SIEMPRE (no se valida rol): es la
     * señal para que el modal pida la justificación obligatoria.
     */
    private function respuesta422Excepcion(string $campoEstado, string $mensaje, array $excepcion): JsonResponse
    {
        $payload = [
            $campoEstado => false,
            'message'    => $mensaje,
        ];
        $payload['excepcion'] = [
            'disponible'      => true,
            'requiere_motivo' => true,
            'error_motivo'    => $excepcion['error_motivo'] ?? null,
            'campo'           => 'motivo_excepcion',
            'min_caracteres'  => self::MOTIVO_EXC_MIN,
            'max_caracteres'  => self::MOTIVO_EXC_MAX,
            'codigo_politica' => self::CODIGO_POLITICA_LIMITE_ANUAL,
        ];
        return response()->json($payload, 422);
    }

    /**
     * 🆕 v31-EXC · Texto de AUDITORÍA de la excepción (SIN tabla dedicada).
     * Se incrusta en el comentario del HistorialEstado, que ya guarda QUIÉN
     * (id_user) y CUÁNDO (created_at); aquí va el POR QUÉ (justificación
     * obligatoria) y el contexto exacto del límite que se saltó.
     * Se construye ANTES de la transacción y se guarda DENTRO de ella: si el
     * historial falla, TODO se revierte (sin auditoría no hay excepción).
     */
    private function textoAuditoriaExcepcion(
        array $evaluacion,
        string $metodo,
        $idReserva,
        $idAsociado,
        string $motivo,
        ?string $checkIn = null,
        ?string $checkOut = null,
        $idInmueble = null
    ): string {
        $contexto = 'metodo=' . $metodo
            . ' · reserva=' . ($idReserva !== null ? (int) $idReserva : 'nueva')
            . ' · asociado=' . (int) $idAsociado
            . ' · inmueble=' . ($idInmueble !== null ? (int) $idInmueble : 'n/d')
            . ' · check-in=' . ($checkIn ?? 'n/d')
            . ' · check-out=' . ($checkOut ?? 'n/d')
            . ' · conteo_activo=' . (int) ($evaluacion['conteo'] ?? 0)
            . ' · limite=' . (int) ($evaluacion['limite'] ?? 0)
            . ' · ventana=' . ($evaluacion['ventana_desde'] ?? '') . ' → ' . ($evaluacion['ventana_hasta'] ?? '')
            . ' · origen=' . (string) ($evaluacion['limite_origen'] ?? '');

        return '⚠ EXCEPCIÓN ' . self::CODIGO_POLITICA_LIMITE_ANUAL
            . ' · justificación: "' . mb_substr($motivo, 0, self::MOTIVO_EXC_MAX) . '"'
            . ' · [' . $contexto . ']';
    }


    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = Reserva::with([
                'inmueble',
                'user',
                'status',
                'origen',
                'huespedes',
                'transacciones',
                'historialEndosos',
            ]);

            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('codigo_reserva', 'like', "%{$search}%")
                      ->orWhere('comentario_reserva', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($u) use ($search) {
                          $u->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                      })
                      ->orWhereHas('inmueble', function ($i) use ($search) {
                          $i->where('name', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('id_rsv_statuses')) {
                $query->where('id_rsv_statuses', $request->id_rsv_statuses);
            }
            if ($request->filled('id_rsv_catalogo_inmueble')) {
                $query->where('id_rsv_catalogo_inmueble', $request->id_rsv_catalogo_inmueble);
            }
            if ($request->filled('fecha_desde')) {
                $query->whereDate('fecha_inicio', '>=', $request->fecha_desde);
            }
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('fecha_fin', '<=', $request->fecha_hasta);
            }

            $perPage = $request->get('per_page', 15);
            $reservas = $query->orderBy('created_at', 'desc')->paginate($perPage);

            if ($request->wantsJson() && !$request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data'    => $reservas->items(),
                ], 200);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html'    => view('rsv.reservas.partials.tabla-registros', compact('reservas'))->render(),
                ], 200);
            }

            return view('rsv.reservas.index', compact('reservas'));

        } catch (Throwable $e) {
            Log::error('Error en ReservaController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Ocurrió un error al cargar las reservas.');
        }
    }

    public function create(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Método no implementado para API REST.',
        ], 405);
    }

    public function store(StoreReservaRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $fi = \Illuminate\Support\Carbon::parse($validatedData['fecha_inicio'])->startOfDay();
            $ff = \Illuminate\Support\Carbon::parse($validatedData['fecha_fin'])->startOfDay();

            // ─── 🆕 v31-EXC · LÍMITE ANUAL + EXCEPCIÓN CON AUDITORÍA ───
            // Regla unificada en evaluarLimiteAnual() (motor v30 del validador
            // v29): ventana rolling ±1 año + máximo de la tarifa ACTIVA
            // (max_reservas_anuales_cliente; null/0 → 1).
            // Si excede: la alerta sale igual; con motivo_excepcion
            // (obligatorio), continúa Y SE AUDITA (sin validar rol: quien
            // ejecuta ya es admin por la ruta).
            $tarifaLimite = $this->getTarifaAplicable($validatedData['id_rsv_catalogo_inmueble'], $fi, $ff);
            $evalLimite = $this->evaluarLimiteAnual((int) $validatedData['id_user'], $fi, null, $tarifaLimite);

            $excepcionLimite = $this->resolverExcepcionLimite($request, $evalLimite['mensaje']);

            if (!$excepcionLimite['proceder']) {
                return $this->respuesta422Excepcion('success', $evalLimite['mensaje'], $excepcionLimite);
            }

            $motivoExcepcion = $excepcionLimite['motivo']; // null si no hubo bloque

            $estadosNoBloqueantes = $this->getEstadosNoBloqueantesIds();

            // ─── Validar solapamiento de fechas (defensa en profundidad) ───
            $solapada = Reserva::where('id_rsv_catalogo_inmueble', $validatedData['id_rsv_catalogo_inmueble'])
                ->whereNotNull('fecha_inicio')
                ->whereNotNull('fecha_fin')
                ->where('fecha_inicio', '<', $ff)
                ->whereRaw('DATE_ADD(fecha_fin, INTERVAL 1 DAY) > ?', [$fi])
                ->when(!empty($estadosNoBloqueantes), function ($q) use ($estadosNoBloqueantes) {
                    $q->whereNotIn('id_rsv_statuses', $estadosNoBloqueantes);
                })
                ->exists();

            if ($solapada) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe una reserva para este inmueble que se solapa con el rango '
                        . $fi->format('d/m/Y') . ' - ' . $ff->format('d/m/Y')
                        . '. Elija otro rango de fechas.',
                ], 422);
            }

            // ─── Validar bloqueos de calendario ───
            if (class_exists(\App\Models\Rsv\BloqueoCalendario::class)) {
                $bloqueado = \App\Models\Rsv\BloqueoCalendario::where('id_rsv_catalogo_inmueble', $validatedData['id_rsv_catalogo_inmueble'])
                    ->whereNotNull('fecha_inicio')
                    ->whereNotNull('fecha_fin')
                    ->where('fecha_inicio', '<', $ff)
                    ->where('fecha_fin', '>', $fi)
                    ->exists();

                if ($bloqueado) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El rango seleccionado está bloqueado en el calendario del inmueble. Elija otro rango.',
                    ], 422);
                }
            }

            /* 🆕 v31-EXC · texto de auditoría (justificación + contexto del
               límite saltado); se incrusta en el historial DENTRO de la
               transacción: si el historial falla, no se guarda nada */
            $textoAuditoria = $motivoExcepcion !== null
                ? $this->textoAuditoriaExcepcion(
                    $evalLimite, 'store', null, (int) $validatedData['id_user'],
                    $motivoExcepcion,
                    $validatedData['fecha_inicio'] ?? null, $validatedData['fecha_fin'] ?? null,
                    $validatedData['id_rsv_catalogo_inmueble'] ?? null)
                : '';

            $reserva = DB::transaction(function () use ($validatedData, $textoAuditoria) {
                $validatedData['codigo_reserva'] = 'RSV-' . strtoupper(uniqid());
                $reserva = Reserva::create($validatedData);

                HistorialEstado::create([
                    'id_rsv_reservas'          => $reserva->id,
                    'id_rsv_statuses_anterior' => null,
                    'id_rsv_statuses_nuevo'    => $reserva->id_rsv_statuses,
                    'id_user'                  => Auth::id() ?? $reserva->id_user,
                    /* 🆕 v31-EXC · el historial ES la auditoría (quién/cuándo/
                       por qué/contexto) · sin auditoría no hay excepción */
                    'comentario'               => 'Creación inicial de la reserva.'
                        . ($textoAuditoria !== '' ? ' · ' . $textoAuditoria : ''),
                ]);
                return $reserva;
            });

            $reserva->load(['inmueble', 'user', 'status', 'origen']);

            return response()->json([
                'success' => true,
                'message' => 'Reserva creada exitosamente.',
                'data'    => $reserva,
            ], 201);

        } catch (Throwable $e) {
            Log::error('Error en ReservaController@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar la reserva.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $reserva = Reserva::with([
                'inmueble', 'user', 'status', 'origen', 'huespedes',
                'transacciones.pasarela', 'itinerarios', 'mensajes',
                'reviews', 'historialEstados', 'historialEndosos',
            ])->find($id);

            if (!$reserva) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reserva solicitada no existe.',
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Reserva obtenida exitosamente.',
                'data'    => $reserva,
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener la reserva.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function edit(string $id): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Método no implementado para API REST.',
        ], 405);
    }

    public function update(UpdateReservaRequest $request, string $id): JsonResponse
    {
        try {
            $reserva = Reserva::find($id);

            if (!$reserva) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reserva solicitada no existe.',
                ], 404);
            }

            $validatedData = $request->validated();

            // ─── 🆕 v29 · FECHAS EFECTIVAS ───
            // Si el form solo envía una fecha, la otra se toma de la reserva
            // actual (v28 se saltaba TODAS las validaciones en ese caso).
            $efFi = isset($validatedData['fecha_inicio'])
                ? \Illuminate\Support\Carbon::parse($validatedData['fecha_inicio'])->startOfDay()
                : ($reserva->fecha_inicio ? \Illuminate\Support\Carbon::parse($reserva->fecha_inicio)->startOfDay() : null);
            $efFf = isset($validatedData['fecha_fin'])
                ? \Illuminate\Support\Carbon::parse($validatedData['fecha_fin'])->startOfDay()
                : ($reserva->fecha_fin ? \Illuminate\Support\Carbon::parse($reserva->fecha_fin)->startOfDay() : null);

            $cambiaFechas  = isset($validatedData['fecha_inicio']) || isset($validatedData['fecha_fin']);
            $cambiaUsuario = array_key_exists('id_user', $validatedData)
                && (int) $validatedData['id_user'] !== (int) $reserva->id_user;

            // 🆕 v31 · inicialización fuera del if (evita variables no definidas
            // cuando el form no cambia fechas ni titular)
            $motivoExcepcion = null;
            $evalLimite = null;
            $userIdLimite = null;

            if ($cambiaFechas || $cambiaUsuario) {

                // ─── 🆕 v31-EXC · LÍMITE ANUAL + EXCEPCIÓN CON AUDITORÍA ───
                // Motor evaluarLimiteAnual() (se excluye la propia reserva,
                // como en v29). Si excede: alerta igual; motivo obligatorio
                // → continúa Y SE AUDITA en la transacción (sin validar rol).
                if ($efFi) {
                    $userIdLimite = $validatedData['id_user'] ?? $reserva->id_user;
                    $tarifaLimite = $this->getTarifaAplicable($reserva->id_rsv_catalogo_inmueble, $efFi, $efFf ?? $efFi);
                    $evalLimite = $this->evaluarLimiteAnual((int) $userIdLimite, $efFi, (int) $reserva->id, $tarifaLimite);

                    $excepcionLimite = $this->resolverExcepcionLimite($request, $evalLimite['mensaje']);

                    if (!$excepcionLimite['proceder']) {
                        return $this->respuesta422Excepcion('success', $evalLimite['mensaje'], $excepcionLimite);
                    }

                    $motivoExcepcion = $excepcionLimite['motivo'];
                }

                // ─── Validación de Solapamiento (v29: también si solo cambia una fecha) ───
                if ($cambiaFechas && $efFi && $efFf) {
                    $estadosNoBloqueantes = $this->getEstadosNoBloqueantesIds();

                    $solapada = Reserva::where('id_rsv_catalogo_inmueble', $reserva->id_rsv_catalogo_inmueble)
                        ->where('id', '!=', $reserva->id)
                        ->whereNotNull('fecha_inicio')
                        ->whereNotNull('fecha_fin')
                        ->where('fecha_inicio', '<', $efFf)
                        ->whereRaw('DATE_ADD(fecha_fin, INTERVAL 1 DAY) > ?', [$efFi])
                        ->when(!empty($estadosNoBloqueantes), function ($q) use ($estadosNoBloqueantes) {
                            $q->whereNotIn('id_rsv_statuses', $estadosNoBloqueantes);
                        })
                        ->exists();

                    if ($solapada) {
                        return response()->json([
                            'success' => false,
                            'message' => 'El nuevo rango de fechas se solapa con otra reserva existente (o su día de alistamiento).',
                        ], 422);
                    }
                }
            }

            /* 🆕 v31-EXC · texto de auditoría (justificación + contexto del
               límite saltado); se incrusta en el historial DENTRO de la
               transacción: si el historial falla, no se guarda nada */
            $textoAuditoria = ($motivoExcepcion !== null && $evalLimite !== null)
                ? $this->textoAuditoriaExcepcion(
                    $evalLimite, 'update', $reserva->id, (int) $userIdLimite,
                    $motivoExcepcion,
                    $efFi ? $efFi->toDateString() : null, $efFf ? $efFf->toDateString() : null,
                    $reserva->id_rsv_catalogo_inmueble)
                : '';

            DB::transaction(function () use ($reserva, $validatedData, $textoAuditoria) {
                $estadoAnterior = $reserva->id_rsv_statuses;
                $nuevoEstado = $validatedData['id_rsv_statuses'] ?? $estadoAnterior;

                $reserva->update($validatedData);

                if ($estadoAnterior !== $nuevoEstado) {
                    HistorialEstado::create([
                        'id_rsv_reservas'          => $reserva->id,
                        'id_rsv_statuses_anterior' => $estadoAnterior,
                        'id_rsv_statuses_nuevo'    => $nuevoEstado,
                        'id_user'                  => Auth::id() ?? $reserva->id_user,
                        /* 🆕 v31-EXC · el historial ES la auditoría */
                        'comentario'               => 'Actualización de estado en modificación general de reserva.'
                            . ($textoAuditoria !== '' ? ' · ' . $textoAuditoria : ''),
                    ]);
                } elseif ($textoAuditoria !== '') {
                    /* 🆕 v31-EXC · sin cambio de estado, la excepción igual
                       queda auditada en el historial de la reserva */
                    HistorialEstado::create([
                        'id_rsv_reservas'          => $reserva->id,
                        'id_rsv_statuses_anterior' => $estadoAnterior,
                        'id_rsv_statuses_nuevo'    => $nuevoEstado,
                        'id_user'                  => Auth::id() ?? $reserva->id_user,
                        'comentario'               => 'Excepción de límite anual aplicada en modificación general de reserva.'
                            . ' · ' . $textoAuditoria,
                    ]);
                }
            });

            $reserva->load(['inmueble', 'user', 'status', 'origen']);
            return response()->json([
                'success' => true,
                'message' => 'Reserva actualizada exitosamente.',
                'data'    => $reserva,
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar la reserva.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $reserva = Reserva::find($id);

            if (!$reserva) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reserva solicitada no existe.',
                ], 404);
            }

            DB::transaction(function () use ($reserva) {
                $reserva->delete(); 
            });

            return response()->json([
                'success' => true,
                'message' => 'Reserva archivada/eliminada lógicamente de manera exitosa.',
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function cambiarEstado(Request $request, string $id): JsonResponse
    {
        try {
            $reserva = Reserva::find($id);
            if (!$reserva) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reserva solicitada no existe.',
                ], 404);
            }

            $validatedData = $request->validate([
                'id_rsv_statuses' => 'required|exists:rsv_statuses,id',
                'comentario'       => 'nullable|string',
            ]);

            $estadoAnterior = $reserva->id_rsv_statuses;
            $nuevoEstado = $validatedData['id_rsv_statuses'];

            if ($estadoAnterior === $nuevoEstado) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reserva ya se encuentra en el estado indicado.',
                ], 422);
            }

            DB::transaction(function () use ($reserva, $estadoAnterior, $nuevoEstado, $validatedData) {
                $reserva->update(['id_rsv_statuses' => $nuevoEstado]);
                HistorialEstado::create([
                    'id_rsv_reservas'          => $reserva->id,
                    'id_rsv_statuses_anterior' => $estadoAnterior,
                    'id_rsv_statuses_nuevo'    => $nuevoEstado,
                    'id_user'                  => Auth::id() ?? $reserva->id_user,
                    'comentario'               => $validatedData['comentario'] ?? 'Cambio manual de estado.',
                ]);
            });

            $reserva->load(['status']);
            return response()->json([
                'success' => true,
                'message' => 'Estado de la reserva cambiado exitosamente.',
                'data'    => $reserva,
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al cambiar el estado.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function fechasOcupadasAjax(Request $request, string $inmueble): JsonResponse
    {
        try {
            $inmuebleObj = CatalogoInmueble::find($inmueble);
            if (!$inmuebleObj) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'Inmueble no encontrado.',
                ], 404);
            }

            $excludeId = $request->integer('exclude');
            $estadosNoBloqueantes = $this->getEstadosNoBloqueantesIds();

            $reservasQuery = Reserva::with('user')
                ->where('id_rsv_catalogo_inmueble', $inmueble)
                ->whereNotNull('fecha_inicio')
                ->whereNotNull('fecha_fin')
                ->when(!empty($estadosNoBloqueantes), function ($q) use ($estadosNoBloqueantes) {
                    $q->whereNotIn('id_rsv_statuses', $estadosNoBloqueantes);
                })
                ->when($excludeId, function ($q) use ($excludeId) {
                    $q->where('id', '!=', $excludeId);
                });

            $reservas = $reservasQuery->get(['id', 'codigo_reserva', 'fecha_inicio', 'fecha_fin', 'id_user', 'id_rsv_statuses']);

            $ocupadas = [];
            foreach ($reservas as $r) {
                try {
                    $fi = \Illuminate\Support\Carbon::parse($r->fecha_inicio)->startOfDay();
                    $ff = \Illuminate\Support\Carbon::parse($r->fecha_fin)->startOfDay();

                    $titular = null;
                    if ($r->user) {
                        $titular = [
                            'name'  => (string) ($r->user->name ?? ''),
                            'nid'   => (string) ($r->user->nid ?? ''),
                            'email' => (string) ($r->user->email ?? ''),
                        ];
                    }

                    $ocupadas[] = [
                        'inicio'  => $fi->format('Y-m-d'),
                        'fin'     => $ff->format('Y-m-d'),
                        'tipo'    => 'reserva',
                        'codigo'  => (string) ($r->codigo_reserva ?? ''),
                        'titular' => $titular,
                    ];

                    $alistamiento = $ff->copy()->addDay();
                    $ocupadas[] = [
                        'inicio'  => $alistamiento->format('Y-m-d'),
                        'fin'     => $alistamiento->format('Y-m-d'),
                        'tipo'    => 'alistamiento',
                        'codigo'  => (string) ($r->codigo_reserva ?? ''),
                        'titular' => $titular,
                    ];
                } catch (\Throwable $ex) {
                    continue;
                }
            }

            if (class_exists(\App\Models\Rsv\BloqueoCalendario::class)) {
                try {
                    $bloqueos = \App\Models\Rsv\BloqueoCalendario::where('id_rsv_catalogo_inmueble', $inmueble)
                        ->whereNotNull('fecha_inicio')
                        ->whereNotNull('fecha_fin')
                        ->get(['fecha_inicio', 'fecha_fin']);
                    foreach ($bloqueos as $b) {
                        try {
                            $fi = \Illuminate\Support\Carbon::parse($b->fecha_inicio)->startOfDay();
                            $ff = \Illuminate\Support\Carbon::parse($b->fecha_fin)->startOfDay();
                            $ocupadas[] = [
                                'inicio'  => $fi->format('Y-m-d'),
                                'fin'     => $ff->format('Y-m-d'),
                                'tipo'    => 'bloqueo',
                                'codigo'  => '',
                                'titular' => null,
                            ];
                        } catch (\Throwable $ex) {}
                    }
                } catch (\Throwable $ex) {}
            }

            $tarifas = [];
            $hoy = now()->startOfDay();
            if (class_exists(TarifaTemporada::class)) {
                try {
                    // 🆕 v29 · Solo tarifas ACTIVAS llegan al mini-calendario
                    // (una desactivada deja de mostrarse/aplicar en nueva reserva).
                    $tarifasDb = TarifaTemporada::where('id_rsv_catalogo_inmueble', $inmueble)
                        ->where('active', true)
                        ->whereDate('fecha_fin', '>=', $hoy->toDateString())
                        ->orderBy('fecha_inicio')
                        ->get([
                            'nombre_temporada', 'fecha_inicio', 'fecha_fin',
                            'precio_noche', 'precio_fin_semana',
                            'precio_minimo_reserva', 'dias_maximos',
                            'max_reservas_anuales_cliente', 'active',
                        ]);

                    foreach ($tarifasDb as $t) {
                        $tarifas[] = [
                            'nombre'                => $t->nombre_temporada,
                            'inicio'                => optional($t->fecha_inicio)->format('Y-m-d'),
                            'fin'                   => optional($t->fecha_fin)->format('Y-m-d'),
                            'precio_noche'          => (float) ($t->precio_noche ?? 0),
                            'precio_fin_semana'     => (float) ($t->precio_fin_semana ?? 0),
                            'precio_minimo_reserva' => (float) ($t->precio_minimo_reserva ?? 0),
                            'dias_maximos'          => (int) ($t->dias_maximos ?? 0),
                            'max_reservas_anuales_cliente' => (int) ($t->max_reservas_anuales_cliente ?? 0), // 🆕 v29
                            'active'                => (bool) ($t->active ?? true),
                        ];
                    }
                } catch (\Throwable $ex) {}
            }

            return response()->json([
                'ok'             => true,
                'inmueble'       => ['id' => $inmuebleObj->id, 'name' => $inmuebleObj->name],
                'ocupadas'       => $ocupadas,
                'tarifas'        => $tarifas,
                'monto_min_base' => self::MONTO_MIN_BASE,
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'ok'      => false,
                'message' => 'Error al obtener fechas ocupadas.',
            ], 500);
        }
    }

    public function storeAjax(Request $request): JsonResponse
    {
        $rules = [
            'id_rsv_catalogo_inmueble' => 'required|integer|exists:App\Models\Rsv\CatalogoInmueble,id',
            'id_user'                  => 'required|integer|exists:App\Models\User,id',
            'fecha_inicio'             => 'required|date|date_format:Y-m-d',
            'fecha_fin'                => 'required|date|date_format:Y-m-d|after_or_equal:fecha_inicio',
            'monto_total'              => 'required|numeric|min:0',
            'id_rsv_origen_reservas'   => 'nullable|integer|exists:App\Models\Rsv\OrigenReserva,id',
            'comentario_reserva'       => 'nullable|string|max:2000',
        ];

        try {
            $data = $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'ok'      => false,
                'message' => 'Datos inválidos.',
                'errors'  => $e->errors(),
            ], 422);
        }

        try {
            $fi = \Illuminate\Support\Carbon::parse($data['fecha_inicio'])->startOfDay();
            $ff = \Illuminate\Support\Carbon::parse($data['fecha_fin'])->startOfDay();
            $noches = (int) $fi->diffInDays($ff);

            $estadosNoBloqueantes = $this->getEstadosNoBloqueantesIds();

            // ─── 3 · 🆕 v29 · TARIFA APLICABLE PRIMERO (v28 la consultaba después) ───
            // getTarifaAplicable() SOLO devuelve tarifas ACTIVAS: al desactivar
            // una temporada, esta deja de gobernar monto mínimo, días máximos
            // y límite anual de las reservas nuevas.
            $montoMinAplicable = self::MONTO_MIN_BASE;
            $diasMaxAplicable  = 0;
            $tarifaAplicable   = $this->getTarifaAplicable($data['id_rsv_catalogo_inmueble'], $fi, $ff);

            if ($tarifaAplicable) {
                if ($tarifaAplicable->precio_minimo_reserva > 0) {
                    $montoMinAplicable = max(self::MONTO_MIN_BASE, (float) $tarifaAplicable->precio_minimo_reserva);
                }
                if ($tarifaAplicable->dias_maximos > 0) {
                    $diasMaxAplicable = (int) $tarifaAplicable->dias_maximos;
                }
            }

            // ─── 2 · 🆕 v31-EXC · LÍMITE ANUAL + EXCEPCIÓN CON AUDITORÍA ───
            // Ventana rolling ±1 año alrededor del check-in nuevo (se mantiene
            // v28/v28.1). El máximo se lee de max_reservas_anuales_cliente de la
            // tarifa ACTIVA aplicable; sin tarifa / null / 0 → 1 (legacy v28).
            // Si excede: la alerta sale igual; con motivo_excepcion
            // (obligatorio), continúa Y SE AUDITA (sin validar rol).
            $evalLimite = $this->evaluarLimiteAnual((int) $data['id_user'], $fi, null, $tarifaAplicable);

            $excepcionLimite = $this->resolverExcepcionLimite($request, $evalLimite['mensaje']);

            if (!$excepcionLimite['proceder']) {
                return $this->respuesta422Excepcion('ok', $evalLimite['mensaje'], $excepcionLimite);
            }

            $motivoExcepcion = $excepcionLimite['motivo']; // null si no hubo bloque

            if ((float) $data['monto_total'] < $montoMinAplicable) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'El monto total debe ser mayor o igual a $' . number_format($montoMinAplicable, 0)
                        . ' ' . ($tarifaAplicable ? '(según tarifa)' : '(mínimo del sistema)'),
                ], 422);
            }

            if ($diasMaxAplicable > 0 && $noches > $diasMaxAplicable) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'El rango seleccionado (' . $noches . ' noches) excede el máximo permitido por la tarifa.',
                ], 422);
            }

            // ─── 6 · VALIDAR SOLAPAMIENTO DE FECHAS + DÍA DE ALISTAMIENTO ───
            $solapada = Reserva::where('id_rsv_catalogo_inmueble', $data['id_rsv_catalogo_inmueble'])
                ->whereNotNull('fecha_inicio')
                ->whereNotNull('fecha_fin')
                ->where('fecha_inicio', '<', $ff)
                ->whereRaw('DATE_ADD(fecha_fin, INTERVAL 1 DAY) > ?', [$fi])
                ->when(!empty($estadosNoBloqueantes), function ($q) use ($estadosNoBloqueantes) {
                    $q->whereNotIn('id_rsv_statuses', $estadosNoBloqueantes);
                })
                ->exists();

            if ($solapada) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'Ya existe una reserva (o su día de alistamiento) para este inmueble que se solapa con el rango. '
                        . 'Recuerde que después de cada salida hay 1 día de alistamiento bloqueado.',
                ], 422);
            }

            if (class_exists(\App\Models\Rsv\BloqueoCalendario::class)) {
                $bloqueado = \App\Models\Rsv\BloqueoCalendario::where('id_rsv_catalogo_inmueble', $data['id_rsv_catalogo_inmueble'])
                    ->whereNotNull('fecha_inicio')
                    ->whereNotNull('fecha_fin')
                    ->where('fecha_inicio', '<', $ff)
                    ->where('fecha_fin', '>', $fi)
                    ->exists();

                if ($bloqueado) {
                    return response()->json([
                        'ok'      => false,
                        'message' => 'El rango seleccionado está bloqueado en el calendario del inmueble.',
                    ], 422);
                }
            }
        } catch (Throwable $e) {
            return response()->json([
                'ok'      => false,
                'message' => 'Error al validar reglas de negocio: ' . $e->getMessage(),
            ], 500);
        }

        $pendiente = Status::whereRaw('LOWER(name) LIKE ?', ['%pendien%'])
            ->orderBy('id', 'asc')
            ->first();
        if (!$pendiente) {
            $pendiente = Status::orderBy('id', 'asc')->first();
        }
        if (!$pendiente) {
            return response()->json([
                'ok'      => false,
                'message' => 'No hay estados definidos en el catálogo de RSV.',
            ], 422);
        }
        $data['id_rsv_statuses'] = $pendiente->id;
        $data['codigo_reserva'] = $this->generarCodigoReserva();

        // 🆕 v29 · Para dejar trazabilidad del límite aplicado en el historial
        $limiteAnualAplicable = $this->getLimiteAnualDesdeTarifa($tarifaAplicable);

        DB::beginTransaction();
        try {
            $reserva = Reserva::create([
                'codigo_reserva'           => $data['codigo_reserva'],
                'id_rsv_catalogo_inmueble' => $data['id_rsv_catalogo_inmueble'],
                'id_user'                  => $data['id_user'],
                'id_rsv_statuses'          => $data['id_rsv_statuses'],
                'fecha_inicio'             => $data['fecha_inicio'],
                'fecha_fin'                => $data['fecha_fin'],
                'monto_total'              => $data['monto_total'],
                'id_rsv_origen_reservas'   => $data['id_rsv_origen_reservas'] ?? null,
                'comentario_reserva'       => $data['comentario_reserva'] ?? null,
            ]);

            /* 🆕 v31-EXC · auditoría OBLIGATORIA en el historial (quién/
               cuándo/por qué/contexto), MISMA transacción: si el historial
               falla, la reserva NO se guarda. Sin tabla dedicada. */
            $textoAuditoria = $motivoExcepcion !== null
                ? $this->textoAuditoriaExcepcion(
                    $evalLimite, 'storeAjax', $reserva->id, (int) $data['id_user'],
                    $motivoExcepcion, $data['fecha_inicio'], $data['fecha_fin'],
                    $data['id_rsv_catalogo_inmueble'])
                : '';

            HistorialEstado::create([
                'id_rsv_reservas'          => $reserva->id,
                'id_rsv_statuses_anterior' => null,
                'id_rsv_statuses_nuevo'    => $reserva->id_rsv_statuses,
                'id_user'                  => Auth::check() ? Auth::id() : $data['id_user'],
                'comentario'               => 'Reserva creada desde tab-reservas · Estado inicial: Pendiente · Año ' . $fi->year
                    . ($tarifaAplicable ? ' · Tarifa aplicada: ' . $tarifaAplicable->nombre_temporada : ' · Sin tarifa aplicable')
                    . ' · Límite anual asociado: ' . $limiteAnualAplicable // 🆕 v29
                    . ($textoAuditoria !== '' ? ' · ' . $textoAuditoria : ''), // 🆕 v31-EXC
            ]);

            DB::commit();
            $reserva->load(['user', 'inmueble', 'status']);

            return response()->json([
                'ok'      => true,
                'message' => 'Reserva creada correctamente (estado inicial: Pendiente).',
                'reserva' => [
                    'id'        => $reserva->id,
                    'codigo'    => $reserva->codigo_reserva,
                    'titular'   => optional($reserva->user)->name,
                    'inmueble'  => optional($reserva->inmueble)->name,
                    'estado'    => optional($reserva->status)->name,
                    'fecha_ini' => $reserva->fecha_inicio?->format('Y-m-d'),
                    'fecha_fin' => $reserva->fecha_fin?->format('Y-m-d'),
                    'monto'     => (float) $reserva->monto_total,
                ],
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('storeAjax reserva: ' . $e->getMessage());
            return response()->json([
                'ok'      => false,
                'message' => 'Error interno al crear la reserva: ' . $e->getMessage(),
            ], 500);
        }
    }

    protected function generarCodigoReserva(): string
    {
        $hoy  = now();
        $pref = 'RSV-' . $hoy->format('Ymd') . '-';

        try {
            $conteo = Reserva::where('codigo_reserva', 'like', $pref . '%')
                ->whereDate('created_at', $hoy->toDateString())
                ->count() + 1;
            $codigo = $pref . str_pad((string) $conteo, 4, '0', STR_PAD_LEFT);

            for ($i = 0; $i < 50; $i++) {
                $existe = Reserva::where('codigo_reserva', $codigo)->exists();
                if (!$existe) {
                    return $codigo;
                }
                $codigo = $pref . str_pad((string) ($conteo + $i + 1), 4, '0', STR_PAD_LEFT) . Str::random(2);
            }
            return $codigo . Str::random(3);
        } catch (Throwable $e) {
            return $pref . str_pad((string) mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }
    }
}
