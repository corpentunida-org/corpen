<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Interacciones - PDF</title>
    <style>
        /* Estilos optimizados para la generación de PDF */
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
        
        /* Contenedor de KPIs usando tablas para asegurar compatibilidad en PDFs */
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

        /* Estilos de las tablas de datos */
        .section-title {
            font-size: 14px;
            color: #2d3748;
            border-bottom: 1px solid #cbd5e0;
            padding-bottom: 5px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
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
        table.data-table td.text-right, table.data-table th.text-right {
            text-align: right;
        }

        /* Sistema de columnas simple para dompdf */
        .row { width: 100%; clear: both; overflow: hidden; }
        .col-half { width: 48%; float: left; }
        .col-spacer { width: 4%; float: left; height: 1px; }
        
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
        <h1>Informe Analítico de Interacciones</h1>
        <p>Periodo reportado: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> al <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong></p>
    </div>

    {{-- Tarjetas de Métricas (KPIs) --}}
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

    {{-- Primera Fila de Tablas --}}
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

    {{-- Segunda Fila de Tablas --}}
    <div class="row">
        <div class="col-half">
            <div class="section-title">Top 5 Agentes (Interacciones)</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Agente</th>
                        <th class="text-right">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($chartAgentes['labels']) && count($chartAgentes['labels']) > 0)
                        @foreach($chartAgentes['labels'] as $index => $label)
                            <tr>
                                <td>{{ $label }}</td>
                                <td class="text-right">{{ $chartAgentes['data'][$index] }}</td>
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
            <div class="section-title">Top 5 Agentes (Seguimientos)</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Agente</th>
                        <th class="text-right">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($chartSeguimientosAgentes['labels']) && count($chartSeguimientosAgentes['labels']) > 0)
                        @foreach($chartSeguimientosAgentes['labels'] as $index => $label)
                            <tr>
                                <td>{{ $label }}</td>
                                <td class="text-right">{{ $chartSeguimientosAgentes['data'][$index] }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr><td colspan="2" class="text-center">No hay datos disponibles</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tercera Fila de Tablas --}}
    <div class="row">
        <div class="col-half">
            <div class="section-title">Top 5 Clientes</div>
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

    {{-- Cuarta Fila (Última) --}}
    <div class="row">
        <div class="col-half">
            <div class="section-title">Top 5 Distritos</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Distrito</th>
                        <th class="text-right">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($chartDistritos['labels']) && count($chartDistritos['labels']) > 0)
                        @foreach($chartDistritos['labels'] as $index => $label)
                            <tr>
                                <td>{{ $label }}</td>
                                <td class="text-right">{{ $chartDistritos['data'][$index] }}</td>
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