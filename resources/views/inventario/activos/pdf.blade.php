<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inventario</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #333; line-height: 1.4; }
        
        /* Encabezado */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .logo-container { width: 30%; }
        .info-container { width: 70%; text-align: right; }
        .report-title { font-size: 18px; font-weight: bold; color: #1e293b; margin: 0; text-transform: uppercase; }
        
        /* Bloque de Resumen */
        .summary-box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .summary-item { display: inline-block; width: 30%; }
        .summary-label { font-weight: bold; color: #64748b; font-size: 8px; text-transform: uppercase; }
        .summary-value { font-size: 12px; color: #0f172a; display: block; }

        /* Tabla de Contenido */
        table.main-table { width: 100%; border-collapse: collapse; border: 1px solid #e2e8f0; }
        table.main-table th { background: #1e293b; color: white; padding: 10px; text-align: left; text-transform: uppercase; font-size: 9px; }
        table.main-table td { padding: 8px; border-bottom: 1px solid #e2e8f0; }
        table.main-table tr:nth-child(even) { background: #f1f5f9; }

        /* Estados Colores */
        .badge { padding: 3px 8px; border-radius: 10px; font-size: 8px; font-weight: bold; }
        .st-ok { background: #dcfce7; color: #166534; }
        .st-busy { background: #e0f2fe; color: #0369a1; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 5px; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="logo-container">
                <h2 style="color: #4f46e5; margin:0;">SISTEMA INV</h2>
            </td>
            <td class="info-container">
                <p class="report-title">Reporte Ejecutivo de Activos</p>
                <p>Fecha de emisión: {{ $fecha }}</p>
            </td>
        </tr>
    </table>

    <div class="summary-box">
        <div class="summary-item">
            <span class="summary-label">Total de Items</span>
            <span class="summary-value">{{ count($activos) }} Unidades</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Valorización Total</span>
            <span class="summary-value">$ {{ number_format($totalValor, 2) }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Filtros Aplicados</span>
            <span class="summary-value">{{ request('search') ?: 'Ninguno' }}</span>
        </div>
    </div>

    

    <table class="main-table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Activo / Marca</th>
                <th>Serial</th>
                <th>Bodega</th>
                <th>Responsable</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activos as $activo)
            <tr>
                <td style="font-family: monospace; font-weight: bold;">{{ $activo->codigo_activo }}</td>
                <td>
                    <strong>{{ $activo->nombre }}</strong><br>
                    <small style="color: #64748b;">{{ $activo->marca->nombre ?? 'N/A' }}</small>
                </td>
                <td>{{ $activo->serial ?: 'S/N' }}</td>
                <td>{{ $activo->referencia->bodega->nombre ?? 'N/A' }}</td>
                <td>{{ $activo->usuarioAsignado->name ?? 'DISPONIBLE' }}</td>
                <td>
                    <span class="badge {{ $activo->id_Estado == 1 ? 'st-ok' : 'st-busy' }}">
                        {{ $activo->estado->nombre }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Página <script type="text/php">echo $PAGE_NUM;</script> de <script type="text/php">echo $PAGE_COUNT;</script> 
        - Generado por Sistema de Inventario Inteligente
    </div>

</body>
</html>