<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Soporte Escalado</title>
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
            background-color: #007b83;
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
            background-color: #eaf4ff;
            padding: 10px;
            border-left: 4px solid #007b83;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Nuevo Soporte Escalado</h1>
    </div>
    
    <div class="content">
        <p>Estimado/a {{ Auth::user()->name }},</p>
        
        <p>Se le ha asignado un nuevo soporte que requiere su atención:</p>
        
        <div class="highlight">
            <p><strong>ID de Soporte:</strong> #{{ $soporte->id }}</p>
            <p><strong>Prioridad:</strong> {{ $soporte->prioridad->nombre ?? 'No definida' }}</p>
            <p><strong>Tipo:</strong> {{ $soporte->tipo->nombre ?? 'No definido' }}</p>
            <p><strong>Creado por:</strong> {{ $soporte->usuario->name ?? 'No definido' }}</p>
            <p><strong>Fecha de creación:</strong> {{ $soporte->created_at->format('d/m/Y H:i') }}</p>
        </div>
        
        <p><strong>Descripción del soporte:</strong></p>
        <p>{{ $soporte->detalles_soporte }}</p>
        
        @if($observacion)
            <p><strong>Observación de escalamiento:</strong></p>
            <p>{{ $observacion->observacion }}</p>
        @endif
        
        <p>Por favor, ingrese al sistema para revisar y gestionar este soporte a la brevedad posible.</p>
        
        <a href="{{ route('soportes.soportes.show', $soporte->id) }}" class="button">Ver Soporte</a>
    </div>
    
    <div class="footer">
        <p>Este es un mensaje automático del Sistema de Gestión de Soportes. Por favor, no responda a este correo.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
    </div>
</body>
</html>