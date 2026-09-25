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
        .insight-box { background-color: #f0fdfa; border-left: 4px solid #0d9488; padding: 12px; margin-bottom: 20px; border-radius: 4px; }
        .insight-title { font-weight: bold; color: #0f766e; font-size: 13px; margin-bottom: 5px; }
        .insight-text { color: #334155; font-size: 12px; margin: 0; line-height: 1.4; }

        .kpi-table { width: 100%; margin-bottom: 20px; border-collapse: separate; border-spacing: 10px 0; }
        .kpi-box { background-color: #fff; border: 1px solid #e2e8f0; padding: 15px; text-align: center; border-radius: 6px; width: 25%; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .kpi-title { font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: bold; margin-bottom: 8px; letter-spacing: 0.5px; }
        .kpi-value { font-size: 24px; font-weight: bold; color: #1e293b; margin: 0; }

        .kpi-primary { border-bottom: 3px solid #3b82f6; }
        .kpi-success { border-bottom: 3px solid #10b981; }
        .kpi-warning { border-bottom: 3px solid #f59e0b; }
        .kpi-danger  { border-bottom: 3px solid #ef4444; }

        .section-title { font-size: 13px; color: #1e293b; border-bottom: 1px solid #cbd5e0; padding-bottom: 5px; margin-top: 4px; margin-bottom: 10px; font-weight: bold; text-transform: uppercase; }
        .sin-datos { color: #94a3b8; font-size: 11px; margin: 0 0 15px 0; }

        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; background-color: #fff; }
        table.data-table th { background-color: #f8fafc; color: #475569; text-align: left; padding: 8px 10px; font-size: 10px; border-bottom: 2px solid #e2e8f0; text-transform: uppercase; }
        table.data-table td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 11px; vertical-align: middle; }
        table.data-table tr:nth-child(even) { background-color: #fcfcfc; }
        table.data-table tr { page-break-inside: avoid; }
        table.data-table thead { display: table-header-group; } /* repite encabezado si la tabla salta de página */
        table.data-table td.text-right, table.data-table th.text-right { text-align: right; }
        table.data-table td.text-center, table.data-table th.text-center { text-align: center; }

        /* Gráficos de barras horizontales (reemplazan a Chart.js, que no corre en el PDF) —
           todo con <table>, sin flexbox: dompdf 2.x es CSS 2.1 y el flex quedaba mal armado. */
        table.bar-chart { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        table.bar-chart tr { page-break-inside: avoid; }
        table.bar-chart td { padding: 4px 0; vertical-align: middle; font-size: 9.5px; word-wrap: break-word; }
        table.bar-chart td.bar-label { width: 36%; color: #475569; padding-right: 8px; }
        table.bar-chart td.bar-track { width: 49%; }
        table.bar-chart td.bar-value { width: 15%; text-align: right; font-weight: bold; color: #1e293b; padding-left: 8px; }
        .bar-track-bg { background-color: #eef2f7; border-radius: 6px; height: 13px; width: 100%; }
        .bar-track-fill { height: 13px; border-radius: 6px; }

        /* Barra apilada (sustituye la dona de "Distribución de Resultados": dompdf no dibuja
           arcos SVG de forma confiable, una barra 100% apilada + leyenda es el equivalente
           seguro para imprimir). */
        .stacked-bar { width: 100%; height: 20px; border-radius: 6px; background-color: #eef2f7; margin-bottom: 12px; }
        .stacked-segment { height: 20px; float: left; }
        .stacked-segment:first-child { border-radius: 6px 0 0 6px; }
        table.legend-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        table.legend-table td { padding: 3px 0; font-size: 10.5px; color: #334155; }
        .legend-swatch { width: 14px; }
        .legend-swatch span { display: block; width: 10px; height: 10px; border-radius: 2px; }
        .legend-value { text-align: right; font-weight: bold; }
        .legend-pct { font-weight: normal; color: #94a3b8; }

        /* Estilos de Barra de Progreso (Efectividad) — reemplazan el "display:flex;
           justify-content: space-between" anterior por floats simples, que dompdf sí calcula
           bien (ese flex era lo que dejaba la etiqueta y el % montados uno sobre otro). */
        .progress-label { overflow: hidden; font-size: 10px; margin-bottom: 2px; }
        .progress-label .lbl { float: left; color: #64748b; }
        .progress-label .val { float: right; font-weight: bold; color: #1e293b; }
        .progress-bg { background-color: #e2e8f0; border-radius: 10px; width: 100%; height: 8px; overflow: hidden; clear: both; }
        .progress-bar { height: 8px; border-radius: 10px; }
        .color-success { background-color: #10b981; }
        .color-warning { background-color: #f59e0b; }
        .color-danger { background-color: #ef4444; }

        .badge { padding: 3px 6px; border-radius: 4px; font-weight: bold; font-size: 10px; }
        .badge-time { background-color: #e2e8f0; color: #475569; border: 1px solid #cbd5e0; }
        .bg-red { background-color: #fee2e2; color: #b91c1c; }
        .bg-green { background-color: #d1fae5; color: #047857; }

        /* Grilla de 2 columnas — hecha con <table>, NO con floats. dompdf 2.x combina mal los
           floats con los saltos de página: un bloque flotado que no cabe entero en lo que queda
           de la página no salta completo a la siguiente, sino que a veces corta el texto a la
           mitad de la palabra justo en el borde (la causa real del "texto montado" al imprimir
           tableros con nombres largos). Una tabla con "page-break-inside: avoid" si no cabe la
           empuja entera a la página siguiente, sin partirla. */
        table.two-col { width: 100%; border-collapse: collapse; table-layout: fixed; page-break-inside: avoid; margin-bottom: 6px; }
        table.two-col td.col-half-cell { width: 48%; vertical-align: top; }
        table.two-col td.col-spacer-cell { width: 4%; }

        .page-break { page-break-before: always; }
        .footer { position: fixed; bottom: -15px; left: 0; right: 0; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 5px; }
        .rank-icon { font-size: 14px; }
    </style>
</head>
<body>

    {{-- ===================== PÁGINA 1: Resumen ejecutivo ===================== --}}
    {{-- Individual (modo=propias): es el informe personal de quien lo pide, no una auditoría de
         terceros — título y encabezado lo dejan claro, y más abajo se ocultan las secciones que
         comparan contra otros agentes (no tienen sentido con una sola persona en el alcance). --}}
    <div class="watermark"></div><div class="header">
        <h1>{{ $modo === 'propias' ? 'Informe Individual de Gestión' : 'Reporte Ejecutivo de Auditoría' }}</h1>
        <p>
            @if ($modo === 'propias')
                Informe personal de <strong>{{ $impresoPor }}</strong> —
            @endif
            Periodo: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> al <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
        </p>
    </div>

    {{-- Resumen Inteligente (Insights Automáticos) --}}
    <div class="insight-box">
        <div class="insight-title">Resumen Analítico Automático</div>
        <div class="insight-text">
            @if ($modo === 'propias')
                Durante el periodo registraste <strong>{{ $stats['total'] }} gestiones</strong> con una tasa de éxito del <strong>{{ $tasaGlobal }}%</strong>.
                Actualmente tienes <strong>{{ $stats['overdue'] }} acciones vencidas</strong> que requieren tu atención.
            @else
                Durante el periodo se registraron <strong>{{ $stats['total'] }} gestiones</strong> con una tasa de éxito global del <strong>{{ $tasaGlobal }}%</strong>.
                @if($mejorAgente)
                    El mayor volumen de gestión fue realizado por <strong>{{ $mejorAgente->nombre }}</strong> ({{ $mejorAgente->total }} casos).
                @endif
                @if($agenteMasEfectivo && $agenteMasEfectivo->efectividad > 0)
                    El agente con mayor precisión y efectividad operativa fue <strong>{{ $agenteMasEfectivo->nombre }}</strong> alcanzando un <strong>{{ $agenteMasEfectivo->efectividad }}%</strong> de éxito.
                @endif
                Actualmente existen <strong>{{ $stats['overdue'] }} acciones vencidas</strong> que requieren atención prioritaria de supervisión.
            @endif
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

    {{-- Interacciones por Día — tendencia dentro del rango, a ancho completo (mismo criterio que
         en pantalla: aplica igual en individual que en área/todos). Si el rango es largo (> 45
         días) chartInteraccionesPorDia() ya viene agrupado por semana en vez de por día, para
         que la lista no se vuelva interminable. --}}
    @php $diasHabilesSinRegistroPdf = $chartInteraccionesPorDia['dias_habiles_sin_registro'] ?? 0; @endphp
    @include('interactions.reportes.partials._pdf_bar_chart', [
        'titulo' => 'Interacciones por Día',
        'labels' => $chartInteraccionesPorDia['labels'] ?? [],
        'data' => $chartInteraccionesPorDia['data'] ?? [],
        'color' => '#3b82f6',
        'nota' => $diasHabilesSinRegistroPdf.' día(s) hábil(es) sin registrar interacciones en el periodo (excluye sábados, domingos y festivos)',
        'notaColor' => $diasHabilesSinRegistroPdf > 0 ? '#b91c1c' : '#15803d',
    ])

    {{-- Distribución de Resultados (barra apilada + leyenda) y Canales (barras) — mismos dos
         tableros que abren el informe en pantalla. --}}
    <table class="two-col">
        <tr>
            <td class="col-half-cell">
                @php
                    $totalResultados = array_sum($chartResultados['data'] ?? []);
                    $paletaResultados = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#14b8a6', '#f472b6'];
                @endphp
                <div class="section-title">Distribución de Resultados</div>
                @if ($totalResultados > 0)
                    <div class="stacked-bar">
                        @foreach ($chartResultados['labels'] as $i => $label)
                            @php
                                $valor = $chartResultados['data'][$i] ?? 0;
                                $pct = round(($valor / $totalResultados) * 100, 1);
                                $color = $paletaResultados[$i % count($paletaResultados)];
                            @endphp
                            @if ($pct > 0)
                                <div class="stacked-segment" style="width: {{ $pct }}%; background-color: {{ $color }};"></div>
                            @endif
                        @endforeach
                    </div>
                    <table class="legend-table">
                        @foreach ($chartResultados['labels'] as $i => $label)
                            @php
                                $valor = $chartResultados['data'][$i] ?? 0;
                                $pct = round(($valor / $totalResultados) * 100, 1);
                                $color = $paletaResultados[$i % count($paletaResultados)];
                            @endphp
                            <tr>
                                <td class="legend-swatch"><span style="background-color: {{ $color }};"></span></td>
                                <td>{{ $label }}</td>
                                <td class="legend-value">{{ $valor }} <span class="legend-pct">({{ $pct }}%)</span></td>
                            </tr>
                        @endforeach
                    </table>
                @else
                    <p class="sin-datos">Sin datos para este periodo.</p>
                @endif
            </td>
            <td class="col-spacer-cell"></td>
            <td class="col-half-cell">
                @include('interactions.reportes.partials._pdf_bar_chart', [
                    'titulo' => 'Interacciones por Canal',
                    'labels' => $chartCanales['labels'] ?? [],
                    'data' => $chartCanales['data'] ?? [],
                    'color' => '#3b82f6',
                ])
            </td>
        </tr>
    </table>

    {{-- SALTO DE PÁGINA --}}
    <div class="page-break"></div>
    <div class="watermark"></div><div class="header" style="border-bottom: 3px solid #0f766e;">
        <h1 style="color: #0f766e;">{{ $modo === 'propias' ? 'Mi Desempeño' : 'Ranking y Auditoría de Agentes' }}</h1>
        <p>
            {{ $modo === 'propias'
                ? 'Detalle individual de gestión y cumplimiento de SLA'
                : 'Métricas detalladas de desempeño y cumplimiento de SLAs por asesor' }}
        </p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">#</th>
                <th width="20%">Nombre del Agente</th>
                <th width="12%">Área</th>
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
                    <td>{{ $agente->area }}</td>
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
                        <div class="progress-label"><span class="lbl">Efectividad</span><span class="val">{{ $agente->efectividad }}%</span></div>
                        <div class="progress-bg">
                            <div class="progress-bar {{ $colorClass }}" style="width: {{ $agente->efectividad }}%;"></div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">No hay datos de agentes para mostrar.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Indicadores por Área — mismas métricas que la tabla de agentes, sumadas por área, para
         comparar Cartera vs Seguros (o las que se activen) sin sumar filas a mano. Solo aporta
         cuando hay más de un agente en el alcance; en el informe individual (modo=propias)
         siempre saldría una sola fila idéntica a la tabla de arriba, así que se omite. --}}
    @if ($modo !== 'propias')
        <div class="section-title" style="margin-top: 8px;">Indicadores por Área</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="20%">Área</th>
                    <th class="text-center">Agentes</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Éxito</th>
                    <th class="text-center">Pendientes</th>
                    <th class="text-center">Vencidas</th>
                    <th width="20%">Nivel de Efectividad</th>
                </tr>
            </thead>
            <tbody>
                @forelse($areasAuditoria as $area)
                    @php
                        $colorClassArea = 'color-warning';
                        if ($area->efectividad >= 75) $colorClassArea = 'color-success';
                        if ($area->efectividad < 50) $colorClassArea = 'color-danger';
                    @endphp
                    <tr>
                        <td><strong>{{ $area->area }}</strong></td>
                        <td class="text-center">{{ $area->agentes }}</td>
                        <td class="text-center">{{ $area->total }}</td>
                        <td class="text-center"><span class="badge bg-green">{{ $area->exitosas }}</span></td>
                        <td class="text-center">{{ $area->pendientes }}</td>
                        <td class="text-center">
                            @if($area->vencidas > 0)
                                <span class="badge bg-red">{{ $area->vencidas }}</span>
                            @else
                                <span style="color: #cbd5e0;">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="progress-label"><span class="lbl">Efectividad</span><span class="val">{{ $area->efectividad }}%</span></div>
                            <div class="progress-bg">
                                <div class="progress-bar {{ $colorClassArea }}" style="width: {{ $area->efectividad }}%;"></div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No hay datos por área para mostrar.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- ===================== PÁGINA 3: Tableros complementarios ===================== --}}
    <div class="page-break"></div>
    <div class="watermark"></div><div class="header" style="border-bottom: 3px solid #6610f2;">
        <h1 style="color: #6610f2;">Tableros Complementarios</h1>
        <p>Mismos gráficos del panel en pantalla, listos para imprimir</p>
    </div>

    {{-- Comparativo entre agentes — solo tiene sentido con más de uno en el alcance; en el
         informe individual se omite (sería una sola barra comparándose contra nadie). --}}
    @if ($modo !== 'propias')
        @include('interactions.reportes.partials._pdf_bar_chart', [
            'titulo' => 'Top 5 Agentes (Interacciones)',
            'labels' => $agentesAuditoria->take(5)->pluck('nombre')->toArray(),
            'data' => $agentesAuditoria->take(5)->pluck('total')->toArray(),
            'color' => '#9b59b6',
        ])
    @endif

    <table class="two-col">
        <tr>
            <td class="col-half-cell">
                @include('interactions.reportes.partials._pdf_bar_chart', [
                    'titulo' => 'Top 5 Asociados',
                    'labels' => $chartClientes['labels'] ?? [],
                    'data' => $chartClientes['data'] ?? [],
                    'color' => '#f39c12',
                ])
            </td>
            <td class="col-spacer-cell"></td>
            <td class="col-half-cell">
                @include('interactions.reportes.partials._pdf_bar_chart', [
                    'titulo' => 'Top 5 Distritos',
                    'labels' => $chartDistritos['labels'] ?? [],
                    'data' => $chartDistritos['data'] ?? [],
                    'color' => '#e74c3c',
                ])
            </td>
        </tr>
    </table>

    <table class="two-col">
        <tr>
            <td class="col-half-cell">
                @include('interactions.reportes.partials._pdf_bar_chart', [
                    'titulo' => 'Impacto por Línea',
                    'labels' => $chartLineas['labels'] ?? [],
                    'data' => $chartLineas['data'] ?? [],
                    'color' => '#64748b',
                ])
            </td>
            <td class="col-spacer-cell"></td>
            <td class="col-half-cell">
                @php $maxAccion = 0; foreach (($chartAccionesAgentes['labels'] ?? []) as $i => $l) { $maxAccion = max($maxAccion, ($chartAccionesAgentes['pendientes'][$i] ?? 0) + ($chartAccionesAgentes['vencidas'][$i] ?? 0)); } @endphp
                <div class="section-title">{{ $modo === 'propias' ? 'Mis Vencidas y Pendientes' : 'Vencidas y Pendientes por Agente' }}</div>
                @if (empty($chartAccionesAgentes['labels'] ?? []))
                    <p class="sin-datos">Sin datos para este periodo.</p>
                @else
                    <table class="bar-chart">
                        @foreach ($chartAccionesAgentes['labels'] as $i => $label)
                            @php
                                $pend = $chartAccionesAgentes['pendientes'][$i] ?? 0;
                                $venc = $chartAccionesAgentes['vencidas'][$i] ?? 0;
                                $total = $pend + $venc;
                                $pctPend = $maxAccion > 0 ? round(($pend / $maxAccion) * 100) : 0;
                                $pctVenc = $maxAccion > 0 ? round(($venc / $maxAccion) * 100) : 0;
                            @endphp
                            <tr>
                                <td class="bar-label">{{ $label }}</td>
                                <td class="bar-track">
                                    <div class="bar-track-bg">
                                        <div class="bar-track-fill" style="width: {{ $pctPend }}%; background-color: #f59e0b; float: left;"></div>
                                        <div class="bar-track-fill" style="width: {{ $pctVenc }}%; background-color: #ef4444; float: left;"></div>
                                    </div>
                                </td>
                                <td class="bar-value">{{ $total }}</td>
                            </tr>
                        @endforeach
                    </table>
                    <table class="legend-table">
                        <tr>
                            <td class="legend-swatch"><span style="background-color: #f59e0b;"></span></td>
                            <td>Pendientes</td>
                            <td class="legend-swatch"><span style="background-color: #ef4444;"></span></td>
                            <td>Vencidas</td>
                        </tr>
                    </table>
                @endif
            </td>
        </tr>
    </table>

    {{-- Motivos de No Efectivo — solo aparece si hay al menos una interacción con motivo
         asignado en el rango (interacciones anteriores a esta funcionalidad no lo tienen). --}}
    @if (count($chartMotivosNoEfectivo['labels'] ?? []) > 0)
        @include('interactions.reportes.partials._pdf_bar_chart', [
            'titulo' => 'Motivos de No Efectivo',
            'labels' => $chartMotivosNoEfectivo['labels'] ?? [],
            'data' => $chartMotivosNoEfectivo['data'] ?? [],
            'color' => '#e74c3c',
        ])
    @endif

    <div class="footer">
        <strong>CORPENTUNIDA</strong> • Impreso por {{ $impresoPor }} el {{ \Carbon\Carbon::now()->format('d/m/Y h:i A') }} • Documento Confidencial de Auditoría Operativa
    </div>

</body>
</html>
