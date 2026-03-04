<x-base-layout>
    <style>
        .split-layout { display: grid; grid-template-columns: 370px 1fr; gap: 30px; max-width: 1400px; margin: 0 auto; padding: 30px; font-family: 'Inter', sans-serif; color: #1e293b; }
        @media (max-width: 900px) { .split-layout { grid-template-columns: 1fr; } }

        .panel { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); overflow: hidden; display: flex; flex-direction: column; }
        .panel-head { padding: 20px; background: #0f172a; color: #fff; font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; gap: 10px; }
        .panel-body { padding: 24px; flex-grow: 1; }

        .form-label { display: block; font-size: 0.7rem; font-weight: 800; margin-bottom: 8px; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; transition: all 0.2s; }
        .form-control:focus { border-color: #4f46e5; ring: 3px rgba(79, 70, 229, 0.1); outline: none; }
        
        .visual-state-badge { padding: 6px 12px; border-radius: 8px; font-weight: 800; font-size: 0.75rem; background: #f1f5f9; color: #475569; display: inline-block; transition: all 0.3s; }
        
        .list-container { max-height: 550px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 12px; }
        .item-row { display: flex; align-items: center; padding: 14px 18px; border-bottom: 1px solid #f1f5f9; transition: 0.2s; cursor: pointer; }
        .item-row:hover { background: #f8fafc; }
        .item-row.selected { background: #eff6ff; border-left: 4px solid #4f46e5; }
        
        .chk { width: 18px; height: 18px; margin-right: 15px; accent-color: #4f46e5; cursor: pointer; }
        
        .hidden-by-filter { display: none !important; }
        
        .btn-action { width: 100%; background: #4f46e5; color: white; padding: 16px; border: none; border-radius: 12px; font-weight: 700; font-size: 1rem; cursor: pointer; transition: 0.2s; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3); }
        .btn-action:hover { background: #4338ca; transform: translateY(-2px); }

        .search-box { width: 100%; padding: 12px 12px 12px 40px; border: 1px solid #cbd5e1; border-radius: 10px; margin-bottom: 15px; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E") no-repeat 15px center; }
    </style>

    {{-- Notificaciones --}}
    @if(session('error'))
        <div style="max-width: 1300px; margin: 0 auto 20px; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 16px; border-radius: 12px; font-weight: 600;">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('inventario.movimientos.store') }}" method="POST">
        @csrf
        <div class="split-layout">
            
            {{-- PANEL IZQUIERDO: CONFIGURACIÓN --}}
            <div class="panel" style="height: fit-content;">
                <div class="panel-head"><i class="bi bi-file-earmark-text"></i> Configuración del Acta</div>
                <div class="panel-body">
                    
                    <label class="form-label">Código Único (Automático)</label>
                    <input type="text" name="codigo_acta" class="form-control" value="ACT-{{ date('Ymd-His') }}" readonly style="background: #f8fafc; font-weight: 700; color: #475569;">

                    {{-- FILTRO VISUAL: No se envía al controlador --}}
                    <label class="form-label">Bodega de Origen (Filtro Visual)</label>
                    <select id="bodega_select" class="form-control" style="border-color: #4f46e5; background: #f5f3ff;">
                        <option value="">Seleccione Bodega para filtrar...</option>
                        @foreach($bodegas as $bodega)
                            <option value="{{ $bodega->id }}">{{ $bodega->nombre }}</option>
                        @endforeach
                    </select>

                    <label class="form-label">Tipo de Movimiento (Estado Resultante)</label>
                    <select name="id_InvTiposRegistros" id="tipo_movimiento_select" class="form-control" required disabled>
                        <option value="">Primero seleccione una bodega...</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id }}" data-bodega="{{ $tipo->id_bodega }}">
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>

                    <div style="margin-bottom: 25px;">
                        <span class="form-label">Indicador de Estado:</span>
                        <span id="visual_estado" class="visual-state-badge">Esperando selección...</span>
                    </div>

                    <label class="form-label">Funcionario Responsable</label>
                    <select name="id_usersAsignado" class="form-control" required>
                        <option value="">Seleccione el funcionario...</option>
                        @foreach($usuarios as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>

                    <label class="form-label">Observaciones Generales</label>
                    <textarea name="observacion_general" class="form-control" rows="3" placeholder="Detalles del acta..."></textarea>
                    
                    <button type="submit" class="btn-action">
                        <i class="bi bi-check-circle-fill"></i> Finalizar y Registrar
                    </button>
                </div>
            </div>

            {{-- PANEL DERECHO: SELECCIÓN DE ACTIVOS --}}
            <div class="panel">
                <div class="panel-head" style="background: #fff; color: #0f172a; border-bottom: 1px solid #e2e8f0; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="bi bi-cpu"></i> Selección de Activos Disponibles
                    </div>
                    <button type="button" id="btn_select_all" style="font-size: 0.75rem; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 6px 12px; border-radius: 8px; cursor: pointer; font-weight: 600;">
                        Seleccionar Todos los Visibles
                    </button>
                </div>
                
                <div class="panel-body" style="background: #fbfcfd;">
                    <input type="text" id="searchInput" class="search-box" placeholder="Filtrar por nombre, marquilla, serial o marca...">

                    <div class="list-container" id="activosList">
                        @forelse($activosDisponibles as $activo)
                            <label class="item-row asset-item" data-bodega-id="{{ $activo->referencia->id_InvBodegas ?? '' }}">
                                <input type="checkbox" name="activos_seleccionados[]" value="{{ $activo->id }}" class="chk">
                                <div style="flex-grow: 1;">
                                    <span style="font-weight: 700; font-size: 0.95rem; color: #0f172a; display: block;">{{ $activo->nombre }}</span>
                                    <span style="font-size: 0.8rem; color: #64748b;">
                                        Placa/Marquilla: <b style="color: #334155;">{{ $activo->codigo_activo }}</b> | S/N: {{ $activo->serial ?? 'N/A' }}
                                    </span>
                                </div>
                                <div style="text-align: right;">
                                    <span style="font-size: 0.75rem; font-weight: 800; color: #4f46e5; background: #eef2ff; padding: 4px 8px; border-radius: 6px;">
                                        {{ $activo->referencia->marca->nombre ?? 'N/A' }}
                                    </span>
                                    <small style="display: block; margin-top: 4px; color: #94a3b8; font-size: 0.65rem;">
                                        {{ $activo->referencia->bodega->nombre ?? 'Bodega N/A' }}
                                    </small>
                                </div>
                            </label>
                        @empty
                            <div style="padding: 60px; text-align: center; color: #94a3b8;">
                                <i class="bi bi-box-seam" style="font-size: 3rem; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                                No hay activos disponibles con estado "Disponible".
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bodegaSelect = document.getElementById('bodega_select');
            const tipoSelect = document.getElementById('tipo_movimiento_select');
            const visualEstado = document.getElementById('visual_estado');
            const assetItems = document.querySelectorAll('.asset-item');
            const searchInput = document.getElementById('searchInput');
            
            // Backup de todas las opciones de estados (tipos de movimiento)
            const masterTipoOptions = Array.from(tipoSelect.options).filter(opt => opt.value !== "");

            // 1. FILTRADO MAESTRO AL CAMBIAR BODEGA
            bodegaSelect.addEventListener('change', function() {
                const selectedBodegaId = this.value;

                // A. Filtrar Dropdown de Estados (id_bodega)
                tipoSelect.innerHTML = '<option value="">Seleccione el tipo...</option>';
                if (selectedBodegaId) {
                    tipoSelect.disabled = false;
                    const filteredTypes = masterTipoOptions.filter(opt => opt.getAttribute('data-bodega') == selectedBodegaId);
                    
                    if(filteredTypes.length > 0) {
                        filteredTypes.forEach(opt => tipoSelect.appendChild(opt.cloneNode(true)));
                    } else {
                        tipoSelect.innerHTML = '<option value="">Sin estados para esta bodega</option>';
                    }
                } else {
                    tipoSelect.disabled = true;
                }

                // B. Filtrar Lista de Activos (id_InvBodegas)
                assetItems.forEach(item => {
                    const itemBodegaId = item.getAttribute('data-bodega-id');
                    if (!selectedBodegaId || itemBodegaId == selectedBodegaId) {
                        item.classList.remove('hidden-by-filter');
                    } else {
                        item.classList.add('hidden-by-filter');
                        item.querySelector('.chk').checked = false; // Desmarcar si se oculta
                    }
                });
                
                // Reiniciar visualizador de badge
                tipoSelect.dispatchEvent(new Event('change'));
            });

            // 2. INDICADOR VISUAL DE ESTADO (Badge)
            tipoSelect.addEventListener('change', function() {
                if (this.value) {
                    const selectedText = this.options[this.selectedIndex].text;
                    const cleanText = selectedText.toLowerCase();
                    
                    visualEstado.textContent = selectedText.toUpperCase();
                    
                    // Lógica de colores dinámica
                    if (cleanText.includes('disponible') || cleanText.includes('entregado')) {
                        visualEstado.style.background = '#d1fae5'; visualEstado.style.color = '#065f46';
                    } else if (cleanText.includes('asignado') || cleanText.includes('prestamo')) {
                        visualEstado.style.background = '#fef3c7'; visualEstado.style.color = '#92400e';
                    } else if (cleanText.includes('baja') || cleanText.includes('dañado')) {
                        visualEstado.style.background = '#fee2e2'; visualEstado.style.color = '#991b1b';
                    } else {
                        visualEstado.style.background = '#e0f2fe'; visualEstado.style.color = '#075985';
                    }
                } else {
                    visualEstado.textContent = 'ESPERANDO SELECCIÓN...';
                    visualEstado.style.background = '#f1f5f9'; visualEstado.style.color = '#475569';
                }
            });

            // 3. BUSCADOR EN TIEMPO REAL
            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                assetItems.forEach(item => {
                    // Solo buscar entre los que ya pasaron el filtro de bodega
                    if (!item.classList.contains('hidden-by-filter')) {
                        const text = item.textContent.toLowerCase();
                        item.style.display = text.includes(term) ? 'flex' : 'none';
                    }
                });
            });

            // 4. SELECCIONAR TODOS LOS VISIBLES
            document.getElementById('btn_select_all').addEventListener('click', function() {
                const visibleCheckboxes = document.querySelectorAll('.asset-item:not(.hidden-by-filter)[style*="display: flex"] .chk, .asset-item:not(.hidden-by-filter):not([style*="display: none"]) .chk');
                const allChecked = Array.from(visibleCheckboxes).every(cb => cb.checked);
                visibleCheckboxes.forEach(cb => cb.checked = !allChecked);
                this.textContent = allChecked ? 'Seleccionar Todos los Visibles' : 'Desmarcar Todos';
            });
        });
    </script>
</x-base-layout>