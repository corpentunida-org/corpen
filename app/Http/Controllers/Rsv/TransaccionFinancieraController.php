<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\TransaccionFinanciera;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class TransaccionFinancieraController extends Controller
{
    /**
     * Listado de transacciones con filtros y paginación.
     * Compatible con web y JSON.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = TransaccionFinanciera::with(['reserva', 'pasarela']);

            if ($request->filled('id_rsv_reservas')) {
                $query->where(
                    'id_rsv_reservas',
                    $request->id_rsv_reservas
                );
            }

            if ($request->filled('id_rsv_pasarela')) {
                $query->where(
                    'id_rsv_pasarela',
                    $request->id_rsv_pasarela
                );
            }

            if ($request->filled('estado_pago')) {
                $query->where('estado_pago', $request->estado_pago);
            }

            if ($request->filled('metodo_pago')) {
                $query->where('metodo_pago', $request->metodo_pago);
            }

            if ($request->filled('search')) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where(
                        'referencia_externa',
                        'like',
                        "%{$search}%"
                    )->orWhere(
                        'metodo_pago',
                        'like',
                        "%{$search}%"
                    );
                });
            }

            $perPage = max(
                1,
                min((int) $request->input('per_page', 15), 100)
            );

            $transacciones = $query
                ->orderByDesc('created_at')
                ->paginate($perPage)
                ->withQueryString();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Listado de transacciones financieras obtenido exitosamente.',
                    'data' => $transacciones,
                ], 200);
            }

            return view(
                'rsv.transacciones-financieras.index',
                compact('transacciones')
            );

        } catch (Throwable $e) {
            Log::error(
                'Error en TransaccionFinancieraController@index: '
                . $e->getMessage(),
                ['trace' => $e->getTraceAsString()]
            );

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el listado de transacciones financieras.',
                    'error' => config('app.debug')
                        ? $e->getMessage()
                        : null,
                ], 500);
            }

            return back()->with(
                'error',
                'Ocurrió un error al obtener el listado de transacciones financieras.'
            );
        }
    }

    /**
     * Creación de formularios no implementada.
     */
    public function create(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Método no implementado para API REST.',
        ], 405);
    }

    /**
     * Registrar una transacción financiera.
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'id_rsv_reservas' => [
                'required',
                'exists:rsv_reservas,id',
            ],
            'id_rsv_pasarela' => [
                'nullable',
                'exists:rsv_pasarelas,id',
            ],
            'monto' => 'required|numeric|min:0',
            'moneda' => 'nullable|string|max:10',
            'estado_pago' => 'nullable|string|max:50',
            'metodo_pago' => 'nullable|string|max:100',
            'referencia_externa' => 'nullable|string|max:255',
            'soporte_pago' => 'nullable|string|max:2048',
        ]);

        try {
            $transaccion = DB::transaction(function () use ($validatedData) {
                $transaccion = TransaccionFinanciera::create(
                    $validatedData
                );

                $transaccion->load(['reserva', 'pasarela']);

                return $transaccion;
            });

            return response()->json([
                'success' => true,
                'message' => 'Transacción financiera registrada exitosamente.',
                'data' => $transaccion,
            ], 201);

        } catch (Throwable $e) {
            Log::error(
                'Error en TransaccionFinancieraController@store: '
                . $e->getMessage(),
                ['trace' => $e->getTraceAsString()]
            );

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar la transacción financiera.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null,
            ], 500);
        }
    }

    /**
     * Consultar una transacción financiera.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $transaccion = TransaccionFinanciera::with([
                'reserva',
                'pasarela',
            ])->find($id);

            if (!$transaccion) {
                return response()->json([
                    'success' => false,
                    'message' => 'La transacción financiera solicitada no existe.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Transacción financiera obtenida exitosamente.',
                'data' => $transaccion,
            ], 200);

        } catch (Throwable $e) {
            Log::error(
                'Error en TransaccionFinancieraController@show: '
                . $e->getMessage(),
                ['trace' => $e->getTraceAsString()]
            );

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener la transacción financiera.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null,
            ], 500);
        }
    }

    /**
     * Edición de formularios no implementada.
     */
    public function edit(string $id): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Método no implementado para API REST.',
        ], 405);
    }

    /**
     * Actualizar una transacción financiera.
     */
    public function update(
        Request $request,
        string $id
    ): JsonResponse {
        $validatedData = $request->validate([
            'id_rsv_reservas' => [
                'sometimes',
                'required',
                'exists:rsv_reservas,id',
            ],
            'id_rsv_pasarela' => [
                'sometimes',
                'nullable',
                'exists:rsv_pasarelas,id',
            ],
            'monto' => 'sometimes|required|numeric|min:0',
            'moneda' => 'sometimes|nullable|string|max:10',
            'estado_pago' => 'sometimes|nullable|string|max:50',
            'metodo_pago' => 'sometimes|nullable|string|max:100',
            'referencia_externa' => 'sometimes|nullable|string|max:255',
            'soporte_pago' => 'sometimes|nullable|string|max:2048',
        ]);

        try {
            $transaccion = TransaccionFinanciera::find($id);

            if (!$transaccion) {
                return response()->json([
                    'success' => false,
                    'message' => 'La transacción financiera solicitada no existe.',
                ], 404);
            }

            DB::transaction(function () use (
                $transaccion,
                $validatedData
            ) {
                $transaccion->update($validatedData);
            });

            $transaccion->load(['reserva', 'pasarela']);

            return response()->json([
                'success' => true,
                'message' => 'Transacción financiera actualizada exitosamente.',
                'data' => $transaccion,
            ], 200);

        } catch (Throwable $e) {
            Log::error(
                'Error en TransaccionFinancieraController@update: '
                . $e->getMessage(),
                ['trace' => $e->getTraceAsString()]
            );

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar la transacción financiera.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null,
            ], 500);
        }
    }

    /**
     * Eliminar una transacción financiera.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $transaccion = TransaccionFinanciera::find($id);

            if (!$transaccion) {
                return response()->json([
                    'success' => false,
                    'message' => 'La transacción financiera solicitada no existe.',
                ], 404);
            }

            DB::transaction(function () use ($transaccion) {
                $transaccion->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Transacción financiera eliminada exitosamente.',
            ], 200);

        } catch (Throwable $e) {
            Log::error(
                'Error en TransaccionFinancieraController@destroy: '
                . $e->getMessage(),
                ['trace' => $e->getTraceAsString()]
            );

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar la transacción financiera.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null,
            ], 500);
        }
    }
}
