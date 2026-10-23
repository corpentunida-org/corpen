<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\ReservaHuesped;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Rsv\StoreReservaHuespedRequest;
use App\Http\Requests\Rsv\UpdateReservaHuespedRequest;
use Throwable;

class ReservaHuespedController extends Controller
{
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = ReservaHuesped::query();

            if ($request->has('id_rsv_reservas')) {
                $query->where('id_rsv_reservas', $request->id_rsv_reservas);
            }
            if ($request->has('es_principal')) {
                $query->where('es_principal', filter_var($request->es_principal, FILTER_VALIDATE_BOOLEAN));
            }
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nombres', 'like', "%{$search}%")
                      ->orWhere('apellidos', 'like', "%{$search}%")
                      ->orWhere('numero_documento', 'like', "%{$search}%");
                });
            }

            $huespedes = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'data' => $huespedes], 200);
            }

            return view('rsv.huespedes.index', compact('huespedes'));
        } catch (Throwable $e) {
            Log::error('Error Index ReservaHuesped: ' . $e->getMessage());
            return $request->wantsJson() || $request->ajax() 
                ? response()->json(['success' => false], 500) 
                : back()->with('error', 'Error al obtener huéspedes.');
        }
    }

    public function store(StoreReservaHuespedRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            
            $huesped = DB::transaction(function () use ($request, $validated) {
                return ReservaHuesped::create([
                    'id_rsv_reservas'     => $validated['id_rsv_reservas'],
                    'id_user_registrador' => Auth::id() ?? $request->user()?->id,
                    'nombre'              => $validated['nombre'] ?? $validated['nombres'] ?? null,
                    'apellidos'           => $validated['apellidos'],
                    'tipo_documento'      => $validated['tipo_documento'] ?? null,
                    'numero_documento'    => $validated['numero_documento'] ?? null,
                    'es_titular'          => $request->boolean('es_titular', $request->boolean('es_principal', false)),
                ]);
            });

            return response()->json(['success' => true, 'data' => $huesped], 201);
        } catch (Throwable $e) {
            Log::error('Error Store ReservaHuesped: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        $huesped = ReservaHuesped::find($id);
        if (!$huesped) return response()->json(['success' => false], 404);
        return response()->json(['success' => true, 'data' => $huesped], 200);
    }

    public function update(UpdateReservaHuespedRequest $request, string $id): JsonResponse
    {
        try {
            $huesped = ReservaHuesped::find($id);
            if (!$huesped) return response()->json(['success' => false], 404);

            $validated = $request->validated();
            $datosActualizar = [];

            if (array_key_exists('nombre', $validated) || array_key_exists('nombres', $validated)) {
                $datosActualizar['nombre'] = $validated['nombre'] ?? $validated['nombres'] ?? null;
            }
            if (array_key_exists('apellidos', $validated)) $datosActualizar['apellidos'] = $validated['apellidos'];
            if (array_key_exists('tipo_documento', $validated)) $datosActualizar['tipo_documento'] = $validated['tipo_documento'];
            if (array_key_exists('numero_documento', $validated)) $datosActualizar['numero_documento'] = $validated['numero_documento'];
            
            if ($request->has('es_titular') || $request->has('es_principal')) {
                $datosActualizar['es_titular'] = $request->boolean('es_titular', $request->boolean('es_principal'));
            }

            DB::transaction(fn() => $huesped->update($datosActualizar));

            return response()->json(['success' => true, 'data' => $huesped->fresh()], 200);
        } catch (Throwable $e) {
            Log::error('Error Update ReservaHuesped: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $huesped = ReservaHuesped::find($id);
            if (!$huesped) return response()->json(['success' => false], 404);

            DB::transaction(fn() => $huesped->delete()); // Eliminación lógica
            return response()->json(['success' => true], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error Destroy ReservaHuesped: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }
}