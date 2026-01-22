<x-base-layout>
    <style>
        .maint-wrapper { max-width: 700px; margin: 40px auto; font-family: 'Inter', sans-serif; color: #0f172a; }
        .card { background: #fff; border-radius: 16px; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.05); padding: 40px; border: 1px solid #f1f5f9; }
        
        .head-title { text-align: center; margin-bottom: 30px; }
        .head-title h2 { font-size: 1.8rem; font-weight: 800; margin: 0; }
        .head-title p { color: #64748b; font-size: 0.9rem; margin-top: 5px; }

        .field { margin-bottom: 20px; }
        .label { display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 8px; }
        .input { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 1rem; }
        .input:focus { outline: none; border-color: #0f172a; }
        
        .cost-input { position: relative; }
        .currency { position: absolute; left: 12px; top: 12px; font-weight: bold; color: #64748b; }
        .cost-field { padding-left: 30px; }

        .btn-submit { width: 100%; background: #000; color: #fff; padding: 15px; border-radius: 10px; font-weight: 700; font-size: 1rem; border: none; cursor: pointer; margin-top: 10px; }
        .btn-submit:hover { background: #222; }
    </style>

    <div class="maint-wrapper">
        <div class="card">
            <div class="head-title">
                <h2>Registrar Mantenimiento</h2>
                <p>Ingrese los detalles del servicio técnico realizado.</p>
            </div>

            <form action="{{ route('inventario.mantenimientos.store') }}" method="POST">
                @csrf
                
                <div class="field">
                    <label class="label">Activo Afectado</label>
                    <select name="id_InvActivos" class="input">
                        <option value="">Buscar activo por placa o nombre...</option>
                        @foreach($activos as $activo)
                            <option value="{{ $activo->id }}">{{ $activo->codigo_activo }} - {{ $activo->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label class="label">Costo del Servicio</label>
                    <div class="cost-input">
                        <span class="currency">$</span>
                        <input type="number" name="costo_mantenimiento" class="input cost-field" placeholder="0.00" step="0.01">
                    </div>
                </div>

                <div class="field">
                    <label class="label">Detalle Técnico / Reparación</label>
                    <textarea name="detalle" class="input" rows="4" placeholder="Describa el fallo y la solución aplicada..."></textarea>
                </div>

                <button type="submit" class="btn-submit">Registrar Evento</button>
            </form>
        </div>
    </div>
</x-base-layout>