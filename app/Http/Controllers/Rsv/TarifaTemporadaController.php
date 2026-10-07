<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\TarifaTemporada;
use App\Http\Requests\Rsv\StoreTarifaTemporadaRequest;
use App\Http\Requests\Rsv\UpdateTarifaTemporadaRequest;
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
                $query->where('nombre_temporada', 'like', "%{$search}%");
            }

            $perPage = $request->get('per_page', 15);
            $tarifas = $query->orderBy('fecha_inicio', 'asc')->paginate($perPage);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Listado de tarifas de temporada obtenido exitosamente.',
                    'data' => $tarifas,
                ], 200);
            }

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
     * Soporte Dual (Web redirect / JSON API).
     */
    public function store(StoreTarifaTemporadaRequest $request): JsonResponse|RedirectResponse
    {
        // 1. Obtiene los datos ya validados por el FormRequest
        $validatedData = $request->validated();

        // 2. Ajuste manual para el checkbox HTML
        $validatedData['active'] = $request->has('active') ? true : false;

        DB::beginTransaction();

        try {
            $tarifa = DB::transaction(function () use ($validatedData) {
                return TarifaTemporada::create($validatedData);
            });

            DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tarifa de temporada creada exitosamente.',
                    'data' => $tarifa,
                ], 201);
            }

            return redirect()->route('rsv.inmuebles.show', $validatedData['id_rsv_catalogo_inmueble'])
                ->with('success', 'Tarifa de temporada registrada exitosamente.');

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en TarifaTemporadaController@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al registrar la tarifa de temporada.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->withInput()->with('error', 'Ocurrió un error al registrar la tarifa de temporada.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): JsonResponse
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
     * Soporte Dual (Web redirect / JSON API).
     */
    public function update(UpdateTarifaTemporadaRequest $request, string $id): JsonResponse|RedirectResponse
    {
        DB::beginTransaction();

        try {
            $tarifa = TarifaTemporada::find($id);

            if (!$tarifa) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La tarifa de temporada solicitada no existe.',
                    ], 404);
                }
                return redirect()->route('rsv.admin.dashboard')->with('error', 'La tarifa solicitada no existe.');
            }

            // 1. Obtiene los datos ya validados por el FormRequest
            $validatedData = $request->validated();

            // 2. Ajuste manual para el checkbox HTML
            if ($request->has('active')) {
                $validatedData['active'] = $request->has('active') ? true : false;
            }

            DB::transaction(function () use ($tarifa, $validatedData) {
                $tarifa->update($validatedData);
            });

            DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tarifa de temporada actualizada exitosamente.',
                    'data' => $tarifa,
                ], 200);
            }

            return redirect()->route('rsv.inmuebles.show', $tarifa->id_rsv_catalogo_inmueble)
                ->with('success', 'Tarifa actualizada exitosamente.');

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en TarifaTemporadaController@update: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al actualizar la tarifa de temporada.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->withInput()->with('error', 'Ocurrió un error al actualizar la tarifa de temporada.');
        }
    }

    /**
     * Remove the specified resource from storage.
     * Soporte Dual (Web redirect / JSON API).
     */
    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        DB::beginTransaction();

        try {
            $tarifa = TarifaTemporada::find($id);

            if (!$tarifa) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La tarifa de temporada solicitada no existe.',
                    ], 404);
                }
                return back()->with('error', 'La tarifa solicitada no existe.');
            }

            $inmuebleId = $tarifa->id_rsv_catalogo_inmueble;

            DB::transaction(function () use ($tarifa) {
                $tarifa->delete();
            });

            DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tarifa de temporada eliminada exitosamente.',
                ], 200);
            }

            return redirect()->route('rsv.inmuebles.show', $inmuebleId)
                ->with('success', 'Tarifa eliminada exitosamente.');

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en TarifaTemporadaController@destroy: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al eliminar la tarifa de temporada.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al eliminar la tarifa de temporada.');
        }
    }
}
