<x-base-layout>
    <div class="app-container" id="workflows-container">
        {{-- Header Ejecutivo --}}
        <header class="main-header">
            <div class="header-info">
                <div class="header-pretitle">Operational Control</div>
                <h1 class="text-title">Workflows</h1>
                <p class="subtitle">Supervisión estratégica de flujos y procesos</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('flujo.tablero') }}" class="btn-neo-ghost d-none-mobile">
                    <i class="fas fa-th-large"></i>
                    <span>Tablero</span>
                </a>
                <a href="{{ route('flujo.workflows.create') }}" class="btn-neo-primary">
                    <i class="fas fa-plus"></i> <span class="d-none-mobile">Crear Proyecto</span>
                </a>
            </div>
        </header>

        {{-- Dashboard de Estados --}}
        @php
            $counts = App\Models\Flujo\Workflow::selectRaw('estado, count(*) as total')->groupBy('estado')->pluck('total', 'estado');
            $total = $counts->sum();
        @endphp

        <div class="metrics-grid-wrapper">
            <div class="metrics-grid">
                <div class="metric-pill total filter-pill {{ !request('estado') ? 'active' : '' }}" data-status="">
                    <span class="m-label">Todos</span>
                    <span class="m-value">{{ $total }}</span>
                </div>
                <div class="metric-pill draft filter-pill {{ request('estado') == 'borrador' ? 'active' : '' }}" data-status="borrador">
                    <span class="m-dot dot-borrador"></span>
                    <span class="m-label">Borrador</span>
                    <span class="m-value">{{ $counts['borrador'] ?? 0 }}</span>
                </div>
                <div class="metric-pill active-st filter-pill {{ request('estado') == 'activo' ? 'active' : '' }}" data-status="activo">
                    <span class="m-dot dot-activo"></span>
                    <span class="m-label">Activos</span>
                    <span class="m-value">{{ $counts['activo'] ?? 0 }}</span>
                </div>
                <div class="metric-pill paused filter-pill {{ request('estado') == 'pausado' ? 'active' : '' }}" data-status="pausado">
                    <span class="m-dot dot-pausado"></span>
                    <span class="m-label">Pausas</span>
                    <span class="m-value">{{ $counts['pausado'] ?? 0 }}</span>
                </div>
                <div class="metric-pill done filter-pill {{ request('estado') == 'completado' ? 'active' : '' }}" data-status="completado">
                    <span class="m-dot dot-completado"></span>
                    <span class="m-label">Hechos</span>
                    <span class="m-value">{{ $counts['completado'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        {{-- Toolbar con Buscador Corregido (Alineación Perfecta) --}}
        <div class="toolbar-area">
            <div class="search-and-filter-wrapper">
                <form action="{{ route('flujo.workflows.index') }}" method="GET" class="neo-search-form" id="main-search-form">
                    <input type="hidden" name="estado" id="hidden-status" value="{{ request('estado') }}">
                    <input type="hidden" name="prioridad" id="hidden-prio" value="{{ request('prioridad') }}">
                    <input type="hidden" name="asignado_a" id="hidden-user" value="{{ request('asignado_a') }}">
                    
                    <div class="search-inner">
                        <div class="search-input-group">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="search" id="live-search" placeholder="Buscar flujos..." value="{{ request('search') }}" autocomplete="off" />
                            @if(request()->anyFilled(['search', 'estado', 'prioridad', 'asignado_a']))
                                <a href="{{ route('flujo.workflows.index') }}" class="btn-reset-input" title="Limpiar todo">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            @endif
                        </div>
                        
                        <div class="v-divider"></div>
                        
                        <button type="button" class="btn-filter-toggle" id="advance-filter-trigger">
                            <i class="fas fa-sliders-h"></i>
                            <span class="d-none-mobile">Filtros</span>
                        </button>
                    </div>
                </form>

                {{-- Popover de Filtros --}}
                <div class="advanced-filter-popover" id="advanced-popover">
                    <div class="popover-header">Filtrado Avanzado</div>
                    <div class="accordion-container">
                        <div class="acc-item active">
                            <div class="acc-header">
                                <span>Prioridad</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="acc-content">
                                <div class="filter-options-grid">
                                    <label class="custom-radio">
                                        <input type="radio" name="prio_opt" value="" {{ !request('prioridad') ? 'checked' : '' }}>
                                        <span class="radio-box">Todas</span>
                                    </label>
                                    <label class="custom-radio">
                                        <input type="radio" name="prio_opt" value="baja" {{ request('prioridad') == 'baja' ? 'checked' : '' }}>
                                        <span class="radio-box">Baja</span>
                                    </label>
                                    <label class="custom-radio">
                                        <input type="radio" name="prio_opt" value="media" {{ request('prioridad') == 'media' ? 'checked' : '' }}>
                                        <span class="radio-box">Media</span>
                                    </label>
                                    <label class="custom-radio">
                                        <input type="radio" name="prio_opt" value="alta" {{ request('prioridad') == 'alta' ? 'checked' : '' }}>
                                        <span class="radio-box">Alta</span>
                                    </label>
                                    <label class="custom-radio">
                                        <input type="radio" name="prio_opt" value="crítica" {{ request('prioridad') == 'crítica' ? 'checked' : '' }}>
                                        <span class="radio-box danger">Crítica</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="acc-item">
                            <div class="acc-header">
                                <span>Responsable</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="acc-content">
                                <select class="filter-select-neo" id="user-opt">
                                    <option value="">Cualquier líder</option>
                                    @foreach(App\Models\User::all() as $user)
                                        <option value="{{ $user->id }}" {{ request('asignado_a') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="popover-footer">
                        <button type="button" class="btn-apply-filters" id="apply-advanced">Aplicar Filtros</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Listado de Proyectos --}}
        <div class="list-wrapper">
            <div class="list-legend d-none-mobile">
                <span class="leg-id">Ref.</span>
                <span class="leg-project">Proyecto</span>
                <span class="leg-status">Estado</span>
                <span class="leg-priority">Prioridad</span>
                <span class="leg-lead">Líder</span>
                <span class="leg-progress">Salud</span>
                <span class="leg-actions text-right">Acción</span>
            </div>

            @forelse($workflows as $workflow)
                <div class="project-card">
                    <div class="p-col-id d-none-mobile">
                        <span class="id-tag">#{{ $workflow->id }}</span>
                    </div>

                    <div class="p-col-main">
                        <div class="name-block">
                            <a href="{{ route('flujo.workflows.show', $workflow) }}" class="project-name-link">{{ $workflow->nombre }}</a>
                        </div>
                        <div class="date-sub">
                            {{ $workflow->fecha_inicio?->format('d M') ?? '--' }} — {{ $workflow->fecha_fin?->format('d M, Y') ?? '--' }}
                        </div>
                    </div>

                    <div class="p-col-status">
                        <div class="status-pill-neo status-{{ $workflow->estado }}">
                            {{ ucfirst($workflow->estado) }}
                        </div>
                    </div>

                    <div class="p-col-prio">
                        <div class="prio-tag-neo prio-{{ strtolower($workflow->prioridad) }}">
                            <span class="prio-indicator"></span> {{ ucfirst($workflow->prioridad) }}
                        </div>
                    </div>

                    <div class="p-col-lead">
                        <div class="lead-avatar-group">
                            <div class="lead-avatar">
                                {{ substr($workflow->asignado?->name ?? 'S', 0, 1) }}
                            </div>
                            <span class="d-only-mobile lead-name">{{ $workflow->asignado?->name ?? 'Sin asignar' }}</span>
                        </div>
                    </div>

                    <div class="p-col-progress">
                        @php
                            $progColor = match($workflow->estado) {
                                'completado' => '#6366f1',
                                'activo' => '#10b981',
                                'pausado' => '#f59e0b',
                                default => '#94a3b8'
                            };
                            $progWidth = $workflow->estado == 'completado' ? '100%' : ($workflow->estado == 'activo' ? '65%' : '20%');
                        @endphp
                        <div class="progress-container-neo">
                            <div class="progress-track">
                                <div class="progress-fill" style="width: {{ $progWidth }}; background: {{ $progColor }};"></div>
                            </div>
                            <span class="progress-val">{{ $progWidth }}</span>
                        </div>
                    </div>

                    <div class="p-col-actions">
                        <div class="action-set">
                            <a href="{{ route('flujo.workflows.show', $workflow) }}" class="act-btn">Ver <i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="neo-empty-state">
                    <div class="empty-art"><i class="fas fa-search"></i></div>
                    <h3>No hay resultados</h3>
                </div>
            @endforelse
        </div>

        <div class="pagination-container">
            {{ $workflows->links() }}
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;800&display=swap');

        :root {
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --bg-body: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --radius-md: 16px;
        }

        body { background-color: var(--bg-body); color: var(--text-main); font-family: 'Inter', sans-serif; margin: 0; }
        .app-container { max-width: 1250px; margin: 20px auto; padding: 0 20px; }

        /* --- Header --- */
        .main-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .text-title { font-family: 'Outfit'; font-size: 2.2rem; font-weight: 800; letter-spacing: -0.02em; margin: 0; }
        .header-pretitle { font-size: 10px; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.1em; }
        .subtitle { color: var(--text-muted); font-size: 0.9rem; margin-top: 2px; }

        /* --- Metrics Scrollable --- */
        .metrics-grid-wrapper { margin: 0 -20px 30px; padding: 0 20px; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .metrics-grid-wrapper::-webkit-scrollbar { display: none; }
        .metrics-grid { display: flex; gap: 12px; min-width: max-content; padding-bottom: 5px; }
        .metric-pill { 
            background: white; padding: 12px 18px; border-radius: var(--radius-md); border: 1px solid var(--border-color);
            cursor: pointer; transition: 0.2s; display: flex; flex-direction: column; gap: 4px; min-width: 120px;
        }
        .metric-pill.active { background: var(--primary); border-color: var(--primary); color: white; }
        .metric-pill.active .m-label, .metric-pill.active .m-value { color: white; }
        .m-label { font-size: 10px; font-weight: 600; text-transform: uppercase; color: var(--text-muted); }
        .m-value { font-family: 'Outfit'; font-size: 1.3rem; font-weight: 700; }
        .m-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
        .dot-activo { background: #10b981; } .dot-pausado { background: #f59e0b; } .dot-borrador { background: #94a3b8; } .dot-completado { background: #6366f1; }

        /* --- Toolbar & Search FIXED (Corrección de Simetría) --- */
        .search-and-filter-wrapper { max-width: 550px; margin-bottom: 25px; position: relative; }
        
        .search-inner { 
            display: flex; 
            align-items: stretch; /* Asegura que el contenido ocupe el mismo alto */
            background: white; 
            border-radius: 14px; 
            border: 1px solid var(--border-color); 
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            overflow: hidden;
            height: 48px; /* Alto fijo para simetría total */
        }

        .search-input-group {
            display: flex;
            align-items: center;
            flex: 1;
            padding: 0 15px;
        }

        .search-input-group input { 
            border: none; 
            padding: 10px; 
            flex: 1; 
            outline: none; 
            font-size: 14px; 
            background: transparent;
        }

        .search-icon { color: var(--text-muted); font-size: 14px; opacity: 0.6; }
        
        .btn-reset-input { 
            color: #cbd5e1; 
            font-size: 18px; 
            display: flex; 
            align-items: center; 
            text-decoration: none; 
            padding: 0 5px; 
            transition: 0.2s; 
        }
        .btn-reset-input:hover { color: #ef4444; }

        .v-divider { 
            width: 1px; 
            background: #f1f5f9; 
            margin: 10px 0; /* Margen superior e inferior para que no toque los bordes */
        }

        .btn-filter-toggle { 
            background: transparent; 
            border: none; 
            padding: 0 20px; 
            font-weight: 700; 
            color: var(--text-main); 
            font-size: 13px; 
            cursor: pointer; 
            display: flex; 
            align-items: center; 
            gap: 8px; 
            transition: 0.2s; 
        }
        .btn-filter-toggle:hover { background: #f8fafc; color: var(--primary); }

        /* --- Popover --- */
        .advanced-filter-popover {
            position: absolute; top: calc(100% + 10px); left: 0; width: 330px; background: white; 
            border-radius: var(--radius-md); box-shadow: 0 15px 45px rgba(0,0,0,0.1);
            z-index: 1000; border: 1px solid var(--border-color); display: none; overflow: hidden;
        }
        .advanced-filter-popover.show { display: block; animation: slideUp 0.3s ease-out; }
        .popover-header { padding: 15px 20px; font-weight: 800; font-size: 13px; background: #fafafa; border-bottom: 1px solid #eee; }
        .acc-header { padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; font-size: 13px; font-weight: 600; }
        .acc-content { padding: 0 20px 15px; display: none; }
        .acc-item.active .acc-content { display: block; }
        .acc-item.active .acc-header i { transform: rotate(180deg); color: var(--primary); }
        
        .filter-options-grid { display: flex; flex-wrap: wrap; gap: 6px; }
        .radio-box { display: inline-block; padding: 7px 12px; background: #f1f5f9; border-radius: 8px; font-size: 11px; font-weight: 600; cursor: pointer; border: 1px solid transparent; }
        .custom-radio input { display: none; }
        .custom-radio input:checked + .radio-box { background: var(--primary-light); border-color: var(--primary); color: var(--primary); }
        .filter-select-neo { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; font-size: 13px; outline: none; }
        .popover-footer { padding: 15px; background: #f8fafc; }
        .btn-apply-filters { width: 100%; background: var(--text-main); color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 700; cursor: pointer; }

        /* --- Listado de Proyectos --- */
        .list-legend { display: grid; grid-template-columns: 50px 2.5fr 1fr 1fr 120px 1.2fr 80px; padding: 0 20px 12px; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
        .project-card { 
            display: grid; grid-template-columns: 50px 2.5fr 1fr 1fr 120px 1.2fr 80px; align-items: center; 
            background: white; border: 1px solid var(--border-color); border-radius: var(--radius-md); 
            padding: 18px 20px; margin-bottom: 12px; transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .project-card:hover { border-color: var(--primary); box-shadow: 0 8px 20px rgba(0,0,0,0.04); transform: translateY(-2px); }

        .project-name-link { font-weight: 700; color: var(--text-main); text-decoration: none; font-size: 15px; margin-bottom: 2px; display: block; }
        .date-sub { font-size: 11px; color: var(--text-muted); display: flex; align-items: center; gap: 5px; }
        .status-pill-neo { font-size: 10px; font-weight: 800; padding: 4px 10px; border-radius: 7px; width: fit-content; text-transform: uppercase; }
        .status-activo { background: #dcfce7; color: #15803d; }
        .status-pausado { background: #fef3c7; color: #b45309; }
        .status-completado { background: #e0e7ff; color: #4338ca; }
        .status-borrador { background: #f1f5f9; color: #475569; }

        .prio-tag-neo { font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .prio-indicator { width: 7px; height: 7px; border-radius: 50%; background: #cbd5e1; }
        .prio-crítica { color: #ef4444; } .prio-crítica .prio-indicator { background: #ef4444; }
        .prio-alta { color: #f59e0b; } .prio-alta .prio-indicator { background: #f59e0b; }

        .progress-container-neo { display: flex; align-items: center; gap: 10px; }
        .progress-track { flex: 1; height: 7px; background: #f1f5f9; border-radius: 10px; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 10px; }
        .progress-val { font-size: 11px; font-weight: 700; color: var(--text-muted); min-width: 30px; }

        .lead-avatar { width: 30px; height: 30px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: var(--primary); border: 1px solid #e2e8f0; }
        
        .act-btn { text-decoration: none; color: var(--primary); font-weight: 700; font-size: 13px; display: flex; align-items: center; gap: 5px; transition: 0.2s; }
        .act-btn:hover { transform: translateX(3px); }

        .btn-neo-primary { background: var(--text-main); color: white; padding: 12px 18px; border-radius: 12px; font-weight: 700; text-decoration: none; font-size: 14px; display: flex; align-items: center; gap: 8px; }
        .btn-neo-ghost { padding: 10px 15px; text-decoration: none; color: var(--text-muted); font-weight: 600; font-size: 14px; }

        @media (max-width: 900px) {
            .app-container { padding: 0 16px; }
            .d-none-mobile { display: none !important; }
            .project-card { grid-template-columns: 1fr 1fr; gap: 15px; padding: 18px; }
            .p-col-main { grid-column: span 2; }
            .p-col-status { grid-column: span 1; }
            .p-col-prio { grid-column: span 1; justify-self: end; }
            .p-col-lead { grid-column: span 2; display: flex; align-items: center; gap: 10px; border-top: 1px solid #f1f5f9; padding-top: 12px; }
            .p-col-progress { grid-column: span 2; }
            .p-col-actions { grid-column: span 2; margin-top: 5px; }
            .act-btn { background: var(--primary-light); padding: 12px; border-radius: 10px; justify-content: center; width: 100%; }
            .advanced-filter-popover { position: fixed; top: auto; bottom: 10px; left: 10px; right: 10px; width: auto; }
        }

        @keyframes slideUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popover = document.getElementById('advanced-popover');
            const trigger = document.getElementById('advance-filter-trigger');
            const searchForm = document.getElementById('main-search-form');

            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                popover.classList.toggle('show');
            });

            document.addEventListener('click', (e) => {
                if (!popover.contains(e.target) && e.target !== trigger) {
                    popover.classList.remove('show');
                }
            });

            document.querySelectorAll('.acc-header').forEach(header => {
                header.addEventListener('click', function() {
                    this.parentElement.classList.toggle('active');
                });
            });

            document.querySelectorAll('.filter-pill').forEach(pill => {
                pill.addEventListener('click', function() {
                    document.getElementById('hidden-status').value = this.dataset.status;
                    searchForm.submit();
                });
            });

            document.getElementById('apply-advanced').addEventListener('click', () => {
                const prio = document.querySelector('input[name="prio_opt"]:checked').value;
                document.getElementById('hidden-prio').value = prio;
                document.getElementById('hidden-user').value = document.getElementById('user-opt').value;
                searchForm.submit();
            });
        });
    </script>
</x-base-layout>