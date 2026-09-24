<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe semanal de Interacciones</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 680px; margin: 0 auto; padding: 20px; }
        .header { background-color: #1e3a8a; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-top: none; }
        .footer { background-color: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #666; border: 1px solid #ddd; border-top: none; border-radius: 0 0 5px 5px; }
        .button { display: inline-block; background-color: #1e3a8a; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 15px; }
        table.lista { width: 100%; border-collapse: collapse; margin: 15px 0; background: white; }
        table.lista th { background-color: #f1f5f9; text-align: left; padding: 8px 10px; font-size: 12px; color: #475569; }
        table.lista td { padding: 8px 10px; font-size: 13px; border-top: 1px solid #eee; }
        .text-right { text-align: right; }
        .sin-actividad { color: #b91c1c; font-weight: bold; }
        .badge-venc { color: #b91c1c; font-weight: bold; }
        .badge-ok { color: #64748b; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin:0; font-size: 20px;">Informe Semanal — Área {{ strtoupper($area) }}</h1>
        <p style="margin: 5px 0 0 0; font-size: 13px;">Semana del {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}</p>
    </div>

    <div class="content">
        <p>Resumen de gestión de todos los agentes del área en Daytrack durante la semana. Los usuarios sin ninguna interacción registrada aparecen igual en la lista, con 0.</p>

        <table class="lista">
            <thead>
                <tr>
                    <th>Agente</th>
                    <th class="text-right">Interacciones (semana)</th>
                    <th class="text-right">Vencidas (hoy)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($filas as $fila)
                    <tr>
                        <td>
                            {{ $fila->usuario->name }}
                            @if ($fila->total === 0)
                                <span class="sin-actividad">— sin actividad esta semana</span>
                            @endif
                        </td>
                        <td class="text-right">{{ $fila->total }}</td>
                        <td class="text-right {{ $fila->vencidas > 0 ? 'badge-venc' : 'badge-ok' }}">
                            {{ $fila->vencidas > 0 ? $fila->vencidas : '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <a href="{{ route('interactions.auditoria') }}" class="button">Ver Auditoría de Interacciones</a>
    </div>

    <div class="footer">
        <p><strong>CORPENTUNIDA</strong> — Este es un mensaje automático semanal del sistema Daytrack (todos los lunes). Por favor, no responda a este correo.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
    </div>
</body>
</html>
