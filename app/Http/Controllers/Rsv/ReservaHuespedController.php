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
        /* ══════════════════════════════════════════════════════════════════
           RH-FIX · BAJA LÓGICA GARANTIZADA Y VERIFICADA (deleted_at)
           ────────────────────────────────────────────────────────────────
           SÍNTOMA REAL (panel DBG): "Operación exitosa" + "eliminados
           lógicos en BD: 0" + el huésped seguía apareciendo en la tabla.
           CAUSA: la versión anterior abría DB::beginTransaction() y NUNCA
           llamaba DB::commit(). El DB::transaction() interno solo crea un
           SAVEPOINT dentro de esa transacción de nivel 1, que quedaba
           ABIERTA; al terminar el request la conexión se cierra y MySQL
           REVIERTE todo → el UPDATE de deleted_at se deshacía en silencio
           después de que el 200 ya había salido al navegador.
           AHORA: UNA sola transacción que SÍ se confirma, con verificación
           dura sobre la tabla. Y si el modelo perdiera el trait
           SoftDeletes, se aplica la baja lógica DIRECTA sobre la tabla
           (la columna deleted_at ya existe en la base de datos). Nunca
           un DELETE físico.
           ══════════════════════════════════════════════════════════════════ */
        try {
            $huesped = ReservaHuesped::find($id);
            if (!$huesped) {
                return response()->json(['success' => false, 'message' => 'Huésped no encontrado.'], 404);
            }

            $usaSoftDeletes = in_array(
                \Illuminate\Database\Eloquent\SoftDeletes::class,
                class_uses_recursive(ReservaHuesped::class)
            );
            $via = $usaSoftDeletes ? 'eloquent (SoftDeletes)' : 'db-directa';

            DB::transaction(function () use ($huesped, $usaSoftDeletes, &$via) {
                if ($usaSoftDeletes) {
                    $huesped->delete(); // SoftDeletes → UPDATE deleted_at (baja LÓGICA)
                    /* Cinturón y tirantes: si un evento deleting u otra causa
                       abortara el soft delete, se fuerza el UPDATE directo
                       para que el requisito se cumpla SIEMPRE. */
                    if (!DB::table('rsv_reserva_huespedes')->where('id', $huesped->id)->whereNotNull('deleted_at')->exists()) {
                        $via = 'db-directa (forzada tras eloquent)';
                        DB::table('rsv_reserva_huespedes')
                            ->where('id', $huesped->id)
                            ->whereNull('deleted_at')
                            ->update(['deleted_at' => now()]);
                    }
                } else {
                    /* El modelo NO tiene el trait: baja lógica DIRECTA en la
                       tabla. Jamás un DELETE físico. */
                    DB::table('rsv_reserva_huespedes')
                        ->where('id', $huesped->id)
                        ->whereNull('deleted_at')
                        ->update(['deleted_at' => now()]);
                }
            });

            /* Verificación DURA fuera de la transacción: success solo si la
               fila realmente quedó con deleted_at (el panel DBG no miente). */
            $verificada = (bool) DB::table('rsv_reserva_huespedes')
                ->where('id', $id)
                ->whereNotNull('deleted_at')
                ->exists();

            Log::info('RH-DESTROY baja lógica', ['id' => $id, 'via' => $via, 'verificada' => $verificada]);

            return response()->json([
                'success' => $verificada,
                'logico'  => $verificada,
                'via'     => $via,
                'message' => $verificada
                    ? 'Baja LÓGICA verificada: la fila queda con deleted_at (no se borra físicamente).'
                    : 'No se pudo aplicar la baja lógica (revisa laravel.log).',
            ], $verificada ? 200 : 500);
        } catch (Throwable $e) {
            Log::error('Error Destroy ReservaHuesped: ' . $e->getMessage(), [
                'en' => $e->getFile() . '@' . $e->getLine(),
            ]);
            return response()->json(['success' => false, 'message' => 'No se pudo eliminar el huésped.'], 500);
        }
    }
}
