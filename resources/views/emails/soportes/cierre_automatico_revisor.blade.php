<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soporte Cerrado por Vencimiento de Tiempos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #dc2626;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .footer {
            background-color: #f1f1f1;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .button {
            display: inline-block;
            background-color: #007b83;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
        .highlight {
            background-color: #fef2f2;
            padding: 10px;
            border-left: 4px solid #dc2626;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Un Soporte a su Cargo se Cerró por Vencimiento</h1>
    </div>

    <div class="content">
        <p>Estimado/a {{ optional($soporte->scpUsuarioAsignado)->maeTercero->nom_ter ?? '' }},</p>

        <p>El siguiente soporte, que tenía asignado para revisión, fue cerrado automáticamente por el
        sistema porque llevaba <strong>{{ $diasEspera ?? 5 }} día(s) sin ningún movimiento</strong> en la etapa de revisión —
        no se registró una decisión ni una respuesta a tiempo.</p>

        <div class="highlight">
            <p><strong>ID de Soporte:</strong> #{{ $soporte->id }}</p>
            <p><strong>Prioridad:</strong> {{ $soporte->prioridad->nombre ?? 'No definida' }}</p>
            <p><strong>Tipo:</strong> {{ $soporte->tipo->nombre ?? 'No definido' }}</p>
            <p><strong>Solicitado por:</strong> {{ $soporte->usuario->name ?? 'No definido' }}</p>
        </div>

        <p><strong>Descripción del soporte:</strong></p>
        <p>{{ $soporte->detalles_soporte }}</p>

        <p>Si la solicitud realmente no quedó resuelta, por favor ingrese al sistema y déjele
        seguimiento — el sistema ya lo marcó como cerrado, pero puede reabrirse si hace falta.</p>

        <a href="{{ route('soportes.soportes.show', $soporte->id) }}" class="button">Ver Soporte</a>
    </div>

    <div class="footer">
        <p>Este es un mensaje automático del Sistema de Gestión de Soportes. Por favor, no responda a este correo.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
    </div>
</body>
</html>
