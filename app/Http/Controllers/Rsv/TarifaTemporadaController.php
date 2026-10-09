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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\View\View;
use Throwable;

/**
 * Controlador para la gestión de Tarifas por Temporada.
 * Maneja respuestas Duales (Web/JSON) y transacciones de base de datos seguras.
 */
class TarifaTemporadaController extends Controller
{
    /**
     * Muestra el listado de tarifas.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = TarifaTemporada::query();

            $query->when($request->filled('id_rsv_catalogo_inmueble'), function ($q) use ($request) {
                $q->where('id_rsv_catalogo_inmueble', $request->id_rsv_catalogo_inmueble);
            })->when($request->filled('search'), function ($q) use ($request) {
                $q->where('nombre_temporada', 'like', '%' . $request->search . '%');
            });

            $perPage = $request->get('per_page', 15);
            $tarifas = $query->orderBy('fecha_inicio', 'asc')->paginate($perPage);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Listado de tarifas de temporada obtenido exitosamente.',
                    'data'    => $tarifas,
                ], 200);
            }

            return view('rsv.tarifas-temporada.index', compact('tarifas'));

        } catch (Throwable $e) {
            Log::error('Error en TarifaTemporadaController@index: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el listado de tarifas.',
                    'error'   => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al cargar las tarifas de temporada.');
        }
    }

    public function create(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Método no implementado para API REST.',
        ], 405);
    }

    /**
     * Almacena una nueva tarifa de temporada.
     */
    public function store(StoreTarifaTemporadaRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $validatedData = $request->validated();

            $tarifa = DB::transaction(function () use ($validatedData) {
                return TarifaTemporada::create($validatedData);
            });

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tarifa de temporada creada exitosamente.',
                    'data'    => $tarifa,
                ], 201);
            }

            // MEJORA: Retorna exactamente a la misma vista actual (Global o Individual)
            return redirect()->back()
                ->with('success', 'Tarifa de temporada registrada exitosamente.');

        } catch (Throwable $e) {
            Log::error('Error en TarifaTemporadaController@store: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al registrar la tarifa.',
                ], 500);
            }

            return back()->withInput()->with('error', 'Ocurrió un error al registrar la tarifa.');
        }
    }

    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $tarifa = TarifaTemporada::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Tarifa de temporada obtenida exitosamente.',
                'data'    => $tarifa,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'La tarifa solicitada no existe.'], 404);
        } catch (Throwable $e) {
            Log::error('Error en TarifaTemporadaController@show: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Ocurrió un error interno.'], 500);
        }
    }

    public function edit(string $id): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Método no implementado para API REST.',
        ], 405);
    }

    /**
     * Actualiza la tarifa especificada en la base de datos.
     */
    public function update(UpdateTarifaTemporadaRequest $request, string $id): JsonResponse|RedirectResponse
    {
        try {
            $tarifa = TarifaTemporada::findOrFail($id);
            $validatedData = $request->validated();

            DB::transaction(function () use ($tarifa, $validatedData) {
                $tarifa->update($validatedData);
            });

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tarifa de temporada actualizada exitosamente.',
                    'data'    => $tarifa->fresh(),
                ], 200);
            }

            // MEJORA: Retorna exactamente a la misma vista actual (Global o Individual)
            return redirect()->back()
                ->with('success', 'Tarifa actualizada exitosamente.');

        } catch (ModelNotFoundException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'La tarifa solicitada no existe.'], 404);
            }
            return back()->with('error', 'La tarifa solicitada no existe.');

        } catch (Throwable $e) {
            Log::error('Error en TarifaTemporadaController@update: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Ocurrió un error al actualizar.'], 500);
            }
            return back()->withInput()->with('error', 'Ocurrió un error al actualizar la tarifa.');
        }
    }

    /**
     * Elimina la tarifa especificada.
     */
    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        try {
            $tarifa = TarifaTemporada::findOrFail($id);

            DB::transaction(function () use ($tarifa) {
                $tarifa->delete();
            });

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tarifa de temporada eliminada exitosamente.',
                ], 200);
            }

            // MEJORA: Retorna exactamente a la misma vista actual (Global o Individual)
            return redirect()->back()
                ->with('success', 'Tarifa eliminada exitosamente.');

        } catch (ModelNotFoundException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'La tarifa a eliminar no existe.'], 404);
            }
            return back()->with('error', 'La tarifa solicitada no existe.');

        } catch (Throwable $e) {
            Log::error('Error en TarifaTemporadaController@destroy: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Ocurrió un error al eliminar la tarifa.'], 500);
            }
            return back()->with('error', 'Ocurrió un error al eliminar la tarifa de temporada.');
        }
    }
}
