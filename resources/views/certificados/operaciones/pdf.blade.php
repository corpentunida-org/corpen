<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe para Solicitud de Crédito - {{ $operacion->numero_radicado }}</title>
    <style>
        /* 1. CONFIGURACIÓN DE PÁGINA PARA DOMPDF */
        @page {
            margin: 3.8cm 2cm 3.5cm 2cm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #334155;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            text-align: justify;
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

        /* 4. ENCABEZADO CORPORATIVO */
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .org-title {
            font-size: 11pt;
            color: #0f172a;
            font-weight: bold;
            text-transform: uppercase;
        }
        .doc-title {
            font-size: 12pt;
            color: #1e293b;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 6px;
            padding-bottom: 6px;
            border-bottom: 1.5px solid #cbd5e1;
            width: 85%;
            margin-left: auto;
            margin-right: auto;
        }

        /* 5. SECCIONES Y TARJETAS DE INFORMACIÓN */
        .section-title {
            font-size: 9.5pt;
            font-weight: bold;
            color: #ffffff;
            background-color: #1e293b;
            padding: 5px 10px;
            margin-top: 15px;
            margin-bottom: 8px;
            border-radius: 3px;
            text-transform: uppercase;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }
        .info-table td {
            padding: 6px 8px;
            font-size: 9pt;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-table tr:last-child td {
            border-bottom: none;
        }
        .info-table .label {
            font-weight: bold;
            color: #475569;
            width: 25%;
        }

        /* 6. TABLA DE DATOS / DETALLE */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8.5pt;
            page-break-inside: auto;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #cbd5e1;
            text-transform: uppercase;
            font-size: 7.5pt;
        }
        .data-table td {
            padding: 5px 4px;
            border: 1px solid #e2e8f0;
            text-align: center;
        }
        .data-table tbody tr:nth-child(even) {
            background-color: #fbfcfd;
        }
        .data-table tbody tr {
            page-break-inside: avoid;
        }

        /* 7. ESTILOS Y ESTADOS */
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .font-mono { font-family: monospace; }

        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 8pt;
        }
        .badge-success { background-color: #dcfce7; color: #166534; }
        .badge-warning { background-color: #fef9c3; color: #854d0e; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }

        /* 8. CAJA DE DICTAMEN / CONCLUSIÓN */
        .analysis-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-left: 4px solid #0f172a;
            padding: 10px;
            font-size: 9pt;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        /* 9. FIRMAS */
        .signature-table {
            width: 250px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signature-cell {
            border-top: 1.5px solid #334155;
            text-align: center;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <!-- FONDO CORPORATIVO -->
    <div id="fondo-plantilla">
        <img src="{{ resource_path('views/certificados/pdf/fondo_pdf.jpg') }}" alt="Fondo">
    </div>

    <!-- PIE DE PÁGINA Y PAGINADOR -->
    <div class="footer">
        SIA Cartera - Comité de Crédito | Página <span class="page-number"></span>
    </div>
    <br><br>
    <!-- ENCABEZADO -->
    <div class="header">
        <div class="org-title">
            ASOCIACIÓN GREMIAL DE MINISTROS DE LA IGLESIA PENTECOSTAL UNIDA DE COLOMBIA<br>- CORPENTUNIDA -
        </div>
        <div class="doc-title">
            Informe de Comportamiento para Solicitud de Crédito
        </div>
    </div>

    <!-- DATOS DE CONTROL DEL INFORME -->
    <table class="info-table" style="margin-bottom: 12px;">
        <tr>
            <td class="label">Radicado Base:</td>
            <td><strong>{{ $operacion->numero_radicado ?? 'N/A' }}</strong></td>
            <td class="label">Fecha de Emisión:</td>
            <td>{{ now()->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="label">Bloque / Lote:</td>
            <td>API-{{ str_pad($operacion->numero_bloque ?? 0, 4, '0', STR_PAD_LEFT) }}</td>
            <td class="label">Asesor Encargado:</td>
            <td>{{ Auth::user()->name ?? 'Sistema' }}</td>
        </tr>
    </table>

    <!-- DATOS DEL CLIENTE -->
    <div class="section-title">1. Información General del Asociado(a)</div>
    <table class="info-table">
        <tr>
            <td class="label">Nombre / Razón Social:</td>
            <td colspan="3"><strong>{{ strtoupper($operacion->tercero->nom_ter ?? '') }} {{ strtoupper($operacion->tercero->apl1 ?? '') }} {{ strtoupper($operacion->tercero->apl2 ?? '') }}</strong></td>
        </tr>
        <tr>
            <td class="label">Identificación (NIT/CC):</td>
            <td>{{ $operacion->tercero->cod_ter ?? 'N/A' }}</td>
            <td class="label">Teléfono de Contacto:</td>
            <td>{{ $operacion->tercero->tel ?? ($operacion->tercero->tel1 ?? 'No registrado') }}</td>
        </tr>
        <tr>
            <td class="label">Correo Electrónico:</td>
            <td>{{ $operacion->tercero->email ?? 'No registrado' }}</td>
            <td class="label">Ciudad / Dirección:</td>
            <td>{{ $operacion->tercero->ciudad ?? 'N/A' }} - {{ $operacion->tercero->dir ?? '' }}</td>
        </tr>
    </table>

    <!-- DETALLE DE LÍNEAS / FACTURAS -->
    <div class="section-title">2. Detalle de Obligaciones Activas y Comportamiento</div>

    @php
        $totalDeuda = 0;
        $tieneMora = false;
    @endphp

    @if(isset($lineasValidas) && $lineasValidas->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th width="12%">Factura</th>
                    <th width="8%">Cuota</th>
                    <th width="15%">Pagaré</th>
                    <th width="15%">Vencimiento</th>
                    <th width="10%">Mora (Días)</th>
                    <th width="15%" class="text-right">Saldo Actual</th>
                    <th width="15%">Calificación</th>
                    <th width="10%">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lineasValidas as $linea)
                    @php
                        // Obtenemos el saldo actual directamente desde la tabla relacionada car_sia_api (columna 'valor')
                        $valorLinea = (float)($linea->factura->valor ?? 0);
                        $totalDeuda += $valorLinea;

                        $diasMora = (int)($linea->dias_mora_automaticos ?? 0);
                        if($diasMora > 0) $tieneMora = true;
                    @endphp
                    <tr>
                        <td><strong class="font-mono">#{{ $linea->id_factura ?? ($linea->factura->id ?? 'N/A') }}</strong></td>
                        <td>{{ $linea->factura->cuota ?? '-' }}</td>
                        <td>{{ $linea->factura->pagare ?? 'N/A' }}</td>
                        <td>{{ $linea->factura && $linea->factura->fecha_venci ? \Carbon\Carbon::parse($linea->factura->fecha_venci)->format('d/m/Y') : 'N/A' }}</td>
                        <td>
                            @if($diasMora > 0)
                                <span style="color: #991b1b; font-weight: bold;">{{ $diasMora }} d</span>
                            @else
                                <span style="color: #166534;">0</span>
                            @endif
                        </td>
                        <td class="text-right">${{ number_format($valorLinea, 2, ',', '.') }}</td>
                        <td>
                            @php $calif = $linea->calificacion ?? 'Bueno'; @endphp
                            <span class="badge {{ $calif == 'Bueno' ? 'badge-success' : ($calif == 'Regular' ? 'badge-warning' : 'badge-danger') }}">
                                {{ strtoupper($calif) }}
                            </span>
                        </td>
                        <td>
                            @if(($linea->factura->estado ?? '') == 'PROCESADO')
                                <span class="badge badge-success">PROCESADO</span>
                            @else
                                <span class="badge badge-warning">PENDIENTE</span>
                            @endif
                        </td>
                    </tr>
                @endforeach

                <!-- TOTALES -->
                <tr style="background-color: #f1f5f9; font-weight: bold;">
                    <td colspan="5" class="text-right" style="padding: 7px;">TOTAL SALDO CARTERA ACTIVA:</td>
                    <td colspan="3" class="text-left" style="color: #991b1b; font-size: 10pt; padding: 7px;">
                        ${{ number_format($totalDeuda, 2, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>
    @else
        <div style="text-align: center; color: #64748b; padding: 15px; background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 4px; margin-bottom: 15px;">
            El asociado no registra obligaciones o compromisos activos procesados actualmente en este bloque.
        </div>
    @endif

    <!-- DICTAMEN O CONCEPTO PARA EL COMITÉ DE CRÉDITO -->
    <div class="section-title">3. Concepto Analítico para Nueva Solicitud</div>
    <div class="analysis-box">
        <strong>Dictamen del Sistema de Cartera:</strong>
        @if($tieneMora)
            El asociado presenta obligaciones con reportes de mora actuales. Se sugiere verificar las políticas de reestructuración o ponerse al día antes de desembolsar una nueva línea de crédito.
        @else
            El asociado presenta un comportamiento financiero favorable, manteniendo sus obligaciones al día o con saldos pendientes dentro de los parámetros normales de pago. Viable para estudio de nueva solicitud de crédito bajo las pautas de riesgo de la Asociación.
        @endif
        <br><br>
        <em>Nota: Este informe es un soporte interno y auxiliar para el análisis de riesgo del comité de créditos de CORPENTUNIDA.</em>
    </div>

    <br><br>
    <!-- FIRMA -->
    <table class="signature-table">
        <tr>
            <td class="signature-cell">
                <strong style="color: #0f172a; font-size: 9.5pt;">Área de Cartera y Crédito</strong><br>
                <span style="font-size: 8.5pt; color: #475569;">CORPENTUNIDA</span>
            </td>
        </tr>
    </table>

</body>
</html>
