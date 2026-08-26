<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado - CORPENTUNIDA</title>
    <style>
        /* Configuraciones de página A4 y márgenes */
        @page {
            margin: 4cm 2.5cm 3cm 2.5cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            position: relative;
        }

        /* --- FONDO DE AGUA (ESTRICTO PARA DOMPDF) --- */
        #fondo-plantilla {
            position: fixed;
            top: -4cm;        /* Exactamente el negativo del margin top */
            left: -2.5cm;     /* Exactamente el negativo del margin left */
            width: 21cm;      /* Ancho estándar A4 */
            height: 29.7cm;   /* Alto estándar A4 */
            z-index: -2000;
        }
        #fondo-plantilla img {
            width: 100%;
            height: 100%;
        }

        /* Estructura del documento */
        .header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 40px;
        }
        .title {
            font-size: 16px;
            margin-bottom: 20px;
            color: #1e293b;
        }
        .content {
            text-align: justify;
            margin-bottom: 25px;
        }

        /* Tabla de obligaciones dinámica */
        .obligaciones-table {
            width: 85%;
            margin: 0 auto 30px auto;
            border-collapse: collapse;
        }
        .obligaciones-table td {
            padding: 8px 10px;
            border-bottom: 1px dashed #cbd5e1;
        }
        .obligaciones-table td.label {
            font-weight: bold;
            width: 60%;
            color: #0f172a;
        }
        .estado-ok { color: #047857; font-weight: bold; }
        .estado-mora { color: #b91c1c; font-weight: bold; }

        /* Caja de calificaciones */
        .calificaciones {
            margin-bottom: 30px;
            background-color: rgba(248, 250, 252, 0.85); /* Fondo ligeramente transparente para que se vea la marca de agua */
            padding: 15px 20px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 13px;
        }
        .calificaciones-item { margin-bottom: 8px; }

        /* Firma */
        .signature { margin-top: 80px; page-break-inside: avoid; }
        .signature-line { width: 250px; border-top: 1px solid #333; margin-bottom: 5px; }

        /* Footer anclado abajo */
        .footer {
            position: fixed;
            bottom: -1cm; /* Se ajusta respecto al margen inferior de la página */
            left: 0px;
            right: 0px;
            text-align: center;
            font-size: 10px;
            border-top: 1px solid #cbd5e1;
            padding-top: 10px;
            color: #64748b;
        }
    </style>
</head>
<body>

    {{-- INYECCIÓN DEL FONDO (Asegúrate de que el archivo se llame exactamente fondo_pdf.jpg) --}}
    <div id="fondo-plantilla">
        <img src="{{ resource_path('views/certificados/pdf/fondo_pdf.jpg') }}" alt="Fondo">
    </div>

    <div class="header">
        <div class="title">LA ASOCIACIÓN GREMIAL DE MINISTROS DE LA IGLESIA PENTECOSTAL UNIDA DE COLOMBIA - CORPENTUNIDA -</div>
        <div style="font-size: 18px; margin-top: 10px;">CERTIFICA QUE:</div>
    </div>

    <div class="content">
        Que el hermano/a <strong>{{ strtoupper($operacion->tercero->nom_ter ?? 'N/A') }} {{ strtoupper($operacion->tercero->apl1 ?? '') }} {{ strtoupper($operacion->tercero->apl2 ?? '') }}</strong>,
        identificado(a) con cédula de ciudadanía No. <strong>{{ $operacion->tercero->cod_ter ?? 'N/A' }}</strong>;
        presenta el siguiente comportamiento con sus obligaciones para con la Asociación, descritas a continuación:
    </div>

    @php
        // 1. Calculamos la calificación general basándonos en TODAS las líneas del cliente
        $calificacionGeneral = 'Bueno';
        if ($lineas->contains('calificacion', 'Irregular')) {
            $calificacionGeneral = 'Irregular';
        } elseif ($lineas->contains('calificacion', 'Regular')) {
            $calificacionGeneral = 'Regular';
        }

        // 2. Agrupamos las líneas dinámicamente por el nombre de la cuenta (Relación lineaSia)
        $lineasAgrupadas = $lineas->groupBy(function($linea) {
            return $linea->lineaSia->nombre ?? 'Línea Desconocida (' . $linea->id_car_sia_lineas . ')';
        });
    @endphp

    <table class="obligaciones-table">
        <tbody>
            @forelse($lineasAgrupadas as $nombreLinea => $grupoLineas)
                @php
                    // Si el cliente tiene 2 facturas de la misma línea, buscamos la que tenga mayor mora
                    $peorMora = $grupoLineas->max('dias_mora_automaticos');
                    $esAlDia = $peorMora == 0;
                    $textoEstado = $esAlDia ? 'Al día' : "En mora ($peorMora días)";
                @endphp
                <tr>
                    <td class="label">{{ mb_strtoupper($nombreLinea, 'UTF-8') }}</td>
                    <td>
                        <span class="{{ $esAlDia ? 'estado-ok' : 'estado-mora' }}">
                            => {{ $textoEstado }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align: center; color: #64748b;">No registra obligaciones en este bloque.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="content">
        Se certifica que a la fecha de expedición de este documento el asociado se encuentra
        <strong>{{ $calificacionGeneral == 'Bueno' ? 'al día' : 'con retrasos' }}</strong> en sus obligaciones con la asociación. <br><br>
        En lo que tiene que ver con su comportamiento de pago, este ha sido catalogado como <strong>{{ strtoupper($calificacionGeneral) }}</strong>.
    </div>

    <div class="calificaciones">
        <div class="calificaciones-item">
            <strong>BUENO:</strong> Ha cumplido oportunamente con sus obligaciones de pago.
        </div>
        <div class="calificaciones-item">
            <strong>REGULAR:</strong> Ha presentado algunos retrasos menores en el cumplimiento de sus obligaciones (inferiores a 2 cuotas / 60 días).
        </div>
        <div class="calificaciones-item">
            <strong>IRREGULAR:</strong> Ha presentado incumplimientos o retrasos en sus obligaciones de pago (de 3 o más cuotas / más de 60 días).
        </div>
    </div>

    <div class="content">
        Este certificado se expide a solicitud del interesado, a los <strong>{{ now()->format('d') }}</strong> días del mes de <strong>{{ ucfirst(now()->locale('es')->monthName) }}</strong> de <strong>{{ now()->format('Y') }}</strong>.
    </div>

    <div class="signature">
        <div class="signature-line"></div>
        <strong>Área de Cartera</strong><br>
        CORPENTUNIDA
    </div>

    <div class="footer">
        Para más información, puede comunicarse al teléfono: 3177772324 o al correo electrónico recaudo@corpentunida.org.co.<br>
        NIT. 860.509.451-5 | Asociación Gremial de Ministros de la Iglesia Pentecostal Unida de Colombia<br>
        Tv 29 38 22 La Soledad / Bogotá / Colombia | www.corpentunida.org.co / www.librerialuzyverdad.co | PBX 60 1 208 71 71
    </div>

</body>
</html>
