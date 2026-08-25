<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado - CORPENTUNIDA</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; line-height: 1.5; margin: 40px; color: #333; }
        .header { text-align: center; font-weight: bold; margin-bottom: 30px; }
        .title { font-size: 18px; margin-bottom: 20px; }
        .content { text-align: justify; margin-bottom: 20px; }
        .obligaciones-table { width: 80%; margin: 0 auto 30px auto; border-collapse: collapse; }
        .obligaciones-table td { padding: 5px 10px; }
        .obligaciones-table td.label { font-weight: bold; width: 60%; }
        .calificaciones { margin-bottom: 30px; }
        .calificaciones-item { margin-bottom: 10px; }
        .footer { position: absolute; bottom: 30px; width: 100%; text-align: center; font-size: 12px; border-top: 1px solid #ccc; padding-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">LA ASOCIACIÓN GREMIAL DE MINISTROS DE LA IGLESIA PENTECOSTAL UNIDA DE COLOMBIA - CORPENTUNIDA -</div>
        <div>CERTIFICA QUE:</div>
    </div>

    <div class="content">
        Que el hermano <strong>{{ strtoupper($operacion->tercero->nom_ter ?? 'N/A') }} {{ strtoupper($operacion->tercero->apl1 ?? '') }}</strong> identificado con cédula de ciudadanía No <strong>{{ $operacion->tercero->cod_ter ?? 'N/A' }}</strong>; presenta el siguiente comportamiento con sus obligaciones para con la Asociación, descritas a continuación[cite: 1]:
    </div>

    @php
        // Lógica de presentación de obligaciones basada en las líneas procesadas
        $obligaciones = [
            'Libre inversión', 'Rapi-crédito', 'Hipotecario', 'Mi Primera Inversión',
            'Congregación', 'Seguro de Vida', 'Librería'
        ];

        $calificacionGeneral = 'Bueno';
        if ($lineas->contains('calificacion', 'Irregular')) {
            $calificacionGeneral = 'Irregular';
        } elseif ($lineas->contains('calificacion', 'Regular')) {
            $calificacionGeneral = 'Regular';
        }
    @endphp

    <table class="obligaciones-table">
        <tbody>
            @foreach($obligaciones as $obligacion)
                @php
                    // Comprobar si existe esta obligación en las líneas procesadas (Ajustar lógica de coincidencia de nombres según base de datos)
                    $tieneObligacion = $lineas->first(function ($linea) use ($obligacion) {
                        return str_contains(strtolower($linea->lineaCredito->nombre ?? ''), strtolower($obligacion));
                    });

                    $estadoTexto = $tieneObligacion ? ($tieneObligacion->dias_mora_automaticos == 0 ? 'al día' : 'en mora') : 'No registra';
                @endphp
                <tr>
                    <td class="label">{{ $obligacion }}</td>
                    <td>=> {{ $estadoTexto }}[cite: 1]</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="content">
        Se certifica que a la fecha de expedición de este documento el asociado se encuentra {{ $calificacionGeneral == 'Bueno' ? 'al día' : 'con retrasos' }} en sus obligaciones con la asociación. <br><br>
        En lo que tiene que ver con su comportamiento de pago este ha sido <strong>{{ $calificacionGeneral }}</strong>[cite: 1].
    </div>

    <div class="calificaciones">
        <div class="calificaciones-item"><strong>Bueno:</strong> Ha cumplido oportunamente con sus obligaciones de pago[cite: 1].</div>
        <div class="calificaciones-item"><strong>Regular:</strong> Ha presentado algunos retrasos menores en el cumplimiento de sus obligaciones Inferiores a 2 cuotas[cite: 1].</div>
        <div class="calificaciones-item"><strong>Irregular:</strong> Ha presentado incumplimientos o retrasos en sus obligaciones de pago de 3 o más cuotas[cite: 1].</div>
    </div>

    <div class="content">
        Este certificado se expide a los {{ now()->format('d') }} días del mes de {{ now()->locale('es')->monthName }} de {{ now()->format('Y') }}.
    </div>

    <br><br><br>
    <div>
        Dios le bendiga grandemente. Cordialmente,<br>
        <strong>Área de Cartera</strong>[cite: 1]
    </div>

    <div class="footer">
        Para más información, puede comunicarse al teléfono - 3177772324 o al correo electrónico recaudo@corpentunida.org.co[cite: 1].<br>
        NIT. 860.509.451-5 | Asociación Gremial de Ministros de la Iglesia Pentecostal Unida de Colombia[cite: 1]<br>
        Tv 29 38 22 La soledad / Bogota / Colombia | www.corpentunida.org.co / www.librerialuzyverdad.co | PBX 60 1 208 71 71[cite: 1]
    </div>

</body>
</html>
