<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Correspondencia pendiente</title>
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
                                Correspondencia pendiente
                            </h2>
                        </td>
                    </tr>

                    <!-- Contenido -->
                    <tr>
                        <td style="padding:35px;">

                            <p style="font-size:16px;color:#333;">
                                Tiene una correspondencia pendiente de gestionar, que requiere su atención.
                            </p>

                            <p style="font-size:18px;color:#333;">
                                <strong>Correspondencia #{{ $idCorrespondencia }}</strong>
                            </p>

                            <p style="color:#666;line-height:24px;">
                                Ingrese al sistema para revisar la información y continuar con el proceso
                                correspondiente.<strong>Proceso: {{ $nombreProceso }} </strong>
                            </p>

                            <div style="text-align:center;margin-top:35px;">

                                <a href="{{ route('correspondencia.correspondencias.show', $idCorrespondencia) }}"
                                    style="background:#0d6efd;
                                      color:#ffffff;
                                      text-decoration:none;
                                      padding:14px 24px;
                                      border-radius:5px;
                                      display:inline-block;
                                      font-weight:bold;">

                                    Ver correspondencia

                                </a>

                            </div>

                        </td>
                    </tr>

                    <!-- Pie -->
                    <tr>
                        <td align="center"
                            style="background:#f8f9fa;
                               color:#777;
                               font-size:12px;
                               padding:15px;">

                            Este es un mensaje generado automáticamente.<br>
                            Por favor, no responda este correo.

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
