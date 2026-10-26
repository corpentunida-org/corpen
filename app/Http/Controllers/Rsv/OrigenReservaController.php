<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\OrigenReserva;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;
use App\Http\Requests\Rsv\StoreOrigenReservaRequest;
use App\Http\Requests\Rsv\UpdateOrigenReservaRequest;

class OrigenReservaController extends Controller
{
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = OrigenReserva::query();
            
            // CORREGIDO: Usando 'nombre' en lugar de 'name'
            if ($request->has('search') && !empty($request->search)) {
                $query->where('nombre', 'like', "%{$request->search}%");
            }

            $origenes = $query->orderBy('nombre', 'asc')->paginate($request->get('per_page', 15));

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'data' => $origenes], 200);
            }
            
            return view('rsv.origen-reservas.index', compact('origenes'));
            
        } catch (Throwable $e) {
            Log::error('Error Index Origen: ' . $e->getMessage());
            return $request->wantsJson() || $request->ajax() 
                ? response()->json(['success' => false, 'message' => $e->getMessage()], 500) 
                : back()->with('error', 'Error al obtener orígenes.');
        }
    }

    public function store(StoreOrigenReservaRequest $request): JsonResponse
    {
        try {
            $origen = DB::transaction(function () use ($request) {
                return OrigenReserva::create($request->validated());
            });

            return response()->json(['success' => true, 'data' => $origen], 201);
            
        } catch (Throwable $e) {
            Log::error('Error Store Origen: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al guardar el origen.'], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        $origen = OrigenReserva::find($id);
        if (!$origen) return response()->json(['success' => false, 'message' => 'No encontrado'], 404);
        
        return response()->json(['success' => true, 'data' => $origen], 200);
    }

    public function update(UpdateOrigenReservaRequest $request, string $id): JsonResponse
    {
        try {
            $origen = OrigenReserva::find($id);
            if (!$origen) return response()->json(['success' => false, 'message' => 'No encontrado'], 404);

            DB::transaction(function () use ($origen, $request) {
                $origen->update($request->validated());
            });

            return response()->json(['success' => true, 'data' => $origen], 200);
            
        } catch (Throwable $e) {
            Log::error('Error Update Origen: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al actualizar el origen.'], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $origen = OrigenReserva::find($id);
            if (!$origen) return response()->json(['success' => false, 'message' => 'No encontrado'], 404);

            DB::transaction(function () use ($origen) {
                $origen->delete(); // Soft Delete
            });

            return response()->json(['success' => true], 200);
            
        } catch (Throwable $e) {
            Log::error('Error Destroy Origen: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al eliminar el origen.'], 500);
        }
    }
}