<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado Compromisos Activos - CORPENTUNIDA</title>
    <style>
        /* 1. CONFIGURACIÓN DE PÁGINA PARA DOMPDF */
        @page {
            margin: 3.8cm 2cm 3.5cm 2cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #334155;
            text-align: justify;
            margin: 0;
            padding: 0;
        }

        /* 2. FONDO CORPORATIVO */
        #fondo-plantilla {
            position: fixed;
            top: -3.8cm;
            left: -2cm;
            width: 21.5cm;
            height: 29.7cm;
            z-index: -2000;
        }
        #fondo-plantilla img {
            width: 100%;
            height: 100%;
        }

        /* 3. PIE DE PÁGINA Y PAGINADOR */
        .footer {
            position: fixed;
            bottom: -3.2cm;
            right: 0cm;
            text-align: right;
            font-size: 8pt;
            color: #64748b;
            font-weight: bold;
        }
        .page-number:before {
            content: "Página " counter(page) " de " counter(pages);
        }

        /* 4. ENCABEZADO DE DOCUMENTO */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .org-title {
            font-size: 11pt;
            color: #0f172a;
            font-weight: bold;
            text-transform: uppercase;
        }
        .doc-title {
            font-size: 12pt;
            color: #0f172a;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 8px;
            padding-bottom: 8px;
            border-bottom: 1.5px solid #cbd5e1;
            width: 85%;
            margin-left: auto;
            margin-right: auto;
        }

        /* 5. CAJA INFORMATIVA DEL ASOCIADO */
        .intro-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #1e293b;
            padding: 12px;
            border-radius: 2px;
            margin-bottom: 20px;
            font-size: 9.5pt;
        }

        /* 6. TABLAS BLINDADAS (SIN ERRORES VISUALES) */
        .table-detalles {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 8.5pt;
            page-break-inside: auto;
        }

        /* Fila del título de la línea (Integrado en la tabla) */
        .title-row th {
            background-color: #1e293b;
            color: #ffffff;
            text-align: left;
            padding: 7px 10px;
            font-size: 9pt;
            text-transform: uppercase;
            border: 1px solid #1e293b;
        }

        /* Fila de los 6 encabezados */
        .header-row th {
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 7px 4px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #cbd5e1;
        }

        /* Celdas de datos */
        .table-detalles td {
            padding: 6px 4px;
            border: 1px solid #e2e8f0;
            text-align: center;
        }

        /* Alternancia de color en filas */
        .table-detalles tbody tr:nth-child(even) {
            background-color: #fbfcfd;
        }
        .table-detalles tbody tr {
            page-break-inside: avoid;
        }

        /* 7. ESTILOS DE TEXTO Y TOTALES */
        .text-right { text-align: right !important; }
        .text-al-dia { color: #15803d; font-weight: bold; }
        .text-en-mora { color: #b91c1c; font-weight: bold; }

        .tr-subtotal td {
            font-weight: bold;
            background-color: #f1f5f9;
            color: #0f172a;
            border-top: 2px solid #94a3b8;
            padding: 7px 6px;
        }

        .total-box {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff;
            padding: 10px 18px;
            font-size: 10.5pt;
            font-weight: bold;
            border-radius: 4px;
        }

        .legal-notice {
            font-size: 8.5pt;
            color: #64748b;
            margin-top: 20px;
            margin-bottom: 30px;
            line-height: 1.4;
        }

        /* 8. FIRMA */
        .signature-table {
            width: 250px;
            border-collapse: collapse;
        }
        .signature-cell {
            border-top: 1.5px solid #334155;
            text-align: center;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <!-- FONDO CORPORATIVO -->
    <div id="fondo-plantilla">
        <img src="{{ resource_path('views/certificados/pdf/fondo_pdf.jpg') }}" alt="Fondo">
    </div>

    <!-- PAGINADOR -->
    <div class="footer">
        <span class="page-number"></span>
    </div>

    <!-- ENCABEZADO -->
    <div class="header">
        <div class="org-title">
            ASOCIACIÓN GREMIAL DE MINISTROS DE LA IGLESIA PENTECOSTAL UNIDA DE COLOMBIA<br>- CORPENTUNIDA -
        </div>
        <div class="doc-title">
            Certificado de Compromisos Activos
        </div>
    </div>

    <!-- DATOS DEL ASOCIADO -->
    <div class="intro-box">
        La <strong>Asociación Gremial de Ministros de la Iglesia Pentecostal Unida de Colombia (CORPENTUNIDA)</strong> certifica que el(la) asociado(a)
        <strong>{{ strtoupper($operacion->tercero->nom_ter ?? '') }} {{ strtoupper($operacion->tercero->apl1 ?? '') }} {{ strtoupper($operacion->tercero->apl2 ?? '') }}</strong>,
        identificado(a) con Cédula de Ciudadanía No. <strong>{{ $operacion->tercero->cod_ter ?? 'N/A' }}</strong>,
        registra en nuestro sistema el siguiente detalle de compromisos y obligaciones financieras activas a la fecha de emisión:
    </div>

    @php
        $lineasAgrupadas = $lineas->groupBy(fn($l) => $l->lineaSia->nombre ?? 'LÍNEA NO ESPECIFICADA');
        $granTotalDeuda = 0;
    @endphp

    <!-- TABLAS DE OBLIGACIONES -->
    @forelse($lineasAgrupadas as $nombreLinea => $grupoLineas)
        @php $subtotalLinea = 0; @endphp

        <table class="table-detalles">
            <thead>
                <!-- TÍTULO DE LA LÍNEA INTEGRADO (Soluciona el problema visual) -->
                <tr class="title-row">
                    <th colspan="6">{{ mb_strtoupper($nombreLinea, 'UTF-8') }}</th>
                </tr>
                <!-- ENCABEZADOS DE COLUMNA EXACTOS CON PORCENTAJES PRECISOS -->
                <tr class="header-row">
                    <th width="15%">FACTURA</th>
                    <th width="10%">CUOTA</th>
                    <th width="22%">FECHA DE VENCIMIENTO</th>
                    <th width="10%">MORA</th>
                    <th width="18%">ESTADO</th>
                    <th width="25%" class="text-right">VALOR</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grupoLineas as $linea)
                    @php
                        $factura = $linea->factura;
                        $valorCuota = $factura ? (float) $factura->valor : 0;
                        $subtotalLinea += $valorCuota;

                        $diasMora = (int) $linea->dias_mora_automaticos;
                        $esAlDia = $diasMora <= 0;

                        $fechaVencimiento = $factura && $factura->fecha_venci
                            ? \Carbon\Carbon::parse($factura->fecha_venci)->format('d/m/Y')
                            : 'N/A';
                    @endphp
                    <tr>
                        <td><strong>{{ $factura->cuenta ?? 'N/A' }}</strong></td>
                        <td>{{ $factura->cuota ?? 'N/A' }}</td>
                        <td>{{ $fechaVencimiento }}</td>
                        <td>
                            @if($diasMora > 0)
                                <span style="color: #b91c1c; font-weight: bold;">{{ $diasMora }}</span>
                            @else
                                0
                            @endif
                        </td>
                        <td>
                            @if($esAlDia)
                                <span class="text-al-dia">AL DÍA</span>
                            @else
                                <span class="text-en-mora">EN MORA</span>
                            @endif
                        </td>
                        <td class="text-right">${{ number_format($valorCuota, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="tr-subtotal">
                    <td colspan="5" class="text-right">SUBTOTAL {{ mb_strtoupper($nombreLinea, 'UTF-8') }}:</td>
                    <td class="text-right">${{ number_format($subtotalLinea, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        @php $granTotalDeuda += $subtotalLinea; @endphp

    @empty
        <div style="text-align: center; padding: 20px; background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 4px; font-weight: bold; color: #64748b; margin-bottom: 20px;">
            El(la) asociado(a) no registra obligaciones o compromisos activos actualmente.
        </div>
    @endforelse

    <!-- BLOQUE FINAL: TOTAL Y FIRMA -->
    <table width="100%" style="page-break-inside: avoid; border-collapse: collapse;">
        <tr>
            <td style="padding: 0;">

                @if($granTotalDeuda > 0)
                    <table width="100%">
                        <tr>
                            <td width="30%"></td>
                            <td width="70%" align="right" style="padding-bottom: 15px;">
                                <div class="total-box">
                                    TOTAL COMPROMISOS ACTIVOS: ${{ number_format($granTotalDeuda, 2, ',', '.') }}
                                </div>
                            </td>
                        </tr>
                    </table>
                @endif

                <div class="legal-notice">
                    Este documento certifica las obligaciones financieras activas registradas en nuestro sistema al momento de su expedición.<br>
                    Expedido a los <strong>{{ now()->format('d') }}</strong> días del mes de <strong>{{ ucfirst(now()->locale('es')->monthName) }}</strong> de <strong>{{ now()->format('Y') }}</strong>.
                </div>

                <table class="signature-table">
                    <tr>
                        <td class="signature-cell">
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
