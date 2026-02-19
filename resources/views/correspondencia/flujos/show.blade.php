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
                {{-- NUEVO: Botón de TRD dinámico en el encabezado --}}
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

                            {{-- NUEVO: ESTADO DE LA TRD --}}
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
                                        @foreach($proceso->usuarios as $user)
                                            <span class="badge border text-dark bg-light shadow-none" style="font-size: 10px; font-weight: 500;">
                                                <i class="bi bi-person me-1"></i>{{ $user->name }}
                                            </span>
                                        @endforeach

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

    {{-- NUEVO: MODAL TRD (Solo se renderiza si no hay TRD vinculada) --}}
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
                        {{-- Vinculación oculta y automática al flujo actual --}}
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
        .bg-soft-indigo { background-color: #f0f3ff; }
        .text-indigo { color: #4f46e5; }
        .bg-indigo { background-color: #4f46e5; }
        .btn-indigo { background-color: #4f46e5; color: white; }
        .btn-primary-neo { background-color: #4f46e5; color: white; border: none; padding: 0.5rem 1.2rem; border-radius: 10px; font-weight: 500; transition: all 0.2s; }
        .hover-up:hover { transform: translateY(-3px); border-color: #c7d2fe !important; }
    </style>

    {{-- Script AJAX para guardar TRD sin salir de la página --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                            // Capturamos la respuesta del servidor
                            const data = await response.json();
                            console.error("Error del servidor:", data);

                            // Si es un error de validación (Status 422)
                            if (response.status === 422 && data.errors) {
                                let mensajes = [];
                                for (let campo in data.errors) {
                                    mensajes.push("• " + data.errors[campo][0]);
                                }
                                alert("No se pudo guardar por estos errores:\n\n" + mensajes.join("\n"));
                            } else {
                                // Otro tipo de error (500, etc)
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