<x-base-layout>
    {{-- 1. Librerías Externas --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    @php
        // --- LÓGICA DE BACKEND ---
        $hasActiveFilters = request()->filled('search') || request()->filled('page') || request()->filled('estado_id') || request()->filled('usuario_id') || request()->filled('condicion');
        $activeTab = $hasActiveFilters ? 'gestion' : 'dashboard';

        // Eager loading para evitar lentitud
        $query = \App\Models\Correspondencia\Correspondencia::with([
            'trd', 'estado', 'usuario', 'flujo.procesos.usuariosAsignados', 'procesos'
        ]);

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

        // Datos para el Modal y Mapas de JS
        $procesos_disponibles = \App\Models\Correspondencia\Proceso::with(['flujo', 'usuariosAsignados.usuario', 'estadosProcesos.estado'])->get();
        $estadosKpi = \App\Models\Correspondencia\Estado::withCount('correspondencias')->get();
        $total = $estadosKpi->sum('correspondencias_count');
        $todosLosUsuarios = \App\Models\User::orderBy('name')->get();

        $chartDistribucion = [
            'labels' => $estadosKpi->pluck('nombre'),
            'data' => $estadosKpi->pluck('correspondencias_count'),
        ];
        
        $usuariosCargaRaw = \App\Models\User::withCount(['correspondencias' => fn($q) => $q->where('finalizado', false)])
                        ->orderBy('correspondencias_count', 'desc')->take(5)->get();

        $chartCarga = [
            'labels' => $usuariosCargaRaw->pluck('name'),
            'data' => $usuariosCargaRaw->pluck('correspondencias_count'),
        ];
    @endphp

    <div class="app-shell">
        {{-- HEADER --}}
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
                        <p class="text-muted small mb-0">Gestión de flujos y cumplimiento de tiempos.</p>
                    </div>
                    <a href="{{ route('correspondencia.correspondencias.create') }}" class="btn btn-indigo-modern shadow-sm">
                        <i class="fas fa-plus-circle me-2"></i> Nuevo Radicado
                    </a>
                </div>
            </div>
        </header>

        {{-- TABS --}}
        <div class="tabs-modern-container mt-4">
            <button class="tab-modern-btn {{ $activeTab === 'dashboard' ? 'active' : '' }}" onclick="switchTab('dashboard')">
                <div class="tab-icon-box"><i class="fas fa-chart-line"></i></div>
                <span>Indicadores Generales</span>
            </button>
            <button class="tab-modern-btn {{ $activeTab === 'gestion' ? 'active' : '' }}" onclick="switchTab('gestion')">
                <div class="tab-icon-box"><i class="fas fa-stream"></i></div>
                <span>Gestión de Radicados</span>
                @if($total > 0) <span class="tab-badge-modern">{{ $total }}</span> @endif
            </button>
        </div>

        <main class="content-wrapper-modern">
            {{-- VIEW 1: DASHBOARD --}}
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
                        <div class="chart-header-modern"><h5>Distribución por Estado</h5></div>
                        <div class="chart-canvas-wrapper"><canvas id="chartDistribution"></canvas></div>
                    </div>
                    <div class="chart-container-modern shadow-sm">
                        <div class="chart-header-modern"><h5>Carga por Usuario</h5></div>
                        <div class="chart-canvas-wrapper"><canvas id="chartWorkload"></canvas></div>
                    </div>
                </div>
            </div>

            {{-- VIEW 2: GESTIÓN --}}
            <div id="view-gestion" class="tab-content-modern {{ $activeTab === 'gestion' ? 'active' : '' }}">
                <div class="toolbar-modern shadow-sm mb-4">
                    <form action="{{ route('correspondencia.tablero') }}" method="GET" id="filter-form" class="row g-3 align-items-center">
                        <input type="hidden" name="estado_id" id="input-estado" value="{{ request('estado_id') }}">
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
                            
                            $soyResponsable = false;
                            $procesoId = null;
                            if (!$c->finalizado && $c->flujo) {
                                foreach ($c->flujo->procesos->sortBy('id') as $paso) {
                                    foreach ($paso->usuariosAsignados as $asig) {
                                        if (($asig->usuario->id ?? $asig->user_id) == auth()->id()) {
                                            $soyResponsable = true; $procesoId = $paso->id; break 2;
                                        }
                                    }
                                }
                            }
                        @endphp
                        <div class="list-item-modern {{ $isVencido ? 'item-vencido' : '' }} row-clickable" 
                             onclick="abrirModalRuta('{{ $c->id_radicado }}')">
                            
                            <div class="item-main">
                                <div class="item-id">#{{ $c->id_radicado }}</div>
                                <div class="item-info">
                                    <span class="item-title">{{ $c->asunto }}</span>
                                    <div class="item-details">
                                        <span><i class="far fa-user"></i> {{ $c->usuario->name ?? 'N/A' }}</span>
                                        <span class="mx-2">•</span>
                                        <span><i class="far fa-calendar"></i> {{ \Carbon\Carbon::parse($c->fecha_solicitud)->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="item-status">
                                <span class="badge-modern">{{ $c->estado->nombre ?? 'N/A' }}</span>
                            </div>

                            <div class="item-actions d-flex gap-2 justify-content-end" onclick="event.stopPropagation()">
                                @if($soyResponsable)
                                    <button onclick="saltarAGestionDirecta('{{ $c->id_radicado }}', '{{ addslashes($c->asunto) }}', '{{ $procesoId }}')" 
                                            class="btn-action-modern text-success" title="Gestión Rápida">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                @endif
                                <a href="{{ route('correspondencia.correspondencias.show', $c) }}" class="btn-action-modern text-primary"><i class="fas fa-eye"></i></a>
                            </div>
                        </div>
                    @empty
                        <p class="text-center py-5 text-muted">No se encontraron registros.</p>
                    @endforelse
                </div>
                <div class="mt-4">{{ $correspondencias->links() }}</div>
            </div>
        </main>
    </div>

    {{-- MODALES --}}
    <div class="modal fade" id="modalSeguimiento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0" style="border-radius: 20px;">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="fw-bold"><i class="fas fa-bolt text-warning me-2"></i>Gestión Rápida</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formGestionRapida" action="{{ route('correspondencia.correspondencias-procesos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_correspondencia" id="modal_id_correspondencia">
                    <input type="hidden" name="id_proceso" id="input_etapa_proceso_hidden">
                    <div class="modal-body px-4">
                        <div class="bg-light p-3 rounded-4 mb-3">
                            <label class="small fw-bold text-muted mb-1">Etapa de Trabajo (Bloqueada)</label>
                            <select id="select_etapa_proceso_visual" class="form-select border-0 shadow-none" disabled style="background: white; font-weight: 700;">
                                @foreach($procesos_disponibles as $p) <option value="{{ $p->id }}">{{ $p->nombre }}</option> @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold mb-2">Nuevo Estado <span class="text-danger">*</span></label>
                            <div id="container_estados_checkin" class="d-flex flex-wrap gap-2"></div>
                            <input type="hidden" name="estado_id" id="input_estado_id_hidden" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Observación <span class="text-danger">*</span></label>
                            <textarea name="observacion" class="form-control rounded-3" rows="3" required></textarea>
                        </div>
                        <div id="container_archivos_dinamicos" class="mb-3"></div>
                        <div class="d-flex gap-2 mb-3">
                            <label class="flex-fill p-2 border rounded-3 text-center cursor-pointer">
                                <input type="checkbox" name="notificado_email" value="1"> Notificar por Email
                            </label>
                            <label class="flex-fill p-2 border border-danger-subtle bg-danger-subtle text-danger rounded-3 text-center cursor-pointer">
                                <input type="checkbox" name="finalizado" value="1"> Finalizar Etapa
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 px-4">
                        <button type="submit" class="btn btn-indigo-modern w-100">Guardar Gestión</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalRuta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 20px;">
                <div class="modal-header border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold"><i class="fas fa-map-signs me-2 text-primary"></i>Ruta del Proceso</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <div id="timeline_steps_container"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ESTILOS CSS --}}
    <style>
        :root { --brand-color: #4f46e5; --brand-light: #eef2ff; --text-main: #1e293b; --radius-md: 16px; }
        .main-header-modern { background: white; padding: 2rem 0; border-bottom: 1px solid #e2e8f0; }
        .custom-breadcrumbs { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #64748b; font-weight: 600; }
        .btn-indigo-modern { background: var(--brand-color); color: white; padding: 0.7rem 1.4rem; border-radius: 12px; font-weight: 600; text-decoration: none; border:none; transition: 0.2s; }
        .btn-indigo-modern:hover { transform: translateY(-2px); color: white; opacity: 0.9; }
        .tab-modern-btn { background: white; border: 1px solid #e2e8f0; padding: 0.8rem 1.2rem; border-radius: var(--radius-md); display: flex; align-items: center; gap: 10px; cursor: pointer; color: #64748b; font-weight: 600; }
        .tab-modern-btn.active { border-color: var(--brand-color); color: var(--brand-color); }
        .tab-icon-box { width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background: #f1f5f9; border-radius: 8px; }
        .tab-badge-modern { background: var(--brand-color); color: white; font-size: 0.65rem; padding: 1px 6px; border-radius: 10px; }
        .kpi-grid-modern { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
        .kpi-card-modern { background: white; padding: 1.2rem; border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center; border: 1px solid #e2e8f0; transition: 0.2s; cursor: pointer; }
        .kpi-card-modern:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .kpi-card-modern.total { background: var(--brand-color); color: white; border: none; }
        .charts-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem; }
        .chart-container-modern { background: white; padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid #e2e8f0; height: 350px; }
        .list-item-modern { background: white; border-radius: var(--radius-md); padding: 1rem 1.5rem; display: grid; grid-template-columns: 1fr 150px 100px; align-items: center; border: 1px solid #e2e8f0; margin-bottom: 0.75rem; transition: 0.2s; }
        .row-clickable { cursor: pointer; }
        .row-clickable:hover { border-color: var(--brand-color); background: #fcfdfe; }
        .item-id { font-weight: 800; color: var(--brand-color); background: var(--brand-light); padding: 3px 8px; border-radius: 6px; font-size: 0.8rem; margin-right: 12px; }
        .item-main { display: flex; align-items: center; }
        .item-title { font-weight: 700; color: var(--text-main); font-size: 0.95rem; }
        .item-details { font-size: 0.75rem; color: #64748b; margin-top: 2px; }
        .badge-modern { background: #f1f5f9; color: #475569; font-weight: 700; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; }
        .btn-action-modern { width: 32px; height: 32px; background: #f1f5f9; border-radius: 8px; border: none; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #64748b; transition: 0.2s; }
        .btn-action-modern:hover { background: #e2e8f0; transform: scale(1.1); }
        .chelin-item { padding: 6px 14px; border-radius: 50px; border: 1px solid #e2e8f0; font-size: 0.75rem; font-weight: 700; cursor: pointer; background: white; }
        .chelin-item.active { background: var(--brand-light); color: var(--brand-color); border-color: var(--brand-color); }
        .tab-content-modern { display: none; }
        .tab-content-modern.active { display: block; animation: fadeIn 0.3s; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>

    {{-- SCRIPTS JS --}}
    <script>
        const numeroArchivosPorProceso = {@foreach($procesos_disponibles as $p) {{ $p->id }}: {{ (int)$p->numero_archivos }}, @endforeach};
        const tiposArchivosPorProceso = {@foreach($procesos_disponibles as $p) {{ $p->id }}: @json($p->tipos_archivos ?? []), @endforeach};
        const estadosPorProceso = {@foreach($procesos_disponibles as $p) {{ $p->id }}: [@foreach($p->estadosProcesos as $ep) @if($ep->estado) {val: "{{ $ep->estado->id }}", text: "{{ $ep->estado->nombre }}"}, @endif @endforeach], @endforeach};
        const dataRutas = {
            @foreach($correspondencias as $c)
            "{{ $c->id_radicado }}": {
                asunto: "{{ addslashes($c->asunto) }}",
                pasos: [
                    @php 
                        $histAgrup = $c->procesos->groupBy('id_proceso'); $nextP = false;
                    @endphp
                    @foreach(($c->flujo ? $c->flujo->procesos->sortBy('id') : collect()) as $p)
                    @php
                        $done = $histAgrup->has($p->id); $stat = $done ? 'completed' : (!$nextP ? 'current' : 'future'); if(!$done) $nextP = true;
                        $isMe = false; foreach($p->usuariosAsignados as $asig) { if(($asig->usuario->id ?? $asig->user_id) == auth()->id()) $isMe = true; }
                    @endphp
                    { id_proceso: "{{ $p->id }}", nombre: "{{ $p->nombre }}", status: "{{ $stat }}", es_responsable: {{ $isMe ? 'true' : 'false' }} },
                    @endforeach
                ]
            },
            @endforeach
        };

        function switchTab(id) {
            document.querySelectorAll('.tab-content-modern').forEach(v => v.classList.remove('active'));
            document.querySelectorAll('.tab-modern-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('view-'+id).classList.add('active');
            event.currentTarget.classList.add('active');
        }

        function abrirModalRuta(idRad) {
            const data = dataRutas[idRad]; if(!data) return;
            const container = document.getElementById('timeline_steps_container');
            container.innerHTML = '';
            data.pasos.forEach((p, i) => {
                const color = p.status === 'completed' ? 'text-success' : (p.status === 'current' ? 'text-primary' : 'text-muted');
                container.innerHTML += `
                    <div class="d-flex mb-3 align-items-center">
                        <i class="fas ${p.status === 'completed' ? 'fa-check-circle' : 'fa-circle'} ${color} me-3"></i>
                        <div class="flex-grow-1 small fw-bold ${color}">${p.nombre}</div>
                        ${p.es_responsable ? `<button onclick="event.stopPropagation(); saltarAGestionDirecta('${idRad}', '${data.asunto}', '${p.id_proceso}')" class="btn btn-indigo-modern py-0 px-2" style="font-size:10px; border-radius:5px">Gestionar</button>` : ''}
                    </div>`;
            });
            new bootstrap.Modal(document.getElementById('modalRuta')).show();
        }

        function saltarAGestionDirecta(idRad, asu, idP) {
            bootstrap.Modal.getInstance(document.getElementById('modalRuta'))?.hide();
            abrirModalRapido(idRad, asu);
            setTimeout(() => {
                const sel = document.getElementById('select_etapa_proceso_visual');
                sel.value = idP; sel.disabled = true;
                cargarEstadosDeEtapa(idP);
            }, 250);
        }

        function abrirModalRapido(idRad, asu) {
            document.getElementById('modal_id_correspondencia').value = idRad;
            document.getElementById('modal_radicado_lbl').innerText = "#" + idRad;
            document.getElementById('modal_asunto_lbl').innerText = asu;
            new bootstrap.Modal(document.getElementById('modalSeguimiento')).show();
        }

        function cargarEstadosDeEtapa(idP) {
            const cont = document.getElementById('container_estados_checkin');
            const inp = document.getElementById('input_estado_id_hidden');
            document.getElementById('input_etapa_proceso_hidden').value = idP;
            cont.innerHTML = '';
            (estadosPorProceso[idP] || []).forEach(e => {
                const div = document.createElement('div'); div.className = 'chelin-item'; div.innerText = e.text;
                div.onclick = () => { cont.querySelectorAll('.chelin-item').forEach(x => x.classList.remove('active')); div.classList.add('active'); inp.value = e.val; };
                cont.appendChild(div);
            });
            const cA = document.getElementById('container_archivos_dinamicos'); cA.innerHTML = '';
            let n = numeroArchivosPorProceso[idP] || 0; let nms = tiposArchivosPorProceso[idP] || [];
            if(n > 0) {
                for(let i=0; i<n; i++) { cA.innerHTML += `<div class="mb-2"><label class="small fw-bold">${nms[i] || 'Doc #'+(i+1)} *</label><input type="file" name="documento_arc[]" class="form-control form-control-sm" required></div>`; }
            }
        }

        function applyFilter(k, v) { document.getElementById('input-'+k.replace('_id','')).value = v; document.getElementById('filter-form').submit(); }
        function resetFilters() { window.location.href = "{{ route('correspondencia.tablero') }}"; }

        document.addEventListener('DOMContentLoaded', function() {
            new Chart(document.getElementById('chartDistribution'), { type: 'doughnut', data: { labels: {!! json_encode($chartDistribucion['labels']) !!}, datasets: [{ data: {!! json_encode($chartDistribucion['data']) !!}, backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'], borderWidth: 0 }] }, options: { maintainAspectRatio: false } });
            new Chart(document.getElementById('chartWorkload'), { type: 'bar', data: { labels: {!! json_encode($chartCarga['labels']) !!}, datasets: [{ label: 'Radicados Pendientes', data: {!! json_encode($chartCarga['data']) !!}, backgroundColor: '#4f46e5', borderRadius: 8 }] }, options: { maintainAspectRatio: false, scales: { y: { beginAtZero: true } } } });
        });
    </script>
</x-base-layout>