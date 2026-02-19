<x-base-layout>
    {{-- 1. Librerías Externas --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    @php
        // --- LÓGICA DE BACKEND DINÁMICA ---
        $hasActiveFilters = request()->filled('search') || request()->filled('estado_id') || request()->filled('usuario_id') || request()->filled('flujo_id') || request()->filled('condicion');
        $activeTab = $hasActiveFilters ? 'gestion' : 'dashboard';

        // 1. EMPAQUETAMOS LOS FILTROS BASE (Excluyendo la categoría/flujo para no afectar los chips hermanos)
        $aplicarFiltrosBase = function($q) {
            if (request()->filled('search')) {
                $words = explode(' ', request('search'));
                $q->where(function($subQ) use ($words) {
                    foreach ($words as $word) {
                        $subQ->orWhere('id_radicado', 'LIKE', "%$word%")
                             ->orWhere('asunto', 'LIKE', "%$word%");
                    }
                });
            }
            if (request()->filled('estado_id')) $q->where('estado_id', request('estado_id'));
            if (request()->filled('usuario_id')) $q->where('usuario_id', request('usuario_id'));
            if (request('condicion') == 'vencido') {
                $q->whereHas('trd', function($subQ) {
                    $subQ->whereRaw("DATE_ADD(corr_correspondencia.fecha_solicitud, INTERVAL corr_trd.tiempo_gestion DAY) < NOW()");
                })->where('finalizado', false);
            }
        };

        // 2. CONSULTA PRINCIPAL DE RADICADOS
        $query = \App\Models\Correspondencia\Correspondencia::with([
            'trd', 'estado', 'usuario', 'flujo.procesos.usuariosAsignados', 'procesos'
        ])->where($aplicarFiltrosBase);

        if (request()->filled('flujo_id')) {
            $query->where('flujo_id', request('flujo_id'));
        }

        $correspondencias = $query->orderBy('fecha_solicitud', 'desc')->paginate(15);

        // 3. CONSULTA DE CATEGORÍAS (CONTEO EXACTO DINÁMICO)
        $flujos_disponibles = \App\Models\Correspondencia\FlujoDeTrabajo::withCount(['correspondencias' => $aplicarFiltrosBase])
            ->orderBy('nombre')
            ->get(); 

        // 4. DATOS PARA KPIs RÁPIDOS DINÁMICOS
        // Clonamos la query base y le agregamos el filtro de flujo si existe, para que los KPIs sean 100% precisos
        $kpiQuery = \App\Models\Correspondencia\Correspondencia::where($aplicarFiltrosBase);
        if (request()->filled('flujo_id')) {
            $kpiQuery->where('flujo_id', request('flujo_id'));
        }

        $kpiTotal = (clone $kpiQuery)->count();
        $kpiPendientes = (clone $kpiQuery)->where('finalizado', false)->count();
        $kpiFinalizados = (clone $kpiQuery)->where('finalizado', true)->count();
        $kpiVencidos = (clone $kpiQuery)->where('finalizado', false)
            ->whereHas('trd', function($q) {
                $q->whereRaw("DATE_ADD(corr_correspondencia.fecha_solicitud, INTERVAL corr_trd.tiempo_gestion DAY) < NOW()");
            })->count();

        // 5. DATOS AUXILIARES Y DE GRÁFICOS
        $procesos_disponibles = \App\Models\Correspondencia\Proceso::with(['flujo', 'usuariosAsignados.usuario', 'estadosProcesos.estado'])->get();
        $estadosKpi = \App\Models\Correspondencia\Estado::withCount('correspondencias')->get();
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

        $chartCategorias = [
            'labels' => $flujos_disponibles->pluck('nombre'),
            'data' => $flujos_disponibles->pluck('correspondencias_count'),
        ];
    @endphp

    <div class="ux-shell">
        {{-- HEADER MINIMALISTA --}}
        <header class="ux-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="ux-eyebrow">Gestión Documental</span>
                        <h1 class="ux-title">Centro de Control</h1>
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        <div class="ux-tab-switch">
                            <button class="{{ $activeTab === 'dashboard' ? 'active' : '' }}" onclick="switchTab('dashboard')">
                                <i class="fas fa-chart-pie"></i> Insights
                            </button>
                            <button class="{{ $activeTab === 'gestion' ? 'active' : '' }}" onclick="switchTab('gestion')">
                                <i class="fas fa-list-ul"></i> Radicados @if($kpiTotal > 0) <span class="badge">{{ $kpiTotal }}</span> @endif
                            </button>
                        </div>
                        <a href="{{ route('correspondencia.correspondencias.create') }}" class="btn-primary-ux">
                            <i class="fas fa-plus"></i> Nuevo Radicado
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="ux-content container-fluid mt-3">
            
            {{-- VISTA 1: DASHBOARD (INSIGHTS) --}}
            <div id="view-dashboard" class="ux-view {{ $activeTab === 'dashboard' ? 'active' : '' }}">
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="ux-card">
                            <h5 class="ux-card-title">Distribución por Estado</h5>
                            <div class="chart-wrapper"><canvas id="chartDistribution"></canvas></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ux-card">
                            <h5 class="ux-card-title">Carga Operativa por Usuario</h5>
                            <div class="chart-wrapper"><canvas id="chartWorkload"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- VISTA 2: GESTIÓN (LISTA Y FILTROS) --}}
            <div id="view-gestion" class="ux-view {{ $activeTab === 'gestion' ? 'active' : '' }}">
                
                {{-- BARRA DE KPIs RÁPIDOS (DINÁMICA) --}}
                <div class="ux-mini-kpi-bar mb-4">
                    <div class="mini-kpi" onclick="resetFilters()">
                        <div class="val">{{ $kpiTotal }}</div>
                        <div class="lbl">Total Radicados</div>
                    </div>
                    <div class="mini-kpi kpi-warning" onclick="applyFilter('condicion', 'vencido')">
                        <div class="val">{{ $kpiVencidos }}</div>
                        <div class="lbl">Vencidos</div>
                    </div>
                    <div class="mini-kpi kpi-info">
                        <div class="val">{{ $kpiPendientes }}</div>
                        <div class="lbl">En Proceso</div>
                    </div>
                    <div class="mini-kpi kpi-success">
                        <div class="val">{{ $kpiFinalizados }}</div>
                        <div class="lbl">Finalizados</div>
                    </div>
                </div>

                <form action="{{ route('correspondencia.tablero') }}" method="GET" id="filter-form">
                    <input type="hidden" name="estado_id" id="input-estado" value="{{ request('estado_id') }}">
                    <input type="hidden" name="flujo_id" id="input-flujo" value="{{ request('flujo_id') }}">
                    <input type="hidden" name="condicion" id="input-condicion" value="{{ request('condicion') }}">
                    
                    {{-- SEARCH Y USUARIO --}}
                    <div class="ux-filter-bar mb-3">
                        <div class="ux-search-box flex-grow-1">
                            <i class="fas fa-search text-muted"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por radicado o asunto..." onblur="this.form.submit()">
                        </div>
                        <div class="ux-select-wrapper">
                            <i class="far fa-user text-muted"></i>
                            <select name="usuario_id" onchange="this.form.submit()">
                                <option value="">Todos los Responsables</option>
                                @foreach($todosLosUsuarios as $u)
                                    <option value="{{ $u->id }}" {{ request('usuario_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" class="btn-clear-ux" onclick="resetFilters()" title="Limpiar Filtros"><i class="fas fa-times"></i></button>
                    </div>

                    {{-- FILTROS DE CATEGORÍA (CHIPS DINÁMICOS) --}}
                    <div class="ux-category-scroll mb-4">
                        <span class="ux-chip {{ !request('flujo_id') ? 'active' : '' }}" onclick="applyFilter('flujo_id', '')">
                            Todas las Categorías
                        </span>
                        @foreach($flujos_disponibles as $flujo)
                            <span class="ux-chip {{ request('flujo_id') == $flujo->id ? 'active' : '' }}" 
                                  onclick="applyFilter('flujo_id', '{{ $flujo->id }}')">
                                {{ $flujo->nombre }} <span class="badge-chip">{{ $flujo->correspondencias_count }}</span>
                            </span>
                        @endforeach
                    </div>
                </form>

                {{-- LISTA EN FILAS (ROWS) --}}
                <div class="ux-list-container">
                    {{-- Encabezado de la lista --}}
                    <div class="ux-row-header">
                        <div class="col-id">Radicado</div>
                        <div class="col-asunto">Asunto</div>
                        <div class="col-categoria">Categoría</div>
                        <div class="col-responsable">Responsable</div>
                        <div class="col-estado">Estado</div>
                        <div class="col-fecha">Fecha</div>
                        <div class="col-acciones text-end">Acciones</div>
                    </div>

                    <div class="ux-row-body">
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

                            <div class="ux-row-item {{ $isVencido ? 'row-vencida' : '' }}" onclick="abrirModalRuta('{{ $c->id_radicado }}')">
                                <div class="col-id">
                                    <span class="rad-badge">#{{ $c->id_radicado }}</span>
                                </div>
                                <div class="col-asunto font-medium text-dark truncate-text" title="{{ $c->asunto }}">
                                    {{ $c->asunto }}
                                </div>
                                <div class="col-categoria text-muted small truncate-text">
                                    <i class="far fa-folder me-1"></i> {{ $c->flujo->nombre ?? 'Sin categoría' }}
                                </div>
                                <div class="col-responsable text-muted small">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-mini">{{ substr($c->usuario->name ?? 'U', 0, 1) }}</div>
                                        <span class="truncate-text">{{ $c->usuario->name ?? 'Sin asignar' }}</span>
                                    </div>
                                </div>
                                <div class="col-estado">
                                    <span class="status-dot {{ $c->finalizado ? 'bg-success' : 'bg-primary' }}"></span>
                                    <span class="small">{{ $c->estado->nombre ?? 'Sin Estado' }}</span>
                                </div>
                                <div class="col-fecha text-muted small">
                                    {{ $c->fecha_solicitud->format('d/m/Y') }}
                                </div>
                                <div class="col-acciones d-flex gap-2 justify-content-end" onclick="event.stopPropagation()">
                                    @if($soyResponsable)
                                        <button onclick="saltarAGestionDirecta('{{ $c->id_radicado }}', '{{ addslashes($c->asunto) }}', '{{ $procesoId }}')" class="action-btn text-indigo" title="Gestión Rápida">
                                            <i class="fas fa-bolt"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('correspondencia.correspondencias.show', $c) }}" class="action-btn text-muted">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="ux-empty-state text-center py-5">
                                <i class="far fa-folder-open empty-icon"></i>
                                <h5>No hay radicados</h5>
                                <p class="text-muted small">Ajusta los filtros o crea un nuevo radicado.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-4">{{ $correspondencias->links() }}</div>

                {{-- GRÁFICOS INFERIORES BASADOS EN CATEGORÍA --}}
                <div class="ux-card mt-5">
                    <h5 class="ux-card-title"><i class="fas fa-chart-bar me-2 text-muted"></i>Volumen de Radicados por Categoría (Según Filtros)</h5>
                    <div class="chart-wrapper" style="height: 250px;"><canvas id="chartCategorias"></canvas></div>
                </div>

            </div>
        </main>
    </div>

    {{-- MODALES --}}
    <div class="modal fade" id="modalSeguimiento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0" style="border-radius: 20px;">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="fw-bold"><i class="fas fa-bolt text-warning me-2"></i>Gestión Rápida <span id="modal_radicado_lbl" class="text-primary ms-2"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="px-4 pb-2 text-muted small fw-bold" id="modal_asunto_lbl"></div>
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
                        <button type="submit" class="btn-primary-ux w-100 justify-content-center py-2">Guardar Gestión</button>
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

    {{-- ESTILOS CSS UX MINIMALISTA --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        :root { 
            --bg-body: #F8FAFC; 
            --surface: #FFFFFF; 
            --surface-hover: #F8FAFC;
            --primary: #4F46E5; 
            --primary-soft: #EEF2FF;
            --text-dark: #0F172A; 
            --text-muted: #64748B; 
            --border-light: #E2E8F0;
            --radius-lg: 16px;
            --radius-md: 10px;
            --shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.05);
            --transition: all 0.2s ease;
        }

        body { font-family: 'Inter', sans-serif; background-color: var(--bg-body); color: var(--text-dark); }
        .truncate-text { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .font-medium { font-weight: 500; }

        /* HEADER */
        .ux-header { padding: 1.5rem 0; border-bottom: 1px solid var(--border-light); background: var(--surface); }
        .ux-eyebrow { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); font-weight: 700; }
        .ux-title { font-weight: 800; font-size: 1.75rem; margin: 0; letter-spacing: -0.5px; }

        /* TABS SEGMENTADOS */
        .ux-tab-switch { background: #F1F5F9; padding: 4px; border-radius: 10px; display: flex; gap: 4px; }
        .ux-tab-switch button { border: none; background: transparent; padding: 6px 14px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; color: var(--text-muted); cursor: pointer; transition: var(--transition); }
        .ux-tab-switch button.active { background: var(--surface); color: var(--text-dark); box-shadow: var(--shadow-sm); }
        .ux-tab-switch .badge { background: var(--primary); color: white; border-radius: 20px; font-size: 0.7rem; padding: 2px 6px; }

        /* BOTONES */
        .btn-primary-ux { background: var(--text-dark); color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: var(--transition); }
        .btn-primary-ux:hover { background: var(--primary); color: white; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2); }
        .btn-clear-ux { width: 40px; height: 40px; border-radius: 10px; border: 1px solid var(--border-light); background: var(--surface); color: var(--text-muted); display: grid; place-items: center; cursor: pointer; transition: var(--transition); }
        .btn-clear-ux:hover { background: #F1F5F9; color: var(--text-dark); }

        /* MINI KPIs BAR */
        .ux-mini-kpi-bar { display: flex; gap: 1rem; flex-wrap: wrap; }
        .mini-kpi { flex: 1; min-width: 150px; background: var(--surface); border: 1px solid var(--border-light); border-radius: var(--radius-md); padding: 1rem; display: flex; flex-direction: column; cursor: pointer; transition: var(--transition); box-shadow: var(--shadow-sm); }
        .mini-kpi:hover { border-color: var(--primary-soft); transform: translateY(-2px); }
        .mini-kpi .val { font-size: 1.5rem; font-weight: 800; line-height: 1; margin-bottom: 4px; color: var(--text-dark); }
        .mini-kpi .lbl { font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .kpi-warning .val { color: #EF4444; }
        .kpi-info .val { color: var(--primary); }
        .kpi-success .val { color: #10B981; }

        /* FILTROS */
        .ux-filter-bar { display: flex; gap: 1rem; flex-wrap: wrap; }
        .ux-search-box, .ux-select-wrapper { background: var(--surface); border: 1px solid var(--border-light); border-radius: 10px; padding: 0 1rem; height: 40px; display: flex; align-items: center; gap: 10px; box-shadow: var(--shadow-sm); transition: var(--transition); }
        .ux-search-box:focus-within, .ux-select-wrapper:focus-within { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-soft); }
        .ux-search-box input, .ux-select-wrapper select { border: none; background: transparent; outline: none; width: 100%; font-size: 0.85rem; color: var(--text-dark); font-weight: 500; }
        
        /* CATEGORY CHIPS */
        .ux-category-scroll { display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 4px; scrollbar-width: none; }
        .ux-category-scroll::-webkit-scrollbar { display: none; }
        .ux-chip { white-space: nowrap; padding: 6px 14px; background: var(--surface); border: 1px solid var(--border-light); border-radius: 50px; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); cursor: pointer; transition: var(--transition); display: flex; align-items: center; gap: 6px;}
        .ux-chip:hover { background: var(--surface-hover); border-color: #CBD5E1; color: var(--text-dark); }
        .ux-chip.active { background: var(--text-dark); color: white; border-color: var(--text-dark); }
        .badge-chip { background: #E2E8F0; color: var(--text-dark); padding: 2px 6px; border-radius: 10px; font-size: 0.65rem; }
        .ux-chip.active .badge-chip { background: rgba(255,255,255,0.2); color: white; }

        /* ROWS (LISTA EN FILAS) */
        .ux-list-container { background: var(--surface); border: 1px solid var(--border-light); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden; }
        
        /* Grid para las columnas */
        .ux-row-header, .ux-row-item { display: grid; grid-template-columns: 80px 3fr 2fr 2fr 1.5fr 100px 80px; gap: 1rem; align-items: center; padding: 0.85rem 1.5rem; }
        
        .ux-row-header { background: #F8FAFC; border-bottom: 1px solid var(--border-light); font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        
        .ux-row-item { border-bottom: 1px solid var(--border-light); cursor: pointer; transition: var(--transition); background: var(--surface); }
        .ux-row-item:last-child { border-bottom: none; }
        .ux-row-item:hover { background: #F8FAFC; }
        .ux-row-item.row-vencida { border-left: 3px solid #EF4444; padding-left: calc(1.5rem - 3px); }
        
        .rad-badge { font-size: 0.75rem; font-weight: 700; color: var(--primary); background: var(--primary-soft); padding: 4px 8px; border-radius: 6px; }
        
        .avatar-mini { width: 24px; height: 24px; background: #E2E8F0; border-radius: 50%; display: grid; place-items: center; font-size: 0.65rem; font-weight: 700; color: var(--text-muted); flex-shrink: 0; }
        
        .status-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 6px; }
        .bg-success { background-color: #10B981 !important; }
        .bg-primary { background-color: var(--primary) !important; }

        .action-btn { width: 30px; height: 30px; border-radius: 6px; border: none; background: transparent; display: grid; place-items: center; cursor: pointer; transition: var(--transition); }
        .action-btn:hover { background: #E2E8F0; color: var(--text-dark); }
        .text-indigo { color: var(--primary); }

        /* CHARTS & EXTRAS */
        .ux-card { background: var(--surface); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm); border: 1px solid var(--border-light); }
        .ux-card-title { font-size: 0.9rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-dark); }
        .chart-wrapper { position: relative; }
        .empty-icon { font-size: 2.5rem; color: #CBD5E1; margin-bottom: 1rem; }
        
        .ux-view { display: none; animation: fadeIn 0.3s forwards; }
        .ux-view.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* Estilos reusables del modal anterior para los chelines */
        .chelin-item { padding: 6px 14px; border-radius: 50px; border: 1px solid var(--border-light); font-size: 0.75rem; font-weight: 700; cursor: pointer; background: white; transition: 0.2s; }
        .chelin-item.active { background: var(--primary-soft); color: var(--primary); border-color: var(--primary); }
    </style>

    {{-- SCRIPTS JS --}}
    <script>
        // Datos para Modales
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
            document.querySelectorAll('.ux-view').forEach(v => v.classList.remove('active'));
            document.querySelectorAll('.ux-tab-switch button').forEach(b => b.classList.remove('active'));
            document.getElementById('view-'+id).classList.add('active');
            event.currentTarget.classList.add('active');
        }

        function applyFilter(key, value) { 
            document.getElementById('input-' + key.replace('_id','')).value = value; 
            document.getElementById('filter-form').submit(); 
        }
        
        function resetFilters() { 
            window.location.href = "{{ route('correspondencia.tablero') }}"; 
        }

        // --- FUNCIONES DE MODALES ---
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
                        ${p.es_responsable ? `<button onclick="event.stopPropagation(); saltarAGestionDirecta('${idRad}', '${data.asunto}', '${p.id_proceso}')" class="btn btn-sm btn-light py-0 px-2" style="font-size:11px; border-radius:5px">Gestionar</button>` : ''}
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

        // --- INICIALIZACIÓN CHART.JS ---
        document.addEventListener('DOMContentLoaded', function() {
            // Chart Distribución (Dashboard)
            new Chart(document.getElementById('chartDistribution'), { 
                type: 'doughnut', 
                data: { 
                    labels: {!! json_encode($chartDistribucion['labels']) !!}, 
                    datasets: [{ data: {!! json_encode($chartDistribucion['data']) !!}, backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'], borderWidth: 0 }] 
                }, 
                options: { maintainAspectRatio: false, cutout: '75%', plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8 } } } } 
            });

            // Chart Carga (Dashboard)
            new Chart(document.getElementById('chartWorkload'), { 
                type: 'bar', 
                data: { 
                    labels: {!! json_encode($chartCarga['labels']) !!}, 
                    datasets: [{ label: 'Pendientes', data: {!! json_encode($chartCarga['data']) !!}, backgroundColor: '#4f46e5', borderRadius: 6 }] 
                }, 
                options: { maintainAspectRatio: false, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, border: { dash: [4, 4] } } }, plugins: { legend: { display: false } } } 
            });

            // Chart Categorías (Gráfico Inferior en Vista Gestión)
            new Chart(document.getElementById('chartCategorias'), { 
                type: 'bar', 
                data: { 
                    labels: {!! json_encode($chartCategorias['labels']) !!}, 
                    datasets: [{ 
                        label: 'Volumen por Categoría', 
                        data: {!! json_encode($chartCategorias['data']) !!}, 
                        backgroundColor: '#94A3B8', 
                        hoverBackgroundColor: '#4F46E5',
                        borderRadius: 4 
                    }] 
                }, 
                options: { 
                    maintainAspectRatio: false, 
                    scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: '#F1F5F9' } } }, 
                    plugins: { legend: { display: false } } 
                } 
            });
        });
    </script>
</x-base-layout>