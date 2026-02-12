<x-base-layout>
    {{-- 1. Librerías Externas --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    @php
        // --- LÓGICA DE BACKEND ---
        $hasActiveFilters = request()->filled('search') || request()->filled('page') || request()->filled('estado_id') || request()->filled('usuario_id') || request()->filled('condicion');
        $activeTab = $hasActiveFilters ? 'gestion' : 'dashboard';

        $query = \App\Models\Correspondencia\Correspondencia::with(['trd', 'estado', 'usuario']);

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

        if (request('condicion') == 'vencido') {
            $query->whereHas('trd', function($q) {
                $q->whereRaw("DATE_ADD(corr_correspondencia.fecha_solicitud, INTERVAL corr_trd.tiempo_gestion DAY) < NOW()");
            })->where('finalizado', false);
        }

        $correspondencias = $query->orderBy('fecha_solicitud', 'desc')->paginate(10);

        $estadosKpi = \App\Models\Correspondencia\Estado::withCount('correspondencias')->get();
        $total = $estadosKpi->sum('correspondencias_count');
        
        $usuariosCarga = \App\Models\User::withCount(['correspondencias' => function($q) {
                $q->where('finalizado', false);
            }])
            ->orderBy('correspondencias_count', 'desc')
            ->take(5)
            ->get();
        
        $todosLosUsuarios = \App\Models\User::orderBy('name')->get();

        $chartDistribucion = [
            'labels' => $estadosKpi->pluck('nombre'),
            'data' => $estadosKpi->pluck('correspondencias_count'),
        ];
        $chartCarga = [
            'labels' => $usuariosCarga->pluck('name'),
            'data' => $usuariosCarga->pluck('correspondencias_count'),
        ];
    @endphp

    <div class="app-shell">
        {{-- HEADER & BREADCRUMBS --}}
        <header class="main-header-modern">
            <div class="container-fluid">
                <nav class="custom-breadcrumbs">
                    <span>Gestión Documental</span>
                    <i class="fas fa-chevron-right mx-2 separator"></i>
                    <span class="active">Tablero de Correspondencia</span>
                </nav>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <h1 class="fw-extrabold mb-1">Centro de Control</h1>
                        <p class="text-muted small mb-0">Monitoreo estratégico de radicación y cumplimiento TRD.</p>
                    </div>
                    <a href="{{ route('correspondencia.correspondencias.create') }}" class="btn btn-indigo-modern shadow-sm">
                        <i class="fas fa-plus-circle me-2"></i> Nuevo Radicado
                    </a>
                </div>
            </div>
        </header>

        {{-- NAVEGACIÓN POR PESTAÑAS --}}
        <div class="tabs-modern-container mt-4">
            <button class="tab-modern-btn {{ $activeTab === 'dashboard' ? 'active' : '' }}" onclick="switchTab('dashboard')">
                <div class="tab-icon-box"><i class="fas fa-chart-line"></i></div>
                <span>Indicadores Generales</span>
            </button>
            <button class="tab-modern-btn {{ $activeTab === 'gestion' ? 'active' : '' }}" onclick="switchTab('gestion')">
                <div class="tab-icon-box"><i class="fas fa-stream"></i></div>
                <span>Gestión de Radicados</span>
                @if($total > 0)
                    <span class="tab-badge-modern">{{ $total }}</span>
                @endif
            </button>
        </div>

        {{-- CONTENIDO DE VISTAS --}}
        <main class="content-wrapper-modern">
            
            {{-- VISTA 1: DASHBOARD --}}
            <div id="view-dashboard" class="tab-content-modern {{ $activeTab === 'dashboard' ? 'active' : '' }}">
                <div class="kpi-grid-modern mb-4">
                    <div class="kpi-card-modern total clickable" onclick="resetFilters()">
                        <div class="kpi-content">
                            <span class="kpi-label-modern text-white-50">Total Radicados</span>
                            <span class="kpi-number-modern text-white">{{ $total }}</span>
                        </div>
                        <div class="kpi-icon-modern"><i class="fas fa-folder-open text-white"></i></div>
                    </div>

                    @foreach($estadosKpi as $est)
                        <div class="kpi-card-modern clickable" onclick="applyFilter('estado_id', '{{ $est->id }}')">
                            <div class="kpi-content">
                                <span class="kpi-label-modern">{{ $est->nombre }}</span>
                                <span class="kpi-number-modern">{{ $est->correspondencias_count }}</span>
                            </div>
                            <div class="kpi-icon-modern soft-bg"><i class="fas fa-tag"></i></div>
                        </div>
                    @endforeach
                </div>

                <div class="charts-row">
                    <div class="chart-container-modern shadow-sm">
                        <div class="chart-header-modern">
                            <h5>Distribución por Estado</h5>
                        </div>
                        <div class="chart-canvas-wrapper">
                            <canvas id="chartDistribution"></canvas>
                        </div>
                    </div>
                    <div class="chart-container-modern shadow-sm">
                        <div class="chart-header-modern">
                            <h5>Carga Operativa por Usuario</h5>
                        </div>
                        <div class="chart-canvas-wrapper">
                            <canvas id="chartWorkload"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- VISTA 2: GESTIÓN --}}
            <div id="view-gestion" class="tab-content-modern {{ $activeTab === 'gestion' ? 'active' : '' }}">
                <div class="toolbar-modern shadow-sm mb-4">
                    <form action="{{ route('correspondencia.tablero') }}" method="GET" id="filter-form" class="row g-3 align-items-center">
                        <input type="hidden" name="estado_id" id="input-estado" value="{{ request('estado_id') }}">
                        <input type="hidden" name="condicion" id="input-condicion" value="{{ request('condicion') }}">
                        
                        <div class="col-md-5">
                            <div class="search-input-modern">
                                <i class="fas fa-search"></i>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar radicado o asunto...">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <select name="usuario_id" class="form-select-modern" onchange="this.form.submit()">
                                <option value="">Todos los Responsables</option>
                                @foreach($todosLosUsuarios as $u)
                                    <option value="{{ $u->id }}" {{ request('usuario_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-dark-modern flex-grow-1">Filtrar</button>
                            <button type="button" class="btn btn-light-modern" onclick="resetFilters()"><i class="fas fa-sync-alt"></i></button>
                        </div>
                    </form>
                </div>

                <div class="list-container-modern">
                    @forelse($correspondencias as $c)
                        @php
                            $fechaLimite = $c->trd ? \Carbon\Carbon::parse($c->fecha_solicitud)->addDays($c->trd->tiempo_gestion) : null;
                            $isVencido = $fechaLimite && $fechaLimite->isPast() && !$c->finalizado;
                        @endphp
                        <div class="list-item-modern {{ $isVencido ? 'item-vencido' : '' }}">
                            <div class="item-main">
                                <div class="item-id">#{{ $c->id_radicado }}</div>
                                <div class="item-info">
                                    <a href="{{ route('correspondencia.correspondencias.show', $c) }}" class="item-title">{{ $c->asunto }}</a>
                                    <div class="item-details">
                                        <span><i class="far fa-user me-1"></i> {{ $c->usuario->name ?? 'Sin asignar' }}</span>
                                        <span class="mx-2">•</span>
                                        <span><i class="far fa-calendar-alt me-1"></i> Recibido: {{ \Carbon\Carbon::parse($c->fecha_solicitud)->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="item-status">
                                <span class="badge-modern">{{ $c->estado->nombre ?? 'N/A' }}</span>
                            </div>

                            <div class="item-action">
                                <a href="{{ route('correspondencia.correspondencias.edit', $c) }}" class="btn-circle-action"><i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state-modern py-5 text-center">
                            <i class="fas fa-search mb-3 opacity-20" style="font-size: 4rem;"></i>
                            <p class="text-muted">No se encontraron resultados para los filtros aplicados.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $correspondencias->appends(request()->query())->links() }}
                </div>
            </div>
        </main>
    </div>

    {{-- ESTILOS PERSONALIZADOS --}}
    <style>
        :root {
            --brand-color: #4f46e5;
            --brand-light: #eef2ff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg-body: #f8fafc;
            --radius-md: 16px;
        }

        body { background-color: var(--bg-body); font-family: 'Inter', sans-serif; color: var(--text-main); }
        .fw-extrabold { font-weight: 800; }

        .main-header-modern { background: white; padding: 2rem 0; border-bottom: 1px solid #e2e8f0; }
        .custom-breadcrumbs { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); font-weight: 600; }
        .custom-breadcrumbs .active { color: var(--brand-color); }
        .btn-indigo-modern { background: var(--brand-color); color: white; padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 600; text-decoration: none; transition: 0.3s; }
        .btn-indigo-modern:hover { background: #4338ca; transform: translateY(-2px); color: white; }

        .tabs-modern-container { display: flex; gap: 1rem; padding: 0 1.5rem; }
        .tab-modern-btn { background: white; border: 1px solid #e2e8f0; padding: 1rem 1.5rem; border-radius: var(--radius-md); display: flex; align-items: center; gap: 12px; transition: 0.3s; cursor: pointer; color: var(--text-muted); font-weight: 600; border-bottom: 3px solid transparent; }
        .tab-modern-btn.active { border-color: var(--brand-color); color: var(--brand-color); box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.1); }
        .tab-icon-box { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background: #f1f5f9; border-radius: 10px; transition: 0.3s; }
        .tab-modern-btn.active .tab-icon-box { background: var(--brand-light); color: var(--brand-color); }
        .tab-badge-modern { background: var(--brand-color); color: white; font-size: 0.7rem; padding: 2px 8px; border-radius: 20px; }

        .kpi-grid-modern { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-top: 1.5rem; }
        .kpi-card-modern { background: white; padding: 1.5rem; border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center; border: 1px solid #e2e8f0; cursor: pointer; transition: 0.3s; }
        .kpi-card-modern:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .kpi-card-modern.total { background: var(--brand-color); border: none; }
        .kpi-label-modern { font-size: 0.85rem; font-weight: 600; color: var(--text-muted); display: block; }
        .kpi-number-modern { font-size: 1.8rem; font-weight: 800; display: block; }
        .kpi-icon-modern { font-size: 1.5rem; color: var(--brand-color); }
        .soft-bg { background: var(--brand-light); width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }

        .charts-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; margin-top: 1.5rem; }
        .chart-container-modern { background: white; padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid #e2e8f0; }
        .chart-header-modern h5 { font-weight: 700; font-size: 1rem; margin-bottom: 1.5rem; }
        .chart-canvas-wrapper { height: 280px; }

        .toolbar-modern { background: white; padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid #e2e8f0; }
        .search-input-modern { position: relative; }
        .search-input-modern i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }
        .search-input-modern input { width: 100%; border: 1px solid #e2e8f0; padding: 0.6rem 1rem 0.6rem 2.5rem; border-radius: 10px; outline: none; transition: 0.3s; }
        .form-select-modern { border: 1px solid #e2e8f0; padding: 0.6rem 1rem; border-radius: 10px; width: 100%; outline: none; background-color: #fff; }

        .list-container-modern { display: flex; flex-direction: column; gap: 0.75rem; }
        .list-item-modern { background: white; border-radius: var(--radius-md); padding: 1.25rem; display: grid; grid-template-columns: 1fr auto 180px 50px; align-items: center; border: 1px solid #e2e8f0; transition: 0.3s; }
        .list-item-modern:hover { transform: scale(1.005); border-color: var(--brand-color); }
        .item-vencido { border-left: 5px solid #ef4444 !important; }
        .item-id { font-weight: 800; color: var(--brand-color); background: var(--brand-light); padding: 4px 10px; border-radius: 8px; font-size: 0.85rem; margin-right: 15px; }
        .item-main { display: flex; align-items: center; }
        .item-title { font-weight: 700; text-decoration: none; color: var(--text-main); font-size: 1rem; }
        .badge-modern { background: #f1f5f9; color: var(--text-main); font-weight: 700; padding: 6px 12px; border-radius: 30px; font-size: 0.75rem; }
        .btn-circle-action { width: 40px; height: 40px; background: #f8fafc; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--text-muted); text-decoration: none; }
        .btn-circle-action:hover { background: var(--brand-color); color: white; }

        .tab-content-modern { display: none; }
        .tab-content-modern.active { display: block; animation: fadeInUp 0.4s ease forwards; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    {{-- SCRIPTS --}}
    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content-modern').forEach(c => c.classList.remove('active'));
            document.querySelectorAll('.tab-modern-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('view-' + tabId).classList.add('active');
            
            // Buscar el botón que disparó el evento para activar su clase
            const targetBtn = Array.from(document.querySelectorAll('.tab-modern-btn')).find(b => b.getAttribute('onclick').includes(tabId));
            if(targetBtn) targetBtn.classList.add('active');
        }

        function applyFilter(key, value) {
            const input = document.getElementById('input-' + key.replace('_id', ''));
            if(input) {
                input.value = value;
                document.getElementById('filter-form').submit();
            }
        }

        function resetFilters() { 
            window.location.href = "{{ route('correspondencia.tablero') }}"; 
        }

        // Inicialización de Gráficos
        document.addEventListener('DOMContentLoaded', function() {
            const ctxDist = document.getElementById('chartDistribution').getContext('2d');
            new Chart(ctxDist, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartDistribucion['labels']) !!},
                    datasets: [{
                        data: {!! json_encode($chartDistribucion['data']) !!},
                        backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                        borderWidth: 0
                    }]
                },
                options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
            });

            const ctxWork = document.getElementById('chartWorkload').getContext('2d');
            new Chart(ctxWork, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartCarga['labels']) !!},
                    datasets: [{
                        label: 'Radicados Pendientes',
                        data: {!! json_encode($chartCarga['data']) !!},
                        backgroundColor: '#4f46e5',
                        borderRadius: 8
                    }]
                },
                options: { 
                    maintainAspectRatio: false, 
                    scales: { y: { beginAtZero: true, grid: { display: false } }, x: { grid: { display: false } } }
                }
            });
        });
    </script>

</x-base-layout>