<x-base-layout>
    {{-- 1. Librería Tom Select (CSS) --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

    {{-- Cálculo de seguridad inicial con búsqueda por palabras --}}
    @php
        // Búsqueda por palabras clave
        $searchWords = [];
        if (request()->filled('search')) {
            $searchWords = array_filter(explode(' ', request('search')));
        }
        
        // Consulta base con búsqueda por palabras
        $workflowQuery = App\Models\Flujo\Workflow::with('asignado');
        
        // Aplicar búsqueda por cada palabra
        if (!empty($searchWords)) {
            $workflowQuery->where(function($query) use ($searchWords) {
                foreach ($searchWords as $word) {
                    $query->orWhere('nombre', 'LIKE', '%' . $word . '%')
                          ->orWhere('descripcion', 'LIKE', '%' . $word . '%');
                }
            });
        }
        
        // Aplicar filtros adicionales
        if (request()->filled('estado')) {
            $workflowQuery->where('estado', request('estado'));
        }
        if (request()->filled('prioridad')) {
            $workflowQuery->where('prioridad', request('prioridad'));
        }
        if (request()->filled('asignado_a')) {
            $workflowQuery->where('asignado_a', request('asignado_a'));
        }
        
        // Obtener resultados
        $workflows = $workflowQuery->orderBy('created_at', 'desc')->paginate(10);
        
        // Conteos para métricas
        $counts = App\Models\Flujo\Workflow::selectRaw('estado, count(*) as total')->groupBy('estado')->pluck('total', 'estado');
        $total = $counts->sum();
        $users = App\Models\User::orderBy('name')->get();
        
        // Datos para gráficos - Dinámicos según filtros
        $baseQuery = App\Models\Flujo\Workflow::query();
        
        // Aplicar mismos filtros a las gráficas
        if (!empty($searchWords)) {
            $baseQuery->where(function($query) use ($searchWords) {
                foreach ($searchWords as $word) {
                    $query->orWhere('nombre', 'LIKE', '%' . $word . '%')
                          ->orWhere('descripcion', 'LIKE', '%' . $word . '%');
                }
            });
        }
        if (request()->filled('estado')) {
            $baseQuery->where('estado', request('estado'));
        }
        if (request()->filled('prioridad')) {
            $baseQuery->where('prioridad', request('prioridad'));
        }
        if (request()->filled('asignado_a')) {
            $baseQuery->where('asignado_a', request('asignado_a'));
        }
        
        // Datos de cumplimiento filtrados
        $cumplimiento = [
            'a_tiempo' => (clone $baseQuery)->where('fecha_fin', '>=', now())->where('estado', '!=', 'completado')->count(),
            'atrasados' => (clone $baseQuery)->where('fecha_fin', '<', now())->where('estado', '!=', 'completado')->count(),
            'completados' => (clone $baseQuery)->where('estado', 'completado')->count(),
        ];
        
        // Datos de carga por líder filtrados
        $leadersData = (clone $baseQuery)->with('asignado')
            ->select('asignado_a', DB::raw('count(*) as total'))
            ->whereNotNull('asignado_a')
            ->groupBy('asignado_a')
            ->get();
            
        // Datos de estados filtrados
        $filteredCounts = (clone $baseQuery)->selectRaw('estado, count(*) as total')->groupBy('estado')->pluck('total', 'estado');
    @endphp

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
        <div class="metrics-grid-wrapper">
            <div class="metrics-grid">
                <div class="metric-pill total filter-pill {{ !request('estado') ? 'active' : '' }}" data-status="">
                    <span class="m-label">Todos</span>
                    <span class="m-value">{{ $total }}</span>
                </div>
                @foreach(['borrador', 'activo', 'pausado', 'completado', 'archivado'] as $est)
                <div class="metric-pill {{ $est }} filter-pill {{ request('estado') == $est ? 'active' : '' }}" data-status="{{ $est }}">
                    <span class="m-dot dot-{{ $est }}"></span>
                    <span class="m-label">{{ $est == 'completado' ? 'Hechos' : ucfirst($est) }}</span>
                    <span class="m-value">{{ $counts[$est] ?? 0 }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- SECCIÓN DE INSIGHTS CON SELECTOR DINÁMICO --}}
        <div class="insights-section">
            <div class="insights-header">
                <h2>Panel de Análisis</h2>
                <div class="chart-view-selector">
                    <label for="chart-view-select">Vista:</label>
                    <select id="chart-view-select" class="chart-selector">
                        <option value="global">Global</option>
                        <option value="leader">Por Líder</option>
                    </select>
                </div>
            </div>
            
            {{-- Gráfica de Distribución --}}
            <div class="insight-card chart-container-wrapper" data-chart="distribution">
                <div class="card-head">
                    <h3>Distribución</h3>
                    <p class="chart-subtitle">Estados de los flujos</p>
                    <div class="chart-legend-custom" id="distribution-legend"></div>
                </div>
                <div class="chart-container">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
            
            {{-- Gráfica de Cumplimiento --}}
            <div class="insight-card chart-container-wrapper" data-chart="compliance">
                <div class="card-head">
                    <h3>Cumplimiento</h3>
                    <p class="chart-subtitle">Vigencia vs Atrasos</p>
                    <div class="chart-stats" id="compliance-stats"></div>
                </div>
                <div class="chart-container">
                    <canvas id="complianceChart"></canvas>
                </div>
            </div>
            
            {{-- Gráfica de Carga de Trabajo --}}
            <div class="insight-card chart-container-wrapper" data-chart="workload">
                <div class="card-head">
                    <h3>Carga de Trabajo</h3>
                    <p class="chart-subtitle">Proyectos por Líder</p>
                    <div class="chart-stats" id="workload-stats"></div>
                </div>
                <div class="chart-container">
                    <canvas id="workloadChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Toolbar con Buscador Mejorado --}}
        <div class="toolbar-area">
            <div class="search-and-filter-wrapper">
                <form action="{{ route('flujo.workflows.index') }}" method="GET" class="neo-search-form" id="main-search-form">
                    <input type="hidden" name="estado" id="hidden-status" value="{{ request('estado') }}">
                    <input type="hidden" name="prioridad" id="hidden-prio" value="{{ request('prioridad') }}">
                    <input type="hidden" name="asignado_a" id="hidden-user" value="{{ request('asignado_a') }}">
                    
                    <div class="search-inner">
                        <div class="search-input-group">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="search" id="live-search" placeholder="Buscar por palabras clave..." value="{{ request('search') }}" autocomplete="off" />
                            @if(request()->filled('search'))
                                <button type="button" class="search-clear-btn" id="clear-search">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                        <div class="v-divider"></div>
                        <button type="button" class="btn-filter-toggle" id="advance-filter-trigger">
                            <i class="fas fa-sliders-h"></i>
                            <span class="d-none-mobile">Filtros</span>
                            <span class="filter-badge" id="active-filters-count">0</span>
                        </button>
                    </div>
                </form>

                {{-- Popover de Filtros --}}
                <div class="advanced-filter-popover" id="advanced-popover">
                    <div class="popover-header">
                        <div class="popover-title">Filtrado Avanzado</div>
                        <button type="button" class="popover-close" id="close-popover">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="accordion-container">
                        <div class="acc-item active">
                            <div class="acc-header"><span>Prioridad</span><i class="fas fa-chevron-down"></i></div>
                            <div class="acc-content">
                                <div class="filter-options-grid">
                                    <label class="custom-radio">
                                        <input type="radio" name="prio_opt" value="" {{ !request('prioridad') ? 'checked' : '' }}>
                                        <span class="radio-box">Todas</span>
                                    </label>
                                    @foreach(['baja', 'media', 'alta'] as $p)
                                    <label class="custom-radio">
                                        <input type="radio" name="prio_opt" value="{{ $p }}" {{ request('prioridad') == $p ? 'checked' : '' }}>
                                        <span class="radio-box">{{ ucfirst($p) }}</span>
                                    </label>
                                    @endforeach
                                    <label class="custom-radio">
                                        <input type="radio" name="prio_opt" value="crítica" {{ request('prioridad') == 'crítica' ? 'checked' : '' }}>
                                        <span class="radio-box danger">Crítica</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="acc-item active">
                            <div class="acc-header"><span>Responsable</span><i class="fas fa-chevron-down"></i></div>
                            <div class="acc-content" style="overflow: visible;"> {{-- Importante para que el dropdown salga --}}
                                {{-- 2. Select con placeholder para Tom Select --}}
                                <select class="filter-select-neo" id="user-opt" placeholder="Buscar líder...">
                                    <option value="">Todos los líderes</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('asignado_a') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="popover-footer">
                        <button type="button" class="btn-reset-filters" id="reset-filters">
                            <i class="fas fa-undo"></i> Restablecer
                        </button>
                        <button type="button" class="btn-apply-filters" id="apply-advanced">
                            <i class="fas fa-check"></i> Aplicar Filtros
                        </button>
                    </div>
                </div>

                {{-- Indicador de búsqueda activa --}}
                @if(request()->filled('search'))
                    <div class="search-indicator">
                        <div class="search-info">
                            <i class="fas fa-info-circle"></i>
                            <span>Mostrando resultados para: 
                                @foreach($searchWords as $word)
                                    <strong>{{ $word }}</strong>{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </span>
                        </div>
                        <a href="{{ route('flujo.workflows.index') }}" class="clear-all-filters">
                            <i class="fas fa-times"></i> Limpiar búsqueda
                        </a>
                    </div>
                @endif
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
                <span class="leg-progress">Salud Operativa</span>
                <span class="leg-actions text-right">Acción</span>
            </div>

            @forelse($workflows as $workflow)
                @php
                    $isOverdue = $workflow->fecha_fin && $workflow->fecha_fin->isPast() && $workflow->estado !== 'completado';
                    
                    // Resaltar palabras de búsqueda
                    $highlightedName = $workflow->nombre;
                    $highlightedDesc = $workflow->descripcion ?? '';
                    if (!empty($searchWords)) {
                        foreach ($searchWords as $word) {
                            $highlightedName = preg_replace('/(' . preg_quote($word, '/') . ')/i', '<mark>$1</mark>', $highlightedName);
                            $highlightedDesc = preg_replace('/(' . preg_quote($word, '/') . ')/i', '<mark>$1</mark>', $highlightedDesc);
                        }
                    }
                @endphp
                <div class="project-card {{ $isOverdue ? 'overdue-alert' : '' }}">
                    <div class="p-col-id d-none-mobile">
                        <span class="id-tag">#{{ $workflow->id }}</span>
                    </div>

                    <div class="p-col-main">
                        <div class="name-block">
                            <a href="{{ route('flujo.workflows.show', $workflow) }}" class="project-name-link">{!! $highlightedName !!}</a>
                        </div>
                        <div class="date-sub">
                             <i class="far fa-calendar-alt"></i> {{ $workflow->fecha_inicio?->format('d M') ?? '--' }} — {{ $workflow->fecha_fin?->format('d M, Y') ?? '--' }}
                        </div>
                        @if(!empty($searchWords) && !empty($workflow->descripcion))
                            <div class="search-snippet d-none-mobile">
                                {!! Str::limit($highlightedDesc, 100) !!}
                            </div>
                        @endif
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
                            <div class="lead-avatar" title="{{ $workflow->asignado?->name ?? 'Sin asignar' }}">
                                {{ substr($workflow->asignado?->name ?? 'S', 0, 1) }}
                            </div>
                            <span class="d-only-mobile lead-name">{{ $workflow->asignado?->name ?? 'Sin asignar' }}</span>
                        </div>
                    </div>

                    <div class="p-col-progress">
                        @php
                            $progColor = $isOverdue ? '#ef4444' : match($workflow->estado) {
                                'completado' => '#6366f1',
                                'activo' => '#10b981',
                                'pausado' => '#f59e0b',
                                'archivado' => '#475569',
                                default => '#94a3b8'
                            };
                            $progWidth = in_array($workflow->estado, ['completado', 'archivado']) ? '100%' : ($workflow->estado == 'activo' ? '65%' : '20%');
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
                    <p>Intenta con otras palabras clave o ajusta los filtros</p>
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
            --radius-sm: 8px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        body { background-color: var(--bg-body); color: var(--text-main); font-family: 'Inter', sans-serif; margin: 0; }
        .app-container { max-width: 1250px; margin: 20px auto; padding: 0 20px; }

        /* --- Header --- */
        .main-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .text-title { font-family: 'Outfit'; font-size: 2.2rem; font-weight: 800; letter-spacing: -0.02em; margin: 0; }
        .header-pretitle { font-size: 10px; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.1em; }

        /* --- Métricas Superior --- */
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
        .dot-activo { background: #10b981; } .dot-pausado { background: #f59e0b; } .dot-borrador { background: #94a3b8; } .dot-completado { background: #6366f1; } .dot-archivado { background: #475569; }

        /* --- Insights Sección con Selector --- */
        .insights-section { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 20px; 
            margin-bottom: 30px; 
        }
        
        .insights-header {
            grid-column: 1 / -1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .insights-header h2 {
            font-family: 'Outfit';
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            color: var(--text-main);
        }
        
        .chart-view-selector {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .chart-view-selector label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
        }
        
        .chart-selector {
            padding: 8px 12px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-color);
            background: white;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .chart-selector:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        .insight-card { 
            background: white; 
            border: 1px solid var(--border-color); 
            border-radius: var(--radius-md); 
            padding: 20px; 
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .insight-card:hover { 
            border-color: var(--primary); 
            box-shadow: 0 8px 15px rgba(0,0,0,0.05);
            transform: translateY(-2px);
        }
        
        .insight-card h3 { margin: 0; font-family: 'Outfit'; font-size: 16px; font-weight: 700; }
        .insight-card p { margin: 2px 0 15px; font-size: 12px; color: var(--text-muted); }
        .chart-container { height: 180px; position: relative; width: 100%; }
        
        /* --- Leyendas y Estadísticas Personalizadas --- */
        .chart-legend-custom {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
        }
        
        .legend-color {
            width: 10px;
            height: 10px;
            border-radius: 2px;
        }
        
        .legend-value {
            color: var(--text-main);
            font-weight: 700;
        }
        
        .chart-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #f1f5f9;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-value {
            font-family: 'Outfit';
            font-size: 18px;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1;
        }
        
        .stat-label {
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 600;
            margin-top: 2px;
        }
        
        .stat-percentage {
            font-size: 9px;
            color: var(--text-muted);
            opacity: 0.7;
            font-weight: 500;
        }

        /* --- Toolbar Buscador --- */
        .search-and-filter-wrapper { max-width: 550px; margin-bottom: 25px; position: relative; }
        .search-inner { display: flex; align-items: stretch; background: white; border-radius: 14px; border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); overflow: hidden; height: 48px; }
        .search-input-group { display: flex; align-items: center; flex: 1; padding: 0 15px; position: relative; }
        .search-input-group input { border: none; padding: 10px; flex: 1; outline: none; font-size: 14px; background: transparent; width: 100%; }
        .search-clear-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 5px;
            margin-left: 5px;
            border-radius: 50%;
            transition: all 0.2s ease;
        }
        .search-clear-btn:hover {
            background: rgba(0, 0, 0, 0.05);
            color: var(--text-main);
        }
        .v-divider { width: 1px; background: #f1f5f9; margin: 10px 0; }
        
        /* --- BOTÓN DE FILTRO MEJORADO --- */
        .btn-filter-toggle { 
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); 
            border: none; 
            padding: 0 20px; 
            font-weight: 600; 
            color: var(--text-main); 
            cursor: pointer; 
            display: flex; 
            align-items: center; 
            gap: 8px; 
            font-size: 13px; 
            position: relative;
            transition: all 0.3s ease;
            border-left: 1px solid var(--border-color);
        }
        
        .btn-filter-toggle:hover {
            background: linear-gradient(135deg, var(--primary-light) 0%, #e0e7ff 100%);
            color: var(--primary);
        }
        
        .btn-filter-toggle i {
            font-size: 14px;
            transition: transform 0.3s ease;
        }
        
        .btn-filter-toggle:hover i {
            transform: rotate(90deg);
        }
        
        .btn-filter-toggle.active {
            background: var(--primary);
            color: white;
        }
        
        .filter-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 18px;
            height: 18px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            font-size: 10px;
            font-weight: 700;
            margin-left: 4px;
            opacity: 0;
            transform: scale(0.8);
            transition: all 0.3s ease;
        }
        
        .btn-filter-toggle:hover .filter-badge,
        .btn-filter-toggle.active .filter-badge {
            opacity: 1;
            transform: scale(1);
        }
        
        .btn-filter-toggle.active .filter-badge {
            background: white;
            color: var(--primary);
        }

        /* --- INDICADOR DE BÚSQUEDA --- */
        .search-indicator {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }
        
        .search-info {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #0369a1;
        }
        
        .search-info i {
            color: #0284c7;
        }
        
        .search-info strong {
            background: white;
            padding: 2px 6px;
            border-radius: 4px;
            margin: 0 2px;
            font-weight: 600;
        }
        
        .clear-all-filters {
            color: #0369a1;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
        }
        
        .clear-all-filters:hover {
            color: #0c4a6e;
        }

        /* --- POPOVER DE FILTROS MEJORADO --- */
        .advanced-filter-popover { 
            position: absolute; 
            top: calc(100% + 10px); 
            left: 0; 
            width: 330px; 
            background: white; 
            border-radius: var(--radius-md); 
            box-shadow: var(--shadow-xl); 
            z-index: 1000; 
            border: 1px solid var(--border-color); 
            display: none; 
            /* overflow: hidden;  <-- ELIMINADO para que el dropdown de Tom Select salga del popover si es necesario */
            transform: translateY(-10px);
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .advanced-filter-popover.show { 
            display: block; 
            animation: slideUp 0.3s ease-out forwards;
        }
        
        .popover-header {
            padding: 16px 20px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: var(--radius-md) var(--radius-md) 0 0;
        }
        
        .popover-title {
            font-weight: 700;
            font-size: 14px;
            color: var(--text-main);
        }
        
        .popover-close {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 14px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        
        .popover-close:hover {
            background: rgba(0, 0, 0, 0.05);
            color: var(--text-main);
        }
        
        .acc-header { 
            padding: 14px 20px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            cursor: pointer; 
            font-size: 13px; 
            font-weight: 600;
            transition: background-color 0.2s ease;
        }
        
        .acc-header:hover {
            background-color: rgba(79, 70, 229, 0.05);
        }
        
        .acc-content { padding: 0 20px 15px; display: none; }
        .acc-item.active .acc-content { display: block; }
        .filter-options-grid { display: flex; flex-wrap: wrap; gap: 8px; }
        .radio-box { 
            display: inline-block; 
            padding: 8px 14px; 
            background: #f8fafc; 
            border-radius: var(--radius-sm); 
            font-size: 11px; 
            font-weight: 600; 
            cursor: pointer; 
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }
        
        .radio-box:hover {
            background: var(--primary-light);
            border-color: var(--primary);
        }
        
        .custom-radio input { display: none; }
        .custom-radio input:checked + .radio-box { 
            background: var(--primary); 
            border-color: var(--primary); 
            color: white;
            box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);
        }
        
        /* --- ESTILOS PERSONALIZADOS PARA TOM SELECT (Buscador en Filtro) --- */
        .ts-wrapper { width: 100%; }
        /* Hacemos que el input del buscador coincida con el tema del filtro (fondo gris claro) */
        .ts-control {
            border: 1px solid #ddd !important;
            border-radius: var(--radius-sm) !important;
            padding: 10px 12px !important;
            font-size: 13px !important;
            color: var(--text-main) !important;
            background: #f9fafb !important; /* Fondo gris para coincidir con el diseño */
            box-shadow: none !important;
            min-height: auto !important;
        }
        /* Color cuando está seleccionado/enfocado */
        .ts-control.focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1) !important;
        }
        /* El menú desplegable */
        .ts-dropdown {
            border-radius: var(--radius-sm) !important;
            border: 1px solid var(--border-color) !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
            margin-top: 5px !important;
            overflow: hidden;
            z-index: 1050 !important; /* Asegurar que quede por encima del popover */
        }
        .ts-dropdown .option { padding: 8px 12px; font-size: 13px; }
        .ts-dropdown .active { background-color: var(--primary-light); color: var(--primary); font-weight: 600; }
        
        .popover-footer {
            padding: 16px 20px;
            background: #f8fafc;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            gap: 10px;
            border-radius: 0 0 var(--radius-md) var(--radius-md);
        }
        
        .btn-reset-filters {
            background: white;
            border: 1px solid var(--border-color);
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }
        
        .btn-reset-filters:hover {
            background: #f1f5f9;
            color: var(--text-main);
        }
        
        .btn-apply-filters {
            background: var(--primary);
            border: none;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }
        
        .btn-apply-filters:hover {
            background: #4338ca;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        /* --- Listado --- */
        .list-legend { display: grid; grid-template-columns: 50px 2.5fr 1fr 1fr 120px 1.2fr 80px; padding: 0 20px 12px; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }
        .project-card { display: grid; grid-template-columns: 50px 2.5fr 1fr 1fr 120px 1.2fr 80px; align-items: center; background: white; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 18px 20px; margin-bottom: 12px; transition: 0.2s; }
        .project-card:hover { border-color: var(--primary); transform: translateY(-2px); }
        .overdue-alert { border-left: 4px solid #ef4444; }
        .project-name-link { font-weight: 700; color: var(--text-main); text-decoration: none; font-size: 15px; }
        .date-sub { font-size: 11px; color: var(--text-muted); margin-top: 4px; }
        .search-snippet {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 6px;
            line-height: 1.4;
        }
        mark {
            background: #fef08a;
            color: #713f12;
            padding: 1px 3px;
            border-radius: 3px;
            font-weight: 600;
        }
        .status-pill-neo { font-size: 10px; font-weight: 800; padding: 4px 10px; border-radius: 7px; width: fit-content; text-transform: uppercase; }
        .status-activo { background: #dcfce7; color: #15803d; }
        .status-pausado { background: #fef3c7; color: #b45309; }
        .status-completado { background: #e0e7ff; color: #4338ca; }
        .status-archivado { background: #f1f5f9; color: #475569; }

        /* Barra de Progreso Restaurada */
        .progress-container-neo { display: flex; align-items: center; gap: 10px; }
        .progress-track { flex: 1; height: 7px; background: #f1f5f9; border-radius: 10px; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 10px; transition: width 1s ease; }
        .progress-val { font-size: 11px; font-weight: 700; color: var(--text-muted); min-width: 30px; }

        .lead-avatar { width: 30px; height: 30px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: var(--primary); border: 1px solid #e2e8f0; }

        /* --- Mobile Styles --- */
        @media (max-width: 900px) {
            .insights-section { grid-template-columns: 1fr; }
            .insights-header { flex-direction: column; align-items: flex-start; gap: 10px; }
            .list-legend { display: none; }
            .project-card { grid-template-columns: 1fr 1fr; gap: 15px; padding: 18px; }
            .p-col-main { grid-column: span 2; }
            .p-col-status { grid-column: span 1; }
            .p-col-prio { grid-column: span 1; justify-self: end; }
            .p-col-lead { grid-column: span 2; border-top: 1px solid #f1f5f9; padding-top: 10px; }
            .p-col-progress { grid-column: span 2; }
            .p-col-actions { grid-column: span 2; }
            .advanced-filter-popover { position: fixed; top: auto; bottom: 10px; left: 10px; right: 10px; width: auto; }
            .search-indicator { flex-direction: column; align-items: flex-start; gap: 8px; }
        }

        @keyframes slideUp { 
            from { 
                opacity: 0; 
                transform: translateY(15px); 
            } 
            to { 
                opacity: 1; 
                transform: translateY(0); 
            } 
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- 3. Librería Tom Select JS --}}
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- INICIALIZAR TOM SELECT EN EL FILTRO ---
            // Importante: create: false y dropdownParent: 'body' para que no se corte en el popover
            let userSelect = new TomSelect('#user-opt', {
                create: false,
                sortField: { field: "text", direction: "asc" },
                plugins: ['dropdown_input'],
                dropdownParent: 'body' // Truco clave para popovers
            });

            // --- Datos dinámicos desde PHP ---
            const globalData = {
                status: {
                    labels: ['Borrador', 'Activo', 'Pausado', 'Hecho', 'Archivado'],
                    data: [{{ $counts['borrador'] ?? 0 }}, {{ $counts['activo'] ?? 0 }}, {{ $counts['pausado'] ?? 0 }}, {{ $counts['completado'] ?? 0 }}, {{ $counts['archivado'] ?? 0 }}],
                    colors: ['#94a3b8', '#10b981', '#f59e0b', '#6366f1', '#475569']
                },
                compliance: {
                    labels: ['A Tiempo', 'Atrasados', 'Hechos'],
                    data: [{{ $cumplimiento['a_tiempo'] }}, {{ $cumplimiento['atrasados'] }}, {{ $cumplimiento['completados'] }}],
                    colors: ['#10b981', '#ef4444', '#6366f1']
                },
                workload: {
                    labels: {!! json_encode($leadersData->map(fn($ld) => explode(' ', $ld->asignado->name)[0])) !!},
                    data: {!! json_encode($leadersData->pluck('total')) !!}
                }
            };
            
            const filteredData = {
                status: {
                    labels: ['Borrador', 'Activo', 'Pausado', 'Hecho', 'Archivado'],
                    data: [{{ $filteredCounts['borrador'] ?? 0 }}, {{ $filteredCounts['activo'] ?? 0 }}, {{ $filteredCounts['pausado'] ?? 0 }}, {{ $filteredCounts['completado'] ?? 0 }}, {{ $filteredCounts['archivado'] ?? 0 }}],
                    colors: ['#94a3b8', '#10b981', '#f59e0b', '#6366f1', '#475569']
                },
                compliance: {
                    labels: ['A Tiempo', 'Atrasados', 'Hechos'],
                    data: [{{ $cumplimiento['a_tiempo'] }}, {{ $cumplimiento['atrasados'] }}, {{ $cumplimiento['completados'] }}],
                    colors: ['#10b981', '#ef4444', '#6366f1']
                },
                workload: {
                    labels: {!! json_encode($leadersData->map(fn($ld) => explode(' ', $ld->asignado->name)[0])) !!},
                    data: {!! json_encode($leadersData->pluck('total')) !!}
                }
            };
            
            let currentView = 'global';
            let currentData = currentView === 'global' ? globalData : filteredData;
            let charts = {};

            // --- Funciones para crear gráficas ---
            function createDoughnutChart(ctx, data, legendId) {
                const total = data.data.reduce((a, b) => a + b, 0);
                const percentages = data.data.map(value => total > 0 ? ((value / total) * 100).toFixed(1) : '0.0');
                
                // Crear leyenda personalizada
                const legendContainer = document.getElementById(legendId);
                legendContainer.innerHTML = '';
                
                data.labels.forEach((label, index) => {
                    const legendItem = document.createElement('div');
                    legendItem.className = 'legend-item';
                    legendItem.innerHTML = `
                        <div class="legend-color" style="background: ${data.colors[index]}"></div>
                        <span>${label}</span>
                        <span class="legend-value">${data.data[index]} (${percentages[index]}%)</span>
                    `;
                    legendContainer.appendChild(legendItem);
                });
                
                return new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.data,
                            backgroundColor: data.colors,
                            borderWidth: 0,
                            cutout: '75%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const percentage = percentages[context.dataIndex];
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            function createBarChart(ctx, data, statsId) {
                const total = data.data.reduce((a, b) => a + b, 0);
                const percentages = data.data.map(value => total > 0 ? ((value / total) * 100).toFixed(1) : '0.0');
                
                // Crear estadísticas
                const statsContainer = document.getElementById(statsId);
                statsContainer.innerHTML = '';
                
                data.labels.forEach((label, index) => {
                    const statItem = document.createElement('div');
                    statItem.className = 'stat-item';
                    statItem.innerHTML = `
                        <div class="stat-value">${data.data[index]}</div>
                        <div class="stat-label">${label}</div>
                        <div class="stat-percentage">${percentages[index]}%</div>
                    `;
                    statsContainer.appendChild(statItem);
                });
                
                return new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.data,
                            backgroundColor: data.colors,
                            borderRadius: 6,
                            barPercentage: 0.7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed.y || 0;
                                        const percentage = percentages[context.dataIndex];
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                grid: { display: false },
                                ticks: {
                                    stepSize: 1,
                                    font: { size: 10 }
                                }
                            }, 
                            x: { 
                                grid: { display: false },
                                ticks: {
                                    font: { size: 10 }
                                }
                            } 
                        }
                    }
                });
            }
            
            function createHorizontalBarChart(ctx, data, statsId) {
                const total = data.data.reduce((a, b) => a + b, 0);
                const percentages = data.data.map(value => total > 0 ? ((value / total) * 100).toFixed(1) : '0.0');
                
                // Crear estadísticas
                const statsContainer = document.getElementById(statsId);
                statsContainer.innerHTML = '';
                
                const maxIndex = data.data.indexOf(Math.max(...data.data));
                const avgValue = (total / (data.data.length || 1)).toFixed(1);
                
                if (data.data.length > 0) {
                    statsContainer.innerHTML = `
                        <div class="stat-item">
                            <div class="stat-value">${data.data[maxIndex]}</div>
                            <div class="stat-label">Máximo</div>
                            <div class="stat-percentage">${data.labels[maxIndex]}</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">${avgValue}</div>
                            <div class="stat-label">Promedio</div>
                            <div class="stat-percentage">por líder</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">${data.labels.length}</div>
                            <div class="stat-label">Líderes</div>
                            <div class="stat-percentage">activos</div>
                        </div>
                    `;
                }
                
                return new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.data,
                            backgroundColor: '#4f46e5',
                            borderRadius: 4,
                            barPercentage: 0.6
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed.x || 0;
                                        const percentage = percentages[context.dataIndex];
                                        return `${label}: ${value} proyectos (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        scales: { 
                            x: { 
                                beginAtZero: true, 
                                ticks: { 
                                    stepSize: 1,
                                    font: { size: 10 }
                                },
                                grid: { display: false }
                            }, 
                            y: { 
                                grid: { display: false },
                                ticks: {
                                    font: { size: 10 }
                                }
                            } 
                        }
                    }
                });
            }
            
            // --- Función para actualizar todas las gráficas ---
            function updateCharts() {
                currentData = currentView === 'global' ? globalData : filteredData;
                
                // Destruir gráficas existentes
                Object.values(charts).forEach(chart => {
                    if (chart) chart.destroy();
                });
                
                // Crear nuevas gráficas
                charts.status = createDoughnutChart(
                    document.getElementById('statusChart').getContext('2d'),
                    currentData.status,
                    'distribution-legend'
                );
                
                charts.compliance = createBarChart(
                    document.getElementById('complianceChart').getContext('2d'),
                    currentData.compliance,
                    'compliance-stats'
                );
                
                charts.workload = createHorizontalBarChart(
                    document.getElementById('workloadChart').getContext('2d'),
                    currentData.workload,
                    'workload-stats'
                );
            }
            
            // --- Inicializar gráficas ---
            updateCharts();
            
            // --- Selector de vista ---
            document.getElementById('chart-view-select').addEventListener('change', function() {
                currentView = this.value;
                updateCharts();
            });
            
            // --- JS UI ORIGINAL MEJORADO ---
            const popover = document.getElementById('advanced-popover');
            const trigger = document.getElementById('advance-filter-trigger');
            const searchForm = document.getElementById('main-search-form');
            const closeBtn = document.getElementById('close-popover');
            const resetBtn = document.getElementById('reset-filters');
            const filterBadge = document.getElementById('active-filters-count');
            const clearSearchBtn = document.getElementById('clear-search');
            const searchInput = document.getElementById('live-search');

            // Función para contar filtros activos
            function updateFilterBadge() {
                let count = 0;
                if (document.getElementById('hidden-status').value) count++;
                if (document.getElementById('hidden-prio').value) count++;
                if (document.getElementById('hidden-user').value) count++;
                if (document.getElementById('live-search').value) count++;
                
                filterBadge.textContent = count;
                if (count > 0) {
                    filterBadge.style.opacity = '1';
                    filterBadge.style.transform = 'scale(1)';
                } else {
                    filterBadge.style.opacity = '0';
                    filterBadge.style.transform = 'scale(0.8)';
                }
            }

            // Inicializar contador de filtros
            updateFilterBadge();

            // Botón de limpiar búsqueda
            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    searchForm.submit();
                });
            }

            trigger.addEventListener('click', (e) => { 
                e.stopPropagation(); 
                popover.classList.toggle('show'); 
                trigger.classList.toggle('active');
            });
            
            closeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                popover.classList.remove('show');
                trigger.classList.remove('active');
            });
            
            document.addEventListener('click', (e) => { 
                if (!popover.contains(e.target) && e.target !== trigger) {
                    // Verificamos si el clic fue en el dropdown de Tom Select (que está en body)
                    if (!e.target.closest('.ts-dropdown') && !e.target.closest('.ts-wrapper')) {
                        popover.classList.remove('show');
                        trigger.classList.remove('active');
                    }
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
            
            // Funcionalidad de restablecer filtros
            resetBtn.addEventListener('click', () => {
                document.getElementById('hidden-status').value = '';
                document.getElementById('hidden-prio').value = '';
                document.getElementById('hidden-user').value = '';
                document.getElementById('live-search').value = '';
                document.querySelector('input[name="prio_opt"][value=""]').checked = true;
                
                // Limpiar Tom Select
                if(userSelect) userSelect.clear();
                
                searchForm.submit();
            });
        });
    </script>
</x-base-layout>