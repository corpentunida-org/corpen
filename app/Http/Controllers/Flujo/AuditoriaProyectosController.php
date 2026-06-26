<?php

namespace App\Http\Controllers\Flujo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Flujo\TaskComment;
use App\Models\Flujo\TaskHistory;
use App\Models\Flujo\Workflow;
use App\Models\Flujo\Task; // Agregado para los KPIs de Tareas
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AuditoriaProyectosController extends Controller
{
    /**
     * Función privada para obtener y filtrar eventos.
     */
    private function getMergedEvents(Request $request, $limit = 100)
    {
        $commentsQuery = TaskComment::with(['user', 'task.workflow']);
        $historyQuery = TaskHistory::with(['user', 'task.workflow']);

        // --- 1. Filtros ---

        // A) Búsqueda General
        if ($request->filled('search')) {
            $search = $request->search;
            $commentsQuery->where(function ($q) use ($search) {
                $q->where('comentario', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('task', fn($t) => $t->where('titulo', 'like', "%{$search}%"));
            });
            $historyQuery->where(function ($q) use ($search) {
                $q->where('estado_nuevo', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('task', fn($t) => $t->where('titulo', 'like', "%{$search}%"));
            });
        }

        // B) Usuario (Normalizando user_id vs cambiado_por)
        if ($request->filled('user_id')) {
            $commentsQuery->where('user_id', $request->user_id);
            $historyQuery->where('cambiado_por', $request->user_id);
        }

        // C) Proyecto
        if ($request->filled('workflow_id')) {
            $commentsQuery->whereHas('task', fn($q) => $q->where('workflow_id', $request->workflow_id));
            $historyQuery->whereHas('task', fn($q) => $q->where('workflow_id', $request->workflow_id));
        }

        // --- 2. Obtención ---
        $filterType = $request->input('tipo_evento');
        $comments = collect([]);
        $history = collect([]);

        if (!$filterType || $filterType === 'comentario') {
            $comments = (clone $commentsQuery)
                ->latest()
                ->limit($limit)
                ->get()
                ->map(function ($item) {
                    $item->tipo_evento = 'comentario';
                    return $item;
                });
        }

        if (!$filterType || $filterType === 'historial') {
            $history = (clone $historyQuery)
                ->latest()
                ->limit($limit)
                ->get()
                ->map(function ($item) {
                    $item->tipo_evento = 'historial';
                    return $item;
                });
        }

        return $comments->concat($history)->sortByDesc('created_at');
    }

    public function index(Request $request)
    {
        // 1. Obtener Eventos de Auditoría (Tu lógica original)
        $merged = $this->getMergedEvents($request, 150);

        // Paginación Manual
        $page = $request->get('page', 1);
        $perPage = 15;
        $paginatedItems = new LengthAwarePaginator($merged->forPage($page, $perPage), $merged->count(), $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);

        // --- 2. ESTADÍSTICAS PARA GRÁFICAS DE AUDITORÍA (Tu lógica original) ---
        $commentsQuery = TaskComment::query();
        $historyQuery = TaskHistory::query();

        $totalComments = $commentsQuery->count();
        $totalHistory = $historyQuery->count();
        $totalEvents = $totalComments + $totalHistory;

        // Top Usuarios
        $usersC = $commentsQuery->select('user_id', DB::raw('count(*) as total'))->groupBy('user_id')->get();

        $usersH = $historyQuery->select('cambiado_por as user_id', DB::raw('count(*) as total'))->whereNotNull('cambiado_por')->groupBy('cambiado_por')->get();

        $userStats = [];
        foreach ($usersC as $u) {
            $userStats[$u->user_id] = ($userStats[$u->user_id] ?? 0) + $u->total;
        }
        foreach ($usersH as $u) {
            $userStats[$u->user_id] = ($userStats[$u->user_id] ?? 0) + $u->total;
        }

        arsort($userStats);
        $topUsersIds = array_slice(array_keys($userStats), 0, 5);

        $chartUserData = ['labels' => [], 'data' => [], 'ids' => []];
        if (!empty($topUsersIds)) {
            $chartUsers = User::whereIn('id', $topUsersIds)->get()->keyBy('id');
            foreach ($topUsersIds as $uid) {
                if (isset($chartUsers[$uid])) {
                    $chartUserData['labels'][] = explode(' ', $chartUsers[$uid]->name)[0];
                    $chartUserData['data'][] = $userStats[$uid];
                    $chartUserData['ids'][] = $uid;
                }
            }
        }

        // --- 3. CÁLCULO DE INDICADORES MATRIZ TIC (AGREGADO) ---

        // A. Uso de herramientas colaborativas (Usuarios activos últimos 30 días)
        $totalUsuarios = User::count();
        $usuariosActivos = TaskHistory::where('created_at', '>=', now()->subDays(30))
            ->whereNotNull('cambiado_por') // Corrección aplicada: cambiado_por en lugar de user_id
            ->distinct('cambiado_por')
            ->count('cambiado_por');

        $kpiColaboracion = $totalUsuarios > 0 ? round(($usuariosActivos / $totalUsuarios) * 100, 1) : 0;

        // B. Cumplimiento del plan estratégico
        $totalProyectos = Workflow::count();
        $proyectosEjecutados = Workflow::where('estado', 'completado')->count();
        $kpiCumplimiento = $totalProyectos > 0 ? round(($proyectosEjecutados / $totalProyectos) * 100, 1) : 0;

        // C. Digitalización de procesos
        $proyectosDigitalizados = Workflow::whereIn('estado', ['activo', 'completado'])->count();
        $kpiDigitalizacion = $totalProyectos > 0 ? round(($proyectosDigitalizados / $totalProyectos) * 100, 1) : 0;

        // D. Tasa de incidentes/tareas resueltos
        $totalTareas = Task::count();
        $tareasResueltas = Task::where('estado', 'completado')->count();
        $kpiResolucion = $totalTareas > 0 ? round(($tareasResueltas / $totalTareas) * 100, 1) : 0;

        // E. Tiempos promedio de respuesta
        $tiempos = Task::select('prioridad', DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as horas_promedio'))->where('estado', 'completado')->groupBy('prioridad')->pluck('horas_promedio', 'prioridad');

        $kpiTiempoAlta = round($tiempos['alta'] ?? 0, 1);
        $kpiTiempoMedia = round($tiempos['media'] ?? 0, 1);
        $kpiTiempoBaja = round($tiempos['baja'] ?? 0, 1);

        // Datos para selects
        $users = User::orderBy('name')->get();
        $workflows = Workflow::select('id', 'nombre')->orderBy('nombre')->get();

        return view('flujo.auditoria.index', [
            // Datos de Auditoría
            'events' => $paginatedItems,
            'users' => $users,
            'workflows' => $workflows,
            'stats' => [
                'total' => $totalEvents,
                'comments' => $totalComments,
                'history' => $totalHistory,
                'usersData' => $chartUserData,
            ],
            // Datos de Matriz TIC (Nuevos)
            'kpiColaboracion' => $kpiColaboracion,
            'kpiCumplimiento' => $kpiCumplimiento,
            'kpiDigitalizacion' => $kpiDigitalizacion,
            'kpiResolucion' => $kpiResolucion,
            'kpiTiempoAlta' => $kpiTiempoAlta,
            'kpiTiempoMedia' => $kpiTiempoMedia,
            'kpiTiempoBaja' => $kpiTiempoBaja,
        ]);
    }

    /**
     * Generar PDF Corporativo Completo
     */
    public function exportPdf(Request $request)
    {
        $date = now()->format('d/m/Y H:i');
        // 1. Obtener TODOS los workflows para el listado general inicial
        $todosLosWorkflows = Workflow::select('id', 'nombre', 'estado')->get();
        // 2. Filtrar los Workflows Completos
        $workflowsCompletos = Workflow::where('estado', 'completado')->get();
        // 3. Filtrar los Workflows Incompletos incluyendo sus Tareas y los Eventos de esas tareas
        $workflowsIncompletos = Workflow::where('estado', '!=', 'completado')
            ->with(['tasks.histories.user']) // Trae las tareas, sus eventos y el usuario del evento
            ->get()
            ->map(function($workflow) {
                $workflow->setRelation('tasks', $workflow->tasks->filter(function($task) {
                    // Decodificar el JSON de la descripción
                    $subtareasArray = json_decode($task->descripcion, true) ?? [];
                    
                    // Si no es un array válido o está vacío, omitimos esta tarea de la descripción de subtareas
                    if (!is_array($subtareasArray) || empty($subtareasArray)) {
                        return false; 
                    }

                    // Conservamos la tarea y le asignamos el array decodificado
                    $task->subtareas_estructuradas = $subtareasArray;
                    return true;
                }));

                return $workflow;
            });

        // 4. Generar el PDF enviando ÚNICAMENTE los datos requeridos
        $pdf = Pdf::loadView('flujo.auditoria.pdf', [
            'date'                 => $date,
            'todosLosWorkflows'    => $todosLosWorkflows,
            'workflowsCompletos'   => $workflowsCompletos,
            'workflowsIncompletos' => $workflowsIncompletos,
        ]);

        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('Reporte_Estructura_Workflows.pdf');
    }
}
