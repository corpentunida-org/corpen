<x-base-layout>
    {{-- 1. Librerías Externas --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    @php
        // 1. Lógica de Pestaña Activa y Filtros
        $hasActiveFilters = request()->filled('search') || request()->filled('page') || request()->filled('estado_id') || request()->filled('usuario_id') || request()->filled('condicion');
        $activeTab = $hasActiveFilters ? 'gestion' : 'dashboard';

        // 2. Consulta Base con Nombres de Clases Completos
        $query = \App\Models\Correspondencia\Correspondencia::with(['trd', 'estado', 'usuario']);

        // 3. Aplicación de Filtros Dinámicos
        if (request()->filled('search')) {
            $words = explode(' ', request('search'));
            $query->where(function($q) use ($words) {
                foreach ($words as $word) {
                    $q->orWhere('id_radicado', 'LIKE', "%$word%")
                      ->orWhere('asunto', 'LIKE', "%$word%");
                }
            });
        }
        
        if (request()->filled('estado_id')) {
            $query->where('estado_id', request('estado_id'));
        }
        
        if (request()->filled('usuario_id')) {
            $query->where('usuario_id', request('usuario_id'));
        }

        // 4. Lógica de Tiempos y Vencimientos basada en TRD
        if (request('condicion') == 'vencido') {
            $query->whereHas('trd', function($q) {
                $q->whereRaw("DATE_ADD(corr_correspondencia.fecha_solicitud, INTERVAL corr_trd.tiempo_gestion DAY) < NOW()");
            })->where('finalizado', false);
        }

        // 5. Resultados Paginados
        $correspondencias = $query->orderBy('fecha_solicitud', 'desc')->paginate(10);

        // --- DATOS PARA EL DASHBOARD ---
        $estadosKpi = \App\Models\Correspondencia\Estado::withCount('correspondencias')->get();
        $total = $estadosKpi->sum('correspondencias_count');
        
        $usuariosCarga = \App\Models\User::withCount(['correspondencias' => function($q) {
                $q->where('finalizado', false);
            }])
            ->orderBy('correspondencias_count', 'desc')
            ->take(5)
            ->get();
        
        $todosLosUsuarios = \App\Models\User::orderBy('name')->get();

        // 6. Preparar variables para el archivo chart_data.blade.php
        $chartDistribucion = [
            'labels' => $estadosKpi->pluck('nombre'),
            'data' => $estadosKpi->pluck('correspondencias_count'),
        ];
        $chartCarga = [
            'labels' => $usuariosCarga->pluck('name'),
            'data' => $usuariosCarga->pluck('correspondencias_count'),
        ];
    @endphp

    <div class="app-container">
        {{-- BREADCRUMBS --}}
        <nav class="breadcrumbs">
            <a href="#" class="crumb-link">Gestión Documental</a>
            <span class="crumb-separator">/</span>
            <span class="crumb-current">Tablero de Correspondencia</span>
        </nav>

        {{-- HEADER PRINCIPAL --}}
        <header class="main-header">
            <div class="header-content">
                <h1 class="page-title">Centro de Control Documental</h1>
                <p class="page-subtitle">Monitoreo estratégico de radicación y tiempos de respuesta.</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('correspondencia.correspondencias.create') }}" class="btn-primary-neo">
                    <i class="fas fa-file-signature"></i> <span>Nuevo Radicado</span>
                </a>
            </div>
        </header>

        {{-- MENÚ DE PESTAÑAS --}}
        <div class="tabs-header">
            <button class="tab-btn {{ $activeTab === 'dashboard' ? 'active' : '' }}" onclick="switchTab('dashboard')">
                <i class="fas fa-chart-pie"></i>
                <span>Indicadores Generales</span>
            </button>
            <button class="tab-btn {{ $activeTab === 'gestion' ? 'active' : '' }}" onclick="switchTab('gestion')">
                <i class="fas fa-archive"></i>
                <span>Gestión de Radicados</span>
                @if($total > 0)
                    <span class="tab-badge">{{ $total }}</span>
                @endif
            </button>
        </div>

        {{-- VISTA 1: DASHBOARD --}}
        <div id="view-dashboard" class="tab-content {{ $activeTab === 'dashboard' ? 'active' : '' }}">
            <div class="section-title">
                <h3>Resumen de Estados</h3>
            </div>

            <div class="kpi-strip">
                <div class="kpi-box total clickable" onclick="resetFilters()">
                    <div class="kpi-inner">
                        <span class="kpi-label">Total Radicados</span>
                        <span class="kpi-number">{{ $total }}</span>
                    </div>
                    <div class="kpi-decor"><i class="fas fa-folder-open"></i></div>
                </div>

                @foreach($estadosKpi as $est)
                    <div class="kpi-box clickable" onclick="applyFilter('estado_id', '{{ $est->id }}')">
                        <div class="kpi-inner">
                            <span class="kpi-label">{{ $est->nombre }}</span>
                            <span class="kpi-number">{{ $est->correspondencias_count }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="charts-grid">
                <div class="chart-box">
                    <div class="chart-header"><h4>Distribución de Solicitudes</h4></div>
                    <div class="chart-body"><canvas id="chartDistribution"></canvas></div>
                </div>
                <div class="chart-box">
                    <div class="chart-header"><h4>Carga de Trabajo por Usuario</h4></div>
                    <div class="chart-body"><canvas id="chartWorkload"></canvas></div>
                </div>
            </div>
        </div>

        {{-- VISTA 2: GESTIÓN Y LISTADO --}}
        <div id="view-gestion" class="tab-content {{ $activeTab === 'gestion' ? 'active' : '' }}">
            <div class="toolbar-panel">
                <form action="{{ route('correspondencia.tablero') }}" method="GET" id="filter-form" class="toolbar-form">
                    <input type="hidden" name="estado_id" id="input-estado" value="{{ request('estado_id') }}">
                    <input type="hidden" name="condicion" id="input-condicion" value="{{ request('condicion') }}">
                    
                    <div class="search-group">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por radicado o asunto...">
                    </div>

                    <div class="filters-group">
                        <div class="select-wrapper" style="width: 250px;">
                            <select name="usuario_id" class="form-control" onchange="this.form.submit()">
                                <option value="">Todos los Usuarios</option>
                                @foreach($todosLosUsuarios as $u)
                                    <option value="{{ $u->id }}" {{ request('usuario_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" class="btn-reset" onclick="resetFilters()"><i class="fas fa-sync-alt"></i></button>
                    </div>
                </form>
            </div>

            <div class="projects-list">
                @forelse($correspondencias as $c)
                    @php
                        $fechaLimite = $c->trd ? \Carbon\Carbon::parse($c->fecha_solicitud)->addDays($c->trd->tiempo_gestion) : null;
                        $isVencido = $fechaLimite && $fechaLimite->isPast() && !$c->finalizado;
                    @endphp
                    <div class="project-row {{ $isVencido ? 'row-overdue' : '' }}">
                        <div class="col-main">
                            <div class="row-title">
                                <span class="row-ref">#{{ $c->id_radicado }}</span>
                                <a href="{{ route('correspondencia.correspondencias.show', $c) }}">{{ $c->asunto }}</a>
                            </div>
                            <div class="row-desc">
                                <strong>Asignado a:</strong> {{ $c->usuario->name ?? 'Sin asignar' }} | 
                                <strong>Recibido:</strong> {{ \Carbon\Carbon::parse($c->fecha_solicitud)->format('d/m/Y') }}
                            </div>
                        </div>
                        <div class="col-status">
                            <span class="badge-status">{{ $c->estado->nombre ?? 'Sin Estado' }}</span>
                        </div>
                        <div class="col-meta">
                            @if($fechaLimite)
                                <div class="meta-item {{ $isVencido ? 'text-danger font-weight-bold' : '' }}">
                                    <i class="fas fa-clock"></i> {{ $isVencido ? 'Vencido' : 'Vence' }} {{ $fechaLimite->diffForHumans() }}
                                </div>
                            @endif
                        </div>
                        <div class="col-action">
                            <a href="{{ route('correspondencia.correspondencias.edit', $c) }}" class="btn-icon"><i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                @empty
                    <div class="empty-state-simple"><p>No se encontraron registros.</p></div>
                @endforelse
            </div>
            <div class="pagination-wrapper mt-4">{{ $correspondencias->appends(request()->query())->links() }}</div>
        </div>
    </div>

    <style>
        :root { --brand: #4f46e5; --bg-page: #f8fafc; --text-dark: #0f172a; --text-gray: #64748b; --border: #e2e8f0; --radius: 12px; }
        .app-container { max-width: 1300px; margin: 20px auto; padding: 0 20px; }
        .breadcrumbs { font-size: 13px; color: var(--text-gray); margin-bottom: 20px; }
        .main-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .page-title { font-size: 26px; font-weight: 800; color: var(--text-dark); }
        .btn-primary-neo { background: var(--brand); color: white; padding: 10px 22px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; }
        .tabs-header { display: flex; gap: 30px; border-bottom: 1px solid var(--border); margin-bottom: 25px; }
        .tab-btn { background: none; border: none; padding: 12px 0; font-size: 14px; font-weight: 600; color: var(--text-gray); cursor: pointer; border-bottom: 2px solid transparent; display: flex; align-items: center; gap: 8px; }
        .tab-btn.active { color: var(--brand); border-bottom-color: var(--brand); }
        .tab-badge { background: #eff6ff; color: var(--brand); padding: 2px 8px; border-radius: 10px; font-size: 11px; }
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeIn 0.3s ease; }
        .kpi-strip { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .kpi-box { background: white; padding: 15px; border-radius: 10px; border: 1px solid var(--border); cursor: pointer; position: relative; }
        .kpi-box.total { background: var(--brand); color: white; border: none; }
        .charts-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; }
        .chart-box { background: white; padding: 20px; border-radius: var(--radius); border: 1px solid var(--border); }
        .chart-body { height: 280px; position: relative; }
        .project-row { background: white; border: 1px solid var(--border); border-radius: 10px; padding: 15px 20px; display: flex; align-items: center; margin-bottom: 10px; }
        .row-overdue { border-left: 5px solid #ef4444; }
        .badge-status { background: #e0e7ff; color: var(--brand); padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('view-' + tabId).classList.add('active');
            event.currentTarget.classList.add('active');
        }
        function applyFilter(key, value) {
            document.getElementById('input-' + key.replace('_id', '')).value = value;
            document.getElementById('filter-form').submit();
        }
        function resetFilters() { window.location.href = "{{ route('correspondencia.tablero') }}"; }
    </script>

    {{-- CARGA DE GRÁFICOS DESDE EL ARCHIVO PARCIAL --}}
    @include('correspondencia.tablero.chart_data')

</x-base-layout>