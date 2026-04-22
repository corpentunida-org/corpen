<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Auditoría de Interacciones - PDF</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
        
        /* Marca de agua elegante */
        .watermark { position: fixed; top: 40%; left: 10%; width: 80%; opacity: 0.04; font-size: 100px; font-weight: bold; text-align: center; transform: rotate(-45deg); color: #000; z-index: -1000; letter-spacing: 10px; }

        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #1e3a8a; padding-bottom: 15px; }
        .header h1 { margin: 0; color: #1e3a8a; font-size: 26px; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 5px 0 0 0; color: #64748b; font-size: 13px; }
        
        /* Caja de Insights (Inteligencia) */
        .insight-box { background-color: #f0fdfa; border-left: 4px solid #0d9488; padding: 12px; margin-bottom: 25px; border-radius: 4px; }
        .insight-title { font-weight: bold; color: #0f766e; font-size: 13px; margin-bottom: 5px; }
        .insight-text { color: #334155; font-size: 12px; margin: 0; line-height: 1.4; }

        .kpi-table { width: 100%; margin-bottom: 30px; border-collapse: separate; border-spacing: 10px 0; }
        .kpi-box { background-color: #fff; border: 1px solid #e2e8f0; padding: 15px; text-align: center; border-radius: 6px; width: 25%; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .kpi-title { font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: bold; margin-bottom: 8px; letter-spacing: 0.5px; }
        .kpi-value { font-size: 24px; font-weight: bold; color: #1e293b; margin: 0; }
        
        .kpi-primary { border-bottom: 3px solid #3b82f6; }
        .kpi-success { border-bottom: 3px solid #10b981; }
        .kpi-warning { border-bottom: 3px solid #f59e0b; }
        .kpi-danger  { border-bottom: 3px solid #ef4444; }

        .section-title { font-size: 14px; color: #1e293b; border-bottom: 1px solid #cbd5e0; padding-bottom: 5px; margin-top: 20px; margin-bottom: 12px; font-weight: bold; text-transform: uppercase; }
        
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; background-color: #fff; }
        table.data-table th { background-color: #f8fafc; color: #475569; text-align: left; padding: 10px; font-size: 10px; border-bottom: 2px solid #e2e8f0; text-transform: uppercase; }
        table.data-table td { padding: 10px; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 11px; vertical-align: middle; }
        table.data-table tr:nth-child(even) { background-color: #fcfcfc; }
        table.data-table td.text-right, table.data-table th.text-right { text-align: right; }
        table.data-table td.text-center, table.data-table th.text-center { text-align: center; }

        /* Estilos de Barra de Progreso CSS */
        .progress-bg { background-color: #e2e8f0; border-radius: 10px; width: 100%; height: 8px; margin-top: 4px; overflow: hidden;}
        .progress-bar { height: 8px; border-radius: 10px; }
        .color-success { background-color: #10b981; }
        .color-warning { background-color: #f59e0b; }
        .color-danger { background-color: #ef4444; }

        .badge { padding: 3px 6px; border-radius: 4px; font-weight: bold; font-size: 10px; }
        .badge-time { background-color: #e2e8f0; color: #475569; border: 1px solid #cbd5e0; }
        .bg-red { background-color: #fee2e2; color: #b91c1c; }
        .bg-green { background-color: #d1fae5; color: #047857; }

        .row { width: 100%; clear: both; overflow: hidden; }
        .col-half { width: 48%; float: left; }
        .col-spacer { width: 4%; float: left; height: 1px; }
        
        .page-break { page-break-before: always; }
        .footer { position: fixed; bottom: -15px; left: 0; right: 0; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 5px; }
        .rank-icon { font-size: 14px; }
    </style>
</head>
<body>

    <div class="watermark"></div><div class="header">
        <h1>Reporte Ejecutivo de Auditoría</h1>
        <p>Análisis de Rendimiento - Periodo: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> al <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong></p>
    </div>

    {{-- Resumen Inteligente (Insights Automáticos) --}}
    <div class="insight-box">
        <div class="insight-title">Resumen Analítico Automático</div>
        <div class="insight-text">
            Durante el periodo se registraron <strong>{{ $stats['total'] }} gestiones</strong> con una tasa de éxito global del <strong>{{ $tasaGlobal }}%</strong>. 
            @if($mejorAgente)
                El mayor volumen de gestión fue realizado por <strong>{{ $mejorAgente->nombre }}</strong> ({{ $mejorAgente->total }} casos). 
            @endif
            @if($agenteMasEfectivo && $agenteMasEfectivo->efectividad > 0)
                El agente con mayor precisión y efectividad operativa fue <strong>{{ $agenteMasEfectivo->nombre }}</strong> alcanzando un <strong>{{ $agenteMasEfectivo->efectividad }}%</strong> de éxito.
            @endif
            Actualmente existen <strong>{{ $stats['overdue'] }} acciones vencidas</strong> que requieren atención prioritaria de supervisión.
        </div>
    </div>

    <table class="kpi-table">
        <tr>
            <td class="kpi-box kpi-primary">
                <div class="kpi-title">Total Interacciones</div>
                <div class="kpi-value">{{ $stats['total'] ?? 0 }}</div>
            </td>
            <td class="kpi-box kpi-success">
                <div class="kpi-title">Gestiones Exitosas</div>
                <div class="kpi-value">{{ $stats['successful'] ?? 0 }}</div>
            </td>
            <td class="kpi-box kpi-warning">
                <div class="kpi-title">Pendientes</div>
                <div class="kpi-value">{{ $stats['pending'] ?? 0 }}</div>
            </td>
            <td class="kpi-box kpi-danger">
                <div class="kpi-title">Acciones Vencidas</div>
                <div class="kpi-value">{{ $stats['overdue'] ?? 0 }}</div>
            </td>
        </tr>
    </table>

    <div class="row">
        <div class="col-half">
            <div class="section-title">Canales de Atención</div>
            <table class="data-table">
                <thead><tr><th>Canal</th><th class="text-right">Volumen</th></tr></thead>
                <tbody>
                    @foreach($chartCanales['labels'] as $index => $label)
                        <tr><td>{{ $label }}</td><td class="text-right"><strong>{{ $chartCanales['data'][$index] }}</strong></td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-spacer"></div>
        <div class="col-half">
            <div class="section-title">Resultados de Tipificación</div>
            <table class="data-table">
                <thead><tr><th>Resultado</th><th class="text-right">Volumen</th></tr></thead>
                <tbody>
                    @foreach($chartResultados['labels'] as $index => $label)
                        <tr><td>{{ $label }}</td><td class="text-right"><strong>{{ $chartResultados['data'][$index] }}</strong></td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- SALTO DE PÁGINA --}}
    <div class="page-break"></div>
    <div class="watermark"></div><div class="header" style="border-bottom: 3px solid #0f766e;">
        <h1 style="color: #0f766e;">Ranking y Auditoría de Agentes</h1>
        <p>Métricas detalladas de desempeño y cumplimiento de SLAs por asesor</p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">#</th>
                <th width="25%">Nombre del Agente</th>
                <th class="text-center">Total</th>
                <th class="text-center">Éxito</th>
                <th class="text-center">Vencidas</th>
                <th class="text-center">Tiempo</th> <th width="20%">Nivel de Efectividad</th>
            </tr>
        </thead>
        <tbody>
            @forelse($agentesAuditoria as $index => $agente)
                @php
                    $colorClass = 'color-warning';
                    if($agente->efectividad >= 75) $colorClass = 'color-success';
                    if($agente->efectividad < 50) $colorClass = 'color-danger';

                    // Ranking con CSS compatible con dompdf (Oro, Plata, Bronce)
                    $rank = $index + 1;
                    $rankDisplay = $rank;
                    if($rank == 1) {
                        $rankDisplay = '<span style="background-color: #fbbf24; color: #fff; padding: 3px 7px; border-radius: 50%; font-weight: bold; font-size: 10px;">1</span>';
                    } elseif($rank == 2) {
                        $rankDisplay = '<span style="background-color: #94a3b8; color: #fff; padding: 3px 7px; border-radius: 50%; font-weight: bold; font-size: 10px;">2</span>';
                    } elseif($rank == 3) {
                        $rankDisplay = '<span style="background-color: #b45309; color: #fff; padding: 3px 7px; border-radius: 50%; font-weight: bold; font-size: 10px;">3</span>';
                    } else {
                        $rankDisplay = '<span style="color: #64748b; font-weight: bold;">' . $rank . '</span>';
                    }

                    // LÓGICA DE FORMATEO DE TIEMPO (El duration está en SEGUNDOS)
                    $tiempoTotalSegundos = $agente->tiempo_total ?? 0;
                    
                    $horas = floor($tiempoTotalSegundos / 3600);
                    $minutos = floor(($tiempoTotalSegundos % 3600) / 60);
                    $segundos = $tiempoTotalSegundos % 60;
                    
                    if ($horas > 0) {
                        $tiempoFormateado = "{$horas}h {$minutos}m {$segundos}s";
                    } elseif ($minutos > 0) {
                        $tiempoFormateado = "{$minutos}m {$segundos}s";
                    } else {
                        $tiempoFormateado = "{$segundos}s";
                    }
                @endphp
                <tr>
                    <td class="text-center">{!! $rankDisplay !!}</td>
                    <td><strong>{{ $agente->nombre }}</strong></td>
                    <td class="text-center">{{ $agente->total }}</td>
                    <td class="text-center"><span class="badge bg-green">{{ $agente->exitosas }}</span></td>
                    <td class="text-center">
                        @if($agente->vencidas > 0)
                            <span class="badge bg-red">{{ $agente->vencidas }}</span>
                        @else
                            <span style="color: #cbd5e0;">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge badge-time">{{ $tiempoFormateado }}</span>
                    </td>
                    <td>
                        <div style="display: flex; justify-content: space-between; font-size: 10px; margin-bottom: 2px;">
                            <span>Efectividad</span>
                            <strong>{{ $agente->efectividad }}%</strong>
                        </div>
                        <div class="progress-bg">
                            <div class="progress-bar {{ $colorClass }}" style="width: {{ $agente->efectividad }}%;"></div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">No hay datos de agentes para mostrar.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="row" style="margin-top: 20px;">
        <div class="col-half">
            <div class="section-title">Top 5 Clientes Auditados</div>
            <table class="data-table">
                <thead><tr><th>Cliente</th><th class="text-right">Casos</th></tr></thead>
                <tbody>
                    @foreach($chartClientes['labels'] as $index => $label)
                        <tr><td>{{ $label }}</td><td class="text-right"><strong>{{ $chartClientes['data'][$index] }}</strong></td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-spacer"></div>
        <div class="col-half">
            <div class="section-title">Impacto por Línea de Crédito</div>
            <table class="data-table">
                <thead><tr><th>Línea de Crédito</th><th class="text-right">Casos</th></tr></thead>
                <tbody>
                    @foreach($chartLineas['labels'] as $index => $label)
                        <tr><td>{{ $label }}</td><td class="text-right"><strong>{{ $chartLineas['data'][$index] }}</strong></td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer">
        Generado el {{ \Carbon\Carbon::now()->format('d/m/Y h:i A') }} • Documento Confidencial de Auditoría Operativa • Daytrack System
    </div>

</body>
</html>