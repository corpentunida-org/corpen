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
            $commentsQuery->where(function($q) use ($search) {
                $q->where('comentario', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('task', fn($t) => $t->where('titulo', 'like', "%{$search}%"));
            });
            $historyQuery->where(function($q) use ($search) {
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
            $comments = (clone $commentsQuery)->latest()->limit($limit)->get()->map(function ($item) {
                $item->tipo_evento = 'comentario';
                return $item;
            });
        }

        if (!$filterType || $filterType === 'historial') {
            $history = (clone $historyQuery)->latest()->limit($limit)->get()->map(function ($item) {
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
        $paginatedItems = new LengthAwarePaginator(
            $merged->forPage($page, $perPage),
            $merged->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // --- 2. ESTADÍSTICAS PARA GRÁFICAS DE AUDITORÍA (Tu lógica original) ---
        $commentsQuery = TaskComment::query();
        $historyQuery = TaskHistory::query();
        
        $totalComments = $commentsQuery->count();
        $totalHistory = $historyQuery->count();
        $totalEvents = $totalComments + $totalHistory;

        // Top Usuarios
        $usersC = $commentsQuery->select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')->get();
            
        $usersH = $historyQuery->select('cambiado_por as user_id', DB::raw('count(*) as total'))
            ->whereNotNull('cambiado_por')
            ->groupBy('cambiado_por')->get();
        
        $userStats = [];
        foreach($usersC as $u) $userStats[$u->user_id] = ($userStats[$u->user_id] ?? 0) + $u->total;
        foreach($usersH as $u) $userStats[$u->user_id] = ($userStats[$u->user_id] ?? 0) + $u->total;
        
        arsort($userStats);
        $topUsersIds = array_slice(array_keys($userStats), 0, 5);
        
        $chartUserData = ['labels' => [], 'data' => [], 'ids' => []];
        if (!empty($topUsersIds)) {
            $chartUsers = User::whereIn('id', $topUsersIds)->get()->keyBy('id');
            foreach($topUsersIds as $uid) {
                if(isset($chartUsers[$uid])) {
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
        $tiempos = Task::select('prioridad', DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as horas_promedio'))
            ->where('estado', 'completado')
            ->groupBy('prioridad')
            ->pluck('horas_promedio', 'prioridad');

        $kpiTiempoAlta  = round($tiempos['alta'] ?? 0, 1);
        $kpiTiempoMedia = round($tiempos['media'] ?? 0, 1);
        $kpiTiempoBaja  = round($tiempos['baja'] ?? 0, 1);

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
                'usersData' => $chartUserData
            ],
            // Datos de Matriz TIC (Nuevos)
            'kpiColaboracion' => $kpiColaboracion,
            'kpiCumplimiento' => $kpiCumplimiento,
            'kpiDigitalizacion' => $kpiDigitalizacion,
            'kpiResolucion' => $kpiResolucion,
            'kpiTiempoAlta' => $kpiTiempoAlta,
            'kpiTiempoMedia' => $kpiTiempoMedia,
            'kpiTiempoBaja' => $kpiTiempoBaja
        ]);
    }

    /**
     * Generar PDF Corporativo Completo
     */
    public function exportPdf(Request $request)
    {
        // 1. Obtener eventos (Aumentamos el límite para el reporte)
        $events = $this->getMergedEvents($request, 1000);

        // ---------------------------------------------------------
        // CORRECCIÓN: CALCULAR ESTADÍSTICAS BASADAS EN LOS EVENTOS REALES
        // (Esto asegura que si hay 41 eventos, la suma de usuarios sea 41)
        // ---------------------------------------------------------
        
        $monthlyStats = [];
        $currentYear = now()->year;

        foreach ($events as $event) {
            // Validar que el evento tenga usuario y fecha
            if (!$event->user || !$event->created_at) continue;

            // Solo graficamos en la matriz el año actual (o puedes quitar este if si quieres todo)
            if ($event->created_at->year != $currentYear) continue;

            $uid = $event->user->id;
            $month = $event->created_at->month; // 1 al 12

            // Inicializar usuario si no existe en el array
            if (!isset($monthlyStats[$uid])) {
                $monthlyStats[$uid] = [
                    'name'   => $event->user->name,
                    'months' => array_fill(1, 12, 0), // Inicializa meses en 0
                    'total'  => 0
                ];
            }

            // Sumar al mes correspondiente y al total
            $monthlyStats[$uid]['months'][$month]++;
            $monthlyStats[$uid]['total']++;
        }

        // Ordenar usuarios por actividad (Mayor a menor)
        uasort($monthlyStats, fn($a, $b) => $b['total'] <=> $a['total']);
        
        // ---------------------------------------------------------
        // FIN CORRECCIÓN
        // ---------------------------------------------------------

        // --- CÁLCULO DE KPIS PARA EL PDF ---
        $totalUsuarios = User::count();
        // Corrección KPI Colaboración: Usar la colección de usuarios encontrados en los eventos
        $usuariosEnReporte = count($monthlyStats);
        $kpiColaboracion = $totalUsuarios > 0 ? round(($usuariosEnReporte / $totalUsuarios) * 100, 1) : 0;

        $totalProyectos = Workflow::count();
        $proyectosEjecutados = Workflow::where('estado', 'completado')->count();
        $kpiCumplimiento = $totalProyectos > 0 ? round(($proyectosEjecutados / $totalProyectos) * 100, 1) : 0;

        $proyectosDigitalizados = Workflow::whereIn('estado', ['activo', 'completado'])->count();
        $kpiDigitalizacion = $totalProyectos > 0 ? round(($proyectosDigitalizados / $totalProyectos) * 100, 1) : 0;

        $totalTareas = Task::count();
        $tareasResueltas = Task::where('estado', 'completado')->count();
        $kpiResolucion = $totalTareas > 0 ? round(($tareasResueltas / $totalTareas) * 100, 1) : 0;

        $tiempos = Task::select('prioridad', DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as horas_promedio'))
            ->where('estado', 'completado')->groupBy('prioridad')->pluck('horas_promedio', 'prioridad');
        
        $kpiTiempoAlta  = round($tiempos['alta'] ?? 0, 1);
        $kpiTiempoMedia = round($tiempos['media'] ?? 0, 1);
        $kpiTiempoBaja  = round($tiempos['baja'] ?? 0, 1);

        // Preparar filtros visuales
        $filtersApplied = [];
        if($request->filled('search')) $filtersApplied[] = "Búsqueda: " . $request->search;
        if($request->filled('tipo_evento')) $filtersApplied[] = "Tipo: " . ucfirst($request->tipo_evento);
        if($request->filled('user_id')) {
            $u = User::find($request->user_id); if($u) $filtersApplied[] = "Usuario: " . $u->name;
        }
        if($request->filled('workflow_id')) {
            $w = Workflow::find($request->workflow_id); if($w) $filtersApplied[] = "Proyecto: " . $w->nombre;
        }

        // Generar PDF
        $pdf = Pdf::loadView('flujo.auditoria.pdf', [
            'events' => $events,
            'filters' => $filtersApplied,
            'date' => now()->format('d/m/Y H:i'),
            'user' => auth()->user()->name,
            'monthlyStats' => $monthlyStats, // Ahora sí lleva datos reales
            'year' => $currentYear,
            
            // KPIs
            'kpiColaboracion' => $kpiColaboracion,
            'kpiCumplimiento' => $kpiCumplimiento,
            'kpiDigitalizacion' => $kpiDigitalizacion,
            'kpiResolucion' => $kpiResolucion,
            'kpiTiempoAlta' => $kpiTiempoAlta,
            'kpiTiempoMedia' => $kpiTiempoMedia,
            'kpiTiempoBaja' => $kpiTiempoBaja
        ]);

        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('Reporte_Auditoria_Global.pdf');
    }
}