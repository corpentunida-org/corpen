{{-- resources/views/flujo/componentes/workflows-card.blade.php --}}

<div class="projects-card-container">
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
        <form action="{{ route('flujo.workflows.index') }}" method="GET" class="search-box-soft">
            <i class="fas fa-search"></i>
            <input type="text" name="search" placeholder="Buscar por nombre..." value="{{ request('search') }}">
        </form>

        @isset($estados, $prioridades)
            <div class="filter-dropdown">
                <button type="button" class="btn-filter-trigger" id="filterTrigger">
                    <i class="fas fa-sliders-h"></i> <span>Filtros</span>
                </button>
                <div class="filter-popover" id="filterPopover">
                    <form action="{{ route('flujo.workflows.index') }}" method="GET" class="filters-form">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        
                        <div class="filter-group">
                            <label>Estado</label>
                            <select name="estado">
                                <option value="">Todos</option>
                                @foreach($estados as $key => $value)
                                    <option value="{{ $key }}" {{ request('estado') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-group">
                            <label>Prioridad</label>
                            <select name="prioridad">
                                <option value="">Todas</option>
                                @foreach($prioridades as $key => $value)
                                    <option value="{{ $key }}" {{ request('prioridad') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-actions">
                            <a href="{{ route('flujo.workflows.index') }}" class="btn-link">Limpiar</a>
                            <button type="submit" class="btn-apply">Aplicar</button>
                        </div>
                    </form>
                </div>
            </div>
        @endisset
    </div>

    <div class="projects-grid-soft">
        @forelse ($workflows as $workflow)
            @php
                // LÓGICA DE PROGRESO INTEGRADA
                $totalTasks = $workflow->tasks->count();
                $completedTasks = $workflow->tasks->where('estado', 'completada')->count();
                
                // Si el estado es 'completado', forzamos 100% aunque no tenga tareas.
                if ($workflow->estado === 'completado') {
                    $progress = 100;
                } else {
                    $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                }

                // Color dinámico de la barra
                $barColor = '#4f46e5'; // Indigo (Default/Activo)
                if($workflow->estado === 'completado') $barColor = '#10b981'; // Verde
                if($workflow->estado === 'pausado') $barColor = '#f59e0b'; // Naranja
                if($workflow->estado === 'borrador') $barColor = '#94a3b8'; // Gris
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
                        <div class="user-avatar-mini" title="{{ $workflow->creator->name ?? 'Usuario' }}">
                            {{ substr($workflow->creator->name ?? '?', 0, 1) }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="no-projects-soft">
                <i class="fas fa-inbox"></i>
                <p>No se encontraron flujos de trabajo</p>
                <small>Intenta cambiar los filtros o crear uno nuevo.</small>
            </div>
        @endforelse
    </div>
    
    <div class="pagination-minimal">
        {{ $workflows->links() }}
    </div>
</div>

<style>
    /* VARIABLES Y RESET */
    .projects-card-container {
        --p-primary: #4f46e5;
        --p-text: #0f172a;
        --p-text-light: #64748b;
        --p-border: #f1f5f9;
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        font-family: 'Inter', sans-serif;
    }

    /* HEADER */
    .card-header-minimal { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .card-title { font-size: 1.2rem; font-weight: 800; color: var(--p-text); margin: 0; display: flex; align-items: center; gap: 10px; }
    .card-title i { color: var(--p-primary); font-size: 1rem; }
    .subtitle { font-size: 0.8rem; color: var(--p-text-light); margin: 2px 0 0 0; }
    
    .btn-new-minimal {
        background: #f8fafc; color: var(--p-text); border: 1px solid var(--p-border);
        padding: 8px 14px; border-radius: 10px; font-weight: 600; font-size: 0.8rem;
        text-decoration: none; transition: 0.2s;
    }
    .btn-new-minimal:hover { background: var(--p-primary); color: #fff; border-color: var(--p-primary); }

    /* TOOLBAR & SEARCH */
    .card-toolbar-soft { display: flex; gap: 12px; margin-bottom: 24px; }
    .search-box-soft { flex: 1; position: relative; }
    .search-box-soft i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--p-text-light); font-size: 0.85rem; }
    .search-box-soft input {
        width: 100%; padding: 10px 10px 10px 36px; border: 1px solid var(--p-border);
        border-radius: 12px; background: #f8fafc; font-size: 0.85rem; outline: none; transition: 0.2s;
    }
    .search-box-soft input:focus { border-color: var(--p-primary); background: #fff; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.05); }

    /* GRID */
    .projects-grid-soft { display: grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap: 20px; }

    /* CARDS */
    .project-item-card {
        border: 1px solid var(--p-border); border-radius: 14px;
        display: flex; flex-direction: column; transition: 0.3s ease;
        background: #fff; position: relative;
    }
    .project-item-card:hover {
        transform: translateY(-5px); border-color: #e2e8f0;
        box-shadow: 0 12px 24px -10px rgba(0,0,0,0.06);
    }

    .p-card-body { padding: 20px; flex: 1; }
    .p-card-top { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
    .p-name { font-size: 0.95rem; font-weight: 700; margin: 0; flex: 1; }
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

    /* PROGRESS BAR */
    .p-progress-area { margin-top: auto; }
    .progress-label { display: flex; justify-content: space-between; font-size: 0.7rem; margin-bottom: 6px; color: var(--p-text-light); }
    .progress-track { height: 6px; background: #f1f5f9; border-radius: 10px; overflow: hidden; }
    .progress-fill-soft { height: 100%; border-radius: 10px; transition: 0.8s cubic-bezier(0.4, 0, 0.2, 1); }

    /* FOOTER CARD */
    .p-card-footer {
        padding: 12px 20px; border-top: 1px solid var(--p-border);
        display: flex; justify-content: space-between; align-items: center; background: #fafafa;
        border-radius: 0 0 14px 14px;
    }
    .p-status-tag {
        font-size: 0.65rem; font-weight: 700; text-transform: uppercase;
        padding: 3px 8px; border-radius: 6px; letter-spacing: 0.02em;
    }
    .st-activo { background: #dcfce7; color: #15803d; }
    .st-borrador { background: #f1f5f9; color: #475569; }
    .st-pausado { background: #fef3c7; color: #b45309; }
    .st-completado { background: #e0e7ff; color: #4338ca; }
    .st-archivado { background: #ebebeb; color: #707070; }

    .p-dates { font-size: 0.7rem; color: var(--p-text-light); display: flex; align-items: center; gap: 5px; margin-left: 10px; }
    .user-avatar-mini {
        width: 24px; height: 24px; background: var(--p-primary); color: #fff;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 0.65rem; font-weight: 700;
    }

    /* POPOVER FILTROS */
    .filter-dropdown { position: relative; }
    .btn-filter-trigger {
        background: #fff; border: 1px solid var(--p-border); padding: 9px 15px;
        border-radius: 12px; font-size: 0.85rem; color: var(--p-text-light);
        cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 500;
    }
    .filter-popover {
        position: absolute; top: 120%; right: 0; width: 240px;
        background: white; border: 1px solid var(--p-border); border-radius: 14px;
        box-shadow: 0 15px 30px rgba(0,0,0,0.1); padding: 16px;
        z-index: 100; display: none;
    }
    .filter-popover.active { display: block; animation: slideDown 0.2s ease; }
    .filter-group { margin-bottom: 12px; }
    .filter-group label { display: block; font-size: 0.7rem; font-weight: 700; color: var(--p-text-light); text-transform: uppercase; margin-bottom: 5px; }
    .filter-group select { width: 100%; padding: 8px; border-radius: 8px; border: 1px solid var(--p-border); font-size: 0.8rem; }
    .filter-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 15px; border-top: 1px solid var(--p-border); padding-top: 12px; }
    .btn-apply { background: var(--p-primary); color: white; border: none; padding: 7px 14px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; cursor: pointer; }
    .btn-link { font-size: 0.75rem; color: var(--p-text-light); text-decoration: none; }

    /* ESTADOS ESPECIALES */
    .is-archived { opacity: 0.6; filter: grayscale(0.5); }
    .no-projects-soft { grid-column: 1/-1; text-align: center; padding: 60px; color: var(--p-text-light); }
    .no-projects-soft i { font-size: 2rem; margin-bottom: 15px; opacity: 0.3; display: block; }

    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const trigger = document.getElementById('filterTrigger');
        const popover = document.getElementById('filterPopover');

        if(trigger) {
            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                popover.classList.toggle('active');
            });
        }

        // Cerrar al hacer clic fuera
        document.addEventListener('click', (e) => {
            if (popover && !popover.contains(e.target) && !trigger.contains(e.target)) {
                popover.classList.remove('active');
            }
        });
    });
</script>