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
     * Display a listing of the resource.
     * Soporte Dual (Web/JSON) con filtros y paginación.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = TransaccionFinanciera::with(['reserva', 'pasarela']);

            if ($request->has('id_rsv_reservas')) {
                $query->where('id_rsv_reservas', $request->id_rsv_reservas);
            }

            if ($request->has('id_rsv_pasarelas')) {
                $query->where('id_rsv_pasarelas', $request->id_rsv_pasarelas);
            }

            if ($request->has('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('referencia', 'like', "%{$search}%")
                      ->orWhere('codigo_transaccion', 'like', "%{$search}%");
                });
            }

            $perPage = $request->get('per_page', 15);
            $transacciones = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Si la petición es por API o AJAX, devolvemos JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Listado de transacciones financieras obtenido exitosamente.',
                    'data' => $transacciones,
                ], 200);
            }

            // Si es navegación web tradicional, renderizamos la vista Blade del módulo
            return view('rsv.transacciones-financieras.index', compact('transacciones'));

        } catch (Throwable $e) {
            Log::error('Error en TransaccionFinancieraController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el listado de transacciones financieras.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al obtener el listado de transacciones financieras.');
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
            'id_rsv_pasarelas' => 'required|exists:rsv_pasarelas,id',
            'monto' => 'required|numeric|min:0',
            'moneda' => 'nullable|string|max:10',
            'estado' => 'nullable|string|max:50',
            'referencia' => 'nullable|string|max:255',
            'codigo_transaccion' => 'nullable|string|max:255',
            'detalles' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $transaccion = DB::transaction(function () use ($validatedData) {
                $trx = TransaccionFinanciera::create($validatedData);
                $trx->load(['reserva', 'pasarela']);
                return $trx;
            });

            return response()->json([
                'success' => true,
                'message' => 'Transacción financiera registrada exitosamente.',
                'data' => $transaccion,
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en TransaccionFinancieraController@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar la transacción financiera.',
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
            $transaccion = TransaccionFinanciera::with(['reserva', 'pasarela'])->find($id);

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
            Log::error('Error en TransaccionFinancieraController@show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener la transacción financiera.',
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
            $transaccion = TransaccionFinanciera::find($id);

            if (!$transaccion) {
                return response()->json([
                    'success' => false,
                    'message' => 'La transacción financiera solicitada no existe.',
                ], 404);
            }

            $validatedData = $request->validate([
                'id_rsv_reservas' => 'sometimes|required|exists:rsv_reservas,id',
                'id_rsv_pasarelas' => 'sometimes|required|exists:rsv_pasarelas,id',
                'monto' => 'sometimes|required|numeric|min:0',
                'moneda' => 'nullable|string|max:10',
                'estado' => 'nullable|string|max:50',
                'referencia' => 'nullable|string|max:255',
                'codigo_transaccion' => 'nullable|string|max:255',
                'detalles' => 'nullable|string',
            ]);

            DB::transaction(function () use ($transaccion, $validatedData) {
                $transaccion->update($validatedData);
            });

            $transaccion->load(['reserva', 'pasarela']);

            return response()->json([
                'success' => true,
                'message' => 'Transacción financiera actualizada exitosamente.',
                'data' => $transaccion,
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en TransaccionFinancieraController@update: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar la transacción financiera.',
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
            DB::rollBack();
            Log::error('Error en TransaccionFinancieraController@destroy: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar la transacción financiera.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
