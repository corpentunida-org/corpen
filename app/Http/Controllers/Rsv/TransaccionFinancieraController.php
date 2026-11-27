<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rsv\StoreTransaccionFinancieraRequest;
use App\Http\Requests\Rsv\UpdateTransaccionFinancieraRequest;
use App\Mail\ReservaInmueble;
use App\Models\Rsv\TransaccionFinanciera;
use App\Services\Rsv\RsvAcceso;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
     * Registrar una transacción financiera mediante StoreTransaccionFinancieraRequest.
     * FIX 1364: garantiza id_rsv_pasarela (resolución automática si viene vacío).
     */
    public function store(StoreTransaccionFinancieraRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        // El asociado solo reporta pagos de SUS reservas y siempre quedan "Pendiente" (antes podía
        // enviar estado_pago=Aprobado). Contabilidad/admin (rsv.pagos.validar) registran con cualquier estado.
        $puedeValidar = RsvAcceso::puede(RsvAcceso::PAGOS_VALIDAR);
        if (!$puedeValidar && !RsvAcceso::esTitular((int) ($validatedData['id_rsv_reservas'] ?? 0))) {
            return RsvAcceso::denegado('Solo puedes reportar pagos de tus reservas.');
        }
        if (!$puedeValidar) {
            $validatedData['estado_pago'] = 'Pendiente';
        }

        // Procesar archivo cargado si proviene de multipart/form-data
        if ($request->hasFile('soporte_pago')) {
            // Disco según entorno (S3 con bucket; si no, "public"). Si la subida falla, se
            // rechaza: antes quedaba soporte_pago = "0" y el pago parecía tener soporte.
            $path = $request->file('soporte_pago')->store('soportes_pagos', TransaccionFinanciera::discoSoportes());
            if (!$path) {
                Log::error('Rsv soporte de pago: no se pudo guardar el archivo', ['disco' => TransaccionFinanciera::discoSoportes()]);
                return response()->json(['success' => false, 'message' => 'No se pudo guardar el archivo del soporte. Intenta de nuevo.'], 422);
            }
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
                $trx = TransaccionFinanciera::create($validatedData);
                $trx->load(['reserva', 'pasarela']);
                return $trx;
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

    /**
     * Actualizar una transacción financiera mediante UpdateTransaccionFinancieraRequest.
     */
    public function update(UpdateTransaccionFinancieraRequest $request, string $id): JsonResponse
    {
        $validatedData = $request->validated();

        if ($request->hasFile('soporte_pago')) {
            // Disco según entorno (S3 con bucket; si no, "public"). Si la subida falla, se
            // rechaza: antes quedaba soporte_pago = "0" y el pago parecía tener soporte.
            $path = $request->file('soporte_pago')->store('soportes_pagos', TransaccionFinanciera::discoSoportes());
            if (!$path) {
                Log::error('Rsv soporte de pago: no se pudo guardar el archivo', ['disco' => TransaccionFinanciera::discoSoportes()]);
                return response()->json(['success' => false, 'message' => 'No se pudo guardar el archivo del soporte. Intenta de nuevo.'], 422);
            }
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

            // motivo_rechazo no es columna: solo viaja en el correo al asociado
            $motivoRechazo = trim((string) ($validatedData['motivo_rechazo'] ?? ''));
            unset($validatedData['motivo_rechazo']);
            $estabaRechazada = str_contains(mb_strtolower((string) $transaccion->estado_pago), 'rechaz');

            DB::transaction(function () use ($transaccion, $validatedData) {
                $transaccion->update($validatedData);
            });

            $transaccion->load(['reserva', 'pasarela']);

            // Contabilidad acaba de RECHAZAR el soporte → la reserva vuelve a "Pendiente subir pago"
            // (estado derivado) con plazo nuevo, y se avisa al asociado.
            if (!$estabaRechazada && str_contains(mb_strtolower((string) $transaccion->estado_pago), 'rechaz')) {
                $this->notificarSoporteRechazado($transaccion, $motivoRechazo);
            }

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
     * Correo al asociado: su soporte fue rechazado, con el motivo y el nuevo plazo para subir otro
     * (si la reserva sigue Pendiente). Un fallo del correo solo se registra; no revierte el rechazo.
     */
    private function notificarSoporteRechazado(TransaccionFinanciera $tx, string $motivo): void
    {
        try {
            $reserva = $tx->reserva;
            $user = $reserva ? $reserva->user : null;
            if (!$reserva || !$user || empty($user->email)) {
                Log::warning('Rsv soporte rechazado: asociado sin correo', ['transaccion' => $tx->id]);
                return;
            }
            $reserva->load(['inmueble', 'status', 'transacciones']);
            $fmt = fn ($f) => \Illuminate\Support\Carbon::parse($f)->locale('es')->translatedFormat('l j \d\e F \d\e Y');
            $pendiente = str_contains(mb_strtolower((string) optional($reserva->status)->name), 'pendien');
            $limite = $pendiente ? ReservaController::fechaLimitePagoDe($reserva) : null;

            $texto = 'Contabilidad revisó el soporte de pago por <strong>$' . number_format((float) $tx->monto, 0, ',', '.') . ' COP</strong> '
                . 'que cargó el ' . e(optional($tx->created_at)->format('d/m/Y')) . ' para su reserva <strong>' . e($reserva->codigo_reserva) . '</strong> '
                . 'en <strong>' . e(optional($reserva->inmueble)->name ?? 'el inmueble') . '</strong> y <strong>no pudo validarlo</strong>.<br><br>'
                . ($motivo !== '' ? '<strong>Motivo:</strong> ' . nl2br(e($motivo)) . '<br><br>' : '')
                . 'Por favor cargue un comprobante de pago válido desde el Portal de Reservas'
                . ($limite ? ' a más tardar el <strong>' . e($fmt($limite)) . '</strong>; si no se recibe, la reserva se cancelará automáticamente' : '')
                . '. ¡Dios le bendiga!';

            Mail::to($user->email)->send(new ReservaInmueble(
                $user->name,
                $texto,
                'Soporte de pago rechazado · Reserva ' . $reserva->codigo_reserva,
                false,
                null,
                url(route('rsv.cliente.portal')) . '?tab=pagos-transacciones&reserva_id=' . $reserva->id . '&subir=1',
                'Subir un nuevo soporte'
            ));
        } catch (Throwable $e) {
            Log::error('Rsv soporte rechazado: no se pudo enviar el correo (tx ' . $tx->id . '): ' . $e->getMessage());
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

    /**
     * ════════════════════════════════════════════════════════════════
     * ELIMINACIÓN LÓGICA (Soft Delete) — REGULA DE NEGOCIO:
     * · NUNCA se borra el registro (se usa SoftDeletes de Laravel).
     * · NUNCA se borra el soporte de S3: permanece visible/auditable.
     * · Se marca estado_pago = 'Anulado' y se llena 'deleted_at' (excluido del balance).
     * ════════════════════════════════════════════════════════════════
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            // Nota: find() ya ignora automáticamente los registros que tengan 'deleted_at' lleno.
            $transaccion = TransaccionFinanciera::find($id);

            if (!$transaccion) {
                return response()->json([
                    'success' => false,
                    'message' => 'La transacción financiera solicitada no existe o ya fue eliminada.',
                ], 404);
            }

            DB::transaction(function () use ($transaccion) {
                // 1. Mantenemos tu lógica de cambiar el estado
                $transaccion->update(['estado_pago' => 'Anulado']);
                
                // 2. Aplicamos la eliminación lógica nativa (llena la columna deleted_at)
                $transaccion->delete();
            });

            Log::info('Transacción financiera ANULADA y ELIMINADA LÓGICAMENTE', [
                'id'      => $transaccion->id,
                'reserva' => $transaccion->id_rsv_reservas,
                'soporte' => $transaccion->soporte_pago, // se conserva
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transacción eliminada lógicamente. El registro y su soporte se conservan para auditoría.',
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
