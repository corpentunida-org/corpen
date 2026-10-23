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
use App\Http\Requests\Rsv\StoreHistorialEstadoRequest;

class HistorialEstadoController extends Controller
{
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = HistorialEstado::with(['reserva:id,codigo_reserva', 'estadoAnterior:id,name', 'estadoNuevo:id,name', 'user:id,name,email']);
            
            if ($request->has('id_rsv_reservas')) $query->where('id_rsv_reservas', $request->id_rsv_reservas);
            if ($request->has('id_user')) $query->where('id_user', $request->id_user);
            if ($request->has('id_rsv_statuses_anterior')) $query->where('id_rsv_statuses_anterior', $request->id_rsv_statuses_anterior);
            if ($request->has('id_rsv_status_nuevo')) $query->where('id_rsv_status_nuevo', $request->id_rsv_status_nuevo);

            $historial = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'data' => $historial], 200);
            }
            return view('rsv.historial-estados.index', compact('historial'));
        } catch (Throwable $e) {
            Log::error('Error Index Historial: ' . $e->getMessage());
            return $request->wantsJson() || $request->ajax() 
                ? response()->json(['success' => false], 500) 
                : back()->with('error', 'Error al obtener historial.');
        }
    }

    public function store(StoreHistorialEstadoRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $historial = DB::transaction(fn() => HistorialEstado::create($request->validated()));
            $historial->load(['reserva', 'estadoAnterior', 'estadoNuevo', 'user']);
            return response()->json(['success' => true, 'data' => $historial], 201);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error Store Historial: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        $historial = HistorialEstado::with(['reserva', 'estadoAnterior', 'estadoNuevo', 'user'])->find($id);
        if (!$historial) return response()->json(['success' => false], 404);
        return response()->json(['success' => true, 'data' => $historial], 200);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        return response()->json(['success' => false, 'message' => 'El historial es inmutable y no se puede modificar.'], 403);
    }

    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $historial = HistorialEstado::find($id);
            if (!$historial) return response()->json(['success' => false], 404);

            DB::transaction(fn() => $historial->delete()); // Eliminación lógica
            return response()->json(['success' => true], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error Destroy Historial: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }
}