<?php

namespace App\Http\Controllers\Flujo;

use App\Http\Controllers\Controller;
use App\Models\Flujo\Workflow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Models\Flujo\TaskComment;
use App\Models\Flujo\TaskHistory;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class WorkflowController extends Controller
{
    /**
     * Listado de workflows (Dashboard Principal).
     */
    public function index(Request $request)
    {
        // 1. Preparar palabras de búsqueda para resaltado en la vista
        $searchWords = [];
        if ($request->filled('search')) {
            $searchWords = array_filter(explode(' ', $request->search));
        }

        // 2. Construcción de la Consulta Base
        $query = Workflow::query();

        // Aplicar filtros (Lógica unificada)
        if (!empty($searchWords)) {
            $query->where(function($q) use ($searchWords) {
                foreach ($searchWords as $word) {
                    $q->orWhere('nombre', 'like', '%' . $word . '%')
                      ->orWhere('descripcion', 'like', '%' . $word . '%');
                }
            });
        }
        
        if ($request->filled('estado')) $query->where('estado', $request->estado);
        if ($request->filled('prioridad')) $query->where('prioridad', $request->prioridad);
        if ($request->filled('asignado_a')) $query->where('asignado_a', $request->asignado_a);

        // 3. Obtener resultados paginados para la lista
        // Clonamos $query para no afectar los cálculos de gráficas posteriores
        $workflows = (clone $query)
            ->with(['asignado']) // Eager loading optimizado
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // 4. Datos Globales (Sin filtros, para los contadores superiores fijos si se requiere, o filtrados)
        // En tu vista original, los contadores superiores parecían ser globales.
        $globalCounts = Workflow::selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');
        $total = $globalCounts->sum();

        // 5. Datos para Gráficos (Basados en la búsqueda actual - $query filtrado)
        
        // A. Distribución de Estados (Filtrado)
        $filteredCounts = (clone $query)
            ->selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        // B. Cumplimiento (Filtrado)
        $cumplimientoQuery = (clone $query);
        $cumplimiento = [
            'a_tiempo' => (clone $cumplimientoQuery)->where('fecha_fin', '>=', now())->where('estado', '!=', 'completado')->count(),
            'atrasados' => (clone $cumplimientoQuery)->where('fecha_fin', '<', now())->where('estado', '!=', 'completado')->count(),
            'completados' => (clone $cumplimientoQuery)->where('estado', 'completado')->count(),
        ];

        // C. Carga por Líder (Filtrado)
        $leadersData = (clone $query)
            ->with('asignado')
            ->select('asignado_a', DB::raw('count(*) as total'))
            ->whereNotNull('asignado_a')
            ->groupBy('asignado_a')
            ->get();

        // 6. Datos Auxiliares para Selects
        $users = User::orderBy('name')->get();
        
        // Definición centralizada de etiquetas
        $estadoLabels = [
            'borrador'   => 'Inicialización',
            'activo'     => 'Ejecución',
            'pausado'    => 'En Cola',
            'completado' => 'Terminado',
            'archivado'  => 'Rechazado'
        ];

        return view('flujo.workflows.index', compact(
            'workflows', 
            'users', 
            'searchWords',
            'globalCounts', // $counts en tu vista original
            'total',
            'filteredCounts',
            'cumplimiento',
            'leadersData',
            'estadoLabels'
        ));
    }

    public function create()
    {
        $workflows = Workflow::select('id', 'nombre')->orderBy('nombre')->get(); 
        $users = User::select('id', 'name')->orderBy('name')->get();
        // Usamos el método privado para mantener consistencia
        $estados = $this->getEstadosOptions(); 
        $prioridades = $this->getPrioridadesOptions();

        return view('flujo.workflows.create', compact('users', 'estados', 'prioridades', 'workflows'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'        => 'required|string|max:255',
            'descripcion'   => 'nullable|string|max:1000',
            'estado'        => ['required', Rule::in(array_keys($this->getEstadosOptions()))],
            'prioridad'     => ['required', Rule::in(array_keys($this->getPrioridadesOptions()))],
            'fecha_inicio'  => 'nullable|date',
            'fecha_fin'     => 'nullable|date|after_or_equal:fecha_inicio',
            'creado_por'    => 'required|exists:users,id',
            'asignado_a'    => 'nullable|exists:users,id',
            'configuracion' => 'nullable', 
        ]);

        if ($request->filled('configuracion') && is_string($request->configuracion)) {
            $data['configuracion'] = json_decode($request->configuracion, true);
        }

        $data['activo'] = $request->has('activo');
        $data['es_plantilla'] = $request->has('es_plantilla');

        Workflow::create($data);

        return redirect()->route('flujo.workflows.index')->with('success', '✅ Proceso creado correctamente.');
    }

    public function show(Workflow $workflow)
    {
        $workflow->load(['creator', 'modifier', 'asignado', 'tasks.asignado', 'tasks.creator']);

        // KPIs
        $totalTasks = $workflow->tasks->count();
        $completedTasks = $workflow->tasks->filter(function($task) {
            $estado = Str::lower($task->estado);
            return in_array($estado, ['completada', 'completado', 'finalizada', 'cerrada', 'terminado']);
        })->count();
        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        // Auditoría
        $taskIds = $workflow->tasks->pluck('id');
        
        // Optimización: Cargar todo en menos consultas
        $comments = TaskComment::whereIn('task_id', $taskIds)->with('user', 'task')->latest()->get();
        $history = TaskHistory::whereIn('task_id', $taskIds)->with('user', 'task')->latest()->get();

        $totalComments = $comments->count();
        $totalHistory = $history->count();

        // Calcular Top Usuarios (PHP collection logic es más rápida aquí que multiples queries si hay pocos datos)
        $userActivity = [];
        foreach($comments as $c) $userActivity[$c->user_id] = ($userActivity[$c->user_id] ?? 0) + 1;
        foreach($history as $h) {
            if($h->cambiado_por) $userActivity[$h->cambiado_por] = ($userActivity[$h->cambiado_por] ?? 0) + 1;
        }
        arsort($userActivity);
        $topUserIds = array_slice(array_keys($userActivity), 0, 5);
        $topUsers = User::whereIn('id', $topUserIds)->pluck('name', 'id');

        $chartUserData = ['labels' => [], 'data' => []];
        foreach($topUserIds as $uid) {
            if(isset($topUsers[$uid])) {
                $chartUserData['labels'][] = explode(' ', $topUsers[$uid])[0];
                $chartUserData['data'][] = $userActivity[$uid];
            }
        }

        // Unificar eventos
        $mappedComments = $comments->take(50)->map(function ($item) { $item->tipo_evento = 'comentario'; return $item; });
        $mappedHistory = $history->take(50)->map(function ($item) { $item->tipo_evento = 'historial'; return $item; });
        $auditEvents = $mappedComments->concat($mappedHistory)->sortByDesc('created_at');

        $workflowHistories = $history->take(10); // Para el sidebar

        $auditStats = [
            'total' => $totalComments + $totalHistory,
            'comments' => $totalComments,
            'history' => $totalHistory,
            'usersData' => $chartUserData
        ];

        return view('flujo.workflows.show', compact(
            'workflow', 'auditEvents', 'auditStats', 'workflowHistories', 
            'totalTasks', 'completedTasks', 'progress'
        ));
    }

    public function edit(Workflow $workflow)
    {
        $users = User::select('id', 'name')->orderBy('name')->get();
        $estados = $this->getEstadosOptions();
        $prioridades = $this->getPrioridadesOptions();
        $workflow->load(['creator', 'modifier', 'asignado']);

        return view('flujo.workflows.edit', compact('workflow', 'users', 'estados', 'prioridades'));
    }

    public function update(Request $request, Workflow $workflow)
    {
        try {
            DB::beginTransaction();
            $configuracion = $this->procesarConfiguracionJson($request);
            
            $data = $request->validate([
                'nombre'        => 'required|string|max:255',
                'descripcion'   => 'nullable|string|max:1000',
                'estado'        => ['required', Rule::in(array_keys($this->getEstadosOptions()))],
                'prioridad'     => ['required', Rule::in(array_keys($this->getPrioridadesOptions()))],
                'fecha_inicio'  => 'nullable|date',
                'fecha_fin'     => 'nullable|date|after_or_equal:fecha_inicio',
                'creado_por'    => 'required|exists:users,id',
                'asignado_a'    => 'nullable|exists:users,id',
            ]);
            
            $data['configuracion'] = $configuracion;
            $data['activo'] = $request->has('activo');
            $data['es_plantilla'] = $request->has('es_plantilla');
            $data['modificado_por'] = auth()->id();
            
            $workflow->update($data);
            DB::commit();
            
            return redirect()->route('flujo.workflows.index')->with('success', '✏️ Proyecto actualizado.');
                
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error update workflow: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['general' => 'Error crítico.']);
        }
    }
    /**
     * Actualiza exclusivamente los integrantes del equipo vía AJAX.
     */
    public function updateTeam(Request $request, Workflow $workflow)
    {
        try {
            // Validamos que sea un array de IDs
            $ids = $request->input('participantes', []);
            
            // Sincronizamos (agrega nuevos, quita los desmarcados)
            $workflow->participantes()->sync($ids);
            
            return response()->json([
                'success' => true,
                'message' => '👥 Equipo de proyecto actualizado correctamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar equipo: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy(Workflow $workflow)
    {
        $workflow->delete();
        return redirect()->route('flujo.workflows.index')->with('success', '🗑️ Proceso eliminado.');
    }

    // --- MÉTODOS PRIVADOS Y DE PDF (Mantenidos igual de sólidos) ---
    private function procesarConfiguracionJson(Request $request)
    {
        $configuracionJson = $request->input('configuracion');
        if (empty($configuracionJson) || $configuracionJson === '[]' || $configuracionJson === '{}') return null;
        if (is_array($configuracionJson)) return $configuracionJson;
        
        $decodedConfig = json_decode($configuracionJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw ValidationException::withMessages(['configuracion' => 'Formato JSON inválido.']);
        }
        return $decodedConfig;
    }

    private function getEstadosOptions()
    {
        return [
            'borrador'   => 'Inicialización',
            'activo'     => 'Ejecución',
            'pausado'    => 'En Cola',
            'completado' => 'Terminado',
            'archivado'  => 'Rechazado'
        ];
    }

    private function getPrioridadesOptions()
    {
        return ['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta', 'crítica' => 'Crítica'];
    }

    public function generatePdf($id)
    {
        $workflow = Workflow::with(['tasks.user', 'asignado', 'creator'])->findOrFail($id);
        
        $totalTasks = $workflow->tasks->count();
        $completedTasks = $workflow->tasks->filter(function($task) {
            $estado = Str::lower($task->estado);
            return in_array($estado, ['completada', 'completado', 'finalizada', 'cerrada', 'terminado', 'finalizado']);
        })->count();
        $pendingTasks = $totalTasks - $completedTasks;
        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        
        $daysActive = intval($workflow->created_at->diffInDays(now())); 
        $daysActive = $daysActive == 0 ? 1 : $daysActive;

        $isCompleted = in_array(strtolower($workflow->estado), ['completado', 'finalizado', 'terminado', 'cerrado', 'archivado']);
        $kpiDate = [];

        if ($isCompleted) {
            $kpiDate = ['value' => 'OK', 'label' => 'FINALIZADO', 'note' => 'Cerrado correctamente.', 'color' => '#10b981'];
        } elseif ($workflow->fecha_fin) {
            $fechaFin = Carbon::parse($workflow->fecha_fin);
            $daysRemaining = now()->diffInDays($fechaFin, false);
            if ($daysRemaining < 0) {
                $kpiDate = ['value' => abs((int)$daysRemaining) . ' d', 'label' => 'DE RETRASO', 'note' => 'Vencido el ' . $fechaFin->format('d/m'), 'color' => '#ef4444'];
            } else {
                $kpiDate = ['value' => abs((int)$daysRemaining) . ' d', 'label' => 'RESTANTES', 'note' => 'Vence el ' . $fechaFin->format('d/m'), 'color' => '#10b981'];
            }
        } else {
            $kpiDate = ['value' => '∞', 'label' => 'SIN FECHA', 'note' => 'Gestión continua.', 'color' => '#94a3b8'];
        }

        $taskIds = $workflow->tasks->pluck('id');
        $comments = TaskComment::whereIn('task_id', $taskIds)->with(['user', 'task'])->get()
            ->map(function ($item) { $item->tipo_evento = 'comentario'; return $item; });
        $history = TaskHistory::whereIn('task_id', $taskIds)->with(['user', 'task'])->get()
            ->map(function ($item) { $item->tipo_evento = 'historial'; return $item; });
        $auditEvents = $comments->concat($history)->sortByDesc('created_at');
        $totalComments = $comments->count();

        $priorityConfig = match($workflow->prioridad) {
            'crítica' => ['bg' => '#FFE4E6', 'text' => '#BE123C'],
            'alta'    => ['bg' => '#FFEDD5', 'text' => '#C2410C'],
            'media'   => ['bg' => '#E0F2FE', 'text' => '#0369A1'],
            default   => ['bg' => '#F1F5F9', 'text' => '#475569'],
        };

        $pdf = Pdf::loadView('flujo.workflows.pdf', compact(
            'workflow', 'progress', 'totalTasks', 'completedTasks', 'pendingTasks',
            'auditEvents', 'priorityConfig', 'daysActive', 'totalComments', 'kpiDate'
        ));
        
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('informe-proyecto-' . $workflow->id . '.pdf');
    }
}