<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta de inactividad</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 640px; margin: 0 auto; padding: 20px; }
        .header { background-color: #b45309; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-top: none; }
        .footer { background-color: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #666; border: 1px solid #ddd; border-top: none; border-radius: 0 0 5px 5px; }
        .button { display: inline-block; background-color: #b45309; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 15px; }
        ul.lista { background: white; border: 1px solid #eee; border-radius: 6px; padding: 12px 12px 12px 32px; margin: 15px 0; }
        ul.lista li { padding: 4px 0; font-size: 13px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin:0; font-size: 20px;">{{ $usuariosInactivos->count() }} usuario(s) sin registrar hoy</h1>
    </div>

    <div class="content">
        <p>Hola,</p>
        <p>
            Hoy <strong>{{ $fecha->translatedFormat('l d/m/Y') }}</strong> (día hábil), los siguientes usuarios del área
            <strong>{{ strtoupper($area) }}</strong> con acceso a Daytrack aún no han registrado ninguna interacción:
        </p>

        <ul class="lista">
            @foreach ($usuariosInactivos as $u)
                <li>{{ $u->name }}</li>
            @endforeach
        </ul>

        <p>Puede ser que el día no haya terminado o que estén realizando otra labor — este aviso es solo para que el área lo tenga presente y pueda hacer seguimiento a tiempo.</p>

        <a href="{{ route('interactions.auditoria') }}" class="button">Ver Auditoría de Interacciones</a>
    </div>

    <div class="footer">
        <p><strong>CORPENTUNIDA</strong> — Este es un mensaje automático del sistema Daytrack, enviado de lunes a viernes. Por favor, no responda a este correo.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
    </div>
</body>
</html>
