<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Lote API-{{ str_pad($numeroBloque, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #4a90e2;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            color: #2c3e50;
        }
        .header p {
            margin: 4px 0 0 0;
            color: #64748b;
            font-size: 11px;
        }
        .kpi-container {
            width: 100%;
            margin-bottom: 20px;
        }
        .kpi-box {
            display: inline-block;
            width: 32%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px;
            box-sizing: border-box;
            text-align: center;
        }
        .kpi-box .title {
            font-size: 10px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
        }
        .kpi-box .value {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-top: 5px;
        }
        h3 {
            font-size: 13px;
            color: #2c3e50;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 5px;
            margin-top: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        /* Barras estadísticas puras en CSS para DomPDF */
        .stat-group {
            margin-bottom: 12px;
        }
        .stat-label {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            font-weight: bold;
            color: #475569;
            margin-bottom: 4px;
        }
        .progress-bar-container {
            width: 100%;
            background-color: #f1f5f9;
            border-radius: 4px;
            height: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .progress-bar-fill {
            height: 100%;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th, table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
            font-size: 11px;
        }
        table th {
            background-color: #f1f5f9;
            color: #475569;
            text-transform: uppercase;
            font-size: 10px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Reporte Estadístico de Lote: API-{{ str_pad($numeroBloque, 4, '0', STR_PAD_LEFT) }}</h2>
        <p><strong>Periodo:</strong> {{ $textoPeriodo }} | <strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}</p>
        <p><strong>Descripción:</strong> {{ $bloqueInfo->descripcion ?? 'Sin descripción registrada' }}</p>
    </div>

    <!-- KPIs Principales -->
    <div class="kpi-container">
        <div class="kpi-box">
            <div class="title">Total Operaciones</div>
            <div class="value">{{ number_format($kpi['total_operaciones'] ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="kpi-box">
            <div class="title">Generados</div>
            <div class="value" style="color: #2e7d32;">{{ number_format($kpi['generados'] ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="kpi-box">
            <div class="title">Pendientes</div>
            <div class="value" style="color: #f57f17;">{{ number_format($kpi['pendientes'] ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>

    @php
        $totalL = $kpi['total_lineas'] > 0 ? $kpi['total_lineas'] : 1;
        $porcGen = round(($kpi['generados'] / $totalL) * 100, 1);
        $porcPen = round(($kpi['pendientes'] / $totalL) * 100, 1);
    @endphp

    <!-- Gráfico Estadístico en Barras CSS -->
    <h3>Gráfico de Eficiencia de Procesamiento</h3>
    <div class="stat-group">
        <div class="stat-label">
            <span>Certificados Generados / Completados</span>
            <span style="color: #2e7d32;">{{ number_format($kpi['generados'], 0, ',', '.') }} ({{ $porcGen }}%)</span>
        </div>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" style="width: {{ $porcGen }}%; background-color: #2e7d32;"></div>
        </div>
    </div>

    <div class="stat-group">
        <div class="stat-label">
            <span>Certificados Pendientes de Gestión</span>
            <span style="color: #f57f17;">{{ number_format($kpi['pendientes'], 0, ',', '.') }} ({{ $porcPen }}%)</span>
        </div>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" style="width: {{ $porcPen }}%; background-color: #f57f17;"></div>
        </div>
    </div>

    <!-- Tipologías de Certificados -->
    <h3>Tipos de Certificados Asociados</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 10%;">#</th>
                <th style="width: 90%;">Nombre de la Tipología / Certificado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tiposCertificadosLote as $index => $tipoOp)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ optional($tipoOp->tipo)->nombre ?? 'Certificado General' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align: center; color: #64748b;">No hay tipologías registradas para este lote.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Sistema de Gestión de Certificados y Lotes Operativos &mdash; Reporte Estadístico Rápido
    </div>

</body>
</html>