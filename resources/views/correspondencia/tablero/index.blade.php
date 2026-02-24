<x-base-layout>
    {{-- Librerías --}}
    @section('titlepage', 'Tablero de Control')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <div class="ux-dashboard-wrapper container-fluid py-4">
        {{-- Header Principal --}}
        <header class="dashboard-header d-flex justify-content-between align-items-end mb-4">
            <div>               
                <h2 class="fw-bold tracking-tight text-slate-900 mb-0">Centro de Gestión Documental</h2>
            </div>
            <div class="d-flex">
                <button class="btn btn-white border shadow-sm me-2 btn-ux" onclick="window.print()">
                    <i class="fas fa-download me-2 text-muted"></i> Reporte
                </button>
                <a href="{{ route('correspondencia.correspondencias.create') }}" class="btn btn-primary px-4 shadow-primary btn-ux">
                    <i class="fas fa-plus-circle me-2"></i> Nuevo Radicado
                </a>
            </div>
        </header>

        {{-- Panel de KPIs --}}
        <div class="row g-3 mb-4">
            @php $kpiSchema = [
                ['label' => 'Total', 'key' => 'total', 'icon' => 'fa-layer-group', 'color' => 'slate'],
                ['label' => 'Vencidos', 'key' => 'vencidos', 'icon' => 'fa-clock', 'color' => 'danger'],
                ['label' => 'En Proceso', 'key' => 'pendientes', 'icon' => 'fa-spinner', 'color' => 'primary'],
                ['label' => 'Completados', 'key' => 'finalizados', 'icon' => 'fa-check-double', 'color' => 'success']
            ]; @endphp

            @foreach($kpiSchema as $item)
            <div class="col-md-3">
                <div class="kpi-card h-100 p-3 border-0 shadow-sm bg-white rounded-4 transition-hover">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="kpi-icon bg-{{ $item['color'] }}-soft text-{{ $item['color'] }}">
                            <i class="fas {{ $item['icon'] }}"></i>
                        </div>
                        <span class="badge rounded-pill bg-{{ $item['color'] }}-soft text-{{ $item['color'] }} small">+{{ rand(1,5) }} hoy</span>
                    </div>
                    <h3 class="fw-extrabold mb-0 text-slate-800">{{ $kpis[$item['key']] }}</h3>
                    <p class="text-muted small fw-medium mb-0 uppercase tracking-wider">{{ $item['label'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row">
            {{-- Columna de Filtros Lateral (Sidebar Interno) --}}
            <aside class="col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 20px;">
                    <div class="card-body p-4">
                        <form action="{{ route('correspondencia.tablero') }}" method="GET" id="filter-form">
                            <h6 class="fw-bold mb-3 text-slate-900">Búsqueda avanzada</h6>
                            <div class="mb-4">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text border-end-0 bg-light"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Radicado o asunto..." value="{{ request('search') }}">
                                </div>
                            </div>

                            <h6 class="fw-bold mb-3 text-slate-900"><i class="far fa-user-circle me-2 text-primary"></i>Responsable</h6>
                            <div class="mb-4">
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

                            <h6 class="fw-bold mb-3 text-slate-900">Categorías</h6>
                            <div class="category-list d-flex flex-column gap-2">
                                <label class="category-item {{ !request('flujo_id') ? 'active' : '' }}">
                                    <input type="radio" name="flujo_id" value="" {{ !request('flujo_id') ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span>Todas</span>
                                    <span class="ms-auto badge bg-light text-dark border">{{ $kpis['total'] }}</span>
                                </label>
                                @foreach($flujos as $f)
                                <label class="category-item {{ request('flujo_id') == $f->id ? 'active' : '' }}">
                                    <input type="radio" name="flujo_id" value="{{ $f->id }}" {{ request('flujo_id') == $f->id ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span class="text-truncate" style="max-width: 130px;">{{ $f->nombre }}</span>
                                    <span class="ms-auto badge bg-light text-dark border small">{{ $f->correspondencias_count }}</span>
                                </label>
                                @endforeach
                            </div>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- Listado Principal --}}
            <main class="col-lg-9">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted small fw-medium">Mostrando {{ $correspondencias->count() }} de {{ $kpis['total'] }} radicados</span>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-white border dropdown-toggle fw-bold" data-bs-toggle="dropdown">Ordenar por</button>
                        <ul class="dropdown-menu shadow-sm border-0">
                            <li><a class="dropdown-item small" href="#">Más recientes</a></li>
                            <li><a class="dropdown-item small" href="#">Vencimiento próximo</a></li>
                        </ul>
                    </div>
                </div>

                <div class="correspondencia-list">
                    @forelse($correspondencias as $c)
                        @php
                            $fechaLimite = $c->trd ? \Carbon\Carbon::parse($c->fecha_solicitud)->addDays($c->trd->tiempo_gestion) : null;
                            $isVencido = $fechaLimite && $fechaLimite->isPast() && !$c->finalizado;
                        @endphp
                        <div class="document-card mb-3 transition-hover" onclick="showDetail('{{ $c->id_radicado }}')">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-body p-0">
                                    <div class="d-flex align-items-stretch">
                                        <div class="status-indicator {{ $isVencido ? 'bg-danger' : ($c->finalizado ? 'bg-success' : 'bg-primary') }}"></div>
                                        <div class="p-3 flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <span class="text-primary fw-bold font-mono">#{{ $c->id_radicado }}</span>
                                                    <span class="text-muted mx-2">•</span>
                                                    <span class="badge bg-light text-slate-600 border">{{ $c->flujo->nombre ?? 'General' }}</span>
                                                </div>
                                                <div class="text-end">
                                                    <small class="d-block text-muted fw-bold">{{ $c->fecha_solicitud->format('d M, Y') }}</small>
                                                    @if($isVencido) <span class="badge bg-danger-soft text-danger small">⚠️ Vencido</span> @endif
                                                </div>
                                            </div>
                                            <h5 class="card-title text-slate-900 fw-bold mb-2">{{ $c->asunto }}</h5>
                                            <div class="d-flex align-items-center gap-3 mt-3">
                                                <div class="user-pill">
                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($c->usuario->name ?? 'U') }}&background=random" class="rounded-circle me-2" width="22">
                                                    <span class="small fw-medium">{{ explode(' ', $c->usuario->name ?? 'Sin asignar')[0] }}</span>
                                                </div>
                                                <div class="status-pill ms-auto">
                                                    <i class="fas fa-circle scale-75 me-1 {{ $c->finalizado ? 'text-success' : 'text-primary' }}"></i>
                                                    <span class="small fw-bold">{{ $c->estado->nombre ?? 'Sin estado' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                            <img src="https://illustrations.popsy.co/slate/empty-folder.svg" width="180" class="mb-3">
                            <h5 class="fw-bold">No encontramos resultados</h5>
                            <p class="text-muted">Prueba limpiando los filtros o buscando otro término.</p>
                            <a href="{{ route('correspondencia.tablero') }}" class="btn btn-light btn-sm mt-2">Limpiar Filtros</a>
                        </div>
                    @endforelse
                </div>
                
                <div class="mt-4">
                    {{ $correspondencias->links() }}
                </div>
            </main>
        </div>
    </div>

    {{-- Estilos UX --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        :root {
            --slate-50: #f8fafc; --slate-100: #f1f5f9; --slate-800: #1e293b; --slate-900: #0f172a;
            --primary: #4f46e5; --primary-soft: #eef2ff; --primary-dark: #3730a3;
        }

        body { background-color: #f4f7fa; color: var(--slate-800); font-family: 'Inter', sans-serif; }

        .btn-ux { border-radius: 10px; font-weight: 600; padding: 10px 18px; transition: all 0.2s; }
        .btn-white { background: white; }
        .btn-ux:hover{all:unset;}

        /* KPI Cards */
        .kpi-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .bg-primary-soft { background-color: var(--primary-soft); }
        .bg-danger-soft { background-color: #fef2f2; }
        .bg-success-soft { background-color: #f0fdf4; }
        .bg-slate-soft { background-color: #f1f5f9; }

        /* Document Cards */
        .document-card { cursor: pointer; border-radius: 16px; border: 2px solid transparent; transition: all 0.2s; }
        .document-card:hover { transform: translateY(-3px); }
        .status-indicator { width: 6px; }
        .user-pill { background: var(--slate-100); padding: 4px 12px 4px 4px; border-radius: 20px; display: flex; align-items: center; }

        /* Category Filters */
        .category-item { 
            display: flex; align-items: center; padding: 10px 14px; border-radius: 12px; cursor: pointer; 
            transition: all 0.2s; border: 1px solid transparent; 
        }
        /*.category-item:hover { background: var(--slate-50); color: var(--primary); }*/
        .category-item.active { background: var(--primary-soft); border-color: var(--primary); color: var(--primary); font-weight: 600; }
        .category-item input { display: none; }

        /* Contenedor del selector Responsable */
        .ux-select-container { max-width: 100%; position: relative; }

        .ts-wrapper.tom-select-custom .ts-control {
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 10px 12px !important;
            background-color: #f8fafc !important;
            font-size: 0.9rem !important;
        }

        .ts-wrapper.tom-select-custom.focus .ts-control {
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1) !important;
            border-color: var(--primary) !important;
        }

        .ts-dropdown .option, .ts-control .item {
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;
        }

        .user-option { display: flex; align-items: center; gap: 10px; }
        .user-option img { width: 22px; height: 22px; border-radius: 50%; border: 1px solid #fff; box-shadow: 0 0 2px rgba(0,0,0,0.1); }

        .shadow-primary { box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3); }
        .transition-hover:hover { box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08) !important; }
        .font-mono { font-family: 'Fira Code', 'Courier New', monospace; }
        .uppercase { text-transform: uppercase; }
        .tracking-wider { letter-spacing: 0.05em; }
    </style>

    {{-- Script para TomSelect --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectElement = document.getElementById('select-responsable');
            
            if (selectElement) {
                new TomSelect(selectElement, {
                    create: false,
                    sortField: { field: "text", order: "asc" },
                    placeholder: "Buscar por nombre...",
                    render: {
                        option: function(data, escape) {
                            return `<div class="user-option py-1 px-1">
                                <img src="${data.avatar}" alt="">
                                <span class="small">${escape(data.text)}</span>
                            </div>`;
                        },
                        item: function(data, escape) {
                            return `<div class="user-option">
                                <img src="${data.avatar}" alt="">
                                <span class="fw-medium">${escape(data.text)}</span>
                            </div>`;
                        }
                    },
                    onDropdownOpen: function() {
                        this.wrapper.classList.add('focus');
                    },
                    onDropdownClose: function() {
                        this.wrapper.classList.remove('focus');
                    }
                });
            }
        });

        function showDetail(idRadicado) {
            console.log("Visualizando radicado:", idRadicado);
            // Redirigir o abrir modal
        }
    </script>
</x-base-layout>