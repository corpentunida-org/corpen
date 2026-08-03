<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\BloqueoCalendario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\View\View;

class BloqueoCalendarioController extends Controller
{
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

            // Si es una petición del navegador web, renderizamos la vista Blade
            return view('rsv.calendarios.index', compact('bloqueos'));

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
     * Show the form for creating a new resource.
     * No aplica en una arquitectura API REST pura.
     */
    public function create(): JsonResponse
    {
        return response()->json(['message' => 'Método no soportado en la API.'], 405);
    }

    /**
     * Store a newly created resource in storage.
     * Valida integridad, fechas lógicas y previene superposición de bloqueos.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_rsv_catalogo_inmueble' => 'required|integer|exists:rsv_catalogo_inmueble,id',
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'motivo' => 'required|string|max:500',
        ]);

        try {
            // Regla de Negocio: Validar que no exista un bloqueo superpuesto para este inmueble
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

            $bloqueo = null;
            DB::transaction(function () use ($validated, &$bloqueo) {
                $bloqueo = BloqueoCalendario::create($validated);
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
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'id_rsv_catalogo_inmueble' => 'sometimes|required|integer|exists:rsv_catalogo_inmueble,id',
            'fecha_inicio' => 'sometimes|required|date',
            'fecha_fin' => 'sometimes|required|date|after_or_equal:fecha_inicio',
            'motivo' => 'sometimes|required|string|max:500',
        ]);

        try {
            $bloqueo = BloqueoCalendario::findOrFail($id);

            $inmuebleId = $validated['id_rsv_catalogo_inmueble'] ?? $bloqueo->id_rsv_catalogo_inmueble;
            $fechaInicio = $validated['fecha_inicio'] ?? $bloqueo->fecha_inicio->format('Y-m-d');
            $fechaFin = $validated['fecha_fin'] ?? $bloqueo->fecha_fin->format('Y-m-d');

            // Validar superposición excluyendo el bloqueo actual
            if (isset($validated['fecha_inicio']) || isset($validated['fecha_fin']) || isset($validated['id_rsv_catalogo_inmueble'])) {
                $hasOverlap = $this->checkOverlap($inmuebleId, $fechaInicio, $fechaFin, $bloqueo->id);

                if ($hasOverlap) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Las nuevas fechas entran en conflicto con un bloqueo existente.',
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
                $bloqueo->delete();
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
     * Método auxiliar para verificar solapamiento de fechas.
     */
    private function checkOverlap(int $inmuebleId, string $fechaInicio, string $fechaFin, ?int $excludeId = null): bool
    {
        $query = BloqueoCalendario::where('id_rsv_catalogo_inmueble', $inmuebleId)
            ->where(function ($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                  ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                  ->orWhere(function ($q2) use ($fechaInicio, $fechaFin) {
                      $q2->where('fecha_inicio', '<=', $fechaInicio)
                         ->where('fecha_fin', '>=', $fechaFin);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
