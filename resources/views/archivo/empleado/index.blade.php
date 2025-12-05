<x-base-layout>
    <!-- CONTENEDOR PRINCIPAL -->
    <div class="main-container">
        <!-- PANEL DE CONTROL PRINCIPAL -->
        <div class="card mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <!-- Título y descripción del módulo -->
                    <div class="col-md-4">
                        <h4 class="fw-bold mb-1">Gestión de Empleados</h4>
                        <p class="text-muted mb-0">Administra el personal registrado</p>
                    </div>
                    
                    <!-- Formulario de búsqueda -->
                    <div class="col-md-4">
                        <form method="GET" action="{{ route('archivo.empleado.index') }}">
                            @csrf
                            <div class="input-group">
                                <input type="search" name="search" class="form-control" 
                                       placeholder="Buscar por cédula o nombre..." 
                                       value="{{ $search ?? '' }}">
                                <button class="btn btn-warning" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Botón para crear nuevo empleado -->
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#crearEmpleadoModal">
                            <i class="bi bi-plus-circle me-2"></i> Nuevo Empleado
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTENEDOR DE VISTAS LADO A LADO -->
        <div class="views-container">
            <!-- COLUMNA IZQUIERDA: LISTA DE EMPLEADOS -->
            <div class="employee-list-container">
                <div class="card h-100">
                    <!-- Header con contador de empleados -->
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-semibold">Empleados</h5>
                            <span class="badge bg-warning">{{ $empleados->count() }}</span>
                        </div>
                    </div>
                    
                    <!-- Lista scrollable de empleados -->
                    <div class="card-body p-0 employee-list">
                        @forelse ($empleados as $empleado)
                            <!-- Tarjeta individual de empleado -->
                            <div class="p-3 employee-card {{ request('id') == $empleado->id ? 'active' : '' }}" 
                                 onclick="window.location.href='{{ route('archivo.empleado.index', ['id' => $empleado->id, 'search' => $search ?? '']) }}'"
                                 role="button">
                                <div class="d-flex align-items-center">
                                    @if ($empleado->ubicacion_foto)
                                        <!-- Foto del empleado si existe -->
                                        <img src="{{ route('archivo.empleado.verFoto', $empleado->id) }}"
                                             alt="Foto de {{ $empleado->nombre_completo }}"
                                             class="rounded-circle me-3"
                                             style="width: 45px; height: 45px; object-fit: cover;">
                                    @else
                                        <!-- Iniciales si no hay foto -->
                                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center me-3" 
                                             style="width: 45px; height: 45px; font-weight: 600;">
                                            {{ strtoupper(substr($empleado->nombre1 ?? '?', 0, 1) . substr($empleado->apellido1 ?? '?', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-medium">{{ $empleado->nombre_completo }}</div>
                                        <small class="text-muted">{{ $empleado->cedula }}</small>
                                        @if($empleado->cargo)
                                            <div class="small text-warning">{{ $empleado->cargo->nombre_cargo }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <!-- Mensaje cuando no hay empleados -->
                            <div class="empty-state">
                                <i class="bi bi-people"></i>
                                <h6>No se encontraron empleados</h6>
                                <p class="small">Intenta ajustar tu búsqueda o registra al primer empleado.</p>
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#crearEmpleadoModal">
                                    <i class="bi bi-plus-circle me-1"></i>Registrar empleado
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: DETALLES DEL EMPLEADO -->
            <div class="employee-details-container">
                @if (isset($empleadoSeleccionado))
                    <!-- Contenedor principal con modo inicial de vista -->
                    <div class="card vista-mode h-100" id="empleadoDetailCard">
                        <!-- Navegación por pestañas (SIMPLIFICADA) -->
                        <div class="card-header bg-white p-0">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active text-warning" data-bs-toggle="tab" href="#personal" role="tab">
                                        <i class="bi bi-person me-1"></i>Personal
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-warning" data-bs-toggle="tab" href="#cargo" role="tab">
                                        <i class="bi bi-briefcase me-1"></i>Cargo
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-warning" data-bs-toggle="tab" href="#documentos" role="tab">
                                        <i class="bi bi-folder2-open me-1"></i>Documentos
                                    </a>
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Contenido de las pestañas -->
                        <div class="card-body" style="overflow-y: auto; max-height: calc(100vh - 300px);">
                            <!-- Cabecera con información básica y botones de acción -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="d-flex align-items-center">
                                    @if ($empleadoSeleccionado->ubicacion_foto)
                                        <img src="{{ route('archivo.empleado.verFoto', $empleadoSeleccionado->id) }}" 
                                             alt="Foto de {{ $empleadoSeleccionado->nombre_completo }}"
                                             id="fotoPreview"
                                             class="rounded-circle me-3"
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div id="fotoPreview"
                                             class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center me-3"
                                             style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: 600;">
                                            {{ substr($empleadoSeleccionado->nombre1, 0, 1) }}{{ substr($empleadoSeleccionado->apellido1, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h5 class="fw-bold mb-1">{{ $empleadoSeleccionado->nombre_completo }}</h5>
                                        <p class="text-muted mb-1">C.C. {{ $empleadoSeleccionado->cedula }}</p>
                                        @if($empleadoSeleccionado->cargo)
                                            <span class="badge bg-warning bg-opacity-10 text-white">{{ $empleadoSeleccionado->cargo->nombre_cargo }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Botones de acción: editar, guardar, cancelar -->
                                <div class="d-flex gap-2">
                                    <button type="button" id="btn-editar" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil me-1"></i> Editar
                                    </button>
                                    <button type="button" id="btn-guardar" class="btn btn-sm btn-success d-none">
                                        <i class="bi bi-check me-1"></i> Guardar
                                    </button>
                                    <button type="button" id="btn-cancelar" class="btn btn-sm btn-light d-none">
                                        <i class="bi bi-x me-1"></i> Cancelar
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Contenido dinámico de las pestañas -->
                            <div class="tab-content">
                                {{-- PESTAÑA: INFORMACIÓN PERSONAL --}}
                                <div class="tab-pane fade show active" id="personal" role="tabpanel">
                                    <form id="form-empleado" method="POST" action="{{ route('archivo.empleado.update', $empleadoSeleccionado->id) }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        
                                        <!-- Sección para la foto de perfil -->
                                        <div class="row g-3 mb-4 align-items-center">
                                            <div class="col-md-8">
                                                <div class="campo-empleado">
                                                    <div class="vista-campo">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-camera text-warning me-2"></i>
                                                            <div>
                                                                <small class="text-muted d-block">Foto de Perfil</small>
                                                                <span class="fw-medium">{{ $empleadoSeleccionado->ubicacion_foto ? 'Foto cargada' : 'Sin foto' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="edicion-campo">
                                                        <label for="ubicacion_foto" class="form-label">Foto de Perfil (Opcional)</label>
                                                        <input class="form-control @error('ubicacion_foto') is-invalid @enderror" type="file" id="ubicacion_foto" name="ubicacion_foto" accept="image/*">
                                                        @error('ubicacion_foto')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <small class="form-text text-muted">Archivos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB.</small>
                                                        
                                                        <!-- Vista previa de la foto actual en modo edición -->
                                                        <div class="mt-2 d-flex align-items-center" id="fotoPreviewContainer">
                                                            @if ($empleadoSeleccionado->ubicacion_foto)
                                                                <img src="{{ route('archivo.empleado.verFoto', $empleadoSeleccionado->id) }}" alt="Foto actual" class="img-thumbnail rounded-circle me-2" style="width: 60px; height: 60px; object-fit: cover;">
                                                                <small class="text-muted">Foto Actual</small>
                                                            @else
                                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width: 60px; height: 60px;">
                                                                    <i class="bi bi-person display-4 text-warning"></i>
                                                                </div>
                                                                <small class="text-muted">Sin foto</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <!-- Campo: Cédula (solo lectura) -->
                                            <div class="col-md-6">
                                                <div class="campo-empleado">
                                                    <div class="vista-campo">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-card-text text-warning me-2"></i>
                                                            <div>
                                                                <small class="text-muted d-block">Cédula</small>
                                                                <span class="fw-medium">{{ $empleadoSeleccionado->cedula }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="edicion-campo">
                                                        <div class="form-floating">
                                                            <input type="text" name="cedula" id="cedula" class="form-control" placeholder="Cédula" value="{{ $empleadoSeleccionado->cedula }}" readonly>
                                                            <label for="cedula">Cédula</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Campo: Correo Personal -->
                                            <div class="col-md-6">
                                                <div class="campo-empleado">
                                                    <div class="vista-campo">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-envelope text-warning me-2"></i>
                                                            <div>
                                                                <small class="text-muted d-block">Correo Personal</small>
                                                                <span class="fw-medium">{{ $empleadoSeleccionado->correo_personal ?? 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="edicion-campo">
                                                        <div class="form-floating">
                                                            <input type="email" name="correo_personal" id="correo_personal" class="form-control" placeholder="Correo Personal" value="{{ $empleadoSeleccionado->correo_personal }}">
                                                            <label for="correo_personal">Correo Personal</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Campos de nombre y apellido -->
                                            <div class="col-md-6">
                                                <div class="campo-empleado">
                                                    <div class="vista-campo">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-person text-warning me-2"></i>
                                                            <div>
                                                                <small class="text-muted d-block">Nombres</small>
                                                                <span class="fw-medium">{{ $empleadoSeleccionado->nombre1 }} {{ $empleadoSeleccionado->nombre2 ?? '' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="edicion-campo">
                                                        <div class="form-floating">
                                                            <input type="text" name="nombre1" id="nombre1" class="form-control" placeholder="Primer Nombre" value="{{ $empleadoSeleccionado->nombre1 }}">
                                                            <label for="nombre1">Primer Nombre</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="campo-empleado">
                                                    <div class="edicion-campo">
                                                        <div class="form-floating">
                                                            <input type="text" name="nombre2" id="nombre2" class="form-control" placeholder="Segundo Nombre" value="{{ $empleadoSeleccionado->nombre2 }}">
                                                            <label for="nombre2">Segundo Nombre</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="campo-empleado">
                                                    <div class="vista-campo">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-person text-warning me-2"></i>
                                                            <div>
                                                                <small class="text-muted d-block">Apellidos</small>
                                                                <span class="fw-medium">{{ $empleadoSeleccionado->apellido1 }} {{ $empleadoSeleccionado->apellido2 }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="edicion-campo">
                                                        <div class="form-floating">
                                                            <input type="text" name="apellido1" id="apellido1" class="form-control" placeholder="Primer Apellido" value="{{ $empleadoSeleccionado->apellido1 }}">
                                                            <label for="apellido1">Primer Apellido</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="campo-empleado">
                                                    <div class="edicion-campo">
                                                        <div class="form-floating">
                                                            <input type="text" name="apellido2" id="apellido2" class="form-control" placeholder="Segundo Apellido" value="{{ $empleadoSeleccionado->apellido2 }}">
                                                            <label for="apellido2">Segundo Apellido</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Campos de nacimiento y demografía -->
                                            <div class="col-md-6">
                                                <div class="campo-empleado">
                                                    <div class="vista-campo">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-calendar-event text-warning me-2"></i>
                                                            <div>
                                                                <small class="text-muted d-block">Fecha de Nacimiento</small>
                                                                <span class="fw-medium">{{ $empleadoSeleccionado->nacimiento ? $empleadoSeleccionado->nacimiento->format('d/m/Y') : 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="edicion-campo">
                                                        <div class="form-floating">
                                                            <input type="date" name="nacimiento" id="nacimiento" class="form-control" placeholder="Fecha de Nacimiento" value="{{ $empleadoSeleccionado->nacimiento ? $empleadoSeleccionado->nacimiento->format('Y-m-d') : '' }}">
                                                            <label for="nacimiento">Fecha de Nacimiento</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="campo-empleado">
                                                    <div class="vista-campo">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-geo-alt text-warning me-2"></i>
                                                            <div>
                                                                <small class="text-muted d-block">Lugar de Nacimiento</small>
                                                                <span class="fw-medium">{{ $empleadoSeleccionado->lugar ?? 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="edicion-campo">
                                                        <div class="form-floating">
                                                            <input type="text" name="lugar" id="lugar" class="form-control" placeholder="Lugar de Nacimiento" value="{{ $empleadoSeleccionado->lugar }}">
                                                            <label for="lugar">Lugar de Nacimiento</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="campo-empleado">
                                                    <div class="vista-campo">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-gender-ambiguous text-warning me-2"></i>
                                                            <div>
                                                                <small class="text-muted d-block">Sexo</small>
                                                                <span class="fw-medium">{{ $empleadoSeleccionado->sexo == 'M' ? 'Masculino' : ($empleadoSeleccionado->sexo == 'F' ? 'Femenino' : 'N/A') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="edicion-campo">
                                                        <div class="form-floating">
                                                            <select class="form-select" id="sexo" name="sexo">
                                                                <option value="">Seleccione...</option>
                                                                <option value="M" {{ $empleadoSeleccionado->sexo == 'M' ? 'selected' : '' }}>Masculino</option>
                                                                <option value="F" {{ $empleadoSeleccionado->sexo == 'F' ? 'selected' : '' }}>Femenino</option>
                                                            </select>
                                                            <label for="sexo">Sexo</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Campos de contacto -->
                                            <div class="col-md-6">
                                                <div class="campo-empleado">
                                                    <div class="vista-campo">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-phone text-warning me-2"></i>
                                                            <div>
                                                                <small class="text-muted d-block">Celular Personal</small>
                                                                <span class="fw-medium">{{ $empleadoSeleccionado->celular_personal ?? 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="edicion-campo">
                                                        <div class="form-floating">
                                                            <input type="text" name="celular_personal" id="celular_personal" class="form-control" placeholder="Celular Personal" value="{{ $empleadoSeleccionado->celular_personal }}">
                                                            <label for="celular_personal">Celular Personal</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="campo-empleado">
                                                    <div class="vista-campo">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-shield-lock text-warning me-2"></i>
                                                            <div>
                                                                <small class="text-muted d-block">Contacto Acudiente</small>
                                                                <span class="fw-medium">{{ $empleadoSeleccionado->celular_acudiente ?? 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="edicion-campo">
                                                        <div class="form-floating">
                                                            <input type="text" name="celular_acudiente" id="celular_acudiente" class="form-control" placeholder="Celular Acudiente" value="{{ $empleadoSeleccionado->celular_acudiente }}">
                                                            <label for="celular_acudiente">Celular Acudiente</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Pestaña: Cargo -->
                                <div class="tab-pane fade" id="cargo" role="tabpanel">
                                    @if ($empleadoSeleccionado->cargo)
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="card border-0 bg-light">
                                                    <div class="card-body">
                                                        <h6 class="fw-bold text-warning mb-3">Información del Cargo</h6>
                                                        <div class="mb-3">
                                                            <small class="text-muted d-block">Nombre del Cargo</small>
                                                            <p class="fw-medium mb-0">{{ $empleadoSeleccionado->cargo->nombre_cargo }}</p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <small class="text-muted d-block">Área Funcional</small>
                                                            <p class="fw-medium mb-0">{{ $empleadoSeleccionado->cargo->gdoArea->nombre ?? 'Sin área' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card border-0 bg-light">
                                                    <div class="card-body">
                                                        <h6 class="fw-bold text-warning mb-3">Detalles Adicionales</h6>
                                                        <div class="mb-3">
                                                            <small class="text-muted d-block">Salario Base</small>
                                                            <p class="fw-medium mb-0">{{ $empleadoSeleccionado->cargo->salario_base !== null ? '$'.number_format($empleadoSeleccionado->cargo->salario_base, 2, ',', '.') : '—' }}</p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <small class="text-muted d-block">Estado</small>
                                                            <p class="mb-0">
                                                                @if($empleadoSeleccionado->cargo->estado)
                                                                    <span class="badge bg-warning">Activo</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Inactivo</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="empty-state">
                                            <i class="bi bi-briefcase text-warning"></i>
                                            <h6>Sin Cargo Asignado</h6>
                                            <p class="small">Este empleado no tiene un cargo registrado actualmente.</p>
                                            <a href="#" class="btn btn-warning">Asignar Cargo</a>
                                        </div>
                                    @endif
                                </div>

                                {{-- PESTAÑA: DOCUMENTOS (MEJORADA Y CENTRALIZADA) --}}
                                <div class="tab-pane fade" id="documentos" role="tabpanel">
                                    <!-- Cabecera con botón para añadir documento -->
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="fw-semibold mb-0 text-warning">Documentos del Empleado</h5>
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#subirDocumentoModal">
                                            <i class="bi bi-plus-circle me-1"></i> Subir Documento
                                        </button>
                                    </div>

                                    <!-- Filtros de documentos -->
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <div class="card bg-light">
                                                <div class="card-body p-3">
                                                    <div class="d-flex flex-wrap gap-2">
                                                        <button class="btn btn-sm btn-outline-warning active" data-filter="todos">
                                                            <i class="bi bi-grid-3x3-gap me-1"></i> Todos
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-warning" data-filter="contratos">
                                                            <i class="bi bi-file-earmark-text me-1"></i> Contratos
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-warning" data-filter="permisos">
                                                            <i class="bi bi-calendar-check me-1"></i> Permisos
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-warning" data-filter="medicos">
                                                            <i class="bi bi-heart-pulse me-1"></i> Obs. Médicas
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-warning" data-filter="dotaciones">
                                                            <i class="bi bi-person-badge me-1"></i> Dotaciones
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-warning" data-filter="afiliaciones">
                                                            <i class="bi bi-shield-check me-1"></i> Afiliaciones
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-warning" data-filter="nomina">
                                                            <i class="bi bi-receipt me-1"></i> Nómina
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-warning" data-filter="otros">
                                                            <i class="bi bi-file-earmark me-1"></i> Otros
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Contenedor de documentos -->
                                    <div class="documentos-container">
                                        @forelse ($documentosDelEmpleado as $doc)
                                            @php
                                                $tipoNombre = strtolower($doc->tipoDocumento->nombre ?? '');
                                                $categoria = 'otros';
                                                $icono = 'bi bi-file-earmark';
                                                $bgColor = 'bg-dark';

                                                if (str_contains($tipoNombre, 'contrato')) { $categoria = 'contratos'; $icono = 'bi bi-file-earmark-text'; $bgColor = 'bg-primary'; }
                                                elseif (str_contains($tipoNombre, 'permiso') || str_contains($tipoNombre, 'licencia')) { $categoria = 'permisos'; $icono = 'bi bi-calendar-check'; $bgColor = 'bg-success'; }
                                                elseif (str_contains($tipoNombre, 'medic') || str_contains($tipoNombre, 'incapacidad')) { $categoria = 'medicos'; $icono = 'bi bi-heart-pulse'; $bgColor = 'bg-danger'; }
                                                elseif (str_contains($tipoNombre, 'dotacion') || str_contains($tipoNombre, 'uniforme')) { $categoria = 'dotaciones'; $icono = 'bi bi-person-badge'; $bgColor = 'bg-info'; }
                                                elseif (str_contains($tipoNombre, 'afiliacion') || str_contains($tipoNombre, 'seguridad') || str_contains($tipoNombre, 'eps') || str_contains($tipoNombre, 'pension') || str_contains($tipoNombre, 'arl')) { $categoria = 'afiliaciones'; $icono = 'bi bi-shield-check'; $bgColor = 'bg-secondary'; }
                                                elseif (str_contains($tipoNombre, 'nomina') || str_contains($tipoNombre, 'pago') || str_contains($tipoNombre, 'comprobante')) { $categoria = 'nomina'; $icono = 'bi bi-receipt'; $bgColor = 'bg-warning'; }
                                            @endphp
                                            <!-- Tarjeta de documento con categoría -->
                                            <div class="card mb-3 documento-item" data-category="{{ $categoria }}">
                                                <div class="card-body p-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-3">
                                                                <div class="rounded-circle d-flex align-items-center justify-content-center {{ $bgColor }}" style="width: 45px; height: 45px;">
                                                                    <i class="{{ $icono }} text-white"></i>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <span class="fw-medium">{{ $doc->tipoDocumento->nombre ?? 'Documento sin tipo' }}</span>
                                                                <small class="text-muted d-block">Subido: {{ $doc->fecha_subida ? $doc->fecha_subida->format('d/m/Y') : 'N/A' }}</small>
                                                            </div>
                                                        </div>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('gdodocsempleados.ver', $doc->id) }}" 
                                                               target="_blank" 
                                                               class="btn btn-sm btn-outline-warning" 
                                                               title="Ver">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                            <a href="{{ route('gdodocsempleados.download', $doc->id) }}" 
                                                               class="btn btn-sm btn-outline-warning" 
                                                               title="Descargar">
                                                                <i class="bi bi-download"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                    title="Eliminar"
                                                                    onclick="confirmarEliminacion({{ $doc->id }})">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <!-- Mensaje cuando no hay documentos -->
                                            <div class="empty-state">
                                                <i class="bi bi-folder2-open text-warning"></i>
                                                <h6>Sin Documentos Adjuntos</h6>
                                                <p class="small">Aún no se han cargado documentos para este perfil.</p>
                                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#subirDocumentoModal">
                                                    <i class="bi bi-plus-circle me-1"></i> Subir primer documento
                                                </button>
                                            </div>
                                        @endforelse
                                        
                                        @if ($documentosDelEmpleado->hasPages())
                                            <!-- Paginación de documentos -->
                                            <div class="mt-4">{{ $documentosDelEmpleado->links('pagination::bootstrap-5-sm') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Mensaje cuando no hay empleado seleccionado -->
                    <div class="card h-100">
                        <div class="card-body text-center py-5 d-flex flex-column justify-content-center h-100">
                            <i class="bi bi-person-lines-fill display-1 text-warning opacity-50 mb-3"></i>
                            <h5 class="fw-semibold">Selecciona un empleado</h5>
                            <p class="text-muted">Haz clic en un empleado de la lista para ver sus detalles.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- MODAL PARA CREAR NUEVO EMPLEADO -->
    <div class="modal fade" id="crearEmpleadoModal" tabindex="-1" aria-labelledby="crearEmpleadoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="crearEmpleadoModalLabel">
                        <i class="bi bi-person-plus me-2 text-warning"></i> Registrar Nuevo Empleado
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-crear-empleado" action="{{ route('archivo.empleado.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Campo: Foto de Perfil -->
                            <div class="col-md-12">
                                <label for="crear_ubicacion_foto" class="form-label">Foto de Perfil (Opcional)</label>
                                <div class="d-flex align-items-center gap-3">
                                    <input class="form-control @error('ubicacion_foto') is-invalid @enderror" type="file" id="crear_ubicacion_foto" name="ubicacion_foto" accept="image/*">
                                    <div id="crear_foto_preview" class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <i class="bi bi-person display-4 text-warning"></i>
                                    </div>
                                </div>
                                @error('ubicacion_foto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Archivos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB.</small>
                            </div>
                            
                            <!-- Campos obligatorios -->
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" name="cedula" id="crear_cedula" class="form-control @error('cedula') is-invalid @enderror" placeholder="Cédula" value="{{ old('cedula') }}" required>
                                    <label for="crear_cedula">Cédula</label>
                                    @error('cedula') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" name="nombre1" id="crear_nombre1" class="form-control @error('nombre1') is-invalid @enderror" placeholder="Primer Nombre" value="{{ old('nombre1') }}" required>
                                    <label for="crear_nombre1">Primer Nombre</label>
                                    @error('nombre1') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" name="apellido1" id="crear_apellido1" class="form-control @error('apellido1') is-invalid @enderror" placeholder="Primer Apellido" value="{{ old('apellido1') }}" required>
                                    <label for="crear_apellido1">Primer Apellido</label>
                                    @error('apellido1') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            
                            <!-- Campos opcionales -->
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" name="nombre2" id="crear_nombre2" class="form-control" placeholder="Segundo Nombre" value="{{ old('nombre2') }}">
                                    <label for="crear_nombre2">Segundo Nombre</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" name="apellido2" id="crear_apellido2" class="form-control" placeholder="Segundo Apellido" value="{{ old('apellido2') }}">
                                    <label for="crear_apellido2">Segundo Apellido</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="date" name="nacimiento" id="crear_nacimiento" class="form-control @error('nacimiento') is-invalid @enderror" placeholder="Fecha de Nacimiento" value="{{ old('nacimiento') }}">
                                    <label for="crear_nacimiento">Fecha de Nacimiento</label>
                                    @error('nacimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" name="lugar" id="crear_lugar" class="form-control" placeholder="Lugar de Nacimiento" value="{{ old('lugar') }}">
                                    <label for="crear_lugar">Lugar de Nacimiento</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select @error('sexo') is-invalid @enderror" id="crear_sexo" name="sexo">
                                        <option value="" selected>Seleccione...</option>
                                        <option value="M" {{ old('sexo') == 'M' ? 'selected' : '' }}>Masculino</option>
                                        <option value="F" {{ old('sexo') == 'F' ? 'selected' : '' }}>Femenino</option>
                                    </select>
                                    <label for="crear_sexo">Sexo</label>
                                    @error('sexo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            
                            <!-- Campos de contacto -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" name="correo_personal" id="crear_correo_personal" class="form-control @error('correo_personal') is-invalid @enderror" placeholder="Correo Personal" value="{{ old('correo_personal') }}">
                                    <label for="crear_correo_personal">Correo Personal</label>
                                    @error('correo_personal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="celular_personal" id="crear_celular_personal" class="form-control" placeholder="Celular Personal" value="{{ old('celular_personal') }}">
                                    <label for="crear_celular_personal">Celular Personal</label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="celular_acudiente" id="crear_celular_acudiente" class="form-control" placeholder="Celular Acudiente" value="{{ old('celular_acudiente') }}">
                                    <label for="crear_celular_acudiente">Celular Acudiente</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-circle me-1"></i> Guardar Empleado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL PARA SUBIR DOCUMENTO -->
    <div class="modal fade" id="subirDocumentoModal" tabindex="-1" aria-labelledby="subirDocumentoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subirDocumentoModalLabel">
                        <i class="bi bi-cloud-upload me-2 text-warning"></i> Subir Documento para {{ $empleadoSeleccionado->nombre_completo ?? 'Empleado' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('archivo.gdodocsempleados.store') }}" method="POST" enctype="multipart/form-data" id="form-subir-documento">
                    @csrf
                    <div class="modal-body">
                        <div class="d-flex flex-column gap-3">
                            <!-- Campo Empleado (pre-seleccionado y oculto) -->
                            <input type="hidden" name="empleado_id" id="empleado_id_doc" value="{{ $empleadoSeleccionado->cedula }}">

                            <!-- Campo Tipo de Documento -->
                            <div class="form-floating">
                                <select name="tipo_documento_id" id="tipo_documento_id" class="form-select" required>
                                    <option value="" disabled selected>Abre para seleccionar...</option>
                                    @if(isset($tiposDocumento))
                                        @foreach($tiposDocumento as $tipo)
                                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="tipo_documento_id">Tipo de Documento</label>
                            </div>
                            
                            <!-- Campo Fecha -->
                            <div class="form-floating">
                                <input type="date" name="fecha_subida" id="fecha_subida" class="form-control" 
                                       placeholder="Fecha de Subida" value="{{ now()->format('Y-m-d') }}" required>
                                <label for="fecha_subida">Fecha de Subida</label>
                            </div>

                            <!-- Campo de Archivo -->
                            <div class="form-floating">
                                <input type="text" id="archivo-display" class="form-control" placeholder="Archivo" 
                                       readonly style="cursor: pointer;">
                                <label for="archivo-display">Archivo</label>
                                <!-- El input real está oculto -->
                                <input type="file" name="archivo" id="archivo-real" class="visually-hidden" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg me-1"></i> Subir Documento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- Librerías JavaScript externas -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Configuración de notificaciones Toastr
                toastr.options = { 
                    "closeButton": true, 
                    "progressBar": true, 
                    "positionClass": "toast-bottom-right",
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000"
                };

                // Mostrar notificaciones de sesión si existen
                @if (session('success'))
                    toastr.success("{{ session('success') }}");
                @endif

                /*
                    ===================================================================
                    SISTEMA DE EDICIÓN EN LÍNEA (PESTAÑA PERSONAL)
                    ===================================================================
                */
                const btnEditar = document.getElementById('btn-editar');
                const btnGuardar = document.getElementById('btn-guardar');
                const btnCancelar = document.getElementById('btn-cancelar');
                const empleadoDetailCard = document.getElementById('empleadoDetailCard');
                const formEmpleado = document.getElementById('form-empleado');
                
                let valoresOriginales = {};
                let fotoPreviewOriginal = '';
                
                function toggleCampos(modo) {
                    const camposVista = document.querySelectorAll('.vista-campo');
                    const camposEdicion = document.querySelectorAll('.edicion-campo');
                    
                    if (modo === 'vista') {
                        camposVista.forEach(campo => campo.style.display = 'block');
                        camposEdicion.forEach(campo => campo.style.display = 'none');
                    } else {
                        camposVista.forEach(campo => campo.style.display = 'none');
                        camposEdicion.forEach(campo => campo.style.display = 'block');
                    }
                }
                
                function activarModoEdicion() {
                    const campos = formEmpleado.querySelectorAll('input, select');
                    campos.forEach(campo => valoresOriginales[campo.name] = campo.value);
                    
                    const fotoPreviewContainer = document.getElementById('fotoPreviewContainer');
                    if (fotoPreviewContainer) fotoPreviewOriginal = fotoPreviewContainer.innerHTML;
                    
                    empleadoDetailCard.classList.remove('vista-mode');
                    empleadoDetailCard.classList.add('edicion-mode');
                    toggleCampos('edicion');
                    
                    btnEditar.classList.add('d-none');
                    btnGuardar.classList.remove('d-none');
                    btnCancelar.classList.remove('d-none');
                    
                    document.querySelectorAll('.nav-tabs .nav-link:not([href="#personal"])').forEach(tab => {
                        tab.setAttribute('data-bs-toggle', '');
                        tab.classList.add('disabled');
                    });
                }
                
                function cancelarEdicion() {
                    const campos = formEmpleado.querySelectorAll('input, select');
                    campos.forEach(campo => {
                        if (campo.type !== 'file') campo.value = valoresOriginales[campo.name];
                    });
                    
                    const fotoPreviewContainer = document.getElementById('fotoPreviewContainer');
                    if (fotoPreviewContainer && fotoPreviewOriginal) {
                        fotoPreviewContainer.innerHTML = fotoPreviewOriginal;
                    }
                    
                    const fileInput = document.getElementById('ubicacion_foto');
                    if (fileInput) fileInput.value = '';
                    
                    empleadoDetailCard.classList.remove('edicion-mode');
                    empleadoDetailCard.classList.add('vista-mode');
                    toggleCampos('vista');
                    
                    btnEditar.classList.remove('d-none');
                    btnGuardar.classList.add('d-none');
                    btnCancelar.classList.add('d-none');
                    
                    document.querySelectorAll('.nav-tabs .nav-link').forEach(tab => {
                        tab.setAttribute('data-bs-toggle', 'tab');
                        tab.classList.remove('disabled');
                    });
                }
                
                if (btnEditar) btnEditar.addEventListener('click', activarModoEdicion);
                if (btnCancelar) btnCancelar.addEventListener('click', cancelarEdicion);

                if (btnGuardar) {
                    btnGuardar.addEventListener('click', function() {
                        const formData = new FormData(formEmpleado);
                        
                        fetch(formEmpleado.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload(); // Recarga para mostrar cambios
                            } else {
                                if (data.errors) {
                                    let errorMessage = 'Por favor, corrige los siguientes errores:<br><ul>';
                                    for (const field in data.errors) errorMessage += `<li>${data.errors[field][0]}</li>`;
                                    errorMessage += '</ul>';
                                    Swal.fire({ icon: 'error', title: 'Error de validación', html: errorMessage });
                                } else {
                                    toastr.error('Ha ocurrido un error al actualizar el empleado.');
                                }
                            }
                        })
                        .catch(error => toastr.error('Ha ocurrido un error al actualizar el empleado.'));
                    });
                }
                
                // Inicializar campos en modo vista
                if (empleadoDetailCard && empleadoDetailCard.classList.contains('vista-mode')) {
                    toggleCampos('vista');
                }

                /*
                    ===================================================================
                    FUNCIONALIDAD PARA MODAL DE CREAR EMPLEADO
                    ===================================================================
                */
                const formCrearEmpleado = document.getElementById('form-crear-empleado');
                const crearFotoInput = document.getElementById('crear_ubicacion_foto');
                const crearFotoPreview = document.getElementById('crear_foto_preview');
                
                if (crearFotoInput && crearFotoPreview) {
                    crearFotoInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                crearFotoPreview.innerHTML = `<img src="${e.target.result}" alt="Vista previa" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }
                
                const editarFotoInput = document.getElementById('ubicacion_foto');
                if (editarFotoInput) {
                    editarFotoInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const previewContainer = editarFotoInput.parentElement.querySelector('#fotoPreviewContainer');
                                if (previewContainer) {
                                    previewContainer.innerHTML = `<img src="${e.target.result}" alt="Vista previa" class="img-thumbnail rounded-circle me-2" style="width: 60px; height: 60px; object-fit: cover;"><small class="text-muted">Nueva Foto</small>`;
                                }
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }
                
                if (formCrearEmpleado) {
                    formCrearEmpleado.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const submitBtn = this.querySelector('button[type="submit"]');
                        const originalText = submitBtn.innerHTML;
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
                        
                        const formData = new FormData(this);
                        
                        fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(response => response.json())
                        .then(data => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                            if (data.success) {
                                bootstrap.Modal.getInstance(document.getElementById('crearEmpleadoModal')).hide();
                                this.reset();
                                crearFotoPreview.innerHTML = '<i class="bi bi-person display-4 text-warning"></i>';
                                toastr.success(data.message);
                                setTimeout(() => window.location.href = `{{ route('archivo.empleado.index') }}?id=${data.empleado.id}`, 1000);
                            } else {
                                // Manejar errores...
                            }
                        })
                        .catch(error => { /* manejar error */ });
                    });
                }

                /*
                    ===================================================================
                    FUNCIONALIDAD PARA PESTAÑA DE DOCUMENTOS
                    ===================================================================
                */
                // Filtros de documentos
                const filterButtons = document.querySelectorAll('[data-filter]');
                const documentItems = document.querySelectorAll('.documento-item');
                
                filterButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        filterButtons.forEach(btn => btn.classList.remove('active'));
                        this.classList.add('active');
                        
                        const filter = this.getAttribute('data-filter');
                        documentItems.forEach(item => {
                            item.style.display = (filter === 'todos' || item.getAttribute('data-category') === filter) ? 'block' : 'none';
                        });
                    });
                });

                // Funcionalidad para el campo de archivo en el modal de subir documento
                const archivoDisplay = document.getElementById('archivo-display');
                const archivoReal = document.getElementById('archivo-real');
                
                if (archivoDisplay && archivoReal) {
                    archivoDisplay.addEventListener('click', () => archivoReal.click());
                    archivoReal.addEventListener('change', function() {
                        archivoDisplay.value = this.files && this.files[0] ? this.files[0].name : '';
                    });
                }

                // Envío del formulario de subir documento
                const formSubirDoc = document.getElementById('form-subir-documento');
                if(formSubirDoc) {
                    formSubirDoc.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const submitBtn = this.querySelector('button[type="submit"]');
                        const originalText = submitBtn.innerHTML;
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Subiendo...';
                        
                        const formData = new FormData(this);
                        fetch(this.action, { method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(response => response.json())
                        .then(data => {
                            submitBtn.disabled = false; submitBtn.innerHTML = originalText;
                            if(data.success) {
                                bootstrap.Modal.getInstance(document.getElementById('subirDocumentoModal')).hide();
                                this.reset();
                                archivoDisplay.value = '';
                                toastr.success(data.message);
                                // Recargar la lista de documentos sin recargar la página completa es complejo,
                                // así que una recarga completa es la solución más robusta y simple aquí.
                                window.location.reload();
                            } else {
                                // Manejar errores de validación
                                let errorMessage = 'Por favor, corrige los siguientes errores:<br><ul>';
                                for (const field in data.errors) errorMessage += `<li>${data.errors[field][0]}</li>`;
                                errorMessage += '</ul>';
                                Swal.fire({ icon: 'error', title: 'Error de validación', html: errorMessage });
                            }
                        })
                        .catch(error => {
                            submitBtn.disabled = false; submitBtn.innerHTML = originalText;
                            toastr.error('Ha ocurrido un error al subir el documento.');
                        });
                    });
                }

                // Función para confirmar eliminación de documento
                window.confirmarEliminacion = function(docId) {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Esta acción no se puede revertir",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/archivo/gdodocsempleados/${docId}`, {
                                method: 'DELETE',
                                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'X-Requested-With': 'XMLHttpRequest' }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    toastr.success(data.message);
                                    window.location.reload(); // Recarga para actualizar la lista
                                } else {
                                    toastr.error('Ha ocurrido un error al eliminar el documento.');
                                }
                            })
                            .catch(error => toastr.error('Ha ocurrido un error al eliminar el documento.'));
                        }
                    });
                };
            });
        </script>
    @endpush
</x-base-layout>