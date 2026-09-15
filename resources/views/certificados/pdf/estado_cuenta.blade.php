<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado de Cuenta Detallado - CORPENTUNIDA</title>
    <style>
        /* 1. MÁRGENES AJUSTADOS (Ganamos espacio vertical) */
        @page { margin: 3.8cm 2cm 3.5cm 2cm; }

        body {
            font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
            font-size: 9.5pt;
            line-height: 1.3;
            color: #1e293b; /* Texto principal más oscuro para máxima legibilidad */
            text-align: justify;
        }

        #fondo-plantilla {
            position: fixed; top: -3.8cm; left: -2cm; width: 21.5cm; height: 29.7cm; z-index: -2000;
        }
        #fondo-plantilla img { width: 100%; height: 100%; }

        /* 2. PIE DE PÁGINA (Paginador) */
        .footer {
            position: fixed;
            bottom: -3.3cm;
            right: 0cm;
            text-align: right;
            font-size: 8pt;
            color: #475569;
            font-weight: bold;
        }
        .page-number:before {
            content: "Página " counter(page) " de " counter(pages);
        }

        /* 3. ENCABEZADO CORPORATIVO DE JERARQUÍA ALTA */
        .header { text-align: center; margin-bottom: 12px; }
        .title {
            font-size: 13pt;
            color: #0f172a;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .subtitle {
            font-size: 10pt;
            color: #0284c7; /* Azul corporativo destacado */
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 6px;
            margin-bottom: 15px;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        .content { margin-bottom: 14px; font-size: 9.5pt; color: #334155; }
        strong { color: #0f172a; }

        /* 4. TABLAS CON JERARQUÍA VISIBLE */
        .table-container { margin-bottom: 15px; page-break-inside: auto; }

        .linea-title {
            background-color: #0f172a; /* Azul noche de máxima autoridad visual */
            color: #ffffff;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 12px;
            display: inline-block;
            border-radius: 4px 4px 0 0;
            letter-spacing: 0.5px;
            margin-bottom: 0;
        }

        .table-detalles {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            border: 1px solid #94a3b8; /* Marco exterior más definido */
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .table-detalles th {
            background-color: #e2e8f0; /* Tonalidad gris-azul limpia y contrastada */
            color: #0f172a;
            padding: 6px 5px;
            text-align: center;
            font-weight: bold;
            border-bottom: 2px solid #64748b;
            text-transform: uppercase;
            font-size: 7.5pt;
            letter-spacing: 0.3px;
        }

        .table-detalles td {
            padding: 5px 6px;
            border-bottom: 1px solid #cbd5e1;
            border-right: 1px solid #f1f5f9;
        }

        .table-detalles tbody tr:nth-child(even) { background-color: #f8fafc; } /* Cebra sutil para descanso visual */
        .table-detalles tbody tr:hover { background-color: #f1f5f9; }
        .table-detalles tbody tr { page-break-inside: avoid; }

        /* 5. ESTADOS Y JERARQUÍA DE DATOS CRÍTICOS */
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* Insignias de Estado con alto contraste para lectura rápida */
        .badge-ok {
            color: #047857;
            background-color: #d1fae5;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 7.5pt;
        }
        .badge-mora {
            color: #b91c1c;
            background-color: #fee2e2;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 7.5pt;
        }

        /* Subtotales con jerarquía intermedia clara */
        .tr-subtotal td {
            font-weight: bold;
            background-color: #f1f5f9;
            color: #0f172a;
            border-top: 2px solid #64748b;
            border-bottom: 2px solid #64748b;
            font-size: 9pt;
            padding: 7px 6px;
        }

        /* Caja de Total General con impacto visual definitivo */
        .total-box {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff;
            padding: 10px 18px;
            font-size: 11pt;
            font-weight: bold;
            border-radius: 6px;
            letter-spacing: 0.5px;
            border: 1px solid #334155;
        }
    </style>
</head>
<body>
    <div id="fondo-plantilla">
        <img src="{{ resource_path('views/certificados/pdf/fondo_pdf.jpg') }}" alt="Fondo">
    </div>

    <!-- PIE DE PÁGINA (PAGINADOR) -->
    <div class="footer">
        <span class="page-number"></span>
    </div>
    <br>
    <div class="header">
        <div class="title">ASOCIACIÓN GREMIAL DE MINISTROS DE LA IGLESIA PENTECOSTAL UNIDA DE COLOMBIA<br>- CORPENTUNIDA -</div>
        <div class="subtitle">Estado de Cuenta de Cartera Detallado</div>
    </div>
    <br>
    <div class="content">
        El presente documento certifica que el(la) asociado(a) <strong>{{ strtoupper($operacion->tercero->nom_ter ?? '') }} {{ strtoupper($operacion->tercero->apl1 ?? '') }} {{ strtoupper($operacion->tercero->apl2 ?? '') }}</strong>,
        identificado(a) con cédula de ciudadanía No. <strong>{{ $operacion->tercero->cod_ter ?? 'N/A' }}</strong>,
        registra el siguiente estado detallado en sus obligaciones financieras con la Asociación a la fecha de corte:
    </div>

    @php
        $lineasAgrupadas = $lineas->groupBy(fn($l) => $l->lineaSia->nombre ?? 'Línea Desconocida');
        $granTotalDeuda = 0;
    @endphp

    @forelse($lineasAgrupadas as $nombreLinea => $grupoLineas)
        @php $subtotalLinea = 0; @endphp

        <div class="table-container">
            <div class="linea-title">{{ $nombreLinea }}</div>

            <table class="table-detalles">
                <thead>
                    <tr>
                        <th width="15%">Factura</th>
                        <th width="10%">Cuota</th>
                        <th width="20%">Vencimiento</th>
                        <th width="15%">Días Mora</th>
                        <th width="20%">Estado</th>
                        <th width="20%" class="text-right">Valor</th>
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
                            <td class="text-center font-monospace" style="font-weight: bold; color: #0f172a;">#{{ $linea->id_factura ?? ($factura->id ?? 'N/A') }}</td>
                            <td class="text-center" style="font-weight: 600;">{{ $factura->cuota ?? 'N/A' }}</td>
                            <td class="text-center">{{ $fechaVencimiento }}</td>
                            <td class="text-center">
                                @if($diasMora > 0)
                                    <span style="color: #b91c1c; font-weight: bold; font-size: 9pt;">{{ $diasMora }}</span>
                                @else
                                    <span style="color: #64748b;">0</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="{{ $esAlDia ? 'badge-ok' : 'badge-mora' }}">
                                    {{ $esAlDia ? 'PENDIENTE' : 'EN MORA' }}
                                </span>
                            </td>
                            <td class="text-right" style="font-weight: bold; color: #0f172a;">${{ number_format($valorCuota, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="tr-subtotal">
                        <td colspan="5" class="text-right">SUBTOTAL {{ mb_strtoupper($nombreLinea, 'UTF-8') }}:</td>
                        <td class="text-right" style="color: #0f172a; font-size: 9.5pt;">${{ number_format($subtotalLinea, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @php $granTotalDeuda += $subtotalLinea; @endphp

    @empty
        <div style="text-align: center; padding: 20px; background-color: #f8fafc; border: 1px dashed #94a3b8; border-radius: 6px; color: #64748b; font-weight: bold;">
            No registra obligaciones activas procesadas para este certificado.
        </div>
    @endforelse

    {{-- BLOQUE DE CIERRE: Estructura blindada contra saltos de página --}}
    <table width="100%" style="page-break-inside: avoid; margin-top: 10px; border-collapse: collapse;">
        <tr>
            <td style="padding: 0;">

                @if($granTotalDeuda > 0)
                    <table width="100%" style="margin-bottom: 15px;">
                        <tr>
                            <td width="30%"></td>
                            <td width="70%" align="right">
                                <div class="total-box">
                                    TOTAL DEUDA CONSOLIDADA: ${{ number_format($granTotalDeuda, 2, ',', '.') }}
                                </div>
                            </td>
                        </tr>
                    </table>
                @endif
                <br>
                <div class="content" style="font-size: 8.5pt; color: #475569; margin-bottom: 25px; line-height: 1.4;">
                    Este documento es de carácter informativo y refleja el saldo de cartera al momento de su generación. Si presenta alguna inconsistencia, por favor comuníquese con el área de cartera de CORPENTUNIDA.<br>
                    Expedido a los <strong>{{ now()->format('d') }}</strong> días del mes de <strong>{{ ucfirst(now()->locale('es')->monthName) }}</strong> de <strong>{{ now()->format('Y') }}</strong>.
                </div>
                <br>
                <table width="250px">
                    <tr>
                        <td style="border-top: 2px solid #0f172a; text-align: center; padding-top: 6px;">
                            <strong style="color: #0f172a; font-size: 9.5pt;">Área de Cartera</strong><br>
                            <span style="font-size: 8.5pt; color: #475569; font-weight: bold;">CORPENTUNIDA</span>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>
</html>
