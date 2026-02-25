<x-base-layout>
    <style>
        .page-wrap { max-width: 1100px; margin: 0 auto; padding: 40px 20px; font-family: 'Inter', sans-serif; color: #0f172a; }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #e2e8f0; padding-bottom: 20px; }
        .back-link { font-size: 0.85rem; font-weight: 600; color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; margin-bottom: 10px; transition: color 0.2s; }
        .back-link:hover { color: #0f172a; }
        .page-title { font-size: 1.8rem; font-weight: 800; margin: 0; }
        
        .btn-action { background: #fff; border: 1px solid #cbd5e1; color: #334155; padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; cursor: pointer; }
        .btn-action:hover { background: #f8fafc; border-color: #94a3b8; color: #0f172a; }
        .btn-primary { background: #0f172a; border-color: #0f172a; color: #fff; }
        .btn-primary:hover { background: #1e293b; border-color: #1e293b; color: #fff; }
        .btn-aws { background: #fff7ed; border-color: #fdba74; color: #ea580c; }
        .btn-aws:hover { background: #ffedd5; border-color: #f97316; color: #c2410c; }

        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 24px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .card-header { background: #f8fafc; padding: 16px 24px; border-bottom: 1px solid #e2e8f0; font-weight: 700; font-size: 0.95rem; color: #334155; display: flex; align-items: center; gap: 8px; }
        .card-body { padding: 24px; }
        
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; }
        .info-group { display: flex; flex-direction: column; gap: 4px; }
        .info-label { font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #64748b; letter-spacing: 0.5px; }
        .info-value { font-size: 1rem; font-weight: 600; color: #0f172a; }
        
        .table-responsive { overflow-x: auto; }
        .table-items { width: 100%; border-collapse: collapse; margin-top: 10px; min-width: 800px; }
        .table-items th { text-align: left; padding: 12px 16px; background: #f1f5f9; font-size: 0.8rem; text-transform: uppercase; color: #475569; border-radius: 6px 6px 0 0; }
        .table-items td { padding: 16px; border-bottom: 1px solid #e2e8f0; font-size: 0.95rem; color: #334155; vertical-align: middle; }
        .table-items tr:last-child td { border-bottom: none; }
        .td-money { font-family: monospace; font-size: 1rem; }
        
        .total-box { background: #f8fafc; padding: 20px; border-radius: 8px; margin-top: 20px; text-align: right; border: 1px solid #e2e8f0; }
        .total-label { font-size: 0.9rem; font-weight: 600; color: #64748b; margin-right: 15px; }
        .total-amount { font-size: 1.5rem; font-weight: 800; color: #0f172a; font-family: monospace; }
        
        /* Fila destacada para IVA o Costos extra */
        .row-extra-cost { background-color: #f8fafc; }
        .row-extra-cost td { border-bottom: 1px dashed #cbd5e1; }
    </style>

    <div class="page-wrap">
        
        <div class="header-actions">
            <div>
                <a href="{{ route('inventario.compras.index') }}" class="back-link">
                    <i class="bi bi-arrow-left"></i> Volver al historial
                </a>
                <h1 class="page-title">Detalle de Compra</h1>
            </div>
            <div style="display: flex; gap: 10px;">
                {{-- Botón AWS S3 --}}
                @if($compra->eg_archivo)
                    <a href="{{ route('inventario.compras.archivo', $compra->id) }}" target="_blank" class="btn-action btn-aws" title="Ver archivo original en AWS">
                        <i class="bi bi-cloud-arrow-down"></i> Ver Soporte (AWS)
                    </a>
                @endif
                
                {{-- Botón PDF del sistema --}}
                <a href="{{ route('inventario.compras.descargar', $compra->id) }}" class="btn-action btn-primary">
                    <i class="bi bi-file-pdf"></i> Generar PDF
                </a>
            </div>
        </div>

        {{-- TARJETA: INFORMACIÓN DEL PROVEEDOR --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-building"></i> Información del Proveedor</div>
            <div class="card-body">
                <div class="info-grid" style="grid-template-columns: 2fr 1fr;">
                    <div class="info-group">
                        <span class="info-label">Razón Social / Nombre</span>
                        <span class="info-value" style="font-size: 1.1rem;">
                            {{ $compra->proveedor->nom_ter ?? 'Proveedor No Encontrado' }} 
                            {{ $compra->proveedor->razon_soc ? ' - ' . $compra->proveedor->razon_soc : '' }}
                        </span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">NIT / Documento</span>
                        <span class="info-value">{{ $compra->cod_ter_proveedor }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- TARJETA: DATOS DE LA FACTURA Y PAGO --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-receipt"></i> Datos de Facturación</div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-group">
                        <span class="info-label">N° de Factura</span>
                        <span class="info-value" style="font-size: 1.2rem; color: #0284c7;">{{ $compra->numero_factura }}</span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Fecha de Emisión</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($compra->fecha_factura)->format('d \d\e F, Y') }}</span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Método de Pago</span>
                        <span class="info-value">{{ $compra->metodo->nombre ?? 'N/A' }}</span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">N° Doc. Interno</span>
                        <span class="info-value">{{ $compra->num_doc_interno ?: 'No registrado' }}</span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">N° de Egreso</span>
                        <span class="info-value">{{ $compra->numero_egreso ?: 'No registrado' }}</span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Registrado en sistema por</span>
                        <span class="info-value">{{ $compra->usuarioRegistro->name ?? 'Sistema' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- TARJETA: DETALLE DE ITEMS (PRODUCTOS) --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-box-seam"></i> Detalle de Artículos Ingresados y Costos</div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table-items">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 25%;">Producto / Referencia</th>
                                <th style="width: 25%;">Detalle</th>
                                <th style="width: 10%; text-align: center;">Cantidad</th>
                                <th style="width: 15%; text-align: right;">Costo Unit.</th>
                                <th style="width: 20%; text-align: right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($compra->detalles as $index => $detalle)
                                {{-- Si la cantidad es 0, asumimos que es un registro de IVA o Costo Extra --}}
                                @if($detalle->cantidades == 0)
                                    <tr class="row-extra-cost">
                                        <td style="font-weight: 700; color: #94a3b8;">*</td>
                                        <td colspan="3" style="font-weight: 600; color: #475569; text-align: right;">
                                            {{ $detalle->detalle ?: 'Costo Adicional' }}
                                        </td>
                                        <td style="text-align: right; color: #475569;" class="td-money">
                                            ${{ number_format($detalle->precio_unitario, 2, ',', '.') }}
                                        </td>
                                        <td style="text-align: right; font-weight: 700;" class="td-money">
                                            ${{ number_format($detalle->precio_unitario, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @else
                                    {{-- Fila normal de producto --}}
                                    <tr>
                                        <td style="font-weight: 700; color: #94a3b8;">{{ $index + 1 }}</td>
                                        <td style="font-weight: 600; color: #0f172a;">
                                            {{ $detalle->referencia->nombre ?? 'N/A' }}
                                        </td>
                                        <td style="font-size: 0.9rem; color: #64748b;">
                                            {{ $detalle->detalle ?: '-' }}
                                        </td>
                                        <td style="text-align: center; font-weight: 600;">
                                            {{ $detalle->cantidades }}
                                        </td>
                                        <td style="text-align: right;" class="td-money">
                                            ${{ number_format($detalle->precio_unitario, 2, ',', '.') }}
                                        </td>
                                        <td style="text-align: right; font-weight: 700;" class="td-money">
                                            ${{ number_format($detalle->sub_total, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- TOTAL GENERAL DE LA COMPRA --}}
            <div class="card-body" style="padding-top: 0;">
                <div class="total-box">
                    <span class="total-label">TOTAL FACTURA:</span>
                    <span class="total-amount">${{ number_format($compra->total_pago, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

    </div>
</x-base-layout>