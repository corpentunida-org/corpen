{{-- resources/views/flujo/componentes/workflows-card.blade.php --}}

<div class="projects-card-container" id="workflows-container">
    <header class="card-header-minimal">
        <div class="header-info">
            <h2 class="card-title"><i class="fas fa-project-diagram"></i> Proyectos</h2>
            <p class="subtitle">{{ $workflows->total() }} flujos en sistema</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('flujo.workflows.create') }}" class="btn-new-minimal">
                <i class="fas fa-plus"></i> <span class="d-none-mobile">Nuevo Proyecto</span>
            </a>
        </div>
    </header>
    
    <div class="card-toolbar-soft">
        <form id="search-form" class="search-box-soft">
            <div class="search-inner-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" id="search-input" placeholder="Búsqueda rápida..." value="{{ request('search') }}">
            </div>
        </form>

        <div class="filter-dropdown">
            <button type="button" class="btn-filter-trigger" id="filterTrigger">
                <i class="fas fa-sliders-h"></i> <span class="d-none-mobile">Filtros Avanzados</span>
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
                    $completedTasks = $workflow->tasks->where('estado', 'completada')->count();
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
                            <div class="user-avatar-mini" title="Asignado a: {{ $workflow->asignado->name ?? 'Sin asignar' }}" style="background: {{ $workflow->asignado_a ? '#4f46e5' : '#64748b' }}">
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
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;800&display=swap');

    .projects-card-container {
        --p-primary: #4f46e5;
        --p-text: #0f172a;
        --p-text-light: #64748b;
        --p-border: #e2e8f0;
        background: #fff; border-radius: 20px; padding: 28px; font-family: 'Inter', sans-serif; position: relative;
        border: 1px solid var(--p-border);
    }

    /* Header */
    .card-header-minimal { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; }
    .card-title { font-family: 'Outfit'; font-size: 1.4rem; font-weight: 800; color: var(--p-text); margin: 0; display: flex; align-items: center; gap: 12px; }
    .card-title i { color: var(--p-primary); }
    .subtitle { font-size: 0.85rem; color: var(--p-text-light); margin-top: 2px; }
    .btn-new-minimal { background: #f8fafc; color: var(--p-text); border: 1px solid var(--p-border); padding: 10px 16px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; text-decoration: none; transition: 0.2s; display: flex; align-items: center; gap: 8px; }
    .btn-new-minimal:hover { background: var(--p-primary); color: #fff; border-color: var(--p-primary); }

    /* Toolbar Simétrica */
    .card-toolbar-soft { display: flex; gap: 12px; margin-bottom: 30px; height: 48px; align-items: stretch; }
    .search-box-soft { flex: 1; position: relative; display: flex; align-items: center; }
    .search-inner-wrapper { position: relative; width: 100%; height: 100%; display: flex; align-items: center; }
    .search-box-soft i { position: absolute; left: 16px; color: var(--p-text-light); font-size: 0.9rem; z-index: 2; }
    .search-box-soft input { width: 100%; height: 100%; padding: 0 10px 0 45px; border: 1px solid var(--p-border); border-radius: 14px; background: #f8fafc; font-size: 0.9rem; outline: none; transition: 0.2s; font-weight: 500; }
    .search-box-soft input:focus { border-color: var(--p-primary); background: #fff; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.08); }

    /* Filtros trigger */
    .btn-filter-trigger { height: 100%; background: #fff; border: 1px solid var(--p-border); padding: 0 20px; border-radius: 14px; font-size: 0.85rem; color: var(--p-text); cursor: pointer; display: flex; align-items: center; gap: 10px; font-weight: 700; transition: 0.2s; }
    .btn-filter-trigger:hover { background: #f8fafc; border-color: var(--p-primary); }

    /* Popover */
    .filter-popover { position: absolute; top: calc(100% + 10px); right: 0; width: 320px; background: white; border: 1px solid var(--p-border); border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.12); padding: 20px; z-index: 1000; display: none; }
    .filter-popover.active { display: block; animation: slideDown 0.3s ease; }

    /* Scroll Horizontal */
    .horizontal-scroll-wrapper { width: 100%; overflow-x: auto; padding: 5px 0 20px 0; cursor: grab; }
    .horizontal-scroll-wrapper::-webkit-scrollbar { height: 6px; }
    .horizontal-scroll-wrapper::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .horizontal-scroll-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    
    .projects-grid-soft { display: flex; gap: 20px; flex-wrap: nowrap; width: max-content; min-width: 100%; }

    /* Card Item */
    .project-item-card { width: 320px; flex-shrink: 0; border: 1px solid var(--p-border); border-radius: 18px; background: #fff; transition: 0.3s ease; display: flex; flex-direction: column; overflow: hidden; }
    .project-item-card:hover { transform: translateY(-5px); border-color: var(--p-primary); box-shadow: 0 12px 25px rgba(0,0,0,0.05); }

    .p-card-body { padding: 24px; flex: 1; display: flex; flex-direction: column; }
    .p-card-top { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
    .p-name { font-size: 1.05rem; font-weight: 700; margin: 0; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .p-name a { color: var(--p-text); text-decoration: none; }
    .p-priority-dot { width: 10px; height: 10px; border-radius: 50%; }
    .p-priority-dot.baja { background: #94a3b8; }
    .p-priority-dot.media { background: #3b82f6; }
    .p-priority-dot.alta { background: #f59e0b; }
    .p-priority-dot.crítica { background: #ef4444; box-shadow: 0 0 8px rgba(239, 68, 68, 0.4); }

    .p-description { font-size: 0.85rem; color: var(--p-text-light); line-height: 1.5; margin-bottom: 20px; min-height: 42px; }

    /* Progress */
    .progress-label { display: flex; justify-content: space-between; font-size: 0.75rem; margin-bottom: 8px; color: var(--p-text-light); font-weight: 600; }
    .progress-track { height: 7px; background: #f1f5f9; border-radius: 10px; overflow: hidden; }
    .progress-fill-soft { height: 100%; border-radius: 10px; transition: 0.8s ease; }

    /* Footer Card */
    .p-card-footer { padding: 15px 24px; border-top: 1px solid var(--p-border); display: flex; justify-content: space-between; align-items: center; background: #fafbfc; }
    .p-status-tag { font-size: 10px; font-weight: 800; text-transform: uppercase; padding: 4px 10px; border-radius: 8px; }
    .st-activo { background: #dcfce7; color: #15803d; }
    .st-pausado { background: #fef3c7; color: #b45309; }
    .st-completado { background: #e0e7ff; color: #4338ca; }
    .p-dates { font-size: 11px; font-weight: 600; color: var(--p-text-light); }
    .user-avatar-mini { width: 28px; height: 28px; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800; }

    /* Acordeón */
    .accordion-header { padding: 12px; background: #f8fafc; cursor: pointer; display: flex; justify-content: space-between; align-items: center; border-radius: 8px; }
    .accordion-item.active .accordion-header { background: #f1f5f9; }
    .accordion-content { max-height: 0; overflow: hidden; transition: 0.3s ease; }
    .accordion-item.active .accordion-content { max-height: 200px; padding: 10px; }

    /* MOBILE UX */
    @media (max-width: 768px) {
        .projects-card-container { padding: 20px 15px; border-radius: 0; }
        .d-none-mobile { display: none !important; }
        .card-toolbar-soft { height: auto; flex-direction: column; }
        .btn-filter-trigger { height: 48px; width: 100%; justify-content: center; }
        .horizontal-scroll-wrapper { overflow-x: visible; cursor: default; }
        .projects-grid-soft { flex-direction: column; width: 100%; gap: 16px; }
        .project-item-card { width: 100%; }
        .filter-popover { position: fixed; top: auto; bottom: 0; left: 0; right: 0; width: 100%; border-radius: 20px 20px 0 0; }
    }

    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
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

        // DRAG-TO-SCROLL
        const scrollContainer = document.querySelector('.horizontal-scroll-wrapper');
        let isDown = false; let startX; let scrollLeft;

        if (window.innerWidth > 768) {
            scrollContainer.addEventListener('mousedown', (e) => {
                isDown = true; scrollContainer.classList.add('active');
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
        }

        function initializeAccordions() {
            document.querySelectorAll('.accordion-header').forEach(header => {
                header.replaceWith(header.cloneNode(true));
            });
            document.querySelectorAll('.accordion-header').forEach(header => {
                header.addEventListener('click', function() {
                    const item = this.parentElement;
                    item.classList.toggle('active');
                    document.querySelectorAll('.accordion-item').forEach(other => {
                        if (other !== item) other.classList.remove('active');
                    });
                });
            });
        }

        function updateView(html) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const newGrid = tempDiv.querySelector('#workflows-grid');
            if (newGrid) workflowsGrid.innerHTML = newGrid.innerHTML;
            const newPagination = tempDiv.querySelector('#pagination-container');
            if (newPagination) paginationContainer.innerHTML = newPagination.innerHTML;
            initializeAccordions();
            const pop = document.getElementById('filterPopover');
            if (pop) {
                const newPop = tempDiv.querySelector('#filterPopover');
                if (newPop) pop.innerHTML = newPop.innerHTML;
            }
        }

        function fetchWorkflows(params) {
            workflowsContainer.style.opacity = '0.5';
            fetch('{{ route("flujo.workflows.index") }}?' + params, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.html) updateView(data.html);
                workflowsContainer.style.opacity = '1';
            })
            .catch(() => { workflowsContainer.style.opacity = '1'; });
        }

        initializeAccordions();

        trigger?.addEventListener('click', (e) => {
            e.stopPropagation();
            popover.classList.toggle('active');
        });

        document.addEventListener('click', (e) => {
            if (popover && !popover.contains(e.target) && !trigger.contains(e.target)) {
                popover.classList.remove('active');
            }
        });

        let searchTimeout;
        searchInput?.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const searchTerm = e.target.value;
            document.getElementById('filter-search').value = searchTerm;
            searchTimeout = setTimeout(() => {
                const formData = new FormData(filtersForm);
                fetchWorkflows(new URLSearchParams(formData).toString());
            }, 500);
        });

        filtersForm?.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(filtersForm);
            fetchWorkflows(new URLSearchParams(formData).toString());
            popover.classList.remove('active');
        });

        clearFiltersBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            filtersForm.reset();
            searchInput.value = '';
            document.getElementById('filter-search').value = '';
            fetchWorkflows('');
            popover.classList.remove('active');
        });
    });
</script>