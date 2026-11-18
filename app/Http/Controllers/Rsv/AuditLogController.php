<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\View\View;
use Throwable;

class AuditLogController extends Controller
{
    /**
     * MOTOR CENTRAL DE AUDITORÍA (Llamable desde Controladores, Observers o Frontend).
     * Captura entradas al portal, visualización de pestañas/fragmentos y operaciones CRUD.
     *
     * @param string     $tablaAfectada  Ej: 'portal_cliente', 'panel_admin', 'rsv_reservas', 'rsv_transacciones'
     * @param string     $accion         Ej: 'VIEW', 'VIEW_FRAGMENT', 'CREATED', 'UPDATED', 'DELETED', 'EXPORT'
     * @param int|string $registroId     ID del registro o del inmueble/reserva consultada (0 si es vista general)
     * @param array|null $datosAnt       Estado anterior (para UPDATED / DELETED)
     * @param array|null $datosNue       Estado nuevo o metadatos de la vista/acción
     * @param int        $cooldownSeg    Evita duplicar logs de lectura idénticos en X segundos (0 = registrar siempre)
     */
    public static function registrar(
        string $tablaAfectada,
        string $accion,
        int|string|null $registroId = 0,
        ?array $datosAnt = null,
        ?array $datosNue = null,
        int $cooldownSeg = 0
    ): ?AuditLog {
        try {
            $req    = request();
            $user   = Auth::user();
            $userId = $user ? (int) $user->id : null;
            $regId  = is_numeric($registroId) ? (int) $registroId : 0;
            $acc    = mb_strtoupper(trim($accion));
            $tabla  = trim($tablaAfectada);

            // Control anti-ruido para eventos de solo lectura/visualización (VIEW / VIEW_FRAGMENT)
            if ($cooldownSeg > 0) {
                $hashKey = 'rsv_audit_cd_' . md5(($userId ?? 'guest') . '|' . $tabla . '|' . $acc . '|' . $regId . '|' . json_encode($datosNue['modulo'] ?? ''));
                if (Cache::has($hashKey)) {
                    return null;
                }
                Cache::put($hashKey, 1, now()->addSeconds($cooldownSeg));
            }

            // Enriquecer datos_nuevos con contexto del usuario, ruta y navegador sin alterar el esquema de la BD
            $metaContexto = [
                '_meta_actor'  => $user ? ($user->name . ' (ID #' . $user->id . ($user->nit ? ' · Doc ' . $user->nit : '') . ')') : 'Invitado/Sistema',
                '_meta_url'    => $req ? $req->fullUrl() : null,
                '_meta_metodo' => $req ? $req->method() : 'CLI',
                '_meta_ajax'   => $req ? ($req->ajax() ? 'SI' : 'NO') : 'NO',
                '_meta_agent'  => $req ? mb_substr((string) $req->userAgent(), 0, 140) : null,
            ];

            $payloadNuevos = array_filter(array_merge($datosNue ?? [], $metaContexto), fn ($v) => !is_null($v));

            return AuditLog::create([
                'id_user'          => $userId,
                'tabla_afectada'   => mb_substr($tabla, 0, 255),
                'registro_id'      => $regId,
                'accion'           => mb_substr($acc, 0, 50),
                'datos_anteriores' => $datosAnt,
                'datos_nuevos'     => $payloadNuevos,
                'ip_address'       => $req ? $req->ip() : '127.0.0.1',
            ]);
        } catch (Throwable $e) {
            Log::warning('AuditLogController::registrar falló: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Listado de registros de auditoría con filtros avanzados y soporte Web/JSON.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = AuditLog::with('user');

            if ($request->filled('tabla_afectada')) {
                $query->where('tabla_afectada', 'like', '%' . trim($request->tabla_afectada) . '%');
            }

            if ($request->filled('accion')) {
                $query->where('accion', trim($request->accion));
            }

            if ($request->filled('id_user')) {
                $query->where('id_user', (int) $request->id_user);
            }

            if ($request->filled('registro_id') && is_numeric($request->registro_id)) {
                $query->where('registro_id', (int) $request->registro_id);
            }

            if ($request->filled('desde')) {
                $query->whereDate('created_at', '>=', $request->desde);
            }

            if ($request->filled('hasta')) {
                $query->whereDate('created_at', '<=', $request->hasta);
            }

            if ($request->filled('q')) {
                $q = '%' . trim($request->q) . '%';
                $query->where(function ($sub) use ($q) {
                    $sub->where('tabla_afectada', 'like', $q)
                        ->orWhere('accion', 'like', $q)
                        ->orWhere('ip_address', 'like', $q)
                        ->orWhere('datos_nuevos', 'like', $q)
                        ->orWhere('datos_anteriores', 'like', $q);
                });
            }

            $sortField = in_array($request->input('sort_by'), ['id', 'created_at', 'accion', 'tabla_afectada', 'id_user'], true)
                ? $request->input('sort_by')
                : 'created_at';
            $sortOrder = strtolower($request->input('sort_order', 'desc')) === 'asc' ? 'asc' : 'desc';

            $query->orderBy($sortField, $sortOrder);

            $logs = $query->paginate((int) $request->input('per_page', 20))->appends($request->query());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registros de auditoría recuperados exitosamente.',
                    'data'    => $logs,
                ]);
            }

            $vista = view()->exists('rsv.admin.partials.auditorias.informes') && $request->boolean('partial')
                ? 'rsv.admin.partials.auditorias.informes'
                : 'rsv.audit-logs.index';

            return view($vista, ['logs' => $logs, 'auditoria' => $logs]);

        } catch (Throwable $e) {
            Log::error('Error al listar audit logs: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener los registros de auditoría.',
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al obtener los registros de auditoría.');
        }
    }

    /**
     * Endpoint para registrar eventos desde el Frontend (clics de pestañas, apertura de modales, descargas PDF, etc.)
     * o desde servicios internos.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_user'          => 'nullable|integer',
            'tabla_afectada'   => 'required|string|max:255',
            'registro_id'      => 'nullable|integer',
            'accion'           => 'required|string|max:50',
            'datos_anteriores' => 'nullable|array',
            'datos_nuevos'     => 'nullable|array',
            'ip_address'       => 'nullable|ip',
        ]);

        try {
            $auditLog = self::registrar(
                $validated['tabla_afectada'],
                $validated['accion'],
                $validated['registro_id'] ?? 0,
                $validated['datos_anteriores'] ?? null,
                $validated['datos_nuevos'] ?? null,
                0
            );

            return response()->json([
                'success' => true,
                'message' => 'Registro de auditoría creado exitosamente.',
                'data'    => $auditLog,
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error al crear audit log: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al crear el registro de auditoría.',
            ], 500);
        }
    }

    /**
     * Muestra el detalle de un registro de auditoría específico.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $auditLog = AuditLog::with('user')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Registro de auditoría recuperado exitosamente.',
                'data'    => $auditLog,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'El registro de auditoría solicitado no existe.',
            ], 404);
        } catch (Throwable $e) {
            Log::error('Error al obtener audit log: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener el registro de auditoría.',
            ], 500);
        }
    }

    /**
     * BLOQUEADO: Regla de negocio de inmutabilidad para logs de auditoría.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Operación no permitida. Los registros de auditoría son inmutables por integridad del sistema.',
        ], 403);
    }

    /**
     * BLOQUEADO: Regla de negocio de inmutabilidad para logs de auditoría.
     */
    public function destroy(string $id): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Operación no permitida. Los registros de auditoría no pueden ser eliminados.',
        ], 403);
    }
}
