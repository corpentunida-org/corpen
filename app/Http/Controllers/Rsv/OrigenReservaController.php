<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\OrigenReserva;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class OrigenReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     * Soporte Dual (Web/JSON) con filtros de búsqueda y paginación.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = OrigenReserva::query();

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
            $origenes = $query->orderBy('name', 'asc')->paginate($perPage);

            // Si la petición es por API o AJAX, devolvemos JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Listado de orígenes de reserva obtenido exitosamente.',
                    'data' => $origenes,
                ], 200);
            }

            // Si es navegación web tradicional, renderizamos la vista Blade del módulo
            return view('rsv.origen-reservas.index', compact('origenes'));

        } catch (Throwable $e) {
            Log::error('Error en OrigenReservaController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el listado de orígenes de reserva.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al obtener el listado de orígenes de reserva.');
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
            'name' => 'required|string|max:255|unique:rsv_origen_reservas,name',
            'active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $origen = DB::transaction(function () use ($validatedData) {
                return OrigenReserva::create($validatedData);
            });

            return response()->json([
                'success' => true,
                'message' => 'Origen de reserva creado exitosamente.',
                'data' => $origen,
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en OrigenReservaController@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar el origen de reserva.',
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
            $origen = OrigenReserva::find($id);

            if (!$origen) {
                return response()->json([
                    'success' => false,
                    'message' => 'El origen de reserva solicitado no existe.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Origen de reserva obtenido exitosamente.',
                'data' => $origen,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en OrigenReservaController@show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener el origen de reserva.',
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
            $origen = OrigenReserva::find($id);

            if (!$origen) {
                return response()->json([
                    'success' => false,
                    'message' => 'El origen de reserva solicitado no existe.',
                ], 404);
            }

            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255|unique:rsv_origen_reservas,name,' . $id,
                'active' => 'sometimes|boolean',
            ]);

            DB::transaction(function () use ($origen, $validatedData) {
                $origen->update($validatedData);
            });

            return response()->json([
                'success' => true,
                'message' => 'Origen de reserva actualizado exitosamente.',
                'data' => $origen,
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en OrigenReservaController@update: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar el origen de reserva.',
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
            $origen = OrigenReserva::find($id);

            if (!$origen) {
                return response()->json([
                    'success' => false,
                    'message' => 'El origen de reserva solicitado no existe.',
                ], 404);
            }

            DB::transaction(function () use ($origen) {
                $origen->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Origen de reserva eliminado exitosamente.',
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en OrigenReservaController@destroy: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar el origen de reserva.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
