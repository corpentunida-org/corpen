<x-base-layout>
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
        .table-minimal tbody tr:hover td { background-color: #fafafa; }

        /* Avatares y Badges */
        .avatar-tiny { width: 28px; height: 28px; border-radius: 8px; background: var(--pastel-purple); color: var(--text-purple); display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; }
        .badge-pill { padding: 2px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 500; display: inline-flex; align-items: center; gap: 4px; }
        .badge-blue { background: var(--pastel-blue); color: var(--text-blue); }
        .badge-green { background: var(--pastel-green); color: var(--text-green); }
        
        /* Botones */
        .btn-ghost { color: var(--text-light); padding: 4px; border-radius: 6px; transition: all 0.2s; cursor: pointer; border: none; background: transparent; }
        .btn-ghost:hover { background: var(--pastel-blue); color: var(--text-blue); }
        .btn-soft-primary { background: var(--pastel-blue); color: var(--text-blue); font-weight: 600; font-size: 0.85rem; padding: 6px 16px; border-radius: 8px; border: 1px solid transparent; transition: all 0.2s; }
        .btn-soft-primary:hover { background: #dbeafe; transform: translateY(-1px); }

        /* --- ESTILOS CHELINS --- */
        .chelin-item { display: inline-block; padding: 8px 16px; border-radius: 50px; border: 1px solid #dee2e6; background-color: white; color: #6c757d; font-size: 0.75rem; font-weight: 700; cursor: pointer; transition: all 0.2s; user-select: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        .chelin-item:hover { background-color: #f8f9fa; transform: translateY(-1px); }
        .chelin-item.active { background-color: var(--pastel-blue); color: var(--text-blue); border-color: #90caf9; box-shadow: 0 4px 6px rgba(13, 110, 253, 0.15); }
        
        .upload-box:hover { background-color: var(--pastel-blue) !important; border-color: var(--text-blue) !important; }
        .action-card { cursor: pointer; transition: all 0.2s; }
        .modal-content { border-radius: 24px; border: none; overflow: hidden; }

        /* --- ESTILOS DE LA RUTA Y PARTICIPANTES --- */
        .avatar-circle-sm { width: 24px; height: 24px; background-color: #fff; color: #495057; border: 2px solid #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 700; margin-left: -8px; transition: all 0.2s ease; cursor: default; }
        .avatar-circle-sm:first-child { margin-left: 0; }
        .avatar-circle-sm:hover { transform: translateY(-3px); z-index: 10; }
        
        .route-line { position: absolute; start: 50%; transform: translateX(-50%); width: 2px; height: 100%; top: 32px; z-index: -1; background-color: #f0f2f5; }
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
        <div class="card-clean p-2 mb-3 d-flex align-items-center gap-2">
            <form action="{{ route('correspondencia.correspondencias.index') }}" method="GET" class="d-flex w-100 align-items-center gap-2">
                <div class="position-relative flex-grow-1" style="max-width: 300px;">
                    <i class="bi bi-search position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 0.8rem;"></i>
                    <input type="text" name="search" class="form-control search-pill ps-5 w-100" placeholder="Buscar..." value="{{ request('search') }}">
                </div>
                <select name="estado" class="form-select search-pill border-0" style="width: auto; cursor: pointer;" onchange="this.form.submit()">
                    <option value="">Estado: Todos</option>
                    @foreach($estados as $est)
                        <option value="{{ $est->id_estado }}" {{ request('estado') == $est->id_estado ? 'selected' : '' }}>{{ $est->nombre }}</option>
                    @endforeach
                </select>
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
                        <tr>
                            <td><span class="fw-bold" style="color: #4b5563;">#{{ $corr->id_radicado }}</span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-truncate" style="max-width: 280px;" title="{{ $corr->asunto }}">{{ $corr->asunto }}</span>
                                    @if($corr->es_confidencial) <i class="bi bi-lock-fill text-danger opacity-50" title="Confidencial" data-bs-toggle="tooltip"></i> @endif
                                    @if($corr->documento_arc) <i class="bi bi-paperclip text-primary opacity-50" title="Tiene adjunto" data-bs-toggle="tooltip"></i> @endif
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
                                <div class="d-flex justify-content-end gap-1">
                                    <button type="button" class="btn-ghost text-primary" 
                                            onclick="abrirModalRuta('{{ $corr->id_radicado }}')"
                                            data-bs-toggle="tooltip" title="Ver Flujo / Gestionar">
                                        <i class="bi bi-people-fill"></i>
                                    </button>

                                    <a href="{{ route('correspondencia.correspondencias.show', $corr) }}" class="btn-ghost" data-bs-toggle="tooltip" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    @if(!$corr->finalizado)
                                        <a href="{{ route('correspondencia.correspondencias.edit', $corr) }}" class="btn-ghost" data-bs-toggle="tooltip" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted opacity-50"><i class="bi bi-inbox fs-4 d-block mb-1"></i> Sin registros</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($correspondencias->hasPages())
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
                            {{-- SELECCIÓN DE ETAPA (BLOQUEABLE) --}}
                            <div class="col-12 bg-light rounded-4 p-3">
                                <label class="form-label fw-bold small text-dark mb-1">1. Etapa a Gestionar</label>
                                <select id="select_etapa_proceso_visual" class="form-select border-0 shadow-none py-2" style="font-weight: 600; background-color: white;" required onchange="cargarEstadosDeEtapa(this.value)">
                                    <option value="">-- Seleccione el paso del flujo --</option>
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

                            <div class="col-12">
                                <label class="fw-bold small text-dark d-block mb-1">Adjuntar Soporte</label>
                                <div class="upload-box position-relative d-flex align-items-center p-2 rounded-4 border border-dashed bg-white">
                                    <i class="bi bi-cloud-arrow-up-fill fs-4 text-primary me-3 ps-2"></i>
                                    <div>
                                        <h6 class="mb-0 fw-bold small">Subir archivo</h6>
                                        <p class="text-muted small mb-0" style="font-size: 0.7rem;">PDF o Imagen</p>
                                    </div>
                                    <input type="file" name="documento_arc" id="file_gestion" class="position-absolute w-100 h-100 top-0 start-0 opacity-0 cursor-pointer">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex gap-3">
                                    <label class="action-card d-flex align-items-center p-2 px-3 rounded-4 border flex-fill bg-white">
                                        <input type="checkbox" name="notificado_email" value="1" class="form-check-input me-2">
                                        <span class="small fw-bold text-dark">Notificar</span>
                                    </label>
                                    <label class="action-card d-flex align-items-center p-2 px-3 rounded-4 border border-danger-subtle bg-danger-subtle flex-fill">
                                        <input type="checkbox" name="finalizado" value="1" class="form-check-input me-2 border-danger">
                                        <span class="small fw-bold text-danger">Finalizar Etapa</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4 justify-content-between">
                        <button type="button" class="btn btn-link text-muted text-decoration-none fw-bold small" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-lg fw-bold">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // 1. DATA: Filtramos procesos según el FLUJO de cada correspondencia
        const dataRutas = {
            @foreach($correspondencias as $corr)
                "{{ $corr->id_radicado }}": {
                    asunto: "{{ addslashes(Str::limit($corr->asunto, 60)) }}",
                    pasos: [
                        @php
                            $historialAgrupado = $corr->procesos->groupBy('id_proceso');
                            $siguientePasoEncontrado = false;
                            $participantes = $corr->procesos->pluck('usuario_id')->merge($corr->procesos->pluck('user_id'))->filter()->unique()->toArray();
                            $procesosDelFlujo = $corr->flujo ? $corr->flujo->procesos->sortBy('id') : collect();
                        @endphp
                        @foreach($procesosDelFlujo as $index => $paso)
                            @php
                                $yaTieneGestion = $historialAgrupado->has($paso->id);
                                $status = 'future';
                                if ($yaTieneGestion) {
                                    $status = 'completed';
                                } elseif (!$siguientePasoEncontrado) {
                                    $status = 'current';
                                    $siguientePasoEncontrado = true;
                                }

                                $esResponsable = false;
                                $usuariosList = [];
                                foreach($paso->usuariosAsignados as $asignacion) {
                                    $u = $asignacion->usuario;
                                    if($u && $u->id == auth()->id()) $esResponsable = true;
                                    $usuariosList[] = [
                                        'initial' => $u ? substr($u->name, 0, 1) : '?',
                                        'name' => $u ? $u->name : 'N/A',
                                        'status' => $u && in_array($u->id, $participantes) ? 'success' : 'danger'
                                    ];
                                }
                            @endphp
                            {
                                id_proceso: "{{ $paso->id }}",
                                nombre: "{{ $paso->nombre }}",
                                index: {{ $index + 1 }},
                                status: "{{ $status }}", 
                                es_responsable: {{ $esResponsable ? 'true' : 'false' }},
                                usuarios: @json($usuariosList)
                            },
                        @endforeach
                    ]
                },
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
                                ${paso.status === 'current' && paso.es_responsable ? `<button onclick="saltarAGestion('${idRadicado}', '${data.asunto}', '${paso.id_proceso}')" class="btn btn-sm btn-primary rounded-pill px-3 py-1">Gestionar</button>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });

            new bootstrap.Modal(document.getElementById('modalRuta')).show();
        }

        // 4. PUENTE: SALTAR DE RUTA A GESTIÓN (BLOQUEA EL SELECT)
        function saltarAGestion(id, asunto, idProceso) {
            const modalRutaEl = document.getElementById('modalRuta');
            const modalRuta = bootstrap.Modal.getInstance(modalRutaEl);
            if(modalRuta) modalRuta.hide();

            setTimeout(() => {
                abrirModalRapido(id, asunto);
                const select = document.getElementById('select_etapa_proceso_visual');
                if(select) {
                    select.value = idProceso;
                    select.disabled = true; // Bloqueo visual
                    select.style.backgroundColor = "#f3f4f6"; // Estilo de bloqueado pero legible
                    select.dispatchEvent(new Event('change'));
                }
            }, 300);
        }

        function abrirModalRapido(idRadicado, asunto) {
            // Reset del formulario antes de mostrar
            const form = document.getElementById('formGestionRapida');
            form.reset();
            
            const select = document.getElementById('select_etapa_proceso_visual');
            select.disabled = false;
            select.style.backgroundColor = "white";

            document.getElementById('modal_id_correspondencia').value = idRadicado;
            document.getElementById('modal_radicado_lbl').textContent = "#" + idRadicado;
            document.getElementById('modal_asunto_lbl').textContent = asunto;
            document.getElementById('container_estados_checkin').innerHTML = "";
            document.getElementById('input_estado_id_hidden').value = "";

            new bootstrap.Modal(document.getElementById('modalSeguimiento')).show();
        }

        function cargarEstadosDeEtapa(idProceso) {
            const container = document.getElementById('container_estados_checkin');
            const inputEstado = document.getElementById('input_estado_id_hidden');
            document.getElementById('input_etapa_proceso_hidden').value = idProceso;
            container.innerHTML = ""; 

            if(!idProceso) return;

            (estadosPorProceso[idProceso] || []).forEach(est => {
                const div = document.createElement('div');
                div.className = 'chelin-item';
                div.textContent = est.text;
                div.onclick = () => {
                    container.querySelectorAll('.chelin-item').forEach(e => e.classList.remove('active'));
                    div.classList.add('active');
                    inputEstado.value = est.val;
                };
                container.appendChild(div);
            });
        }
    </script>
</x-base-layout>