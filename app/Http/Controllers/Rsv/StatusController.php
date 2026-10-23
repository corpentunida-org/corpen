<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\Status;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;
use App\Http\Requests\Rsv\StoreStatusRequest;
use App\Http\Requests\Rsv\UpdateStatusRequest;

class StatusController extends Controller
{
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = Status::query();
            if ($request->has('search') && !empty($request->search)) {
                $query->where('name', 'like', "%{$request->search}%");
            }
            $statuses = $query->orderBy('name', 'asc')->paginate($request->get('per_page', 15));

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'data' => $statuses], 200);
            }
            return view('rsv.statuses.index', compact('statuses'));
        } catch (Throwable $e) {
            Log::error('Error Index Status: ' . $e->getMessage());
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false], 500)
                : back()->with('error', 'Error al obtener estados.');
        }
    }

    public function store(StoreStatusRequest $request): JsonResponse|RedirectResponse
    {
        $esWeb = !($request->wantsJson() || $request->ajax());
        DB::beginTransaction();
        try {
            $status = DB::transaction(fn() => Status::create($request->validated()));
            return $esWeb
                ? back()->with('success', 'Estado creado exitosamente.')
                : response()->json(['success' => true, 'data' => $status], 201);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error Store Status: ' . $e->getMessage());
            return $esWeb ? back()->with('error', 'Error.')->withInput() : response()->json(['success' => false], 500);
        }
    }

    public function show(string $rsvStatus): JsonResponse
    {
        $status = Status::find($rsvStatus);
        if (!$status) return response()->json(['success' => false], 404);
        return response()->json(['success' => true, 'data' => $status], 200);
    }

    public function update(UpdateStatusRequest $request, string $rsvStatus): JsonResponse|RedirectResponse
    {
        $esWeb = !($request->wantsJson() || $request->ajax());
        DB::beginTransaction();
        try {
            $status = Status::find($rsvStatus);
            if (!$status) {
                return $esWeb ? back()->with('error', 'No existe.') : response()->json(['success' => false], 404);
            }

            DB::transaction(fn() => $status->update($request->validated()));

            return $esWeb
                ? back()->with('success', 'Actualizado exitosamente.')
                : response()->json(['success' => true, 'data' => $status], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error Update Status: ' . $e->getMessage());
            return $esWeb ? back()->with('error', 'Error.')->withInput() : response()->json(['success' => false], 500);
        }
    }

    public function destroy(Request $request, string $rsvStatus): JsonResponse|RedirectResponse
    {
        $esWeb = !($request->wantsJson() || $request->ajax());
        DB::beginTransaction();
        try {
            $status = Status::find($rsvStatus);
            if (!$status) {
                return $esWeb ? back()->with('error', 'No existe.') : response()->json(['success' => false], 404);
            }

            if ($status->reservas()->exists()) {
                return $esWeb
                    ? back()->with('error', "No se puede eliminar, tiene reservas.")
                    : response()->json(['success' => false, 'message' => 'Tiene reservas asociadas.'], 422);
            }

            DB::transaction(fn() => $status->delete()); // Eliminación lógica

            return $esWeb
                ? back()->with('success', 'Eliminado lógicamente.')
                : response()->json(['success' => true], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error Destroy Status: ' . $e->getMessage());
            return $esWeb ? back()->with('error', 'Error.') : response()->json(['success' => false], 500);
        }
    }
}