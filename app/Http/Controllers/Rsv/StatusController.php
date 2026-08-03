<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\Status;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     * Soporte Dual (Web/JSON) con filtros de búsqueda y paginación.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = Status::query();

            // Búsqueda por nombre del estado
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where('name', 'like', "%{$search}%");
            }

            // Filtro por estado activo/inactivo
            if ($request->has('active')) {
                $query->where('active', filter_var($request->active, FILTER_VALIDATE_BOOLEAN));
            }

            $perPage = $request->get('per_page', 15);
            $statuses = $query->orderBy('name', 'asc')->paginate($perPage);

            // Si la petición es por API o AJAX, devolvemos JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Listado de estados obtenido exitosamente.',
                    'data' => $statuses,
                ], 200);
            }

            // Si es navegación web tradicional, renderizamos la vista Blade del módulo
            return view('rsv.statuses.index', compact('statuses'));

        } catch (Throwable $e) {
            Log::error('Error en StatusController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el listado de estados.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al obtener el listado de estados.');
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
            'name' => 'required|string|max:255|unique:rsv_statuses,name',
            'active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $status = DB::transaction(function () use ($validatedData) {
                return Status::create($validatedData);
            });

            return response()->json([
                'success' => true,
                'message' => 'Estado creado exitosamente.',
                'data' => $status,
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en StatusController@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar el estado.',
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
            $status = Status::find($id);

            if (!$status) {
                return response()->json([
                    'success' => false,
                    'message' => 'El estado solicitado no existe.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Estado obtenido exitosamente.',
                'data' => $status,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en StatusController@show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener el estado.',
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
            $status = Status::find($id);

            if (!$status) {
                return response()->json([
                    'success' => false,
                    'message' => 'El estado solicitado no existe.',
                ], 404);
            }

            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255|unique:rsv_statuses,name,' . $id,
                'active' => 'sometimes|boolean',
            ]);

            DB::transaction(function () use ($status, $validatedData) {
                $status->update($validatedData);
            });

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente.',
                'data' => $status,
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en StatusController@update: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar el estado.',
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
            $status = Status::find($id);

            if (!$status) {
                return response()->json([
                    'success' => false,
                    'message' => 'El estado solicitado no existe.',
                ], 404);
            }

            DB::transaction(function () use ($status) {
                $status->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Estado eliminado exitosamente.',
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en StatusController@destroy: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar el estado.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
