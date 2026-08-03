<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\TarifaTemporada;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class TarifaTemporadaController extends Controller
{
    /**
     * Display a listing of the resource.
     * Soporte Dual (Web/JSON) con filtros y paginación.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = TarifaTemporada::query();

            if ($request->has('id_rsv_catalogo_inmueble')) {
                $query->where('id_rsv_catalogo_inmueble', $request->id_rsv_catalogo_inmueble);
            }

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where('nombre', 'like', "%{$search}%");
            }

            $perPage = $request->get('per_page', 15);
            $tarifas = $query->orderBy('fecha_inicio', 'asc')->paginate($perPage);

            // Si la petición es por API o AJAX, devolvemos JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Listado de tarifas de temporada obtenido exitosamente.',
                    'data' => $tarifas,
                ], 200);
            }

            // Si es navegación web tradicional, renderizamos la vista Blade del módulo
            return view('rsv.tarifas-temporada.index', compact('tarifas'));

        } catch (Throwable $e) {
            Log::error('Error en TarifaTemporadaController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el listado de tarifas de temporada.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al obtener el listado de tarifas de temporada.');
        }
    }

    /**
     * Show the form for creating a new resource.
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
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'id_rsv_catalogo_inmueble' => 'required|exists:rsv_catalogo_inmueble,id',
            'nombre' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'precio' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $tarifa = DB::transaction(function () use ($validatedData) {
                return TarifaTemporada::create($validatedData);
            });

            return response()->json([
                'success' => true,
                'message' => 'Tarifa de temporada creada exitosamente.',
                'data' => $tarifa,
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en TarifaTemporadaController@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar la tarifa de temporada.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $tarifa = TarifaTemporada::find($id);

            if (!$tarifa) {
                return response()->json([
                    'success' => false,
                    'message' => 'La tarifa de temporada solicitada no existe.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tarifa de temporada obtenida exitosamente.',
                'data' => $tarifa,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en TarifaTemporadaController@show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener la tarifa de temporada.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
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
     */
    public function update(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $tarifa = TarifaTemporada::find($id);

            if (!$tarifa) {
                return response()->json([
                    'success' => false,
                    'message' => 'La tarifa de temporada solicitada no existe.',
                ], 404);
            }

            $validatedData = $request->validate([
                'id_rsv_catalogo_inmueble' => 'sometimes|required|exists:rsv_catalogo_inmueble,id',
                'nombre' => 'sometimes|required|string|max:255',
                'fecha_inicio' => 'sometimes|required|date',
                'fecha_fin' => 'sometimes|required|date|after_or_equal:fecha_inicio',
                'precio' => 'sometimes|required|numeric|min:0',
            ]);

            DB::transaction(function () use ($tarifa, $validatedData) {
                $tarifa->update($validatedData);
            });

            return response()->json([
                'success' => true,
                'message' => 'Tarifa de temporada actualizada exitosamente.',
                'data' => $tarifa,
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en TarifaTemporadaController@update: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar la tarifa de temporada.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $tarifa = TarifaTemporada::find($id);

            if (!$tarifa) {
                return response()->json([
                    'success' => false,
                    'message' => 'La tarifa de temporada solicitada no existe.',
                ], 404);
            }

            DB::transaction(function () use ($tarifa) {
                $tarifa->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Tarifa de temporada eliminada exitosamente.',
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en TarifaTemporadaController@destroy: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar la tarifa de temporada.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
