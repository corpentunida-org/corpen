<?php

/**
 * ============================================================================
 *  ReservaController · App\Http\Controllers\Rsv · v27 (FINAL)
 * ============================================================================
 *
 *  Controller del módulo de Reservas (RSV).
 *
 *  Métodos públicos:
 *  ────────────────────────────────────────────────────────────────────────
 *   · index()              → Listado con filtros (Web/AJAX/JSON)
 *   · create()             → 405 (no implementado para API REST)
 *   · store()              → Endpoint RESTful estándar (StoreReservaRequest)
 *   · storeAjax()          → Creación desde el modal de tab-reservas (AJAX)
 *   · show()               → Detalle con carga profunda de relaciones
 *   · edit()               → 405 (no implementado)
 *   · update()             → Actualización con detección de cambio de estado
 *   · destroy()            → Soft delete (Eliminación visual, preserva BD)
 *   · cambiarEstado()      → Cambio de estado justificado + historial
 *   · fechasOcupadasAjax() → Disponibilidad por inmueble (para mini-calendario)
 *
 *  Helpers protegidos:
 *  ────────────────────────────────────────────────────────────────────────
 *   · generarCodigoReserva()       → Código único con verificación defensiva
 *   · getEstadosNoBloqueantesIds() → IDs de estados que NO bloquean (Cancelada, Rechazada, Anulada)
 *
 *  Reglas de negocio críticas (NO REMOVER):
 *  ────────────────────────────────────────────────────────────────────────
 *   1. storeAjax() SIEMPRE asigna estado "Pendiente" (ignora el del form).
 *   2. 🆕 v27 · UN ASOCIADO SOLO PUEDE TENER UNA RESERVA POR AÑO
 *      (sin importar el inmueble). El check-in (fecha_inicio) define el año.
 *      Se valida rigurosamente en store(), storeAjax() y update().
 *      🆕 v27.1 · Se EXCLUYEN reservas con estado Cancelada/Rechazada/Anulada
 *      (esas reservas no cuentan como "activas" para esta regla).
 *   3. storeAjax() valida SOLAPAMIENTO de fechas contra reservas existentes
 *      del mismo inmueble + bloqueos de calendario + día de alistamiento.
 *      🆕 v27.1 · También excluye reservas canceladas/rechazadas/anuladas.
 *   4. storeAjax() valida monto mínimo según tarifa aplicable
 *      (precio_minimo_reserva) o 200.000 si no hay tarifa.
 *   5. storeAjax() valida noches máximas según tarifa aplicable (dias_maximos).
 *   6. Toda creación/actualización/cambio de estado registra HistorialEstado.
 *   7. La creación/actualización va envuelta en DB::transaction para rollback.
 *   8. fechasOcupadasAjax() incluye datos del TITULAR (name, nid, email,
 *      codigo_reserva) en cada entrada de ocupadas, para que el calendario
 *      pueda mostrar quién tiene la reserva al clicar un día bloqueado.
 *      🆕 v27.1 · Excluye reservas Cancelada/Rechazada/Anulada (no se
 *      muestran como días ocupados en el calendario).
 * ============================================================================
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
    /* ========================================================================
       MÉTODO: MONTO MÍNIMO BASE DEL SISTEMA
       ========================================================================
       Constante usada como fallback cuando no existe tarifa aplicable al
       rango seleccionado. El usuario lo referencia también en el Blade
       (tab-reservas.blade.php) cuando no hay tarifa configurada.
       ======================================================================== */
    private const MONTO_MIN_BASE = 200000;


    /* ========================================================================
       HELPER · ESTADOS NO-BLOQUEANTES
       ========================================================================
       Devuelve los IDs de estados que NO deben bloquear la creación de
       nuevas reservas, ni aparecer en el calendario como días ocupados.
       Estos estados representan reservas que ya no están vigentes:
         · Cancelada / Cancelado
         · Rechazada / Rechazado
         · Anulada  / Anulado
       La detección es por nombre (LOWER LIKE) para ser resistente a
       cambios de IDs entre entornos. Si el catálogo de estados cambia,
       no hay que tocar este código.
       ======================================================================== */
    private function getEstadosNoBloqueantesIds(): array
    {
        try {
            if (!class_exists(Status::class)) {
                return [];
            }
            return Status::where(function ($q) {$q->whereRaw('LOWER(name) LIKE ?', ['%cancela%'])
                  ->orWhereRaw('LOWER(name) LIKE ?', ['%rechaz%'])
                  ->orWhereRaw('LOWER(name) LIKE ?', ['%anulad%']);
            })->pluck('id')->toArray();
        } catch (\Throwable $e) {
            Log::warning('getEstadosNoBloqueantesIds: ' . $e->getMessage());
            return [];
        }
    }


    /* ========================================================================
       CRUD · MÉTODOS DEL RESOURCE CONTROLLER
       ======================================================================== */

    /**
     * Display a listing of the resource.
     *
     * Soporte triple de respuesta:
     *   · wantsJson() + !ajax() → JSON plano (API REST / Calendario)
     *   · ajax()                → JSON con HTML renderizado (tablas Blade)
     *   · default               → vista Blade tradicional
     *
     * Filtros aceptados por query string:
     *   · search               → busca en código, comentario, usuario, inmueble
     *   · id_rsv_statuses      → filtra por estado exacto
     *   · id_rsv_catalogo_inmueble → filtra por inmueble exacto
     *   · fecha_desde          → filtra reservas con fecha_inicio >= fecha_desde
     *   · fecha_hasta          → filtra reservas con fecha_fin    <= fecha_hasta
     *   · per_page             → paginación dinámica (default 15)
     *
     * @param  Request  $request
     * @return View|JsonResponse|RedirectResponse
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            // 1. Relaciones optimizadas (eager loading para evitar N+1)
            $query = Reserva::with([
                'inmueble',
                'user',
                'status',
                'origen',
                'huespedes',
                'transacciones',
                'historialEndosos',
            ]);

            // 2. Filtro de Búsqueda (campo "search" del query string)
            if ($request->filled('search')) {$search = trim($request->search);$query->where(function ($q) use ($search) {
                    $q->where('codigo_reserva', 'like', "\%{$search}%")
                      ->orWhere('comentario_reserva', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($u) use ($search) {
                          $u->where('name', 'like', "\%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                      })
                      ->orWhereHas('inmueble', function ($i) use ($search) {
                          $i->where('name', 'like', "\%{$search}%");
                      });
                });
            }

            // 3. Filtros Exactos (status, inmueble)
            if ($request->filled('id_rsv_statuses')) {
                $query->where('id_rsv_statuses',$request->id_rsv_statuses);
            }
            if ($request->filled('id_rsv_catalogo_inmueble')) {
                $query->where('id_rsv_catalogo_inmueble',$request->id_rsv_catalogo_inmueble);
            }

            // 4. Filtros de Fechas para el Calendario
            if ($request->filled('fecha_desde')) {
                $query->whereDate('fecha_inicio', '>=',$request->fecha_desde);
            }
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('fecha_fin', '<=',$request->fecha_hasta);
            }

            // Paginación dinámica
            $perPage = $request->get('per_page', 15);$reservas = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // 5. Respuesta JSON para el Calendario / API REST
            if ($request->wantsJson() && !$request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data'    => $reservas->items(),
                ], 200);
            }

            // 6. Respuesta AJAX (Tablas Blade — devuelve HTML renderizado)
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html'    => view('rsv.reservas.partials.tabla-registros', compact('reservas'))->render(),
                ], 200);
            }

            // 7. Respuesta Vista Tradicional (navegación web)
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

    /**
     * Show the form for creating a new resource.
     * API REST — no implementado (la creación se hace vía storeAjax).
     */
    public function create(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Método no implementado para API REST.',
        ], 405);
    }

    /**
     * Store a newly created resource in storage.
     *
     * Endpoint RESTful estándar (POST /rsv/reservas) — usa FormRequest
     * StoreReservaRequest para validación centralizada.
     * Genera código con uniqid() (rápido, sin consecutivo diario).
     * Crea HistorialEstado inicial automáticamente.
     *
     * @param  StoreReservaRequest  $request
     * @return JsonResponse
     */
    public function store(StoreReservaRequest $request): JsonResponse
    {
        try {
            $validatedData =$request->validated();

            $fi = \Illuminate\Support\Carbon::parse($validatedData['fecha_inicio'])->startOfDay();
            $ff = \Illuminate\Support\Carbon::parse($validatedData['fecha_fin'])->startOfDay();

            // ─── 🆕 v27 · UN ASOCIADO = UNA RESERVA POR AÑO (sin importar el inmueble) ───
            // 🆕 v27.1 · Se EXCLUYEN reservas con estado Cancelada/Rechazada/Anulada
            //            para que un asociado pueda re-agendar tras cancelar.
            $yearReserva =$fi->year;
            $estadosNoBloqueantes =$this->getEstadosNoBloqueantesIds();
            $asociadoTieneReservaEnAnio = Reserva::where('id_user',$validatedData['id_user'])
                ->whereYear('fecha_inicio', $yearReserva)
                ->when(!empty($estadosNoBloqueantes), function ($q) use ($estadosNoBloqueantes) {
                    $q->whereNotIn('id_rsv_statuses',$estadosNoBloqueantes);
                })
                ->exists();

            if ($asociadoTieneReservaEnAnio) {
                return response()->json([
                    'success' => false,
                    'message' => 'El asociado seleccionado ya tiene una reserva ACTIVA con check-in en el año ' . $yearReserva
                        . '. Un asociado solo puede tener una reserva activa por año, sin importar el inmueble. '
                        . 'Si canceló una reserva previamente, verifique que su estado haya quedado como "Cancelada", "Rechazada" o "Anulada" para que libere el cupo.',
                ], 422);
            }

            // ─── Validar solapamiento de fechas (defensa en profundidad) ───
            // 🆕 v27.1 · También excluye reservas canceladas/rechazadas/anuladas
            $solapada = Reserva::where('id_rsv_catalogo_inmueble',$validatedData['id_rsv_catalogo_inmueble'])
                ->whereNotNull('fecha_inicio')
                ->whereNotNull('fecha_fin')
                ->where('fecha_inicio', '<', $ff)
                ->whereRaw('DATE_ADD(fecha_fin, INTERVAL 1 DAY) > ?', [$fi])
                ->when(!empty($estadosNoBloqueantes), function ($q) use ($estadosNoBloqueantes) {
                    $q->whereNotIn('id_rsv_statuses',$estadosNoBloqueantes);
                })
                ->exists();

            if ($solapada) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe una reserva para este inmueble que se solapa con el rango '
                        . $fi->format('d/m/Y') . ' - ' .$ff->format('d/m/Y')
                        . '. Elija otro rango de fechas.',
                ], 422);
            }

            // ─── Validar bloqueos de calendario (si existe el modelo) ───
            if (class_exists(\App\Models\Rsv\BloqueoCalendario::class)) {
                $bloqueado = \App\Models\Rsv\BloqueoCalendario::where('id_rsv_catalogo_inmueble',$validatedData['id_rsv_catalogo_inmueble'])
                    ->whereNotNull('fecha_inicio')
                    ->whereNotNull('fecha_fin')
                    ->where('fecha_inicio', '<', $ff)
                    ->where('fecha_fin', '>', $fi)
                    ->exists();

                if ($bloqueado) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El rango seleccionado está bloqueado en el calendario del inmueble. '
                            . 'Elija otro rango de fechas.',
                    ], 422);
                }
            }

            $reserva = DB::transaction(function () use ($validatedData) {
                // Código con uniqid() (rápido). Para creación vía modal usar
                // storeAjax() que tiene consecutivo diario RSV-YYYYMMDD-NNNN.
                $validatedData['codigo_reserva'] = 'RSV-' . strtoupper(uniqid());

                $reserva = Reserva::create($validatedData);

                // Registrar automáticamente el estado inicial en el historial
                HistorialEstado::create([
                    'id_rsv_reservas'          => $reserva->id,
                    'id_rsv_statuses_anterior' => null, // anterior: ninguno (creación)
                    'id_rsv_statuses_nuevo'    => $reserva->id_rsv_statuses,
                    'id_user'                  => Auth::id() ?? $reserva->id_user,
                    'comentario'               => 'Creación inicial de la reserva.',
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

    /**
     * Display the specified resource.
     *
     * Devuelve el detalle COMPLETO de la reserva con todas sus relaciones
     * anidadas. Usado por la vista de detalle y por la API REST.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $reserva = Reserva::with([
                'inmueble',
                'user',
                'status',
                'origen',
                'huespedes',
                'transacciones.pasarela', // Anidada: transacción → pasarela.
                'itinerarios',
                'mensajes',
                'reviews',
                'historialEstados',
                'historialEndosos',
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
            Log::error('Error en ReservaController@show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener la reserva.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * API REST — no implementado (las ediciones van por update()).
     */
    public function edit(string $id): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Método no implementado para API REST.',
        ], 405);
    }

    /**
     * Update the specified resource in storage.
     *
     * Endpoint RESTful estándar (PUT/PATCH /rsv/reservas/{id}).
     * Usa FormRequest UpdateReservaRequest para validación.
     * Detecta automáticamente cambio de estado y lo registra en HistorialEstado.
     */
    public function update(UpdateReservaRequest $request, string$id): JsonResponse
    {
        try {
            $reserva = Reserva::find($id);

            if (!$reserva) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reserva solicitada no existe.',
                ], 404);
            }

            $validatedData =$request->validated();

            // ─── Si se está cambiando el rango de fechas, validar solapamiento ───
            $newFi = isset($validatedData['fecha_inicio'])
                ? \Illuminate\Support\Carbon::parse($validatedData['fecha_inicio'])->startOfDay()
                : null;
            $newFf = isset($validatedData['fecha_fin'])
                ? \Illuminate\Support\Carbon::parse($validatedData['fecha_fin'])->startOfDay()
                : null;

            if ($newFi &&$newFf) {
                // ─── 🆕 v27 · UN ASOCIADO = UNA RESERVA POR AÑO (excluyendo la propia) ───
                // 🆕 v27.1 · Excluye reservas canceladas/rechazadas/anuladas
                $yearReserva =$newFi->year;
                $userId = $validatedData['id_user'] ?? $reserva->id_user;
                $estadosNoBloqueantes =$this->getEstadosNoBloqueantesIds();
                $asociadoTieneReservaEnAnio = Reserva::where('id_user',$userId)
                    ->where('id', '!=', $reserva->id)
                    ->whereYear('fecha_inicio', $yearReserva)
                    ->when(!empty($estadosNoBloqueantes), function ($q) use ($estadosNoBloqueantes) {
                        $q->whereNotIn('id_rsv_statuses',$estadosNoBloqueantes);
                    })
                    ->exists();

                if ($asociadoTieneReservaEnAnio) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El asociado ya tiene otra reserva ACTIVA con check-in en el año ' . $yearReserva
                            . '. Un asociado solo puede tener una reserva activa por año.',
                    ], 422);
                }

                // ─── Validación de Solapamiento
                // 🆕 v27.1 · Excluye reservas canceladas/rechazadas/anuladas
                $solapada = Reserva::where('id_rsv_catalogo_inmueble',$reserva->id_rsv_catalogo_inmueble)
                    ->where('id', '!=', $reserva->id) // excluir la propia reserva que se edita
                    ->whereNotNull('fecha_inicio')
                    ->whereNotNull('fecha_fin')
                    ->where('fecha_inicio', '<', $newFf)
                    ->whereRaw('DATE_ADD(fecha_fin, INTERVAL 1 DAY) > ?', [$newFi])
                    ->when(!empty($estadosNoBloqueantes), function ($q) use ($estadosNoBloqueantes) {
                        $q->whereNotIn('id_rsv_statuses',$estadosNoBloqueantes);
                    })
                    ->exists();

                if ($solapada) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El nuevo rango de fechas se solapa con otra reserva existente (o su día de alistamiento).',
                    ], 422);
                }
            }

            DB::transaction(function () use ($reserva,$validatedData) {
                // Guardar estado anterior para detectar cambio
                $estadoAnterior =$reserva->id_rsv_statuses;
                $nuevoEstado = $validatedData['id_rsv_statuses'] ?? $estadoAnterior;

                $reserva->update($validatedData);

                // Registrar en historial si el estado cambió
                if ($estadoAnterior !==$nuevoEstado) {
                    HistorialEstado::create([
                        'id_rsv_reservas'          => $reserva->id,
                        'id_rsv_statuses_anterior' => $estadoAnterior,
                        'id_rsv_statuses_nuevo'    => $nuevoEstado,
                        'id_user'                  => Auth::id() ?? $reserva->id_user,
                        'comentario'               => 'Actualización de estado en modificación general de reserva.',
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
            Log::error('Error en ReservaController@update: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar la reserva.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage (Soft Delete).
     *
     * El modelo Reserva usa trait SoftDeletes, así que esto solo marca
     * 'deleted_at' y oculta la reserva en consultas futuras (sin borrarla
     * físicamente de la base de datos).
     */
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

            DB::transaction(function () use ($reserva) {$reserva->delete(); // Soft delete (marca deleted_at)
            });

            return response()->json([
                'success' => true,
                'message' => 'Reserva archivada/eliminada lógicamente de manera exitosa.',
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en ReservaController@destroy: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }


    /* ========================================================================
       ACCIONES ESPECÍFICAS (no resource)
       ======================================================================== */

    /**
     * Cambiar el estado de la reserva de forma específica y justificada.
     *
     * A diferencia de update() (que actualiza todo), este endpoint SOLO cambia
     * el estado y registra el motivo en el historial. Útil para flujos
     * tipo "Confirmar reserva", "Cancelar reserva", "Marcar como No-Show".
     *
     * URL:  PATCH /rsv/reservas/{id}/cambiar-estado
     * Nombre: rsv.reservas.cambiar_estado
     *
     * Body:
     *   · id_rsv_statuses (required) → nuevo estado
     *   · comentario (nullable)      → justificación del cambio
     *
     * @param  Request  $request
     * @param  string   $id
     * @return JsonResponse
     */
    public function cambiarEstado(Request $request, string$id): JsonResponse
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

            // Validar que el estado realmente esté cambiando
            if ($estadoAnterior === $nuevoEstado) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reserva ya se encuentra en el estado indicado.',
                ], 422);
            }

            DB::transaction(function () use ($reserva, $estadoAnterior, $nuevoEstado,$validatedData) {
                $reserva->update(['id_rsv_statuses' =>$nuevoEstado]);

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
            Log::error('Error en ReservaController@cambiarEstado: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al cambiar el estado de la reserva.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }


    /* ========================================================================
       MÉTODOS PARA EL MODAL DE NUEVA RESERVA (tab-reservas.blade.php)
       ======================================================================== */

    /**
     * GET /rsv/reservas/fechas-ocupadas/{inmueble}
     *
     * Devuelve la disponibilidad de un inmueble para alimentar el
     * mini-calendario del modal de Nueva Reserva.
     *
     * Respuesta JSON:
     *   · ok        → bool
     *   · inmueble  → {id, name}
     *   · ocupadas  → [{inicio, fin, tipo, codigo, titular}]  (reservas + bloqueos + alistamiento)
     *     Donde tipo = 'reserva' | 'alistamiento' | 'bloqueo'
     *     Y titular = {name, nid, email} | null  (datos del usuario que tiene la reserva)
     *     Y codigo  = string  (código de reserva RSV-YYYYMMDD-NNNN)
     *   · tarifas   → [{nombre, inicio, fin, precio_noche,
     *                  precio_fin_semana, precio_minimo_reserva,
     *                  dias_maximos, active}]
     *   · monto_min_base → int (200000 fallback si no hay tarifa)
     *
     * El campo `titular` y `codigo` permiten que el calendario muestre
     * un popup con los datos de quién tiene la reserva al hacer clic
     * en un día bloqueado.
     *
     * 🆕 v27.1 · Las reservas con estado Cancelada/Rechazada/Anulada
     *    NO aparecen como días ocupados en el calendario, ya que están
     *    liberadas para nuevas reservas. Tampoco se incluye su día de
     *    alistamiento.
     *
     * Query params opcionales:
     *   · exclude=N → excluye la reserva con id=N (para editar sin chocar
     *                 con ella misma)
     *
     * @param  Request  $request
     * @param  string   $inmueble  ID del inmueble
     * @return JsonResponse
     */
    public function fechasOcupadasAjax(Request $request, string$inmueble): JsonResponse
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

            // 🆕 v27.1 · IDs de estados no-bloqueantes (Cancelada/Rechazada/Anulada)
            $estadosNoBloqueantes = $this->getEstadosNoBloqueantesIds();

            // ─── 1 · Reservas existentes del inmueble (con titular) ─────
            // 🆕 v25 · Se carga la relación 'user' para incluir los datos
            // del titular (name, nid, email) en cada entrada de ocupadas.
            // Esto permite que el calendario muestre un popup con la info
            // de quién tiene la reserva al hacer clic en un día bloqueado.
            // 🆕 v27.1 · Excluye reservas canceladas/rechazadas/anuladas
            //           para que no aparezcan como días bloqueados.
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

                    // Datos del titular para mostrar al clic en día bloqueado
                    $titular = null;
                    if ($r->user) {
                        $titular = [
                            'name'  => (string) ($r->user->name ?? ''),
                            'nid'   => (string) ($r->user->nid ?? ''),
                            'email' => (string) ($r->user->email ?? ''),
                        ];
                    }

                    // Rango real de la reserva
                    $ocupadas[] = [
                        'inicio'  => $fi->format('Y-m-d'),
                        'fin'     => $ff->format('Y-m-d'),
                        'tipo'    => 'reserva',
                        'codigo'  => (string) ($r->codigo_reserva ?? ''),
                        'titular' => $titular,
                    ];

                    // 🧹 Día de alistamiento: 1 día después de la salida
                    // Bloquea el día siguiente para limpieza/preparación.
                    // Mismo titular y código que la reserva original.
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

            // ─── 2 · Bloqueos manuales del calendario (mantenimiento, overbooking) ───
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
                } catch (\Throwable $ex) {
                    // Si no existe el modelo o la tabla, simplemente no agrega bloqueos
                    Log::warning('fechasOcupadasAjax · BloqueoCalendario no disponible: ' . $ex->getMessage());
                }
            }

            // ─── 3 · Tarifas vigentes (a partir de hoy) ─────────────
            // Las usa el front para calcular precio sugerido y validar
            // monto mínimo + días máximos.
            $tarifas = [];
            $hoy = now()->startOfDay();
            if (class_exists(TarifaTemporada::class)) {
                try {
                    $tarifasDb = TarifaTemporada::where('id_rsv_catalogo_inmueble', $inmueble)
                        ->whereDate('fecha_fin', '>=', $hoy->toDateString())
                        ->orderBy('fecha_inicio')
                        ->get([
                            'nombre_temporada', 'fecha_inicio', 'fecha_fin',
                            'precio_noche', 'precio_fin_semana',
                            'precio_minimo_reserva', 'dias_maximos', 'active',
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
                            'active'                => (bool) ($t->active ?? true),
                        ];
                    }
                } catch (\Throwable $ex) {
                    Log::warning('fechasOcupadasAjax · tarifas no disponibles: ' . $ex->getMessage());
                }
            }

            return response()->json([
                'ok'             => true,
                'inmueble'       => ['id' => $inmuebleObj->id, 'name' => $inmuebleObj->name],
                'ocupadas'       => $ocupadas,
                'tarifas'        => $tarifas,
                'monto_min_base' => self::MONTO_MIN_BASE,
            ], 200);

        } catch (Throwable $e) {
            Log::error('fechasOcupadasAjax: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'ok'      => false,
                'message' => 'Error al obtener fechas ocupadas.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * POST /rsv/reservas/store-ajax
     *
     * Crea una reserva nueva desde el modal de tab-reservas.blade.php.
     *
     * Reglas de negocio aplicadas (en este orden):
     *   1. Validación de campos base (inmueble, titular, fechas, monto).
     *   2. 🆕 v27 · UN ASOCIADO = UNA RESERVA POR AÑO (sin importar inmueble).
     *      🆕 v27.1 · Se excluyen reservas Cancelada/Rechazada/Anulada.
     *   3. Calcular tarifa aplicable al rango (temporada que cubre TODO el rango).
     *   4. Validar monto mínimo dinámico (precio_minimo_reserva de la tarifa,
     *      o 200.000 si no hay tarifa aplicable).
     *   5. Validar noches máximas según tarifa (dias_maximos).
     *   6. VALIDAR SOLAPAMIENTO de fechas contra reservas existentes
     *      del mismo inmueble + bloqueos de calendario + día de alistamiento.
     *      🆕 v27.1 · Excluye reservas Cancelada/Rechazada/Anulada.
     *   7. Forzar estado inicial = "Pendiente" (no respeta el del form).
     *   8. Generar código único RSV-YYYYMMDD-NNNN.
     *   9. Crear Reserva + HistorialEstado en transacción atómica.
     *
     * Respuesta exitosa: {ok, message, reserva:{id, codigo, titular,
     *                    inmueble, estado, fecha_ini, fecha_fin, monto}}
     * Errores: {ok:false, message, errors?} con HTTP 422 (validación)
     *          o 500 (error interno).
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function storeAjax(Request $request): JsonResponse
    {
        // ─── 1 · Validación base ──────────────────────────────────────
        // IMPORTANTE: usar FQCN del modelo en exists: para que Laravel
        // resuelva automáticamente el $table del modelo.
        // (Ej: CatalogoInmueble declara $table='rsv_catalogo_inmueble' SINGULAR)
        $rules = [
            'id_rsv_catalogo_inmueble' => 'required|integer|exists:App\Models\Rsv\CatalogoInmueble,id',
            'id_user'                  => 'required|integer|exists:App\Models\User,id',
            'fecha_inicio'             => 'required|date|date_format:Y-m-d',
            'fecha_fin'                => 'required|date|date_format:Y-m-d|after_or_equal:fecha_inicio',
            'monto_total'              => 'required|numeric|min:0',
            'id_rsv_origen_reservas'   => 'nullable|integer|exists:App\Models\Rsv\OrigenReserva,id',
            'comentario_reserva'       => 'nullable|string|max:2000',
            // NOTA: 'id_rsv_statuses' NO se valida aquí porque SIEMPRE se
            //       fuerza a "Pendiente" (regla de negocio #7).
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

            // 🆕 v27.1 · Cargar IDs de estados no-bloqueantes una sola vez
            $estadosNoBloqueantes = $this->getEstadosNoBloqueantesIds();

            // ─── 2 · 🆕 v27 · UN ASOCIADO = UNA RESERVA POR AÑO ────────────
            // REGLA DE NEGOCIO: Un asociado (titular) solo puede tener UNA reserva
            // por año calendario, SIN IMPORTAR EL INMUEBLE. El check-in
            // (fecha_inicio) define el año de la reserva.
            // 🆕 v27.1 · Se EXCLUYEN reservas con estado Cancelada/Rechazada/Anulada
            //            para permitir re-agendar tras una cancelación previa.
            $yearReserva = $fi->year;
            $asociadoTieneReservaEnAnio = Reserva::where('id_user', $data['id_user'])
                ->whereYear('fecha_inicio', $yearReserva)
                ->when(!empty($estadosNoBloqueantes), function ($q) use ($estadosNoBloqueantes) {
                    $q->whereNotIn('id_rsv_statuses', $estadosNoBloqueantes);
                })
                ->exists();

            if ($asociadoTieneReservaEnAnio) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'El asociado seleccionado ya tiene una reserva ACTIVA con check-in en el año ' . $yearReserva
                        . '. Un asociado solo puede tener una reserva activa por año, sin importar el inmueble. '
                        . 'Si tiene una reserva cancelada/rechazada/anulada, esta ya NO cuenta como activa y debió liberar el cupo. '
                        . 'Verifique que el estado de la reserva previa sea "Cancelada", "Rechazada" o "Anulada" '
                        . '(no "Pendiente" ni "Confirmada"). Si necesita reservar para el año ' . $yearReserva
                        . ', primero debe cancelar o eliminar la reserva ACTIVA existente de ese asociado, o elegir un año diferente.',
                ], 422);
            }

            // ─── 3 · Calcular tarifa aplicable al rango ───────────────────
            // Tarifa aplicable = la temporada que cubre TODO el rango seleccionado.
            // Si hay múltiples, se toma la más reciente (ORDER BY fecha_inicio DESC).
            $montoMinAplicable = self::MONTO_MIN_BASE; // fallback
            $diasMaxAplicable  = 0;                    // 0 = sin límite
            $tarifaAplicable   = null;

            if (class_exists(TarifaTemporada::class)) {
                $tarifaAplicable = TarifaTemporada::where('id_rsv_catalogo_inmueble', $data['id_rsv_catalogo_inmueble'])
                    ->whereDate('fecha_inicio', '<=', $fi->toDateString())
                    ->whereDate('fecha_fin', '>=', $ff->toDateString())
                    ->orderBy('fecha_inicio', 'desc')
                    ->first();

                if ($tarifaAplicable) {
                    // Si la tarifa define un precio_minimo_reserva > 200.000,
                    // usar ese valor como mínimo. Si no, fallback a 200.000.
                    if ($tarifaAplicable->precio_minimo_reserva > 0) {
                        $montoMinAplicable = max(self::MONTO_MIN_BASE, (float) $tarifaAplicable->precio_minimo_reserva);
                    }
                    if ($tarifaAplicable->dias_maximos > 0) {
                        $diasMaxAplicable = (int) $tarifaAplicable->dias_maximos;
                    }
                }
            }

            // ─── 4 · Validar monto mínimo dinámico ────────────────────
            if ((float) $data['monto_total'] < $montoMinAplicable) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'El monto total debe ser mayor o igual a $' . number_format($montoMinAplicable, 0)
                        . ' ' . ($tarifaAplicable ? '(según tarifa: ' . $tarifaAplicable->nombre_temporada . ')' : '(mínimo del sistema)'),
                ], 422);
            }

            // ─── 5 · Validar días máximos según tarifa ────────────────
            if ($diasMaxAplicable > 0 && $noches > $diasMaxAplicable) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'El rango seleccionado (' . $noches . ' noches) excede el máximo permitido por la tarifa '
                        . $tarifaAplicable->nombre_temporada . ' (' . $diasMaxAplicable . ' noches).',
                ], 422);
            }

            // ─── 6 · VALIDAR SOLAPAMIENTO DE FECHAS + DÍA DE ALISTAMIENTO ───
            // Solapa si: reserva_existente.fecha_inicio < nueva.fecha_fin
            //            AND (reserva_existente.fecha_fin + 1 día) > nueva.fecha_inicio
            // El "+1 día" representa el día de alistamiento (cleaning day).
            // 🆕 v27.1 · Excluye reservas Cancelada/Rechazada/Anulada
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
                    'message' => 'Ya existe una reserva (o su día de alistamiento) para este inmueble que se solapa con el rango '
                        . $fi->format('d/m/Y') . ' - ' . $ff->format('d/m/Y')
                        . '. Recuerde que después de cada salida hay 1 día de alistamiento bloqueado.',
                ], 422);
            }

            // ─── 6b · Validar contra bloqueos de calendario ────────────
            // Si el modelo existe, verificar también que no haya un bloqueo
            // manual (mantenimiento, overbooking, etc.) que afecte el rango.
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
                        'message' => 'El rango seleccionado está bloqueado en el calendario del inmueble. '
                            . 'Elija otro rango de fechas.',
                    ], 422);
                }
            }
        } catch (Throwable $e) {
            return response()->json([
                'ok'      => false,
                'message' => 'Error al validar reglas de negocio: ' . $e->getMessage(),
            ], 500);
        }

        // ─── 7 · Forzar estado inicial = "Pendiente" ──────────────────
        // REGLA DE NEGOCIO: el estado inicial SIEMPRE es "Pendiente",
        // sin importar lo que el form envíe. El usuario NO decide el estado.
        $pendiente = Status::whereRaw('LOWER(name) LIKE ?', ['%pendien%'])
            ->orderBy('id', 'asc')
            ->first();
        if (!$pendiente) {
            // Fallback al estado de menor ID si no encuentra "Pendiente"
            $pendiente = Status::orderBy('id', 'asc')->first();
        }
        if (!$pendiente) {
            return response()->json([
                'ok'      => false,
                'message' => 'No hay estados definidos en el catálogo de RSV.',
            ], 422);
        }
        $data['id_rsv_statuses'] = $pendiente->id;

        // ─── 8 · Generar código único (RSV-YYYYMMDD-NNNN) ──────────────
        $data['codigo_reserva'] = $this->generarCodigoReserva();

        // ─── 9 · Transacción: Reserva + HistorialEstado ────────────────
        // Si algo falla (DB, FK, etc.), se hace rollback de ambos.
        DB::beginTransaction();
        try {
            // 9a · Crear la reserva
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

            // 9b · Registrar en HistorialEstado (transición null → Pendiente)
            HistorialEstado::create([
                'id_rsv_reservas'          => $reserva->id,
                'id_rsv_statuses_anterior' => null, // estado anterior: ninguno (creación)
                'id_rsv_statuses_nuevo'    => $reserva->id_rsv_statuses,
                'id_user'                  => Auth::check() ? Auth::id() : $data['id_user'],
                'comentario'               => 'Reserva creada desde tab-reservas · Estado inicial: Pendiente · Año ' . $fi->year
                    . ($tarifaAplicable ? ' · Tarifa aplicada: ' . $tarifaAplicable->nombre_temporada : ' · Sin tarifa aplicable'),
            ]);

            DB::commit();

            // 9c · Cargar relaciones para el payload de respuesta
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

            Log::error('storeAjax reserva: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data'  => $data,
            ]);

            return response()->json([
                'ok'      => false,
                'message' => 'Error interno al crear la reserva: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Genera un código único de reserva con formato: RSV-YYYYMMDD-NNNN
     *
     * Donde NNNN es el consecutivo del día basado en reservas ya creadas
     * hoy. Si por concurrencia dos peticiones generan el mismo código,
     * agrega un colchón aleatorio de 2 caracteres para evitar colisión.
     *
     * Verificación de unicidad defensiva: hasta 50 intentos.
     * Fallback final: código con random si todo falla.
     *
     * @return string
     */
    protected function generarCodigoReserva(): string
    {
        $hoy  = now();
        $pref = 'RSV-' . $hoy->format('Ymd') . '-';

        try {
            // Contar reservas creadas HOY con este prefijo
            $conteo = Reserva::where('codigo_reserva', 'like', $pref . '%')
                ->whereDate('created_at', $hoy->toDateString())
                ->count() + 1;

            $codigo = $pref . str_pad((string) $conteo, 4, '0', STR_PAD_LEFT);

            // Verificación de unicidad (hasta 50 intentos)
            for ($i = 0; $i < 50; $i++) {
                $existe = Reserva::where('codigo_reserva', $codigo)->exists();
                if (!$existe) {
                    return $codigo;
                }
                // Si existe (concurrencia), generar siguiente + colchón aleatorio
                $codigo = $pref . str_pad((string) ($conteo + $i + 1), 4, '0', STR_PAD_LEFT) . Str::random(2);
            }

            // Si después de 50 intentos no encuentra único, agrega más random
            return $codigo . Str::random(3);
        } catch (Throwable $e) {
            // Fallback final: random si todo lo demás falla
            return $pref . str_pad((string) mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }
    }
}