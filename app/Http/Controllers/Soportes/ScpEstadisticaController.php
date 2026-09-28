<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Soportes\ScpSoporte;
use App\Models\Soportes\ScpPrioridad;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class ScpEstadisticaController extends Controller
{
    // Constantes de Estados
    const ESTADO_ABIERTO = 1;
    const ESTADO_PROCESO = 2;
    const ESTADO_REVISION = 3;
    const ESTADO_CERRADO = 4;

    // Constantes de Tipos de Observación
    const TIPO_OBS_RESPUESTA = 2;
    const TIPO_OBS_SOLUCION = 4;

    public function index(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $data = $this->calculateStats($request);

        $labelsPrioridad = ScpPrioridad::pluck('nombre');

        return view(
            'soportes.estadisticas.index',
            array_merge($data, [
                'labelsPrioridadSelect' => $labelsPrioridad,
                'labelsPrioridad'       => $labelsPrioridad,
            ])
        );
    }

    public function getDashboardData(Request $request): JsonResponse
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        try {
            $data = $this->calculateStats($request);

            // Formato fechas para el AJAX
            $data['startDate'] = $data['startDate']->format('Y-m-d');
            $data['endDate'] = $data['endDate']->format('Y-m-d');

            // Formateo de tabla de Soportes para JS
            $data['actividadReciente'] = $data['actividadReciente']->map(function ($soporte) {
                $nombreAsignado = 'Sin asignar';
                $inicialAsignado = '?';

                if ($soporte->scpUsuarioAsignado) {
                    $nombreAsignado = $soporte->scpUsuarioAsignado->name ?? ($soporte->scpUsuarioAsignado->maeTercero->nom_ter ?? 'Usuario');
                    $inicialAsignado = substr($nombreAsignado, 0, 1);
                }

                return [
                    'id' => $soporte->id,
                    'creador_nombre' => $soporte->usuario?->name ?? ($soporte->usuario?->maeTercero?->nom_ter ?? 'Sistema'),
                    'detalles_soporte' => $soporte->detalles_soporte,
                    'created_at_formatted' => $soporte->created_at->format('d/m/Y H:i'),
                    'estado_color' => $soporte->estadoSoporte->color ?? 'light',
                    'estado_nombre' => $soporte->estadoSoporte->nombre ?? 'N/A',
                    'prioridad_color' => $soporte->prioridad->color ?? 'secondary',
                    'prioridad_nombre' => $soporte->prioridad->nombre ?? 'N/A',
                    'asignado_nombre' => $nombreAsignado,
                    'asignado_inicial' => $inicialAsignado,
                ];
            });

            return response()->json($data);
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'error' => 'Error Interno: ' . $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
                500
            );
        }
    }

    private function calculateStats(Request $request)
    {
        $defaultStart = now()->startOfMonth();
        $defaultEnd = now()->endOfDay();

        $startDate = Carbon::parse($request->get('start_date', $defaultStart))->startOfDay();
        $endDate = Carbon::parse($request->get('end_date', $defaultEnd))->endOfDay();

        $priority = $request->get('priority');
        $status = $request->get('status');

        $baseQuery = ScpSoporte::whereBetween('scp_soportes.created_at', [$startDate, $endDate]);

        if ($priority) {
            $baseQuery->where('scp_soportes.id_scp_prioridad', $priority);
        }
        if ($status) {
            $baseQuery->where('scp_soportes.estado', $status);
        }

        // KPIs Volumen
        $totalTickets = (clone $baseQuery)->count();
        $openTickets = (clone $baseQuery)->where('scp_soportes.estado', self::ESTADO_ABIERTO)->count();
        $inProgressTickets = (clone $baseQuery)->where('scp_soportes.estado', self::ESTADO_PROCESO)->count();
        $revisionTickets = (clone $baseQuery)->where('scp_soportes.estado', self::ESTADO_REVISION)->count();
        $closedTickets = (clone $baseQuery)->where('scp_soportes.estado', self::ESTADO_CERRADO)->count();

        $escalatedTickets = (clone $baseQuery)->whereNotNull('scp_soportes.usuario_escalado')->count();
        $escalationRate = $totalTickets > 0 ? round(($escalatedTickets / $totalTickets) * 100, 1) : 0;

        // Tiempo Resolución
        $avgResolutionTimeVal = (clone $baseQuery)->where('scp_soportes.estado', self::ESTADO_CERRADO)->selectRaw('AVG(TIMESTAMPDIFF(HOUR, scp_soportes.created_at, scp_soportes.updated_at)) as avg_time')->value('avg_time');
        $avgResolutionTime = $avgResolutionTimeVal ? round($avgResolutionTimeVal, 1) . ' hrs' : '0 hrs';

        // KPIs Avanzados
        $firstResponseRate = $this->calculateFirstResponseRate($baseQuery);
        $reopenRate = $this->calculateReopenRate($baseQuery);
        $csatScore = $this->calculateCsatScore($baseQuery);
        $avgFirstResponseTime = $this->calculateAvgFirstResponseTime($baseQuery);
        $slaCompliance = $this->calculateSlaCompliance($baseQuery);

        // Gráfico Mensual (Evolución) — antes hacía un COUNT() por cada mes del rango (81
        // consultas para un rango de ~7 años, por ejemplo); una sola consulta agrupada por
        // año/mes y luego se arma el arreglo en PHP, igual que chartInteraccionesPorDia() en
        // Interacciones.
        $labelsMes = [];
        $dataMes = [];
        $period = CarbonPeriod::create($startDate->copy()->startOfMonth(), '1 month', $endDate->copy()->endOfMonth());

        $porMes = (clone $baseQuery)
            ->selectRaw('YEAR(scp_soportes.created_at) as anio, MONTH(scp_soportes.created_at) as mes, COUNT(*) as total')
            ->groupBy('anio', 'mes')
            ->get()
            ->keyBy(fn ($fila) => $fila->anio.'-'.$fila->mes);

        foreach ($period as $date) {
            $labelsMes[] = $date->format('M Y');
            $clave = $date->year.'-'.$date->month;
            $dataMes[] = (int) ($porMes[$clave]->total ?? 0);
        }

        // --- GRÁFICOS RESTANTES ---

        $ticketsByType = $this->getTicketsByType($baseQuery);
        $ticketsByPriority = $this->getTicketsByPriority($baseQuery);

        $estadosData = (clone $baseQuery)->join('scp_estados', 'scp_soportes.estado', '=', 'scp_estados.id')
            ->selectRaw('scp_estados.nombre as label, COUNT(scp_soportes.id) as data')
            ->groupBy('scp_estados.id', 'scp_estados.nombre')->get();
        $labelsEstado = $estadosData->pluck('label');
        $dataEstado = $estadosData->pluck('data');

        $areasData = (clone $baseQuery)->join('gdo_cargo', 'scp_soportes.id_gdo_cargo', '=', 'gdo_cargo.id')
            ->join('gdo_area', 'gdo_cargo.GDO_area_id', '=', 'gdo_area.id')
            ->selectRaw('gdo_area.nombre as label, COUNT(scp_soportes.id) as data')
            ->groupBy('gdo_area.id', 'gdo_area.nombre')->orderByDesc('data')->limit(10)->get();
        $labelsArea = $areasData->pluck('label');
        $dataArea = $areasData->pluck('data');

        // Top Agentes Gráfica Radar (Datos crudos) - LÓGICA A PRUEBA DE BALAS CON LEFT JOIN
        $agentesData = (clone $baseQuery)
            ->leftJoin('scp_usuarios', 'scp_soportes.usuario_escalado', '=', 'scp_usuarios.id')
            ->leftJoin('MaeTerceros', 'MaeTerceros.cod_ter', '=', 'scp_usuarios.cod_ter')
            ->leftJoin('users as u_escalado', 'scp_soportes.usuario_escalado', '=', 'u_escalado.id')
            ->leftJoin('users as u_creador', 'scp_soportes.id_users', '=', 'u_creador.id')
            ->where('scp_soportes.estado', self::ESTADO_CERRADO)
            ->selectRaw('COALESCE(MaeTerceros.nom_ter, u_escalado.name, u_creador.name, "Agente Local") as label, COUNT(scp_soportes.id) as data')
            ->groupBy('label')
            ->orderByDesc('data')
            ->limit(10)
            ->get();
        $labelsAgente = $agentesData->pluck('label');
        $dataAgente = $agentesData->pluck('data');

        // Top Agentes de Resolución (Para la tabla) - LÓGICA A PRUEBA DE BALAS
        $topAgentes = $this->getTopAgentesQuery($baseQuery);

        // Tabla Completa Registros
        $actividadReciente = (clone $baseQuery)
            ->with(['estadoSoporte', 'usuario', 'prioridad', 'scpUsuarioAsignado.maeTercero'])
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'totalTickets' => $totalTickets,
            'openTickets' => $openTickets,
            'inProgressTickets' => $inProgressTickets,
            'revisionTickets' => $revisionTickets,
            'closedTickets' => $closedTickets,
            'escalatedTickets' => $escalatedTickets,
            'avgResolutionTime' => $avgResolutionTime,
            'escalationRate' => $escalationRate,
            'slaCompliance' => $slaCompliance,
            'csatScore' => $csatScore,
            'reopenRate' => $reopenRate,
            'firstResponseRate' => $firstResponseRate,
            'avgFirstResponseTime' => $avgFirstResponseTime,

            'startDate' => $startDate,
            'endDate' => $endDate,

            'labelsMes' => $labelsMes,
            'dataMes' => $dataMes,

            'ticketsByType' => $ticketsByType,
            'ticketsByPriority' => $ticketsByPriority,

            'labelsEstado' => $labelsEstado,
            'dataEstado' => $dataEstado,

            'labelsArea' => $labelsArea,
            'dataArea' => $dataArea,

            'labelsAgente' => $labelsAgente,
            'dataAgente' => $dataAgente,

            'topAgentes' => $topAgentes,
            'actividadReciente' => $actividadReciente,
        ];
    }

    private function calculateSlaCompliance($baseQuery)
    {
        $totalDentroSLA = (clone $baseQuery)
            ->joinSub(
                DB::table('scp_observaciones')
                    ->select('id_scp_soporte', DB::raw('MIN(timestam) as fecha_cierre'))
                    ->where('id_scp_estados', 4)
                    ->groupBy('id_scp_soporte'),
                'o',
                'o.id_scp_soporte',
                '=',
                'scp_soportes.id'
            )
            ->whereRaw(
                "
                    TIMESTAMPDIFF(DAY, scp_soportes.timestam, o.fecha_cierre) <=
                    CASE scp_soportes.id_scp_prioridad
                        WHEN 3 THEN 15
                        WHEN 2 THEN 10
                        WHEN 1 THEN 5
                        ELSE 0
                    END
                "
            )
            ->count();

        $totalQuery = (clone $baseQuery)->count();
        if ($totalQuery == 0) return 0;

        return ($totalDentroSLA / $totalQuery) * 100;
    }

    /**
     * CSAT real: promedio de las calificaciones (1-5 estrellas, scp_observaciones.calcification)
     * que de verdad se registran al cerrar un ticket (ver "Calificación del servicio" en
     * show.blade.php). Antes devolvía siempre 4.8 fijo ("// Simulado"), sin importar el periodo
     * ni los datos reales — cualquiera que mirara este número en el tablero estaba viendo un
     * dato inventado.
     */
    private function calculateCsatScore($baseQuery)
    {
        $promedio = (clone $baseQuery)
            ->join('scp_observaciones', 'scp_soportes.id', '=', 'scp_observaciones.id_scp_soporte')
            ->whereNotNull('scp_observaciones.calcification')
            ->where('scp_observaciones.calcification', '!=', '')
            ->avg('scp_observaciones.calcification');

        return $promedio ? round($promedio, 1) : 0;
    }

    /**
     * "Reabierto" de verdad: el ticket tuvo una observación en Cerrado y, DESPUÉS, otra en un
     * estado distinto — es decir, se cerró y alguien volvió a tocarlo. Antes esto calculaba
     * exactamente la misma fórmula que escalationRate (porcentaje de escalados), sin ninguna
     * relación con reaperturas reales.
     */
    private function calculateReopenRate($baseQuery)
    {
        $total = (clone $baseQuery)->count();
        if ($total === 0) {
            return 0;
        }

        $reabiertos = (clone $baseQuery)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('scp_observaciones as cierre')
                    ->whereColumn('cierre.id_scp_soporte', 'scp_soportes.id')
                    ->where('cierre.id_scp_estados', self::ESTADO_CERRADO)
                    ->whereExists(function ($query2) {
                        $query2->select(DB::raw(1))
                            ->from('scp_observaciones as reapertura')
                            ->whereColumn('reapertura.id_scp_soporte', 'cierre.id_scp_soporte')
                            ->where('reapertura.id_scp_estados', '!=', self::ESTADO_CERRADO)
                            ->whereColumn('reapertura.created_at', '>', 'cierre.created_at');
                    });
            })
            ->count();

        return round(($reabiertos / $total) * 100, 1);
    }

    private function calculateFirstResponseRate($baseQuery)
    {
        $totalTickets = (clone $baseQuery)->count();
        if ($totalTickets === 0) {
            return 0;
        }

        $ticketsWithFirstResponse = (clone $baseQuery)
            ->whereHas('observaciones')
            ->count();

        return round(($ticketsWithFirstResponse / $totalTickets) * 100, 1);
    }

    /**
     * Tiempo promedio hasta la PRIMERA respuesta — antes hacía join directo contra
     * scp_observaciones y promediaba el tiempo hasta TODAS las observaciones de cada ticket, no
     * solo la primera: un ticket con muchas observaciones a lo largo de semanas inflaba el
     * promedio de "primera respuesta" con datos de gestiones muy posteriores. Se agrupa primero
     * por ticket (MIN de created_at) y se promedia sobre eso.
     */
    private function calculateAvgFirstResponseTime($baseQuery)
    {
        $avgTime = (clone $baseQuery)
            ->joinSub(
                DB::table('scp_observaciones')
                    ->select('id_scp_soporte', DB::raw('MIN(created_at) as primera_respuesta'))
                    ->groupBy('id_scp_soporte'),
                'primera',
                'primera.id_scp_soporte',
                '=',
                'scp_soportes.id'
            )
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, scp_soportes.created_at, primera.primera_respuesta)) as avg_time')
            ->value('avg_time');

        return $avgTime ? round($avgTime, 1) . ' hrs' : '0 hrs';
    }

    private function getTicketsByType($baseQuery)
    {
        return (clone $baseQuery)->join('scp_tipos', 'scp_soportes.id_scp_tipo', '=', 'scp_tipos.id')->selectRaw('scp_tipos.nombre as label, COUNT(scp_soportes.id) as data')->groupBy('scp_tipos.id', 'scp_tipos.nombre')->orderByDesc('data')->limit(5)->get();
    }

    private function getTicketsByPriority($baseQuery)
    {
        return (clone $baseQuery)->join('scp_prioridads', 'scp_soportes.id_scp_prioridad', '=', 'scp_prioridads.id')->selectRaw('scp_prioridads.nombre as label, COUNT(scp_soportes.id) as data')->groupBy('scp_prioridads.id', 'scp_prioridads.nombre')->get();
    }

    private function getTopAgentesQuery($baseQuery)
    {
        return (clone $baseQuery)
            ->leftJoin('scp_usuarios', 'scp_soportes.usuario_escalado', '=', 'scp_usuarios.id')
            ->leftJoin('MaeTerceros', 'MaeTerceros.cod_ter', '=', 'scp_usuarios.cod_ter')
            ->leftJoin('users as u_escalado', 'scp_soportes.usuario_escalado', '=', 'u_escalado.id')
            ->leftJoin('users as u_creador', 'scp_soportes.id_users', '=', 'u_creador.id')
            ->where('scp_soportes.estado', self::ESTADO_CERRADO)
            ->selectRaw('
                COALESCE(scp_soportes.usuario_escalado, scp_soportes.id_users, 0) as agente_id,
                COALESCE(MaeTerceros.nom_ter, u_escalado.name, u_creador.name, "Agente Local") as name,
                COUNT(scp_soportes.id) as total_cerrados,
                0 as tiempo_promedio
            ')
            ->groupBy('agente_id', 'name')
            ->orderByDesc('total_cerrados')
            ->limit(10)
            ->get();
    }
}
