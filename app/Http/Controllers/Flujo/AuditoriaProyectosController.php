<?php

namespace App\Http\Controllers\Flujo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Flujo\TaskComment;
use App\Models\Flujo\TaskHistory;
use App\Models\Flujo\Workflow;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AuditoriaProyectosController extends Controller
{
    /**
     * Función privada para obtener y filtrar eventos.
     * Se usa tanto en la vista web como en la exportación PDF.
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
        // Usamos la función compartida para la vista web (limitado a 150 para rendimiento)
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

        // --- ESTADÍSTICAS PARA GRÁFICAS WEB ---
        // (Nota: Aquí no aplicamos filtros complejos para las gráficas generales, 
        //  pero podrías replicar la lógica de filtros si lo deseas)
        
        $commentsQuery = TaskComment::query();
        $historyQuery = TaskHistory::query();
        
        $totalComments = $commentsQuery->count();
        $totalHistory = $historyQuery->count();
        $totalEvents = $totalComments + $totalHistory;

        // Top Usuarios (Normalizando columnas)
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

        $users = User::orderBy('name')->get();
        $workflows = Workflow::select('id', 'nombre')->orderBy('nombre')->get();

        return view('flujo.auditoria.index', [
            'events' => $paginatedItems,
            'users' => $users,
            'workflows' => $workflows,
            'stats' => [
                'total' => $totalEvents,
                'comments' => $totalComments,
                'history' => $totalHistory,
                'usersData' => $chartUserData
            ]
        ]);
    }

    /**
     * Generar PDF Corporativo Completo
     */
    public function exportPdf(Request $request)
    {
        // 1. Obtener eventos detallados
        $events = $this->getMergedEvents($request, 500);

        // 2. LÓGICA DE ESTADÍSTICAS MENSUALES (AÑO ACTUAL)
        $currentYear = now()->year;
        
        // Comentarios por Mes
        $commentsByMonth = TaskComment::selectRaw('user_id, MONTH(created_at) as mes, COUNT(*) as total')
            ->whereYear('created_at', $currentYear)
            ->groupBy('user_id', 'mes')
            ->get();

        // Historial por Mes (usando cambiado_por)
        $historyByMonth = TaskHistory::selectRaw('cambiado_por as user_id, MONTH(created_at) as mes, COUNT(*) as total')
            ->whereYear('created_at', $currentYear)
            ->whereNotNull('cambiado_por')
            ->groupBy('cambiado_por', 'mes')
            ->get();

        // Unificar datos en matriz [Usuario => [Meses => Total]]
        $monthlyStats = [];
        $allUserIds = $commentsByMonth->pluck('user_id')
                        ->merge($historyByMonth->pluck('user_id'))
                        ->unique();
        
        $usersMap = User::whereIn('id', $allUserIds)->pluck('name', 'id');

        foreach ($usersMap as $id => $name) {
            $monthlyStats[$id] = [
                'name' => $name,
                'months' => array_fill(1, 12, 0), // Inicializar 12 meses en 0
                'total' => 0
            ];
        }

        // Llenar matriz con comentarios
        foreach ($commentsByMonth as $c) {
            if (isset($monthlyStats[$c->user_id])) {
                $monthlyStats[$c->user_id]['months'][$c->mes] += $c->total;
                $monthlyStats[$c->user_id]['total'] += $c->total;
            }
        }

        // Llenar matriz con historial
        foreach ($historyByMonth as $h) {
            if (isset($monthlyStats[$h->user_id])) {
                $monthlyStats[$h->user_id]['months'][$h->mes] += $h->total;
                $monthlyStats[$h->user_id]['total'] += $h->total;
            }
        }

        // Ordenar por actividad total descendente
        uasort($monthlyStats, fn($a, $b) => $b['total'] <=> $a['total']);

        // 3. Preparar filtros para mostrar en el header del PDF
        $filtersApplied = [];
        if($request->filled('search')) $filtersApplied[] = "Búsqueda: " . $request->search;
        if($request->filled('tipo_evento')) $filtersApplied[] = "Tipo: " . ucfirst($request->tipo_evento);
        
        if($request->filled('user_id')) {
            $u = User::find($request->user_id);
            if($u) $filtersApplied[] = "Usuario: " . $u->name;
        }
        if($request->filled('workflow_id')) {
            $w = Workflow::find($request->workflow_id);
            if($w) $filtersApplied[] = "Proyecto: " . $w->nombre;
        }

        // 4. Generar PDF
        $pdf = Pdf::loadView('flujo.auditoria.pdf', [
            'events' => $events,
            'filters' => $filtersApplied,
            'date' => now()->format('d/m/Y H:i'),
            'user' => auth()->user()->name,
            'monthlyStats' => $monthlyStats, // <-- Datos mensuales enviados a la vista
            'year' => $currentYear
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Reporte_Auditoria_Global_' . now()->format('Ymd_Hi') . '.pdf');
    }
}