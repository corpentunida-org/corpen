<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta de postergación reiterada — Soportes</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 640px; margin: 0 auto; padding: 20px; }
        .header { background-color: #0c3572; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-top: none; }
        .footer { background-color: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #666; border: 1px solid #ddd; border-top: none; border-radius: 0 0 5px 5px; }
        .button { display: inline-block; background-color: #0c3572; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 15px; }
        .highlight { background-color: #dbeafe; padding: 12px; border-left: 4px solid #1d4ed8; margin: 15px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin:0; font-size: 20px;">Postergación reiterada de soportes</h1>
    </div>

    <div class="content">
        <p>Hola,</p>
        <p>
            Cada cierto número de horas, Centro de Soportes le pide a cada usuario con soportes asignados sin
            cerrar que decida entre <strong>responder ahora</strong> o <strong>posponer</strong>. El siguiente
            usuario lleva varios días seguidos eligiendo posponer, sin responder ninguna vez en todo el día:
        </p>

        <div class="highlight">
            <p style="margin:0;"><strong>{{ $usuario->name }}</strong></p>
            <p style="margin:4px 0 0 0;">Días consecutivos posponiendo: <strong>{{ $diasConsecutivos }}</strong></p>
        </div>

        <p>Puede ser útil hacer seguimiento directo con la persona para entender si hay una carga de trabajo excesiva, una dificultad puntual, o simplemente falta de atención al módulo.</p>

        <a href="{{ route('soportes.soportes.index') }}" class="button">Ver Soportes</a>
    </div>

    <div class="footer">
        <p><strong>CORPENTUNIDA</strong> — Este es un mensaje automático del sistema. Por favor, no responda a este correo.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
    </div>
</body>
</html>
