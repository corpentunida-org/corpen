<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Soporte Escalado</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f9fc; color: #333; padding: 20px; }
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 8px 8px 0 0;
            font-size: 18px;
            font-weight: bold;
        }
        .content { padding: 20px; }
        .btn {
            display: inline-block;
            background: #007bff;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 6px;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            color: #777;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            📢 Nuevo Soporte Escalado #{{ $soporte->id }}
        </div>
        <div class="content">
            <p>Estimado/a <strong>{{ $soporte->usuarioEscalado->name ?? 'Usuario' }}</strong>,</p>

            <p>Se te ha asignado un nuevo soporte escalado con el siguiente detalle:</p>

            <ul>
                <li><strong>Título:</strong> {{ $soporte->detalles_soporte }}</li>
                <li><strong>Categoría:</strong> {{ $soporte->categoria->nombre ?? 'Sin categoría' }}</li>
                <li><strong>Prioridad:</strong> {{ $soporte->prioridad->nombre ?? 'No definida' }}</li>
                <li><strong>Estado actual:</strong> {{ $soporte->estadoSoporte->nombre ?? 'Pendiente' }}</li>
            </ul>

            <p><strong>Escalado por:</strong> {{ $escaladoPor->name ?? 'Sistema' }}</p>

            <p>Puedes revisar este soporte haciendo clic en el siguiente enlace:</p>

            <a href="{{ url('/soportes/soportes/' . $soporte->id) }}" class="btn">Ver Soporte</a>
        </div>
        <div class="footer">
            Este mensaje fue generado automáticamente por el sistema de Soportes Corpentunida.
        </div>
    </div>
</body>
</html>
