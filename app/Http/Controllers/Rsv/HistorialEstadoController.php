<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\HistorialEstado;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class HistorialEstadoController extends Controller
{
    /**
     * Display a listing of the resource.
     * Soporte Dual (Web/JSON) con filtros y paginación.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = HistorialEstado::with([
                'reserva:id,codigo_reserva',
                'estadoAnterior:id,name',
                'estadoNuevo:id,name',
                'user:id,name,email'
            ]);

            if ($request->has('id_rsv_reservas')) {
                $query->where('id_rsv_reservas', $request->id_rsv_reservas);
            }

            if ($request->has('id_user')) {
                $query->where('id_user', $request->id_user);
            }

            if ($request->has('id_rsv_statuses_anterior')) {
                $query->where('id_rsv_statuses_anterior', $request->id_rsv_statuses_anterior);
            }

            if ($request->has('id_rsv_status_nuevo')) {
                $query->where('id_rsv_status_nuevo', $request->id_rsv_status_nuevo);
            }

            $perPage = $request->get('per_page', 15);
            $historial = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Si es petición por API o AJAX, devolvemos JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Historial de estados obtenido exitosamente.',
                    'data' => $historial,
                ], 200);
            }

            // Si es navegación web tradicional, renderizamos la vista Blade
            return view('rsv.historial-estados.index', compact('historial'));

        } catch (Throwable $e) {
            Log::error('Error en HistorialEstadoController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el historial de estados.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al obtener el historial de estados.');
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
     * Nota: La creación de este historial suele ser automática al cambiar el estado en ReservaController.
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'id_rsv_reservas' => 'required|exists:rsv_reservas,id',
            'id_rsv_statuses_anterior' => 'nullable|exists:rsv_statuses,id',
            'id_rsv_status_nuevo' => 'required|exists:rsv_statuses,id',
            'id_user' => 'required|integer',
            'comentario' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $historial = DB::transaction(function () use ($validatedData) {
                return HistorialEstado::create($validatedData);
            });

            $historial->load(['reserva', 'estadoAnterior', 'estadoNuevo', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Registro de historial de estado creado exitosamente.',
                'data' => $historial,
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en HistorialEstadoController@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar el historial de estado.',
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
            $historial = HistorialEstado::with([
                'reserva',
                'estadoAnterior',
                'estadoNuevo',
                'user'
            ])->find($id);

            if (!$historial) {
                return response()->json([
                    'success' => false,
                    'message' => 'El registro de historial solicitado no existe.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detalle del historial de estado obtenido exitosamente.',
                'data' => $historial,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en HistorialEstadoController@show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener el registro de historial.',
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
     * Regla de negocio: La tabla rsv_historial_estados es Append-Only. No se permiten actualizaciones.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Operación denegada. Los registros de auditoría e historial son inmutables (Append-Only) y no pueden ser modificados.',
        ], 403);
    }

    /**
     * Remove the specified resource from storage.
     * Regla de negocio: La tabla rsv_historial_estados es Append-Only. No se permiten eliminaciones.
     */
    public function destroy(string $id): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Operación denegada. Los registros de auditoría e historial son inmutables (Append-Only) y no pueden ser eliminados.',
        ], 403);
    }
}
