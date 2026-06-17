<x-base-layout>
    {{-- Librerías --}}
    @section('titlepage', 'Tablero de Control')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    {{-- Librería para Gráficos --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <div class="ux-dashboard-wrapper container-fluid px-xl-5 py-4">
        {{-- Header Principal --}}
        <header class="dashboard-header d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>               
                <h2 class="fw-bold tracking-tight text-slate-900 mb-1">Centro de Gestión Documental</h2>
                <p class="text-muted mb-0 small">Supervisa, filtra y gestiona los radicados del sistema.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-white border shadow-sm btn-ux d-flex align-items-center" onclick="window.print()">
                    <i class="fas fa-download me-2 text-slate-500"></i> Reporte
                </button>
                <a href="{{ route('correspondencia.correspondencias.create') }}" class="btn btn-primary shadow-primary btn-ux d-flex align-items-center">
                    <i class="fas fa-plus-circle me-2"></i> Nuevo Radicado
                </a>
            </div>
        </header>

        {{-- Panel de KPIs (AHORA SON CLICKABLES) --}}
        <div class="row g-4 mb-4">
            @php $kpiSchema = [
                ['label' => 'Total Radicados', 'key' => 'total', 'icon' => 'fa-layer-group', 'color' => 'slate'],
                ['label' => 'Vencidos', 'key' => 'vencidos', 'icon' => 'fa-clock', 'color' => 'danger'],
                ['label' => 'En Proceso', 'key' => 'pendientes', 'icon' => 'fa-spinner', 'color' => 'primary'],
                ['label' => 'Completados', 'key' => 'finalizados', 'icon' => 'fa-check-double', 'color' => 'success']
            ]; @endphp

            @foreach($kpiSchema as $item)
            @php 
                // Lógica para resaltar el KPI activo
                $isActive = request('estado_kpi') == $item['key'] || (request('estado_kpi') == '' && $item['key'] == 'total' && !request('mes_filtro')); 
            @endphp
            <div class="col-sm-6 col-xl-3">
                <div class="kpi-card h-100 p-4 border-0 shadow-sm bg-white rounded-4 transition-hover d-flex flex-column justify-content-between {{ $isActive ? 'ring-active' : '' }}" 
                     style="cursor: pointer;" 
                     onclick="filterByDashboard('estado_kpi', '{{ $item['key'] === 'total' ? '' : $item['key'] }}')">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="kpi-icon bg-{{ $item['color'] }}-soft text-{{ $item['color'] }}">
                            <i class="fas {{ $item['icon'] }}"></i>
                        </div>
                        @if($isActive)
                            <span class="badge rounded-pill bg-primary text-white px-2 py-1 small fw-bold shadow-sm">
                                <i class="fas fa-check me-1"></i> Filtrando
                            </span>
                        @else
                            <span class="badge rounded-pill bg-{{ $item['color'] }}-soft text-{{ $item['color'] }} border border-{{ $item['color'] }}-subtle px-2 py-1 small fw-bold">
                                +{{ rand(1,5) }} hoy
                            </span>
                        @endif
                    </div>
                    <div>
                        <h2 class="fw-extrabold mb-1 text-slate-900">{{ $kpis[$item['key']] }}</h2>
                        <p class="text-muted small fw-semibold mb-0 uppercase tracking-wider">{{ $item['label'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- SECCIÓN: Gráficos de Análisis --}}
        <div class="row g-4 mb-5">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4" style="cursor: pointer;" title="Haz clic en un punto para filtrar por mes">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="fw-bold text-slate-900 mb-0">Evolución de Radicados</h6>
                            <small class="text-muted">Filtra tocando un mes específico</small>
                        </div>
                        @if(request('mes_filtro'))
                            <span class="badge bg-primary text-white border transition-hover shadow-sm" onclick="event.stopPropagation(); filterByDashboard('mes_filtro', '')" style="cursor:pointer">
                                Mes: {{ request('mes_filtro') }} <i class="fas fa-times ms-1"></i>
                            </span>
                        @else
                            <span class="badge bg-slate-100 text-slate-600 border"><i class="fas fa-chart-area me-1"></i> Tendencia</span>
                        @endif
                    </div>
                    <div id="chart-evolucion" style="min-height: 280px;"></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4" style="cursor: pointer;" title="Haz clic en una sección para filtrar">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="fw-bold text-slate-900 mb-0">Estado Actual</h6>
                            <small class="text-muted">Filtra tocando una sección</small>
                        </div>
                    </div>
                    <div id="chart-estados" class="d-flex justify-content-center align-items-center" style="min-height: 280px;"></div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Columna de Filtros Lateral (Sidebar Interno) --}}
            <aside class="col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 24px;">
                    <div class="card-body p-4">
                        <form action="{{ route('correspondencia.tablero') }}" method="GET" id="filter-form">
                            
                            {{-- CAMPOS OCULTOS PARA INTERACTIVIDAD DE GRÁFICOS Y KPIs --}}
                            <input type="hidden" name="estado_kpi" id="hidden-estado_kpi" value="{{ request('estado_kpi') }}">
                            <input type="hidden" name="mes_filtro" id="hidden-mes_filtro" value="{{ request('mes_filtro') }}">

                            {{-- Búsqueda --}}
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3 text-slate-800 text-uppercase tracking-wider small">Búsqueda rápida</h6>
                                <div class="input-group input-group-merge shadow-none border rounded-3 overflow-hidden focus-ring-wrapper">
                                    <span class="input-group-text border-0 bg-transparent pe-1"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" name="search" class="form-control border-0 bg-transparent shadow-none" placeholder="Radicado o asunto..." value="{{ request('search') }}">
                                </div>
                            </div>

                            <hr class="text-slate-200 my-4">

                            {{-- Responsable --}}
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3 text-slate-800 text-uppercase tracking-wider small">Responsable</h6>
                                <div class="ux-select-container">
                                    <select name="usuario_id" id="select-responsable" class="tom-select-custom" onchange="this.form.submit()">
                                        <option value="">Buscar responsable...</option>
                                        @foreach($usuarios as $u)
                                            <option value="{{ $u->id }}" 
                                                data-avatar="https://ui-avatars.com/api/?name={{ urlencode($u->name) }}&background=random"
                                                {{ request('usuario_id') == $u->id ? 'selected' : '' }}>
                                                {{ $u->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <hr class="text-slate-200 my-4">

                            {{-- Categorías --}}
                            <div class="mb-2">
                                <h6 class="fw-bold mb-3 text-slate-800 text-uppercase tracking-wider small">Flujos / Categorías</h6>
                                <div class="category-list d-flex flex-column gap-2">
                                    <label class="category-item {{ !request('flujo_id') ? 'active' : '' }}">
                                        <input type="radio" name="flujo_id" value="" {{ !request('flujo_id') ? 'checked' : '' }} onchange="this.form.submit()">
                                        <i class="fas fa-inbox me-2 opacity-50"></i>
                                        <span class="flex-grow-1">Todas</span>
                                        <span class="badge {{ !request('flujo_id') ? 'bg-primary text-white' : 'bg-slate-100 text-slate-600' }} rounded-pill">{{ $kpis['total'] }}</span>
                                    </label>
                                    @foreach($flujos as $f)
                                    <label class="category-item {{ request('flujo_id') == $f->id ? 'active' : '' }}">
                                        <input type="radio" name="flujo_id" value="{{ $f->id }}" {{ request('flujo_id') == $f->id ? 'checked' : '' }} onchange="this.form.submit()">
                                        <i class="fas fa-folder me-2 opacity-50"></i>
                                        <span class="flex-grow-1 text-truncate" title="{{ $f->nombre }}">{{ $f->nombre }}</span>
                                        <span class="badge {{ request('flujo_id') == $f->id ? 'bg-primary text-white' : 'bg-slate-100 text-slate-600' }} rounded-pill">{{ $f->correspondencias_count }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- Listado Principal --}}
            <main class="col-lg-9">
                <div class="d-flex align-items-center justify-content-between mb-4 bg-white p-3 rounded-4 shadow-sm border-0">
                    <span class="text-slate-500 small fw-semibold">
                        Mostrando <strong class="text-slate-900">{{ $correspondencias->count() }}</strong> de <strong class="text-slate-900">{{ $correspondencias->total() }}</strong> radicados filtrados
                        
                        {{-- Indicador visual de filtros activos desde gráficos --}}
                        @if(request('estado_kpi') || request('mes_filtro'))
                            <span class="mx-2 text-slate-300">|</span>
                            <a href="{{ route('correspondencia.tablero') }}" class="text-danger small fw-bold text-decoration-none">
                                <i class="fas fa-times-circle"></i> Limpiar gráficos
                            </a>
                        @endif
                    </span>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-white border dropdown-toggle fw-bold text-slate-700" data-bs-toggle="dropdown">
                            <i class="fas fa-sort-amount-down me-1 text-muted"></i> Ordenar por
                        </button>
                        <ul class="dropdown-menu shadow border-0 rounded-3">
                            <li><a class="dropdown-item py-2 small fw-medium" href="#">Más recientes</a></li>
                            <li><a class="dropdown-item py-2 small fw-medium" href="#">Vencimiento próximo</a></li>
                        </ul>
                    </div>
                </div>

                <div class="correspondencia-list">
                    @forelse($correspondencias as $c)
                        @php
                            $fechaLimite = $c->trd ? \Carbon\Carbon::parse($c->fecha_solicitud)->addDays($c->trd->tiempo_gestion) : null;
                            $isVencido = $fechaLimite && $fechaLimite->isPast() && !$c->finalizado;
                            $statusColor = $isVencido ? 'danger' : ($c->finalizado ? 'success' : 'primary');
                        @endphp
                        {{-- La tarjeta mantiene el hover visual, ya no tiene onclick en la card entera para evitar conflictos --}}
                        <div class="document-card mb-3 transition-hover">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative h-100">
                                <div class="position-absolute top-0 bottom-0 start-0 bg-{{ $statusColor }}" style="width: 5px;"></div>
                                
                                <div class="card-body p-4 ps-5">
                                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="badge bg-{{ $statusColor }}-soft text-{{ $statusColor }} fw-bold font-mono px-2 py-1">#{{ $c->id_radicado }}</span>
                                                <span class="badge bg-slate-100 text-slate-600 border border-slate-200">{{ $c->flujo->nombre ?? 'General' }}</span>
                                            </div>
                                            <h5 class="card-title text-slate-900 fw-bold mb-1">{{ $c->asunto }}</h5>
                                            <p class="text-muted small mb-0 d-flex align-items-center">
                                                <i class="far fa-calendar-alt me-1"></i> Solicitado el {{ $c->fecha_solicitud->format('d M, Y') }}
                                            </p>
                                        </div>

                                        <div class="d-flex flex-md-column justify-content-between align-items-end text-end">
                                            @if($isVencido) 
                                                <span class="badge bg-danger-soft text-danger border border-danger-subtle d-flex align-items-center px-2 py-1 pulse-danger">
                                                    <i class="fas fa-exclamation-circle me-1"></i> Vencido
                                                </span> 
                                            @elseif($c->finalizado)
                                                <span class="badge bg-success-soft text-success border border-success-subtle d-flex align-items-center px-2 py-1">
                                                    <i class="fas fa-check-circle me-1"></i> Finalizado
                                                </span>
                                            @else
                                                <span class="badge bg-primary-soft text-primary border border-primary-subtle d-flex align-items-center px-2 py-1">
                                                    <i class="fas fa-spinner fa-spin me-1"></i> En Proceso
                                                </span>
                                            @endif

                                            <div class="d-flex align-items-center gap-3 mt-3">
                                                <div class="status-pill text-end">
                                                    <span class="small fw-bold text-slate-700 d-block">{{ $c->estado->nombre ?? 'Sin estado' }}</span>
                                                </div>
                                                <div class="user-pill border" title="{{ $c->usuario->name ?? 'Sin asignar' }}">
                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($c->usuario->name ?? 'U') }}&background=random" class="rounded-circle" width="32">
                                                </div>
                                                
                                                {{-- BOTÓN: Abrir Show en nueva pestaña --}}
                                                <div class="ms-2 ps-2 border-start">
                                                    <a href="{{ route('correspondencia.correspondencias.show', $c->id ?? $c->id_radicado) }}" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-white border shadow-sm fw-bold px-3 py-1 text-decoration-none">
                                                        Abrir <i class="fas fa-external-link-alt text-primary ms-1" style="font-size: 0.8rem;"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 bg-white rounded-4 shadow-sm border-0 mt-3">
                            <img src="https://illustrations.popsy.co/slate/empty-folder.svg" width="220" class="mb-4 opacity-75">
                            <h4 class="fw-bolder text-slate-800">No encontramos resultados</h4>
                            <p class="text-slate-500 mb-4">No hay radicados que coincidan con los filtros aplicados actualmente.</p>
                            <a href="{{ route('correspondencia.tablero') }}" class="btn btn-slate px-4 py-2 fw-bold text-decoration-none">
                                <i class="fas fa-eraser me-2"></i> Limpiar todos los filtros
                            </a>
                        </div>
                    @endforelse
                </div>
                
                <div class="mt-4 d-flex justify-content-center custom-pagination">
                    {{ $correspondencias->links() }}
                </div>
            </main>
        </div>
    </div>

    {{-- Estilos UX --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Fira+Code:wght@500&display=swap');

        :root {
            --slate-50: #f8fafc; --slate-100: #f1f5f9; --slate-200: #e2e8f0; --slate-300: #cbd5e1;
            --slate-500: #64748b; --slate-600: #475569; --slate-700: #334155;
            --slate-800: #1e293b; --slate-900: #0f172a;
            --primary: #4f46e5; --primary-soft: #eef2ff; --primary-subtle: #c7d2fe;
            --danger: #ef4444; --danger-soft: #fef2f2; --danger-subtle: #fecaca;
            --success: #10b981; --success-soft: #ecfdf5; --success-subtle: #a7f3d0;
        }

        body { 
            background-color: #f4f7fa; 
            color: var(--slate-800); 
            font-family: 'Inter', sans-serif; 
        }

        /* Botones */
        .btn-ux { border-radius: 10px; font-weight: 600; padding: 10px 20px; transition: all 0.2s ease-in-out; }
        .btn-white { background: white; color: var(--slate-700); }
        .btn-white:hover { background: var(--slate-50); border-color: var(--slate-200); transform: translateY(-1px); }
        .btn-primary:hover { background-color: var(--slate-900); border-color: var(--slate-900); transform: translateY(-1px); box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.3); }
        .btn-slate { background-color: var(--slate-100); color: var(--slate-700); border-radius: 10px; transition: all 0.2s; }
        .btn-slate:hover { background-color: var(--slate-200); color: var(--slate-900); }

        /* KPI Cards & Charts */
        .kpi-card, .card { border: 1px solid rgba(0,0,0,0.03) !important; }
        .kpi-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; }
        .bg-primary-soft { background-color: var(--primary-soft); }
        .bg-danger-soft { background-color: var(--danger-soft); }
        .bg-success-soft { background-color: var(--success-soft); }
        .bg-slate-soft { background-color: var(--slate-100); }
        
        /* Efecto de anillo para KPI seleccionado */
        .ring-active { box-shadow: 0 0 0 2px var(--primary), 0 10px 15px -3px rgba(79, 70, 229, 0.1) !important; transform: translateY(-3px); }

        /* Document Cards */
        .document-card { border-radius: 16px; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); display: block;}
        .document-card:hover { transform: translateY(-2px); }
        .document-card:hover .card { box-shadow: 0 15px 30px -5px rgba(0,0,0,0.06) !important; }
        
        .user-pill { padding: 2px; border-radius: 50%; display: inline-flex; align-items: center; background: white;}
        .user-pill img { object-fit: cover; border: 2px solid white; }

        /* Category Filters */
        .category-item { 
            display: flex; align-items: center; padding: 10px 16px; border-radius: 10px; cursor: pointer; 
            transition: all 0.2s ease; border: 1px solid transparent; font-size: 0.9rem; font-weight: 500;
            color: var(--slate-600);
        }
        .category-item:hover:not(.active) { background: var(--slate-50); color: var(--slate-900); }
        .category-item.active { background: var(--primary-soft); border-color: var(--primary-subtle); color: var(--primary); font-weight: 600; }
        .category-item input { display: none; }

        /* Input y Búsqueda */
        .focus-ring-wrapper:focus-within { border-color: var(--primary) !important; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        .form-control:focus { background-color: transparent; }

        /* TomSelect Personalizado */
        .ux-select-container { max-width: 100%; position: relative; }
        .ts-wrapper.tom-select-custom .ts-control {
            border: 1px solid var(--slate-200) !important; border-radius: 10px !important; padding: 10px 14px !important;
            background-color: transparent !important; font-size: 0.9rem !important; box-shadow: none; transition: all 0.2s;
        }
        .ts-wrapper.tom-select-custom.focus .ts-control { border-color: var(--primary) !important; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1) !important; }
        .ts-dropdown { border-radius: 10px !important; border: 1px solid var(--slate-200) !important; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1) !important; overflow: hidden; padding: 5px 0;}
        .ts-dropdown .option, .ts-control .item { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; }
        .ts-dropdown .option.active { background-color: var(--primary-soft) !important; color: var(--primary) !important;}
        .user-option { display: flex; align-items: center; gap: 12px; }
        .user-option img { width: 24px; height: 24px; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }

        /* Utilidades */
        .shadow-primary { box-shadow: 0 8px 15px -3px rgba(79, 70, 229, 0.25); }
        .font-mono { font-family: 'Fira Code', monospace; font-size: 0.85rem;}
        .uppercase { text-transform: uppercase; }
        .tracking-wider { letter-spacing: 0.05em; }
        
        /* ApexCharts ajustes */
        .apexcharts-tooltip { border-radius: 8px !important; border: none !important; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1) !important; }
        .apexcharts-legend-text { font-family: 'Inter', sans-serif !important; color: var(--slate-600) !important; cursor: pointer; }
        .apexcharts-pie-slice, .apexcharts-marker { cursor: pointer; transition: 0.2s; }
        .apexcharts-pie-slice:hover { filter: brightness(0.95); }

        @keyframes pulse-soft {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
            70% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        .pulse-danger { animation: pulse-soft 2s infinite; }
        .custom-pagination nav .pagination { margin-bottom: 0; gap: 5px;}
        .custom-pagination .page-item .page-link { border-radius: 8px; border: 1px solid var(--slate-200); color: var(--slate-700); margin: 0 2px; }
        .custom-pagination .page-item.active .page-link { background-color: var(--primary); border-color: var(--primary); color: white; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); }
    </style>

    {{-- Scripts Funcionales --}}
    <script>
        // Función maestra para inyectar filtros y enviar el formulario
        function filterByDashboard(inputName, value) {
            // Si el valor ya está activo, lo quitamos (toggle)
            const currentVal = document.getElementById('hidden-' + inputName).value;
            document.getElementById('hidden-' + inputName).value = (currentVal === value) ? '' : value;
            document.getElementById('filter-form').submit();
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Inicialización de TomSelect (Responsable)
            const selectElement = document.getElementById('select-responsable');
            if (selectElement) {
                new TomSelect(selectElement, {
                    create: false,
                    sortField: { field: "text", order: "asc" },
                    placeholder: "Buscar por nombre...",
                    render: {
                        option: function(data, escape) {
                            return `<div class="user-option py-2 px-3"><img src="${data.avatar}" alt=""><span class="fw-medium">${escape(data.text)}</span></div>`;
                        },
                        item: function(data, escape) {
                            return `<div class="user-option"><img src="${data.avatar}" alt=""><span class="fw-medium">${escape(data.text)}</span></div>`;
                        }
                    },
                    onDropdownOpen: function() { this.wrapper.classList.add('focus'); },
                    onDropdownClose: function() { this.wrapper.classList.remove('focus'); }
                });
            }

            // GRÁFICOS APEXCHARTS CON INTERACTIVIDAD

            // 1. Gráfico de Área (Evolución)
            const mesesEvolucion = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul'];
            
            var optionsEvolucion = {
                series: [{
                    name: 'Radicados Nuevos',
                    data: [31, 40, 28, 51, 42, 109, 100] 
                }],
                chart: {
                    height: 280,
                    type: 'area',
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    events: {
                        markerClick: function(event, chartContext, { seriesIndex, dataPointIndex, config }) {
                            const mesSeleccionado = mesesEvolucion[dataPointIndex];
                            filterByDashboard('mes_filtro', mesSeleccionado);
                        }
                    }
                },
                colors: ['#4f46e5'], 
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                xaxis: {
                    categories: mesesEvolucion, 
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { style: { colors: '#64748b' } }
                },
                yaxis: {
                    labels: { style: { colors: '#64748b' } }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4,
                },
                markers: {
                    size: 5,
                    colors: ["#fff"],
                    strokeColors: "#4f46e5",
                    strokeWidth: 2,
                    hover: { size: 7 }
                }
            };

            var chartEvolucion = new ApexCharts(document.querySelector("#chart-evolucion"), optionsEvolucion);
            chartEvolucion.render();


            // 2. Gráfico de Donas (Estado) 
            const kpiFinalizados = {{ $kpis['finalizados'] ?? 0 }};
            const kpiPendientes  = {{ $kpis['pendientes'] ?? 0 }};
            const kpiVencidos    = {{ $kpis['vencidos'] ?? 0 }};
            const mapEstadosKeys = ['finalizados', 'pendientes', 'vencidos'];

            var optionsEstados = {
                series: [kpiFinalizados, kpiPendientes, kpiVencidos], 
                chart: {
                    type: 'donut',
                    height: 280,
                    fontFamily: 'Inter, sans-serif',
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            const estadoSeleccionado = mapEstadosKeys[config.dataPointIndex];
                            filterByDashboard('estado_kpi', estadoSeleccionado);
                        }
                    }
                },
                labels: ['Completados', 'En Proceso', 'Vencidos'],
                colors: ['#10b981', '#4f46e5', '#ef4444'], 
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%',
                            labels: {
                                show: true,
                                name: { fontSize: '0.85rem', color: '#64748b' },
                                value: { fontSize: '1.5rem', fontWeight: 700, color: '#1e293b' },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    color: '#64748b',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                    }
                                }
                            }
                        },
                        expandOnClick: true
                    }
                },
                dataLabels: { enabled: false },
                stroke: { width: 2, colors: ['#fff'] },
                legend: {
                    position: 'bottom',
                    markers: { radius: 12 },
                    itemMargin: { horizontal: 10, vertical: 5 }
                }
            };

            var chartEstados = new ApexCharts(document.querySelector("#chart-estados"), optionsEstados);
            chartEstados.render();
        });
    </script>
</x-base-layout>