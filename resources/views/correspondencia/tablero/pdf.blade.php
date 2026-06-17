<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Ejecutivo de Gestión</title>
    <style>
        /* Configuración de Página y Tipografía Base */
        @page { margin: 1.5cm 1.5cm 2cm 1.5cm; size: a4 landscape; }
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            color: #334155; 
            font-size: 10px; 
            line-height: 1.5; 
        }

        /* Línea corporativa superior (Sutil detalle de branding) */
        .brand-line {
            position: absolute;
            top: -1.5cm;
            left: -1.5cm;
            right: -1.5cm;
            height: 4px;
            background-color: #0f172a;
        }

        /* Encabezado del Documento */
        .report-header { 
            margin-bottom: 20px; 
            border-bottom: 1px solid #cbd5e1; 
            padding-bottom: 12px; 
        }
        .report-title { 
            color: #0f172a; 
            font-size: 20px; 
            margin: 0 0 5px 0; 
            font-weight: 300; 
            text-transform: uppercase; 
            letter-spacing: 1.5px; 
        }
        .report-meta { 
            color: #64748b; 
            font-size: 8.5px; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
        }

        /* Contenedor de Filtros (Estilo Nota Ejecutiva) */
        .filter-banner { 
            background: transparent; 
            border-left: 2px solid #0f172a; 
            padding: 4px 12px; 
            margin-bottom: 20px; 
            font-size: 9px; 
            color: #475569;
            letter-spacing: 0.3px;
        }
        .filter-banner strong { 
            color: #0f172a; 
            font-weight: bold;
        }
        .filter-banner u {
            text-decoration: none;
            border-bottom: 1px dotted #94a3b8;
            padding-bottom: 1px;
        }

        /* ESTRUCTURA DASHBOARD COMPACTO (KPIs + GRÁFICOS) */
        .dashboard-matrix {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 20px;
        }
        .matrix-left {
            display: table-cell;
            width: 45%;
            vertical-align: middle;
        }
        .matrix-right {
            display: table-cell;
            width: 55%;
            vertical-align: middle;
            padding-left: 40px;
            border-left: 1px solid #e2e8f0;
        }

        /* Resumen de KPIs Numéricos */
        .kpi-grid { display: table; width: 100%; border-spacing: 10px 0px; margin-left: -10px; }
        .kpi-box { display: table-cell; width: 25%; padding: 10px; border: 1px solid #e2e8f0; background: #f8fafc; border-radius: 4px; }
        .kpi-val { font-size: 18px; font-weight: 300; color: #0f172a; display: block; line-height: 1; margin-bottom: 4px; }
        .kpi-lbl { font-size: 7.5px; color: #64748b; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; }

        /* SECCIÓN GRÁFICA ULTRA-CORPORATIVA */
        .chart-title {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #0f172a;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        /* Gráfico de Barra Acumulada (Macro Distribución) */
        .stacked-bar-container {
            width: 100%;
            margin-bottom: 15px;
        }
        .stacked-bar-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            border-radius: 3px;
            overflow: hidden;
        }
        .stacked-bar-table td {
            padding: 0;
            height: 14px;
            border: none;
        }
        
        /* Leyendas del Gráfico */
        .chart-legend {
            display: table;
            width: 100%;
            margin-top: 6px;
        }
        .legend-item {
            display: table-cell;
            font-size: 8.5px;
            color: #475569;
        }
        .legend-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            margin-right: 4px;
            border-radius: 1px;
        }

        /* Tabla de Datos (Estilo Consultoría / Reporte Financiero) */
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
        }
        th { 
            border-top: 1px solid #0f172a; 
            border-bottom: 1px solid #0f172a; 
            color: #0f172a; 
            padding: 9px 8px; 
            font-size: 8.5px; 
            text-align: left; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
            font-weight: bold;
        }
        td.data-cell { 
            padding: 9px 8px; 
            border-bottom: 1px solid #f1f5f9; 
            font-size: 9.5px; 
            color: #334155;
            vertical-align: top;
        }
        
        .col-id { font-weight: bold; color: #0f172a; }
        .col-estado { font-weight: 600; }

        /* Footer Ejecutivo */
        .footer { 
            position: fixed; 
            bottom: -1cm; 
            width: 100%; 
            font-size: 8px; 
            color: #94a3b8; 
            border-top: 1px solid #e2e8f0; 
            padding-top: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .footer table { width: 100%; border: none; margin: 0; }
        .footer td { padding: 0; border: none; font-size: 8px; }
        .page-number:before { content: counter(page); }
    </style>
</head>
<body>
    <div class="brand-line"></div>

    <div class="report-header">
        <h2 class="report-title">Informe de Gestión Documental</h2>
        <p class="report-meta">Fecha de emisión: {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp; Generado por: {{ auth()->user()->name }}</p>
    </div>

    {{-- Información del rango tomado por el PDF --}}
    <div class="filter-banner">
        <strong>PERÍODO ACTIVO:</strong> Desde el <u>{{ $filtrosActivos['desde'] }}</u> hasta el <u>{{ $filtrosActivos['hasta'] }}</u>.
        @if(request()->filled('estado_kpi'))
            &nbsp;|&nbsp; <strong>ESTADO:</strong> {{ mb_strtoupper(request('estado_kpi'), 'UTF-8') }}
        @endif
        @if(request()->filled('search'))
            &nbsp;|&nbsp; <strong>BÚSQUEDA:</strong> "{{ mb_strtoupper(request('search'), 'UTF-8') }}"
        @endif
    </div>

    {{-- CÁLCULOS MATEMÁTICOS SEGUROS PARA LOS GRÁFICOS VISUALES --}}
    @php
        $total = $resumenKpis['Total'] ?? 0;
        $totalCalculable = $total > 0 ? $total : 1;
        
        $pctCompletados = (($resumenKpis['Completados'] ?? 0) / $totalCalculable) * 100;
        $pctProceso     = (($resumenKpis['En Proceso'] ?? 0) / $totalCalculable) * 100;
        $pctVencidos    = (($resumenKpis['Vencidos'] ?? 0) / $totalCalculable) * 100;
    @endphp

    {{-- INTERFAZ COMPACTA EXECUTIVA CON INTERPRETACIÓN EN SEGUNDOS --}}
    <div class="dashboard-matrix">
        <div class="matrix-left">
            <div class="chart-title" style="margin-left: 2px;">Métricas Clave del Período</div>
            <div class="kpi-grid">
                @foreach($resumenKpis as $label => $val)
                    <div class="kpi-box">
                        <span class="kpi-val">{{ number_format($val, 0, ',', '.') }}</span>
                        <span class="kpi-lbl">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="matrix-right">
            <div class="chart-title">Distribución Visual del Estado Operativo (%)</div>
            
            <div class="stacked-bar-container">
                @if($total > 0)
                    <table class="stacked-bar-table">
                        <tr>
                            @if($pctCompletados > 0)
                                <td style="width: {{ $pctCompletados }}%; background-color: #0f172a;" title="Completados"></td>
                            @endif
                            @if($pctProceso > 0)
                                <td style="width: {{ $pctProceso }}%; background-color: #64748b;" title="En Proceso"></td>
                            @endif
                            @if($pctVencidos > 0)
                                <td style="width: {{ $pctVencidos }}%; background-color: #991b1b;" title="Vencidos"></td>
                            @endif
                        </tr>
                    </table>
                @else
                    <div style="background: #f1f5f9; height: 14px; border-radius: 3px; text-align: center; color: #94a3b8; font-size: 8px; line-height: 14px;">
                        SIN DATOS DISPONIBLES PARA REPRESENTACIÓN
                    </div>
                @endif

                <div class="chart-legend">
                    <div class="legend-item">
                        <span class="legend-dot" style="background-color: #0f172a;"></span>
                        Completados: <strong>{{ number_format($pctCompletados, 1) }}%</strong>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot" style="background-color: #64748b;"></span>
                        En Proceso: <strong>{{ number_format($pctProceso, 1) }}%</strong>
                    </div>
                    <div class="legend-item" style="text-align: right;">
                        <span class="legend-dot" style="background-color: #991b1b;"></span>
                        Vencidos: <strong>{{ number_format($pctVencidos, 1) }}%</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 12%;">Radicado</th>
                <th style="width: 45%;">Asunto Detallado</th>
                <th style="width: 13%;">Fecha Solicitud</th>
                <th style="width: 15%;">Estado Actual</th>
                <th style="width: 15%;">Responsable Asignado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($correspondencias as $c)
            <tr>
                <td class="data-cell col-id">#{{ $c->id_radicado }}</td>
                <td class="data-cell">{{ Str::limit($c->asunto, 90) }}</td>
                <td class="data-cell">{{ $c->fecha_solicitud->format('d/m/Y') }}</td>
                <td class="data-cell col-estado">{{ $c->estado->nombre ?? 'Sin estado' }}</td>
                <td class="data-cell">{{ $c->usuario->name ?? 'Sin asignar' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 30px; color: #94a3b8; font-style: italic;">
                    No se encontraron registros coincidentes con los parámetros y rango de fechas establecidos.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table style="margin-top: 0; width: 100%;">
            <tr>
                <td style="text-align: left; color: #94a3b8;">Documento Confidencial - Uso Interno Privado</td>
                <td style="text-align: right; color: #94a3b8;">Página <span class="page-number"></span></td>
            </tr>
        </table>
    </div>
</body>
</html>