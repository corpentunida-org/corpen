<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\Reserva;
use App\Models\Rsv\HistorialEstado;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     * Soporte Dual (Web/JSON) con filtros de búsqueda, relaciones y paginación.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = Reserva::with([
                'inmueble',
                'user',
                'status',
                'origenReserva',
                'huespedes',
                'transacciones',
            ]);

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('codigo_reserva', 'like', "%{$search}%")
                      ->orWhere('comentario_reserva', 'like', "%{$search}%");
                });
            }

            if ($request->has('id_rsv_statuses')) {
                $query->where('id_rsv_statuses', $request->id_rsv_statuses);
            }

            if ($request->has('id_rsv_catalogo_inmueble')) {
                $query->where('id_rsv_catalogo_inmueble', $request->id_rsv_catalogo_inmueble);
            }

            $perPage = $request->get('per_page', 15);
            $reservas = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Si la petición es por API o AJAX, devolvemos JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Listado de reservas obtenido exitosamente.',
                    'data' => $reservas,
                ], 200);
            }

            // Si es navegación web tradicional, renderizamos la vista Blade del módulo
            return view('rsv.reservas.index', compact('reservas'));

        } catch (Throwable $e) {
            Log::error('Error en ReservaController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el listado de reservas.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al obtener el listado de reservas.');
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
            'id_user' => 'required|integer',
            'id_rsv_statuses' => 'required|exists:rsv_statuses,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'monto_total' => 'required|numeric|min:0',
            'id_rsv_origen_reservas' => 'required|exists:rsv_origen_reservas,id',
            'comentario_reserva' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $reserva = DB::transaction(function () use ($validatedData) {
                $validatedData['codigo_reserva'] = 'RSV-' . strtoupper(uniqid());

                $reserva = Reserva::create($validatedData);

                // Registrar automáticamente el estado inicial
                HistorialEstado::create([
                    'id_rsv_reservas' => $reserva->id,
                    'id_rsv_statuses_anterior' => null,
                    'id_rsv_status_nuevo' => $reserva->id_rsv_statuses,
                    'id_user' => Auth::id() ?? $reserva->id_user,
                    'comentario' => 'Creación inicial de la reserva.',
                ]);

                return $reserva;
            });

            $reserva->load(['inmueble', 'user', 'status', 'origenReserva']);

            return response()->json([
                'success' => true,
                'message' => 'Reserva creada exitosamente.',
                'data' => $reserva,
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en ReservaController@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar la reserva.',
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
            $reserva = Reserva::with([
                'inmueble',
                'user',
                'status',
                'origenReserva',
                'huespedes',
                'transacciones.pasarela',
                'itinerarios',
                'mensajes',
                'reviews',
                'historialEstados',
                'historialEndosos',
            ])->find($id);

            if (!$reserva) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reserva solicitada no existe.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Reserva obtenida exitosamente.',
                'data' => $reserva,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en ReservaController@show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener la reserva.',
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
            $reserva = Reserva::find($id);

            if (!$reserva) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reserva solicitada no existe.',
                ], 404);
            }

            $validatedData = $request->validate([
                'id_rsv_catalogo_inmueble' => 'sometimes|required|exists:rsv_catalogo_inmueble,id',
                'id_user' => 'sometimes|required|integer',
                'id_rsv_statuses' => 'sometimes|required|exists:rsv_statuses,id',
                'fecha_inicio' => 'sometimes|required|date',
                'fecha_fin' => 'sometimes|required|date|after:fecha_inicio',
                'monto_total' => 'sometimes|required|numeric|min:0',
                'id_rsv_origen_reservas' => 'sometimes|required|exists:rsv_origen_reservas,id',
                'comentario_reserva' => 'nullable|string',
            ]);

            DB::transaction(function () use ($reserva, $validatedData) {
                $estadoAnterior = $reserva->id_rsv_statuses;
                $nuevoEstado = $validatedData['id_rsv_statuses'] ?? $estadoAnterior;

                $reserva->update($validatedData);

                // Registrar en historial si el estado cambió
                if ($estadoAnterior !== $nuevoEstado) {
                    HistorialEstado::create([
                        'id_rsv_reservas' => $reserva->id,
                        'id_rsv_statuses_anterior' => $estadoAnterior,
                        'id_rsv_status_nuevo' => $nuevoEstado,
                        'id_user' => Auth::id() ?? $reserva->id_user,
                        'comentario' => 'Actualización de estado en modificación general de reserva.',
                    ]);
                }
            });

            $reserva->load(['inmueble', 'user', 'status', 'origenReserva']);

            return response()->json([
                'success' => true,
                'message' => 'Reserva actualizada exitosamente.',
                'data' => $reserva,
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en ReservaController@update: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar la reserva.',
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
            $reserva = Reserva::find($id);

            if (!$reserva) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reserva solicitada no existe.',
                ], 404);
            }

            DB::transaction(function () use ($reserva) {
                $reserva->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Reserva eliminada exitosamente.',
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en ReservaController@destroy: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar la reserva.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Cambiar el estado de la reserva de forma específica y justificada.
     */
    public function cambiarEstado(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $reserva = Reserva::find($id);

            if (!$reserva) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reserva solicitada no existe.',
                ], 404);
            }

            $validatedData = $request->validate([
                'id_rsv_statuses' => 'required|exists:rsv_statuses,id',
                'comentario' => 'nullable|string',
            ]);

            $estadoAnterior = $reserva->id_rsv_statuses;
            $nuevoEstado = $validatedData['id_rsv_statuses'];

            if ($estadoAnterior === $nuevoEstado) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reserva ya se encuentra en el estado indicado.',
                ], 422);
            }

            DB::transaction(function () use ($reserva, $estadoAnterior, $nuevoEstado, $validatedData) {
                $reserva->update(['id_rsv_statuses' => $nuevoEstado]);

                HistorialEstado::create([
                    'id_rsv_reservas' => $reserva->id,
                    'id_rsv_statuses_anterior' => $estadoAnterior,
                    'id_rsv_status_nuevo' => $nuevoEstado,
                    'id_user' => Auth::id() ?? $reserva->id_user,
                    'comentario' => $validatedData['comentario'] ?? 'Cambio manual de estado.',
                ]);
            });

            $reserva->load(['status']);

            return response()->json([
                'success' => true,
                'message' => 'Estado de la reserva cambiado exitosamente.',
                'data' => $reserva,
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en ReservaController@cambiarEstado: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al cambiar el estado de la reserva.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
