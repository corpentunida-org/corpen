{{-- resources/views/flujo/componentes/workflows-card.blade.php --}}

<div class="projects-card-container" id="workflows-container">
    <header class="card-header-minimal">
        <div class="header-info">
            <h2 class="card-title"><i class="fas fa-project-diagram"></i> Proyectos</h2>
            <p class="subtitle">{{ $workflows->total() }} flujos en sistema</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('flujo.workflows.create') }}" class="btn-new-minimal">
                <i class="fas fa-plus"></i> Nuevo Proyecto
            </a>
        </div>
    </header>
    
    <div class="card-toolbar-soft">
        <form id="search-form" class="search-box-soft">
            <i class="fas fa-search"></i>
            <input type="text" name="search" id="search-input" placeholder="Búsqueda rápida..." value="{{ request('search') }}">
        </form>

        <div class="filter-dropdown">
            <button type="button" class="btn-filter-trigger" id="filterTrigger">
                <i class="fas fa-sliders-h"></i> <span>Filtros Avanzados</span>
            </button>
            <div class="filter-popover" id="filterPopover">
                <form id="filters-form" class="filters-form">
                    {{-- Campo oculto para mantener la búsqueda global --}}
                    <input type="hidden" name="search" id="filter-search" value="{{ request('search') }}">
                    
                    <div class="accordion-container">
                        {{-- Acordeón 1: Nombre del Proceso --}}
                        <div class="accordion-item">
                            <div class="accordion-header" data-target="nombre-filter">
                                <label>Nombre del Proceso</label>
                                <i class="fas fa-chevron-down accordion-icon"></i>
                            </div>
                            <div class="accordion-content" id="nombre-filter">
                                <div class="filter-group">
                                    <input type="text" name="nombre" id="filter-nombre" placeholder="Nombre específico..." value="{{ request('nombre') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Acordeón 2: Estado --}}
                        @isset($estados)
                            <div class="accordion-item">
                                <div class="accordion-header" data-target="estado-filter">
                                    <label>Estado</label>
                                    <i class="fas fa-chevron-down accordion-icon"></i>
                                </div>
                                <div class="accordion-content" id="estado-filter">
                                    <div class="filter-group">
                                        <select name="estado" id="filter-estado">
                                            <option value="">Todos</option>
                                            @foreach($estados as $key => $value)
                                                <option value="{{ $key }}" {{ request('estado') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endisset

                        {{-- Acordeón 3: Prioridad --}}
                        @isset($prioridades)
                            <div class="accordion-item">
                                <div class="accordion-header" data-target="prioridad-filter">
                                    <label>Prioridad</label>
                                    <i class="fas fa-chevron-down accordion-icon"></i>
                                </div>
                                <div class="accordion-content" id="prioridad-filter">
                                    <div class="filter-group">
                                        <select name="prioridad" id="filter-prioridad">
                                            <option value="">Todas</option>
                                            @foreach($prioridades as $key => $value)
                                                <option value="{{ $key }}" {{ request('prioridad') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endisset

                        {{-- Acordeón 4: Asignado A --}}
                        <div class="accordion-item">
                            <div class="accordion-header" data-target="asignado-filter">
                                <label>Asignado A</label>
                                <i class="fas fa-chevron-down accordion-icon"></i>
                            </div>
                            <div class="accordion-content" id="asignado-filter">
                                <div class="filter-group">
                                    <select name="asignado_a" id="filter-asignado">
                                        <option value="">Cualquier usuario</option>
                                        @if(isset($users) && count($users) > 0)
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ request('asignado_a') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>No hay usuarios disponibles</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="filter-actions">
                        <a href="#" id="clear-filters" class="btn-link">Limpiar</a>
                        <button type="submit" class="btn-apply">Aplicar Filtros</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- CONTENEDOR CON SCROLL HORIZONTAL --}}
    <div class="horizontal-scroll-wrapper">
        <div class="projects-grid-soft" id="workflows-grid">
            @forelse ($workflows as $workflow)
                @php
                    $totalTasks = $workflow->tasks->count();
                    $completedTasks = $workflow->completed_tasks;
                    $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : ($workflow->estado === 'completado' ? 100 : 0);
                    
                    $barColor = match($workflow->estado) {
                        'completado' => '#10b981',
                        'pausado' => '#f59e0b',
                        'borrador' => '#94a3b8',
                        default => '#4f46e5'
                    };
                @endphp

                <div class="project-item-card {{ $workflow->estado === 'archivado' ? 'is-archived' : '' }}">
                    <div class="p-card-body">
                        <div class="p-card-top">
                            <span class="p-priority-dot {{ $workflow->prioridad }}" title="Prioridad: {{ ucfirst($workflow->prioridad) }}"></span>
                            <h3 class="p-name">
                                <a href="{{ route('flujo.workflows.show', $workflow->id) }}">{{ $workflow->nombre }}</a>
                            </h3>
                            <div class="p-actions-hidden">
                                <a href="{{ route('flujo.workflows.edit', $workflow->id) }}" class="edit-link"><i class="far fa-edit"></i></a>
                            </div>
                        </div>
                        
                        <p class="p-description">{{ Str::limit($workflow->descripcion, 70) ?? 'Sin descripción disponible.' }}</p>

                        <div class="p-progress-area">
                            <div class="progress-label">
                                <span>Tareas: <strong>{{ $completedTasks }}/{{ $totalTasks }}</strong></span>
                                <strong>{{ $progress }}%</strong>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill-soft" style="width: {{ $progress }}%; background-color: {{ $barColor }};"></div>
                            </div>
                        </div>
                    </div>

                    <div class="p-card-footer">
                        <div class="p-footer-info">
                            <span class="p-status-tag st-{{ $workflow->estado }}">
                                {{ $workflow->estado }}
                            </span>
                            <div class="p-dates">
                                <i class="far fa-calendar-check"></i> {{ $workflow->fecha_fin?->format('d M') ?? 'S/F' }}
                            </div>
                        </div>
                        <div class="p-user">
                            <div class="user-avatar-mini" title="Asignado a: {{ $workflow->asignado->name ?? 'Sin asignar' }}" style="background: {{ $workflow->asignado_a ? '#10b981' : '#64748b' }}">
                                {{ substr($workflow->asignado->name ?? '?', 0, 1) }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="no-projects-soft">
                    <i class="fas fa-search"></i>
                    <p>No se encontraron resultados.</p>
                    <small>Intenta ajustar los filtros de búsqueda.</small>
                </div>
            @endforelse
        </div>
    </div>
    
    <div class="pagination-minimal" id="pagination-container">
        {{ $workflows->links() }}
    </div>
</div>

<style>
    /* VARIABLES Y RESET (TUYOS) */
    .projects-card-container {
        --p-primary: #4f46e5;
        --p-text: #0f172a;
        --p-text-light: #64748b;
        --p-border: #f1f5f9;
        background: #fff; border-radius: 16px; padding: 24px; font-family: 'Inter', sans-serif; position: relative;
    }

    /* ESTILOS PARA EL DESPLAZAMIENTO HORIZONTAL (NUEVO) */
    .horizontal-scroll-wrapper {
        width: 100%;
        overflow-x: auto;
        padding-bottom: 15px; /* Espacio para la barra */
        cursor: grab;
    }

    .horizontal-scroll-wrapper:active {
        cursor: grabbing;
    }

    /* Personalización de la barra de desplazamiento */
    .horizontal-scroll-wrapper::-webkit-scrollbar {
        height: 8px;
    }
    .horizontal-scroll-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .horizontal-scroll-wrapper::-webkit-scrollbar-thumb {
        background: #c7c7c7;
        border-radius: 10px;
        transition: background 0.3s;
    }
    .horizontal-scroll-wrapper::-webkit-scrollbar-thumb:hover {
        background: var(--p-primary);
    }

    /* Ajuste de la Grilla para que no rompa filas */
    .projects-grid-soft {
        display: flex; /* Cambiado de grid a flex */
        gap: 20px;
        flex-wrap: nowrap; /* Fuerza una sola línea */
        width: max-content; /* Se ajusta al contenido */
        min-width: 100%;
    }

    /* Ajuste de la Card para mantener tamaño constante en el scroll */
    .project-item-card {
        width: 320px; /* Ancho fijo para las tarjetas */
        flex-shrink: 0; /* Evita que se encojan */
        border: 1px solid var(--p-border); border-radius: 14px;
        display: flex; flex-direction: column; transition: 0.3s ease;
        background: #fff; position: relative;
    }

    /* TUS ESTILOS RESTANTES */
    .card-header-minimal { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .card-title { font-size: 1.2rem; font-weight: 800; color: var(--p-text); margin: 0; display: flex; align-items: center; gap: 10px; }
    .card-title i { color: var(--p-primary); font-size: 1rem; }
    .subtitle { font-size: 0.8rem; color: var(--p-text-light); margin: 2px 0 0 0; }
    .btn-new-minimal { background: #f8fafc; color: var(--p-text); border: 1px solid var(--p-border); padding: 8px 14px; border-radius: 10px; font-weight: 600; font-size: 0.8rem; text-decoration: none; transition: 0.2s; }
    .btn-new-minimal:hover { background: var(--p-primary); color: #fff; border-color: var(--p-primary); }
    .card-toolbar-soft { display: flex; gap: 12px; margin-bottom: 24px; }
    .search-box-soft { flex: 1; position: relative; }
    .search-box-soft i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--p-text-light); font-size: 0.85rem; }
    .search-box-soft input { width: 100%; padding: 10px 10px 10px 36px; border: 1px solid var(--p-border); border-radius: 12px; background: #f8fafc; font-size: 0.85rem; outline: none; transition: 0.2s; }
    .search-box-soft input:focus { border-color: var(--p-primary); background: #fff; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.05); }
    .project-item-card:hover { transform: translateY(-5px); border-color: #e2e8f0; box-shadow: 0 12px 24px -10px rgba(0,0,0,0.06); }
    .p-card-body { padding: 20px; flex: 1; }
    .p-card-top { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
    .p-name { font-size: 0.95rem; font-weight: 700; margin: 0; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .p-name a { color: var(--p-text); text-decoration: none; }
    .p-priority-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .p-priority-dot.baja { background: #94a3b8; }
    .p-priority-dot.media { background: #3b82f6; }
    .p-priority-dot.alta { background: #f59e0b; }
    .p-priority-dot.crítica { background: #ef4444; box-shadow: 0 0 6px rgba(239, 68, 68, 0.4); }
    .p-actions-hidden { opacity: 0; transition: 0.2s; }
    .project-item-card:hover .p-actions-hidden { opacity: 1; }
    .edit-link { color: var(--p-text-light); font-size: 0.8rem; }
    .p-description { font-size: 0.8rem; color: var(--p-text-light); line-height: 1.5; margin: 0 0 20px 0; min-height: 36px; }
    .p-progress-area { margin-top: auto; }
    .progress-label { display: flex; justify-content: space-between; font-size: 0.7rem; margin-bottom: 6px; color: var(--p-text-light); }
    .progress-track { height: 6px; background: #f1f5f9; border-radius: 10px; overflow: hidden; }
    .progress-fill-soft { height: 100%; border-radius: 10px; transition: 0.8s cubic-bezier(0.4, 0, 0.2, 1); }
    .p-card-footer { padding: 12px 20px; border-top: 1px solid var(--p-border); display: flex; justify-content: space-between; align-items: center; background: #fafafa; border-radius: 0 0 14px 14px; }
    .p-status-tag { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; padding: 3px 8px; border-radius: 6px; letter-spacing: 0.02em; }
    .st-activo { background: #dcfce7; color: #15803d; }
    .st-borrador { background: #f1f5f9; color: #475569; }
    .st-pausado { background: #fef3c7; color: #b45309; }
    .st-completado { background: #e0e7ff; color: #4338ca; }
    .st-archivado { background: #ebebeb; color: #707070; }
    .p-dates { font-size: 0.7rem; color: var(--p-text-light); display: flex; align-items: center; gap: 5px; margin-left: 10px; }
    .user-avatar-mini { width: 24px; height: 24px; background: var(--p-primary); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 700; }
    .filter-dropdown { position: relative; }
    .btn-filter-trigger { background: #fff; border: 1px solid var(--p-border); padding: 9px 15px; border-radius: 12px; font-size: 0.85rem; color: var(--p-text-light); cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 500; }
    .filter-popover { position: absolute; top: 120%; right: 0; width: 320px; background: white; border: 1px solid var(--p-border); border-radius: 14px; box-shadow: 0 15px 30px rgba(0,0,0,0.1); padding: 16px; z-index: 1000; display: none; max-height: 80vh; overflow-y: auto; }
    .filter-popover.active { display: block; animation: slideDown 0.2s ease; }
    .filter-group { margin-bottom: 12px; }
    .filter-group label { display: block; font-size: 0.7rem; font-weight: 700; color: var(--p-text-light); text-transform: uppercase; margin-bottom: 5px; }
    .filter-group select, .filter-group input { width: 100%; padding: 8px; border-radius: 8px; border: 1px solid var(--p-border); font-size: 0.8rem; outline: none; }
    .filter-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 15px; border-top: 1px solid var(--p-border); padding-top: 12px; position: sticky; bottom: 0; background: white; }
    .btn-apply { background: var(--p-primary); color: white; border: none; padding: 7px 14px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; cursor: pointer; }
    .btn-link { font-size: 0.75rem; color: var(--p-text-light); text-decoration: none; }
    .loading-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255, 255, 255, 0.8); display: flex; justify-content: center; align-items: center; z-index: 1000; border-radius: 16px; }
    .loading-spinner { width: 40px; height: 40px; border: 4px solid rgba(79, 70, 229, 0.1); border-radius: 50%; border-top-color: var(--p-primary); animation: spin 1s ease-in-out infinite; }
    
    /* ACORDEÓN */
    .accordion-container { margin-bottom: 15px; }
    .accordion-item { border: 1px solid var(--p-border); border-radius: 8px; margin-bottom: 8px; overflow: hidden; }
    .accordion-header { display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; background: #f8fafc; cursor: pointer; transition: background-color 0.2s; }
    .accordion-header:hover { background: #f1f5f9; }
    .accordion-header label { margin: 0; font-size: 0.8rem; font-weight: 600; color: var(--p-text); cursor: pointer; }
    .accordion-icon { transition: transform 0.2s; color: var(--p-text-light); font-size: 0.7rem; }
    .accordion-item.active .accordion-icon { transform: rotate(180deg); }
    .accordion-content { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
    .accordion-item.active .accordion-content { max-height: 200px; }
    .accordion-content .filter-group { padding: 10px 12px; margin-bottom: 0; }
    
    @keyframes spin { to { transform: rotate(360deg); } }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

    /* MEDIA QUERIES PARA CELULAR */
    @media (max-width: 768px) {
        .projects-card-container { padding: 15px; border-radius: 0; }
        .card-header-minimal { flex-direction: column; align-items: flex-start; gap: 10px; }
        .header-actions { width: 100%; }
        .btn-new-minimal { width: 100%; justify-content: center; }
        .card-toolbar-soft { flex-direction: column; gap: 10px; }
        .search-box-soft { width: 100%; }
        .filter-dropdown { width: 100%; }
        .btn-filter-trigger { width: 100%; justify-content: center; }
        .filter-popover { width: calc(100vw - 30px); right: 0; left: 0; position: fixed; top: auto; bottom: 0; border-radius: 16px 16px 0 0; }
        
        /* Desactivar scroll horizontal forzado y pasar a vertical o scroll suave */
        .projects-grid-soft { flex-wrap: wrap; width: 100%; justify-content: center; }
        .project-item-card { width: 100%; max-width: 400px; }
        .horizontal-scroll-wrapper { overflow-x: hidden; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const trigger = document.getElementById('filterTrigger');
        const popover = document.getElementById('filterPopover');
        const searchInput = document.getElementById('search-input');
        const filtersForm = document.getElementById('filters-form');
        const clearFiltersBtn = document.getElementById('clear-filters');
        const workflowsContainer = document.getElementById('workflows-container');
        const workflowsGrid = document.getElementById('workflows-grid');
        const paginationContainer = document.getElementById('pagination-container');

        // LOGICA DE DRAG-TO-SCROLL (NUEVO)
        const scrollContainer = document.querySelector('.horizontal-scroll-wrapper');
        let isDown = false;
        let startX;
        let scrollLeft;

        scrollContainer.addEventListener('mousedown', (e) => {
            isDown = true;
            startX = e.pageX - scrollContainer.offsetLeft;
            scrollLeft = scrollContainer.scrollLeft;
        });
        scrollContainer.addEventListener('mouseleave', () => { isDown = false; });
        scrollContainer.addEventListener('mouseup', () => { isDown = false; });
        scrollContainer.addEventListener('mousemove', (e) => {
            if(!isDown) return;
            e.preventDefault();
            const x = e.pageX - scrollContainer.offsetLeft;
            const walk = (x - startX) * 2; 
            scrollContainer.scrollLeft = scrollLeft - walk;
        });

        // FUNCIONES AJAX
        function showLoading() {
            const loadingOverlay = document.createElement('div');
            loadingOverlay.className = 'loading-overlay';
            loadingOverlay.innerHTML = '<div class="loading-spinner"></div>';
            workflowsContainer.style.position = 'relative';
            workflowsContainer.appendChild(loadingOverlay);
        }

        function hideLoading() {
            const loadingOverlay = workflowsContainer.querySelector('.loading-overlay');
            if (loadingOverlay) loadingOverlay.remove();
        }

        function updateView(html) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            const newGrid = tempDiv.querySelector('#workflows-grid');
            if (newGrid) workflowsGrid.innerHTML = newGrid.innerHTML;
            
            const newPagination = tempDiv.querySelector('#pagination-container');
            if (newPagination) paginationContainer.innerHTML = newPagination.innerHTML;
            
            const newSubtitle = tempDiv.querySelector('.subtitle');
            if (newSubtitle) document.querySelector('.subtitle').textContent = newSubtitle.textContent;
            
            const newFilterPopover = tempDiv.querySelector('#filterPopover');
            if (newFilterPopover) {
                popover.innerHTML = newFilterPopover.innerHTML;
                const newFiltersForm = document.getElementById('filters-form');
                if (newFiltersForm) {
                    newFiltersForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const params = new URLSearchParams();
                        const formData = new FormData(newFiltersForm);
                        for (let [key, value] of formData.entries()) {
                            if (value !== "") {
                                params.append(key, value);
                            }
                        }
                        fetchWorkflows(params.toString());
                        popover.classList.remove('active');
                    });
                }
                initializeAccordions();
            }
            hideLoading();
        }

        function fetchWorkflows(params) {
            showLoading();
            fetch('{{ route("flujo.workflows.index") }}?' + params, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.html) updateView(data.html);
            })
            .catch(error => {
                console.error('Error:', error);
                hideLoading();
            });
        }

        function initializeAccordions() {
            const accordionHeaders = document.querySelectorAll('.accordion-header');
            accordionHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const accordionItem = this.parentElement;
                    accordionItem.classList.toggle('active');
                    document.querySelectorAll('.accordion-item').forEach(item => {
                        if (item !== accordionItem) {
                            item.classList.remove('active');
                        }
                    });
                });
            });
        }

        initializeAccordions();

        if(trigger) {
            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                popover.classList.toggle('active');
            });
        }

        document.addEventListener('click', (e) => {
            if (popover && !popover.contains(e.target) && !trigger.contains(e.target)) {
                popover.classList.remove('active');
            }
        });

        let searchTimeout;
        if(searchInput) {
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                const searchTerm = e.target.value;
                document.getElementById('filter-search').value = searchTerm;
                searchTimeout = setTimeout(() => {
                    const formData = new FormData(filtersForm);
                    fetchWorkflows(new URLSearchParams(formData).toString());
                }, 500);
            });
        }

        if(filtersForm) {
            filtersForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const params = new URLSearchParams();
                const formData = new FormData(filtersForm);
                for (let [key, value] of formData.entries()) {
                    if (value !== "") {
                        params.append(key, value);
                    }
                }
                fetchWorkflows(params.toString());
                popover.classList.remove('active');
            });
        }

        if(clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', (e) => {
                e.preventDefault();
                filtersForm.reset();
                searchInput.value = '';
                document.getElementById('filter-search').value = '';
                fetchWorkflows('');
                popover.classList.remove('active');
            });
        }

        document.addEventListener('click', function(e) {
            if (e.target.closest('.pagination a')) {
                e.preventDefault();
                const url = e.target.closest('.pagination a').getAttribute('href');
                const params = url.split('?')[1];
                fetchWorkflows(params);
            }
        });
    });
</script>