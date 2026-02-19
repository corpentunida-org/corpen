<x-base-layout>
    @php
        // 1. EMPAQUETAMOS LOS FILTROS BASE EN UNA FUNCIÓN (CLOSURE)
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
            if (request()->filled('estado')) {
                $q->where('estado_id', request('estado')); // Asumiendo que 'estado' es 'estado_id' en la bd
            }
        };

        // 2. CONSULTA DE CATEGORÍAS (FLUJOS) CON CONTEO DINÁMICO
        $flujos_disponibles = \App\Models\Correspondencia\FlujoDeTrabajo::withCount(['correspondencias' => $aplicarFiltrosBase])
            ->orderBy('nombre')
            ->get();
            
        // Variable para contar todos los radicados sin importar la categoría
        $total_radicados_busqueda = \App\Models\Correspondencia\Correspondencia::where($aplicarFiltrosBase)->count();
    @endphp

    <style>
        :root {
            /* Paleta Pastel Minimalista */
            --bg-app: #f9fafb; --white: #ffffff; --text-dark: #374151; --text-light: #9ca3af; --border-light: #f3f4f6;
            --pastel-blue: #eff6ff; --text-blue: #3b82f6;
            --pastel-green: #ecfdf5; --text-green: #10b981;
            --pastel-purple: #f5f3ff; --text-purple: #8b5cf6;
            --pastel-red: #fef2f2; --text-red: #ef4444;
            --pastel-orange: #fff7ed; --text-orange: #f97316;
            --pastel-info: #e0f7fa; --text-info: #006064;
        }

        body { background-color: var(--bg-app); font-family: 'Inter', sans-serif; }
        .app-container { padding: 1.5rem; max-width: 1400px; margin: 0 auto; }

        /* --- UI COMPONENTS --- */
        .card-clean { background: var(--white); border-radius: 12px; border: 1px solid var(--border-light); box-shadow: 0 1px 2px rgba(0,0,0,0.03); }
        .search-pill { background: var(--bg-app); border: 1px solid transparent; border-radius: 50px; padding: 0.4rem 1rem; font-size: 0.85rem; transition: all 0.2s; color: var(--text-dark); }
        .search-pill:focus { background: var(--white); border-color: var(--pastel-blue); box-shadow: 0 0 0 3px var(--pastel-blue); outline: none; }
        
        /* Tabla Minimalista */
        .table-minimal { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-minimal thead th { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-light); font-weight: 600; padding: 12px 16px; border-bottom: 1px solid var(--border-light); text-align: left; }
        .table-minimal tbody td { padding: 10px 16px; font-size: 0.85rem; color: var(--text-dark); border-bottom: 1px solid var(--border-light); vertical-align: middle; background: var(--white); }
        
        /* Fila Clickeable */
        .row-clickable { cursor: pointer; transition: background-color 0.2s ease; }
        .row-clickable:hover td { background-color: #f8fafc !important; }

        /* Avatares y Badges */
        .avatar-tiny { width: 28px; height: 28px; border-radius: 8px; background: var(--pastel-purple); color: var(--text-purple); display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; }
        .badge-pill { padding: 2px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 500; display: inline-flex; align-items: center; gap: 4px; }
        .badge-blue { background: var(--pastel-blue); color: var(--text-blue); }
        .badge-green { background: var(--pastel-green); color: var(--text-green); }
        
        /* Botones */
        .btn-ghost { color: var(--text-light); padding: 4px; border-radius: 6px; transition: all 0.2s; cursor: pointer; border: none; background: transparent; display: inline-flex; align-items: center; justify-content: center;}
        .btn-ghost:hover { background: var(--pastel-blue); color: var(--text-blue); }
        .btn-soft-primary { background: var(--pastel-blue); color: var(--text-blue); font-weight: 600; font-size: 0.85rem; padding: 6px 16px; border-radius: 8px; border: 1px solid transparent; transition: all 0.2s; }
        .btn-soft-primary:hover { background: #dbeafe; transform: translateY(-1px); }

        /* Estilos Chelins (Gestión Rápida) */
        .chelin-item { display: inline-block; padding: 8px 16px; border-radius: 50px; border: 1px solid #dee2e6; background-color: white; color: #6c757d; font-size: 0.75rem; font-weight: 700; cursor: pointer; transition: all 0.2s; user-select: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        .chelin-item:hover { background-color: #f8f9fa; transform: translateY(-1px); }
        .chelin-item.active { background-color: var(--pastel-blue); color: var(--text-blue); border-color: #90caf9; box-shadow: 0 4px 6px rgba(13, 110, 253, 0.15); }
        
        .upload-box:hover { background-color: var(--pastel-blue) !important; border-color: var(--text-blue) !important; }
        .action-card { cursor: pointer; transition: all 0.2s; }
        .modal-content { border-radius: 24px; border: none; overflow: hidden; }

        /* Ruta y Timeline */
        .avatar-circle-sm { width: 24px; height: 24px; background-color: #fff; color: #495057; border: 2px solid #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 700; margin-left: -8px; transition: all 0.2s ease; cursor: default; }
        .avatar-circle-sm:first-child { margin-left: 0; }
        .avatar-circle-sm:hover { transform: translateY(-3px); z-index: 10; }
        .route-line { position: absolute; left: 50%; transform: translateX(-50%); width: 2px; height: 100%; top: 32px; z-index: -1; background-color: #f0f2f5; }

        /* NUEVO: ESTILOS CHIPS CATEGORÍAS */
        .ux-category-scroll { display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 4px; scrollbar-width: none; }
        .ux-category-scroll::-webkit-scrollbar { display: none; }
        .ux-chip { white-space: nowrap; padding: 6px 14px; background: var(--white); border: 1px solid var(--border-light); border-radius: 50px; font-size: 0.8rem; font-weight: 600; color: var(--text-light); cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);}
        .ux-chip:hover { background: #f8fafc; border-color: #d1d5db; color: var(--text-dark); }
        .ux-chip.active { background: var(--text-dark); color: white; border-color: var(--text-dark); }
        .badge-chip { background: #f1f5f9; color: var(--text-dark); padding: 2px 6px; border-radius: 10px; font-size: 0.65rem; }
        .ux-chip.active .badge-chip { background: rgba(255,255,255,0.2); color: white; }
    </style>

    <div class="app-container">
        
        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold m-0" style="font-size: 1.1rem; color: #111827;">Correspondencia</h1>
                <p class="m-0 text-muted" style="font-size: 0.8rem;">Bandeja de gestión rápida</p>
            </div>
            <a href="{{ route('correspondencia.correspondencias.create') }}" class="btn-soft-primary text-decoration-none shadow-sm">
                <i class="bi bi-plus text-lg"></i> Nuevo
            </a>
        </div>

        {{-- FILTROS --}}
        <div class="card-clean p-3 mb-3">
            <form action="{{ route('correspondencia.correspondencias.index') }}" method="GET" id="filter-form-minimal">
                {{-- Input oculto para la categoría (flujo_id) --}}
                <input type="hidden" name="flujo_id" id="input-flujo" value="{{ request('flujo_id') }}">

                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="position-relative flex-grow-1" style="max-width: 350px;">
                        <i class="bi bi-search position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 0.8rem;"></i>
                        <input type="text" name="search" class="form-control search-pill ps-5 w-100" placeholder="Buscar radicado, asunto..." value="{{ request('search') }}" onblur="this.form.submit()">
                    </div>
                    <select name="estado" class="form-select search-pill border-0" style="width: auto; cursor: pointer; background-color: var(--bg-app);" onchange="this.form.submit()">
                        <option value="">Estado: Todos</option>
                        @foreach($estados as $est)
                            <option value="{{ $est->id_estado }}" {{ request('estado') == $est->id_estado ? 'selected' : '' }}>{{ $est->nombre }}</option>
                        @endforeach
                    </select>
                    @if(request()->filled('search') || request()->filled('estado') || request()->filled('flujo_id'))
                        <a href="{{ route('correspondencia.correspondencias.index') }}" class="btn btn-sm btn-light rounded-circle p-2 shadow-sm text-danger" title="Limpiar Filtros">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>

                {{-- NUEVO: FILTROS DE CATEGORÍA (CHIPS) --}}
                <div class="ux-category-scroll">
                    <span class="ux-chip {{ !request('flujo_id') ? 'active' : '' }}" onclick="applyCategoryFilter('')">
                        Todas las Categorías <span class="badge-chip">{{ $total_radicados_busqueda }}</span>
                    </span>
                    @foreach($flujos_disponibles as $flujo)
                        <span class="ux-chip {{ request('flujo_id') == $flujo->id ? 'active' : '' }}" 
                              onclick="applyCategoryFilter('{{ $flujo->id }}')">
                            {{ $flujo->nombre }} <span class="badge-chip">{{ $flujo->correspondencias_count }}</span>
                        </span>
                    @endforeach
                </div>
            </form>
        </div>

        {{-- TABLA PRINCIPAL --}}
        <div class="card-clean overflow-hidden">
            <div class="table-responsive">
                <table class="table-minimal">
                    <thead>
                        <tr>
                            <th width="10%">Radicado</th>
                            <th width="30%">Asunto</th>
                            <th width="20%">Remitente</th>
                            <th width="15%">Estado</th>
                            <th width="10%">Fecha</th>
                            <th width="15%" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($correspondencias as $corr)
                        <tr onclick="abrirModalRuta('{{ $corr->id_radicado }}')" class="row-clickable" title="Clic para ver flujo completo">
                            <td><span class="fw-bold" style="color: #4b5563;">#{{ $corr->id_radicado }}</span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-truncate" style="max-width: 280px;">{{ $corr->asunto }}</span>
                                    @if($corr->es_confidencial) <i class="bi bi-lock-fill text-danger opacity-50" title="Confidencial" data-bs-toggle="tooltip"></i> @endif
                                    @if($corr->documento_arc) <i class="bi bi-paperclip text-primary opacity-50" title="Tiene adjunto" data-bs-toggle="tooltip"></i> @endif
                                </div>
                                <div class="text-muted small mt-1" style="font-size: 0.7rem;">
                                    <i class="bi bi-folder me-1"></i> {{ $corr->flujo->nombre ?? 'Sin categoría' }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-tiny">{{ substr($corr->remitente->nom_ter ?? '?', 0, 1) }}</div>
                                    <span class="text-truncate" style="max-width: 150px;">{{ Str::limit($corr->remitente->nom_ter ?? 'N/A', 20) }}</span>
                                </div>
                            </td>
                            <td>
                                @if($corr->finalizado)
                                    <span class="badge-pill badge-green"><i class="bi bi-check2"></i> Finalizado</span>
                                @else
                                    <span class="badge-pill badge-blue"><i class="bi bi-clock"></i> En Proceso</span>
                                @endif
                            </td>
                            <td class="text-muted" style="font-size: 0.75rem;">{{ $corr->fecha_solicitud->format('d M, Y') }}</td>
                            
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1 position-relative" style="z-index: 2;">
                                    @php
                                        $soyResponsableDeAlgo = false;
                                        $primerIdProcesoResponsable = null;

                                        if (!$corr->finalizado && $corr->flujo) {
                                            foreach ($corr->flujo->procesos->sortBy('id') as $paso) {
                                                foreach ($paso->usuariosAsignados as $asignacion) {
                                                    $uid = $asignacion->usuario->id ?? $asignacion->user_id ?? $asignacion->usuario_id;
                                                    if ($uid == auth()->id()) {
                                                        $soyResponsableDeAlgo = true;
                                                        $primerIdProcesoResponsable = $paso->id;
                                                        break 2; 
                                                    }
                                                }
                                            }
                                        }
                                    @endphp

                                    @if($soyResponsableDeAlgo)
                                        <button type="button" class="btn-ghost text-success fw-bold" 
                                                onclick="event.stopPropagation(); saltarAGestionDirecta('{{ $corr->id_radicado }}', '{{ addslashes(Str::limit($corr->asunto, 60)) }}', '{{ $primerIdProcesoResponsable }}')"
                                                data-bs-toggle="tooltip" title="¡Estás asignado! Gestión Rápida">
                                            <i class="bi bi-pencil-square fs-6"></i>
                                        </button>
                                    @endif

                                    <a href="{{ route('correspondencia.correspondencias.show', $corr) }}" class="btn-ghost" onclick="event.stopPropagation();" data-bs-toggle="tooltip" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    @if(!$corr->finalizado)
                                        <a href="{{ route('correspondencia.correspondencias.edit', $corr) }}" class="btn-ghost" onclick="event.stopPropagation();" data-bs-toggle="tooltip" title="Finalización">
                                            <i class="bi bi-box-arrow-right"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted opacity-50"><i class="bi bi-inbox fs-4 d-block mb-1"></i> Sin registros en esta categoría</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($correspondencias) && method_exists($correspondencias, 'hasPages') && $correspondencias->hasPages())
                <div class="px-3 py-2 border-top">{{ $correspondencias->links() }}</div>
            @endif
        </div>
    </div>

    {{-- MODAL RUTA Y PARTICIPANTES --}}
    <div class="modal fade" id="modalRuta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow-lg">
                <div class="modal-header border-0 pb-0 bg-white">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-diagram-3 me-2 text-primary"></i>Ruta y Participantes
                    </h6>
                    <button type="button" class="btn-close shadow-none bg-light rounded-circle p-2" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pt-3 pb-4">
                    <div class="text-center mb-3 pb-2 border-bottom border-light">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Radicado</small>
                        <h5 class="fw-bold text-dark m-0" id="ruta_radicado_lbl">#</h5>
                    </div>
                    <div id="timeline_steps_container" class="px-2"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL GESTIÓN RÁPIDA --}}
    <div class="modal fade" id="modalSeguimiento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header border-0 pt-4 px-4 pb-0 bg-white">
                    <div>
                        <h5 class="modal-title fw-bolder text-dark">
                            <span class="text-warning me-2"><i class="bi bi-lightning-charge-fill"></i></span>
                            Gestión Rápida
                        </h5>
                        <p class="text-muted small mb-0 mt-1">Radicado: <span id="modal_radicado_lbl" class="fw-bold text-dark">#</span> - <span id="modal_asunto_lbl"></span></p>
                    </div>
                    <button type="button" class="btn-close shadow-none bg-light rounded-circle p-2" data-bs-dismiss="modal"></button>
                </div>

                <form id="formGestionRapida" action="{{ route('correspondencia.correspondencias-procesos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_correspondencia" id="modal_id_correspondencia">
                    <input type="hidden" name="id_proceso" id="input_etapa_proceso_hidden">

                    <div class="modal-body px-4 py-3">
                        <div class="row g-4">
                            {{-- ETAPA (BLOQUEADA POR DEFECTO AL GESTIONAR) --}}
                            <div class="col-12 bg-light rounded-4 p-3">
                                <label class="form-label fw-bold small text-dark mb-1">1. Etapa a Gestionar</label>
                                <select id="select_etapa_proceso_visual" class="form-select border-0 shadow-none py-2" style="font-weight: 600; background-color: white;" required onchange="cargarEstadosDeEtapa(this.value)">
                                    <option value="">-- Seleccione etapa --</option>
                                    @foreach($procesos_disponibles as $proceso)
                                        <option value="{{ $proceso->id }}">{{ $proceso->nombre }}</option>
                                    @endforeach
                                </select>
                                <input type="datetime-local" name="fecha_gestion" class="d-none" value="{{ now()->format('Y-m-d\TH:i') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold small text-dark mb-2">2. Nuevo Estado / Decisión <span class="text-danger">*</span></label>
                                <div id="container_estados_checkin" class="d-flex flex-wrap gap-2 p-3 rounded-4 border border-light bg-white" style="min-height: 60px;"></div>
                                <input type="hidden" name="estado_id" id="input_estado_id_hidden" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold small text-dark">3. Detalle de la Gestión <span class="text-danger">*</span></label>
                                <textarea id="observacion_gestion" name="observacion" class="form-control border-light bg-light rounded-4 p-3 shadow-sm" rows="2" placeholder="Describa la gestión..." style="resize: none;" required></textarea>
                            </div>

                            {{-- ARCHIVOS DINÁMICOS --}}
                            <div class="col-12">
                                <label class="form-label fw-bold small text-dark d-flex justify-content-between">
                                    <span>4. Soporte Documental</span>
                                    <span id="label_req_archivos" class="badge bg-secondary text-uppercase">Requerimiento</span>
                                </label>
                                <div id="container_archivos_dinamicos" class="d-flex flex-column gap-2"></div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex gap-3">
                                    <label class="action-card d-flex align-items-center p-2 px-3 rounded-4 border flex-fill bg-white">
                                        <input type="checkbox" name="notificado_email" value="1" class="form-check-input me-2">
                                        <span class="small fw-bold text-dark">Notificar Email</span>
                                    </label>
                                    <label class="action-card d-flex align-items-center p-2 px-3 rounded-4 border border-danger-subtle bg-danger-subtle flex-fill">
                                        <input type="checkbox" name="finalizado" value="1" class="form-check-input me-2 border-danger">
                                        <span class="small fw-bold text-danger">Finalizar Paso</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4 justify-content-between">
                        <button type="button" class="btn btn-link text-muted text-decoration-none fw-bold small" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-lg fw-bold">Guardar Gestión</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Función para enviar el formulario al dar clic en un Chip de Categoría
        function applyCategoryFilter(idFlujo) {
            document.getElementById('input-flujo').value = idFlujo;
            document.getElementById('filter-form-minimal').submit();
        }

        // CONFIGURACIÓN DE MAPAS
        const numeroArchivosPorProceso = {
            @foreach($procesos_disponibles as $proceso)
                {{ $proceso->id }}: {{ (int) $proceso->numero_archivos }},
            @endforeach
        };
        const tiposArchivosPorProceso = {
            @foreach($procesos_disponibles as $proceso)
                {{ $proceso->id }}: @json($proceso->tipos_archivos ?? []),
            @endforeach
        };
        const estadosPorProceso = {
            @foreach($procesos_disponibles as $proceso)
                {{ $proceso->id }}: [
                    @foreach($proceso->estadosProcesos as $ep)
                        @if($ep->estado) { val: "{{ $ep->estado->id }}", text: "{{ $ep->estado->nombre }}" }, @endif
                    @endforeach
                ],
            @endforeach
        };

        // DATA DE RUTAS 
        const dataRutas = {
            @foreach($correspondencias as $corr)
                "{{ $corr->id_radicado }}": {
                    asunto: "{{ addslashes(Str::limit($corr->asunto, 60)) }}",
                    pasos: [
                        @php
                            $historialAgrupado = $corr->procesos->groupBy('id_proceso');
                            $siguientePasoEncontrado = false;
                            $participantesIds = $corr->procesos->pluck('usuario_id')->merge($corr->procesos->pluck('user_id'))->filter()->unique()->toArray();
                            $procesosDelFlujo = $corr->flujo ? $corr->flujo->procesos->sortBy('id') : collect();
                        @endphp
                        @foreach($procesosDelFlujo as $index => $paso)
                            @php
                                $yaTieneGestion = $historialAgrupado->has($paso->id);
                                $status = 'future';
                                if ($yaTieneGestion) { $status = 'completed'; }
                                elseif (!$siguientePasoEncontrado) { $status = 'current'; $siguientePasoEncontrado = true; }

                                $esResponsableEnEstePaso = false;
                                $usuariosList = [];
                                foreach($paso->usuariosAsignados as $asignacion) {
                                    $uid = $asignacion->usuario->id ?? $asignacion->user_id ?? $asignacion->usuario_id;
                                    if($uid == auth()->id()) $esResponsableEnEstePaso = true;
                                    $u = $asignacion->usuario;
                                    $usuariosList[] = [
                                        'initial' => $u ? substr($u->name, 0, 1) : '?',
                                        'name' => $u ? $u->name : 'N/A',
                                        'status' => $u && in_array($u->id, $participantesIds) ? 'success' : 'danger'
                                    ];
                                }
                            @endphp
                            {
                                id_proceso: "{{ $paso->id }}",
                                nombre: "{{ $paso->nombre }}",
                                index: {{ $index + 1 }},
                                status: "{{ $status }}", 
                                es_responsable: {{ $esResponsableEnEstePaso ? 'true' : 'false' }},
                                usuarios: @json($usuariosList)
                            },
                        @endforeach
                    ]
                },
            @endforeach
        };

        function abrirModalRuta(idRadicado) {
            const data = dataRutas[idRadicado];
            if(!data) return;

            document.getElementById('ruta_radicado_lbl').textContent = "#" + idRadicado;
            const container = document.getElementById('timeline_steps_container');
            container.innerHTML = ''; 

            data.pasos.forEach((paso, idx) => {
                let badgeClass = 'bg-light text-muted border';
                let iconHtml = `${paso.index}`;
                let textClass = 'text-muted';

                if(paso.status === 'completed') { badgeClass = 'bg-pastel-success text-success'; iconHtml = '<i class="bi bi-check2"></i>'; textClass = 'text-success fw-bold'; }
                else if(paso.status === 'current') { badgeClass = 'bg-pastel-warning text-dark'; iconHtml = '<i class="bi bi-hourglass-split"></i>'; textClass = 'text-dark fw-bold'; }

                let line = idx < data.pasos.length - 1 ? `<div class="route-line"></div>` : '';
                let avatars = paso.usuarios.map(u => `
                    <div class="avatar-circle-sm position-relative" title="${u.name}" data-bs-toggle="tooltip" style="border: 2px solid ${u.status === 'success' ? '#a5d6a7' : '#ef9a9a'} !important;">
                        ${u.initial}
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-${u.status} p-1" style="width: 8px; height: 8px;"></span>
                    </div>
                `).join('');

                container.innerHTML += `
                    <div class="d-flex mb-4 position-relative">
                        <div class="me-3 position-relative" style="z-index: 2;">
                            <span class="badge rounded-circle ${badgeClass} p-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">${iconHtml}</span>
                            ${line}
                        </div>
                        <div class="w-100 pb-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="small ${textClass}">${paso.nombre}</div>
                                    <div class="d-flex align-items-center mt-2 flex-wrap gap-1">${avatars}</div>
                                </div>
                                ${paso.es_responsable ? `<button onclick="event.stopPropagation(); saltarAGestionDirecta('${idRadicado}', '${data.asunto}', '${paso.id_proceso}')" class="btn btn-sm btn-primary rounded-pill px-3 py-1 shadow-sm"><i class="bi bi-pencil-fill me-1"></i>Gestionar</button>` : ''}
                            </div>
                        </div>
                    </div>`;
            });
            new bootstrap.Modal(document.getElementById('modalRuta')).show();
        }

        function saltarAGestionDirecta(idRadicado, asunto, idProceso) {
            const modalRutaEl = document.getElementById('modalRuta');
            const modalRuta = bootstrap.Modal.getInstance(modalRutaEl);
            if(modalRuta) modalRuta.hide();

            abrirModalRapido(idRadicado, asunto);
            
            setTimeout(() => {
                const select = document.getElementById('select_etapa_proceso_visual');
                if(select) {
                    select.value = idProceso;
                    // BLOQUEO ESTRICTO DE ETAPA
                    select.disabled = true; 
                    select.style.backgroundColor = "#f3f4f6"; 
                    select.style.cursor = "not-allowed";
                    cargarEstadosDeEtapa(idProceso);
                }
            }, 150);
        }

        function abrirModalRapido(idRadicado, asunto) {
            document.getElementById('formGestionRapida').reset();
            const select = document.getElementById('select_etapa_proceso_visual');
            select.disabled = false;
            select.style.backgroundColor = "white";
            select.style.cursor = "default";

            document.getElementById('modal_id_correspondencia').value = idRadicado;
            document.getElementById('modal_radicado_lbl').textContent = "#" + idRadicado;
            document.getElementById('modal_asunto_lbl').textContent = asunto;
            document.getElementById('container_estados_checkin').innerHTML = "";
            document.getElementById('input_estado_id_hidden').value = "";
            document.getElementById('container_archivos_dinamicos').innerHTML = '<span class="text-muted small px-2 italic">Seleccione etapa para ver requisitos.</span>';
            
            new bootstrap.Modal(document.getElementById('modalSeguimiento')).show();
        }

        function cargarEstadosDeEtapa(idProceso) {
            const containerEstados = document.getElementById('container_estados_checkin');
            const inputEstado = document.getElementById('input_estado_id_hidden');
            document.getElementById('input_etapa_proceso_hidden').value = idProceso;
            containerEstados.innerHTML = ""; 
            if(!idProceso) return;

            // ESTADOS
            const estados = estadosPorProceso[idProceso] || [];
            if(estados.length > 0) {
                estados.forEach(est => {
                    const div = document.createElement('div');
                    div.className = 'chelin-item'; div.textContent = est.text;
                    div.onclick = () => {
                        containerEstados.querySelectorAll('.chelin-item').forEach(e => e.classList.remove('active'));
                        div.classList.add('active'); inputEstado.value = est.val;
                    };
                    containerEstados.appendChild(div);
                });
            } else { containerEstados.innerHTML = '<span class="text-muted small italic px-2">Sin estados requeridos.</span>'; }

            // ARCHIVOS DINÁMICOS
            let numArchivos = numeroArchivosPorProceso[idProceso] || 0;
            let nombresArchivos = tiposArchivosPorProceso[idProceso] || [];
            let containerArchivos = document.getElementById('container_archivos_dinamicos');
            let labelBadge = document.getElementById('label_req_archivos');
            containerArchivos.innerHTML = ''; 

            if(numArchivos > 0) {
                labelBadge.className = "badge bg-danger rounded-pill";
                labelBadge.textContent = numArchivos + " Obligatorio(s)";
                for(let i = 1; i <= numArchivos; i++) {
                    let nombreDoc = nombresArchivos[i-1] ? nombresArchivos[i-1] : `Documento #${i}`;
                    containerArchivos.innerHTML += `
                        <div class="position-relative d-flex align-items-center p-2 rounded-4 border border-dashed bg-white">
                            <div class="p-2 bg-light rounded-circle text-danger me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                <i class="bi bi-file-earmark-pdf-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold small text-uppercase" style="font-size:0.65rem;">${nombreDoc} *</h6>
                                <input type="file" name="documento_arc[]" class="form-control form-control-sm mt-1 border-0 bg-light" required>
                            </div>
                        </div>`;
                }
            } else {
                labelBadge.className = "badge bg-pastel-primary text-primary rounded-pill";
                labelBadge.textContent = "Opcional";
                containerArchivos.innerHTML = `
                    <div class="upload-box position-relative d-flex align-items-center p-2 rounded-4 border border-dashed bg-white">
                        <i class="bi bi-cloud-arrow-up-fill fs-4 text-primary me-3 ps-2"></i>
                        <div><h6 class="mb-0 fw-bold small">Subir soportes</h6><p class="text-muted small mb-0" style="font-size:0.65rem;">PDF/Imágenes</p></div>
                        <input type="file" name="documento_arc[]" class="position-absolute w-100 h-100 top-0 start-0 opacity-0 cursor-pointer" multiple>
                    </div>`;
            }
        }
    </script>
</x-base-layout>