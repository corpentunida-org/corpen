<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $compra->numero_factura }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 20px; }
        .header { width: 100%; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header td { vertical-align: top; }
        .title { font-size: 24px; font-weight: bold; margin: 0; }
        
        .info-table { width: 100%; margin-bottom: 30px; }
        .info-table td { width: 50%; vertical-align: top; padding: 5px; }
        .info-label { font-weight: bold; font-size: 12px; color: #555; text-transform: uppercase; }
        .info-value { font-size: 14px; margin-bottom: 10px; }

        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { background-color: #f4f4f4; padding: 10px; text-align: left; border-bottom: 1px solid #ddd; font-size: 12px; }
        .items-table td { padding: 10px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        /* Estilos para la fila de IVA y Costos Varios */
        .extra-cost-row { background-color: #fafafa; }
        .extra-cost-row td { border-bottom: 1px dashed #ccc; }
        
        .total-box { width: 100%; text-align: right; margin-top: 20px; font-size: 18px; }
        .total-label { font-weight: bold; margin-right: 15px; }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td>
                <h1 class="title">COMPRA DE INVENTARIO</h1>
                <p style="margin: 5px 0; color: #666;">Registrado por: {{ $compra->usuarioRegistro->name ?? 'Sistema' }}</p>
            </td>
            <td class="text-right">
                <h2 style="margin: 0; color: #000;">N° {{ $compra->numero_factura }}</h2>
                <p style="margin: 5px 0;">Emisión: {{ \Carbon\Carbon::parse($compra->fecha_factura)->format('d/m/Y') }}</p>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td>
                <div class="info-label">Proveedor</div>
                <div class="info-value">
                    {{ $compra->proveedor->nom_ter ?? 'No Registrado' }} 
                    {{ $compra->proveedor->razon_soc ? ' - ' . $compra->proveedor->razon_soc : '' }}<br>
                    NIT: {{ $compra->cod_ter_proveedor }}
                </div>
            </td>
            <td>
                <div class="info-label">Detalles de Pago</div>
                <div class="info-value">
                    Método: {{ $compra->metodo->nombre ?? 'N/A' }}<br>
                    N° Egreso: {{ $compra->numero_egreso ?: 'N/A' }}<br>
                    Doc. Interno: {{ $compra->num_doc_interno ?: 'N/A' }}
                </div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Producto / Referencia</th>
                <th style="width: 20%;">Detalle</th>
                <th class="text-center" style="width: 10%;">Cantidad</th>
                <th class="text-right" style="width: 20%;">Costo Unit.</th>
                <th class="text-right" style="width: 20%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($compra->detalles as $index => $detalle)
                @if($detalle->cantidades == 0)
                    {{-- Fila para IVA o Costos Adicionales --}}
                    <tr class="extra-cost-row">
                        <td>*</td>
                        <td colspan="3" class="text-right" style="font-weight: bold; color: #555;">
                            {{ $detalle->detalle ?: 'Costo Adicional' }}
                        </td>
                        <td class="text-right">
                            ${{ number_format($detalle->precio_unitario, 2, ',', '.') }}
                        </td>
                        <td class="text-right" style="font-weight: bold;">
                            ${{ number_format($detalle->precio_unitario, 2, ',', '.') }}
                        </td>
                    </tr>
                @else
                    {{-- Fila de Producto Normal --}}
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $detalle->referencia->nombre ?? 'N/A' }}</td>
                        <td style="font-size: 12px; color: #666;">{{ $detalle->detalle ?: '-' }}</td>
                        <td class="text-center">{{ $detalle->cantidades }}</td>
                        <td class="text-right">${{ number_format($detalle->precio_unitario, 2, ',', '.') }}</td>
                        <td class="text-right">${{ number_format($detalle->sub_total, 2, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        <span class="total-label">TOTAL FACTURA:</span>
        <span>${{ number_format($compra->total_pago, 2, ',', '.') }}</span>
    </div>

</body>
</html>