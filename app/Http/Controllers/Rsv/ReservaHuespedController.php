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
            $query = ReservaHuesped::query()
                ->with(['reserva:id,codigo_reserva,id_rsv_catalogo_inmueble', 'reserva.inmueble:id,name', 'registrador:id,name']);

            if ($request->has('id_rsv_reservas')) {
                $query->where('id_rsv_reservas', $request->id_rsv_reservas);
            }
            if ($request->has('es_titular') || $request->has('es_principal')) {
                $val = filter_var($request->get('es_titular', $request->get('es_principal')), FILTER_VALIDATE_BOOLEAN);
                $query->where('es_titular', $val);
            }
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('apellidos', 'like', "%{$search}%")
                      ->orWhere('numero_documento', 'like', "%{$search}%");
                });
            }

            // Si necesitas las reservas para el select del modal en la misma vista:
            $rhReservasOpt = collect();
            if (class_exists(\App\Models\Rsv\Reserva::class)) {
                $rhReservasOpt = \App\Models\Rsv\Reserva::query()->select('id', 'codigo_reserva')
                    ->orderBy('created_at', 'desc')->limit(500)->get()
                    ->map(fn ($r) => ['id' => $r->id, 'cod' => $r->codigo_reserva])->values();
            }

            $huespedes = $query->orderBy('created_at', 'desc')->get(); // O paginate si prefieres colección directa para el JS

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'data' => $huespedes], 200);
            }

            return view('rsv.huespedes.index', compact('huespedes', 'rhReservasOpt'));
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
        // ===== TEMPORAL: diagnóstico (quitar cuando funcione) =====
        Log::info('RH-UPDATE raw', ['id' => $id, 'payload' => $request->all()]);
        Log::info('RH-UPDATE validated', ['validated' => $request->validated()]);
        // ===========================================================

        try {
            $huesped = ReservaHuesped::find($id);
            if (!$huesped) return response()->json(['success' => false], 404);

            $validated = $request->validated();
            $datosActualizar = [];

            // Procesar el cambio de reserva
            if (!empty($validated['id_rsv_reservas'])) {
                $datosActualizar['id_rsv_reservas'] = (int) $validated['id_rsv_reservas'];
            }

            if (array_key_exists('nombre', $validated) || array_key_exists('nombres', $validated)) {
                $datosActualizar['nombre'] = $validated['nombre'] ?? $validated['nombres'] ?? null;
            }
            if (array_key_exists('apellidos', $validated)) $datosActualizar['apellidos'] = $validated['apellidos'];
            if (array_key_exists('tipo_documento', $validated)) $datosActualizar['tipo_documento'] = $validated['tipo_documento'];
            if (array_key_exists('numero_documento', $validated)) $datosActualizar['numero_documento'] = $validated['numero_documento'];

            if ($request->has('es_titular') || $request->has('es_principal')) {
                $datosActualizar['es_titular'] = $request->boolean('es_titular', $request->boolean('es_principal'));
            }

            // ===== TEMPORAL: qué se va a escribir realmente =====
            Log::info('RH-UPDATE datosActualizar', ['id' => $id, 'datos' => $datosActualizar]);
            // =====================================================

            if (empty($datosActualizar)) {
                return response()->json(['success' => false, 'message' => 'Nada que actualizar.'], 422);
            }

            DB::transaction(fn() => $huesped->update($datosActualizar));

            // Devolver con relaciones para que el JS pinte el código de reserva fresco
            return response()->json([
                'success' => true,
                'data'    => $huesped->fresh([
                    'reserva:id,codigo_reserva,id_rsv_catalogo_inmueble',
                    'reserva.inmueble:id,name',
                    'registrador:id,name',
                ]),
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error Update ReservaHuesped: ' . $e->getMessage(), [
                'en' => $e->getFile() . '@' . $e->getLine(),
            ]);
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