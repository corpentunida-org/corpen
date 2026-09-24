<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interacciones vencidas</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 640px; margin: 0 auto; padding: 20px; }
        .header { background-color: #b91c1c; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-top: none; }
        .footer { background-color: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #666; border: 1px solid #ddd; border-top: none; border-radius: 0 0 5px 5px; }
        .button { display: inline-block; background-color: #b91c1c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 15px; }
        table.lista { width: 100%; border-collapse: collapse; margin: 15px 0; background: white; }
        table.lista th { background-color: #f1f5f9; text-align: left; padding: 8px 10px; font-size: 12px; color: #475569; }
        table.lista td { padding: 8px 10px; font-size: 13px; border-top: 1px solid #eee; }
        .vencida-dias { color: #b91c1c; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin:0; font-size: 20px;">Tienes {{ $vencidas->count() }} interacción(es) vencida(s)</h1>
    </div>

    <div class="content">
        <p>Hola {{ $usuario->name }},</p>
        <p>Estas interacciones ya pasaron su fecha de próxima acción y siguen sin cerrarse en Daytrack:</p>

        <table class="lista">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Próxima acción vencía</th>
                    <th>Días vencida</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vencidas as $seguimiento)
                    <tr>
                        <td>
                            <a href="{{ route('interactions.show', $seguimiento->id_interaction) }}">
                                {{ $seguimiento->interaction->client->nom_ter ?? 'Cliente '.$seguimiento->interaction->client_id }}
                            </a>
                        </td>
                        <td>{{ $seguimiento->next_action_date->format('d/m/Y H:i') }}</td>
                        <td class="vencida-dias">{{ (int) $seguimiento->next_action_date->diffInDays(now()) }} día(s)</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p>Por favor ingresa y actualiza el seguimiento de cada una a la brevedad posible.</p>

        <a href="{{ route('interactions.index') }}" class="button">Ver Mis Interacciones</a>
    </div>

    <div class="footer">
        <p><strong>CORPENTUNIDA</strong> — Este es un mensaje automático diario del sistema Daytrack. Por favor, no responda a este correo.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
    </div>
</body>
</html>
