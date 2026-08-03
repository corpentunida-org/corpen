<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\HistorialEndoso;
use App\Models\Rsv\Reserva;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

class HistorialEndosoController extends Controller
{
    /**
     * Display a listing of the resource.
     * Soporte Dual (Web/JSON) con filtros y paginación.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = HistorialEndoso::with([
                'reserva:id,codigo_reserva,id_user',
                'userAnterior:id,name,email',
                'userNuevo:id,name,email',
                'autorizadoPor:id,name,email'
            ]);

            // Filtros de búsqueda
            if ($request->has('id_rsv_reservas')) {
                $query->where('id_rsv_reservas', $request->id_rsv_reservas);
            }

            if ($request->has('id_user_anterior')) {
                $query->where('id_user_anterior', $request->id_user_anterior);
            }

            if ($request->has('id_user_nuevo')) {
                $query->where('id_user_nuevo', $request->id_user_nuevo);
            }

            $perPage = $request->get('per_page', 15);
            $endosos = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Si es petición AJAX o API, devolvemos JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Historial de endosos obtenido exitosamente.',
                    'data' => $endosos,
                ], 200);
            }

            // Si es navegación web tradicional, renderizamos la vista Blade
            return view('rsv.endosos.index', compact('endosos'));

        } catch (Throwable $e) {
            Log::error('Error en HistorialEndosoController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el historial de endosos.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al obtener el historial de endosos.');
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
     * Al registrar un endoso, se transfiere la titularidad de la reserva de forma segura.
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'id_rsv_reservas' => 'required|exists:rsv_reservas,id',
            'id_user_nuevo' => 'required|integer',
            'id_user_autorizado_por' => 'required|integer',
            'motivo_endoso' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            // Validar la reserva y bloquearla para evitar condiciones de carrera (Race Conditions)
            $reserva = Reserva::lockForUpdate()->find($validatedData['id_rsv_reservas']);

            if (!$reserva) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reserva especificada no existe.',
                ], 404);
            }

            if ($reserva->id_user == $validatedData['id_user_nuevo']) {
                return response()->json([
                    'success' => false,
                    'message' => 'El nuevo usuario no puede ser el mismo titular actual.',
                ], 422);
            }

            // Inyectar el titular actual como el usuario anterior
            $validatedData['id_user_anterior'] = $reserva->id_user;

            // 1. Crear el registro del historial de endoso
            $endoso = HistorialEndoso::create($validatedData);

            // 2. Actualizar la titularidad de la reserva principal
            $reserva->update([
                'id_user' => $validatedData['id_user_nuevo']
            ]);

            DB::commit();

            $endoso->load(['reserva', 'userAnterior', 'userNuevo', 'autorizadoPor']);

            return response()->json([
                'success' => true,
                'message' => 'Endoso registrado y titularidad transferida exitosamente.',
                'data' => $endoso,
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en HistorialEndosoController@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar el endoso.',
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
            $endoso = HistorialEndoso::with([
                'reserva',
                'userAnterior:id,name,email',
                'userNuevo:id,name,email',
                'autorizadoPor:id,name,email'
            ])->find($id);

            if (!$endoso) {
                return response()->json([
                    'success' => false,
                    'message' => 'El registro de endoso solicitado no existe.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detalle de endoso obtenido exitosamente.',
                'data' => $endoso,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en HistorialEndosoController@show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener el registro de endoso.',
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
     * Permite actualizar únicamente el motivo (para correcciones menores), manteniendo la inmutabilidad de los actores.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validatedData = $request->validate([
            'motivo_endoso' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $endoso = HistorialEndoso::find($id);

            if (!$endoso) {
                return response()->json([
                    'success' => false,
                    'message' => 'El registro de endoso solicitado no existe.',
                ], 404);
            }

            $endoso->update($validatedData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Motivo de endoso actualizado exitosamente.',
                'data' => $endoso,
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en HistorialEndosoController@update: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar el registro de endoso.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Los historiales de auditoría y endosos son estrictamente inmutables.
     */
    public function destroy(string $id): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Operación denegada. Los registros históricos de endosos no pueden ser eliminados.',
        ], 403);
    }
}
