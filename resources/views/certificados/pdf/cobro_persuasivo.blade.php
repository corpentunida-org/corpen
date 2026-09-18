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
            font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
            font-size: 9.5pt;
            line-height: 1.4;
            color: #1e293b; /* Texto principal oscuro de alta legibilidad */
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
            color: #475569;
            font-weight: bold;
        }
        .page-number:before {
            content: "Página " counter(page) " de " counter(pages);
        }

        /* 4. ENCABEZADO DE DOCUMENTO */
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .org-title {
            font-size: 12pt;
            color: #0f172a;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-title {
            font-size: 11pt;
            color: #0284c7; /* Azul corporativo destacado */
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 6px;
            padding-bottom: 6px;
            border-bottom: 2px solid #0284c7;
            width: 85%;
            margin-left: auto;
            margin-right: auto;
        }

        /* 5. CAJA INFORMATIVA DEL ASOCIADO */
        .intro-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-left: 4px solid #0f172a; /* Jerarquía visual fuerte */
            padding: 12px 14px;
            border-radius: 4px;
            margin-bottom: 18px;
            font-size: 9.5pt;
            color: #334155;
        }
        .intro-box strong {
            color: #0f172a;
        }

        /* 6. TABLAS CON JERARQUÍA Y ALTO CONTRASTE */
        .table-detalles {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 8pt;
            border: 1px solid #94a3b8;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            page-break-inside: auto;
        }

        /* Fila del título de la línea (Máxima jerarquía de sección) */
        .title-row th {
            background-color: #0f172a;
            color: #ffffff;
            text-align: left;
            padding: 7px 12px;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #0f172a;
        }

        /* Fila de encabezados de columnas */
        .header-row th {
            background-color: #e2e8f0;
            color: #0f172a;
            padding: 6px 4px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #cbd5e1;
            border-bottom: 2px solid #64748b;
            text-transform: uppercase;
            font-size: 7pt;
            letter-spacing: 0.3px;
        }

        /* Celdas de datos */
        .table-detalles td {
            padding: 6px 4px;
            border: 1px solid #cbd5e1;
            text-align: center;
            color: #334155;
        }

        /* Alternancia de color (Cebra) */
        .table-detalles tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .table-detalles tbody tr:hover {
            background-color: #f1f5f9;
        }
        .table-detalles tbody tr {
            page-break-inside: avoid;
        }

        /* 7. ESTILOS DE TEXTO, ESTADOS Y BADGES */
        .text-right { text-align: right !important; }

        /* Insignias de Estado con lectura inmediata */
        .badge-ok {
            color: #047857;
            background-color: #d1fae5;
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 7pt;
        }
        .badge-mora {
            color: #b91c1c;
            background-color: #fee2e2;
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 7pt;
        }
        .badge-api-pago {
            color: #047857;
            background-color: #d1fae5;
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 7pt;
        }
        .badge-api-falta {
            color: #b45309;
            background-color: #fef3c7;
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 7pt;
        }

        .legal-notice {
            font-size: 8.5pt;
            color: #475569;
            margin-top: 15px;
            margin-bottom: 25px;
            line-height: 1.4;
        }
        .legal-notice strong {
            color: #0f172a;
        }

        /* 8. FIRMA */
        .signature-table {
            width: 250px;
            border-collapse: collapse;
        }
        .signature-cell {
            border-top: 2px solid #0f172a;
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
    <br><br>
    <!-- ENCABEZADO -->
    <div class="header">
        <div class="org-title">
            ASOCIACIÓN GREMIAL DE MINISTROS DE LA IGLESIA PENTECOSTAL UNIDA DE COLOMBIA<br>
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
        // 1. Ordenamos la colección por fecha de vencimiento (de la más antigua a la más reciente)
        $lineasOrdenadas = $lineas->sortBy(function($linea) {
            return $linea->fecha_venci ?? optional($linea->factura)->fecha_venci;
        });

        // 2. Agrupamos las líneas que ya vienen ordenadas
        $lineasAgrupadas = $lineasOrdenadas->groupBy(fn($l) => $l->lineaSia->nombre ?? 'LÍNEA NO ESPECIFICADA');
    @endphp

    <!-- TABLAS DE OBLIGACIONES -->
    @forelse($lineasAgrupadas as $nombreLinea => $grupoLineas)

        <table class="table-detalles">
            <thead>
                <!-- TÍTULO DE LA LÍNEA INTEGRADO -->
                <tr class="title-row">
                    <th colspan="6">{{ mb_strtoupper($nombreLinea, 'UTF-8') }}</th>
                </tr>
                <!-- ENCABEZADOS DE COLUMNA -->
                <tr class="header-row">
                    <th width="18%">Factura</th>
                    <th width="10%">Cuota</th>
                    <th width="22%">Vencimiento</th>
                    <th width="10%">Mora</th>
                    <th width="20%">Estado Mora</th>
                    <th width="20%">Estado API</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grupoLineas as $linea)
                    @php
                        $factura = $linea->factura;
                        $diasMora = (int) $linea->dias_mora_automaticos;

                        // Validación del Estado API para determinar si está pagado
                        $estadoApiVal = $linea->estadoApi;
                        $tieneEstadoApi = !is_null($estadoApiVal) && trim($estadoApiVal) !== '';
                        $esPago = $tieneEstadoApi && ($estadoApiVal == '1' || strtoupper(trim($estadoApiVal)) === 'CANCELADO');

                        // Si está pagado por API, se considera al día automáticamente
                        $esAlDia = ($diasMora <= 0) || $esPago;

                        // Priorizamos la fecha editada en el modelo línea sobre la API cruda
                        $fechaVencReal = $linea->fecha_venci ?? optional($factura)->fecha_venci;

                        $fechaVencimiento = $fechaVencReal
                            ? \Carbon\Carbon::parse($fechaVencReal)->format('d/m/Y')
                            : 'N/A';

                        // Lógica para cuota o formato mes-año en seguros/cuentas por cobrar
                        $esSeguro = stripos($nombreLinea, 'SEGURO') !== false;
                        $cuotaOriginal = $factura->cuota ?? null;

                        if ($esSeguro || empty($cuotaOriginal) || $cuotaOriginal === 'N/A') {
                            if ($fechaVencReal) {
                                $fechaVenc = \Carbon\Carbon::parse($fechaVencReal);
                                $meses = [1 => 'ENE', 2 => 'FEB', 3 => 'MAR', 4 => 'ABR', 5 => 'MAY', 6 => 'JUN', 7 => 'JUL', 8 => 'AGO', 9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DIC'];
                                $mesAbrev = $meses[$fechaVenc->month] ?? '';
                                $anio2Digitos = $fechaVenc->format('y');
                                $cuotaMostrar = "{$mesAbrev}-{$anio2Digitos}";
                            } else {
                                $cuotaMostrar = 'N/A';
                            }
                        } else {
                            $cuotaMostrar = $cuotaOriginal;
                        }
                    @endphp
                    <tr>
                        <td style="font-family: monospace; font-weight: bold; color: #0f172a;">#{{ $linea->id_factura ?? ($factura->id ?? 'N/A') }}</td>
                        <td style="font-weight: 600;">{{ $cuotaMostrar }}</td>
                        <td>{{ $fechaVencimiento }}</td>
                        <td>
                            @if($diasMora > 0 && !$esPago)
                                <span style="color: #b91c1c; font-weight: bold; font-size: 8.5pt;">{{ $diasMora }}</span>
                            @else
                                <span style="color: #64748b;">0</span>
                            @endif
                        </td>
                        <td>
                            <span class="{{ $esAlDia ? 'badge-ok' : 'badge-mora' }}">
                                @if($esPago)
                                    AL DÍA
                                @else
                                    {{ $esAlDia ? '-' : 'EN MORA' }}
                                @endif
                            </span>
                        </td>
                        <td>
                            @if($tieneEstadoApi)
                                <span class="badge-api-pago" title="Valor: {{ $estadoApiVal }}">
                                    {{ $estadoApiVal == '1' ? 'CANCELADO' : strtoupper($estadoApiVal) }}
                                </span>
                            @else
                                <span class="badge-api-falta">FALTA POR PAGAR</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @empty
        <div style="text-align: center; padding: 20px; background-color: #f8fafc; border: 1px dashed #94a3b8; border-radius: 6px; font-weight: bold; color: #64748b; margin-bottom: 20px;">
            El(la) asociado(a) no registra obligaciones o compromisos activos actualmente.
        </div>
    @endforelse

    <!-- BLOQUE FINAL: FIRMA -->
    <table width="100%" style="page-break-inside: avoid; border-collapse: collapse; margin-top: 10px;">
        <tr>
            <td style="padding: 0;">

                <div class="legal-notice">
                    Este documento certifica las obligaciones financieras activas registradas en nuestro sistema al momento de su expedición.<br>
                    Expedido a los <strong>{{ now()->format('d') }}</strong> días del mes de <strong>{{ ucfirst(now()->locale('es')->monthName) }}</strong> de <strong>{{ now()->format('Y') }}</strong>.
                </div>
                <br><br>
                <table class="signature-table">
                    <tr>
                        <td class="signature-cell">
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
