<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Soportes\ScpSoporte; 
use App\Models\Soportes\ScpEstado;   
use App\Models\Soportes\ScpObservacion; 
use App\Models\Soportes\ScpTipo;
use App\Models\Soportes\ScpPrioridad;
use App\Models\Soportes\ScpCategoria;
use App\Models\Soportes\ScpUsuario;
use App\Models\Archivo\GdoArea;
use App\Models\Archivo\GdoCargo;
use App\Models\Creditos\LineaCredito;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ScpEstadisticaController extends Controller
{
    /**
     * Muestra la página de estadísticas de soportes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // --- 1. Obtener rango de fechas (por defecto, últimos 6 meses) ---
        $startDate = Carbon::parse($request->get('start_date', now()->subMonths(6)->startOfDay()));
        $endDate = Carbon::parse($request->get('end_date', now()->endOfDay()));

        // --- 2. Lógica para obtener los datos de la base de datos ---
        
        // Consulta base para filtrar por fechas
        $baseQuery = ScpSoporte::whereBetween('created_at', [$startDate, $endDate]);

        // KPI: Total de soportes en el rango de fechas
        $totalTickets = $baseQuery->count();
        
        // --- IDs de estados ---
        $idEstadoAbierto = 1; // <-- Reemplaza con el ID correcto
        $idEstadoCerrado = 3; // <-- Reemplaza con el ID correcto
        $idEstadoEnProceso = 2; // <-- Reemplaza con el ID correcto

        // KPI: Soportes abiertos
        $openTickets = (clone $baseQuery)->where('estado', $idEstadoAbierto)->count();
        
        // KPI: Soportes en proceso
        $inProgressTickets = (clone $baseQuery)->where('estado', $idEstadoEnProceso)->count();
        
        // KPI: Soportes cerrados
        $closedTickets = (clone $baseQuery)->where('estado', $idEstadoCerrado)->count();
        
        // KPI: Soportes escalados
        $escalatedTickets = (clone $baseQuery)->whereNotNull('usuario_escalado')->count();
        
        // --- KPI: Tiempo promedio de resolución (en horas) ---
        $avgResolutionTime = ScpSoporte::selectRaw('
                AVG(TIMESTAMPDIFF(HOUR, first_obs.timestam, last_obs.timestam)) as avg_time
            ')
            ->join('scp_observaciones as first_obs', function ($join) {
                $join->on('first_obs.id_scp_soporte', '=', 'scp_soportes.id')
                     ->whereRaw('first_obs.timestam = (SELECT MIN(timestam) FROM scp_observaciones WHERE id_scp_soporte = scp_soportes.id)');
            })
            ->join('scp_observaciones as last_obs', function ($join) use ($idEstadoCerrado) {
                $join->on('last_obs.id_scp_soporte', '=', 'scp_soportes.id')
                     ->where('last_obs.id_scp_estados', '=', $idEstadoCerrado)
                     ->whereRaw('last_obs.timestam = (SELECT MAX(timestam) FROM scp_observaciones WHERE id_scp_soporte = scp_soportes.id AND id_scp_estados = ?)', [$idEstadoCerrado]);
            })
            ->where('scp_soportes.estado', $idEstadoCerrado)
            ->whereBetween('scp_soportes.created_at', [$startDate, $endDate])
            ->whereNotNull('first_obs.timestam')
            ->whereNotNull('last_obs.timestam')
            ->value('avg_time');
        
        $avgResolutionTime = $avgResolutionTime ? round($avgResolutionTime) . ' hrs' : 'N/A';
        
        // --- KPI: Tiempo promedio de primera respuesta (en horas) ---
        $avgFirstResponseTime = ScpSoporte::selectRaw('
                AVG(TIMESTAMPDIFF(HOUR, scp_soportes.created_at, first_obs.timestam)) as avg_time
            ')
            ->join('scp_observaciones as first_obs', function ($join) {
                $join->on('first_obs.id_scp_soporte', '=', 'scp_soportes.id')
                     ->whereRaw('first_obs.timestam = (SELECT MIN(timestam) FROM scp_observaciones WHERE id_scp_soporte = scp_soportes.id)');
            })
            ->whereBetween('scp_soportes.created_at', [$startDate, $endDate])
            ->whereNotNull('first_obs.timestam')
            ->value('avg_time');
        
        $avgFirstResponseTime = $avgFirstResponseTime ? round($avgFirstResponseTime, 1) . ' hrs' : 'N/A';
        
        // --- KPI: Tasa de escalado (porcentaje) ---
        $escalationRate = $totalTickets > 0 ? round(($escalatedTickets / $totalTickets) * 100, 1) : 0;
        
        // --- KPI: Soportes resueltos sin escalado ---
        $resolvedWithoutEscalation = ScpSoporte::where('estado', $idEstadoCerrado)
            ->whereNull('usuario_escalado')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        
        // --- Gráfico 1: Soportes por Mes ---
        $labelsMes = [];
        $dataMes = [];
        $period = \Carbon\CarbonPeriod::create($startDate->copy()->startOfMonth(), '1 month', $endDate->copy()->endOfMonth());
        foreach ($period as $date) {
            $labelsMes[] = $date->format('M Y');
            $dataMes[] = ScpSoporte::whereMonth('created_at', $date->month)
                                   ->whereYear('created_at', $date->year)
                                   ->count();
        }

        // --- Gráfico 2: Soportes por Estado ---
        $estadosCounts = ScpEstado::leftJoin('scp_soportes', function($join) use ($startDate, $endDate) {
                $join->on('scp_estados.id', '=', 'scp_soportes.estado')
                     ->whereBetween('scp_soportes.created_at', [$startDate, $endDate]);
            })
            ->selectRaw('scp_estados.nombre as label, COUNT(scp_soportes.id) as data')
            ->groupBy('scp_estados.id', 'scp_estados.nombre')
            ->orderBy('scp_estados.nombre')
            ->get();

        $labelsEstado = $estadosCounts->pluck('label')->toArray();
        $dataEstado = $estadosCounts->pluck('data')->toArray();
        
        // --- Gráfico 3: Soportes por Tipo ---
        $tiposCounts = ScpTipo::leftJoin('scp_soportes', function($join) use ($startDate, $endDate) {
                $join->on('scp_tipos.id', '=', 'scp_soportes.id_scp_tipo')
                     ->whereBetween('scp_soportes.created_at', [$startDate, $endDate]);
            })
            ->selectRaw('scp_tipos.nombre as label, COUNT(scp_soportes.id) as data')
            ->groupBy('scp_tipos.id', 'scp_tipos.nombre')
            ->orderBy('data', 'desc')
            ->limit(10) // Limitar a los 10 tipos más comunes
            ->get();

        $labelsTipo = $tiposCounts->pluck('label')->toArray();
        $dataTipo = $tiposCounts->pluck('data')->toArray();
        
        // --- Gráfico 4: Soportes por Prioridad ---
        $prioridadesCounts = ScpPrioridad::leftJoin('scp_soportes', function($join) use ($startDate, $endDate) {
                $join->on('scp_prioridads.id', '=', 'scp_soportes.id_scp_prioridad')
                     ->whereBetween('scp_soportes.created_at', [$startDate, $endDate]);
            })
            ->selectRaw('scp_prioridads.nombre as label, COUNT(scp_soportes.id) as data')
            ->groupBy('scp_prioridads.id', 'scp_prioridads.nombre')
            ->orderBy('scp_prioridads.id')
            ->get();

        $labelsPrioridad = $prioridadesCounts->pluck('label')->toArray();
        $dataPrioridad = $prioridadesCounts->pluck('data')->toArray();
        
        // --- Gráfico 5: Soportes por Área ---
        $areasCounts = GdoArea::leftJoin('gdo_cargo', 'gdo_area.id', '=', 'gdo_cargo.GDO_area_id')
            ->leftJoin('scp_soportes', 'gdo_cargo.id', '=', 'scp_soportes.id_gdo_cargo')
            ->whereBetween('scp_soportes.created_at', [$startDate, $endDate])
            ->selectRaw('gdo_area.nombre as label, COUNT(scp_soportes.id) as data')
            ->groupBy('gdo_area.id', 'gdo_area.nombre')
            ->orderBy('data', 'desc')
            ->limit(10)
            ->get();

        $labelsArea = $areasCounts->pluck('label')->toArray();
        $dataArea = $areasCounts->pluck('data')->toArray();
        
        // --- Gráfico 6: Soportes por Categoría ---
        $categoriasCounts = ScpCategoria::leftJoin('scp_soportes', function($join) use ($startDate, $endDate) {
                $join->on('scp_categorias.id', '=', 'scp_soportes.id_categoria')
                     ->whereBetween('scp_soportes.created_at', [$startDate, $endDate]);
            })
            ->selectRaw('scp_categorias.nombre as label, COUNT(scp_soportes.id) as data')
            ->groupBy('scp_categorias.id', 'scp_categorias.nombre')
            ->orderBy('data', 'desc')
            ->get();

        $labelsCategoria = $categoriasCounts->pluck('label')->toArray();
        $dataCategoria = $categoriasCounts->pluck('data')->toArray();
        
        // --- Gráfico 7: Distribución por día de la semana ---
        $labelsDiaSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $dataDiaSemana = [];
        
        for ($i = 1; $i <= 7; $i++) {
            $dataDiaSemana[] = ScpSoporte::whereRaw('DAYOFWEEK(created_at) = ?', [$i + 1]) // MySQL: 1=Lunes, 7=Domingo
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
        }
        
        // --- Gráfico 8: Rendimiento por Agente (Top 10) ---
        $agentesRendimiento = User::join('scp_soportes', 'users.id', '=', 'scp_soportes.usuario_escalado')
            ->where('scp_soportes.estado', $idEstadoCerrado)
            ->whereBetween('scp_soportes.created_at', [$startDate, $endDate])
            ->selectRaw('users.name as label, COUNT(scp_soportes.id) as data')
            ->groupBy('users.id', 'users.name')
            ->orderBy('data', 'desc')
            ->limit(10)
            ->get();

        $labelsAgente = $agentesRendimiento->pluck('label')->toArray();
        $dataAgente = $agentesRendimiento->pluck('data')->toArray();
        
        // --- Gráfico 9: Tiempo de resolución por prioridad ---
        $tiempoPorPrioridad = ScpPrioridad::leftJoin('scp_soportes', 'scp_prioridads.id', '=', 'scp_soportes.id_scp_prioridad')
            ->join('scp_observaciones as first_obs', function ($join) {
                $join->on('first_obs.id_scp_soporte', '=', 'scp_soportes.id')
                     ->whereRaw('first_obs.timestam = (SELECT MIN(timestam) FROM scp_observaciones WHERE id_scp_soporte = scp_soportes.id)');
            })
            ->join('scp_observaciones as last_obs', function ($join) use ($idEstadoCerrado) {
                $join->on('last_obs.id_scp_soporte', '=', 'scp_soportes.id')
                     ->where('last_obs.id_scp_estados', '=', $idEstadoCerrado)
                     ->whereRaw('last_obs.timestam = (SELECT MAX(timestam) FROM scp_observaciones WHERE id_scp_soporte = scp_soportes.id AND id_scp_estados = ?)', [$idEstadoCerrado]);
            })
            ->where('scp_soportes.estado', $idEstadoCerrado)
            ->whereBetween('scp_soportes.created_at', [$startDate, $endDate])
            ->whereNotNull('first_obs.timestam')
            ->whereNotNull('last_obs.timestam')
            ->selectRaw('scp_prioridads.nombre as label, AVG(TIMESTAMPDIFF(HOUR, first_obs.timestam, last_obs.timestam)) as data')
            ->groupBy('scp_prioridads.id', 'scp_prioridads.nombre')
            ->orderBy('scp_prioridads.id')
            ->get();

        $labelsTiempoPrioridad = $tiempoPorPrioridad->pluck('label')->toArray();
        $dataTiempoPrioridad = $tiempoPorPrioridad->pluck('data')->map(function($item) {
            return round($item, 1);
        })->toArray();
        
        // --- Gráfico 10: Tasa de cumplimiento SLA por prioridad ---
        // CORRECCIÓN: Simplificamos esta consulta para evitar el error con la columna tiempo_maximo
        $slaPorPrioridad = ScpPrioridad::leftJoin('scp_soportes', 'scp_prioridads.id', '=', 'scp_soportes.id_scp_prioridad')
            ->whereBetween('scp_soportes.created_at', [$startDate, $endDate])
            ->selectRaw('
                scp_prioridads.nombre as label, 
                COUNT(scp_soportes.id) as total,
                SUM(CASE WHEN scp_soportes.estado = ? THEN 1 ELSE 0 END) as cumplidos
            ', [$idEstadoCerrado])
            ->groupBy('scp_prioridads.id', 'scp_prioridads.nombre')
            ->orderBy('scp_prioridads.id')
            ->get();

        $labelsSlaPrioridad = $slaPorPrioridad->pluck('label')->toArray();
        $dataSlaPrioridad = $slaPorPrioridad->map(function($item) {
            return $item->total > 0 ? round(($item->cumplidos / $item->total) * 100, 1) : 0;
        })->toArray();
        
        // --- Datos para la tabla de actividad reciente ---
        $actividadReciente = ScpSoporte::with(['estadoSoporte', 'usuario', 'maeTercero'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // --- Datos para la tabla de top agentes ---
        $topAgentes = ScpUsuario::join('scp_soportes', 'scp_usuarios.id', '=', 'scp_soportes.usuario_escalado')
            ->join('MaeTerceros', 'MaeTerceros.cod_ter', '=', 'scp_usuarios.cod_ter')
            ->where('scp_soportes.estado', 3)
            ->whereBetween('scp_soportes.created_at', [$startDate, $endDate])
            ->selectRaw('
                scp_usuarios.id,
                MaeTerceros.nom_ter,
                COUNT(scp_soportes.id) as total_cerrados,
                AVG(
                    TIMESTAMPDIFF(HOUR,
                        (SELECT timestam FROM scp_observaciones WHERE id_scp_soporte = scp_soportes.id ORDER BY timestam ASC LIMIT 1),
                        (SELECT timestam FROM scp_observaciones WHERE id_scp_soporte = scp_soportes.id AND id_scp_estados = 3 ORDER BY timestam DESC LIMIT 1)
                    )
                ) as tiempo_promedio
            ')
            ->groupBy('scp_usuarios.id', 'MaeTerceros.nom_ter')
            ->orderByDesc('total_cerrados')
            ->get();

            

        // --- 3. Pasar todos los datos a la vista ---
        return view('soportes.estadisticas.index', compact(
            'totalTickets', 
            'openTickets', 
            'inProgressTickets',
            'closedTickets', 
            'escalatedTickets',
            'resolvedWithoutEscalation',
            'avgResolutionTime',
            'avgFirstResponseTime',
            'escalationRate',
            'labelsMes',
            'dataMes',
            'labelsEstado',
            'dataEstado',
            'labelsTipo',
            'dataTipo',
            'labelsPrioridad',
            'dataPrioridad',
            'labelsArea',
            'dataArea',
            'labelsCategoria',
            'dataCategoria',
            'labelsDiaSemana',
            'dataDiaSemana',
            'labelsAgente',
            'dataAgente',
            'labelsTiempoPrioridad',
            'dataTiempoPrioridad',
            'labelsSlaPrioridad',
            'dataSlaPrioridad',
            'actividadReciente',
            'topAgentes'
        ));
    }
    
    /**
     * Obtiene los datos del dashboard para la API
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDashboardData(Request $request): JsonResponse
    {
        
        // --- 1. Obtener rango de fechas (por defecto, últimos 6 meses) ---
        $startDate = Carbon::parse($request->get('start_date', now()->subMonths(6)->startOfDay()));
        $endDate = Carbon::parse($request->get('end_date', now()->endOfDay()));
        
        // Obtener filtros adicionales
        $priority = $request->get('priority');
        $status = $request->get('status');

        // --- IDs de estados ---
        $idEstadoAbierto = 1; // <-- Reemplaza con el ID correcto
        $idEstadoCerrado = 3; // <-- Reemplaza con el ID correcto
        $idEstadoEnProceso = 2; // <-- Reemplaza con el ID correcto

        // --- 2. Lógica para obtener los datos de la base de datos ---
        
        // Consulta base para filtrar por fechas y filtros adicionales
        $baseQuery = ScpSoporte::whereBetween('created_at', [$startDate, $endDate]);
        
        // Aplicar filtros adicionales si se proporcionan
        if ($priority) {
            $baseQuery->where('id_scp_prioridad', $priority);
        }
        
        if ($status) {
            $baseQuery->where('estado', $status);
        }

        // KPI: Total de soportes en el rango de fechas
        $totalTickets = (clone $baseQuery)->count();
        
        // KPI: Soportes abiertos
        $openTickets = (clone $baseQuery)->where('estado', $idEstadoAbierto)->count();
        
        // KPI: Soportes en proceso
        $inProgressTickets = (clone $baseQuery)->where('estado', $idEstadoEnProceso)->count();
        
        // KPI: Soportes cerrados
        $closedTickets = (clone $baseQuery)->where('estado', $idEstadoCerrado)->count();
        
        // KPI: Soportes escalados
        $escalatedTickets = (clone $baseQuery)->whereNotNull('usuario_escalado')->count();
        
        // --- KPI: Tiempo promedio de resolución (en horas) ---
        $avgResolutionTime = ScpSoporte::selectRaw('
                AVG(TIMESTAMPDIFF(HOUR, first_obs.timestam, last_obs.timestam)) as avg_time
            ')
            ->join('scp_observaciones as first_obs', function ($join) {
                $join->on('first_obs.id_scp_soporte', '=', 'scp_soportes.id')
                     ->whereRaw('first_obs.timestam = (SELECT MIN(timestam) FROM scp_observaciones WHERE id_scp_soporte = scp_soportes.id)');
            })
            ->join('scp_observaciones as last_obs', function ($join) use ($idEstadoCerrado) {
                $join->on('last_obs.id_scp_soporte', '=', 'scp_soportes.id')
                     ->where('last_obs.id_scp_estados', '=', $idEstadoCerrado)
                     ->whereRaw('last_obs.timestam = (SELECT MAX(timestam) FROM scp_observaciones WHERE id_scp_soporte = scp_soportes.id AND id_scp_estados = ?)', [$idEstadoCerrado]);
            })
            ->where('scp_soportes.estado', $idEstadoCerrado)
            ->whereBetween('scp_soportes.created_at', [$startDate, $endDate])
            ->whereNotNull('first_obs.timestam')
            ->whereNotNull('last_obs.timestam');
            
        // Aplicar filtros adicionales si se proporcionan
        if ($priority) {
            $avgResolutionTime->where('scp_soportes.id_scp_prioridad', $priority);
        }
        
        if ($status) {
            $avgResolutionTime->where('scp_soportes.estado', $status);
        }
        
        $avgResolutionTime = $avgResolutionTime->value('avg_time');
        $avgResolutionTime = $avgResolutionTime ? round($avgResolutionTime) . ' hrs' : 'N/A';
        
        // --- KPI: Tiempo promedio de primera respuesta (en horas) ---
        $avgFirstResponseTime = ScpSoporte::selectRaw('
                AVG(TIMESTAMPDIFF(HOUR, scp_soportes.created_at, first_obs.timestam)) as avg_time
            ')
            ->join('scp_observaciones as first_obs', function ($join) {
                $join->on('first_obs.id_scp_soporte', '=', 'scp_soportes.id')
                     ->whereRaw('first_obs.timestam = (SELECT MIN(timestam) FROM scp_observaciones WHERE id_scp_soporte = scp_soportes.id)');
            })
            ->whereBetween('scp_soportes.created_at', [$startDate, $endDate])
            ->whereNotNull('first_obs.timestam');
            
        // Aplicar filtros adicionales si se proporcionan
        if ($priority) {
            $avgFirstResponseTime->where('scp_soportes.id_scp_prioridad', $priority);
        }
        
        if ($status) {
            $avgFirstResponseTime->where('scp_soportes.estado', $status);
        }
        
        $avgFirstResponseTime = $avgFirstResponseTime->value('avg_time');
        $avgFirstResponseTime = $avgFirstResponseTime ? round($avgFirstResponseTime, 1) . ' hrs' : 'N/A';
        
        // --- KPI: Tasa de escalado (porcentaje) ---
        $escalationRate = $totalTickets > 0 ? round(($escalatedTickets / $totalTickets) * 100, 1) : 0;
        
        // --- KPI: Soportes resueltos sin escalado ---
        $resolvedWithoutEscalation = ScpSoporte::where('estado', $idEstadoCerrado)
            ->whereNull('usuario_escalado')
            ->whereBetween('created_at', [$startDate, $endDate]);
            
        // Aplicar filtros adicionales si se proporcionan
        if ($priority) {
            $resolvedWithoutEscalation->where('id_scp_prioridad', $priority);
        }
        
        if ($status) {
            $resolvedWithoutEscalation->where('estado', $status);
        }
        
        $resolvedWithoutEscalation = $resolvedWithoutEscalation->count();
        
        // --- Gráfico 1: Soportes por Mes ---
        $labelsMes = [];
        $dataMes = [];
        $period = \Carbon\CarbonPeriod::create($startDate->copy()->startOfMonth(), '1 month', $endDate->copy()->endOfMonth());
        foreach ($period as $date) {
            $labelsMes[] = $date->format('M Y');
            
            $queryMes = (clone $baseQuery)->whereMonth('created_at', $date->month)
                                   ->whereYear('created_at', $date->year);
            $dataMes[] = $queryMes->count();
        }

        // --- Gráfico 2: Soportes por Estado ---
        $estadosCounts = ScpEstado::leftJoin('scp_soportes', function($join) use ($startDate, $endDate, $priority, $status) {
                $join->on('scp_estados.id', '=', 'scp_soportes.estado')
                     ->whereBetween('scp_soportes.created_at', [$startDate, $endDate]);
                     
                if ($priority) {
                    $join->where('scp_soportes.id_scp_prioridad', $priority);
                }
                
                if ($status) {
                    $join->where('scp_soportes.estado', $status);
                }
            })
            ->selectRaw('scp_estados.nombre as label, COUNT(scp_soportes.id) as data')
            ->groupBy('scp_estados.id', 'scp_estados.nombre')
            ->orderBy('scp_estados.nombre')
            ->get();

        $labelsEstado = $estadosCounts->pluck('label')->toArray();
        $dataEstado = $estadosCounts->pluck('data')->toArray();
        
        // --- Gráfico 3: Soportes por Tipo ---
        $tiposCounts = ScpTipo::leftJoin('scp_soportes', function($join) use ($startDate, $endDate, $priority, $status) {
                $join->on('scp_tipos.id', '=', 'scp_soportes.id_scp_tipo')
                     ->whereBetween('scp_soportes.created_at', [$startDate, $endDate]);
                     
                if ($priority) {
                    $join->where('scp_soportes.id_scp_prioridad', $priority);
                }
                
                if ($status) {
                    $join->where('scp_soportes.estado', $status);
                }
            })
            ->selectRaw('scp_tipos.nombre as label, COUNT(scp_soportes.id) as data')
            ->groupBy('scp_tipos.id', 'scp_tipos.nombre')
            ->orderBy('data', 'desc')
            ->limit(10) // Limitar a los 10 tipos más comunes
            ->get();

        $labelsTipo = $tiposCounts->pluck('label')->toArray();
        $dataTipo = $tiposCounts->pluck('data')->toArray();
        
        // --- Gráfico 4: Soportes por Prioridad ---
        $prioridadesCounts = ScpPrioridad::leftJoin('scp_soportes', function($join) use ($startDate, $endDate, $priority, $status) {
                $join->on('scp_prioridads.id', '=', 'scp_soportes.id_scp_prioridad')
                     ->whereBetween('scp_soportes.created_at', [$startDate, $endDate]);
                     
                if ($status) {
                    $join->where('scp_soportes.estado', $status);
                }
            })
            ->selectRaw('scp_prioridads.nombre as label, COUNT(scp_soportes.id) as data')
            ->groupBy('scp_prioridads.id', 'scp_prioridads.nombre')
            ->orderBy('scp_prioridads.id')
            ->get();

        $labelsPrioridad = $prioridadesCounts->pluck('label')->toArray();
        $dataPrioridad = $prioridadesCounts->pluck('data')->toArray();
        
        // --- Gráfico 5: Soportes por Área ---
        $areasCounts = GdoArea::leftJoin('gdo_cargos', 'gdo_areas.id', '=', 'gdo_cargos.GDO_area_id')
            ->leftJoin('scp_soportes', 'gdo_cargos.id', '=', 'scp_soportes.id_gdo_cargo')
            ->whereBetween('scp_soportes.created_at', [$startDate, $endDate]);
            
        // Aplicar filtros adicionales si se proporcionan
        if ($priority) {
            $areasCounts->where('scp_soportes.id_scp_prioridad', $priority);
        }
        
        if ($status) {
            $areasCounts->where('scp_soportes.estado', $status);
        }
            
        $areasCounts = $areasCounts->selectRaw('gdo_areas.nombre as label, COUNT(scp_soportes.id) as data')
            ->groupBy('gdo_areas.id', 'gdo_areas.nombre')
            ->orderBy('data', 'desc')
            ->limit(10) // Limitar a las 10 áreas con más soportes
            ->get();

        $labelsArea = $areasCounts->pluck('label')->toArray();
        $dataArea = $areasCounts->pluck('data')->toArray();
        
        // --- Gráfico 6: Soportes por Categoría ---
        $categoriasCounts = ScpCategoria::leftJoin('scp_soportes', function($join) use ($startDate, $endDate, $priority, $status) {
                $join->on('scp_categorias.id', '=', 'scp_soportes.id_categoria')
                     ->whereBetween('scp_soportes.created_at', [$startDate, $endDate]);
                     
                if ($priority) {
                    $join->where('scp_soportes.id_scp_prioridad', $priority);
                }
                
                if ($status) {
                    $join->where('scp_soportes.estado', $status);
                }
            })
            ->selectRaw('scp_categorias.nombre as label, COUNT(scp_soportes.id) as data')
            ->groupBy('scp_categorias.id', 'scp_categorias.nombre')
            ->orderBy('data', 'desc')
            ->get();

        $labelsCategoria = $categoriasCounts->pluck('label')->toArray();
        $dataCategoria = $categoriasCounts->pluck('data')->toArray();
        
        // --- Gráfico 7: Distribución por día de la semana ---
        $labelsDiaSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $dataDiaSemana = [];
        
        for ($i = 1; $i <= 7; $i++) {
            $queryDia = (clone $baseQuery)->whereRaw('DAYOFWEEK(created_at) = ?', [$i + 1]); // MySQL: 1=Lunes, 7=Domingo
            $dataDiaSemana[] = $queryDia->count();
        }
        
        // --- Gráfico 8: Rendimiento por Agente (Top 10) ---
        $agentesRendimiento = User::join('scp_soportes', 'users.id', '=', 'scp_soportes.usuario_escalado')
            ->whereBetween('scp_soportes.created_at', [$startDate, $endDate]);
        //dd($agentesRendimiento);
        // Aplicar filtros adicionales si se proporcionan
        if ($priority) {
            $agentesRendimiento->where('scp_soportes.id_scp_prioridad', $priority);
        }
        
        if ($status) {
            $agentesRendimiento->where('scp_soportes.estado', $status);
        }
            
        $agentesRendimiento = $agentesRendimiento->selectRaw('users.name as label, COUNT(scp_soportes.id) as data')
            ->groupBy('users.id', 'users.name')
            ->orderBy('data', 'desc')
            ->limit(10)
            ->get();

        $labelsAgente = $agentesRendimiento->pluck('label')->toArray();
        $dataAgente = $agentesRendimiento->pluck('data')->toArray();
        
        // --- Gráfico 9: Tiempo de resolución por prioridad ---
        $tiempoPorPrioridad = ScpPrioridad::leftJoin('scp_soportes', 'scp_prioridads.id', '=', 'scp_soportes.id_scp_prioridad')
            ->join('scp_observaciones as first_obs', function ($join) {
                $join->on('first_obs.id_scp_soporte', '=', 'scp_soportes.id')
                     ->whereRaw('first_obs.timestam = (SELECT MIN(timestam) FROM scp_observaciones WHERE id_scp_soporte = scp_soportes.id)');
            })
            ->join('scp_observaciones as last_obs', function ($join) use ($idEstadoCerrado) {
                $join->on('last_obs.id_scp_soporte', '=', 'scp_soportes.id')
                     ->where('last_obs.id_scp_estados', '=', $idEstadoCerrado)
                     ->whereRaw('last_obs.timestam = (SELECT MAX(timestam) FROM scp_observaciones WHERE id_scp_soporte = scp_soportes.id AND id_scp_estados = ?)', [$idEstadoCerrado]);
            })
            ->where('scp_soportes.estado', $idEstadoCerrado)
            ->whereBetween('scp_soportes.created_at', [$startDate, $endDate])
            ->whereNotNull('first_obs.timestam')
            ->whereNotNull('last_obs.timestam');
            
        // Aplicar filtros adicionales si se proporcionan
        if ($priority) {
            $tiempoPorPrioridad->where('scp_soportes.id_scp_prioridad', $priority);
        }
        
        if ($status) {
            $tiempoPorPrioridad->where('scp_soportes.estado', $status);
        }
            
        $tiempoPorPrioridad = $tiempoPorPrioridad->selectRaw('scp_prioridads.nombre as label, AVG(TIMESTAMPDIFF(HOUR, first_obs.timestam, last_obs.timestam)) as data')
            ->groupBy('scp_prioridads.id', 'scp_prioridads.nombre')
            ->orderBy('scp_prioridads.id')
            ->get();

        $labelsTiempoPrioridad = $tiempoPorPrioridad->pluck('label')->toArray();
        $dataTiempoPrioridad = $tiempoPorPrioridad->pluck('data')->map(function($item) {
            return round($item, 1);
        })->toArray();
        
        // --- Gráfico 10: Tasa de cumplimiento SLA por prioridad ---
        $slaPorPrioridad = ScpPrioridad::leftJoin('scp_soportes', 'scp_prioridads.id', '=', 'scp_soportes.id_scp_prioridad')
            ->whereBetween('scp_soportes.created_at', [$startDate, $endDate]);
            
        // Aplicar filtros adicionales si se proporcionan
        if ($priority) {
            $slaPorPrioridad->where('scp_soportes.id_scp_prioridad', $priority);
        }
        
        if ($status) {
            $slaPorPrioridad->where('scp_soportes.estado', $status);
        }
            
        $slaPorPrioridad = $slaPorPrioridad->selectRaw('
            scp_prioridads.nombre as label, 
            COUNT(scp_soportes.id) as total,
            SUM(CASE WHEN scp_soportes.estado = ? THEN 1 ELSE 0 END) as cumplidos
        ', [$idEstadoCerrado])
            ->groupBy('scp_prioridads.id', 'scp_prioridads.nombre')
            ->orderBy('scp_prioridads.id')
            ->get();

        $labelsSlaPrioridad = $slaPorPrioridad->pluck('label')->toArray();
        $dataSlaPrioridad = $slaPorPrioridad->map(function($item) {
            return $item->total > 0 ? round(($item->cumplidos / $item->total) * 100, 1) : 0;
        })->toArray();
        
        // --- 3. Devolver los datos en formato JSON ---
        return response()->json([
            // KPIs
            'totalTickets' => $totalTickets,
            'openTickets' => $openTickets,
            'inProgressTickets' => $inProgressTickets,
            'closedTickets' => $closedTickets,
            'escalatedTickets' => $escalatedTickets,
            'resolvedWithoutEscalation' => $resolvedWithoutEscalation,
            'avgResolutionTime' => $avgResolutionTime,
            'avgFirstResponseTime' => $avgFirstResponseTime,
            'escalationRate' => $escalationRate,
            
            // Gráficos
            'labelsMes' => $labelsMes,
            'dataMes' => $dataMes,
            'labelsEstado' => $labelsEstado,
            'dataEstado' => $dataEstado,
            'labelsTipo' => $labelsTipo,
            'dataTipo' => $dataTipo,
            'labelsPrioridad' => $labelsPrioridad,
            'dataPrioridad' => $dataPrioridad,
            'labelsArea' => $labelsArea,
            'dataArea' => $dataArea,
            'labelsCategoria' => $labelsCategoria,
            'dataCategoria' => $dataCategoria,
            'labelsDiaSemana' => $labelsDiaSemana,
            'dataDiaSemana' => $dataDiaSemana,
            'labelsAgente' => $labelsAgente,
            'dataAgente' => $dataAgente,
            'labelsTiempoPrioridad' => $labelsTiempoPrioridad,
            'dataTiempoPrioridad' => $dataTiempoPrioridad,
            'labelsSlaPrioridad' => $labelsSlaPrioridad,
            'dataSlaPrioridad' => $dataSlaPrioridad,
        ]);
    }
}