<x-base-layout>
    {{-- 1. Librerías Externas --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    {{-- Lógica PHP --}}
    @php

        // Lógica de búsqueda y Pestaña Activa
        $searchWords = request()->filled('search') ? array_filter(explode(' ', request('search'))) : [];
        
        // Detectar filtros activos
        $hasActiveFilters = request()->filled('search') || request()->filled('page') || request()->filled('estado') || request()->filled('condicion') || request()->filled('asignado_a');
        $activeTab = $hasActiveFilters ? 'gestion' : 'dashboard';

       

        // Datos Gráficas
        $baseMetrics = App\Models\Flujo\Workflow::query();
        $cumplimiento = [
            'a_tiempo' => (clone $baseMetrics)->where('fecha_fin', '>=', now())->where('estado', '!=', 'completado')->count(),
            'atrasado' => (clone $baseMetrics)->where('fecha_fin', '<', now())->where('estado', '!=', 'completado')->count(),
            'completado' => (clone $baseMetrics)->where('estado', 'completado')->count(),
        ];

        $leadersData = (clone $baseMetrics)->with('asignado')
            ->select('asignado_a', DB::raw('count(*) as total'))
            ->whereNotNull('asignado_a')
            ->groupBy('asignado_a')
            ->get();
    @endphp
    
    <div class="app-container">
        {{-- BREADCRUMBS --}}
        <nav class="breadcrumbs">
            <a href="#" class="crumb-link">Inicio</a>
            <span class="crumb-separator">/</span>
            <span class="crumb-current">Workflows</span>
        </nav>

        {{-- HEADER PRINCIPAL --}}
        <header class="main-header">
            <div class="header-content">
                <h1 class="page-title">Centro de Control de Flujos</h1>
                <p class="page-subtitle">Visión estratégica y gestión operativa de proyectos.</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('flujo.workflows.create') }}" class="btn-primary-neo">
                    <i class="fas fa-plus"></i> <span>Nuevo Proyecto</span>
                </a>
            </div>
        </header>

        {{-- MENÚ HORIZONTAL (TABS) --}}
        <div class="tabs-header">
            <button class="tab-btn {{ $activeTab === 'dashboard' ? 'active' : '' }}" onclick="switchTab('dashboard')">
                <i class="fas fa-chart-pie"></i>
                <span>Tablero de Mando</span>
            </button>
            <button class="tab-btn {{ $activeTab === 'gestion' ? 'active' : '' }}" onclick="switchTab('gestion')">
                <i class="fas fa-tasks"></i>
                <span>Listado y Gestión</span>
                @if($total > 0)
                    <span class="tab-badge">{{ $total }}</span>
                @endif
            </button>
            {{-- NUEVO BOTÓN AGREGADO --}}
            <button class="tab-btn" onclick="switchTab('cronograma')">
                <i class="fas fa-hourglass-half"></i>
                <span>Cronograma de Tiempos</span>
            </button>
        </div>

        {{-- VISTA 1: DASHBOARD --}}
        <div id="view-dashboard" class="tab-content {{ $activeTab === 'dashboard' ? 'active' : '' }}">
            
            <div class="section-title">
                <h3>Indicadores Clave</h3>
            </div>

            {{-- KPI STRIP (DISEÑO HORIZONTAL) --}}
            <div class="kpi-strip">
                {{-- 1. Total (Limpia filtros) --}}
                <div class="kpi-box total clickable" onclick="resetFilters()">
                    <div class="kpi-inner">
                        <span class="kpi-label">Total Proyectos</span>
                        <span class="kpi-number">{{ $total }}</span>
                    </div>
                    <div class="kpi-decor"><i class="fas fa-layer-group"></i></div>
                </div>

                {{-- Tarjetas interactivas por estado --}}
                @foreach(['borrador', 'activo', 'pausado', 'completado', 'archivado'] as $est)
                    @php
                        $color = match($est) {
                            'borrador' => '#94a3b8', // Gris
                            'activo' => '#10b981',   // Verde
                            'pausado' => '#f59e0b',  // Naranja
                            'completado' => '#6366f1', // Indigo
                            'archivado' => '#475569', // Slate
                        };
                        /*$label = match($est) {
                            'borrador' => 'Inicialización',
                            'activo' => 'Ejecución',
                            'pausado' => 'En Cola',
                            'completado' => 'Terminado',
                            'archivado' => 'Rechazado',
                        };*/
                    @endphp
                    <div class="kpi-box clickable" onclick="applyFilter('estado', '{{ $est }}')" style="border-top-color: {{ $color }};">
                        <div class="kpi-inner">
                            <span class="kpi-label" style="color: {{ $color }};">{{-- $label --}} {{ $est }}</span>
                            <span class="kpi-number">{{ $rawCounts[$est] ?? 0 }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- GRÁFICAS --}}
            <div class="section-title" style="margin-top: 30px;">
                <h3>Análisis Gráfico</h3>
            </div>
            
            <div class="charts-grid">
                {{-- 1. Distribución --}}
                <div class="chart-box">
                    <div class="chart-header">
                        <h4>Distribución por Estado</h4>
                    </div>
                    <div class="chart-body">
                        <canvas id="chartDistribution"></canvas>
                    </div>
                </div>

                {{-- 2. Cumplimiento --}}
                <div class="chart-box">
                    <div class="chart-header">
                        <h4>Cumplimiento (Vigencia vs Atrasos)</h4>
                    </div>
                    <div class="chart-body">
                        <canvas id="chartCompliance"></canvas>
                    </div>
                </div>

                {{-- 3. Carga --}}
                <div class="chart-box">
                    <div class="chart-header">
                        <h4>Carga Operativa por Líder</h4>
                    </div>
                    <div class="chart-body">
                        <canvas id="chartWorkload"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- VISTA 2: GESTIÓN --}}
        <div id="view-gestion" class="tab-content {{ $activeTab === 'gestion' ? 'active' : '' }}">
            
            {{-- TOOLBAR --}}
            <div class="toolbar-panel">
                <form action="{{ route('flujo.workflows.index') }}" method="GET" id="main-filter-form" class="toolbar-form">
                    <input type="hidden" name="estado" id="input-estado" value="{{ request('estado') }}">
                    <input type="hidden" name="condicion" id="input-condicion" value="{{ request('condicion') }}">
                    <input type="hidden" name="asignado_a" id="input-user" value="{{ request('asignado_a') }}">
                    
                    <div class="search-group">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar proyecto..." autocomplete="off">
                        @if(request()->filled('search'))
                            <a href="{{ route('flujo.workflows.index') }}" class="clear-search"><i class="fas fa-times"></i></a>
                        @endif
                    </div>

                    <div class="filters-group">
                        <div class="status-legend">
                            @if(request('estado'))
                                <span class="active-filter-badge">
                                    Estado: {{ $estadoLabels[request('estado')] ?? request('estado') }}
                                    <a href="#" onclick="applyFilter('estado', ''); return false;"><i class="fas fa-times"></i></a>
                                </span>
                            @endif
                            @if(request('condicion'))
                                <span class="active-filter-badge warning">
                                    Filtro: {{ ucfirst(str_replace('_', ' ', request('condicion'))) }}
                                    <a href="#" onclick="applyFilter('condicion', ''); return false;"><i class="fas fa-times"></i></a>
                                </span>
                            @endif
                        </div>

                        <div class="leader-select-wrapper">
                            <select id="leader-select" onchange="applyFilter('user', this.value)" placeholder="Filtrar por Líder...">
                                <option value="">Todos los líderes</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ request('asignado_a') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <button type="button" class="btn-reset" onclick="resetFilters()">
                            <i class="fas fa-undo"></i>
                        </button>
                    </div>
                </form>
            </div>

            {{-- LISTADO --}}
            <div class="projects-list">
                <div class="list-header d-none-mobile">
                    <div class="col-main">Proyecto</div>
                    <div class="col-status">Estado</div>
                    <div class="col-progress">Progreso Estimado</div>
                    <div class="col-meta">Detalles</div>
                    <div class="col-action"></div>
                </div>

                @forelse($workflows as $wf)
                    @php
                    $isOverdue = false;
                    if($wf->estado === 'activo'){
                        $isOverdue = $wf->fecha_fin && $wf->fecha_fin->isPast();
                    }                        
                        $progColor = $isOverdue ? '#ef4444' : match($wf->estado) {
                            'completado' => '#6366f1',
                            'activo'     => '#10b981',
                            'pausado'    => '#f59e0b',
                            'archivado'  => '#475569',
                            default      => '#94a3b8'
                        };                        
                    @endphp

                    <div class="project-row {{ $isOverdue ? 'row-overdue' : '' }}">
                        <div class="col-main">
                            <div class="row-title">
                                <a href="{{ route('flujo.workflows.show', $wf) }}">{{ $wf->nombre }}</a>
                                <span class="row-ref">#{{ $wf->id }}</span>
                            </div>
                            <div class="row-desc">
                                {{ Str::limit($wf->descripcion, 80) }}
                            </div>
                        </div>

                        <div class="col-status">
                            <span class="badge-status {{ $wf->estado }}">
                                {{ $wf->estado }}
                            </span>
                        </div>
                      
                        <div class="col-progress">
                            <div class="progress-container-neo">
                                <div class="progress-track">
                                    <div class="progress-fill" style="width: {{ $wf->progreso }}%; background: {{ $progColor }};"></div>
                                </div>
                                <span class="progress-val">{{ $wf->progreso }}%</span>
                            </div>
                            @if($isOverdue)
                                <span class="overdue-text">Vencido</span>
                            @endif
                        </div>

                        <div class="col-meta">
                            <div class="meta-item">
                                <div class="mini-avatar" title="{{ $wf->asignado?->name ?? 'Sin asignar' }}">{{ substr($wf->asignado?->name ?? '?', 0, 1) }}</div>
                                <span class="d-none-desktop">{{ $wf->asignado?->name ?? 'Sin Asignar' }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="far fa-calendar"></i> {{ $wf->fecha_fin?->format('d M') ?? '--' }}
                            </div>
                        </div>

                        <div class="col-action">
                            <a href="{{ route('flujo.workflows.show', $wf) }}" class="btn-icon">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="empty-state-simple">
                        <div class="empty-icon"><i class="fas fa-search"></i></div>
                        <p>No se encontraron resultados para los filtros seleccionados.</p>
                        <button onclick="resetFilters()" class="btn-text">Limpiar todo</button>
                    </div>
                @endforelse
            </div>

            <div class="pagination-wrapper">
                {{ $workflows->appends(request()->query())->links() }}
            </div>
        </div>

        {{-- VISTA 3: CRONOGRAMA DE TIEMPOS (NUEVO) --}}
        <div id="view-cronograma" class="tab-content">
            <div class="section-title">
                <h3>Seguimiento Temporal</h3>
            </div>

            {{-- Tabla estilo Excel para visualización rápida de fechas --}}
            <div class="chart-box" style="padding: 0; overflow: hidden;">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid var(--border); text-align: left;">
                                <th style="padding: 15px 20px; font-size: 12px; font-weight: 700; color: var(--text-gray);">PROYECTO</th>
                                <th style="padding: 15px 20px; font-size: 12px; font-weight: 700; color: var(--text-gray);">FECHA INICIO</th>
                                <th style="padding: 15px 20px; font-size: 12px; font-weight: 700; color: var(--text-gray);">FECHA FINAL</th>
                                <th style="padding: 15px 20px; font-size: 12px; font-weight: 700; color: var(--text-gray);">PROGRESO EST.</th>
                                <th style="padding: 15px 20px; font-size: 12px; font-weight: 700; color: var(--text-gray);">ESTADO DE TIEMPO</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($workflows as $wf)
                                @php
                                    // Lógica de progreso visual (Misma que en gestión)
                                    $progWidth = in_array($wf->estado, ['completado', 'archivado']) ? 100 : ($wf->estado == 'activo' ? 65 : 20);
                                    $progColor = match($wf->estado) {
                                        'completado' => '#10b981', // Verde
                                        'archivado' => '#94a3b8',  // Gris
                                        'activo' => '#4f46e5',     // Azul
                                        default => '#f59e0b'       // Naranja
                                    };

                                    // LÓGICA DE TIEMPO SOLICITADA
                                    $timeStatus = '';
                                    $timeColor = '';
                                    $timeIcon = '';

                                    if ($wf->estado === 'completado') {
                                        // CASO 1: YA TERMINÓ -> CUÁNDO LO TERMINÓ
                                        $timeStatus = 'Finalizado hace ' . $wf->updated_at->diffForHumans(null, true);
                                        $timeColor = '#10b981'; // Verde
                                        $timeIcon = 'fa-check-circle';
                                    } elseif ($wf->fecha_fin && $wf->fecha_fin->isPast() && $wf->estado !== 'archivado') {
                                        // CASO 2: SE PASÓ DE LA FECHA -> TIEMPO DE RETRASO
                                        $timeStatus = 'Vencido hace ' . $wf->fecha_fin->diffForHumans(null, true);
                                        $timeColor = '#ef4444'; // Rojo
                                        $timeIcon = 'fa-exclamation-circle';
                                    } elseif ($wf->fecha_fin) {
                                        // CASO 3: AÚN A TIEMPO -> CUÁNTO FALTA
                                        $timeStatus = 'Vence en ' . $wf->fecha_fin->diffForHumans(null, true);
                                        $timeColor = '#3b82f6'; // Azul
                                        $timeIcon = 'fa-clock';
                                    } else {
                                        $timeStatus = 'Sin fecha límite';
                                        $timeColor = '#94a3b8';
                                        $timeIcon = 'fa-infinity';
                                    }
                                @endphp
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <td style="padding: 15px 20px;">
                                        <div style="font-weight: 600; color: var(--text-dark);">{{ $wf->nombre }}</div>
                                        <div style="font-size: 11px; color: var(--text-gray);">{{ $wf->estado }}</div>
                                    </td>
                                    <td style="padding: 15px 20px; color: var(--text-gray); font-size: 13px;">
                                        {{ $wf->fecha_inicio ? $wf->fecha_inicio->format('d M, Y') : '--' }}
                                    </td>
                                    <td style="padding: 15px 20px; color: var(--text-gray); font-size: 13px;">
                                        {{ $wf->fecha_fin ? $wf->fecha_fin->format('d M, Y') : 'Indefinido' }}
                                    </td>
                                    <td style="padding: 15px 20px;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <div style="flex: 1; height: 6px; background: #f1f5f9; border-radius: 4px; overflow: hidden; width: 80px;">
                                                <div style="height: 100%; width: {{ $progWidth }}%; background: {{ $progColor }};"></div>
                                            </div>
                                            <span style="font-size: 12px; font-weight: 700; color: {{ $progColor }};">{{ $progWidth }}%</span>
                                        </div>
                                    </td>
                                    <td style="padding: 15px 20px;">
                                        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background-color: {{ $timeColor }}15; color: {{ $timeColor }}; border: 1px solid {{ $timeColor }}30;">
                                            <i class="fas {{ $timeIcon }}"></i>
                                            {{ $timeStatus }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="padding: 40px; text-align: center; color: var(--text-gray);">
                                        No hay proyectos para mostrar en el cronograma.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- Paginación compartida --}}
                <div class="pagination-wrapper" style="padding: 15px;">
                    {{ $workflows->appends(request()->query())->links() }}
                </div>
            </div>
        </div>        
    </div>

    {{-- ESTILOS CSS --}}
    <style>
        :root {
            --brand: #4f46e5;
            --bg-page: #f8fafc;
            --bg-card: #ffffff;
            --text-dark: #0f172a;
            --text-gray: #64748b;
            --border: #e2e8f0;
            --radius: 12px;
        }

        body { background: var(--bg-page); font-family: 'Inter', sans-serif; color: var(--text-dark); margin: 0; }
        .app-container { max-width: 1250px; margin: 20px auto; padding: 0 20px; }

        /* HEADER & BREADCRUMBS */
        .breadcrumbs { font-size: 13px; color: var(--text-gray); margin-bottom: 20px; display: flex; gap: 8px; }
        .crumb-link { text-decoration: none; color: var(--text-gray); }
        .crumb-current { font-weight: 600; color: var(--text-dark); }
        
        .main-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; }
        .page-title { font-size: 28px; font-weight: 800; margin: 0; font-family: 'Outfit', sans-serif; letter-spacing: -0.02em; }
        .page-subtitle { margin: 5px 0 0; color: var(--text-gray); font-size: 14px; }
        
        .btn-primary-neo { background: var(--brand); color: white; padding: 10px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); }
        .btn-primary-neo:hover { background: #4338ca; transform: translateY(-1px); }

        /* TABS */
        .tabs-container { border-bottom: 1px solid var(--border); margin-bottom: 30px; }
        .tabs-header { display: flex; gap: 30px; }
        .tab-btn { background: none; border: none; padding: 12px 0; font-size: 14px; font-weight: 600; color: var(--text-gray); cursor: pointer; border-bottom: 2px solid transparent; display: flex; align-items: center; gap: 8px; transition: 0.2s; }
        .tab-btn:hover { color: var(--brand); }
        .tab-btn.active { color: var(--brand); border-bottom-color: var(--brand); }
        .tab-badge { background: #eff6ff; color: var(--brand); padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 700; }
        .tab-content { display: none; animation: fadeIn 0.3s ease; }
        .tab-content.active { display: block; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(5px);} to{opacity:1;transform:translateY(0);} }

        /* --- NUEVO DISEÑO KPI HORIZONTAL (STRIP) --- */
        .section-title { margin-bottom: 15px; }
        .section-title h3 { font-size: 18px; font-weight: 700; margin: 0; }

        .kpi-strip {
            display: grid;
            /* Aquí forzamos 6 columnas iguales para que abarque todo el horizontal */
            grid-template-columns: repeat(6, 1fr);
            gap: 12px;
            margin-bottom: 30px;
        }

        .kpi-box {
            background: white;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid var(--border);
            border-top: 3px solid transparent; /* Para el color superior */
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .kpi-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .kpi-inner {
            display: flex;
            flex-direction: column;
        }

        .kpi-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .kpi-number {
            font-size: 22px;
            font-weight: 800;
            font-family: 'Outfit';
            line-height: 1;
            color: var(--text-dark);
        }

        /* Estilos específicos para la tarjeta TOTAL */
        .kpi-box.total {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            border: none;
            border-top: 3px solid #4f46e5;
        }
        .kpi-box.total .kpi-label { color: rgba(255,255,255,0.8); }
        .kpi-box.total .kpi-number { color: white; }
        .kpi-decor {
            color: white;
            opacity: 0.15;
            font-size: 24px;
            position: absolute;
            right: 10px;
            bottom: -5px;
        }

        /* CHART GRID */
        .charts-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px; }
        .chart-box { background: white; padding: 20px; border-radius: var(--radius); border: 1px solid var(--border); }
        .chart-header { margin-bottom: 15px; font-weight: 700; font-size: 15px; }
        .chart-body { height: 250px; position: relative; }

        /* TOOLBAR */
        .toolbar-panel { background: white; padding: 15px; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 20px; }
        .toolbar-form { display: flex; flex-wrap: wrap; gap: 15px; justify-content: space-between; align-items: center; }
        .search-group { background: #f1f5f9; border-radius: 8px; padding: 0 12px; height: 40px; display: flex; align-items: center; flex: 1; min-width: 200px; }
        .search-group input { border: none; background: transparent; width: 100%; outline: none; font-size: 14px; margin-left: 8px; }
        .search-group i { color: var(--text-gray); }
        .clear-search { color: #ef4444; }
        .filters-group { display: flex; gap: 10px; align-items: center; }
        .active-filter-badge { background: #e0e7ff; color: var(--brand); padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; display: flex; gap: 5px; align-items: center; }
        .active-filter-badge.warning { background: #fee2e2; color: #b91c1c; }
        .active-filter-badge a { color: inherit; text-decoration: none; opacity: 0.6; }
        .btn-reset { width: 40px; height: 40px; border: 1px solid var(--border); background: white; border-radius: 8px; cursor: pointer; color: var(--text-gray); transition: 0.2s; }
        .leader-select-wrapper { width: 220px; }
        .ts-control { border-radius: 8px !important; border-color: var(--border) !important; height: 40px !important; display: flex !important; align-items: center !important; }

        /* LISTADO */
        .list-header { display: flex; padding: 0 20px 10px; font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; }
        .project-row { background: white; border: 1px solid var(--border); border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; margin-bottom: 8px; transition: 0.2s; }
        .project-row:hover { border-color: var(--brand); box-shadow: 0 4px 10px rgba(0,0,0,0.03); transform: translateY(-1px); }
        .row-overdue { border-left: 4px solid #ef4444; }
        .col-main { flex: 2; padding-right: 15px; }
        .col-status { flex: 1; }
        .col-progress { flex: 1.5; padding-right: 20px; }
        .col-meta { flex: 1; display: flex; gap: 15px; align-items: center; font-size: 12px; color: var(--text-gray); }
        .col-action { width: 40px; text-align: right; }
        .row-title { margin-bottom: 4px; display: flex; align-items: center; gap: 8px; }
        .row-title a { font-weight: 700; color: var(--text-dark); text-decoration: none; font-size: 15px; }
        .row-ref { background: #f1f5f9; font-size: 10px; padding: 2px 6px; border-radius: 4px; color: var(--text-gray); font-weight: 600; }
        .row-desc { font-size: 13px; color: var(--text-gray); }
        .progress-container-neo { display: flex; align-items: center; gap: 10px; width: 100%; }
        .progress-track { flex: 1; height: 6px; background: #f1f5f9; border-radius: 10px; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 10px; }
        .progress-val { font-size: 12px; font-weight: 700; color: var(--text-gray); min-width: 35px; text-align: right; }
        .overdue-text { font-size: 10px; color: #ef4444; font-weight: 700; display: block; margin-top: 4px; }
        .badge-status { font-size: 10px; font-weight: 800; text-transform: uppercase; padding: 4px 10px; border-radius: 6px; }
        .badge-status.borrador { background: #f1f5f9; color: #64748b; }
        .badge-status.activo { background: #dcfce7; color: #16a34a; }
        .badge-status.pausado { background: #ffedd5; color: #c2410c; }
        .badge-status.completado { background: #e0e7ff; color: #4f46e5; }
        .badge-status.archivado { background: #f1f5f9; color: #475569; }
        .mini-avatar { width: 26px; height: 26px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700; color: var(--brand); border: 1px solid var(--border); }
        .btn-icon { color: var(--text-gray); transition: 0.2s; }
        .btn-icon:hover { color: var(--brand); }
        .btn-text { background: none; border: none; color: var(--brand); font-weight: 600; cursor: pointer; text-decoration: underline; margin-top: 10px; }
        .empty-state-simple { text-align: center; padding: 40px; color: var(--text-gray); }
        .empty-icon { font-size: 24px; margin-bottom: 10px; opacity: 0.5; }

        @media (max-width: 1024px) {
            /* En pantallas medianas, dividir en 2 filas de 3 */
            .kpi-strip { grid-template-columns: repeat(3, 1fr); }
        }

        @media (max-width: 900px) {
            .charts-grid { grid-template-columns: 1fr; }
            .toolbar-form { flex-direction: column; align-items: stretch; }
            .list-header { display: none; }
            .project-row { flex-direction: column; align-items: flex-start; gap: 15px; }
            .col-main, .col-progress, .col-meta { width: 100%; padding: 0; }
            .col-action { position: absolute; top: 15px; right: 15px; }
            .d-none-desktop { display: inline; margin-left: 8px; }
        }
        
        @media (max-width: 600px) {
            /* En móvil, 2 columnas */
            .kpi-strip { grid-template-columns: repeat(2, 1fr); }
        }
    </style>

    <script>
        // --- 1. Control de Pestañas (ACTUALIZADO) ---
        function switchTab(tabName) {
            // Ocultar todos los contenidos
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            // Desactivar todos los botones
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            
            // Activar el contenido seleccionado
            const targetView = document.getElementById('view-' + tabName);
            if(targetView) targetView.classList.add('active');
            
            // Activar el botón correspondiente (lógica por índice)
            const btns = document.querySelectorAll('.tab-btn');
            if(tabName === 'dashboard' && btns[0]) btns[0].classList.add('active');
            if(tabName === 'gestion' && btns[1]) btns[1].classList.add('active');
            if(tabName === 'cronograma' && btns[2]) btns[2].classList.add('active');
        }

        // --- 2. Lógica de Filtrado Interactivo ---
        function applyFilter(type, value) {
            if(type === 'estado') document.getElementById('input-estado').value = value;
            if(type === 'condicion') document.getElementById('input-condicion').value = value;
            if(type === 'user') document.getElementById('input-user').value = value;
            
            if(type === 'estado') document.getElementById('input-condicion').value = '';
            if(type === 'condicion') document.getElementById('input-estado').value = '';

            document.getElementById('main-filter-form').submit();
        }

        function resetFilters() {
            document.getElementById('input-estado').value = '';
            document.getElementById('input-condicion').value = '';
            document.getElementById('input-user').value = '';
            document.querySelector('input[name="search"]').value = '';
            document.getElementById('main-filter-form').submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect("#leader-select",{ create: false, sortField: { field: "text", direction: "asc" } });

            // --- 3. Gráficas Interactivas ---
            const ctxDist = document.getElementById('chartDistribution');
            if(ctxDist) {
                new Chart(ctxDist, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode(array_values($estadoLabels)) !!},
                        keys: {!! json_encode(array_keys($estadoLabels)) !!},
                        datasets: [{
                            data: [{{ $rawCounts['borrador']??0 }}, {{ $rawCounts['activo']??0 }}, {{ $rawCounts['pausado']??0 }}, {{ $rawCounts['completado']??0 }}, {{ $rawCounts['archivado']??0 }}],
                            backgroundColor: ['#94a3b8', '#10b981', '#f59e0b', '#6366f1', '#475569'],
                            borderWidth: 0
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false, 
                        plugins: { legend: { position: 'right' } }, 
                        cutout: '70%',
                        onHover: (event, chartElement) => { event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default'; },
                        onClick: (evt, elements) => {
                            if(elements.length > 0) {
                                const index = elements[0].index;
                                const key = evt.chart.data.keys[index];
                                applyFilter('estado', key);
                            }
                        }
                    }
                });
            }

            const ctxComp = document.getElementById('chartCompliance');
            if(ctxComp) {
                new Chart(ctxComp, {
                    type: 'bar',
                    data: {
                        labels: ['A Tiempo', 'Atrasados', 'Completados'],
                        keys: ['a_tiempo', 'atrasado', 'completado'],
                        datasets: [{
                            data: [{{ $cumplimiento['a_tiempo'] }}, {{ $cumplimiento['atrasado'] }}, {{ $cumplimiento['completado'] }}],
                            backgroundColor: ['#10b981', '#ef4444', '#6366f1'],
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, grid: { display: false } }, x: { grid: { display: false } } },
                        onHover: (event, chartElement) => { event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default'; },
                        onClick: (evt, elements) => {
                            if(elements.length > 0) {
                                const index = elements[0].index;
                                const key = evt.chart.data.keys[index];
                                if(key === 'completado') { applyFilter('estado', 'completado'); } 
                                else { applyFilter('condicion', key); }
                            }
                        }
                    }
                });
            }

            const ctxWork = document.getElementById('chartWorkload');
            if(ctxWork) {
                new Chart(ctxWork, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($leadersData->map(fn($l) => explode(' ', $l->asignado->name)[0])) !!},
                        ids: {!! json_encode($leadersData->pluck('asignado_a')) !!},
                        datasets: [{
                            label: 'Proyectos',
                            data: {!! json_encode($leadersData->pluck('total')) !!},
                            backgroundColor: '#4f46e5',
                            borderRadius: 6
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false, 
                        scales: { y: { beginAtZero: true, grid: { display: false } }, x: { grid: { display: false } } },
                        onHover: (event, chartElement) => { event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default'; },
                        onClick: (evt, elements) => {
                            if(elements.length > 0) {
                                const index = elements[0].index;
                                const userId = evt.chart.data.ids[index];
                                applyFilter('user', userId);
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-base-layout>