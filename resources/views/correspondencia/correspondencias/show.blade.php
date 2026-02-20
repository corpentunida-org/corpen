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

                            {{-- ================================================================= --}}
                            {{-- VISUALIZACIÓN DEL ARCHIVO ADJUNTO INICIAL --}}
                            {{-- ================================================================= --}}
                            <div class="col-12">
                                <label class="text-muted small d-block mb-2 text-uppercase fw-bold"><i class="bi bi-paperclip me-1"></i>Solicitud Digital</label>
                                @if($correspondencia->documento_arc)
                                    <div class="d-flex align-items-center p-3 rounded-4 border border-primary-subtle bg-primary-subtle bg-opacity-10 transition-all hover-lift">
                                        <div class="me-3 bg-white p-2 rounded-circle text-primary shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                            <i class="bi bi-file-earmark-pdf-fill fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-primary">Documento Adjunto Disponible</h6>
                                            <small class="text-muted">Clic en el botón para visualizar el archivo original</small>
                                        </div>
                                        <a href="{{ $correspondencia->getFile($correspondencia->documento_arc) }}" target="_blank" class="btn btn-primary rounded-pill ms-auto px-4 fw-bold shadow-sm">
                                            <i class="bi bi-eye me-2"></i>Ver Documento
                                        </a>
                                    </div>
                                @else
                                    <div class="p-3 rounded-4 border border-dashed bg-light text-muted d-flex align-items-center">
                                        <i class="bi bi-file-earmark-x fs-4 me-3 opacity-50"></i>
                                        <span>No se cargó ningún soporte digital para esta solicitud.</span>
                                    </div>
                                @endif
                            </div>
                            {{-- ================================================================= --}}

                            <div class="col-12">
                                <label class="text-muted small d-block mb-1">Observaciones Iniciales</label>
                                <div class="p-3 rounded-4 italic text-muted border-start border-4 border-primary-subtle" style="background-color: #f8f9fa;">
                                    {{ $correspondencia->observacion_previa ?? 'Sin observaciones adicionales.' }}
                                </div>
                            </div>

                            {{-- RESOLUCIÓN FINAL (SI EXISTE) --}}
                            @if($correspondencia->finalizado && $correspondencia->final_descripcion)
                            <div class="col-12">
                                <label class="text-danger small d-block mb-1 fw-bold text-uppercase"><i class="bi bi-check-all me-1"></i>Resolución de cierre – usuario inicial o cierre abrupto</label>
                                <div class="p-3 rounded-4 text-dark border border-danger-subtle bg-danger-subtle bg-opacity-10">
                                    {{ $correspondencia->final_descripcion }}
                                </div>
                            </div>
                            @endif
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
                                                
                                                {{-- MODIFICACIÓN ARCHIVOS: Leer el array de URLs mediante el modelo --}}
                                                @php $urlsArchivos = $registro->getArchivosUrls(); @endphp
                                                @if(count($urlsArchivos) > 0)
                                                    <div class="d-flex gap-1">
                                                        @foreach($urlsArchivos as $archivo)
                                                            <a href="{{ $archivo['url'] }}" target="_blank" class="text-danger opacity-75 hover-opacity-100" title="{{ $archivo['nombre'] }}" onclick="event.stopPropagation();">
                                                                <i class="bi bi-file-earmark-pdf-fill" style="font-size: 1rem;"></i>
                                                            </a>
                                                        @endforeach
                                                    </div>
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
                        $bgColor = '#e3f2fd'; $textColor = '#1565c0'; $borderColor = '#bbdefb';
                    } elseif ($diferenciatrd->y <= 2 || $diferenciatrd->y >= 0) {
                        $bgColor = '#ffebee'; $textColor = '#c62828'; $borderColor = '#ffcdd2';
                    } else {
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

    {{-- MODAL 1: REGISTRAR GESTIÓN (RENOVADO UX/UI CON ARCHIVOS DINÁMICOS) --}}
    <div class="modal fade" id="modalSeguimiento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 25px; overflow: hidden;">
                
                {{-- Cabecera más limpia --}}
                <div class="modal-header border-0 pt-4 px-4 pb-2 bg-white">
                    <div>
                        <h5 class="modal-title fw-bolder text-dark" style="letter-spacing: -0.5px;">
                            <span class="text-primary me-2"><i class="bi bi-pencil-square"></i></span>
                            Registrar Gestión
                        </h5>
                        <p class="text-muted small mb-0 mt-1">Complete los detalles para avanzar en el flujo.</p>
                    </div>
                    <button type="button" class="btn-close shadow-none bg-light rounded-circle p-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('correspondencia.correspondencias-procesos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_correspondencia" value="{{ $correspondencia->id_radicado }}">
                    <input type="hidden" name="id_proceso" id="input_etapa_proceso_hidden">

                    <div class="modal-body px-4 py-2">
                        <div class="row g-4">
                            
                            {{-- FILA 1: Contexto Visual (Etapa y Fecha) --}}
                            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2 p-3 bg-light rounded-4">
                                <div>
                                    <label class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Etapa Actual</label>
                                    <div class="d-flex align-items-center">
                                        <div class="fw-bold text-dark fs-5" id="visual_etapa_nombre">Cargando etapa...</div>
                                        <select id="select_etapa_proceso_visual" class="d-none" disabled>
                                            <option value="">Seleccione...</option>
                                            @foreach($procesos_disponibles as $proceso)
                                                <option value="{{ $proceso->id }}">{{ $proceso->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <label class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Fecha de Registro</label>
                                    <div class="d-flex align-items-center justify-content-end text-primary fw-bold">
                                        <i class="bi bi-calendar-event me-2"></i>
                                        <span>{{ now()->format('d/m/Y H:i A') }}</span>
                                    </div>
                                    <input type="datetime-local" name="fecha_gestion" class="d-none" value="{{ now()->format('Y-m-d\TH:i') }}" readonly>
                                </div>
                            </div>

                            {{-- FILA 2: Decisión (Chelins) --}}
                            <div class="col-12">
                                <label class="form-label fw-bold small text-dark mb-2">Seleccione Nuevo Estado <span class="text-danger">*</span></label>
                                <div id="container_estados_checkin" class="d-flex flex-wrap gap-2 p-2 rounded-3 border border-light bg-white">
                                    <span class="text-muted small italic">Seleccione una etapa primero...</span>
                                </div>
                                <input type="hidden" name="estado_id" id="input_estado_id_hidden" required>
                            </div>

                            {{-- FILA 3: Observaciones --}}
                            <div class="col-12">
                                <label class="form-label fw-bold small text-dark">Detalle de la Observación <span class="text-danger">*</span></label>
                                <textarea name="observacion" class="form-control border-light bg-light rounded-4 p-3 shadow-sm" rows="3" placeholder="Escriba aquí los detalles importantes de esta gestión..." style="resize: none;" required></textarea>
                            </div>

                            {{-- FILA 4: CARGA DE ARCHIVOS DINÁMICA (Array) --}}
                            <div class="col-12">
                                <label class="form-label fw-bold small text-dark d-flex justify-content-between">
                                    <span>Soporte Documental</span>
                                    <span id="label_req_archivos" class="badge bg-secondary">Cargando requerimiento...</span>
                                </label>
                                
                                {{-- Contenedor donde se inyectan los inputs dinámicos --}}
                                <div id="container_archivos_dinamicos" class="d-flex flex-column gap-2">
                                    </div>
                            </div>

                            {{-- FILA 5: Acciones Críticas (Cards) --}}
                            <div class="col-12">
                                <div class="row g-3">
                                    {{-- Card Notificar --}}
                                    <div class="col-md-6">
                                        <label class="action-card d-flex align-items-center p-3 rounded-4 border cursor-pointer h-100 transition-all">
                                            <input type="checkbox" name="notificado_email" value="1" class="form-check-input me-3 mt-0 fs-5">
                                            <div>
                                                <div class="fw-bold text-dark small">Notificar al Remitente</div>
                                                <div class="text-muted" style="font-size: 0.7rem;">Enviar correo automático</div>
                                            </div>
                                            <i class="bi bi-envelope-check ms-auto text-muted opacity-50 fs-5"></i>
                                        </label>
                                    </div>
                                    {{-- Card Finalizar --}}
                                    <div class="col-md-6">
                                        <label class="action-card d-flex align-items-center p-3 rounded-4 border border-danger-subtle bg-danger-subtle cursor-pointer h-100 transition-all">
                                            <input type="checkbox" name="finalizado" value="1" class="form-check-input me-3 mt-0 fs-5 border-danger">
                                            <div>
                                                <div class="fw-bold text-danger small">Cerrar Etapa</div>
                                                <div class="text-danger opacity-75" style="font-size: 0.7rem;">Finalizar y avanzar flujo</div>
                                            </div>
                                            <i class="bi bi-check-circle-fill ms-auto text-danger opacity-50 fs-5"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0 pb-4 px-4 justify-content-between align-items-center">
                        <button type="button" class="btn btn-link text-muted text-decoration-none fw-bold small" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 shadow-lg fw-bold transition-all hover-lift">
                            Guardar Gestión
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
                        <div class="col-6"><label class="text-muted small d-block mb-1">La etapa fue finalizada y el proceso continúa</label><span id="det-finalizado" class="fw-bold"></span></div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Regresar</button>
                </div>
            </div>
        </div>
    </div>
{{-- ================================================================= --}}
{{-- COMPONENTE: RESPUESTA OFICIAL (COMUNICACIÓN SALIENTE) --}}
{{-- ================================================================= --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius: 24px; overflow: hidden;">
    <div class="card-body p-4">
        @if($correspondencia->comunicacionSalida)
            {{-- ESTADO: COMUNICACIÓN EXISTENTE (DISEÑO PROFESIONAL) --}}
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <div class="symbol-label bg-pastel-success">
                            <i class="bi bi-send-check-fill text-success fs-2"></i>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h5 class="fw-bolder text-dark mb-0">Respuesta Oficial Generada</h5>
                            @php
                                $estadoSalida = $correspondencia->comunicacionSalida->estado_envio;
                                $badgeSalida = [
                                    'Generado' => 'bg-pastel-primary',
                                    'Enviado por Email' => 'bg-pastel-info',
                                    'Notificado Físicamente' => 'bg-pastel-success'
                                ][$estadoSalida] ?? 'bg-light';
                            @endphp
                            <span class="badge {{ $badgeSalida }} rounded-pill px-3 py-1 fw-bold fs-9" style="text-transform: uppercase;">
                                {{ $estadoSalida }}
                            </span>
                        </div>
                        <p class="text-muted small mb-0">
                            Oficio: <span class="fw-bold text-primary">{{ $correspondencia->comunicacionSalida->nro_oficio_salida }}</span> 
                            • {{ $correspondencia->comunicacionSalida->fecha_generacion->format('d M, Y') }}
                        </p>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="{{ route('correspondencia.comunicaciones-salida.descargarPdf', $correspondencia->comunicacionSalida->id_respuesta) }}" 
                       target="_blank" 
                       class="btn btn-white border border-danger-subtle text-danger rounded-pill px-4 fw-bold shadow-sm hover-lift">
                        <i class="bi bi-file-earmark-pdf-fill me-2"></i>Descargar PDF
                    </a>
                </div>
            </div>

            @if($correspondencia->comunicacionSalida->cuerpo_carta)
                <div class="mt-4 p-3 rounded-4 bg-light bg-opacity-50 border border-dashed text-muted small italic">
                    <i class="bi bi-quote fs-4 text-gray-300"></i>
                    {{ Str::limit($correspondencia->comunicacionSalida->cuerpo_carta, 180) }}
                </div>
            @endif

        @else
            {{-- ESTADO: SIN RESPUESTA (INVITACIÓN MINIMALISTA) --}}
            <div class="text-center py-2">
                <div class="symbol symbol-60px mb-3">
                    <div class="symbol-label bg-pastel-primary">
                        <i class="bi bi-envelope-plus text-primary fs-1"></i>
                    </div>
                </div>
                <h5 class="fw-bolder text-dark">Sin Respuesta Oficial</h5>
                <p class="text-muted small mx-auto mb-4" style="max-width: 400px;">
                    Este radicado aún no cuenta con un oficio de salida vinculado. Genere una comunicación formal para dar cierre al proceso.
                </p>
                <button type="button" class="btn btn-primary rounded-pill px-5 fw-bold shadow hover-lift" 
                        data-bs-toggle="modal" data-bs-target="#modalCrearSalidaRapida">
                    <i class="bi bi-magic me-2"></i>Redactar Oficio de Salida
                </button>
            </div>
        @endif
    </div>
</div>

{{-- ================================================================= --}}
{{-- MODAL UX: CREACIÓN RÁPIDA DE OFICIO CON BUSCADOR --}}
{{-- ================================================================= --}}
<div class="modal fade" id="modalCrearSalidaRapida" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 30px; overflow: hidden;">
            <div class="modal-header bg-pastel-primary border-0 pt-5 px-5 pb-4">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-3">
                        <div class="symbol-label bg-white shadow-sm">
                            <i class="bi bi-pencil-fill text-primary fs-3"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="fw-bolder text-primary mb-0">Generar Respuesta Oficial</h3>
                        <span class="text-muted small fw-bold text-uppercase">RDS #{{ $correspondencia->id_radicado }}</span>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('correspondencia.comunicaciones-salida.store') }}" method="POST" enctype="multipart/form-data" id="formCrearSalida">
                @csrf
                <input type="hidden" name="id_correspondencia" value="{{ $correspondencia->id_radicado }}">

                <div class="modal-body p-5">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark"><i class="bi bi-person-check me-1 text-primary"></i>Funcionario que Firma</label>
                            <select name="fk_usuario" id="select_firmante_modal" class="form-select border-light bg-light rounded-4" required>
                                <option value="">Escriba para buscar...</option>
                                @foreach($usuarios as $user)
                                    <option value="{{ $user->id }}" {{ auth()->id() == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark"><i class="bi bi-send me-1 text-primary"></i>Estado de Envío</label>
                            <select name="estado_envio" class="form-select border-light bg-light rounded-4" required>
                                <option value="Generado">Generado (Borrador)</option>
                                <option value="Enviado por Email" selected>Enviado por Email</option>
                                <option value="Notificado Físicamente">Notificado Físicamente</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold text-dark"><i class="bi bi-layout-text-sidebar-reverse me-1 text-primary"></i>Plantilla de Texto</label>
                            <select name="id_plantilla" id="select_plantilla_modal" class="form-select border-light bg-light rounded-4">
                                <option value="">Texto libre (Sin plantilla)</option>
                                @foreach($plantillas as $plan)
                                    <option value="{{ $plan->id }}" data-contenido="{{ $plan->cuerpo_carta }}">{{ $plan->nombre_plantilla }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold text-dark">Contenido del Oficio (Respuesta)</label>
                            <textarea name="cuerpo_carta" class="form-control border-light bg-light rounded-4 p-4" rows="8" placeholder="Escriba la respuesta oficial aquí..." style="resize: none;" required></textarea>
                        </div>

                        <div class="col-md-12">
                            <div class="p-4 rounded-4 border border-warning border-dashed bg-light-warning bg-opacity-10">
                                <label class="form-label fw-bold text-warning mb-2"><i class="bi bi-cloud-arrow-up me-2"></i>Sello de Firma (.png / .jpg)</label>
                                <input type="file" name="firma_digital" class="form-control border-0 bg-white shadow-sm" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="button" class="btn btn-light-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnSubmitSalida" class="btn btn-primary rounded-pill px-8 py-3 fw-bold shadow-lg transition-all hover-lift">
                        <i class="bi bi-check-circle-fill me-2"></i>Guardar y Generar PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL DE CARGA (LOADING) --}}
<div class="modal fade" id="modalProcesandoSalida" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body text-center p-5">
                <div class="spinner-border text-primary mb-4" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <h4 class="fw-bolder text-dark mb-2">Generando Documento Oficial</h4>
                <p class="text-muted mb-0">Estamos procesando los datos y estampando la firma. Por favor, espere un momento...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Inicializar Select2 al abrir el modal
        $('#modalCrearSalidaRapida').on('shown.bs.modal', function () {
            $('#select_firmante_modal').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Buscar funcionario...',
                allowClear: true,
                dropdownParent: $('#modalCrearSalidaRapida')
            });
        });

        // Autocarga de contenido al seleccionar plantilla
        $('#select_plantilla_modal').on('change', function() {
            const contenido = $(this).find(':selected').data('contenido');
            if(contenido) {
                $('textarea[name="cuerpo_carta"]').val(contenido);
            }
        });

        // PREVENCIÓN DE MULTIPLES CLICS Y MODAL DE CARGA
        const form = document.getElementById('formCrearSalida');
        const btn = document.getElementById('btnSubmitSalida');
        const modalLoading = new bootstrap.Modal(document.getElementById('modalProcesandoSalida'));

        form.addEventListener('submit', function (e) {
            // Verificar si el formulario es válido (HTML5 validation)
            if (form.checkValidity()) {
                // Deshabilitar botón para evitar múltiples clics
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
                
                // Cerrar el modal de formulario
                $('#modalCrearSalidaRapida').modal('hide');
                
                // Mostrar el modal de carga
                modalLoading.show();
            }
        });
    });
</script>
@endpush

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
   
        /* Estilos generales */
        .btn-primary {
            background-color: #1976d2; border-color: #1976d2;
        }
        .btn-primary:hover {
            background-color: #1565c0; border-color: #1565c0;
        }

        /* --- ESTILOS CHELIN (Fichas de selección) --- */
        .chelin-item {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 50px;
            border: 1px solid #dee2e6;
            background-color: white;
            color: #6c757d;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .chelin-item:hover {
            background-color: #f8f9fa;
            border-color: #adb5bd;
            transform: translateY(-1px);
        }

        .chelin-item.active {
            background-color: #e3f2fd;
            color: #1565c0;
            border-color: #90caf9;
            box-shadow: 0 4px 6px rgba(13, 110, 253, 0.15);
        }

        /* --- NUEVOS ESTILOS UX MODAL --- */
        .cursor-pointer { cursor: pointer; }
        .border-dashed { border-style: dashed !important; }
        
        .upload-box:hover {
            background-color: #f1f8ff !important;
            border-color: #bbdefb !important;
        }

        .action-card:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .action-card input:checked + div {
            color: #0d6efd;
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
    </style>

<script>
        // -------------------------------------------------------------
        // MAPA DE DATOS DEL PROCESO
        // -------------------------------------------------------------
        
        const numeroArchivosPorProceso = {
            @foreach($procesos_disponibles as $proceso)
                {{ $proceso->id }}: {{ (int) $proceso->numero_archivos }},
            @endforeach
        };

        // NUEVO: Mapa con los nombres de los archivos requeridos
        const tiposArchivosPorProceso = {
            @foreach($procesos_disponibles as $proceso)
                {{ $proceso->id }}: @json($proceso->tipos_archivos ?? []),
            @endforeach
        };

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

        const nombresProcesos = {
            @foreach($procesos_disponibles as $proceso)
                {{ $proceso->id }}: "{{ $proceso->nombre }}",
            @endforeach
        };

        function abrirModalGestion(idProceso) {
            var myModal = new bootstrap.Modal(document.getElementById('modalSeguimiento'));
            
            // Asignaciones base
            document.getElementById('input_etapa_proceso_hidden').value = idProceso;
            document.getElementById('select_etapa_proceso_visual').value = idProceso;
            document.getElementById('visual_etapa_nombre').textContent = nombresProcesos[idProceso] || 'Etapa Seleccionada';

            // --- LÓGICA 1: CHELINS DE ESTADOS ---
            var containerEstados = document.getElementById('container_estados_checkin');
            var inputEstadoHidden = document.getElementById('input_estado_id_hidden');
            containerEstados.innerHTML = '';
            inputEstadoHidden.value = '';

            var estadosDisponibles = estadosPorProceso[idProceso] || [];
            if (estadosDisponibles.length > 0) {
                estadosDisponibles.forEach(function(estado) {
                    var chelin = document.createElement('div');
                    chelin.className = 'chelin-item'; 
                    chelin.textContent = estado.text;
                    chelin.onclick = function() {
                        containerEstados.querySelectorAll('.chelin-item').forEach(el => el.classList.remove('active'));
                        chelin.classList.add('active');
                        inputEstadoHidden.value = estado.val;
                    };
                    containerEstados.appendChild(chelin);
                });
            } else {
                containerEstados.innerHTML = '<span class="text-muted small italic px-2">Esta etapa no requiere selección de estado.</span>';
            }

            // --- LÓGICA 2: ARCHIVOS DINÁMICOS CON NOMBRES ---
            let numArchivos = numeroArchivosPorProceso[idProceso] || 0;
            let nombresArchivos = tiposArchivosPorProceso[idProceso] || [];
            let containerArchivos = document.getElementById('container_archivos_dinamicos');
            let labelBadge = document.getElementById('label_req_archivos');
            
            containerArchivos.innerHTML = ''; // Limpiar anteriores

            if(numArchivos > 0) {
                // Generar N inputs individuales obligatorios
                labelBadge.className = "badge bg-danger rounded-pill";
                labelBadge.textContent = numArchivos + " Archivo(s) Obligatorio(s)";
                
                for(let i = 1; i <= numArchivos; i++) {
                    // Si el proceso tiene nombre definido lo usa, si no, usa un nombre genérico
                    let nombreDelDocumento = nombresArchivos[i-1] ? nombresArchivos[i-1] : `Documento Requerido #${i}`;

                    containerArchivos.innerHTML += `
                        <div class="position-relative d-flex align-items-center p-2 rounded-4 border-2 border-dashed" style="border-color: #dee2e6; background-color: #fcfcfc;">
                            <div class="p-2 bg-white rounded-circle shadow-sm me-3 text-danger d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold small text-uppercase">${nombreDelDocumento} <span class="text-danger">*</span></h6>
                                <input type="file" name="documento_arc[]" class="form-control form-control-sm mt-1 shadow-sm border-0 bg-white" accept=".pdf,.doc,.docx,.jpg,.png" required>
                            </div>
                        </div>
                    `;
                }
            } else {
                // Generar 1 input drag and drop opcional múltiple
                labelBadge.className = "badge bg-pastel-primary rounded-pill text-uppercase";
                labelBadge.textContent = "Opcional";
                
                containerArchivos.innerHTML = `
                    <div class="upload-box position-relative d-flex align-items-center p-3 rounded-4 border-2 border-dashed" style="border-color: #dee2e6; background-color: #fcfcfc;">
                        <div class="p-3 bg-white rounded-circle shadow-sm me-3 text-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-cloud-arrow-up-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Subir archivos opcionales</h6>
                            <p class="text-muted small mb-0" style="font-size: 0.75rem;">Puede seleccionar varios PDF/Imágenes</p>
                        </div>
                        <input type="file" name="documento_arc[]" class="position-absolute w-100 h-100 top-0 start-0 opacity-0 cursor-pointer" multiple accept=".pdf,.doc,.docx,.jpg,.png" title="Seleccionar archivos">
                    </div>
                `;
            }

            myModal.show();
        }

        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl) });

            // Lógica para abrir detalles desde la Timeline
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