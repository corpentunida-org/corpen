<x-base-layout>
    <style>
        :root {
            --primary: #0f172a;
            --primary-hover: #1e293b;
            --success: #10b981;
            --danger: #ef4444;
            --slate-200: #e2e8f0;
            --slate-600: #475569;
        }

        .page-wrap { max-width: 1200px; margin: 0 auto; padding: 40px 20px; font-family: 'Inter', sans-serif; color: var(--primary); }
        
        /* Encabezado */
        .header-actions { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; border-bottom: 1px solid var(--slate-200); padding-bottom: 20px; }
        .back-link { font-size: 0.85rem; font-weight: 600; color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 10px; transition: 0.2s; }
        .back-link:hover { color: var(--primary); transform: translateX(-3px); }
        .page-title { font-size: 2rem; font-weight: 800; margin: 0; letter-spacing: -0.025em; }

        /* Botones */
        .btn-action { background: #fff; border: 1px solid var(--slate-200); color: #334155; padding: 10px 20px; border-radius: 10px; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: 0.2s; }
        .btn-action:hover { background: #f8fafc; border-color: #94a3b8; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .btn-primary { background: var(--primary); border-color: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-hover); transform: translateY(-1px); }

        /* Cards */
        .card { background: #fff; border: 1px solid var(--slate-200); border-radius: 16px; margin-bottom: 24px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card-header { background: #f8fafc; padding: 18px 24px; border-bottom: 1px solid var(--slate-200); font-weight: 700; color: var(--slate-600); display: flex; align-items: center; gap: 10px; }
        .card-body { padding: 24px; }

        /* Formulario */
        .info-label { font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 6px; text-transform: uppercase; display: block; }
        .form-control { border: 1.5px solid var(--slate-200); border-radius: 10px; padding: 10px 14px; width: 100%; font-size: 0.95rem; transition: 0.2s; }
        .form-control:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1); }

        /* Archivo S3 */
        .file-status-box { display: flex; align-items: center; gap: 10px; margin-top: 8px; padding: 8px 12px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 0.8rem; }
        .btn-view-file { color: #166534; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; }
        .btn-view-file:hover { text-decoration: underline; color: #064e3b; }

        /* Tabla */
        .table-items { width: 100%; border-collapse: collapse; }
        .table-items th { text-align: left; padding: 14px 20px; background: #f1f5f9; font-size: 0.75rem; color: var(--slate-600); text-transform: uppercase; }
        .table-items td { padding: 14px 20px; border-bottom: 1px solid var(--slate-200); vertical-align: middle; }
        .item-row:hover { background-color: #f8fafc; }

        /* Visualización Dinero */
        .input-money-group { position: relative; display: flex; align-items: center; }
        .input-money-group::before { content: '$'; position: absolute; left: 12px; color: #94a3b8; font-weight: 700; z-index: 5; }
        .input-money-group .form-money { padding-left: 28px; text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 700; color: #0f172a; }

        /* Resumen Final */
        .summary-section { background: #f8fafc; padding: 30px; border-top: 2px solid var(--slate-200); }
        .total-box { background: var(--primary); padding: 30px; border-radius: 14px; text-align: right; color: white; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .total-amount { font-size: 2.5rem; font-weight: 800; font-family: monospace; display: block; }
        
        .remove-row { color: var(--danger); border: none; background: none; font-size: 1.2rem; cursor: pointer; opacity: 0.5; transition: 0.2s; }
        .remove-row:hover { opacity: 1; transform: scale(1.2); }
    </style>

    <div class="page-wrap">
        <form action="{{ route('inventario.compras.update', $compra->id) }}" method="POST" enctype="multipart/form-data" id="compra-form">
            @csrf @method('PUT')

            <div class="header-actions">
                <div>
                    <a href="{{ route('inventario.compras.show', $compra->id) }}" class="back-link"><i class="bi bi-arrow-left-circle-fill"></i> Regresar</a>
                    <h1 class="page-title">Editar Factura <span style="color: #94a3b8;">#{{ $compra->numero_factura }}</span></h1>
                </div>
                <button type="submit" class="btn-action btn-primary"><i class="bi bi-cloud-check-fill"></i> Guardar Cambios</button>
            </div>

            {{-- SECCIÓN 1: CABECERA --}}
            <div class="card">
                <div class="card-header"><i class="bi bi-receipt-cutoff"></i> Información de Cabecera</div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                        <div>
                            <span class="info-label">Proveedor</span>
                            <select name="cod_ter_proveedor" class="form-control" required>
                                @foreach($proveedores as $p)
                                    <option value="{{ $p->cod_ter }}" {{ $compra->cod_ter_proveedor == $p->cod_ter ? 'selected' : '' }}>
                                        {{ $p->nom_ter }} ({{ $p->cod_ter }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <span class="info-label">N° de Factura</span>
                            <input type="text" name="numero_factura" value="{{ $compra->numero_factura }}" class="form-control" required>
                        </div>
                        <div>
                            <span class="info-label">Fecha de Factura</span>
                            <input type="date" name="fecha_factura" value="{{ $compra->fecha_factura->format('Y-m-d') }}" class="form-control" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 24px;">
                        <div>
                            <span class="info-label">Método de Pago</span>
                            <select name="id_InvMetodos" class="form-control" required>
                                @foreach($metodos as $m)
                                    <option value="{{ $m->id }}" {{ $compra->id_InvMetodos == $m->id ? 'selected' : '' }}>{{ $m->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <span class="info-label">Doc. Interno</span>
                            <input type="text" name="num_doc_interno" value="{{ $compra->num_doc_interno }}" class="form-control">
                        </div>
                        <div>
                            <span class="info-label">Egreso N°</span>
                            <input type="text" name="numero_egreso" value="{{ $compra->numero_egreso }}" class="form-control">
                        </div>
                        <div>
                            <span class="info-label">Actualizar factura</span>
                            <input type="file" name="eg_archivo" class="form-control" style="font-size: 0.8rem;">
                            
                            @if($compra->eg_archivo)
                                <div class="file-status-box">
                                    <i class="bi bi-file-earmark-check-fill" style="color: var(--success); font-size: 1.1rem;"></i>
                                    <div>
                                        <div style="color: #166534; font-weight: 600;">Factura actual disponible.</div>
                                        <a href="{{ route('inventario.compras.archivo', $compra->id) }}" target="_blank" class="btn-view-file">
                                            <i class="bi bi-eye-fill"></i> Ver factura
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 2: DETALLES --}}
            <div class="card">
                <div class="card-header" style="justify-content: space-between;">
                    <span><i class="bi bi-box-seam-fill"></i> Ítems de la Factura</span>
                    <button type="button" id="add-row" class="btn-action" style="padding: 6px 14px; font-size: 0.8rem; background: #f0fdf4; color: #166534; border-color: #bbf7d0;">
                        <i class="bi bi-plus-circle-fill"></i> Añadir Ítem
                    </button>
                </div>
                <div class="card-body" style="padding: 0; overflow-x: auto;">
                    <table class="table-items">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Referencia / Producto</th>
                                <th>Detalle</th>
                                <th style="width: 100px; text-align: center;">Cant.</th>
                                <th style="width: 180px; text-align: right;">Precio Unit.</th>
                                <th style="width: 180px; text-align: right;">Subtotal</th>
                                <th style="width: 60px;"></th>
                            </tr>
                        </thead>
                        <tbody id="details-body">
                            @foreach($compra->detalles->where('cantidades', '>', 0) as $index => $item)
                                <tr class="item-row">
                                    <td style="font-weight: 700; color: #94a3b8;">
                                        {{ $loop->iteration }}
                                        <input type="hidden" name="detalles[{{ $index }}][id]" value="{{ $item->id }}">
                                    </td>
                                    <td>
                                        <select name="detalles[{{ $index }}][invReferencias_id]" class="form-control" required>
                                            @foreach($referencias as $ref)
                                                <option value="{{ $ref->id }}" {{ $item->invReferencias_id == $ref->id ? 'selected' : '' }}>
                                                    {{ $ref->subgrupo?->nombre }} — {{ $ref->referencia }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="text" name="detalles[{{ $index }}][detalle]" value="{{ $item->detalle }}" class="form-control"></td>
                                    <td><input type="number" name="detalles[{{ $index }}][cantidad]" value="{{ (float)$item->cantidades }}" class="form-control input-qty text-center" step="any" style="font-weight: 700;"></td>
                                    <td>
                                        <div class="input-money-group">
                                            <input type="text" class="form-control form-money currency-mask" value="{{ number_format($item->precio_unitario, 0, ',', '.') }}">
                                            <input type="hidden" name="detalles[{{ $index }}][precio]" value="{{ $item->precio_unitario }}">
                                        </div>
                                    </td>
                                    <td style="text-align: right; font-weight: 800; font-family: monospace;" class="subtotal-cell">
                                        ${{ number_format($item->sub_total, 0, ',', '.') }}
                                    </td>
                                    <td style="text-align: center;">
                                        <button type="button" class="remove-row"><i class="bi bi-trash3-fill"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="summary-section">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div>
                                <span class="info-label">IVA Liquidado</span>
                                <div class="input-money-group">
                                    <input type="text" class="form-control form-money currency-mask" value="{{ number_format($extraCosts['iva'], 0, ',', '.') }}">
                                    <input type="hidden" name="costo_iva" id="costo_iva" value="{{ $extraCosts['iva'] }}">
                                </div>
                            </div>
                            <div>
                                <span class="info-label">Otros Gastos</span>
                                <div class="input-money-group">
                                    <input type="text" class="form-control form-money currency-mask" value="{{ number_format($extraCosts['varios'], 0, ',', '.') }}">
                                    <input type="hidden" name="costo_varios" id="costo_varios" value="{{ $extraCosts['varios'] }}">
                                </div>
                            </div>
                        </div>
                        <div class="total-box">
                            <span style="font-size: 0.8rem; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Total a Pagar</span>
                            <span class="total-amount" id="grand-total">${{ number_format($compra->total_pago, 0, ',', '.') }}</span>
                            <input type="hidden" name="total_pago" id="total_pago_hidden" value="{{ $compra->total_pago }}">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.getElementById('details-body');
            const fmt = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 });

            function applyMask(e) {
                let value = e.target.value.replace(/\D/g, "");
                if (value === "") value = "0";
                const hiddenInput = e.target.nextElementSibling;
                hiddenInput.value = value;
                e.target.value = new Intl.NumberFormat('es-CO').format(value);
                calculate();
            }

            function calculate() {
                let itemsSubTotal = 0;
                document.querySelectorAll('.item-row').forEach(row => {
                    const qty = parseFloat(row.querySelector('.input-qty').value) || 0;
                    const price = parseFloat(row.querySelector('input[name*="[precio]"]').value) || 0;
                    const sub = qty * price;
                    row.querySelector('.subtotal-cell').textContent = fmt.format(sub);
                    itemsSubTotal += sub;
                });
                const iva = parseFloat(document.getElementById('costo_iva').value) || 0;
                const varios = parseFloat(document.getElementById('costo_varios').value) || 0;
                const grandTotal = itemsSubTotal + iva + varios;
                document.getElementById('grand-total').textContent = fmt.format(grandTotal);
                document.getElementById('total_pago_hidden').value = grandTotal;
            }

            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('currency-mask')) applyMask(e);
                if (e.target.classList.contains('input-qty')) calculate();
            });

            document.getElementById('add-row').addEventListener('click', () => {
                const idx = Date.now();
                const rowHtml = `
                    <tr class="item-row" style="background: #f0fdf4; border-left: 4px solid var(--success);">
                        <td style="font-weight: 700; color: var(--success);">+ <input type="hidden" name="detalles[${idx}][id]" value=""></td>
                        <td>
                            <select name="detalles[${idx}][invReferencias_id]" class="form-control" required>
                                <option value="">Seleccione...</option>
                                @foreach($referencias as $ref)
                                    <option value="{{ $ref->id }}">{{ $ref->subgrupo?->nombre }} — {{ $ref->referencia }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" name="detalles[${idx}][detalle]" class="form-control"></td>
                        <td><input type="number" name="detalles[${idx}][cantidad]" value="1" class="form-control input-qty text-center" step="any" style="font-weight: 700;"></td>
                        <td>
                            <div class="input-money-group">
                                <input type="text" class="form-control form-money currency-mask" value="0">
                                <input type="hidden" name="detalles[${idx}][precio]" value="0">
                            </div>
                        </td>
                        <td style="text-align: right; font-weight: 800; font-family: monospace;" class="subtotal-cell">$ 0</td>
                        <td style="text-align: center;"><button type="button" class="remove-row"><i class="bi bi-trash3-fill"></i></button></td>
                    </tr>`;
                body.insertAdjacentHTML('beforeend', rowHtml);
            });

            body.addEventListener('click', e => {
                const btn = e.target.closest('.remove-row');
                if(btn && confirm('¿Eliminar esta línea?')) {
                    const row = btn.closest('.item-row');
                    const idInput = row.querySelector('input[name*="[id]"]');
                    if(idInput && idInput.value) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'detalles_eliminados[]';
                        input.value = idInput.value;
                        document.getElementById('compra-form').appendChild(input);
                    }
                    row.remove();
                    calculate();
                }
            });

            calculate();
        });
    </script>
</x-base-layout>