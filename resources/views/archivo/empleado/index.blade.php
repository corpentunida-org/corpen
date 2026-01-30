<x-base-layout>
    <div class="main-container">
        <div class="card mb-4 shadow-sm">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h4 class="fw-bold mb-1 text-blue-king">Gestión de Empleados</h4>
                        <p class="text-muted mb-0">Administra el personal registrado</p>
                    </div>
                    
                    <div class="col-md-4">
                        <form method="GET" action="{{ route('archivo.empleado.index') }}">
                            @csrf
                            <div class="input-group border rounded shadow-sm">
                                <input type="search" name="search" class="form-control border-0" 
                                       placeholder="Buscar por cédula o nombre..." 
                                       value="{{ $search ?? '' }}">
                                <button class="btn btn-blue-king" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#crearEmpleadoModal">
                            <i class="bi bi-plus-circle me-2"></i> Nuevo Empleado
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="views-container">
            <div class="employee-list-container">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-semibold text-blue-king">Lista de Personal</h5>
                    </div>
                    
                    <div class="card-body p-0 employee-list">
                        @forelse ($empleados as $empleado)
                            <div class="p-3 employee-card {{ request('id') == $empleado->id ? 'active' : '' }}" 
                                 onclick="window.location.href='{{ route('archivo.empleado.index', ['id' => $empleado->id, 'search' => $search ?? '']) }}'"
                                 role="button">
                                <div class="d-flex align-items-center">
                                    @if ($empleado->ubicacion_foto)
                                        <img src="{{ route('archivo.empleado.verFoto', $empleado->id) }}" class="rounded-circle me-3 shadow-sm" style="width: 45px; height: 45px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-blue-king bg-opacity-10 text-blue-king d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 45px; height: 45px; font-weight: 600;">
                                            {{ strtoupper(substr($empleado->nombre1 ?? '?', 0, 1) . substr($empleado->apellido1 ?? '?', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-medium">{{ $empleado->nombre_completo }}</div>
                                        <small class="text-muted">C.C. {{ $empleado->cedula }}</small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center p-4">No se encontraron empleados</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="employee-details-container">
                @if (isset($empleadoSeleccionado))
                    <div class="card shadow-sm h-100" id="empleadoDetailCard">
                        <div class="card-header bg-white p-0">
                            <ul class="nav nav-tabs border-bottom-0" role="tablist">
                                <li class="nav-item"><a class="nav-link active fw-bold text-blue-king" data-bs-toggle="tab" href="#personal"><i class="bi bi-person-vcard me-2"></i>Personal</a></li>
                                <li class="nav-item"><a class="nav-link text-blue-king" data-bs-toggle="tab" href="#cargo"><i class="bi bi-briefcase me-2"></i>Cargo</a></li>
                                <li class="nav-item"><a class="nav-link text-blue-king" data-bs-toggle="tab" href="#documentos"><i class="bi bi-folder-check me-2"></i>Documentos</a></li>
                            </ul>
                        </div>
                        
                        <div class="card-body p-4" style="overflow-y: auto; max-height: calc(100vh - 300px);">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold mb-0 text-blue-king">Perfil de Empleado</h5>
                                <div class="d-flex gap-2">
                                    <button type="button" id="btn-editar" class="btn btn-sm btn-outline-blue-king shadow-sm"><i class="bi bi-pencil-square me-1"></i>Editar</button>
                                    <button type="button" id="btn-guardar" class="btn btn-sm btn-success d-none shadow-sm"><i class="bi bi-save me-1"></i>Guardar Cambios</button>
                                    <button type="button" id="btn-cancelar" class="btn btn-sm btn-light border d-none shadow-sm">Cancelar</button>
                                </div>
                            </div>
                            
                            <div class="tab-content">
                                {{-- PESTAÑA PERSONAL: MEJORA UX CON FLOATING LABELS --}}
                                <div class="tab-pane fade show active" id="personal" role="tabpanel">
                                    <form id="form-empleado" method="POST" action="{{ route('archivo.empleado.update', $empleadoSeleccionado->id) }}" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="row g-3">
                                            <div class="col-md-12 mb-3">
                                                <div class="vista-campo">
                                                    <small class="text-muted d-block"><i class="bi bi-camera me-1"></i>Foto de Perfil</small>
                                                    <span class="fw-bold text-dark">{{ $empleadoSeleccionado->ubicacion_foto ? 'Foto cargada satisfactoriamente' : 'El empleado no posee foto' }}</span>
                                                </div>
                                                <div class="edicion-campo" style="display:none">
                                                    <label class="form-label small text-muted fw-bold">Actualizar Foto de Perfil</label>
                                                    <input type="file" name="ubicacion_foto" class="form-control form-control-sm" accept="image/*">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="campo-ux">
                                                    <div class="vista-campo">
                                                        <small class="text-muted d-block"><i class="bi bi-card-text me-1"></i>Cédula</small>
                                                        <span class="fw-bold">{{ $empleadoSeleccionado->cedula }}</span>
                                                    </div>
                                                    <div class="edicion-campo form-floating" style="display:none">
                                                        <input type="text" name="cedula" class="form-control" value="{{ $empleadoSeleccionado->cedula }}" readonly>
                                                        <label>Número de Cédula</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="campo-ux">
                                                    <div class="vista-campo">
                                                        <small class="text-muted d-block"><i class="bi bi-calendar-event me-1"></i>Fecha Expedida</small>
                                                        <span class="fw-bold">{{ $empleadoSeleccionado->fecha_expedida ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="edicion-campo form-floating" style="display:none">
                                                        <input type="date" name="fecha_expedida" class="form-control" value="{{ $empleadoSeleccionado->fecha_expedida }}">
                                                        <label>Fecha Expedición</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="campo-ux">
                                                    <div class="vista-campo">
                                                        <small class="text-muted d-block"><i class="bi bi-geo-alt me-1"></i>Lugar Expedición</small>
                                                        <span class="fw-bold">{{ $empleadoSeleccionado->lugar_exp ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="edicion-campo form-floating" style="display:none">
                                                        <input type="text" name="lugar_exp" class="form-control" value="{{ $empleadoSeleccionado->lugar_exp }}">
                                                        <label>Lugar Expedición</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="campo-ux">
                                                    <div class="vista-campo">
                                                        <small class="text-muted d-block">Primer Nombre</small>
                                                        <span class="fw-bold">{{ $empleadoSeleccionado->nombre1 }}</span>
                                                    </div>
                                                    <div class="edicion-campo form-floating" style="display:none">
                                                        <input type="text" name="nombre1" class="form-control" value="{{ $empleadoSeleccionado->nombre1 }}">
                                                        <label>Primer Nombre</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="campo-ux">
                                                    <div class="vista-campo">
                                                        <small class="text-muted d-block">Segundo Nombre</small>
                                                        <span class="fw-bold">{{ $empleadoSeleccionado->nombre2 ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="edicion-campo form-floating" style="display:none">
                                                        <input type="text" name="nombre2" class="form-control" value="{{ $empleadoSeleccionado->nombre2 }}">
                                                        <label>Segundo Nombre</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="campo-ux">
                                                    <div class="vista-campo">
                                                        <small class="text-muted d-block">Primer Apellido</small>
                                                        <span class="fw-bold">{{ $empleadoSeleccionado->apellido1 }}</span>
                                                    </div>
                                                    <div class="edicion-campo form-floating" style="display:none">
                                                        <input type="text" name="apellido1" class="form-control" value="{{ $empleadoSeleccionado->apellido1 }}">
                                                        <label>Primer Apellido</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="campo-ux">
                                                    <div class="vista-campo">
                                                        <small class="text-muted d-block">Segundo Apellido</small>
                                                        <span class="fw-bold">{{ $empleadoSeleccionado->apellido2 ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="edicion-campo form-floating" style="display:none">
                                                        <input type="text" name="apellido2" class="form-control" value="{{ $empleadoSeleccionado->apellido2 }}">
                                                        <label>Segundo Apellido</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="campo-ux">
                                                    <div class="vista-campo">
                                                        <small class="text-muted d-block"><i class="bi bi-calendar3 me-1"></i>Fecha Nacimiento</small>
                                                        <span class="fw-bold">{{ $empleadoSeleccionado->nacimiento ? $empleadoSeleccionado->nacimiento->format('d/m/Y') : 'N/A' }}</span>
                                                    </div>
                                                    <div class="edicion-campo form-floating" style="display:none">
                                                        <input type="date" name="nacimiento" class="form-control" value="{{ $empleadoSeleccionado->nacimiento ? $empleadoSeleccionado->nacimiento->format('Y-m-d') : '' }}">
                                                        <label>Fecha Nacimiento</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="campo-ux">
                                                    <div class="vista-campo">
                                                        <small class="text-muted d-block"><i class="bi bi-geo-alt-fill me-1"></i>Lugar Nacimiento</small>
                                                        <span class="fw-bold">{{ $empleadoSeleccionado->lugar ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="edicion-campo form-floating" style="display:none">
                                                        <input type="text" name="lugar" class="form-control" value="{{ $empleadoSeleccionado->lugar }}">
                                                        <label>Lugar Nacimiento</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="campo-ux">
                                                    <div class="vista-campo">
                                                        <small class="text-muted d-block"><i class="bi bi-gender-ambiguous me-1"></i>Sexo</small>
                                                        <span class="fw-bold">{{ $empleadoSeleccionado->sexo == 'M' ? 'Masculino' : 'Femenino' }}</span>
                                                    </div>
                                                    <div class="edicion-campo form-floating" style="display:none">
                                                        <select name="sexo" class="form-select">
                                                            <option value="M" {{ $empleadoSeleccionado->sexo == 'M' ? 'selected' : '' }}>Masculino</option>
                                                            <option value="F" {{ $empleadoSeleccionado->sexo == 'F' ? 'selected' : '' }}>Femenino</option>
                                                        </select>
                                                        <label>Género</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="campo-ux">
                                                    <div class="vista-campo">
                                                        <small class="text-muted d-block"><i class="bi bi-house me-1"></i>Dirección Residencia</small>
                                                        <span class="fw-bold">{{ $empleadoSeleccionado->direccion_residencia ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="edicion-campo form-floating" style="display:none">
                                                        <input type="text" name="direccion_residencia" class="form-control" value="{{ $empleadoSeleccionado->direccion_residencia }}">
                                                        <label>Dirección</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="campo-ux">
                                                    <div class="vista-campo">
                                                        <small class="text-muted d-block"><i class="bi bi-hospital me-1"></i>Entidad EPS</small>
                                                        <span class="fw-bold">{{ $empleadoSeleccionado->entidad_eps ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="edicion-campo form-floating" style="display:none">
                                                        <input type="text" name="entidad_eps" class="form-control" value="{{ $empleadoSeleccionado->entidad_eps }}">
                                                        <label>Nombre de EPS</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="campo-ux">
                                                    <div class="vista-campo">
                                                        <small class="text-muted d-block"><i class="bi bi-envelope me-1"></i>Correo</small>
                                                        <span class="fw-bold">{{ $empleadoSeleccionado->correo_personal ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="edicion-campo form-floating" style="display:none">
                                                        <input type="email" name="correo_personal" class="form-control" value="{{ $empleadoSeleccionado->correo_personal }}">
                                                        <label>Correo Electrónico</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="campo-ux">
                                                    <div class="vista-campo">
                                                        <small class="text-muted d-block"><i class="bi bi-phone me-1"></i>Celular</small>
                                                        <span class="fw-bold">{{ $empleadoSeleccionado->celular_personal ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="edicion-campo form-floating" style="display:none">
                                                        <input type="text" name="celular_personal" class="form-control" value="{{ $empleadoSeleccionado->celular_personal }}">
                                                        <label>Celular</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="campo-ux">
                                                    <div class="vista-campo">
                                                        <small class="text-muted d-block"><i class="bi bi-telephone-plus me-1"></i>Acudiente</small>
                                                        <span class="fw-bold">{{ $empleadoSeleccionado->celular_acudiente ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="edicion-campo form-floating" style="display:none">
                                                        <input type="text" name="celular_acudiente" class="form-control" value="{{ $empleadoSeleccionado->celular_acudiente }}">
                                                        <label>Contacto Acudiente</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                {{-- PESTAÑA: CARGO (Versión Minimalista UX Pro) --}}
                                <div class="tab-pane fade" id="cargo" role="tabpanel">
                                    @if ($empleadoSeleccionado->cargo)
                                        <form id="form-cargo" method="POST" action="{{ route('archivo.cargo.update', $empleadoSeleccionado->cargo->id) }}">
                                            @csrf @method('PUT')
                                            
                                            <div class="row g-3">
                                                <div class="col-12 mt-2">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="bi bi-diagram-3 text-blue-king me-2"></i>
                                                        <span class="fw-bold text-blue-king small text-uppercase" style="letter-spacing: 1px;">Estructura y Posición</span>
                                                    </div>
                                                    <hr class="mt-0 mb-3 opacity-10">
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="vista-campo border-start border-blue-king border-3 ps-3 py-1">
                                                        <small class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Denominación</small>
                                                        <div class="text-dark fw-bold fs-5">{{ $empleadoSeleccionado->cargo->nombre_cargo }}</div>
                                                    </div>
                                                    <div class="edicion-campo" style="display:none">
                                                        <div class="form-floating">
                                                            <input type="text" name="nombre_cargo" class="form-control form-control-sm border-blue-king shadow-none" value="{{ $empleadoSeleccionado->cargo->nombre_cargo }}">
                                                            <label class="text-muted">Nombre del Cargo</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="vista-campo border-start border-blue-king border-3 ps-3 py-1">
                                                        <small class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Unidad / Área</small>
                                                        <div class="text-blue-king fw-bold fs-5">
                                                            {{ $empleadoSeleccionado->cargo->gdoArea->nombre ?? 'SIN ÁREA ASIGNADA' }}
                                                        </div>
                                                    </div>
                                                    <div class="edicion-campo" style="display:none">
                                                        <div class="form-floating">
                                                            <select name="GDO_area_id" class="form-select border-blue-king shadow-none">
                                                                @isset($areas)
                                                                    @foreach($areas as $area)
                                                                        <option value="{{ $area->id }}" {{ $empleadoSeleccionado->cargo->GDO_area_id == $area->id ? 'selected' : '' }}>{{ $area->nombre }}</option>
                                                                    @endforeach
                                                                @endisset
                                                            </select>
                                                            <label class="text-muted">Área Funcional</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mt-4">
                                                    <div class="vista-campo">
                                                        <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 10px;">Remuneración Base</small>
                                                        <span class="text-success fw-bold fs-5">${{ number_format($empleadoSeleccionado->cargo->salario_base, 2) }}</span>
                                                    </div>
                                                    <div class="edicion-campo" style="display:none">
                                                        <div class="form-floating">
                                                            <input type="number" step="0.01" name="salario_base" class="form-control border-success shadow-none" value="{{ $empleadoSeleccionado->cargo->salario_base }}">
                                                            <label class="text-success">Salario Base</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mt-4">
                                                    <div class="vista-campo">
                                                        <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 10px;">Jornada Laboral</small>
                                                        <span class="text-dark fw-bold">{{ $empleadoSeleccionado->cargo->jornada ?? 'NO DEFINIDA' }}</span>
                                                    </div>
                                                    <div class="edicion-campo" style="display:none">
                                                        <div class="form-floating">
                                                            <input type="text" name="jornada" class="form-control shadow-none" value="{{ $empleadoSeleccionado->cargo->jornada }}">
                                                            <label>Jornada</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mt-4 text-md-end">
                                                    <div class="vista-campo">
                                                        <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 10px;">Estatus</small>
                                                        <span class="badge {{ $empleadoSeleccionado->cargo->estado ? 'bg-primary' : 'bg-danger' }} px-3 py-2 rounded-pill">
                                                            {{ $empleadoSeleccionado->cargo->estado ? 'ACTIVO' : 'INACTIVO' }}
                                                        </span>
                                                    </div>
                                                    <div class="edicion-campo" style="display:none">
                                                        <div class="form-floating">
                                                            <select name="estado" class="form-select shadow-none">
                                                                <option value="1" {{ $empleadoSeleccionado->cargo->estado ? 'selected' : '' }}>Activo</option>
                                                                <option value="0" {{ !$empleadoSeleccionado->cargo->estado ? 'selected' : '' }}>Inactivo</option>
                                                            </select>
                                                            <label>Estado</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 mt-4">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="bi bi-telephone-inbound text-blue-king me-2"></i>
                                                        <span class="fw-bold text-blue-king small text-uppercase" style="letter-spacing: 1px;">Canales Corporativos</span>
                                                    </div>
                                                    <hr class="mt-0 mb-3 opacity-10">
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="vista-campo bg-white p-3 rounded border shadow-sm h-100">
                                                        <div class="mb-3">
                                                            <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 9px;">Email Institucional</small>
                                                            <span class="text-primary fw-bold">{{ $empleadoSeleccionado->cargo->correo_corporativo ?? 'N/A' }}</span>
                                                        </div>
                                                        <div>
                                                            <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 9px;">Gmail Auxiliar</small>
                                                            <span class="text-dark fw-bold">{{ $empleadoSeleccionado->cargo->gmail_corporativo ?? 'N/A' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="edicion-campo" style="display:none">
                                                        <div class="form-floating mb-2">
                                                            <input type="email" name="correo_corporativo" class="form-control shadow-none" value="{{ $empleadoSeleccionado->cargo->correo_corporativo }}">
                                                            <label>Email Corporativo</label>
                                                        </div>
                                                        <div class="form-floating">
                                                            <input type="email" name="gmail_corporativo" class="form-control shadow-none" value="{{ $empleadoSeleccionado->cargo->gmail_corporativo }}">
                                                            <label>Gmail Auxiliar</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="vista-campo bg-white p-3 rounded border shadow-sm h-100">
                                                        <div class="row">
                                                            <div class="col-6 mb-3">
                                                                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 9px;">Teléfono Fijo</small>
                                                                <span class="text-dark fw-bold">{{ $empleadoSeleccionado->cargo->telefono_corporativo ?? 'N/A' }}</span>
                                                            </div>
                                                            <div class="col-6 mb-3">
                                                                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 9px;">Extensión</small>
                                                                <span class="text-blue-king fw-bold">{{ $empleadoSeleccionado->cargo->ext_corporativo ?? '000' }}</span>
                                                            </div>
                                                            <div class="col-12">
                                                                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 9px;">Celular Corporativo</small>
                                                                <span class="text-dark fw-bold"><i class="bi bi-phone me-1"></i>{{ $empleadoSeleccionado->cargo->celular_corporativo ?? 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="edicion-campo" style="display:none">
                                                        <div class="row g-2">
                                                            <div class="col-8">
                                                                <div class="form-floating"><input type="text" name="telefono_corporativo" class="form-control shadow-none" value="{{ $empleadoSeleccionado->cargo->telefono_corporativo }}"><label>Teléfono</label></div>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="form-floating"><input type="text" name="ext_corporativo" class="form-control shadow-none" value="{{ $empleadoSeleccionado->cargo->ext_corporativo }}"><label>Ext.</label></div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="form-floating"><input type="text" name="celular_corporativo" class="form-control shadow-none" value="{{ $empleadoSeleccionado->cargo->celular_corporativo }}"><label>Móvil Corp.</label></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 mt-4">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="bi bi-card-checklist text-blue-king me-2"></i>
                                                        <span class="fw-bold text-blue-king small text-uppercase" style="letter-spacing: 1px;">Gestión de Funciones</span>
                                                    </div>
                                                    <hr class="mt-0 mb-3 opacity-10">
                                                </div>

                                                <div class="col-12">
                                                    <div class="vista-campo">
                                                        <div class="p-3 rounded-3" style="background-color: #f8f9fa; border: 1px dashed #dee2e6;">
                                                            <div class="mb-3">
                                                                <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 9px;">Manual de Funciones</small>
                                                                
                                                                @if(isset($empleadoSeleccionado->cargo) && $empleadoSeleccionado->cargo->manual_funciones)
                                                                    <a href="{{ route('archivo.cargo.verManual', $empleadoSeleccionado->cargo->id) }}" 
                                                                    target="_blank" 
                                                                    class="text-decoration-none shadow-sm p-1 px-2 border rounded bg-light d-inline-block transition-all"
                                                                    style="border-color: #e2e8f0 !important;">
                                                                        <span class="text-dark fw-medium small">
                                                                            <i class="bi bi-file-earmark-pdf-fill me-1 text-danger"></i> 
                                                                            Ver Manual de Funciones
                                                                        </span>
                                                                        <i class="bi bi-box-arrow-up-right ms-1 text-muted" style="font-size: 10px;"></i>
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted small italic">
                                                                        <i class="bi bi-file-earmark-x me-1"></i> Manual no disponible
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <div>
                                                                <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 9px;">Observaciones Adicionales</small>
                                                                <p class="text-dark small mb-0 lh-sm">{{ $empleadoSeleccionado->cargo->observacion ?? 'Sin observaciones registradas.' }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="edicion-campo" style="display:none">
                                                        <div class="form-floating mb-2">
                                                            <input type="text" name="manual_funciones" class="form-control shadow-none" value="{{ $empleadoSeleccionado->cargo->manual_funciones }}">
                                                            <label>Nombre/Link Manual de Funciones</label>
                                                        </div>
                                                        <div class="form-floating">
                                                            <textarea name="observacion" class="form-control shadow-none" style="height: 100px">{{ $empleadoSeleccionado->cargo->observacion }}</textarea>
                                                            <label>Observaciones de Gestión</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    @else
                                        <div class="text-center py-5">
                                            <div class="bg-light d-inline-block p-4 rounded-circle mb-3">
                                                <i class="bi bi-person-exclamation text-blue-king display-4"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark">Cargo no definido</h5>
                                            <p class="text-muted small">Este empleado no tiene una relación contractual de cargo activa.</p>
                                            <button class="btn btn-warning shadow-sm fw-bold px-4 rounded-pill btn-sm" data-bs-toggle="modal" data-bs-target="#asignarCargoModal">
                                                Asignar Cargo
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                {{-- PESTAÑA: DOCUMENTOS --}}
                                <div class="tab-pane fade" id="documentos">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="text-blue-king fw-bold mb-0">Documentos del Expediente</h6>
                                        <button type="button" class="btn btn-sm btn-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#subirDocumentoModal">
                                            <i class="bi bi-upload me-1"></i>Subir
                                        </button>
                                    </div>
                                    <div class="list-group list-group-flush">
                                        @forelse ($documentosDelEmpleado as $doc)
                                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                <div>
                                                    <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                                                    <span class="fw-medium">{{ $doc->tipoDocumento->nombre }}</span>
                                                </div>
                                                <div class="btn-group">
                                                    <a href="{{ route('archivo.empleado.verDocumento', $doc->id) }}" target="_blank" class="btn btn-sm btn-light border shadow-sm"><i class="bi bi-eye"></i></a>
                                                    <button onclick="confirmarEliminacion({{ $doc->id }})" class="btn btn-sm btn-light border text-danger shadow-sm"><i class="bi bi-trash"></i></button>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-center text-muted py-4 small">No se han registrado documentos aún.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card h-100 d-flex align-items-center justify-content-center border-0 bg-transparent opacity-50">
                        <div class="text-center">
                            <i class="bi bi-person-bounding-box display-1 text-blue-king"></i>
                            <h5 class="mt-3 fw-bold">Seleccione un empleado</h5>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="subirDocumentoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-blue-king text-white">
                    <h5 class="modal-title"><i class="bi bi-cloud-arrow-up me-2"></i>Subir Documento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-subir-documento" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <input type="hidden" name="empleado_id" value="{{ $empleadoSeleccionado->cedula ?? '' }}">
                        <div class="form-floating mb-3">
                            <select name="tipo_documento_id" id="val_tipo_id" class="form-select" required>
                                <option value="">Seleccione tipo...</option>
                                @isset($tiposDocumento)
                                    @foreach($tiposDocumento as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                @endisset
                            </select>
                            <label>Tipo de Documento</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="date" name="fecha_subida" id="val_fecha" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                            <label>Fecha de Registro</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Seleccionar Archivo</label>
                            <input type="file" name="archivo" id="archivo_input" class="form-control shadow-sm" required>
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-3">
                        <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-warning px-4 shadow-sm fw-bold" id="btn-subir-accion" onclick="procesarSubidaManual()">
                            Subir Documento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@push('scripts')
    <script>
        // FUNCIÓN MEJORADA: Subida AJAX blindada
        function procesarSubidaManual() {
            const btn = document.getElementById('btn-subir-accion');
            const fileInput = document.getElementById('archivo_input');
            const tipoInput = document.getElementById('val_tipo_id');

            if (!fileInput.files[0] || !tipoInput.value) {
                Swal.fire({ icon: 'warning', title: 'Faltan datos', text: 'Por favor complete todos los campos obligatorios.' });
                return;
            }

            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Subiendo...';

            const formData = new FormData(document.getElementById('form-subir-documento'));
            const url = "{{ route('archivo.empleado.storeDocumento') }}";

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Excelente', text: data.message, timer: 2000, showConfirmButton: false })
                    .then(() => {
                        window.location.hash = 'documentos';
                        window.location.reload();
                    });
                } else {
                    throw data;
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                let msg = err.message || (err.errors ? Object.values(err.errors).flat().join('\n') : 'Error técnico al subir');
                Swal.fire('Error', msg, 'error');
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Manejo persistente de tabs por Hash
            const hash = window.location.hash;
            if (hash) {
                const triggerEl = document.querySelector(`.nav-tabs a[href="${hash}"]`);
                if (triggerEl) bootstrap.Tab.getOrCreateInstance(triggerEl).show();
            }

            // Lógica UX Edición Personal
            const btnEd = document.getElementById('btn-editar');
            const btnSv = document.getElementById('btn-guardar');
            const btnCn = document.getElementById('btn-cancelar');

            if(btnEd) {
                btnEd.addEventListener('click', function() {
                    document.querySelectorAll('.vista-campo').forEach(el => el.style.display = 'none');
                    document.querySelectorAll('.edicion-campo').forEach(el => el.style.display = 'block');
                    this.classList.add('d-none');
                    btnSv.classList.remove('d-none');
                    btnCn.classList.remove('d-none');
                });
            }

            if(btnSv) {
                btnSv.addEventListener('click', function() {
                    const form = document.getElementById('form-empleado');
                    const fd = new FormData(form);
                    
                    btnSv.disabled = true;
                    btnSv.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                    fetch(form.action, {
                        method: 'POST',
                        body: fd,
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.json())
                    .then(d => {
                        if(d.success) {
                            Swal.fire({ icon: 'success', title: 'Perfil Actualizado', timer: 1500, showConfirmButton: false })
                            .then(() => window.location.reload());
                        } else {
                            btnSv.disabled = false;
                            btnSv.innerHTML = 'Guardar Cambios';
                            let errs = d.errors ? Object.values(d.errors).flat().join('\n') : 'No se pudo guardar';
                            Swal.fire('Error', errs, 'error');
                        }
                    });
                });
            }

            if(btnCn) btnCn.addEventListener('click', () => window.location.reload());
        });

        // Eliminar Documento (S3)
        function confirmarEliminacion(id) {
            Swal.fire({
                title: '¿Deseas eliminar el archivo?',
                text: "Esta acción lo borrará permanentemente de AWS S3.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                confirmButtonColor: '#d33',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ route('archivo.empleado.destroyDocumento', '') }}/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) window.location.reload();
                    });
                }
            });
        }
    </script>
@endpush
</x-base-layout>