<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AuditLogController extends Controller
{
    /**
     * Display a listing of the audit logs.
     * Implementa paginación, carga ansiosa (N+1), filtros estratégicos y soporte Dual (Web/JSON).
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            // Eager loading optimizado solicitando solo los campos necesarios del usuario
            $query = AuditLog::with('user:id,name,email');

            if ($request->filled('tabla_afectada')) {
                $query->where('tabla_afectada', $request->tabla_afectada);
            }

            if ($request->filled('accion')) {
                $query->where('accion', $request->accion);
            }

            if ($request->filled('id_user')) {
                $query->where('id_user', $request->id_user);
            }

            // Ordenamiento por defecto descendente para ver lo más reciente
            $sortField = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortField, $sortOrder);

            $logs = $query->paginate($request->input('per_page', 15));

            // Si la petición es por API o AJAX, devolvemos JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registros de auditoría recuperados exitosamente.',
                    'data' => $logs
                ]);
            }

            // Si es una petición del navegador web, renderizamos la vista Blade
            return view('rsv.audit-logs.index', compact('logs'));

        } catch (\Throwable $e) {
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
     * Store a newly created audit log in storage.
     * En una arquitectura empresarial, la auditoría suele registrarse vía Observers/Eventos.
     * Se expone el endpoint validado en caso de que un microservicio externo requiera registrar.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_user' => 'nullable|exists:users,id',
            'tabla_afectada' => 'required|string|max:255',
            'registro_id' => 'required|integer',
            'accion' => 'required|string|max:50',
            'datos_anteriores' => 'nullable|array',
            'datos_nuevos' => 'nullable|array',
            'ip_address' => 'nullable|ip',
        ]);

        try {
            $auditLog = AuditLog::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Registro de auditoría creado exitosamente.',
                'data' => $auditLog
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error al crear audit log: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al crear el registro de auditoría.',
            ], 500);
        }
    }

    /**
     * Display the specified audit log.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $auditLog = AuditLog::with('user:id,name,email')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Registro de auditoría recuperado exitosamente.',
                'data' => $auditLog
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'El registro de auditoría solicitado no existe.',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Error al obtener audit log: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener el registro de auditoría.',
            ], 500);
        }
    }

    /**
     * Update the specified audit log.
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
     * Remove the specified audit log from storage.
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
