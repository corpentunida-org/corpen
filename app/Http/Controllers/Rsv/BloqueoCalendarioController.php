<?php
// app/Http/Controllers/Rsv/BloqueoCalendarioController.php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rsv\StoreBloqueoCalendarioRequest;
use App\Http\Requests\Rsv\UpdateBloqueoCalendarioRequest;
use App\Models\Rsv\BloqueoCalendario;
use App\Models\Rsv\Reserva;
use App\Models\Rsv\Status;
use App\Models\Rsv\CatalogoInmueble;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\View\View;
use Carbon\Carbon;

class BloqueoCalendarioController extends Controller
{
    /**
     * Estados de reserva que BLOQUEAN el calendario.
     * Ajusta los nombres a los que tengas en tu tabla rsv_status.
     */
    private const ESTATUS_QUE_BLOQUEAN = ['Pendiente', 'Confirmada', 'Pagada', 'En Curso'];

    /**
     * Display a listing of the resource.
     * Implementa paginación, carga ansiosa (N+1), filtros por inmueble y fechas, y soporte Dual (Web/JSON).
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = BloqueoCalendario::with('inmueble:id,name,city');

            // Filtro por Inmueble
            if ($request->filled('id_rsv_catalogo_inmueble')) {
                $query->where('id_rsv_catalogo_inmueble', $request->id_rsv_catalogo_inmueble);
            }

            // Filtro por rango de fechas (bloqueos activos en un periodo)
            if ($request->filled('fecha_desde') && $request->filled('fecha_hasta')) {
                $query->where(function ($q) use ($request) {
                    $q->whereBetween('fecha_inicio', [$request->fecha_desde, $request->fecha_hasta])
                      ->orWhereBetween('fecha_fin', [$request->fecha_desde, $request->fecha_hasta])
                      ->orWhere(function ($q2) use ($request) {
                          $q2->where('fecha_inicio', '<=', $request->fecha_desde)
                             ->where('fecha_fin', '>=', $request->fecha_hasta);
                      });
                });
            }

            $sortField = $request->input('sort_by', 'fecha_inicio');
            $sortOrder = $request->input('sort_order', 'asc');
            $query->orderBy($sortField, $sortOrder);

            $bloqueos = $query->paginate($request->input('per_page', 15));

            // Si la petición es por API o AJAX, devolvemos JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bloqueos de calendario recuperados exitosamente.',
                    'data' => $bloqueos
                ]);
            }

            // 🆕 Si es una petición del navegador web, cargamos también el catálogo para el modal
            $listaInmuebles = CatalogoInmueble::orderBy('name')->get(['id', 'name']);
            return view('rsv.calendarios.index', compact('bloqueos', 'listaInmuebles'));

        } catch (\Throwable $e) {
            Log::error('Error al listar bloqueos de calendario: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener los bloqueos del calendario.',
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al obtener los bloqueos del calendario.');
        }
    }

    /**
     * 🆕 Show the form for creating a new resource.
     * No aplica en una arquitectura API REST pura.
     */
    public function create(): JsonResponse
    {
        return response()->json(['message' => 'Método no soportado en la API.'], 405);
    }

    /**
     * Store a newly created resource in storage.
     * Valida integridad, fechas lógicas y previene superposición con BLOQUEOS y RESERVAS activas.
     * La validación de campos se ejecuta ANTES en StoreBloqueoCalendarioRequest.
     */
    public function store(StoreBloqueoCalendarioRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            // Regla de Negocio: no debe existir un bloqueo NI una reserva activa superpuesta
            $hasOverlap = $this->checkOverlap(
                $validated['id_rsv_catalogo_inmueble'],
                $validated['fecha_inicio'],
                $validated['fecha_fin']
            );

            if ($hasOverlap) {
                return response()->json([
                    'success' => false,
                    'message' => 'El inmueble ya tiene un bloqueo o reserva registrada en las fechas seleccionadas.',
                ], 422);
            }

            $bloqueo = DB::transaction(function () use ($validated) {
                return BloqueoCalendario::create($validated);
            });

            return response()->json([
                'success' => true,
                'message' => 'Bloqueo de calendario creado exitosamente.',
                'data' => $bloqueo->load('inmueble:id,name')
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error al crear bloqueo de calendario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al crear el bloqueo del calendario.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $bloqueo = BloqueoCalendario::with('inmueble:id,name,city')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Bloqueo recuperado exitosamente.',
                'data' => $bloqueo
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'El bloqueo de calendario solicitado no existe.',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Error al mostrar bloqueo de calendario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error interno al recuperar el registro.',
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * No aplica en una arquitectura API REST pura.
     */
    public function edit(string $id): JsonResponse
    {
        return response()->json(['message' => 'Método no soportado en la API.'], 405);
    }

    /**
     * Update the specified resource in storage.
     * La validación de campos se ejecuta ANTES en UpdateBloqueoCalendarioRequest.
     */
    public function update(UpdateBloqueoCalendarioRequest $request, string $id): JsonResponse
    {
        $validated = $request->validated();

        try {
            $bloqueo = BloqueoCalendario::findOrFail($id);

            $inmuebleId = $validated['id_rsv_catalogo_inmueble'] ?? $bloqueo->id_rsv_catalogo_inmueble;
            $fechaInicio = $validated['fecha_inicio'] ?? $bloqueo->fecha_inicio->format('Y-m-d');
            $fechaFin = $validated['fecha_fin'] ?? $bloqueo->fecha_fin->format('Y-m-d');

            // Validar superposición (bloqueos y reservas) excluyendo el bloqueo actual
            if (isset($validated['fecha_inicio']) || isset($validated['fecha_fin']) || isset($validated['id_rsv_catalogo_inmueble'])) {
                $hasOverlap = $this->checkOverlap($inmuebleId, $fechaInicio, $fechaFin, $bloqueo->id);

                if ($hasOverlap) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Las nuevas fechas entran en conflicto con un bloqueo o reserva existente.',
                    ], 422);
                }
            }

            DB::transaction(function () use ($bloqueo, $validated) {
                $bloqueo->update($validated);
            });

            return response()->json([
                'success' => true,
                'message' => 'Bloqueo de calendario actualizado exitosamente.',
                'data' => $bloqueo->fresh(['inmueble:id,name'])
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'El bloqueo de calendario no existe.',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar bloqueo de calendario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar el bloqueo.',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $bloqueo = BloqueoCalendario::findOrFail($id);

            DB::transaction(function () use ($bloqueo) {
                $bloqueo->delete(); // Eliminará lógicamente si el modelo usa SoftDeletes
            });

            return response()->json([
                'success' => true,
                'message' => 'Bloqueo de calendario eliminado exitosamente.'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'El bloqueo de calendario no existe.',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Error al eliminar bloqueo de calendario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar el bloqueo.',
            ], 500);
        }
    }

    /**
     * 🆕🆕🆕 FECHAS OCUPADAS (para el mini calendario del modal)
     * GET /rsv/bloqueos-calendario/fechas-ocupadas/{inmueble}?exclude=ID
     *
     * Devuelve los días ocupados del inmueble con el DETALLE completo que el
     * popup necesita (tipo, motivo, código, titular, estado), consistente con
     * las mismas reglas de negocio de checkOverlap():
     *   - Bloqueos del inmueble (excluyendo el propio si viene ?exclude=).
     *   - Reservas activas (solo estados de ESTATUS_QUE_BLOQUEAN).
     */
    public function fechasOcupadas(Request $request, int $inmueble): JsonResponse
    {
        try {
            $excludeId = $request->query('exclude');
            $ocupadas = [];

            // ─── 1) BLOQUEOS (con motivo) ───
            $bq = BloqueoCalendario::with('inmueble:id,name')
                ->where('id_rsv_catalogo_inmueble', $inmueble);

            if ($excludeId) {
                $bq->where('id', '!=', (int) $excludeId);
            }

            $bq->get(['id', 'id_rsv_catalogo_inmueble', 'fecha_inicio', 'fecha_fin', 'motivo'])
                ->each(function ($b) use (&$ocupadas) {
                    $ocupadas[] = [
                        'tipo'     => 'bloqueo',
                        'id'       => $b->id,
                        'inicio'   => Carbon::parse($b->fecha_inicio)->toDateString(),
                        'fin'      => Carbon::parse($b->fecha_fin)->toDateString(),
                        'motivo'   => (string) ($b->motivo ?? ''),
                        'inmueble' => $b->inmueble->name ?? '',
                    ];
                });

            // ─── 2) RESERVAS ACTIVAS (con titular y código) ───
            $statusIdsBloqueantes = Status::whereIn('name', self::ESTATUS_QUE_BLOQUEAN)->pluck('id');

            if ($statusIdsBloqueantes->isNotEmpty()) {
                Reserva::with(['user:id,name,nid,email', 'status:id,name'])
                    ->where('id_rsv_catalogo_inmueble', $inmueble)
                    ->whereIn('id_rsv_status', $statusIdsBloqueantes)
                    ->get(['id', 'id_rsv_catalogo_inmueble', 'fecha_inicio', 'fecha_fin', 'codigo_reserva', 'id_rsv_status'])
                    ->each(function ($r) use (&$ocupadas) {
                        $ocupadas[] = [
                            'tipo'    => 'reserva',
                            'id'      => $r->id,
                            'inicio'  => Carbon::parse($r->fecha_inicio)->toDateString(),
                            'fin'     => Carbon::parse($r->fecha_fin)->toDateString(),
                            'codigo'  => (string) ($r->codigo_reserva ?? ''),
                            'titular' => [
                                'name'  => $r->user->name  ?? 'Sin asignar',
                                'nid'   => $r->user->nid   ?? '',
                                'email' => $r->user->email ?? '',
                            ],
                            'estado'  => $r->status->name ?? 'Pendiente',
                        ];
                    });
            }

            return response()->json([
                'success'  => true,
                'ocupadas' => $ocupadas,
            ]);

        } catch (\Throwable $e) {
            Log::error('Error al obtener fechas ocupadas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener la disponibilidad del inmueble.',
            ], 500);
        }
    }

    /**
     * Método auxiliar para verificar solapamiento de fechas.
     * Regla de Negocio completa:
     * 1. No debe existir un BLOQUEO que se superponga.
     * 2. No debe existir una RESERVA ACTIVA (Pendiente, Confirmada, Pagada, En Curso) que se superponga.
     */
    private function checkOverlap(int $inmuebleId, string $fechaInicio, string $fechaFin, ?int $excludeId = null): bool
    {
        $inicio = Carbon::parse($fechaInicio)->toDateString();
        $fin    = Carbon::parse($fechaFin)->toDateString();

        // --- 1) BLOQUEOS existentes superpuestos ---
        $bloqueoQuery = BloqueoCalendario::where('id_rsv_catalogo_inmueble', $inmuebleId)
            ->where(function ($q) use ($inicio, $fin) {
                $q->whereBetween('fecha_inicio', [$inicio, $fin])
                  ->orWhereBetween('fecha_fin', [$inicio, $fin])
                  ->orWhere(function ($q2) use ($inicio, $fin) {
                      $q2->where('fecha_inicio', '<=', $inicio)
                         ->where('fecha_fin', '>=', $fin);
                  });
            });

        if ($excludeId) {
            $bloqueoQuery->where('id', '!=', $excludeId);
        }

        if ($bloqueoQuery->exists()) {
            return true;
        }

        // --- 2) RESERVAS ACTIVAS superpuestas ---
        return $this->checkReservaOverlap($inmuebleId, $inicio, $fin);
    }

    /**
     * Verifica si existe una RESERVA en estado activo que se superponga con las fechas dadas.
     */
    private function checkReservaOverlap(int $inmuebleId, string $inicio, string $fin): bool
    {
        $statusIdsBloqueantes = Status::whereIn('name', self::ESTATUS_QUE_BLOQUEAN)->pluck('id');

        if ($statusIdsBloqueantes->isEmpty()) {
            return false;
        }

        return Reserva::where('id_rsv_catalogo_inmueble', $inmuebleId)
            ->whereIn('id_rsv_status', $statusIdsBloqueantes)
            ->where(function ($q) use ($inicio, $fin) {
                $q->whereBetween('fecha_inicio', [$inicio, $fin])
                  ->orWhereBetween('fecha_fin', [$inicio, $fin])
                  ->orWhere(function ($q2) use ($inicio, $fin) {
                      $q2->where('fecha_inicio', '<=', $inicio)
                         ->where('fecha_fin', '>=', $fin);
                  });
            })
            ->exists();
    }
}
