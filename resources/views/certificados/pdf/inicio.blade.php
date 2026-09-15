<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado Informativo - CORPENTUNIDA</title>
    <style>
        /* 1. CONFIGURACIÓN DE PÁGINA Y MÁRGENES */
        @page { margin: 4cm 2cm 3.5cm 2cm; }

        body {
            font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #334155;
            text-align: justify;
        }

        #fondo-plantilla {
            position: fixed; top: -4cm; left: -2cm; width: 21.5cm; height: 29.7cm; z-index: -2000;
        }
        #fondo-plantilla img { width: 100%; height: 100%; }

        /* 2. PIE DE PÁGINA (PAGINADOR DINÁMICO) */
        .footer {
            position: fixed;
            bottom: -2cm;
            left: 0cm;
            right: 0cm;
            text-align: right;
            font-size: 8pt;
            color: #64748b;
        }
        .page-number:before {
            content: "Página " counter(page) " de " counter(pages);
            font-weight: bold;
        }

        /* 3. ENCABEZADO CORPORATIVO */
        .header { text-align: center; margin-bottom: 20px; }
        .title {
            font-size: 12pt;
            color: #0f172a;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        .subtitle {
            font-size: 10pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 8px;
            margin-bottom: 25px;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        /* 4. TEXTOS */
        .content { margin-bottom: 20px; font-size: 10pt; }
        strong { color: #0f172a; }

        /* 5. TABLA INFORMATIVA PROFESIONAL */
        .table-container { margin-bottom: 25px; page-break-inside: auto; }

        .table-info {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            border: 1px solid #cbd5e1;
        }

        .table-info th {
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 8px 10px;
            text-align: center;
            font-weight: bold;
            border-bottom: 2px solid #cbd5e1;
            text-transform: uppercase;
            font-size: 8.5pt;
        }

        .table-info td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-info tbody tr:nth-child(even) { background-color: #f8fafc; }
        .table-info tbody tr { page-break-inside: avoid; }

        /* 6. ESTADOS E INSIGNIAS */
        .text-center { text-align: center; }
        .badge-ok { color: #047857; font-weight: bold; }
        .badge-mora { color: #dc2626; font-weight: bold; }

        /* 7. CAJA DE CALIFICACIONES */
        .calificaciones {
            margin-bottom: 25px;
            background-color: #f8fafc;
            padding: 12px 18px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            font-size: 9pt;
        }
        .calificaciones-item { margin-bottom: 5px; }
        .calificaciones-item:last-child { margin-bottom: 0; }
    </style>
</head>
<body>
    <div id="fondo-plantilla">
        <img src="{{ resource_path('views/certificados/pdf/fondo_pdf.jpg') }}" alt="Fondo">
    </div>

    <!-- PAGINADOR EN EL PIE DE PÁGINA -->
    <div class="footer">
        <span class="page-number"></span>
    </div>

    <div class="header">
        <div class="title">ASOCIACIÓN GREMIAL DE MINISTROS DE LA IGLESIA PENTECOSTAL UNIDA DE COLOMBIA<br>- CORPENTUNIDA -</div>
        <div class="subtitle">Certificado de Comportamiento e Información de Cartera</div>
    </div>

    <div class="content">
        El presente documento certifica que el(la) asociado(a) <strong>{{ strtoupper($operacion->tercero->nom_ter ?? 'N/A') }} {{ strtoupper($operacion->tercero->apl1 ?? '') }} {{ strtoupper($operacion->tercero->apl2 ?? '') }}</strong>,
        identificado(a) con cédula de ciudadanía No. <strong>{{ $operacion->tercero->cod_ter ?? 'N/A' }}</strong>,
        registra el siguiente comportamiento histórico y estado en sus obligaciones financieras con la Asociación:
    </div>

    @php
        $calificacionGeneral = 'Bueno';
        if ($lineas->contains('calificacion', 'Irregular')) { $calificacionGeneral = 'Irregular'; }
        elseif ($lineas->contains('calificacion', 'Regular')) { $calificacionGeneral = 'Regular'; }

        $lineasAgrupadas = $lineas->groupBy(fn($l) => $l->lineaSia->nombre ?? 'Línea Desconocida');
    @endphp

    <div class="table-container">
        <table class="table-info">
            <thead>
                <tr>
                    <th width="40%">Línea de Crédito / Obligación</th>
                    <th width="25%">Fecha de Vencimiento / Corte</th>
                    <th width="35%">Estado Actual</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lineasAgrupadas as $nombreLinea => $grupoLineas)
                    @php
                        $peorMora = $grupoLineas->max('dias_mora_automaticos');
                        $esAlDia = $peorMora <= 0;

                        // Buscamos la fecha de vencimiento más representativa o la última del grupo
                        $ultimaLinea = $grupoLineas->sortByDesc('created_at')->first();
                        $fechaVencimiento = $ultimaLinea && $ultimaLinea->fecha_venci
                            ? \Carbon\Carbon::parse($ultimaLinea->fecha_venci)->format('d/m/Y')
                            : 'N/A';
                    @endphp
                    <tr>
                        <td><strong>{{ mb_strtoupper($nombreLinea, 'UTF-8') }}</strong></td>
                        <td class="text-center">{{ $fechaVencimiento }}</td>
                        <td class="text-center">
                            <span class="{{ $esAlDia ? 'badge-ok' : 'badge-mora' }}">
                                {{ $esAlDia ? 'AL DÍA' : "EN MORA ($peorMora días)" }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-3 text-muted">No registra obligaciones activas asociadas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="content">
        Su comportamiento de pago global dentro de las líneas asociadas ha sido catalogado con un perfil: <strong>{{ strtoupper($calificacionGeneral) }}</strong>.
    </div>

    <div class="calificaciones">
        <div class="calificaciones-item"><strong>BUENO:</strong> Ha cumplido oportunamente con sus obligaciones de pago.</div>
        <div class="calificaciones-item"><strong>REGULAR:</strong> Ha presentado retrasos menores en el cumplimiento (inferiores a 60 días).</div>
        <div class="calificaciones-item"><strong>IRREGULAR:</strong> Ha presentado incumplimientos o retrasos significativos (más de 60 días).</div>
    </div>

    {{-- BLOQUE DE CIERRE: Blindado contra saltos huérfanos --}}
    <table width="100%" style="page-break-inside: avoid; margin-top: 15px; border-collapse: collapse;">
        <tr>
            <td style="padding: 0;">
                <div class="content" style="font-size: 9pt; color: #64748b; margin-bottom: 40px;">
                    Este documento es de carácter informativo. Expedido a los <strong>{{ now()->format('d') }}</strong> días del mes de <strong>{{ ucfirst(now()->locale('es')->monthName) }}</strong> de <strong>{{ now()->format('Y') }}</strong>.
                </div>

                <table width="250px">
                    <tr>
                        <td style="border-top: 1px solid #334155; text-align: center; padding-top: 5px;">
                            <strong style="color: #0f172a; font-size: 9.5pt;">Área de Cartera</strong><br>
                            <span style="font-size: 8.5pt; color: #475569;">CORPENTUNIDA</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
