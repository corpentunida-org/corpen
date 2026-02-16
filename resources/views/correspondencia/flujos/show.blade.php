<x-base-layout>
    <div class="app-container py-4">
        <header class="main-header mb-4">
            <div class="header-content">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('correspondencia.flujos.index') }}" class="text-decoration-none">Flujos</a></li>
                        <li class="breadcrumb-item active">Detalle</li>
                    </ol>
                </nav>
                <h1 class="page-title h3 fw-bold">{{ $flujo->nombre }}</h1>
            </div>
            <div class="header-actions d-flex gap-2">
                <button type="button" class="btn btn-primary-neo shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCrearProceso">
                    <i class="bi bi-plus-circle-fill me-1"></i> Configuración de Nuevo Proceso
                </button>
                <a href="{{ route('correspondencia.flujos.edit', $flujo) }}" class="btn btn-warning btn-sm d-flex align-items-center rounded-3 px-3">
                    <i class="bi bi-pencil me-1"></i> Editar Detalles del Flujo
                </a>
            </div>
        </header>

        <div class="row">
            {{-- Columna Izquierda: Info General --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 text-center">
                        <div class="icon-box bg-soft-indigo text-indigo mx-auto mb-3" style="width: 70px; height: 70px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-diagram-3-fill fs-2"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Información General</h5>
                        <span class="badge rounded-pill {{ ($flujo->correspondencias_count ?? 0) > 0 ? 'bg-soft-warning text-warning' : 'bg-soft-success text-success' }} mb-3">
                            {{ ($flujo->correspondencias_count ?? 0) > 0 ? 'En Uso: ' . $flujo->correspondencias_count : 'Sin Uso Activo' }}
                        </span>
                        
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

                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="small text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Creado</label>
                                    <p class="small fw-medium mb-0"><i class="bi bi-calendar-event me-1"></i>{{ $flujo->created_at->format('d/m/Y') }}</p>
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Actualizado</label>
                                    <p class="small fw-medium mb-0"><i class="bi bi-clock-history me-1"></i>{{ $flujo->updated_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna Derecha: Pasos --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="text-uppercase fw-bold text-muted small mb-3" style="letter-spacing: 1px;">Descripción del Proceso</h6>
                        <p class="text-secondary">{{ $flujo->detalle ?? 'No se ha proporcionado una descripción detallada.' }}</p>
                        
                        <hr class="my-4 text-muted opacity-25">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="text-uppercase fw-bold text-indigo mb-0">
                                <i class="bi bi-list-check me-2"></i>Secuencia de Pasos
                            </h6>
                            <span class="badge bg-light text-indigo border px-3 py-2 rounded-pill">{{ $flujo->procesos->count() }} Etapas</span>
                        </div>

                        @forelse($flujo->procesos->sortBy('id') as $proceso)
                            <div class="d-flex mb-4 position-relative">
                                @if(!$loop->last)
                                    <div class="position-absolute h-100 border-start border-2 border-light" style="left: 15px; top: 35px; z-index: 1;"></div>
                                @endif
                                
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-indigo text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                                         style="width: 32px; height: 32px; z-index: 2; position: relative; font-size: 13px; font-weight: bold; {{ !$proceso->activo ? 'background-color: #adb5bd !important;' : '' }}">
                                        {{ $loop->iteration }}
                                    </div>
                                </div>
                                <div class="flex-grow-1 p-3 border rounded-3 bg-white shadow-sm hover-up">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="fw-bold mb-1 text-dark">
                                                {{ $proceso->nombre }}
                                                @if(!$proceso->activo)
                                                    <span class="badge bg-light text-muted border ms-2 fw-normal" style="font-size: 10px;">Inactivo</span>
                                                @endif
                                            </h6>
                                        </div>
                                        <div class="btn-group">
                                            <a href="{{ route('correspondencia.procesos.show', $proceso) }}" class="btn btn-sm btn-light text-primary border" title="Asignar Participantes y Estados">
                                                <i class="bi bi-gear-fill"></i>
                                            </a>
                                            <a href="{{ route('correspondencia.procesos.edit', $proceso) }}" class="btn btn-sm btn-light text-warning border">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-2">{{ $proceso->detalle }}</p>
                                    
                                    <div class="d-flex align-items-center flex-wrap gap-1 mt-2">
                                        {{-- Solo iterará sobre los que el controlador permitió (los activos) --}}
                                        @foreach($proceso->usuarios as $user)
                                            <span class="badge border text-dark bg-light shadow-none" style="font-size: 10px; font-weight: 500;">
                                                <i class="bi bi-person me-1"></i>{{ $user->name }}
                                            </span>
                                        @endforeach

                                        {{-- Si el controlador no devolvió ninguno (o no hay asignados), sale la advertencia --}}
                                        @if($proceso->usuarios->isEmpty())
                                            <span class="text-danger small fw-medium" style="font-size: 10px;">
                                                <i class="bi bi-exclamation-triangle me-1"></i>Requiere responsables activos
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 border border-dashed rounded-4 bg-light-subtle">
                                <i class="bi bi-stack text-muted opacity-50 display-4"></i>
                                <p class="text-muted mt-2">No hay pasos definidos.</p>
                                <button class="btn btn-sm btn-outline-indigo mt-2" data-bs-toggle="modal" data-bs-target="#modalCrearProceso">
                                    <i class="bi bi-plus-circle me-1"></i>Agregar el primer paso
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                        {{-- Campo oculto para vincular al flujo actual --}}
                        <input type="hidden" name="flujo_id" value="{{ $flujo->id }}">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Nombre de la etapa</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-tag text-indigo"></i></span>
                                <input type="text" name="nombre" class="form-control border-start-0 ps-0" placeholder="Ej: Revisión Jurídica" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Instrucciones</label>
                            <textarea name="detalle" class="form-control" rows="3" placeholder="¿Qué debe hacer el responsable en este paso?"></textarea>
                        </div>

                        <div class="bg-light p-3 rounded-3 border d-flex justify-content-between align-items-center">
                            <div>
                                <label class="form-label fw-bold small text-muted text-uppercase mb-0 d-block">¿Paso Activo?</label>
                                <small class="text-muted">Si se desactiva, se saltará en el flujo.</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input ms-0" type="checkbox" name="activo" value="1" id="switchActivo" checked style="width: 2.4em; height: 1.2em;">
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

    {{-- Estilos --}}
    <style>
        .bg-soft-indigo { background-color: #f0f3ff; }
        .text-indigo { color: #4f46e5; }
        .bg-indigo { background-color: #4f46e5; }
        .btn-indigo { background-color: #4f46e5; color: white; }
        .btn-primary-neo { background-color: #4f46e5; color: white; border: none; padding: 0.5rem 1.2rem; border-radius: 10px; font-weight: 500; transition: all 0.2s; }
        .hover-up:hover { transform: translateY(-3px); border-color: #c7d2fe !important; }
    </style>
</x-base-layout>