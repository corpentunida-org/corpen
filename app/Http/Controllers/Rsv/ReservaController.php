<?php

/**
 * ============================================================================
 *  ReservaController · App\Http\Controllers\Rsv · v28 (CORREGIDO)
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
 *  Reglas de negocio críticas (NO REMOVER):
 *  ────────────────────────────────────────────────────────────────────────
 *   1. storeAjax() SIEMPRE asigna estado "Pendiente" (ignora el del form).
 *   2. 🆕 v28 · UN ASOCIADO SOLO PUEDE TENER UNA RESERVA CON UN AÑO DE DIFERENCIA
 *      (sin importar el inmueble). El check-in (fecha_inicio) define la restricción.
 *      Se cambió whereYear() por whereBetween() para ser 100% compatible con la BD
 *      y asegurar que la distancia mínima sea 1 año.
 *      🆕 v28.1 · Se EXCLUYEN reservas con estado Cancelada/Rechazada/Anulada.
 *   3. storeAjax() valida SOLAPAMIENTO de fechas contra reservas existentes
 *      del mismo inmueble + bloqueos de calendario + día de alistamiento.
 *   4. storeAjax() valida monto mínimo según tarifa aplicable
 *      (precio_minimo_reserva) o 200.000 si no hay tarifa.
 *   5. storeAjax() valida noches máximas según tarifa aplicable (dias_maximos).
 *   6. Toda creación/actualización/cambio de estado registra HistorialEstado.
 *   7. La creación/actualización va envuelta en DB::transaction para rollback.
 *   8. fechasOcupadasAjax() incluye datos del TITULAR.
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
    private const MONTO_MIN_BASE = 200000;

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

            // ─── 🆕 v28 · VALIDACIÓN EXTRICTA: UNA RESERVA POR AÑO ───
            // Se usa rango estricto de 1 año (365 días) de diferencia.
            $limiteInferior = $fi->copy()->subYear()->format('Y-m-d');
            $limiteSuperior = $fi->copy()->addYear()->format('Y-m-d');

            $estadosNoBloqueantes = $this->getEstadosNoBloqueantesIds();
            $asociadoTieneReservaEnAnio = Reserva::where('id_user', $validatedData['id_user'])
                ->whereBetween('fecha_inicio', [$limiteInferior, $limiteSuperior])
                ->when(!empty($estadosNoBloqueantes), function ($q) use ($estadosNoBloqueantes) {
                    $q->whereNotIn('id_rsv_statuses', $estadosNoBloqueantes);
                })
                ->exists();

            if ($asociadoTieneReservaEnAnio) {
                return response()->json([
                    'success' => false,
                    'message' => 'El asociado seleccionado ya tiene una reserva ACTIVA en menos de un año de diferencia. '
                        . 'La diferencia de fechas entre reservas debe ser estrictamente de al menos 1 año. '
                        . 'Si canceló una reserva, verifique que su estado sea "Cancelada" o "Rechazada".',
                ], 422);
            }

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

            $reserva = DB::transaction(function () use ($validatedData) {
                $validatedData['codigo_reserva'] = 'RSV-' . strtoupper(uniqid());
                $reserva = Reserva::create($validatedData);

                HistorialEstado::create([
                    'id_rsv_reservas'          => $reserva->id,
                    'id_rsv_statuses_anterior' => null,
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
            $newFi = isset($validatedData['fecha_inicio']) ? \Illuminate\Support\Carbon::parse($validatedData['fecha_inicio'])->startOfDay() : null;
            $newFf = isset($validatedData['fecha_fin']) ? \Illuminate\Support\Carbon::parse($validatedData['fecha_fin'])->startOfDay() : null;

            if ($newFi && $newFf) {
                // ─── Validación 1 año diferencia ───
                $limiteInferior = $newFi->copy()->subYear()->format('Y-m-d');
                $limiteSuperior = $newFi->copy()->addYear()->format('Y-m-d');
                
                $userId = $validatedData['id_user'] ?? $reserva->id_user;
                $estadosNoBloqueantes = $this->getEstadosNoBloqueantesIds();
                
                $asociadoTieneReservaEnAnio = Reserva::where('id_user', $userId)
                    ->where('id', '!=', $reserva->id)
                    ->whereBetween('fecha_inicio', [$limiteInferior, $limiteSuperior])
                    ->when(!empty($estadosNoBloqueantes), function ($q) use ($estadosNoBloqueantes) {
                        $q->whereNotIn('id_rsv_statuses', $estadosNoBloqueantes);
                    })
                    ->exists();

                if ($asociadoTieneReservaEnAnio) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El asociado ya tiene otra reserva ACTIVA en menos de 1 año de diferencia. '
                            . 'Un asociado solo puede tener una reserva activa con 1 año de distancia mínima.',
                    ], 422);
                }

                // ─── Validación de Solapamiento
                $solapada = Reserva::where('id_rsv_catalogo_inmueble', $reserva->id_rsv_catalogo_inmueble)
                    ->where('id', '!=', $reserva->id)
                    ->whereNotNull('fecha_inicio')
                    ->whereNotNull('fecha_fin')
                    ->where('fecha_inicio', '<', $newFf)
                    ->whereRaw('DATE_ADD(fecha_fin, INTERVAL 1 DAY) > ?', [$newFi])
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

            DB::transaction(function () use ($reserva, $validatedData) {
                $estadoAnterior = $reserva->id_rsv_statuses;
                $nuevoEstado = $validatedData['id_rsv_statuses'] ?? $estadoAnterior;

                $reserva->update($validatedData);

                if ($estadoAnterior !== $nuevoEstado) {
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

            // ─── 2 · 🆕 v28 · UN ASOCIADO = UNA RESERVA CON AL MENOS 1 AÑO DE DISTANCIA ───
            // Se abandona el whereYear que puede fallar en ciertos motores y en cambio 
            // se verifica explicitamente un radio de 365 días (+/- un año) 
            $limiteInferior = $fi->copy()->subYear()->format('Y-m-d');
            $limiteSuperior = $fi->copy()->addYear()->format('Y-m-d');

            $asociadoTieneReservaEnAnio = Reserva::where('id_user', $data['id_user'])
                ->whereBetween('fecha_inicio', [$limiteInferior, $limiteSuperior])
                ->when(!empty($estadosNoBloqueantes), function ($q) use ($estadosNoBloqueantes) {
                    $q->whereNotIn('id_rsv_statuses', $estadosNoBloqueantes);
                })
                ->exists();

            if ($asociadoTieneReservaEnAnio) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'El asociado seleccionado ya tiene una reserva ACTIVA creada a menos de un año de distancia. '
                        . 'La diferencia obligatoria entre reservas debe ser al menos de 1 año exacto (365 días). '
                        . 'Si el usuario canceló su reserva anterior, asegúrate de que esté en estado "Cancelada" o "Anulada" y no "Pendiente".',
                ], 422);
            }

            // ─── 3 · Calcular tarifa aplicable al rango ───
            $montoMinAplicable = self::MONTO_MIN_BASE;
            $diasMaxAplicable  = 0;
            $tarifaAplicable   = null;

            if (class_exists(TarifaTemporada::class)) {
                $tarifaAplicable = TarifaTemporada::where('id_rsv_catalogo_inmueble', $data['id_rsv_catalogo_inmueble'])
                    ->whereDate('fecha_inicio', '<=', $fi->toDateString())
                    ->whereDate('fecha_fin', '>=', $ff->toDateString())
                    ->orderBy('fecha_inicio', 'desc')
                    ->first();

                if ($tarifaAplicable) {
                    if ($tarifaAplicable->precio_minimo_reserva > 0) {
                        $montoMinAplicable = max(self::MONTO_MIN_BASE, (float) $tarifaAplicable->precio_minimo_reserva);
                    }
                    if ($tarifaAplicable->dias_maximos > 0) {
                        $diasMaxAplicable = (int) $tarifaAplicable->dias_maximos;
                    }
                }
            }

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

            HistorialEstado::create([
                'id_rsv_reservas'          => $reserva->id,
                'id_rsv_statuses_anterior' => null,
                'id_rsv_statuses_nuevo'    => $reserva->id_rsv_statuses,
                'id_user'                  => Auth::check() ? Auth::id() : $data['id_user'],
                'comentario'               => 'Reserva creada desde tab-reservas · Estado inicial: Pendiente · Año ' . $fi->year
                    . ($tarifaAplicable ? ' · Tarifa aplicada: ' . $tarifaAplicable->nombre_temporada : ' · Sin tarifa aplicable'),
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