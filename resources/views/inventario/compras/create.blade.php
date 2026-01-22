<x-base-layout>
    <style>
        .page-wrap { max-width: 900px; margin: 0 auto; padding: 40px 20px; font-family: 'Inter', sans-serif; color: #0f172a; }
        .form-header { margin-bottom: 30px; border-bottom: 1px solid #e2e8f0; padding-bottom: 20px; }
        .back-link { font-size: 0.85rem; font-weight: 600; color: #64748b; text-decoration: none; display: flex; align-items: center; gap: 5px; margin-bottom: 10px; }
        .form-title { font-size: 1.8rem; font-weight: 800; margin: 0; }
        
        .section-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 24px; overflow: hidden; }
        .section-head { background: #f8fafc; padding: 15px 24px; border-bottom: 1px solid #e2e8f0; font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; gap: 10px; }
        .section-body { padding: 24px; }
        
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #334155; }
        .input { width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.95rem; transition: 0.2s; background: #fbfcfd; }
        .input:focus { outline: none; border-color: #0f172a; background: #fff; }
        
        .btn-submit { background: #000; color: #fff; border: none; padding: 14px 30px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 1rem; }
        .btn-submit:hover { background: #222; }
    </style>

    <div class="page-wrap">
        <form action="{{ route('inventario.compras.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-header">
                <a href="{{ route('inventario.compras.index') }}" class="back-link"><i class="bi bi-arrow-left"></i> Cancelar</a>
                <h1 class="form-title">Nueva Compra</h1>
            </div>

            {{-- DATOS FACTURA --}}
            <div class="section-card">
                <div class="section-head"><i class="bi bi-receipt"></i> Datos de Facturación</div>
                <div class="section-body">
                    <div class="grid-2">
                        <div>
                            <label class="label">Número de Factura *</label>
                            <input type="text" name="numero_factura" class="input" placeholder="Ej: F-2024-001" required>
                        </div>
                        <div>
                            <label class="label">Fecha de Emisión *</label>
                            <input type="date" name="fecha_factura" class="input" required>
                        </div>
                        <div>
                            <label class="label">Método de Pago</label>
                            <select name="id_InvMetodos" class="input">
                                @foreach($metodos as $metodo)
                                    <option value="{{ $metodo->id }}">{{ $metodo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="label">Total Factura ($)</label>
                            <input type="number" name="total_pago" class="input" step="0.01" placeholder="0.00">
                        </div>
                        <div style="grid-column: span 2;">
                            <label class="label">Adjuntar PDF</label>
                            <input type="file" name="eg_archivo" class="input" style="padding: 7px;">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ITEMS --}}
            <div class="section-card">
                <div class="section-head"><i class="bi bi-box-seam"></i> Detalle de Items (Stock)</div>
                <div class="section-body">
                    <div style="background: #f1f5f9; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                        <div class="grid-2">
                            <div>
                                <label class="label">Cantidad</label>
                                <input type="number" name="detalles[0][cantidad]" class="input" value="1" min="1">
                            </div>
                            <div>
                                <label class="label">Costo Unitario</label>
                                <input type="number" name="detalles[0][precio]" class="input" placeholder="0.00" step="0.01">
                            </div>
                        </div>
                    </div>
                    <p style="font-size: 0.8rem; color: #64748b;">* Al guardar, se crearán los activos automáticamente en el almacén.</p>
                </div>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn-submit">Guardar e Ingresar</button>
            </div>
        </form>
    </div>
</x-base-layout>