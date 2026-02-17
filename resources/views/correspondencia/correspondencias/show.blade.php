<x-base-layout>
    <div class="app-container py-4">
        {{-- HEADER CON ACCIONES --}}
        <header class="main-header d-flex justify-content-between align-items-center mb-4 pb-3">
            <div>
                <h1 class="page-title h3 fw-bold mb-1" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="bottom" 
                    title="NÚMERO DE RADICADO DE SOLICITUD" 
                    style="cursor: help;">
                    <i class="bi bi-file-earmark-text text-primary me-2"></i>RDS #{{ $correspondencia->id_radicado }}
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('correspondencia.correspondencias.index') }}" class="text-decoration-none text-muted">Solicitudes</a></li>
                        <li class="breadcrumb-item active fw-medium">Detalle de Gestión</li>
                    </ol>
                </nav>
            </div>
            <div class="header-actions d-flex gap-2">
                <a href="{{ route('correspondencia.correspondencias.edit', $correspondencia) }}" 
                   class="btn btn-outline-secondary rounded-pill px-3"
                   data-bs-toggle="tooltip" 
                   data-bs-placement="bottom" 
                   title="Observación: Funcionalidad de uso exclusivo del usuario final encargado de la gestión y validación de solicitudes en estado completamente cerrado.">
                    <i class="bi bi-pencil-square"></i> Finalización del Proceso
                </a>
            </div>
        </header>

        <div class="row g-4">
            {{-- COLUMNA IZQUIERDA: INFORMACIÓN Y TRAZABILIDAD --}}
            <div class="col-lg-8">
                {{-- CARD PRINCIPAL (INFORMACIÓN) --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <span class="badge bg-pastel-primary mb-2 px-3 py-2 rounded-pill" style="font-size: 0.7rem; letter-spacing: 0.5px; font-weight: 700; text-transform: uppercase;">Asunto del Documento</span>
                                <h4 class="fw-bold text-dark mb-0">{{ $correspondencia->asunto }}</h4>
                            </div>
                            <span class="badge rounded-pill px-4 py-2 bg-pastel-info fw-bold">
                                {{ $correspondencia->estado->nombre ?? 'Estado N/D' }}
                            </span>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="text-muted small d-block mb-1"><i class="bi bi-person me-1"></i>Remitente</label>
                                <span class="fw-bold text-dark">{{ $correspondencia->remitente->nom_ter ?? 'N/A' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block mb-1"><i class="bi bi-person-badge me-1"></i>Registrado por</label>
                                <span class="fw-bold text-dark">{{ $correspondencia->usuario->name ?? 'Sin asignar' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block mb-1"><i class="bi bi-calendar-event me-1"></i>Fecha Radicación</label>
                                <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($correspondencia->fecha_solicitud)->format('d/m/Y') }}</span>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small d-block mb-1">Observaciones Iniciales</label>
                                <div class="p-3 rounded-4 italic text-muted border-start border-4 border-primary-subtle" style="background-color: #f8f9fa;">
                                    {{ $correspondencia->observacion_previa ?? 'Sin observaciones adicionales.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TRAZABILIDAD (HISTORIAL - RUTA DE GESTIÓN TIMELINE MEJORADA) --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0 text-dark">Historial de la Ruta</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="management-timeline">
                            @forelse($correspondencia->procesos->sortByDesc('fecha_gestion') as $registro)
                                <div class="timeline-item ps-4 pb-3 position-relative border-start border-2 {{ $registro->finalizado ? 'border-danger-subtle' : 'border-primary-subtle' }}" style="margin-left: 12px;">
                                    
                                    {{-- Punto de la Ruta (Pastel) --}}
                                    <div class="timeline-dot position-absolute rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                                         style="width: 24px; height: 24px; left: -13px; top: 0; background-color: {{ $registro->finalizado ? '#ffcdd2' : '#bbdefb' }}; border: 2px solid #fff; z-index: 2;">
                                        <i class="bi {{ $registro->finalizado ? 'bi-lock-fill text-danger' : 'bi-check2 text-primary' }}" style="font-size: 0.75rem;"></i>
                                    </div>

                                    {{-- Contenedor de la Gestión (Card Clickable & Angosta) --}}
                                    {{-- Nota: py-2 px-3 hace la tarjeta más angosta verticalmente --}}
                                    <div class="timeline-card clickable-row bg-white py-2 px-3 rounded-4 border shadow-sm transition-all"
                                         data-proceso="{{ $registro->proceso->nombre ?? 'Gestión Directa' }}"
                                         data-estado="{{ ucfirst(str_replace('_', ' ', $registro->estado)) }}"
                                         data-usuario="{{ $registro->usuario->name ?? 'N/D' }}"
                                         data-fecha="{{ \Carbon\Carbon::parse($registro->fecha_gestion)->format('d/m/Y H:i') }}"
                                         data-observacion="{{ $registro->observacion }}"
                                         data-notificado="{{ $registro->notificado_email ? 'Sí' : 'No' }}"
                                         data-finalizado="{{ $registro->finalizado ? 'Sí' : 'No' }}"
                                         style="cursor: pointer;">
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div class="d-flex align-items-center gap-2">
                                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.8rem;">
                                                    {{ $registro->proceso->nombre ?? 'Gestión Directa' }}
                                                </h6>
                                                {{-- Badge de Estado Pastel --}}
                                                <span class="badge rounded-pill {{ $registro->finalizado ? 'bg-pastel-danger' : 'bg-pastel-primary' }} text-uppercase" style="font-size: 0.6rem; letter-spacing: 0.3px;">
                                                    {{ str_replace('_', ' ', $registro->estado) }}
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted" style="font-size: 0.7rem;">
                                                    {{ \Carbon\Carbon::parse($registro->fecha_gestion)->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                        </div>

                                        <div class="py-1">
                                            <p class="text-muted small mb-0 italic" style="line-height: 1.3; font-size: 0.75rem;">
                                                "{{ Str::limit($registro->observacion, 120) }}"
                                            </p>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mt-2 pt-1 border-top border-light">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle-sm bg-light text-secondary border me-2" style="width: 20px; height: 20px; font-size: 0.6rem;">
                                                    {{ strtoupper(substr($registro->usuario->name ?? 'U', 0, 1)) }}
                                                </div>
                                                <span class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">
                                                    {{ $registro->usuario->name ?? 'N/D' }}
                                                </span>
                                            </div>
                                            
                                            <div class="d-flex gap-2 align-items-center">
                                                @if($registro->notificado_email)
                                                    <i class="bi bi-envelope-check text-success opacity-75" style="font-size: 0.9rem;" title="Correo Enviado" data-bs-toggle="tooltip"></i>
                                                @endif
                                                @if($registro->documento_arc)
                                                    <a href="{{ $registro->getFile($registro->documento_arc) }}" target="_blank" class="text-danger opacity-75 hover-opacity-100" onclick="event.stopPropagation();">
                                                        <i class="bi bi-file-earmark-pdf-fill" style="font-size: 1rem;"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="bi bi-journal-x text-muted opacity-25" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2">No hay gestiones registradas en la ruta.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: INDICADORES Y FLUJO --}}
            <div class="col-lg-4">
                @php                    
                    // Lógica de colores Pastel para TRD
                    if ($diferenciatrd->y > 3) {
                        // Azul Pastel
                        $bgColor = '#e3f2fd'; $textColor = '#1565c0'; $borderColor = '#bbdefb';
                    } elseif ($diferenciatrd->y <= 2 || $diferenciatrd->y >= 0) {
                        // Rojo Pastel
                        $bgColor = '#ffebee'; $textColor = '#c62828'; $borderColor = '#ffcdd2';
                    } else {
                        // Amarillo Pastel
                        $bgColor = '#fffde7'; $textColor = '#f9a825'; $borderColor = '#fff9c4';
                    }
                @endphp
                
                <div class="card border-0 shadow-sm mb-4" 
                    style="border-radius: 24px; background-color: {{ $bgColor }}; border: 1px solid {{ $borderColor }} !important; overflow: hidden;">
                    
                    <div class="card-header border-0 bg-transparent p-3 text-center" 
                        style="cursor: pointer;" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#collapseTRD" 
                        aria-expanded="false">
                        <span class="badge rounded-pill px-3 py-2" style="background-color: rgba(255,255,255,0.4); color: {{ $textColor }}; font-size: 0.65rem; letter-spacing: 0.5px;">
                            <i class="bi bi-info-circle-fill me-1"></i> CONTROL DE GESTIÓN DOCUMENTAL
                            <i class="bi bi-chevron-down ms-2 small opacity-50"></i>
                        </span>
                    </div>

                    <div class="collapse" id="collapseTRD">
                        <div class="card-body p-4 pt-0">
                            <div class="text-center mb-4">
                                <h6 class="text-uppercase fw-bolder mb-3" style="color: {{ $textColor }}; opacity: 0.9; font-size: 0.75rem;">
                                    Tiempo restante de conservación
                                </h6>
                                
                                <div class="row g-2 justify-content-center">
                                    <div class="col-4">
                                        <div class="p-2 rounded-3" style="background: rgba(255,255,255,0.4);">
                                            <div class="h3 fw-bold m-0" style="color: {{ $textColor }};">{{$diferenciatrd->y}}</div>
                                            <div class="small opacity-75" style="color: {{ $textColor }}; font-size: 0.6rem;">AÑOS</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 rounded-3" style="background: rgba(255,255,255,0.4);">
                                            <div class="h3 fw-bold m-0" style="color: {{ $textColor }};">{{$diferenciatrd->m}}</div>
                                            <div class="small opacity-75" style="color: {{ $textColor }}; font-size: 0.6rem;">MESES</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 rounded-3" style="background: rgba(255,255,255,0.4);">
                                            <div class="h3 fw-bold m-0" style="color: {{ $textColor }};">{{$diferenciatrd->d}}</div>
                                            <div class="small opacity-75" style="color: {{ $textColor }}; font-size: 0.6rem;">DÍAS</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4 text-center px-2">
                                <p style="color: {{ $textColor }}; font-size: 0.85rem; line-height: 1.4; opacity: 0.9;">
                                    Este documento está sujeto a los tiempos de retención definidos para la serie 
                                    <strong>"{{ $correspondencia->trd->serie_documental ?? 'General' }}"</strong>. 
                                </p>
                            </div>
                            
                            <div class="p-3 rounded-4" style="background-color: rgba(255,255,255,0.4); backdrop-filter: blur(4px); border: 1px solid rgba(255,255,255,0.3);">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2" style="border-bottom: 1px dashed {{ $borderColor }}; opacity: 0.8;">
                                    <span class="small fw-bold" style="color: {{ $textColor }};"><i class="bi bi-shield-check me-1"></i> Disposición:</span>
                                    <span class="small fw-bold text-uppercase" style="color: {{ $textColor }};">Archivo Central</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold" style="color: {{ $textColor }};"><i class="bi bi-calendar-event me-1"></i> Límite TRD:</span>
                                    <span class="badge shadow-sm" style="background-color: {{ $textColor }}; color: {{ $bgColor }}; border-radius: 8px; font-size: 0.85rem;">
                                        {{ $limite->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RUTA DEL PROCESO (PARTICIPANTES) --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-people me-2 text-primary"></i>Ruta y Participantes</h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline-steps">
                            @php 
                                $historialAgrupado = $correspondencia->procesos->groupBy('id_proceso');
                                $siguientePasoEncontrado = false;
                            @endphp
                            
                            @forelse($procesos_disponibles as $index => $paso)
                                @php
                                    $logsDeEstaEtapa = $historialAgrupado->get($paso->id);
                                    $yaTieneGestion = $logsDeEstaEtapa && $logsDeEstaEtapa->count() > 0;
                                    
                                    $idsQueYaGestionaron = [];
                                    if($logsDeEstaEtapa) {
                                        foreach($logsDeEstaEtapa as $log) {
                                            $idLog = $log->usuario->id ?? $log->user_id ?? $log->usuario_id ?? null;
                                            if($idLog) {
                                                $idsQueYaGestionaron[] = (int)$idLog;
                                            }
                                        }
                                    }

                                    // Colores Pastel para la Ruta
                                    if ($yaTieneGestion) {
                                        $badgeClass = 'bg-pastel-success text-success shadow-sm'; // Verde Pastel
                                        $textClass = 'text-success fw-bold';
                                        $iconClass = 'bi-check2';
                                    } elseif (!$siguientePasoEncontrado) {
                                        $badgeClass = 'bg-pastel-warning text-dark shadow-sm'; // Amarillo Pastel
                                        $textClass = 'text-dark fw-bold';
                                        $iconClass = 'bi-hourglass-split';
                                        $siguientePasoEncontrado = true;
                                    } else {
                                        $badgeClass = 'bg-light text-muted border';
                                        $textClass = 'text-muted';
                                        $iconClass = '';
                                    }

                                    // Lógica de Permisos (Botón)
                                    $usuarioLogueadoId = (int)auth()->id();
                                    $esResponsable = false;
                                    if($paso->usuariosAsignados) {
                                        foreach($paso->usuariosAsignados as $asignacion) {
                                            $uid = (int)($asignacion->usuario->id ?? $asignacion->user_id ?? $asignacion->usuario_id);
                                            if($uid == $usuarioLogueadoId) {
                                                $esResponsable = true;
                                                break;
                                            }
                                        }
                                    }
                                @endphp

                                <div class="d-flex mb-4 position-relative">
                                    <div class="me-3 position-relative" style="z-index: 2;">
                                        <span class="badge rounded-circle {{ $badgeClass }} p-2" 
                                              style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                            @if($iconClass)
                                                <i class="bi {{ $iconClass }}"></i>
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </span>
                                        @if(!$loop->last)
                                            <div class="position-absolute start-50 translate-middle-x" style="width: 2px; height: 100%; top: 32px; z-index: -1; background-color: #f0f2f5;"></div>
                                        @endif
                                    </div>
                                    
                                    <div class="w-100 pb-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="small {{ $textClass }}">
                                                    {{ $paso->nombre }}
                                                </div>

                                                <div class="d-flex align-items-center mt-2 flex-wrap gap-1">
                                                    @if($paso->usuariosAsignados && $paso->usuariosAsignados->count() > 0)
                                                        @foreach($paso->usuariosAsignados as $asignacion)
                                                            @php
                                                                $uidAsignado = (int)($asignacion->usuario->id ?? $asignacion->user_id ?? $asignacion->usuario_id);
                                                                $participo = in_array($uidAsignado, $idsQueYaGestionaron);
                                                                // Bordes Pastel para avatares
                                                                $borderColor = $participo ? '#a5d6a7' : '#ef9a9a'; 
                                                                $tooltipText = $participo ? 'Ya gestionó' : 'Pendiente';
                                                            @endphp
                                                            
                                                            <div class="avatar-circle-sm position-relative" 
                                                                 title="{{ $asignacion->usuario->name ?? 'Usuario' }} - {{ $tooltipText }}" 
                                                                 data-bs-toggle="tooltip"
                                                                 style="border: 2px solid {{ $borderColor }} !important;">
                                                                {{ strtoupper(substr($asignacion->usuario->name ?? 'U', 0, 1)) }}
                                                                <span class="position-absolute top-0 start-100 translate-middle badge border border-light rounded-circle bg-{{ $participo ? 'success' : 'danger' }} p-1" style="width: 8px; height: 8px;"></span>
                                                            </div>
                                                        @endforeach
                                                        <span class="ms-2 text-muted" style="font-size: 0.65rem;">Asignados</span>
                                                    @else
                                                        <span class="text-muted italic" style="font-size: 0.65rem;">Sin responsables fijos</span>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($esResponsable)
                                                <button type="button" 
                                                        class="btn btn-sm btn-primary shadow-sm rounded-pill px-3 py-1 ms-2" 
                                                        style="font-size: 0.7rem;"
                                                        onclick="abrirModalGestion({{ $paso->id }})">
                                                    <i class="bi bi-pencil-fill me-1"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted small text-center">Sin flujo definido.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL 1: REGISTRAR GESTIÓN --}}
    <div class="modal fade" id="modalSeguimiento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 25px;">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-plus-circle-fill text-primary me-2"></i>Registrar Gestión
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('correspondencia.correspondencias-procesos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_correspondencia" value="{{ $correspondencia->id_radicado }}">
                    <input type="hidden" name="id_proceso" id="input_etapa_proceso_hidden">

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Etapa del Proceso</label>
                                <select id="select_etapa_proceso_visual" class="form-select border-0 bg-secondary-subtle rounded-3 py-2" disabled>
                                    <option value="">Seleccione etapa...</option>
                                    @foreach($procesos_disponibles as $proceso)
                                        <option value="{{ $proceso->id }}">{{ $proceso->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Nuevo Estado</label>
                                <div id="container_estados_checkin" class="d-flex flex-wrap gap-2">
                                    <span class="text-muted small italic">Seleccione una etapa primero...</span>
                                </div>
                                <input type="hidden" name="estado_id" id="input_estado_id_hidden" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Observaciones</label>
                                <textarea name="observacion" class="form-control border-0 bg-light rounded-3" rows="3" placeholder="Detalles de la gestión..." required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Soporte (PDF/Img)</label>
                                <input type="file" name="documento_arc" class="form-control border-0 bg-light rounded-3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Fecha</label>
                                <input type="datetime-local" name="fecha_gestion" class="form-control border-0 bg-light rounded-3" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                            </div>
                            <div class="col-12 mt-4">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch p-3 border rounded-3 bg-light w-100 d-flex align-items-center justify-content-between">
                                            <div class="ms-1">
                                                <label class="form-check-label fw-bold d-block mb-0" for="notifCheck">Notificar Remitente</label>
                                                <small class="text-muted" style="font-size: 0.7rem;">Enviar correo automático</small>
                                            </div>
                                            <input class="form-check-input ms-0" style="width: 2.5em; height: 1.25em;" type="checkbox" name="notificado_email" value="1" id="notifCheck">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch p-3 border rounded-3 w-100 d-flex align-items-center justify-content-between" style="background-color: #fff5f5; border-color: #feb2b2 !important;">
                                            <div class="ms-1">
                                                <label class="form-check-label fw-bold text-danger d-block mb-0" for="finalizadoCheck">Finalización de Solicitud</label>
                                                <small class="text-danger opacity-75" style="font-size: 0.7rem;">Cierra el ciclo del radicado</small>
                                            </div>
                                            <input class="form-check-input ms-0" style="width: 2.5em; height: 1.25em;" type="checkbox" name="finalizado" value="1" id="finalizadoCheck">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pb-4 px-4">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow fw-bold">
                            <i class="bi bi-save me-2"></i>Guardar Gestión
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 2: VER DETALLES --}}
    <div class="modal fade" id="modalDetalleHistorial" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 25px;">
                <div class="modal-header bg-pastel-primary border-0 py-3 px-4" style="border-radius: 25px 25px 0 0;">
                    <h5 class="modal-title fw-bold text-primary">Detalle de la Gestión</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="text-muted small d-block mb-1 text-uppercase fw-bold">Observación</label>
                        <div id="det-observacion" class="p-3 rounded-4 bg-light text-dark border-start border-4 border-primary shadow-sm"></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6"><label class="text-muted small d-block mb-1">Etapa</label><span id="det-proceso" class="fw-bold text-primary"></span></div>
                        <div class="col-6"><label class="text-muted small d-block mb-1">Estado</label><span id="det-estado" class="badge bg-pastel-info text-info rounded-pill px-3 text-uppercase"></span></div>
                        <div class="col-6"><label class="text-muted small d-block mb-1">Responsable</label><span id="det-usuario" class="fw-bold"></span></div>
                        <div class="col-6"><label class="text-muted small d-block mb-1">Fecha y Hora</label><span id="det-fecha" class="text-muted small d-block"></span></div>
                        <div class="col-6"><label class="text-muted small d-block mb-1">Notificado</label><span id="det-notificado" class="fw-bold"></span></div>
                        <div class="col-6"><label class="text-muted small d-block mb-1">Finalización de Solicitud</label><span id="det-finalizado" class="fw-bold"></span></div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Regresar</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        body { background-color: #f8faff; }
        
        /* --- COLORES PASTEL --- */
        .bg-pastel-primary { background-color: #e3f2fd; color: #1565c0; }
        .bg-pastel-danger { background-color: #ffebee; color: #c62828; }
        .bg-pastel-success { background-color: #e8f5e9; color: #2e7d32; }
        .bg-pastel-warning { background-color: #fffde7; color: #f9a825; }
        .bg-pastel-info { background-color: #e0f7fa; color: #006064; }

        .bg-secondary-subtle { background-color: #f1f3f5; color: #6c757d; cursor: not-allowed; } 
        
        .main-header { border-bottom: 1px solid #e9ecef; }
        
        /* Avatar Styles */
        .avatar-circle-sm {
            width: 24px; height: 24px; background-color: #fff; color: #495057;
            border: 2px solid #e9ecef; border-radius: 50%; display: flex;
            align-items: center; justify-content: center; font-size: 0.65rem;
            font-weight: 700; margin-left: -8px; transition: all 0.2s ease; cursor: default;
        }
        .avatar-circle-sm:first-child { margin-left: 0; }
        .avatar-circle-sm:hover { transform: translateY(-3px); z-index: 10; }
        
        /* Timeline Specific Styles */
        .management-timeline .timeline-item:last-child { border-start-color: transparent !important; }
        
        /* Tarjetas Angostas y Transiciones */
        .timeline-card { 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            border-color: #f1f3f5 !important;
        }
        .timeline-card:hover { 
            transform: translateX(5px); 
            border-color: #bbdefb !important; 
            background-color: #f1f8ff !important; 
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
        }
        
        .italic { font-style: italic; }
        .transition-all { transition: all 0.2s ease-in-out; }
        .clickable-row { cursor: pointer; }

        .form-select:focus, .form-control:focus { background-color: #fff; box-shadow: 0 0 0 0.25rem rgba(227, 242, 253, 0.5); border: 1px solid #90caf9; }
   
        #container_estados_checkin button {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-width: 2px;
        }

        #container_estados_checkin button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(13, 110, 253, 0.15);
        }

        /* Estilo para el botón cuando está seleccionado */
        .btn-primary {
            background-color: #1976d2; border-color: #1976d2;
        }
        .btn-primary:hover {
            background-color: #1565c0; border-color: #1565c0;
        }
    </style>

    <script>
        // -------------------------------------------------------------
        // MAPA DE ESTADOS POR PROCESO
        // -------------------------------------------------------------
        const estadosPorProceso = {
            @foreach($procesos_disponibles as $proceso)
                {{ $proceso->id }}: [
                    @foreach($proceso->estadosProcesos as $estadoProc)
                        @if($estadoProc->estado) 
                        {
                            val: "{{ $estadoProc->estado->id }}", 
                            text: "{{ $estadoProc->estado->nombre }}"
                        },
                        @endif
                    @endforeach
                ],
            @endforeach
        };

        function abrirModalGestion(idProceso) {
            var myModal = new bootstrap.Modal(document.getElementById('modalSeguimiento'));
            
            // IDs de los elementos
            var inputProcesoHidden = document.getElementById('input_etapa_proceso_hidden');
            var selectVisual = document.getElementById('select_etapa_proceso_visual');
            var containerEstados = document.getElementById('container_estados_checkin');
            var inputEstadoHidden = document.getElementById('input_estado_id_hidden');

            // 1. Asignar Proceso
            if(inputProcesoHidden) inputProcesoHidden.value = idProceso;
            if(selectVisual) selectVisual.value = idProceso;

            // 2. Limpiar contenedor y resetear input de estado
            containerEstados.innerHTML = '';
            inputEstadoHidden.value = '';

            // 3. Obtener estados y generar botones
            var estadosDisponibles = estadosPorProceso[idProceso] || [];

            if (estadosDisponibles.length > 0) {
                estadosDisponibles.forEach(function(estado) {
                    // Crear el "botón" tipo check-in
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-outline-primary btn-sm rounded-pill px-3 py-2 fw-bold flex-grow-1';
                    btn.style.fontSize = '0.75rem';
                    btn.textContent = estado.text;
                    
                    // Evento al hacer clic
                    btn.onclick = function() {
                        // Quitar clase "active" de todos los hermanos
                        containerEstados.querySelectorAll('button').forEach(b => {
                            b.classList.remove('btn-primary', 'text-white');
                            b.classList.add('btn-outline-primary');
                        });
                        // Marcar este como activo
                        btn.classList.remove('btn-outline-primary');
                        btn.classList.add('btn-primary', 'text-white');
                        
                        // Guardar el valor en el input oculto para el Form
                        inputEstadoHidden.value = estado.val;
                    };

                    containerEstados.appendChild(btn);
                });
            } else {
                containerEstados.innerHTML = '<span class="text-danger small">Sin estados configurados</span>';
            }

            myModal.show();
        }

        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl) });

            // Lógica para abrir detalles desde la nueva Timeline
            const filas = document.querySelectorAll('.clickable-row');
            const modalDetalle = document.getElementById('modalDetalleHistorial');
            if (modalDetalle) {
                const modalInstance = new bootstrap.Modal(modalDetalle);
                filas.forEach(fila => {
                    fila.addEventListener('click', function() {
                        document.getElementById('det-proceso').textContent = this.getAttribute('data-proceso');
                        document.getElementById('det-estado').textContent = this.getAttribute('data-estado');
                        document.getElementById('det-usuario').textContent = this.getAttribute('data-usuario');
                        document.getElementById('det-fecha').textContent = this.getAttribute('data-fecha');
                        document.getElementById('det-observacion').textContent = this.getAttribute('data-observacion') || 'Sin detalles registrados.';
                        document.getElementById('det-notificado').textContent = this.getAttribute('data-notificado');
                        document.getElementById('det-finalizado').textContent = this.getAttribute('data-finalizado');
                        modalInstance.show();
                    });
                });
            }
        });
    </script>
</x-base-layout>