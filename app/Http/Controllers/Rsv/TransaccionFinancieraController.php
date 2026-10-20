<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\TransaccionFinanciera;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Throwable;

class TransaccionFinancieraController extends Controller
{
    /**
     * FIX 1364: la columna id_rsv_pasarela es NOT NULL sin default.
     * Cuando el formulario no envía pasarela ("— Directo —"), resolvemos
     * automáticamente una pasarela representativa del catálogo:
     *   1) Busca una llamada "Directo"/"Efectivo".
     *   2) Si no existe, la CREA automáticamente.
     *   3) Si falla la creación, toma la primera del catálogo.
     * Devuelve null solo si la tabla rsv_pasarelas está vacía e infutable.
     */
    private function resolverPasarelaDirecta(): ?int
    {
        try {
            if (!class_exists(\App\Models\Rsv\Pasarela::class)) {
                return null;
            }

            $col = Schema::hasColumn('rsv_pasarelas', 'name') ? 'name' : 'nombre';

            // 1) Buscar una pasarela "Directo" o "Efectivo"
            $pasarela = \App\Models\Rsv\Pasarela::query()
                ->where(function ($w) use ($col) {
                    $w->where($col, 'like', '%directo%')
                      ->orWhere($col, 'like', '%efectivo%');
                })
                ->orderBy('id')
                ->first();

            // 2) No existe → crearla (así "Directo" siempre está disponible)
            if (!$pasarela) {
                try {
                    $pasarela = \App\Models\Rsv\Pasarela::create([$col => 'Directo']);
                } catch (Throwable $eCreate) {
                    Log::warning('resolverPasarelaDirecta (create): ' . $eCreate->getMessage());
                    // 3) Fallback: primera del catálogo
                    $pasarela = \App\Models\Rsv\Pasarela::orderBy('id')->first();
                }
            }

            return $pasarela?->id;
        } catch (\Throwable $e) {
            Log::warning('resolverPasarelaDirecta: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Listado de transacciones con filtros y paginación.
     * Compatible con web y JSON.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = TransaccionFinanciera::with(['reserva', 'pasarela']);

            if ($request->filled('id_rsv_reservas')) {
                $query->where('id_rsv_reservas', $request->id_rsv_reservas);
            }

            if ($request->filled('id_rsv_pasarela')) {
                $query->where('id_rsv_pasarela', $request->id_rsv_pasarela);
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
                    $q->where('referencia_externa', 'like', "%{$search}%")
                      ->orWhere('metodo_pago', 'like', "%{$search}%");
                });
            }

            $perPage = max(1, min((int) $request->input('per_page', 15), 100));

            $transacciones = $query
                ->orderByDesc('created_at')
                ->paginate($perPage)
                ->withQueryString();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Listado de transacciones financieras obtenido exitosamente.',
                    'data'    => $transacciones,
                ], 200);
            }

            return view('rsv.transacciones-financieras.index', compact('transacciones'));

        } catch (Throwable $e) {
            Log::error('Error en TransaccionFinancieraController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el listado de transacciones financieras.',
                    'error'   => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al obtener el listado de transacciones financieras.');
        }
    }

    public function create(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Método no implementado para API REST.',
        ], 405);
    }

    /**
     * Registrar una transacción financiera.
     * FIX 1364: garantiza id_rsv_pasarela (resolución automática si viene vacío).
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'id_rsv_reservas'    => 'required|exists:rsv_reservas,id',
            'id_rsv_pasarela'    => 'nullable|exists:rsv_pasarelas,id',
            'monto'              => 'required|numeric|min:0',
            'moneda'             => 'nullable|string|max:10',
            'estado_pago'        => 'nullable|string|max:50',
            'metodo_pago'        => 'nullable|string|max:100',
            'referencia_externa' => 'nullable|string|max:255',
            'soporte_pago'       => 'nullable',
        ]);

        // Procesar archivo cargado si proviene de multipart/form-data
        if ($request->hasFile('soporte_pago')) {
            $request->validate([
                'soporte_pago' => 'file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            ]);
            $path = $request->file('soporte_pago')->store('soportes_pagos', 's3');
            $validatedData['soporte_pago'] = $path;
        }

        DB::beginTransaction();

        try {
            // ── FIX 1364: la columna es NOT NULL — resolvemos pasarela si viene vacía ──
            if (empty($validatedData['id_rsv_pasarela'])) {
                $idPasarela = $this->resolverPasarelaDirecta();

                if (!$idPasarela) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'No se puede registrar el pago: el catálogo rsv_pasarelas está vacío. Configure al menos una pasarela (ej. "Directo").',
                    ], 422);
                }
                $validatedData['id_rsv_pasarela'] = $idPasarela;
            }

            $transaccion = DB::transaction(function () use ($validatedData) {
                $transaccion = TransaccionFinanciera::create($validatedData);
                $transaccion->load(['reserva', 'pasarela']);

                return $transaccion;
            });

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transacción financiera registrada exitosamente.',
                'data'    => $transaccion,
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en TransaccionFinancieraController@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar la transacción financiera.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

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
                'data'    => $transaccion,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en TransaccionFinancieraController@show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener la transacción financiera.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function edit(string $id): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Método no implementado para API REST.',
        ], 405);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $validatedData = $request->validate([
            'id_rsv_reservas'    => 'sometimes|required|exists:rsv_reservas,id',
            'id_rsv_pasarela'    => 'sometimes|nullable|exists:rsv_pasarelas,id',
            'monto'              => 'sometimes|required|numeric|min:0',
            'moneda'             => 'sometimes|nullable|string|max:10',
            'estado_pago'        => 'sometimes|nullable|string|max:50',
            'metodo_pago'        => 'sometimes|nullable|string|max:100',
            'referencia_externa' => 'sometimes|nullable|string|max:255',
            'soporte_pago'       => 'sometimes|nullable',
        ]);

        if ($request->hasFile('soporte_pago')) {
            $request->validate([
                'soporte_pago' => 'file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            ]);
            $path = $request->file('soporte_pago')->store('soportes_pagos', 's3');
            $validatedData['soporte_pago'] = $path;
        }

        try {
            $transaccion = TransaccionFinanciera::find($id);

            if (!$transaccion) {
                return response()->json([
                    'success' => false,
                    'message' => 'La transacción financiera solicitada no existe.',
                ], 404);
            }

            DB::transaction(function () use ($transaccion, $validatedData) {
                $transaccion->update($validatedData);
            });

            $transaccion->load(['reserva', 'pasarela']);

            return response()->json([
                'success' => true,
                'message' => 'Transacción financiera actualizada exitosamente.',
                'data'    => $transaccion,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en TransaccionFinancieraController@update: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar la transacción financiera.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * ════════════════════════════════════════════════════════════════
     * ELIMINACIÓN LÓGICA (ANULACIÓN) — REGULA DE NEGOCIO:
     * · NUNCA se borra el registro.
     * · NUNCA se borra el soporte de S3: permanece visible/auditable.
     * · Se marca estado_pago = 'Anulado' (excluido del balance).
     * El endpoint se mantiene para compatibilidad con consumidores API,
     * pero su efecto es la anulación, no la destrucción.
     * ════════════════════════════════════════════════════════════════
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

            // Idempotente: si ya está anulada, informar sin re-procesar
            if (mb_strtolower((string) ($transaccion->estado_pago ?? '')) === 'anulado') {
                return response()->json([
                    'success' => true,
                    'message' => 'La transacción ya se encontraba ANULADA. El registro y su soporte se conservan.',
                ], 200);
            }

            DB::transaction(function () use ($transaccion) {
                $transaccion->update(['estado_pago' => 'Anulado']);
            });

            Log::info('Transacción financiera ANULADA (eliminación lógica)', [
                'id'      => $transaccion->id,
                'reserva' => $transaccion->id_rsv_reservas,
                'soporte' => $transaccion->soporte_pago, // se conserva
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transacción ANULADA (eliminación lógica). El registro y su soporte se conservan para auditoría.',
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en TransaccionFinancieraController@destroy: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al anular la transacción financiera.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
