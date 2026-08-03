<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\ItinerarioEvento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class ItinerarioEventoController extends Controller
{
    /**
     * Display a listing of the resource.
     * Soporte Dual (Web/JSON) con filtros de búsqueda y paginación.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = ItinerarioEvento::with(['reserva:id,codigo_reserva']);

            if ($request->has('id_rsv_reservas')) {
                $query->where('id_rsv_reservas', $request->id_rsv_reservas);
            }

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('titulo', 'like', "%{$search}%")
                      ->orWhere('descripcion', 'like', "%{$search}%");
                });
            }

            $perPage = $request->get('per_page', 15);
            $eventos = $query->orderBy('fecha_hora_inicio', 'asc')->paginate($perPage);

            // Si la petición es por API o AJAX, devolvemos JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Listado de eventos del itinerario obtenido exitosamente.',
                    'data' => $eventos,
                ], 200);
            }

            // Si es navegación web tradicional, renderizamos la vista Blade del módulo
            return view('rsv.itinerarios.index', compact('eventos'));

        } catch (Throwable $e) {
            Log::error('Error en ItinerarioEventoController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el listado de eventos del itinerario.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al obtener el listado de eventos del itinerario.');
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
            'id_rsv_reservas' => 'required|exists:rsv_reservas,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_hora_inicio' => 'required|date',
            'fecha_hora_fin' => 'required|date|after_or_equal:fecha_hora_inicio',
            'lugar' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $evento = DB::transaction(function () use ($validatedData) {
                return ItinerarioEvento::create($validatedData);
            });

            $evento->load('reserva:id,codigo_reserva');

            return response()->json([
                'success' => true,
                'message' => 'Evento de itinerario creado exitosamente.',
                'data' => $evento,
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en ItinerarioEventoController@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar el evento de itinerario.',
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
            $evento = ItinerarioEvento::with(['reserva'])->find($id);

            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'message' => 'El evento de itinerario solicitado no existe.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Evento de itinerario obtenido exitosamente.',
                'data' => $evento,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en ItinerarioEventoController@show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener el evento de itinerario.',
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
            $evento = ItinerarioEvento::find($id);

            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'message' => 'El evento de itinerario solicitado no existe.',
                ], 404);
            }

            $validatedData = $request->validate([
                'titulo' => 'sometimes|required|string|max:255',
                'descripcion' => 'nullable|string',
                'fecha_hora_inicio' => 'sometimes|required|date',
                'fecha_hora_fin' => 'sometimes|required|date|after_or_equal:fecha_hora_inicio',
                'lugar' => 'nullable|string|max:255',
            ]);

            DB::transaction(function () use ($evento, $validatedData) {
                $evento->update($validatedData);
            });

            $evento->load('reserva:id,codigo_reserva');

            return response()->json([
                'success' => true,
                'message' => 'Evento de itinerario actualizado exitosamente.',
                'data' => $evento,
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en ItinerarioEventoController@update: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar el evento de itinerario.',
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
            $evento = ItinerarioEvento::find($id);

            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'message' => 'El evento de itinerario solicitado no existe.',
                ], 404);
            }

            DB::transaction(function () use ($evento) {
                $evento->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Evento de itinerario eliminado exitosamente.',
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en ItinerarioEventoController@destroy: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar el evento de itinerario.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
