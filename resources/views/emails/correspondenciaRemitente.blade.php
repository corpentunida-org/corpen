<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Actualización de su radicado</title>
</head>

<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,Helvetica,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding:30px 0;">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff;border-radius:8px;overflow:hidden;">

                    <!-- Encabezado -->
                    <tr>
                        <td align="center" style="background:#0d6efd;color:#ffffff;padding:25px;">
                            <h2 style="margin:0;">
                                Actualización de su radicado
                            </h2>
                        </td>
                    </tr>

                    <!-- Contenido -->
                    <tr>
                        <td style="padding:35px;">

                            <p style="font-size:16px;color:#333;">
                                Hola, {{ $nombreRemitente }}.
                            </p>

                            <p style="font-size:18px;color:#333;">
                                <strong>Radicado #{{ $idCorrespondencia }}</strong>
                            </p>

                            <p style="color:#666;line-height:24px;">
                                <strong>Asunto:</strong> {{ $asunto }}<br>
                                <strong>Estado:</strong> {{ $nombreEstado }}
                            </p>

                            <p style="color:#333;line-height:24px;">
                                {!! nl2br(e($observacion)) !!}
                            </p>

                            @if (count($rutasArchivos) > 0)
                                <p style="color:#666;line-height:24px;">
                                    Este mensaje incluye {{ count($rutasArchivos) }} documento(s) adjunto(s).
                                </p>
                            @endif

                        </td>
                    </tr>

                    <!-- Pie -->
                    <tr>
                        <td align="center" style="background:#f8f9fa;color:#888;padding:20px;font-size:12px;">
                            Este es un mensaje automático, por favor no lo responda.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
