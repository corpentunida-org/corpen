<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Auditoría de Interacciones - PDF</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #4a90e2;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #2d3748;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #718096;
            font-size: 14px;
        }
        
        .kpi-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: separate;
            border-spacing: 10px 0;
        }
        .kpi-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            width: 25%;
        }
        .kpi-title {
            font-size: 11px;
            color: #718096;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .kpi-value {
            font-size: 22px;
            font-weight: bold;
            color: #2d3748;
            margin: 0;
        }
        .kpi-primary { border-top: 3px solid #4a90e2; }
        .kpi-success { border-top: 3px solid #2ecc71; }
        .kpi-warning { border-top: 3px solid #f39c12; }
        .kpi-danger  { border-top: 3px solid #e74c3c; }

        .section-title {
            font-size: 14px;
            color: #2d3748;
            border-bottom: 1px solid #cbd5e0;
            padding-bottom: 5px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th {
            background-color: #edf2f7;
            color: #4a5568;
            text-align: left;
            padding: 8px;
            font-size: 11px;
            border-bottom: 2px solid #cbd5e0;
        }
        table.data-table td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
            color: #4a5568;
            font-size: 12px;
        }
        table.data-table td.text-right, table.data-table th.text-right { text-align: right; }
        table.data-table td.text-center, table.data-table th.text-center { text-align: center; }

        .row { width: 100%; clear: both; overflow: hidden; }
        .col-half { width: 48%; float: left; }
        .col-spacer { width: 4%; float: left; height: 1px; }
        
        .page-break { page-break-before: always; }
        .badge-success { color: #2ecc71; font-weight: bold; }
        .badge-danger { color: #e74c3c; font-weight: bold; }
        .badge-warning { color: #f39c12; font-weight: bold; }

        .footer {
            position: fixed;
            bottom: -10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #a0aec0;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Auditoría y Rendimiento de Interacciones</h1>
        <p>Periodo evaluado: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> al <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong></p>
    </div>

    {{-- Resumen Ejecutivo General --}}
    <div class="section-title">Resumen Ejecutivo General</div>
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

    {{-- Fila de Tablas Resumen --}}
    <div class="row">
        <div class="col-half">
            <div class="section-title">Interacciones por Canal</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Canal</th>
                        <th class="text-right">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($chartCanales['labels']) && count($chartCanales['labels']) > 0)
                        @foreach($chartCanales['labels'] as $index => $label)
                            <tr>
                                <td>{{ $label }}</td>
                                <td class="text-right">{{ $chartCanales['data'][$index] }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr><td colspan="2" class="text-center">No hay datos disponibles</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="col-spacer"></div>

        <div class="col-half">
            <div class="section-title">Distribución de Resultados</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Resultado</th>
                        <th class="text-right">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($chartResultados['labels']) && count($chartResultados['labels']) > 0)
                        @foreach($chartResultados['labels'] as $index => $label)
                            <tr>
                                <td>{{ $label }}</td>
                                <td class="text-right">{{ $chartResultados['data'][$index] }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr><td colspan="2" class="text-center">No hay datos disponibles</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- SALTO DE PÁGINA PARA LA AUDITORÍA DE AGENTES --}}
    <div class="page-break"></div>

    <div class="header">
        <h1>Detalle de Auditoría por Agente</h1>
        <p>Desglose de rendimiento y cumplimiento por asesor</p>
    </div>

    {{-- NUEVA SECCIÓN: Tabla Maestra de Auditoría por Agente --}}
    <table class="data-table">
        <thead>
            <tr>
                <th>Nombre del Agente</th>
                <th class="text-center">Total Gestiones</th>
                <th class="text-center">Exitosas</th>
                <th class="text-center">Pendientes</th>
                <th class="text-center">Vencidas</th>
                <th class="text-center">Seguimientos</th>
                <th class="text-right">Efectividad (%)</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($agentesAuditoria) && count($agentesAuditoria) > 0)
                @foreach($agentesAuditoria as $agente)
                    @php
                        // Calculando el porcentaje de éxito para colorearlo
                        $total = $agente->total > 0 ? $agente->total : 1;
                        $efectividad = round(($agente->exitosas / $total) * 100, 1);
                        
                        $claseEfectividad = 'badge-warning';
                        if($efectividad >= 80) $claseEfectividad = 'badge-success';
                        if($efectividad < 50) $claseEfectividad = 'badge-danger';
                    @endphp
                    <tr>
                        <td><strong>{{ $agente->nombre }}</strong></td>
                        <td class="text-center">{{ $agente->total }}</td>
                        <td class="text-center badge-success">{{ $agente->exitosas }}</td>
                        <td class="text-center">{{ $agente->pendientes }}</td>
                        <td class="text-center badge-danger">{{ $agente->vencidas }}</td>
                        <td class="text-center">{{ $agente->seguimientos }}</td>
                        <td class="text-right {{ $claseEfectividad }}">{{ $efectividad }}%</td>
                    </tr>
                @endforeach
            @else
                <tr><td colspan="7" class="text-center">No hay datos suficientes para auditar agentes en este periodo.</td></tr>
            @endif
        </tbody>
    </table>

    <div class="row" style="margin-top: 30px;">
        <div class="col-half">
            <div class="section-title">Top 5 Clientes Impactados</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th class="text-right">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($chartClientes['labels']) && count($chartClientes['labels']) > 0)
                        @foreach($chartClientes['labels'] as $index => $label)
                            <tr>
                                <td>{{ $label }}</td>
                                <td class="text-right">{{ $chartClientes['data'][$index] }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr><td colspan="2" class="text-center">No hay datos disponibles</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="col-spacer"></div>

        <div class="col-half">
            <div class="section-title">Interacciones por Línea de Crédito</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Línea de Crédito</th>
                        <th class="text-right">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($chartLineas['labels']) && count($chartLineas['labels']) > 0)
                        @foreach($chartLineas['labels'] as $index => $label)
                            <tr>
                                <td>{{ $label }}</td>
                                <td class="text-right">{{ $chartLineas['data'][$index] }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr><td colspan="2" class="text-center">No hay datos disponibles</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer">
        Generado el {{ \Carbon\Carbon::now()->format('d/m/Y h:i A') }} - Sistema Daytrack
    </div>

</body>
</html>