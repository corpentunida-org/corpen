<x-base-layout>
    <div class="app-container py-4">
        <header class="main-header mb-4">
            <div class="header-content">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('correspondencia.flujos.index') }}" class="text-decoration-none">Flujos</a></li>
                        <li class="breadcrumb-item active">Detalle y Analítica</li>
                    </ol>
                </nav>
                <h1 class="page-title h3 fw-bold">{{ $flujo->nombre }}</h1>
            </div>
            <div class="header-actions d-flex gap-2">
                @if(empty($flujo->trd))
                    <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center rounded-3 px-3 shadow-sm bg-white" data-bs-toggle="modal" data-bs-target="#modalTRD">
                        <i class="bi bi-folder-plus me-1"></i> Configurar TRD
                    </button>
                @endif
                <button type="button" class="btn btn-primary-neo shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCrearProceso">
                    <i class="bi bi-plus-circle-fill me-1"></i> Configuración de Nuevo Proceso
                </button>
                <a href="{{ route('correspondencia.flujos.edit', $flujo) }}" class="btn btn-warning btn-sm d-flex align-items-center rounded-3 px-3">
                    <i class="bi bi-pencil me-1"></i> Editar Detalles del Flujo
                </a>
            </div>
        </header>

        <div class="row">
            {{-- COLUMNA IZQUIERDA: Info General y KPIs --}}
            <div class="col-lg-4">
                
                {{-- Tarjeta 1: Info General --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 text-center">
                        <div class="icon-box bg-soft-indigo text-indigo mx-auto mb-3" style="width: 70px; height: 70px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-diagram-3-fill fs-2"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Información General</h5>
                        <p class="text-muted small mb-3">{{ $flujo->detalle ?? 'Sin descripción.' }}</p>
                        
                        <hr class="my-3 text-muted opacity-25">
                        
                        <div class="text-start">
                            <div class="mb-3">
                                <label class="small text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Área / Dependencia</label>
                                <div class="d-flex align-items-center mt-1 text-indigo">
                                    <i class="bi bi-building me-2"></i>
                                    <span class="fw-bold small">{{ $flujo->area->nombre ?? 'Área no definida' }}</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="small text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Responsable (Jefe)</label>
                                <div class="d-flex align-items-center mt-1">
                                    <div class="avatar-xs bg-indigo text-white me-2" style="width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold;">
                                        {{ strtoupper(substr($flujo->usuario->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <span class="fw-bold small">{{ $flujo->usuario->name ?? 'Sin asignar' }}</span>
                                </div>
                            </div>

                            {{-- ESTADO DE LA TRD --}}
                            <div class="mb-3 bg-light p-3 rounded-3 border">
                                <label class="small text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Serie Documental (TRD)</label>
                                @if(!empty($flujo->trd))
                                    <div class="d-flex align-items-start mt-1 text-success">
                                        <i class="bi bi-check-circle-fill me-2 mt-1"></i>
                                        <div>
                                            <span class="fw-bold small d-block text-dark">{{ $flujo->trd->serie_documental }}</span>
                                            <span class="text-muted" style="font-size: 0.7rem;">Retención configurada</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center justify-content-between mt-1">
                                        <div class="d-flex align-items-center text-warning fw-bold small">
                                            <i class="bi bi-exclamation-circle-fill me-2"></i> Pendiente
                                        </div>
                                        <button class="btn btn-sm btn-light border text-indigo rounded-pill px-3 shadow-sm hover-up" style="font-size: 0.7rem; font-weight: bold;" data-bs-toggle="modal" data-bs-target="#modalTRD">
                                            Asignar ahora
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tarjeta 2: KPIs / Métricas --}}
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="card border-0 shadow-sm rounded-4 bg-soft-warning h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-warning text-white rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="bi bi-activity"></i>
                                    </div>
                                    <h6 class="mb-0 text-dark fw-bold" style="font-size: 0.8rem;">Activos</h6>
                                </div>
                                <h3 class="fw-bold text-dark mb-0">{{ $total_activos }}</h3>
                                <small class="text-muted" style="font-size: 0.7rem;">Radicados en curso</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card border-0 shadow-sm rounded-4 bg-soft-success h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-success text-white rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="bi bi-check2-all"></i>
                                    </div>
                                    <h6 class="mb-0 text-dark fw-bold" style="font-size: 0.8rem;">Finalizados</h6>
                                </div>
                                <h3 class="fw-bold text-dark mb-0">{{ $total_finalizados }}</h3>
                                <small class="text-muted" style="font-size: 0.7rem;">Radicados cerrados</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 bg-light">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="bi bi-stopwatch text-indigo me-2"></i>
                                        <h6 class="mb-0 text-dark fw-bold" style="font-size: 0.8rem;">Tiempo Promedio</h6>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.7rem;">Desde la creación hasta el cierre</small>
                                </div>
                                <div class="text-end">
                                    <h4 class="fw-bold text-indigo mb-0">{{ number_format($tiempo_promedio_dias, 1) }}</h4>
                                    <small class="text-muted" style="font-size: 0.7rem;">Días</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: Dashboard y Pasos --}}
            <div class="col-lg-8">
                
                {{-- NUEVO: DASHBOARD CUELLO DE BOTELLA (Chart.js) --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="text-uppercase fw-bold text-indigo mb-0">
                                    <i class="bi bi-bar-chart-fill me-2"></i>Análisis de Cuellos de Botella
                                </h6>
                                <small class="text-muted">Distribución de los {{ $total_activos }} radicados activos por etapa.</small>
                            </div>
                        </div>
                        
                        @if($total_activos > 0)
                            <div style="height: 250px; width: 100%;">
                                <canvas id="bottleneckChart"></canvas>
                            </div>
                        @else
                            <div class="text-center py-4 bg-light rounded-3 border-dashed">
                                <i class="bi bi-emoji-smile text-success fs-2 opacity-50 mb-2 d-block"></i>
                                <p class="text-muted mb-0 small">No hay radicados estancados en este momento. ¡Todo fluye!</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Secuencia de Pasos Mejorada (Diseño Pro) --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                            <h6 class="text-uppercase fw-bold text-dark mb-0 d-flex align-items-center">
                                <i class="bi bi-list-check me-2 text-indigo fs-5"></i>Ruta del Proceso
                            </h6>
                            <span class="badge bg-soft-indigo text-indigo border border-indigo-subtle px-3 py-2 rounded-pill fw-bold">
                                {{ $flujo->procesos->count() }} Etapas Configuradas
                            </span>
                        </div>

                        @forelse($flujo->procesos as $proceso)
                            <div class="d-flex mb-4 position-relative step-item {{ !$proceso->activo ? 'opacity-75' : '' }}">
                                {{-- Línea conectora de la línea de tiempo --}}
                                @if(!$loop->last)
                                    <div class="position-absolute h-100 border-start border-2" style="left: 17px; top: 38px; z-index: 1; border-color: #e5e7eb !important;"></div>
                                @endif
                                
                                {{-- Círculo con el número del paso --}}
                                <div class="flex-shrink-0 me-3 mt-1">
                                    <div class="{{ $proceso->activo ? 'bg-indigo text-white shadow' : 'bg-secondary text-white shadow-sm' }} rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 36px; height: 36px; z-index: 2; position: relative; font-size: 14px; font-weight: 800; border: 3px solid #fff;">
                                        {{ $loop->iteration }}
                                    </div>
                                </div>

                                {{-- Tarjeta del contenido del paso --}}
                                <div class="flex-grow-1 p-0 border rounded-4 bg-white shadow-sm hover-up overflow-hidden transition-all">
                                    {{-- Cabecera de la tarjeta --}}
                                    <div class="p-3 {{ $proceso->activo ? 'bg-light' : 'bg-light-subtle' }} border-bottom d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center">
                                                {{ $proceso->nombre }}
                                                @if(!$proceso->activo)
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-2 fw-bold" style="font-size: 0.65rem;">
                                                        <i class="bi bi-pause-circle me-1"></i>Pausado
                                                    </span>
                                                @endif
                                            </h6>
                                        </div>
                                        <div class="btn-group shadow-sm rounded-pill">
                                            <a href="{{ route('correspondencia.procesos.show', $proceso) }}" class="btn btn-sm btn-white text-indigo border" title="Configurar Equipo y Estados" style="background: white;">
                                                <i class="bi bi-gear-fill"></i>
                                            </a>
                                            <a href="{{ route('correspondencia.procesos.edit', $proceso) }}" class="btn btn-sm btn-white text-warning border border-start-0" title="Editar Información" style="background: white;">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </div>
                                    </div>

                                    {{-- Cuerpo de la tarjeta --}}
                                    <div class="p-3">
                                        @if($proceso->detalle)
                                            <p class="small text-muted mb-3" style="line-height: 1.5;">{{ $proceso->detalle }}</p>
                                        @endif
                                        
                                        {{-- NUEVO: Panel de Requisitos y Métricas --}}
                                        <div class="bg-light rounded-3 p-2 d-flex flex-wrap align-items-center gap-3 mb-3 border border-dashed">
                                            {{-- Tiempo de Respuesta --}}
                                            <div class="d-flex align-items-center">
                                                <div class="icon-sm bg-white border rounded d-flex align-items-center justify-content-center text-warning me-2 shadow-sm" style="width: 28px; height: 28px;">
                                                    <i class="bi bi-clock-history" style="font-size: 0.85rem;"></i>
                                                </div>
                                                <div>
                                                    <span class="d-block text-uppercase text-muted fw-bold" style="font-size: 0.55rem; line-height: 1;">SLA Respuesta</span>
                                                    <span class="small fw-bold text-dark">{{ $proceso->tiempo_respuesta_dias ?: 'Sin límite' }} {{ $proceso->tiempo_respuesta_dias ? 'Días' : '' }}</span>
                                                </div>
                                            </div>

                                            <div class="vr bg-secondary opacity-25 d-none d-md-block" style="width: 2px;"></div>

                                            {{-- Archivos Requeridos --}}
                                            <div class="d-flex align-items-center flex-grow-1">
                                                <div class="icon-sm bg-white border rounded d-flex align-items-center justify-content-center text-info me-2 shadow-sm" style="width: 28px; height: 28px;">
                                                    <i class="bi bi-file-earmark-check" style="font-size: 0.85rem;"></i>
                                                </div>
                                                <div>
                                                    <span class="d-block text-uppercase text-muted fw-bold" style="font-size: 0.55rem; line-height: 1;">Archivos Obligatorios</span>
                                                    @if($proceso->numero_archivos > 0)
                                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                                            <span class="badge bg-info-subtle text-info border border-info-subtle fw-bold" style="font-size: 0.65rem;">
                                                                {{ $proceso->numero_archivos }} Total
                                                            </span>
                                                            {{-- Mostrar nombres de los archivos si existen --}}
                                                            @if(!empty($proceso->tipos_archivos) && is_array($proceso->tipos_archivos))
                                                                @foreach($proceso->tipos_archivos as $archivo)
                                                                    @if(!empty($archivo))
                                                                        <span class="badge bg-white text-secondary border border-secondary-subtle fw-normal" style="font-size: 0.65rem;">
                                                                            {{ Str::limit($archivo, 15) }}
                                                                        </span>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="small fw-bold text-dark">No requiere</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        {{-- Responsables --}}
                                        <div>
                                            <span class="text-uppercase text-muted fw-bold d-block mb-2" style="font-size: 0.65rem;">Equipo Responsable</span>
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                @forelse($proceso->usuarios as $user)
                                                    <span class="badge bg-white border border-secondary-subtle text-dark shadow-sm d-flex align-items-center py-1 px-2" style="font-weight: 500;">
                                                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center me-1" 
                                                             style="width: 16px; height: 16px; font-size: 8px; background: linear-gradient(135deg, #6366f1, #4f46e5);">
                                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                                        </div>
                                                        {{ $user->name }}
                                                    </span>
                                                @empty
                                                    <span class="text-danger bg-danger-subtle px-2 py-1 rounded small fw-medium" style="font-size: 11px;">
                                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Sin responsables asignados
                                                    </span>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 border border-dashed rounded-4 bg-light-subtle">
                                <div class="bg-white shadow-sm rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                                    <i class="bi bi-diagram-3 text-indigo fs-2 opacity-75"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">El flujo está vacío</h6>
                                <p class="text-muted small mb-3">Aún no has configurado la secuencia lógica de pasos.</p>
                                <button class="btn btn-indigo btn-sm rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCrearProceso">
                                    <i class="bi bi-plus-circle me-1"></i>Añadir Primer Paso
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODALES A CONTINUACIÓN (Crear Proceso y Configurar TRD) Mantenidos igual --}}
    
    {{-- MODAL CREAR PASO --}}
    <div class="modal fade" id="modalCrearProceso" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-indigo text-white rounded-top-4">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Nuevo Paso para {{ $flujo->nombre }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('correspondencia.procesos.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <input type="hidden" name="flujo_id" value="{{ $flujo->id }}">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Nombre de la etapa <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-tag text-indigo"></i></span>
                                <input type="text" name="nombre" class="form-control border-start-0 ps-0" placeholder="Ej: Revisión Jurídica" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Instrucciones</label>
                            <textarea name="detalle" class="form-control shadow-sm" rows="2" placeholder="¿Qué debe hacer el responsable en este paso?"></textarea>
                        </div>

                        {{-- NUEVOS CAMPOS: Tiempo de respuesta y N° de Archivos --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Días de Respuesta</label>
                                <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-clock-history text-indigo"></i></span>
                                    <input type="number" name="tiempo_respuesta_dias" class="form-control border-start-0 ps-0" min="0" placeholder="Ej: 3">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Archivos Requeridos</label>
                                <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-file-earmark-plus text-indigo"></i></span>
                                    <input type="number" id="modal_cantidad_archivos" name="numero_archivos" class="form-control border-start-0 ps-0" min="0" value="0">
                                </div>
                            </div>
                        </div>

                        {{-- Contenedor de archivos dinámicos (Se llena con JavaScript) --}}
                        <div id="modal_contenedor_archivos" class="row g-2 mb-3"></div>

                        <div class="bg-light p-3 rounded-3 border d-flex justify-content-between align-items-center">
                            <div>
                                <label class="form-label fw-bold small text-muted text-uppercase mb-0 d-block">¿Paso Activo?</label>
                                <small class="text-muted" style="font-size: 0.75rem;">Si se desactiva, se saltará en el flujo.</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input ms-0" type="checkbox" name="activo" value="1" id="switchActivo" checked style="width: 2.4em; height: 1.2em; cursor: pointer;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light rounded-bottom-4 border-top">
                        <button type="button" class="btn btn-light border btn-sm px-3 shadow-sm" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-indigo btn-sm px-4 shadow-sm">
                            Guardar Etapa <i class="bi bi-arrow-right-short ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL TRD --}}
    @if(empty($flujo->trd))
    <div class="modal fade" id="modalTRD" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-indigo text-white rounded-top-4">
                    <h5 class="modal-title fw-bold"><i class="bi bi-folder-plus me-2"></i>Configurar Serie Documental (TRD)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('correspondencia.trds.store') }}" method="POST" id="form-trd">
                    @csrf
                    <div class="modal-body p-4">
                        <input type="hidden" name="usuario_id" value="{{ auth()->id() }}">
                        <input type="hidden" name="fk_flujo" value="{{ $flujo->id }}">

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Flujo Vinculado</label>
                            <input type="text" class="form-control bg-light border-0 fw-bold text-indigo" value="{{ $flujo->nombre }}" readonly>
                            <small class="text-muted">La serie se vinculará automáticamente a este flujo.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Nombre de la Serie Documental <span class="text-danger">*</span></label>
                            <input type="text" name="serie_documental" class="form-control" placeholder="Ej: Contratos de Servicios" required>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Archivo Gestión (Años) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-briefcase text-info"></i></span>
                                    <input type="number" name="tiempo_gestion" class="form-control" value="0" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Archivo Central (Años) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-building text-warning"></i></span>
                                    <input type="number" name="tiempo_central" class="form-control" value="0" min="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="bg-light p-3 rounded-3 border">
                            <label class="form-label fw-bold small text-muted text-uppercase d-block mb-2">Disposición Final <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="disposicion_final" id="disp1" value="conservar" checked>
                                    <label class="form-check-label fw-bold text-success small" for="disp1">
                                        <i class="bi bi-archive-fill me-1"></i> Conservación Total
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="disposicion_final" id="disp2" value="eliminar">
                                    <label class="form-check-label fw-bold text-danger small" for="disp2">
                                        <i class="bi bi-trash-fill me-1"></i> Eliminación
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light rounded-bottom-4 border-top">
                        <button type="button" class="btn btn-light border btn-sm px-3 shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btn-save-trd" class="btn btn-indigo btn-sm px-4 shadow-sm">
                            Guardar Serie <i class="bi bi-check-circle ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Estilos --}}
    <style>
        .bg-soft-indigo { background-color: #e0e7ff; }
        .text-indigo { color: #4f46e5; }
        .bg-indigo { background-color: #4f46e5; }
        .btn-indigo { background-color: #4f46e5; color: white; }
        
        .bg-soft-warning { background-color: #fffbeb; }
        .bg-soft-success { background-color: #ecfdf5; }
        .border-dashed { border: 1px dashed #dee2e6; }
        
        .btn-primary-neo { background-color: #4f46e5; color: white; border: none; padding: 0.5rem 1.2rem; border-radius: 10px; font-weight: 500; transition: all 0.2s; }
        .hover-up:hover { transform: translateY(-3px); border-color: #c7d2fe !important; }
    </style>

    @push('scripts')
    {{-- Cargar Chart.js mediante CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // ===================================================================
            // 1. Lógica para renderizar la Gráfica de Cuellos de Botella
            // ===================================================================
            const chartData = {!! json_encode($datosGrafica ?? []) !!};
            const chartLabels = {!! json_encode($labelsGrafica ?? []) !!};
            
            // Solo dibujamos si hay datos activos
            if (chartData.length > 0 && chartData.reduce((a, b) => a + b, 0) > 0) {
                const ctx = document.getElementById('bottleneckChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: 'Radicados en esta etapa',
                            data: chartData,
                            backgroundColor: 'rgba(79, 70, 229, 0.6)', // Color índigo semi-transparente
                            borderColor: 'rgba(79, 70, 229, 1)',
                            borderWidth: 1,
                            borderRadius: 6, // Bordes redondeados modernos
                            barPercentage: 0.6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }, // Ocultamos la leyenda por ser obvia
                            tooltip: {
                                backgroundColor: '#111827',
                                padding: 12,
                                titleFont: { size: 13, family: 'Inter' },
                                bodyFont: { size: 14, weight: 'bold', family: 'Inter' },
                                displayColors: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1, font: { family: 'Inter' } },
                                grid: { color: '#f3f4f6', drawBorder: false }
                            },
                            x: {
                                ticks: { font: { family: 'Inter', size: 11 } },
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            // ===================================================================
            // 2. Lógica dinámica para N° de Archivos en el Modal de Crear Paso
            // ===================================================================
            const modalInputCantidad = document.getElementById('modal_cantidad_archivos');
            const modalContenedorArchivos = document.getElementById('modal_contenedor_archivos');

            if (modalInputCantidad && modalContenedorArchivos) {
                modalInputCantidad.addEventListener('input', function() {
                    let cant = parseInt(this.value) || 0;
                    
                    // Prevenir valores negativos
                    if (cant < 0) { 
                        cant = 0; 
                        this.value = 0; 
                    }
                    
                    modalContenedorArchivos.innerHTML = ''; // Limpiar contenedor
                    
                    // Generar los inputs dinámicamente
                    for (let i = 0; i < cant; i++) {
                        let col = document.createElement('div');
                        col.className = 'col-md-6 mb-2';
                        col.innerHTML = `
                            <label class="form-label fw-bold small text-muted text-uppercase mb-1" style="font-size: 0.65rem;">Nombre Archivo ${i+1}</label>
                            <input type="text" name="tipos_archivos[]" class="form-control form-control-sm border shadow-sm" placeholder="Ej: Cédula, Contrato..." required>
                        `;
                        modalContenedorArchivos.appendChild(col);
                    }
                });
            }

            // ===================================================================
            // 3. Lógica para guardar TRD vía AJAX
            // ===================================================================
            const trdForm = document.getElementById('form-trd');
            if(trdForm) {
                trdForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const btn = document.getElementById('btn-save-trd');
                    const originalHtml = btn.innerHTML;
                    
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Guardando...';
                    btn.disabled = true;

                    try {
                        const response = await fetch(this.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: new FormData(this)
                        });

                        if (response.ok) {
                            window.location.reload();
                        } else {
                            const data = await response.json();
                            if (response.status === 422 && data.errors) {
                                let mensajes = [];
                                for (let campo in data.errors) {
                                    mensajes.push("• " + data.errors[campo][0]);
                                }
                                alert("No se pudo guardar:\n\n" + mensajes.join("\n"));
                            } else {
                                alert(data.message || 'Ocurrió un error inesperado al guardar.');
                            }
                            btn.innerHTML = originalHtml;
                            btn.disabled = false;
                        }
                    } catch (error) {
                        alert('Error de red. Inténtelo de nuevo.');
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    }
                });
            }
        });
    </script>
    @endpush
</x-base-layout>