<x-base-layout>

    {{-- 1. ESTILOS Y LIBRERÍAS REQUERIDAS --}}
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <style>
            /* Paleta de colores y variables para fácil mantenimiento */
            :root {
                --bs-body-bg-rgb: 248, 249, 250;
                --subtle-border-color: #dee2e6;
                --subtle-bg-color: #f1f3f5;
            }

            body { background-color: rgb(var(--bs-body-bg-rgb)); }
            .card {
                border-radius: .75rem;
                border: none;
                box-shadow: 0 4px 12px rgba(0,0,0, .08);
            }
            .nav-tabs .nav-link {
                border-radius: .5rem .5rem 0 0;
                font-weight: 600;
                color: var(--bs-secondary-text-emphasis);
                background-color: transparent;
                border-bottom-width: 2px;
            }
            .nav-tabs .nav-link.active {
                color: var(--bs-primary-text-emphasis);
                border-color: var(--bs-primary) var(--bs-primary) #fff;
                background-color: #fff;
            }
            .nav-tabs .nav-link i {
                margin-right: 0.5rem;
                color: var(--bs-secondary-text-emphasis);
            }
            .nav-tabs .nav-link.active i {
                color: var(--bs-primary);
            }

            /* Estilo de avatar profesional */
            .profile-avatar {
                width: 80px; height: 80px; border-radius: 50%;
                background-color: var(--bs-primary-bg-subtle);
                color: var(--bs-primary-text-emphasis);
                display: flex; align-items: center; justify-content: center;
                font-size: 2.5rem; font-weight: 600;
            }

            /* Estilo consistente para detalles (ícono + texto) */
            .detail-item {
                display: flex; align-items: flex-start; gap: 1rem;
            }
            .detail-item i { font-size: 1.5rem; color: #6c757d; margin-top: .25rem; }

            /* ESTADO VACÍO PROFESIONAL Y FUNCIONAL */
            .professional-empty-state {
                background-color: var(--subtle-bg-color);
                border: 1px dashed var(--subtle-border-color);
                border-radius: .5rem;
                padding: 2.5rem;
                text-align: center;
            }
            .professional-empty-state i {
                font-size: 2rem;
                color: #adb5bd;
            }
        </style>
    @endpush

    {{-- 2. ESTRUCTURA DE DASHBOARD --}}
    <div class="row g-4 g-lg-5 animate-on-load">

        {{-- COLUMNA DERECHA (Sidebar) --}}
        <div class="col-lg-4 order-lg-2">
            <div class="card position-sticky" style="top: 20px;">
                <div class="card-body text-center p-4">
                    <div class="profile-avatar mx-auto mb-3">
                        {{ substr($empleado->nombre1, 0, 1) }}{{ substr($empleado->apellido1, 0, 1) }}
                    </div>
                    <h5 class="fw-bold mb-1">{{ $empleado->nombre_completo }}</h5>
                    <p class="text-muted">C.C. {{ $empleado->cedula }}</p>
                    <hr class="my-4">
                    <div class="d-grid gap-2">
                        <a href="{{ route('archivo.empleado.edit', $empleado->id) }}" class="btn btn-primary"><i class="bi bi-pencil-square me-2"></i> Editar Perfil</a>
                        <a href="{{ route('archivo.empleado.index') }}" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i> Volver al Listado</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLUMNA IZQUIERDA (Main Content) --}}
        <div class="col-lg-8 order-lg-1">
            <div class="card">
                <div class="card-header bg-white border-bottom p-0">
                    <ul class="nav nav-tabs nav-tabs-bordered px-3" role="tablist">
                        <li class="nav-item" role="presentation"><a class="nav-link active" data-bs-toggle="tab" href="#personal" role="tab"><i class="bi bi-person"></i>Personal</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" data-bs-toggle="tab" href="#cargo" role="tab"><i class="bi bi-briefcase"></i>Cargo</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" data-bs-toggle="tab" href="#contratos" role="tab"><i class="bi bi-file-text"></i>Contratos</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" data-bs-toggle="tab" href="#permisos" role="tab"><i class="bi bi-calendar2-check"></i>Permisos</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" data-bs-toggle="tab" href="#observaciones" role="tab"><i class="bi bi-heart-pulse"></i>Obs. Médicas</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" data-bs-toggle="tab" href="#dotaciones" role="tab"><i class="bi bi-person-badge"></i>Dotaciones</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" data-bs-toggle="tab" href="#afiliaciones" role="tab"><i class="bi bi-shield-check"></i>Afiliaciones</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" data-bs-toggle="tab" href="#nomina" role="tab"><i class="bi bi-receipt"></i>Nómina</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" data-bs-toggle="tab" href="#documentos" role="tab"><i class="bi bi-folder2-open"></i>Documentos</a></li>
                    </ul>
                </div>
                
                <div class="card-body p-4">
                    <div class="tab-content pt-3">

                        {{-- Pestaña: Información Personal --}}
                        <div class="tab-pane fade show active" id="personal" role="tabpanel">
                            <h5 class="fw-bold mb-4">Datos Personales y de Contacto</h5>
                            <div class="row g-4">
                                <div class="col-md-6 col-lg-4 detail-item"><i class="bi bi-calendar-event"></i><div><small class="text-muted">F. Nacimiento</small><p class="fw-semibold mb-0">{{ $empleado->nacimiento ? $empleado->nacimiento->format('d/m/Y') : 'N/A' }}</p></div></div>
                                <div class="col-md-6 col-lg-4 detail-item"><i class="bi bi-geo-alt"></i><div><small class="text-muted">Lugar</small><p class="fw-semibold mb-0">{{ $empleado->lugar ?? 'N/A' }}</p></div></div>
                                <div class="col-md-6 col-lg-4 detail-item"><i class="bi bi-gender-ambiguous"></i><div><small class="text-muted">Sexo</small><p class="fw-semibold mb-0">{{ $empleado->sexo == 'M' ? 'Masculino' : ($empleado->sexo == 'F' ? 'Femenino' : 'N/A') }}</p></div></div>
                                <div class="col-md-6 detail-item"><i class="bi bi-envelope"></i><div><small class="text-muted">Correo Personal</small><p class="fw-semibold mb-0">{{ $empleado->correo_personal ?? 'N/A' }}</p></div></div>
                                <div class="col-md-6 detail-item"><i class="bi bi-phone"></i><div><small class="text-muted">Celular</small><p class="fw-semibold mb-0">{{ $empleado->celular_personal ?? 'N/A' }}</p></div></div>
                                <div class="col-md-6 detail-item"><i class="bi bi-shield-lock"></i><div><small class="text-muted">Contacto Acudiente</small><p class="fw-semibold mb-0">{{ $empleado->celular_acudiente ?? 'N/A' }}</p></div></div>
                            </div>
                        </div>

                        {{-- Pestaña: Cargo --}}
                        <div class="tab-pane fade" id="cargo" role="tabpanel">
                             @if ($empleado->cargo)
                                <h5 class="fw-bold mb-4">Puesto Actual</h5>
                                <div class="row g-4">
                                    <div class="col-md-6 detail-item"><i class="bi bi-briefcase-fill text-primary"></i><div><small class="text-muted">Nombre del Cargo</small><p class="fw-semibold mb-0">{{ $empleado->cargo->nombre_cargo }}</p></div></div>
                                    <div class="col-md-6 detail-item"><i class="bi bi-building"></i><div><small class="text-muted">Área Funcional</small><p class="fw-semibold mb-0">{{ $empleado->cargo->gdoArea->nombre ?? 'Sin área' }}</p></div></div>
                                    <div class="col-md-6 detail-item"><i class="bi bi-cash-coin"></i><div><small class="text-muted">Salario Base</small><p class="fw-semibold mb-0">{{ $empleado->cargo->salario_base !== null ? '$'.number_format($empleado->cargo->salario_base, 2, ',', '.') : '—' }}</p></div></div>
                                    <div class="col-md-6 detail-item"><i class="bi bi-toggle-on"></i><div><small class="text-muted">Estado</small><p class="fw-semibold mb-0">@if($empleado->cargo->estado)<span class="badge rounded-pill bg-success-subtle text-success-emphasis">Activo</span>@else<span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis">Inactivo</span>@endif</p></div></div>
                                </div>
                            @else
                                <div class="professional-empty-state">
                                    <i class="bi bi-person-workspace mb-3"></i>
                                    <h6 class="fw-bold">Sin Cargo Asignado</h6>
                                    <p class="text-muted small mb-3">Este empleado no tiene un cargo registrado actualmente.</p>
                                    <a href="#" class="btn btn-sm btn-primary">Asignar Cargo</a>
                                </div>
                            @endif
                        </div>

                        <!-- Pestaña: Contratos -->
                        <div class="tab-pane fade" id="contratos" role="tabpanel">
                            <div class="professional-empty-state">
                                <i class="bi bi-file-earmark-ruled mb-3"></i>
                                <h6 class="fw-bold">Sin Historial de Contratos</h6>
                                <p class="text-muted small mb-3">No se han registrado contratos para este empleado.</p>
                                <a href="#" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i> Añadir Contrato</a>
                            </div>
                        </div>
                        
                        <!-- Pestaña: Permisos -->
                        <div class="tab-pane fade" id="permisos" role="tabpanel">
                            <div class="professional-empty-state">
                                <i class="bi bi-calendar-x mb-3"></i>
                                <h6 class="fw-bold">Sin Registro de Permisos</h6>
                                <p class="text-muted small mb-3">No hay permisos, licencias o ausencias registradas.</p>
                                <a href="#" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i> Registrar Permiso</a>
                            </div>
                        </div>
                        
                        <!-- Pestaña: Observaciones Médicas -->
                        <div class="tab-pane fade" id="observaciones" role="tabpanel">
                             <div class="professional-empty-state">
                                <i class="bi bi-journal-medical mb-3"></i>
                                <h6 class="fw-bold">Sin Observaciones Médicas</h6>
                                <p class="text-muted small mb-3">No se han registrado incapacidades u observaciones médicas.</p>
                                <a href="#" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i> Añadir Observación</a>
                            </div>
                        </div>
                        
                        <!-- Pestaña: Dotaciones -->
                        <div class="tab-pane fade" id="dotaciones" role="tabpanel">
                            <div class="professional-empty-state">
                                <i class="bi bi-box-seam mb-3"></i>
                                <h6 class="fw-bold">Sin Entregas de Dotación</h6>
                                <p class="text-muted small mb-3">No hay registros de entrega de dotación para este empleado.</p>
                                <a href="#" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i> Registrar Entrega</a>
                            </div>
                        </div>
                        
                        <!-- Pestaña: Afiliaciones -->
                        <div class="tab-pane fade" id="afiliaciones" role="tabpanel">
                            <div class="professional-empty-state">
                                <i class="bi bi-shield-slash mb-3"></i>
                                <h6 class="fw-bold">Sin Información de Afiliaciones</h6>
                                <p class="text-muted small mb-3">No se han registrado las afiliaciones a seguridad social (EPS, ARL, etc.).</p>
                                <a href="#" class="btn btn-sm btn-primary"><i class="bi bi-pencil-square me-1"></i> Editar Afiliaciones</a>
                            </div>
                        </div>
                        
                        <!-- Pestaña: Nómina -->
                        <div class="tab-pane fade" id="nomina" role="tabpanel">
                            <div class="professional-empty-state">
                                <i class="bi bi-file-earmark-zip mb-3"></i>
                                <h6 class="fw-bold">Sin Comprobantes de Nómina</h6>
                                <p class="text-muted small mb-3">No se han cargado comprobantes de pago para este empleado.</p>
                                <a href="#" class="btn btn-sm btn-primary"><i class="bi bi-upload me-1"></i> Cargar Comprobante</a>
                            </div>
                        </div>

                        {{-- Pestaña: Documentos --}}
                        <div class="tab-pane fade" id="documentos" role="tabpanel">
                             <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0">Documentos Adjuntos</h5>
                                <a href="{{ route('archivo.gdodocsempleados.create', ['empleado_cedula' => $empleado->cedula]) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-plus-lg me-1"></i> Añadir</a>
                            </div>

                            {{-- ============================================= --}}
                            {{--           AQUÍ ESTÁ LA CORRECCIÓN           --}}
                            {{-- ============================================= --}}

                            @forelse ($documentosDelEmpleado as $doc)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-file-earmark-text fs-4 text-primary me-3"></i>
                                        <div>
                                            <span class="fw-semibold">{{ $doc->tipoDocumento->nombre ?? 'Documento sin tipo' }}</span>
                                            <small class="text-muted d-block">Subido: {{ $doc->fecha_subida ? $doc->fecha_subida->format('d/m/Y') : 'N/A' }}</small>
                                        </div>
                                    </div>
                                    <div class="btn-group">
                                        <a href="{{ route('gdodocsempleados.ver', $doc->id) }}" target="_blank" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="Ver"><i class="bi bi-box-arrow-up-right"></i></a>
                                        <a href="{{ route('gdodocsempleados.download', $doc->id) }}" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="Descargar"><i class="bi bi-download"></i></a>
                                    </div>
                                </div>
                            @empty
                                <div class="professional-empty-state mt-3">
                                    <i class="bi bi-folder-x mb-3"></i>
                                    <h6 class="fw-bold">Sin Documentos Adjuntos</h6>
                                    <p class="text-muted small">Aún no se han cargado documentos para este perfil.</p>
                                </div>
                            @endforelse {{-- ESTA LÍNEA ES LA QUE PROBABLEMENTE FALTABA O ESTABA MAL ESCRITA --}}
                            
                            @if ($documentosDelEmpleado->hasPages())
                                <div class="mt-4">{{ $documentosDelEmpleado->links('pagination::bootstrap-5-sm') }}</div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        {{-- (Coloca aquí el bloque <script> de las respuestas anteriores si lo necesitas) --}}
    @endpush

</x-base-layout>