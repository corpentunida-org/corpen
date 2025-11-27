<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Soportes\ScpSoporte; // Importamos el modelo de Soporte
use App\Models\Soportes\ScpEstado;   // Importamos el modelo de Estado
use App\Models\Soportes\ScpObservacion; // <-- Importamos el modelo de Observación
use Illuminate\Http\Request;
use Carbon\Carbon; // Útil para manejar fechas


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
        // CORRECCIÓN: Convertir explícitamente a objetos Carbon
        $startDate = Carbon::parse($request->get('start_date', now()->subMonths(6)->startOfDay()));
        $endDate = Carbon::parse($request->get('end_date', now()->endOfDay()));

        // --- 2. Lógica para obtener los datos de la base de datos ---
        
        // Consulta base para filtrar por fechas
        $baseQuery = ScpSoporte::whereBetween('created_at', [$startDate, $endDate]);

        // KPI: Total de soportes en el rango de fechas
        $totalTickets = $baseQuery->count();
        
        // --- IMPORTANTE: AJUSTA ESTOS IDS ---
        // Debes ir a tu tabla 'scp_estados' y verificar cuáles son los IDs 
        // para los estados "Abierto" y "Cerrado".
        $idEstadoAbierto = 1; // <-- Reemplaza con el ID correcto
        $idEstadoCerrado = 3; // <-- Reemplaza con el ID correcto

        // KPI: Soportes abiertos
        $openTickets = (clone $baseQuery)->where('estado', $idEstadoAbierto)->count();
        
        // KPI: Soportes cerrados
        $closedTickets = (clone $baseQuery)->where('estado', $idEstadoCerrado)->count();
        
        // --- KPI: Tiempo promedio de resolución (en horas) ---
        // NUEVA LÓGICA: Calcula el tiempo desde la primera observación hasta la última observación con estado "Cerrado".
        $avgResolutionTime = ScpSoporte::selectRaw('
                AVG(TIMESTAMPDIFF(HOUR, first_obs.timestam, last_obs.timestam)) as avg_time
            ')
            // Unimos con la primera observación de cada soporte
            ->join('scp_observaciones as first_obs', function ($join) {
                $join->on('first_obs.id_scp_soporte', '=', 'scp_soportes.id')
                     ->whereRaw('first_obs.timestam = (SELECT MIN(timestam) FROM scp_observaciones WHERE id_scp_soporte = scp_soportes.id)');
            })
            // Unimos con la última observación que cierra el soporte
            ->join('scp_observaciones as last_obs', function ($join) use ($idEstadoCerrado) {
                $join->on('last_obs.id_scp_soporte', '=', 'scp_soportes.id')
                     ->where('last_obs.id_scp_estados', '=', $idEstadoCerrado)
                     ->whereRaw('last_obs.timestam = (SELECT MAX(timestam) FROM scp_observaciones WHERE id_scp_soporte = scp_soportes.id AND id_scp_estados = ?)', [$idEstadoCerrado]);
            })
            // Filtramos por soportes que están cerrados y que se crearon en el rango de fechas
            ->where('scp_soportes.estado', $idEstadoCerrado)
            ->whereBetween('scp_soportes.created_at', [$startDate, $endDate])
            // Nos aseguramos de que existan ambas observaciones para el cálculo
            ->whereNotNull('first_obs.timestam')
            ->whereNotNull('last_obs.timestam')
            ->value('avg_time');
        
        // Formatea el resultado para mostrarlo en la vista
        $avgResolutionTime = $avgResolutionTime ? round($avgResolutionTime) . ' hrs' : 'N/A';

        // --- Gráfico 1: Soportes por Mes ---
        $labelsMes = [];
        $dataMes = [];
        // CORRECCIÓN: Ahora que $startDate y $endDate son objetos Carbon, podemos usar copy()
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

        // --- 3. Pasar todos los datos a la vista ---
        return view('soportes.estadisticas.index', compact(
            'totalTickets', 
            'openTickets', 
            'closedTickets', 
            'avgResolutionTime',
            'labelsMes',
            'dataMes',
            'labelsEstado',
            'dataEstado'
        ));
    }
}