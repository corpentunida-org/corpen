<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\Pasarela;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class PasarelaController extends Controller
{
    /**
     * Display a listing of the resource.
     * Soporte Dual (Web/JSON) con filtros de búsqueda y paginación.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = Pasarela::query();

            // Búsqueda por nombre de la pasarela
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where('name', 'like', "%{$search}%");
            }

            // Filtro por estado activo/inactivo
            if ($request->has('active')) {
                $query->where('active', filter_var($request->active, FILTER_VALIDATE_BOOLEAN));
            }

            $perPage = $request->get('per_page', 15);
            $pasarelas = $query->orderBy('name', 'asc')->paginate($perPage);

            // Si la petición es por API o AJAX, devolvemos JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Listado de pasarelas de pago obtenido exitosamente.',
                    'data' => $pasarelas,
                ], 200);
            }

            // Si es navegación web tradicional, renderizamos la vista Blade del módulo
            return view('rsv.pasarelas.index', compact('pasarelas'));

        } catch (Throwable $e) {
            Log::error('Error en PasarelaController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el listado de pasarelas.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al obtener el listado de pasarelas.');
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
            'name' => 'required|string|max:255|unique:rsv_pasarelas,name',
            'active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $pasarela = DB::transaction(function () use ($validatedData) {
                return Pasarela::create($validatedData);
            });

            return response()->json([
                'success' => true,
                'message' => 'Pasarela de pago creada exitosamente.',
                'data' => $pasarela,
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en PasarelaController@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar la pasarela de pago.',
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
            $pasarela = Pasarela::find($id);

            if (!$pasarela) {
                return response()->json([
                    'success' => false,
                    'message' => 'La pasarela solicitada no existe.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pasarela obtenida exitosamente.',
                'data' => $pasarela,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en PasarelaController@show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener la pasarela.',
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
            $pasarela = Pasarela::find($id);

            if (!$pasarela) {
                return response()->json([
                    'success' => false,
                    'message' => 'La pasarela solicitada no existe.',
                ], 404);
            }

            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255|unique:rsv_pasarelas,name,' . $id,
                'active' => 'sometimes|boolean',
            ]);

            DB::transaction(function () use ($pasarela, $validatedData) {
                $pasarela->update($validatedData);
            });

            return response()->json([
                'success' => true,
                'message' => 'Pasarela de pago actualizada exitosamente.',
                'data' => $pasarela,
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en PasarelaController@update: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar la pasarela de pago.',
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
            $pasarela = Pasarela::find($id);

            if (!$pasarela) {
                return response()->json([
                    'success' => false,
                    'message' => 'La pasarela solicitada no existe.',
                ], 404);
            }

            DB::transaction(function () use ($pasarela) {
                $pasarela->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Pasarela de pago eliminada exitosamente.',
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en PasarelaController@destroy: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar la pasarela de pago.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
