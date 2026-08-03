<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\TipoReceptor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class TipoReceptorController extends Controller
{
    /**
     * Display a listing of the resource.
     * Soporte Dual (Web/JSON) con filtros y paginación.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = TipoReceptor::query();

            // Búsqueda por nombre
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where('name', 'like', "%{$search}%");
            }

            // Filtro por estado activo/inactivo
            if ($request->has('active')) {
                $query->where('active', filter_var($request->active, FILTER_VALIDATE_BOOLEAN));
            }

            $perPage = $request->get('per_page', 15);
            $tiposReceptor = $query->orderBy('name', 'asc')->paginate($perPage);

            // Si la petición es por API o AJAX, devolvemos JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Listado de tipos de receptor obtenido exitosamente.',
                    'data' => $tiposReceptor,
                ], 200);
            }

            // Si es navegación web tradicional, renderizamos la vista Blade del módulo
            return view('rsv.tipo-receptores.index', compact('tiposReceptor'));

        } catch (Throwable $e) {
            Log::error('Error en TipoReceptorController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el listado de tipos de receptor.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al obtener el listado de tipos de receptor.');
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
            'name' => 'required|string|max:255|unique:rsv_tipo_receptores,name',
            'active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $tipoReceptor = DB::transaction(function () use ($validatedData) {
                return TipoReceptor::create($validatedData);
            });

            return response()->json([
                'success' => true,
                'message' => 'Tipo de receptor creado exitosamente.',
                'data' => $tipoReceptor,
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en TipoReceptorController@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar el tipo de receptor.',
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
            $tipoReceptor = TipoReceptor::find($id);

            if (!$tipoReceptor) {
                return response()->json([
                    'success' => false,
                    'message' => 'El tipo de receptor solicitado no existe.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tipo de receptor obtenido exitosamente.',
                'data' => $tipoReceptor,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en TipoReceptorController@show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener el tipo de receptor.',
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
            $tipoReceptor = TipoReceptor::find($id);

            if (!$tipoReceptor) {
                return response()->json([
                    'success' => false,
                    'message' => 'El tipo de receptor solicitado no existe.',
                ], 404);
            }

            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255|unique:rsv_tipo_receptores,name,' . $id,
                'active' => 'sometimes|boolean',
            ]);

            DB::transaction(function () use ($tipoReceptor, $validatedData) {
                $tipoReceptor->update($validatedData);
            });

            return response()->json([
                'success' => true,
                'message' => 'Tipo de receptor actualizado exitosamente.',
                'data' => $tipoReceptor,
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en TipoReceptorController@update: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar el tipo de receptor.',
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
            $tipoReceptor = TipoReceptor::find($id);

            if (!$tipoReceptor) {
                return response()->json([
                    'success' => false,
                    'message' => 'El tipo de receptor solicitado no existe.',
                ], 404);
            }

            DB::transaction(function () use ($tipoReceptor) {
                $tipoReceptor->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Tipo de receptor eliminado exitosamente.',
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en TipoReceptorController@destroy: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar el tipo de receptor.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
