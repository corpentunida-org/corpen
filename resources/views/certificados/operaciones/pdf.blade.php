<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Cliente - {{ $operacion->numero_radicado }}</title>
    <style>
        /* Estilos optimizados para DOMPDF */
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #0052cc;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
            border: none;
        }
        .header td {
            vertical-align: middle;
            border: none;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            text-align: right;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 11px;
            color: #666666;
            text-align: right;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #0052cc;
            background-color: #e7f0ff;
            padding: 5px 10px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .info-table, .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 5px;
            border: none;
            font-size: 12px;
        }
        .info-table .label {
            font-weight: bold;
            color: #64748b;
            width: 120px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
            padding: 8px;
            text-align: left;
            border: 1px solid #cbd5e1;
            font-size: 11px;
            text-transform: uppercase;
        }
        .data-table td {
            padding: 8px;
            border: 1px solid #cbd5e1;
            font-size: 11px;
        }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .badge {
            padding: 3px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
        }
        .badge-success { background-color: #e8f5e9; color: #2e7d32; }
        .badge-warning { background-color: #fff9c4; color: #f57f17; }
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 30px;
            font-size: 10px;
            color: #999;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>

    <!-- PIE DE PÁGINA FIJO -->
    <div class="footer">
        SIA Cartera - Generado el {{ now()->format('d/m/Y H:i') }} | Página <span class="page-number"></span>
    </div>

    <!-- CABECERA -->
    <div class="header">
        <table cellspacing="0" cellpadding="0">
            <tr>
                <td width="30%">
                    <!-- Pon la ruta absoluta o base64 de tu logo -->
                    <h2 style="color: #0052cc; margin: 0;">SIA CARTERA</h2>
                </td>
                <td width="70%">
                    <div class="title">Informe de Comportamiento</div>
                    <div class="subtitle">Radicado: <strong>{{ $operacion->numero_radicado }}</strong> | Bloque: API-{{ str_pad($operacion->numero_bloque, 4, '0', STR_PAD_LEFT) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- DATOS DEL CLIENTE -->
    <div class="section-title">Información del Cliente</div>
    <table class="info-table">
        <tr>
            <td class="label">Nombre / Razón Social:</td>
            <td>{{ $operacion->tercero->nom_ter ?? 'N/A' }} {{ $operacion->tercero->apl1 ?? '' }}</td>
            <td class="label">Identificación (NIT):</td>
            <td>{{ $operacion->tercero->cod_ter ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Teléfono:</td>
            <td>{{ $operacion->tercero->tel ?? 'No registrado' }}</td>
            <td class="label">Correo Electrónico:</td>
            <td>{{ $operacion->tercero->email ?? 'No registrado' }}</td>
        </tr>
        <tr>
            <td class="label">Ciudad:</td>
            <td colspan="3">{{ $operacion->tercero->ciudad ?? 'N/A' }}</td>
        </tr>
    </table>

    <!-- DETALLE DE LÍNEAS / FACTURAS -->
    <div class="section-title">Detalle de Operaciones y Facturas</div>

    @if(isset($operacion->lineas) && $operacion->lineas->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Factura</th>
                    <th class="text-center">Cuota</th>
                    <th>Pagaré</th>
                    <th>Vencimiento</th>
                    <th class="text-right">Valor Inicial</th>
                    <th class="text-right">Saldo a Pagar</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                @php $totalDeuda = 0; @endphp

                @foreach($operacion->lineas as $linea)
                    @php $totalDeuda += (float)$linea->valor; @endphp
                    <tr>
                        <td><strong>#{{ $linea->id_factura }}</strong></td>
                        <td class="text-center">{{ $linea->cuota ?? '-' }}</td>
                        <td>{{ $linea->pagare ?? 'N/A' }}</td>
                        <td>{{ $linea->fecha_venci ? \Carbon\Carbon::parse($linea->fecha_venci)->format('d/m/Y') : 'Sin definir' }}</td>
                        <td class="text-right">${{ number_format((float)$linea->valor_inicial, 2) }}</td>
                        <td class="text-right fw-bold">${{ number_format((float)$linea->valor, 2) }}</td>
                        <td class="text-center">
                            @if($linea->estado == 'PROCESADO')
                                <span class="badge badge-success">PROCESADO</span>
                            @else
                                <span class="badge badge-warning">PENDIENTE</span>
                            @endif
                        </td>
                    </tr>
                @endforeach

                <!-- TOTALES -->
                <tr>
                    <td colspan="5" class="text-right" style="font-weight: bold; background-color: #f8fafc;">TOTAL DEUDA ACTIVA:</td>
                    <td colspan="2" class="text-left" style="font-weight: bold; color: #b91c1c; font-size: 13px; background-color: #f8fafc;">
                        ${{ number_format($totalDeuda, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    @else
        <p style="text-align: center; color: #94a3b8; padding: 20px;">No se encontraron facturas asociadas a esta operación.</p>
    @endif

</body>
</html>
