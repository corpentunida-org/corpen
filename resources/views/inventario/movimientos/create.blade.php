<x-base-layout>
    <style>
        .split-layout { display: grid; grid-template-columns: 370px 1fr; gap: 30px; max-width: 1400px; margin: 0 auto; padding: 30px; font-family: 'Inter', sans-serif; color: #1e293b; }
        @media (max-width: 900px) { .split-layout { grid-template-columns: 1fr; } }

        .panel { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); overflow: hidden; display: flex; flex-direction: column; }
        .panel-head { padding: 20px; background: #0f172a; color: #fff; font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; gap: 10px; }
        .panel-body { padding: 24px; flex-grow: 1; }

        .form-label { display: block; font-size: 0.7rem; font-weight: 800; margin-bottom: 8px; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; transition: all 0.2s; }
        .form-control:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); outline: none; }
        
        .visual-state-badge { padding: 6px 12px; border-radius: 8px; font-weight: 800; font-size: 0.75rem; background: #f1f5f9; color: #475569; display: inline-block; transition: all 0.3s; }
        
        .list-container { max-height: 550px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 12px; }
        .item-row { display: flex; align-items: center; padding: 14px 18px; border-bottom: 1px solid #f1f5f9; transition: 0.2s; cursor: pointer; }
        .item-row:hover { background: #f8fafc; }
        
        /* ESTILO PARA FILA SELECCIONADA */
        .item-row.selected { background: #eff6ff; border-left: 4px solid #4f46e5; }
        
        .chk { width: 18px; height: 18px; margin-right: 15px; accent-color: #4f46e5; cursor: pointer; }
        
        .hidden-by-filter { display: none !important; }
        
        .btn-action { width: 100%; background: #4f46e5; color: white; padding: 16px; border: none; border-radius: 12px; font-weight: 700; font-size: 1rem; cursor: pointer; transition: 0.2s; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3); }
        .btn-action:hover { background: #4338ca; transform: translateY(-2px); }

        .search-box { width: 100%; padding: 12px 12px 12px 40px; border: 1px solid #cbd5e1; border-radius: 10px; margin-bottom: 15px; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E") no-repeat 15px center; }

        .quick-filters { display: flex; gap: 10px; margin-bottom: 15px; overflow-x: auto; padding-bottom: 5px; }
        .filter-tab { padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; cursor: pointer; border: 1px solid #e2e8f0; background: #fff; color: #64748b; transition: all 0.2s; white-space: nowrap; }
        .filter-tab:hover { background: #f1f5f9; }
        .filter-tab.active { background: #0f172a; color: #fff; border-color: #0f172a; }
        
        .status-pill { font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; font-weight: 700; text-transform: uppercase; margin-left: 8px; }
        .st-disp { background: #dcfce7; color: #166534; }
        .st-asig { background: #fef3c7; color: #92400e; }
        .st-mant { background: #e0f2fe; color: #075985; }

        .autocomplete-wrapper { position: relative; margin-bottom: 20px; }
        .autocomplete-wrapper .form-control { margin-bottom: 0; padding-right: 35px; } 
        .autocomplete-list { position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #cbd5e1; border-radius: 8px; margin-top: 4px; max-height: 200px; overflow-y: auto; z-index: 50; display: none; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .autocomplete-item { padding: 12px 16px; cursor: pointer; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; color: #334155; }
        .autocomplete-item:last-child { border-bottom: none; }
        .autocomplete-item:hover { background: #eef2ff; color: #4f46e5; font-weight: 600; }
        .clear-btn { position: absolute; right: 12px; top: 32px; cursor: pointer; color: #94a3b8; display: none; font-size: 0.9rem; }
        .clear-btn:hover { color: #ef4444; }
    </style>

    @if(session('error'))
        <div style="max-width: 1300px; margin: 0 auto 20px; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 16px; border-radius: 12px; font-weight: 600;">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('inventario.movimientos.store') }}" method="POST" id="movimientoForm">
        @csrf
        <div class="split-layout">
            
            {{-- PANEL IZQUIERDO: CONFIGURACIÓN --}}
            <div class="panel" style="height: fit-content;">
                <div class="panel-head"><i class="bi bi-file-earmark-text"></i> Configuración del Acta</div>
                <div class="panel-body">
                    
                    <label class="form-label">Código Único (Automático)</label>
                    <input type="text" name="codigo_acta" class="form-control" value="ACT-{{ date('Ymd-His') }}" readonly style="background: #f8fafc; font-weight: 700; color: #475569;">

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

                    <div class="autocomplete-wrapper">
                        <label class="form-label">Funcionario Responsable / Destino</label>
                        <input type="text" id="search_funcionario" class="form-control" placeholder="🔍 Escribe para buscar funcionario..." autocomplete="off">
                        <span class="clear-btn" id="clear_funcionario" title="Limpiar selección">✖</span>
                        <input type="hidden" name="id_usersAsignado" id="id_usersAsignado" required>
                        <div id="list_funcionario" class="autocomplete-list"></div>
                    </div>

                    <label class="form-label">Observaciones Generales</label>
                    <textarea name="observacion_general" class="form-control" rows="3" placeholder="Detalles del acta o motivo de devolución..."></textarea>
                    
                    <button type="submit" class="btn-action">
                        <i class="bi bi-check-circle-fill"></i> Finalizar y Registrar
                    </button>
                </div>
            </div>

            {{-- PANEL DERECHO: SELECCIÓN DE ACTIVOS --}}
            <div class="panel">
                <div class="panel-head" style="background: #fff; color: #0f172a; border-bottom: 1px solid #e2e8f0; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="bi bi-cpu"></i> Base Global de Activos
                        <span id="counter_badge" style="background: #4f46e5; color: white; padding: 2px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; margin-left: 10px;">
                            0 seleccionados
                        </span>
                    </div>
                    <button type="button" id="btn_select_all" style="font-size: 0.75rem; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 6px 12px; border-radius: 8px; cursor: pointer; font-weight: 600;">
                        Seleccionar Todos los Visibles
                    </button>
                </div>
                
                <div class="panel-body" style="background: #fbfcfd; padding-top: 15px;">
                    
                    <div class="quick-filters" id="quickFilters">
                        <button type="button" class="filter-tab active" data-filter="all">Todos</button>
                        <button type="button" class="filter-tab" data-filter="disponible">Solo Disponibles</button>
                        <button type="button" class="filter-tab" data-filter="asignado">Equipos Asignados</button>
                        <button type="button" class="filter-tab" data-filter="otros">Otros (Mantenimiento, etc)</button>
                    </div>

                    <input type="text" id="searchInput" class="search-box" placeholder="Buscar por nombre, código, serial o actual responsable...">

                    <div class="list-container" id="activosList">
                        @forelse($activosDisponibles as $activo)
                            @php
                                $nombreEstado = strtolower($activo->estado->nombre ?? '');
                                $claseEstado = match(true) {
                                    str_contains($nombreEstado, 'disponible') => 'st-disp',
                                    str_contains($nombreEstado, 'asignado') => 'st-asig',
                                    default => 'st-mant',
                                };
                                $categoriaFiltro = match(true) {
                                    str_contains($nombreEstado, 'disponible') => 'disponible',
                                    str_contains($nombreEstado, 'asignado') => 'asignado',
                                    default => 'otros',
                                };
                            @endphp

                            <label class="item-row asset-item" 
                                   data-bodega-id="{{ $activo->referencia->id_InvBodegas ?? '' }}"
                                   data-categoria="{{ $categoriaFiltro }}">
                                
                                <input type="checkbox" name="activos_seleccionados[]" value="{{ $activo->id }}" class="chk">
                                
                                <div style="flex-grow: 1;">
                                    <div style="display: flex; align-items: center;">
                                        <span style="font-weight: 700; font-size: 0.95rem; color: #0f172a;">{{ $activo->nombre }}</span>
                                        <span class="status-pill {{ $claseEstado }}">{{ $activo->estado->nombre ?? 'N/A' }}</span>
                                    </div>
                                    <span style="font-size: 0.8rem; color: #64748b; display: block; margin-top: 2px;">
                                        Cod: <b style="color: #334155;">{{ $activo->codigo_activo }}</b> | S/N: {{ $activo->serial ?? 'N/A' }}
                                    </span>
                                    
                                    @if($activo->usuarioAsignado)
                                        <div style="font-size: 0.75rem; color: #92400e; margin-top: 4px; font-weight: 600; background: #fef3c7; display: inline-block; padding: 2px 6px; border-radius: 4px;">
                                            En poder de: {{ $activo->usuarioAsignado->name }}
                                        </div>
                                    @endif
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
                                No hay activos registrados en el sistema.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </form>

    @php
        $usuariosJson = $usuarios->map(fn($u) => ['id' => $u->id, 'text' => $u->name])->values()->toJson();
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- 1. LÓGICA DEL BUSCADOR DE FUNCIONARIOS ---
            const dataUsuarios = {!! $usuariosJson !!};
            const inputFuncionario = document.getElementById('search_funcionario');
            const hiddenId = document.getElementById('id_usersAsignado');
            const listContainer = document.getElementById('list_funcionario');
            const clearBtn = document.getElementById('clear_funcionario');

            inputFuncionario.addEventListener('input', function() {
                const val = this.value.toLowerCase().trim();
                listContainer.innerHTML = '';
                hiddenId.value = ''; 
                
                if (val.length === 0) {
                    listContainer.style.display = 'none';
                    clearBtn.style.display = 'none';
                    return;
                }

                clearBtn.style.display = 'block';
                const matches = dataUsuarios.filter(item => item.text.toLowerCase().includes(val)).slice(0, 10);
                
                if (matches.length > 0) {
                    matches.forEach(match => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.innerHTML = match.text;
                        
                        div.addEventListener('click', function() {
                            inputFuncionario.value = match.text;
                            hiddenId.value = match.id;
                            listContainer.style.display = 'none';
                        });
                        listContainer.appendChild(div);
                    });
                    listContainer.style.display = 'block';
                } else {
                    listContainer.innerHTML = '<div class="autocomplete-item" style="color: #ef4444; pointer-events: none;">No se encontraron resultados...</div>';
                    listContainer.style.display = 'block';
                }
            });

            clearBtn.addEventListener('click', function() {
                inputFuncionario.value = '';
                hiddenId.value = '';
                listContainer.style.display = 'none';
                this.style.display = 'none';
                inputFuncionario.focus();
            });

            document.addEventListener('click', function(e) {
                if (e.target !== inputFuncionario) {
                    listContainer.style.display = 'none';
                }
            });

            document.getElementById('movimientoForm').addEventListener('submit', function(e) {
                if(!hiddenId.value) {
                    e.preventDefault();
                    alert('⚠️ Por favor, busca y selecciona un Funcionario Responsable del listado desplegable.');
                    inputFuncionario.focus();
                }
                
                // Evitar que envíen el formulario en 0
                const checkedCount = document.querySelectorAll('.chk:checked').length;
                if(checkedCount === 0) {
                    e.preventDefault();
                    alert('⚠️ Debes seleccionar al menos un equipo de la lista de la derecha para registrar el acta.');
                }
            });


            // --- 2. LÓGICA DE FILTROS Y SELECCIÓN MÚLTIPLE ---
            const bodegaSelect = document.getElementById('bodega_select');
            const tipoSelect = document.getElementById('tipo_movimiento_select');
            const visualEstado = document.getElementById('visual_estado');
            const assetItems = document.querySelectorAll('.asset-item');
            const searchInput = document.getElementById('searchInput');
            const filterTabs = document.querySelectorAll('.filter-tab');
            const counterBadge = document.getElementById('counter_badge');
            
            const masterTipoOptions = Array.from(tipoSelect.options).filter(opt => opt.value !== "");

            // FUNCION PARA ACTUALIZAR CONTADOR Y COLORES DE SELECCIÓN
            function updateSelectionVisuals() {
                const checkedBoxes = document.querySelectorAll('.chk:checked');
                counterBadge.textContent = checkedBoxes.length + (checkedBoxes.length === 1 ? ' seleccionado' : ' seleccionados');
                
                assetItems.forEach(item => {
                    const checkbox = item.querySelector('.chk');
                    if (checkbox.checked) {
                        item.classList.add('selected');
                    } else {
                        item.classList.remove('selected');
                    }
                });
            }

            // Escuchar cambios en todos los checkboxes para actualizar el contador
            document.querySelectorAll('.chk').forEach(chk => {
                chk.addEventListener('change', updateSelectionVisuals);
            });

            bodegaSelect.addEventListener('change', function() {
                const selectedBodegaId = this.value;

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

                applyAllFilters();
                tipoSelect.dispatchEvent(new Event('change'));
            });

            tipoSelect.addEventListener('change', function() {
                if (this.value) {
                    const selectedText = this.options[this.selectedIndex].text;
                    const cleanText = selectedText.toLowerCase();
                    
                    visualEstado.textContent = selectedText.toUpperCase();
                    
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

            let currentTabFilter = 'all';
            
            filterTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    filterTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    currentTabFilter = this.getAttribute('data-filter');
                    applyAllFilters();
                });
            });

            searchInput.addEventListener('input', applyAllFilters);

            function applyAllFilters() {
                const selectedBodegaId = bodegaSelect.value;
                const searchTerm = searchInput.value.toLowerCase();

                assetItems.forEach(item => {
                    const itemBodegaId = item.getAttribute('data-bodega-id');
                    const itemCategoria = item.getAttribute('data-categoria');
                    const textContent = item.textContent.toLowerCase();

                    let isVisible = true;

                    if (selectedBodegaId && itemBodegaId != selectedBodegaId) isVisible = false;
                    if (currentTabFilter !== 'all' && itemCategoria !== currentTabFilter) isVisible = false;
                    if (searchTerm && !textContent.includes(searchTerm)) isVisible = false;

                    // CORRECCIÓN MAGISTRAL: Ahora solo aplicamos visibilidad. NUNCA tocamos el .checked aquí.
                    if (isVisible) {
                        item.classList.remove('hidden-by-filter');
                        item.style.display = 'flex';
                    } else {
                        item.classList.add('hidden-by-filter');
                        item.style.display = 'none';
                    }
                });
            }

            // Seleccionar Todos los Visibles
            document.getElementById('btn_select_all').addEventListener('click', function() {
                // Solo selecciona los que NO están ocultos
                const visibleCheckboxes = document.querySelectorAll('.asset-item:not(.hidden-by-filter) .chk');
                const allChecked = Array.from(visibleCheckboxes).every(cb => cb.checked);
                
                visibleCheckboxes.forEach(cb => {
                    cb.checked = !allChecked;
                });
                
                this.textContent = allChecked ? 'Seleccionar Todos los Visibles' : 'Desmarcar Visibles';
                updateSelectionVisuals(); // Actualizar contador
            });
        });
    </script>
</x-base-layout>