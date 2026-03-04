<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta {{ $movimiento->codigo_acta }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header h2 { margin: 5px 0 0; font-size: 14px; color: #555; }
        
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 4px; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .items-table th, .items-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .items-table th { background-color: #f2f2f2; font-weight: bold; }
        
        .firmas { width: 100%; margin-top: 80px; text-align: center; }
        .firma-box { width: 45%; display: inline-block; vertical-align: top; }
        .linea-firma { border-top: 1px solid #000; width: 80%; margin: 0 auto 5px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>ACTA DE MOVIMIENTO DE INVENTARIO</h1>
        <h2>{{ $movimiento->codigo_acta }}</h2>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Fecha y Hora:</strong> {{ $movimiento->created_at->format('d/m/Y H:i') }}</td>
            <td><strong>Tipo de Movimiento:</strong> {{ $movimiento->tipoRegistro->nombre ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Generado por:</strong> {{ $movimiento->creador->name ?? 'N/A' }}</td>
            <td><strong>Responsable Asignado:</strong> {{ $movimiento->responsable->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Observaciones:</strong> {{ $movimiento->observacion_general ?? 'Ninguna' }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Ítem</th>
                <th>Nombre del Activo</th>
                <th>Placa / Marquilla</th>
                <th>Serial</th>
                <th>Marca</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movimiento->detalles as $index => $detalle)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $detalle->activo->nombre ?? 'N/A' }}</td>
                <td>{{ $detalle->activo->codigo_activo ?? 'N/A' }}</td>
                <td>{{ $detalle->activo->serial ?? 'N/A' }}</td>
                <td>{{ $detalle->activo->referencia->marca->nombre ?? 'N/A' }}</td>
                <td>{{ $detalle->estado_individual ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="firmas">
        <div class="firma-box">
            <div class="linea-firma"></div>
            <strong>ENTREGA</strong><br>
            {{ $movimiento->creador->name ?? 'Firma' }}<br>
            C.C. / Doc: _________________
        </div>
        <div class="firma-box">
            <div class="linea-firma"></div>
            <strong>RECIBE CONFORME</strong><br>
            {{ $movimiento->responsable->name ?? 'Firma' }}<br>
            C.C. / Doc: _________________
        </div>
    </div>

</body>
</html>