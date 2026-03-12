<x-base-layout>
    @push('styles')
    <style>
        /* Mejoras UX Customizadas */
        /* Scrollbar elegante para contenedores internos */
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f8f9fa; border-radius: 8px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #c5d0e6; border-radius: 8px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #0a4275; }

        /* Animaciones suaves para la interfaz */
        .fade-in { animation: fadeIn 0.4s cubic-bezier(0.39, 0.57, 0.04, 1) forwards; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Tarjetas de empleados iterativas */
        .employee-card { 
            transition: all 0.25s ease-in-out; 
            border-radius: 10px; 
            border: 1px solid transparent;
            margin-bottom: 0.25rem;
        }
        .employee-card:hover { 
            background-color: #f8f9fc; 
            transform: translateY(-2px); 
            border-color: #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .employee-card.active { 
            background-color: #f0f4f8; 
            border-color: #c5d0e6; 
            border-left: 4px solid #0a4275; 
        }

        /* Mejoras en inputs al editar */
        .edicion-campo .form-control:focus, 
        .edicion-campo .form-select:focus { 
            box-shadow: 0 0 0 0.25rem rgba(10, 66, 117, 0.15); 
            border-color: #0a4275; 
        }

        /* Botones transiciones */
        .btn { transition: all 0.2s ease; }
        .btn:hover { transform: translateY(-1px); }
    </style>
    @endpush

    <div class="main-container">
        <div class="card mb-4 shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <h4 class="fw-bold mb-1 text-blue-king d-flex align-items-center">
                            <i class="bi bi-people-fill me-2 opacity-75"></i>Gestión de Empleados
                        </h4>
                        <p class="text-muted mb-0 small">Administra el personal registrado</p>
                    </div>
                    
                    <div class="col-md-4 mb-3 mb-md-0">
                        <form method="GET" action="{{ route('archivo.empleado.index') }}">
                            @csrf
                            <div class="input-group rounded-pill shadow-sm overflow-hidden" style="border: 1px solid #dee2e6;">
                                <input type="search" name="search" class="form-control border-0 bg-light px-4" 
                                       placeholder="Buscar por cédula o nombre..." 
                                       value="{{ $search ?? '' }}">
                                <button class="btn btn-blue-king px-4" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="col-md-4 text-md-end">
                        <button type="button" class="btn btn-warning shadow-sm rounded-pill px-4 fw-medium" data-bs-toggle="modal" data-bs-target="#crearEmpleadoModal">
                            <i class="bi bi-plus-circle me-2"></i> Nuevo Empleado
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="views-container row g-4">
            <div class="employee-list-container col-lg-4 col-md-5">
                <div class="card h-100 shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white py-3 border-bottom-0 rounded-top-4">
                        <h5 class="mb-0 fw-bold text-blue-king"><i class="bi bi-list-ul me-2"></i>Lista de Personal</h5>
                    </div>
                    
                    <div class="card-body p-2 employee-list custom-scroll" style="overflow-y: auto; max-height: calc(100vh - 250px);">
                        @forelse ($empleados as $empleado)
                            <div class="p-3 employee-card {{ request('id') == $empleado->id ? 'active shadow-sm' : '' }}" 
                                 onclick="window.location.href='{{ route('archivo.empleado.index', ['id' => $empleado->id, 'search' => $search ?? '']) }}'"
                                 role="button">
                                <div class="d-flex align-items-center">
                                    @if ($empleado->ubicacion_foto)
                                        <img src="{{ route('archivo.empleado.verFoto', $empleado->id) }}" class="rounded-circle me-3 shadow-sm border" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-blue-king bg-opacity-10 text-blue-king d-flex align-items-center justify-content-center me-3 shadow-sm border" style="width: 50px; height: 50px; font-weight: 700; font-size: 1.1rem;">
                                            {{ strtoupper(substr($empleado->nombre1 ?? '?', 0, 1) . substr($empleado->apellido1 ?? '?', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark">{{ $empleado->nombre_completo }}</div>
                                        <small class="text-muted d-flex align-items-center"><i class="bi bi-person-vcard me-1 opacity-75"></i>C.C. {{ $empleado->cedula }}</small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center p-5 text-muted">
                                <i class="bi bi-inbox display-4 d-block mb-3 opacity-50"></i>
                                No se encontraron empleados
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="employee-details-container col-lg-8 col-md-7">
                @if (isset($empleadoSeleccionado))
                    <div class="card shadow-sm h-100 border-0 rounded-4" id="empleadoDetailCard">
                        <div class="card-header bg-white p-0 rounded-top-4 overflow-hidden border-bottom">
                            <ul class="nav nav-tabs border-bottom-0 px-3 pt-2" role="tablist">
                                <li class="nav-item"><a class="nav-link active fw-bold text-blue-king px-4 py-3" data-bs-toggle="tab" href="#personal"><i class="bi bi-person-vcard me-2"></i>Personal</a></li>
                                <li class="nav-item"><a class="nav-link text-blue-king px-4 py-3 fw-medium" data-bs-toggle="tab" href="#cargo"><i class="bi bi-briefcase me-2"></i>Cargo</a></li>
                                <li class="nav-item"><a class="nav-link text-blue-king px-4 py-3 fw-medium" data-bs-toggle="tab" href="#documentos"><i class="bi bi-folder-check me-2"></i>Documentos</a></li>
                            </ul>
                        </div>
                        
                        <div class="card-body p-4 custom-scroll" style="overflow-y: auto; max-height: calc(100vh - 250px);">
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                                <h5 class="fw-bold mb-0 text-blue-king"><i class="bi bi-person-badge me-2"></i>Perfil de Empleado</h5>
                                <div class="d-flex gap-2">
                                    <button type="button" id="btn-editar" class="btn btn-sm btn-outline-blue-king rounded-pill px-3 shadow-sm"><i class="bi bi-pencil-square me-1"></i>Editar Información</button>
                                    <button type="button" id="btn-cancelar" class="btn btn-sm btn-light border rounded-pill px-3 d-none shadow-sm"><i class="bi bi-x-circle me-1"></i>Cancelar</button>
                                    <button type="button" id="btn-guardar" class="btn btn-sm btn-success rounded-pill px-3 d-none shadow-sm"><i class="bi bi-check2-all me-1"></i>Guardar Cambios</button>
                                </div>
                            </div>
                            
                            <div class="tab-content">
                                {{-- PESTAÑA PERSONAL --}}
                                <div class="tab-pane fade show active" id="personal" role="tabpanel">
                                    <form id="form-empleado" method="POST" action="{{ route('archivo.empleado.update', $empleadoSeleccionado->id) }}" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="row g-4">
                                            <div class="col-md-12 mb-2">
                                                <div class="vista-campo p-3 bg-light rounded-3 border">
                                                    <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;"><i class="bi bi-camera me-1"></i>Foto de Perfil</small>
                                                    <span class="fw-bold text-dark"><i class="bi {{ $empleadoSeleccionado->ubicacion_foto ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-warning' }} me-2"></i>{{ $empleadoSeleccionado->ubicacion_foto ? 'Foto cargada satisfactoriamente' : 'El empleado no posee foto' }}</span>
                                                </div>
                                                <div class="edicion-campo" style="display:none">
                                                    <label class="form-label small text-muted fw-bold">Actualizar Foto de Perfil</label>
                                                    <input type="file" name="ubicacion_foto" class="form-control" accept="image/*">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="campo-ux">
                                                    <div class="vista-campo">
                                                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;"><i class="bi bi-card-text me-1"></i>Cédula</small>
                                                        <span class="fw-bold fs-6">{{ $empleadoSeleccionado->cedula }}</span>
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
                                                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;"><i class="bi bi-calendar-event me-1"></i>Fecha Expedida</small>
                                                        <span class="fw-bold fs-6">{{ $empleadoSeleccionado->fecha_expedida ?? 'N/A' }}</span>
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
                                                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;"><i class="bi bi-geo-alt me-1"></i>Lugar Expedición</small>
                                                        <span class="fw-bold fs-6">{{ $empleadoSeleccionado->lugar_exp ?? 'N/A' }}</span>
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
                                                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;">Primer Nombre</small>
                                                        <span class="fw-bold fs-6">{{ $empleadoSeleccionado->nombre1 }}</span>
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
                                                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;">Segundo Nombre</small>
                                                        <span class="fw-bold fs-6">{{ $empleadoSeleccionado->nombre2 ?? 'N/A' }}</span>
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
                                                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;">Primer Apellido</small>
                                                        <span class="fw-bold fs-6">{{ $empleadoSeleccionado->apellido1 }}</span>
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
                                                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;">Segundo Apellido</small>
                                                        <span class="fw-bold fs-6">{{ $empleadoSeleccionado->apellido2 ?? 'N/A' }}</span>
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
                                                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;"><i class="bi bi-calendar3 me-1"></i>Fecha Nacimiento</small>
                                                        <span class="fw-bold fs-6">{{ $empleadoSeleccionado->nacimiento ? $empleadoSeleccionado->nacimiento->format('d/m/Y') : 'N/A' }}</span>
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
                                                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;"><i class="bi bi-geo-alt-fill me-1"></i>Lugar Nacimiento</small>
                                                        <span class="fw-bold fs-6">{{ $empleadoSeleccionado->lugar ?? 'N/A' }}</span>
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
                                                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;"><i class="bi bi-gender-ambiguous me-1"></i>Sexo</small>
                                                        <span class="fw-bold fs-6">{{ $empleadoSeleccionado->sexo == 'M' ? 'Masculino' : 'Femenino' }}</span>
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
                                                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;"><i class="bi bi-house me-1"></i>Dirección Residencia</small>
                                                        <span class="fw-bold fs-6">{{ $empleadoSeleccionado->direccion_residencia ?? 'N/A' }}</span>
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
                                                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;"><i class="bi bi-hospital me-1"></i>Entidad EPS</small>
                                                        <span class="fw-bold fs-6">{{ $empleadoSeleccionado->entidad_eps ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="edicion-campo form-floating" style="display:none">
                                                        <input type="text" name="entidad_eps" class="form-control" value="{{ $empleadoSeleccionado->entidad_eps }}">
                                                        <label>Nombre de EPS</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="campo-ux border-top pt-3 mt-2">
                                                    <div class="vista-campo">
                                                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;"><i class="bi bi-envelope me-1"></i>Correo</small>
                                                        <span class="fw-bold fs-6 text-primary">{{ $empleadoSeleccionado->correo_personal ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="edicion-campo form-floating" style="display:none">
                                                        <input type="email" name="correo_personal" class="form-control" value="{{ $empleadoSeleccionado->correo_personal }}">
                                                        <label>Correo Electrónico</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="campo-ux border-top pt-3 mt-2">
                                                    <div class="vista-campo">
                                                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;"><i class="bi bi-phone me-1"></i>Celular</small>
                                                        <span class="fw-bold fs-6">{{ $empleadoSeleccionado->celular_personal ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="edicion-campo form-floating" style="display:none">
                                                        <input type="text" name="celular_personal" class="form-control" value="{{ $empleadoSeleccionado->celular_personal }}">
                                                        <label>Celular</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="campo-ux border-top pt-3 mt-2">
                                                    <div class="vista-campo">
                                                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;"><i class="bi bi-telephone-plus me-1"></i>Acudiente</small>
                                                        <span class="fw-bold fs-6">{{ $empleadoSeleccionado->celular_acudiente ?? 'N/A' }}</span>
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

                                {{-- PESTAÑA: CARGO --}}
                                <div class="tab-pane fade" id="cargo" role="tabpanel">
                                    @if ($empleadoSeleccionado->cargo)
                                        <form id="form-cargo" method="POST" action="{{ route('archivo.cargo.update', $empleadoSeleccionado->cargo->id) }}">
                                            @csrf @method('PUT')
                                            
                                            <div class="row g-4">
                                                <div class="col-12 mt-2">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="badge bg-blue-king text-white me-2 p-2 rounded-circle"><i class="bi bi-diagram-3"></i></span>
                                                        <span class="fw-bold text-blue-king small text-uppercase" style="letter-spacing: 1px;">Estructura y Posición</span>
                                                    </div>
                                                    <hr class="mt-2 mb-3 opacity-10">
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="vista-campo border-start border-blue-king border-4 ps-3 py-2 bg-light rounded-end">
                                                        <small class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Denominación</small>
                                                        <div class="text-dark fw-bold fs-5">{{ $empleadoSeleccionado->cargo->nombre_cargo }}</div>
                                                    </div>
                                                    <div class="edicion-campo" style="display:none">
                                                        <div class="form-floating">
                                                            <input type="text" name="nombre_cargo" class="form-control border-blue-king shadow-none" value="{{ $empleadoSeleccionado->cargo->nombre_cargo }}">
                                                            <label class="text-muted">Nombre del Cargo</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="vista-campo border-start border-blue-king border-4 ps-3 py-2 bg-light rounded-end">
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
                                                        <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 10px;"><i class="bi bi-cash-stack me-1"></i>Remuneración Base</small>
                                                        <span class="text-success fw-bold fs-4">${{ number_format($empleadoSeleccionado->cargo->salario_base, 2) }}</span>
                                                    </div>
                                                    <div class="edicion-campo" style="display:none">
                                                        <div class="form-floating">
                                                            <input type="number" step="0.01" name="salario_base" class="form-control border-success shadow-none fs-5 fw-bold text-success" value="{{ $empleadoSeleccionado->cargo->salario_base }}">
                                                            <label class="text-success">Salario Base</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mt-4">
                                                    <div class="vista-campo">
                                                        <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 10px;"><i class="bi bi-clock-history me-1"></i>Jornada Laboral</small>
                                                        <span class="text-dark fw-bold fs-5">{{ $empleadoSeleccionado->cargo->jornada ?? 'NO DEFINIDA' }}</span>
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
                                                        <small class="text-muted fw-bold text-uppercase d-block mb-2" style="font-size: 10px;">Estatus</small>
                                                        <span class="badge {{ $empleadoSeleccionado->cargo->estado ? 'bg-success' : 'bg-danger' }} px-4 py-2 rounded-pill shadow-sm fs-6">
                                                            <i class="bi {{ $empleadoSeleccionado->cargo->estado ? 'bi-check-circle' : 'bi-x-circle' }} me-1"></i> {{ $empleadoSeleccionado->cargo->estado ? 'ACTIVO' : 'INACTIVO' }}
                                                        </span>
                                                    </div>
                                                    <div class="edicion-campo" style="display:none">
                                                        <div class="form-floating">
                                                            <select name="estado" class="form-select shadow-none">
                                                                <option value="1" {{ $empleadoSeleccionado->cargo->estado ? 'selected' : '' }}>Activo</option>
                                                                <option value="0" {{ !$empleadoSeleccionado->cargo->estado ? 'selected' : '' }}>Inactivo</option>
                                                            </select>
                                                            <label>Estado del Cargo</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 mt-5">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="badge bg-secondary text-white me-2 p-2 rounded-circle"><i class="bi bi-telephone-inbound"></i></span>
                                                        <span class="fw-bold text-secondary small text-uppercase" style="letter-spacing: 1px;">Canales Corporativos</span>
                                                    </div>
                                                    <hr class="mt-2 mb-3 opacity-10">
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="vista-campo bg-light p-4 rounded-4 border shadow-sm h-100 transition-all">
                                                        <div class="mb-3">
                                                            <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 9px;"><i class="bi bi-envelope-at me-1"></i>Email Institucional</small>
                                                            <span class="text-primary fw-bold fs-6">{{ $empleadoSeleccionado->cargo->correo_corporativo ?? 'N/A' }}</span>
                                                        </div>
                                                        <div>
                                                            <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 9px;"><i class="bi bi-google me-1"></i>Gmail Auxiliar</small>
                                                            <span class="text-dark fw-bold fs-6">{{ $empleadoSeleccionado->cargo->gmail_corporativo ?? 'N/A' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="edicion-campo p-3 border rounded-3 bg-light" style="display:none">
                                                        <div class="form-floating mb-3">
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
                                                    <div class="vista-campo bg-light p-4 rounded-4 border shadow-sm h-100 transition-all">
                                                        <div class="row g-3">
                                                            <div class="col-6">
                                                                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 9px;"><i class="bi bi-telephone me-1"></i>Teléfono Fijo</small>
                                                                <span class="text-dark fw-bold fs-6">{{ $empleadoSeleccionado->cargo->telefono_corporativo ?? 'N/A' }}</span>
                                                            </div>
                                                            <div class="col-6 border-start">
                                                                <small class="text-muted fw-bold text-uppercase d-block ps-2" style="font-size: 9px;">Extensión</small>
                                                                <span class="text-blue-king fw-bold fs-6 ps-2">{{ $empleadoSeleccionado->cargo->ext_corporativo ?? '000' }}</span>
                                                            </div>
                                                            <div class="col-12 pt-3 border-top mt-3">
                                                                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 9px;"><i class="bi bi-phone-vibrate me-1"></i>Celular Corporativo</small>
                                                                <span class="text-dark fw-bold fs-6">{{ $empleadoSeleccionado->cargo->celular_corporativo ?? 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="edicion-campo p-3 border rounded-3 bg-light" style="display:none">
                                                        <div class="row g-2">
                                                            <div class="col-8">
                                                                <div class="form-floating mb-2"><input type="text" name="telefono_corporativo" class="form-control shadow-none" value="{{ $empleadoSeleccionado->cargo->telefono_corporativo }}"><label>Teléfono Fijo</label></div>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="form-floating mb-2"><input type="text" name="ext_corporativo" class="form-control shadow-none text-center fw-bold text-blue-king" value="{{ $empleadoSeleccionado->cargo->ext_corporativo }}"><label>Ext.</label></div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="form-floating"><input type="text" name="celular_corporativo" class="form-control shadow-none" value="{{ $empleadoSeleccionado->cargo->celular_corporativo }}"><label>Móvil Corporativo</label></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 mt-5">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="badge bg-dark text-white me-2 p-2 rounded-circle"><i class="bi bi-card-checklist"></i></span>
                                                        <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 1px;">Gestión de Funciones</span>
                                                    </div>
                                                    <hr class="mt-2 mb-3 opacity-10">
                                                </div>

                                                <div class="col-12">
                                                    <div class="vista-campo">
                                                        <div class="p-4 rounded-4 shadow-sm transition-all" style="background-color: #f8f9fa; border: 1px dashed #ced4da;">
                                                            <div class="mb-4 pb-3 border-bottom border-light">
                                                                <small class="text-muted fw-bold text-uppercase d-block mb-2" style="font-size: 9px;"><i class="bi bi-journal-text me-1"></i>Manual de Funciones</small>
                                                                
                                                                @if(isset($empleadoSeleccionado->cargo) && $empleadoSeleccionado->cargo->manual_funciones)
                                                                    <a href="{{ route('archivo.cargo.verManual', $empleadoSeleccionado->cargo->id) }}" 
                                                                       target="_blank" 
                                                                       class="text-decoration-none shadow-sm p-2 px-3 border rounded-pill bg-white d-inline-flex align-items-center transition-all employee-card"
                                                                       style="border-color: #e2e8f0 !important;">
                                                                        <span class="text-dark fw-bold small">
                                                                            <i class="bi bi-file-earmark-pdf-fill fs-5 me-2 text-danger"></i> 
                                                                            Ver Manual Oficial
                                                                        </span>
                                                                        <i class="bi bi-box-arrow-up-right ms-2 text-primary" style="font-size: 11px;"></i>
                                                                    </a>
                                                                @else
                                                                    <span class="badge bg-light text-muted border py-2 px-3 fw-medium">
                                                                        <i class="bi bi-file-earmark-x me-1"></i> Documento de manual no adjunto
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <div>
                                                                <small class="text-muted fw-bold text-uppercase d-block mb-2" style="font-size: 9px;"><i class="bi bi-chat-left-text me-1"></i>Observaciones Adicionales</small>
                                                                <p class="text-dark small mb-0 lh-base p-3 bg-white border rounded-3 shadow-sm">{{ $empleadoSeleccionado->cargo->observacion ?? 'No hay observaciones de gestión registradas en el expediente.' }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="edicion-campo p-4 border rounded-4 bg-light shadow-sm" style="display:none">
                                                        <div class="form-floating mb-3">
                                                            <input type="text" name="manual_funciones" class="form-control shadow-none border-primary" value="{{ $empleadoSeleccionado->cargo->manual_funciones }}">
                                                            <label class="text-primary fw-bold">Nombre o Link del Manual de Funciones</label>
                                                        </div>
                                                        <div class="form-floating">
                                                            <textarea name="observacion" class="form-control shadow-none custom-scroll" style="height: 120px">{{ $empleadoSeleccionado->cargo->observacion }}</textarea>
                                                            <label>Redactar Observaciones de Gestión</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    @else
                                        <div class="text-center py-5 my-5">
                                            <div class="bg-light d-inline-flex p-4 rounded-circle mb-4 shadow-sm border">
                                                <i class="bi bi-person-exclamation text-warning display-4"></i>
                                            </div>
                                            <h4 class="fw-bold text-dark">Cargo no definido</h4>
                                            <p class="text-muted mb-4">Este empleado actualmente no tiene una relación contractual de cargo activa en el sistema.</p>
                                            <button class="btn btn-warning shadow rounded-pill px-5 py-2 fw-bold transition-all hover-lift" data-bs-toggle="modal" data-bs-target="#asignarCargoModal">
                                                <i class="bi bi-briefcase-fill me-2"></i> Asignar Cargo Nuevo
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                {{-- PESTAÑA: DOCUMENTOS --}}
                                <div class="tab-pane fade" id="documentos">
                                    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                                        <h6 class="text-dark fw-bold mb-0"><i class="bi bi-folder2-open me-2 text-warning"></i>Documentos del Expediente</h6>
                                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 shadow text-white fw-bold transition-all" data-bs-toggle="modal" data-bs-target="#subirDocumentoModal">
                                            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Subir Archivo
                                        </button>
                                    </div>
                                    <div class="list-group list-group-flush shadow-sm rounded-3 border overflow-hidden">
                                        @forelse ($documentosDelEmpleado as $doc)
                                            <div class="list-group-item d-flex justify-content-between align-items-center p-3 transition-all employee-card">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-danger bg-opacity-10 text-danger p-2 rounded me-3">
                                                        <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                                                    </div>
                                                    <span class="fw-bold text-dark">{{ $doc->tipoDocumento->nombre }}</span>
                                                </div>
                                                <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                                    <a href="{{ route('archivo.empleado.verDocumento', $doc->id) }}" target="_blank" class="btn btn-sm btn-light border-0 bg-white hover-bg-light text-primary"><i class="bi bi-eye-fill fs-6"></i></a>
                                                    <button onclick="confirmarEliminacion({{ $doc->id }})" class="btn btn-sm btn-light border-0 bg-white hover-bg-light text-danger"><i class="bi bi-trash3-fill fs-6"></i></button>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="p-5 text-center text-muted bg-light">
                                                <i class="bi bi-folder-x display-4 text-secondary opacity-25 mb-3 d-block"></i>
                                                <p class="mb-0 fw-medium">El expediente está vacío. No se han registrado documentos aún.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card h-100 d-flex align-items-center justify-content-center border-0 bg-light rounded-4 border shadow-sm">
                        <div class="text-center p-5 opacity-75">
                            <i class="bi bi-person-bounding-box display-1 text-blue-king opacity-50 mb-4 d-block"></i>
                            <h4 class="fw-bold text-dark">Área de Visualización</h4>
                            <p class="text-muted">Seleccione un empleado del panel izquierdo para ver y gestionar su expediente completo.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="subirDocumentoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="modal-header bg-blue-king text-white border-0 p-4">
                    <h5 class="modal-title fw-bold"><i class="bi bi-cloud-arrow-up-fill me-2"></i>Repositorio de Documentos</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-subir-documento" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4 bg-light">
                        <input type="hidden" name="empleado_id" value="{{ $empleadoSeleccionado->cedula ?? '' }}">
                        
                        <div class="card border-0 shadow-sm rounded-4 p-3 mb-3">
                            <div class="form-floating mb-3">
                                <select name="tipo_documento_id" id="val_tipo_id" class="form-select border-0 bg-light fw-bold text-dark shadow-none" required>
                                    <option value="">Seleccione categoría del documento...</option>
                                    @isset($tiposDocumento)
                                        @foreach($tiposDocumento as $tipo)
                                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                                <label><i class="bi bi-tag-fill me-1 text-primary"></i>Tipo de Documento</label>
                            </div>
                            
                            <div class="form-floating">
                                <input type="date" name="fecha_subida" id="val_fecha" class="form-control border-0 bg-light shadow-none" value="{{ now()->format('Y-m-d') }}" required>
                                <label><i class="bi bi-calendar-check me-1 text-success"></i>Fecha de Registro en Sistema</label>
                            </div>
                        </div>

                        <div class="card border border-primary border-opacity-25 shadow-sm rounded-4 p-4 text-center" style="border-style: dashed !important;">
                            <label class="form-label d-block text-primary fw-bold mb-3"><i class="bi bi-file-earmark-arrow-up display-4 d-block mb-2"></i>Adjuntar Archivo PDF</label>
                            <input type="file" name="archivo" id="archivo_input" class="form-control shadow-sm rounded-pill" required>
                            <small class="text-muted mt-2 d-block">Asegúrese de que el documento esté legible y en formato PDF.</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-top-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary px-5 rounded-pill shadow fw-bold transition-all" id="btn-subir-accion" onclick="procesarSubidaManual()">
                            <i class="bi bi-cloud-upload me-2"></i> Iniciar Subida
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
                Swal.fire({ icon: 'warning', title: 'Información Incompleta', text: 'Por favor seleccione el tipo de documento y adjunte el archivo antes de continuar.', confirmButtonColor: '#0a4275', confirmButtonText: 'Entendido' });
                return;
            }

            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando subida...';

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
                    Swal.fire({ icon: 'success', title: '¡Documento Registrado!', text: data.message, timer: 2000, showConfirmButton: false, backdrop: `rgba(10,66,117,0.4)` })
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
                let msg = err.message || (err.errors ? Object.values(err.errors).flat().join('\n') : 'Error de red o servidor al subir archivo');
                Swal.fire({icon: 'error', title: 'Fallo en la carga', text: msg, confirmButtonColor: '#dc3545'});
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Manejo persistente de tabs por Hash (Mejorado UX)
            const hash = window.location.hash;
            if (hash) {
                const triggerEl = document.querySelector(`.nav-tabs a[href="${hash}"]`);
                if (triggerEl) bootstrap.Tab.getOrCreateInstance(triggerEl).show();
            }

            // Lógica UX Edición Personal: Integración de animaciones CSS Fade-in
            const btnEd = document.getElementById('btn-editar');
            const btnSv = document.getElementById('btn-guardar');
            const btnCn = document.getElementById('btn-cancelar');

            if(btnEd) {
                btnEd.addEventListener('click', function() {
                    document.querySelectorAll('.vista-campo').forEach(el => el.style.display = 'none');
                    document.querySelectorAll('.edicion-campo').forEach(el => {
                        el.style.display = 'block';
                        el.classList.add('fade-in'); 
                    });
                    this.classList.add('d-none');
                    btnSv.classList.remove('d-none');
                    btnCn.classList.remove('d-none');
                    btnSv.classList.add('fade-in');
                    btnCn.classList.add('fade-in');
                });
            }

            if(btnSv) {
                btnSv.addEventListener('click', function() {
                    const form = document.getElementById('form-empleado');
                    const fd = new FormData(form);
                    
                    btnSv.disabled = true;
                    btnSv.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';

                    fetch(form.action, {
                        method: 'POST',
                        body: fd,
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.json())
                    .then(d => {
                        if(d.success) {
                            Swal.fire({ icon: 'success', title: 'Perfil de empleado actualizado con éxito', timer: 1800, showConfirmButton: false })
                            .then(() => window.location.reload());
                        } else {
                            btnSv.disabled = false;
                            btnSv.innerHTML = '<i class="bi bi-check2-all me-1"></i>Guardar Cambios';
                            let errs = d.errors ? Object.values(d.errors).flat().join('\n') : 'La actualización no se pudo concretar';
                            Swal.fire({icon: 'error', title: 'Atención', text: errs, confirmButtonColor: '#dc3545'});
                        }
                    });
                });
            }

            if(btnCn) btnCn.addEventListener('click', () => window.location.reload());
        });

        // Eliminar Documento (S3) UX Mejorada
        function confirmarEliminacion(id) {
            Swal.fire({
                title: '¿Confirmar eliminación?',
                text: "Esta acción borrará permanentemente el documento del expediente digital.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="bi bi-trash-fill me-1"></i> Sí, eliminar',
                confirmButtonColor: '#dc3545',
                cancelButtonText: 'Mantener archivo',
                cancelButtonColor: '#6c757d',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({title: 'Borrando...', allowOutsideClick: false, didOpen: () => {Swal.showLoading()}});
                    fetch(`{{ route('archivo.empleado.destroyDocumento', '') }}/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                             Swal.fire({ icon: 'success', title: 'Eliminado', text: 'Archivo removido correctamente', timer: 1500, showConfirmButton: false})
                             .then(() => {
                                window.location.hash = 'documentos';
                                window.location.reload();
                             });
                        }
                    });
                }
            });
        }
    </script>
@endpush
</x-base-layout>