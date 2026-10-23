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
            if ($request->has('search') && !empty($request->search)) {
                $query->where('name', 'like', "%{$request->search}%");
            }
            if ($request->has('active')) {
                $query->where('active', filter_var($request->active, FILTER_VALIDATE_BOOLEAN));
            }
            $origenes = $query->orderBy('name', 'asc')->paginate($request->get('per_page', 15));

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'data' => $origenes], 200);
            }
            return view('rsv.origen-reservas.index', compact('origenes'));
        } catch (Throwable $e) {
            Log::error('Error Index Origen: ' . $e->getMessage());
            return $request->wantsJson() || $request->ajax() 
                ? response()->json(['success' => false], 500) 
                : back()->with('error', 'Error al obtener orígenes.');
        }
    }

    public function store(StoreOrigenReservaRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $origen = DB::transaction(fn() => OrigenReserva::create($request->validated()));
            return response()->json(['success' => true, 'data' => $origen], 201);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error Store Origen: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        $origen = OrigenReserva::find($id);
        if (!$origen) return response()->json(['success' => false], 404);
        return response()->json(['success' => true, 'data' => $origen], 200);
    }

    public function update(UpdateOrigenReservaRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $origen = OrigenReserva::find($id);
            if (!$origen) return response()->json(['success' => false], 404);

            DB::transaction(fn() => $origen->update($request->validated()));
            return response()->json(['success' => true, 'data' => $origen], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error Update Origen: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $origen = OrigenReserva::find($id);
            if (!$origen) return response()->json(['success' => false], 404);

            DB::transaction(fn() => $origen->delete()); // Eliminación lógica
            return response()->json(['success' => true], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error Destroy Origen: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }
}