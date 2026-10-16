<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rsv\StoreReservaRequest;
use App\Http\Requests\Rsv\UpdateReservaRequest;
use App\Models\Rsv\HistorialEstado;
use App\Models\Rsv\Reserva;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     * Soporte Dual (Web/AJAX/JSON) con filtros de búsqueda, fechas, relaciones y paginación.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            // 1. Relaciones optimizadas
            $query = Reserva::with([
                'inmueble',
                'user',
                'status',
                'origen',
                'huespedes',
                'transacciones',
                'historialEndosos',
            ]);

            // 2. Filtro de Búsqueda
            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('codigo_reserva', 'like', "%{$search}%")
                      ->orWhere('comentario_reserva', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($u) use ($search) {
                          $u->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                      })
                      ->orWhereHas('inmueble', function ($i) use ($search) {
                          $i->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // 3. Filtros Exactos
            if ($request->filled('id_rsv_statuses')) {
                $query->where('id_rsv_statuses', $request->id_rsv_statuses);
            }

            if ($request->filled('id_rsv_catalogo_inmueble')) {
                $query->where('id_rsv_catalogo_inmueble', $request->id_rsv_catalogo_inmueble);
            }

            // 4. Filtros de Fechas para el Calendario
            if ($request->filled('fecha_desde')) {
                $query->whereDate('fecha_inicio', '>=', $request->fecha_desde);
            }

            if ($request->filled('fecha_hasta')) {
                $query->whereDate('fecha_fin', '<=', $request->fecha_hasta);
            }

            // Paginación dinámica
            $perPage = $request->get('per_page', 15);
            $reservas = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // 5. Respuesta JSON para el Calendario / API REST
            if ($request->wantsJson() && !$request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $reservas->items(),
                ], 200);
            }

            // Respuesta AJAX (Tablas Blade)
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('rsv.reservas.partials.tabla-registros', compact('reservas'))->render(),
                ], 200);
            }

            // Respuesta Vista Tradicional
            return view('rsv.reservas.index', compact('reservas'));

        } catch (Throwable $e) {
            Log::error('Error en ReservaController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al cargar las reservas.');
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
    public function store(StoreReservaRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $reserva = DB::transaction(function () use ($validatedData) {
                $validatedData['codigo_reserva'] = 'RSV-' . strtoupper(uniqid());

                $reserva = Reserva::create($validatedData);

                // Registrar automáticamente el estado inicial
                HistorialEstado::create([
                    'id_rsv_reservas'          => $reserva->id,
                    'id_rsv_statuses_anterior' => null,
                    'id_rsv_statuses_nuevo'      => $reserva->id_rsv_statuses,
                    'id_user'                  => Auth::id() ?? $reserva->id_user,
                    'comentario'               => 'Creación inicial de la reserva.',
                ]);

                return $reserva;
            });

            $reserva->load(['inmueble', 'user', 'status', 'origen']);

            return response()->json([
                'success' => true,
                'message' => 'Reserva creada exitosamente.',
                'data'    => $reserva,
            ], 201);

        } catch (Throwable $e) {
            Log::error('Error en ReservaController@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar la reserva.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
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
                'origen',
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
                'data'    => $reserva,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en ReservaController@show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener la reserva.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
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
    public function update(UpdateReservaRequest $request, string $id): JsonResponse
    {
        try {
            $reserva = Reserva::find($id);

            if (!$reserva) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reserva solicitada no existe.',
                ], 404);
            }

            $validatedData = $request->validated();

            DB::transaction(function () use ($reserva, $validatedData) {
                $estadoAnterior = $reserva->id_rsv_statuses;
                $nuevoEstado = $validatedData['id_rsv_statuses'] ?? $estadoAnterior;

                $reserva->update($validatedData);

                // Registrar en historial si el estado cambió
                if ($estadoAnterior !== $nuevoEstado) {
                    HistorialEstado::create([
                        'id_rsv_reservas'          => $reserva->id,
                        'id_rsv_statuses_anterior' => $estadoAnterior,
                        'id_rsv_statuses_nuevo'      => $nuevoEstado,
                        'id_user'                  => Auth::id() ?? $reserva->id_user,
                        'comentario'               => 'Actualización de estado en modificación general de reserva.',
                    ]);
                }
            });

            $reserva->load(['inmueble', 'user', 'status', 'origen']);

            return response()->json([
                'success' => true,
                'message' => 'Reserva actualizada exitosamente.',
                'data'    => $reserva,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en ReservaController@update: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar la reserva.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
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
            Log::error('Error en ReservaController@destroy: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar la reserva.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Cambiar el estado de la reserva de forma específica y justificada.
     */
    public function cambiarEstado(Request $request, string $id): JsonResponse
    {
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
                'comentario'      => 'nullable|string',
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
                    'id_rsv_reservas'          => $reserva->id,
                    'id_rsv_statuses_anterior' => $estadoAnterior,
                    'id_rsv_statuses_nuevo'      => $nuevoEstado,
                    'id_user'                  => Auth::id() ?? $reserva->id_user,
                    'comentario'               => $validatedData['comentario'] ?? 'Cambio manual de estado.',
                ]);
            });

            $reserva->load(['status']);

            return response()->json([
                'success' => true,
                'message' => 'Estado de la reserva cambiado exitosamente.',
                'data'    => $reserva,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en ReservaController@cambiarEstado: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al cambiar el estado de la reserva.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
