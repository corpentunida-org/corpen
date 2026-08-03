<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\CatalogoInmueble;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\View\View;

class CatalogoInmuebleController extends Controller
{
    /**
     * Display a listing of the resource.
     * Implementa paginación, carga ansiosa para la multimedia (portada), filtros y soporte Dual (Web/JSON).
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            // Cargar solo la multimedia destacada (portada) para optimizar el listado
            $query = CatalogoInmueble::with(['multimedia' => function ($q) {
                $q->where('es_portada', true);
            }]);

            // Filtros estratégicos
            if ($request->filled('city')) {
                $query->where('city', 'like', '%' . $request->city . '%');
            }

            if ($request->filled('active')) {
                $query->where('active', filter_var($request->active, FILTER_VALIDATE_BOOLEAN));
            }

            if ($request->filled('capacidad_minima')) {
                $query->where('capacidad_maxima', '>=', $request->capacidad_minima);
            }

            if ($request->filled('tipo_inmueble_id')) {
                $query->where('tipo_inmueble_id', $request->tipo_inmueble_id);
            }

            $sortField = $request->input('sort_by', 'name');
            $sortOrder = $request->input('sort_order', 'asc');
            $query->orderBy($sortField, $sortOrder);

            $inmuebles = $query->paginate($request->input('per_page', 15));

            // Si la petición es por API o AJAX, devolvemos JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Catálogo de inmuebles recuperado exitosamente.',
                    'data' => $inmuebles
                ]);
            }

            // Si es una petición del navegador web, renderizamos la vista Blade
            return view('rsv.inmuebles.index', compact('inmuebles'));

        } catch (\Throwable $e) {
            Log::error('Error al listar catálogo de inmuebles: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el catálogo de inmuebles.',
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al obtener el catálogo de inmuebles.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): JsonResponse
    {
        return response()->json(['message' => 'Método no soportado en la API.'], 405);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'ubicacion' => 'nullable|string|max:500',
            'active' => 'boolean',
            'capacidad_maxima' => 'required|integer|min:1',
            'precio_base_noche' => 'required|numeric|min:0',
            'tipo_inmueble_id' => 'required|integer',
        ]);

        try {
            // Asignar valor por defecto para 'active' si no se proporciona
            $validated['active'] = $validated['active'] ?? true;

            $inmueble = DB::transaction(function () use ($validated) {
                return CatalogoInmueble::create($validated);
            });

            return response()->json([
                'success' => true,
                'message' => 'Inmueble creado exitosamente.',
                'data' => $inmueble
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error al crear inmueble: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar el inmueble.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * Carga relaciones clave para la vista de detalle.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $inmueble = CatalogoInmueble::with([
                'multimedia',
                'tarifasTemporadas' => function ($q) {
                    $q->where('active', true)->where('fecha_fin', '>=', now());
                }
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Inmueble recuperado exitosamente.',
                'data' => $inmueble
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'El inmueble solicitado no existe.',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Error al mostrar inmueble: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error interno al recuperar el registro.',
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
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
            'name' => 'sometimes|required|string|max:255',
            'city' => 'sometimes|required|string|max:255',
            'ubicacion' => 'nullable|string|max:500',
            'active' => 'sometimes|boolean',
            'capacidad_maxima' => 'sometimes|required|integer|min:1',
            'precio_base_noche' => 'sometimes|required|numeric|min:0',
            'tipo_inmueble_id' => 'sometimes|required|integer',
        ]);

        try {
            $inmueble = CatalogoInmueble::findOrFail($id);

            DB::transaction(function () use ($inmueble, $validated) {
                $inmueble->update($validated);
            });

            return response()->json([
                'success' => true,
                'message' => 'Inmueble actualizado exitosamente.',
                'data' => $inmueble->fresh()
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'El inmueble a actualizar no existe.',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar inmueble: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar el inmueble.',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Previene la eliminación si tiene reservas asociadas.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $inmueble = CatalogoInmueble::withCount('reservas')->findOrFail($id);

            // Regla de Negocio: No se puede eliminar un inmueble con reservas históricas o activas.
            if ($inmueble->reservas_count > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el inmueble porque tiene reservas asociadas. Considere desactivarlo.',
                ], 422);
            }

            DB::transaction(function () use ($inmueble) {
                // Eliminar dependencias si es necesario (ej: multimedia, bloqueos)
                $inmueble->multimedia()->delete();
                $inmueble->bloqueosCalendario()->delete();
                $inmueble->tarifasTemporadas()->delete();

                $inmueble->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Inmueble eliminado exitosamente.'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'El inmueble a eliminar no existe.',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Error al eliminar inmueble: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al intentar eliminar el inmueble.',
            ], 500);
        }
    }

    /**
     * Cambiar el estado activo/inactivo del inmueble.
     */
    public function cambiarEstado(string $id): JsonResponse
    {
        try {
            $inmueble = CatalogoInmueble::findOrFail($id);

            $inmueble->active = !$inmueble->active;
            $inmueble->save();

            $estado = $inmueble->active ? 'activado' : 'desactivado';

            return response()->json([
                'success' => true,
                'message' => "El inmueble ha sido {$estado} exitosamente.",
                'data' => $inmueble
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'El inmueble solicitado no existe.',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Error al cambiar estado de inmueble: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al cambiar el estado del inmueble.',
            ], 500);
        }
    }
}
