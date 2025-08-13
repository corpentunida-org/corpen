<x-base-layout>
    {{-- Alerta de éxito (si existe un mensaje en la sesión) --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- TARJETA DE PERFIL UNIFICADA --}}
    <div class="card border-light-subtle shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-4">
                {{-- Avatar con iniciales --}}
                <div class="avatar me-3">
                    <span class="avatar-text">
                        {{ substr($empleado->nombre1, 0, 1) }}{{ substr($empleado->apellido1, 0, 1) }}
                    </span>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold">{{ $empleado->nombre1 }} {{ $empleado->nombre2 }} {{ $empleado->apellido1 }} {{ $empleado->apellido2 }}</h4>
                    <span class="text-muted">Cédula: {{ $empleado->cedula }}</span>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('archivo.empleado.edit', $empleado->id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil-square me-2"></i> Editar Perfil
                    </a>
                </div>
            </div>

            <hr>

            {{-- Sección de Detalles Personales --}}
            <h5 class="mb-3 fw-semibold">Detalles Personales</h5>
            <div class="row g-4">
                <div class="col-md-4">
                    <p class="small text-muted mb-0">Fecha de Nacimiento</p>
                    <p class="fw-semibold">{{ $empleado->nacimiento ? $empleado->nacimiento->format('d-m-Y') : 'No especificada' }}</p>
                </div>
                <div class="col-md-4">
                    <p class="small text-muted mb-0">Lugar</p>
                    <p class="fw-semibold">{{ $empleado->lugar ?? 'No especificado' }}</p>
                </div>
                <div class="col-md-4">
                    <p class="small text-muted mb-0">Sexo</p>
                    <p class="fw-semibold">{{ $empleado->sexo == 'M' ? 'Masculino' : ($empleado->sexo == 'F' ? 'Femenino' : 'No especificado') }}</p>
                </div>
            </div>

            <hr>
            
            {{-- Sección de Información de Contacto --}}
            <h5 class="mb-3 fw-semibold">Información de Contacto</h5>
            <div class="row g-4">
                <div class="col-md-4">
                    <p class="small text-muted mb-0">Correo Personal</p>
                    <p class="fw-semibold">{{ $empleado->correo_personal ?? 'No especificado' }}</p>
                </div>
                <div class="col-md-4">
                    <p class="small text-muted mb-0">Celular Personal</p>
                    <p class="fw-semibold">{{ $empleado->celular_personal ?? 'No especificado' }}</p>
                </div>
                <div class="col-md-4">
                    <p class="small text-muted mb-0">Celular Acudiente</p>
                    <p class="fw-semibold">{{ $empleado->celular_acudiente ?? 'No especificado' }}</p>
                </div>
            </div>

            @if($empleado->cargo)
            <hr>
            {{-- Sección de Información del Cargo --}}
            <h5 class="mb-3 fw-semibold">Información del Cargo</h5>
            <div class="row g-4">
                <div class="col-md-4">
                    <p class="small text-muted mb-0">Nombre del Cargo</p>
                    <p class="fw-semibold">{{ $empleado->cargo->nombre_cargo }}</p>
                </div>
                <div class="col-md-4">
                    <p class="small text-muted mb-0">Salario Base</p>
                    <p class="fw-semibold">{{ $empleado->cargo->salario_base !== null ? '$'.number_format($empleado->cargo->salario_base, 2, ',', '.') : '—' }}</p>
                </div>
                <div class="col-md-4">
                    <p class="small text-muted mb-0">Jornada</p>
                    <p class="fw-semibold">{{ $empleado->cargo->jornada ?? '—' }}</p>
                </div>
                <div class="col-md-4">
                    <p class="small text-muted mb-0">Estado del Cargo</p>
                    @if($empleado->cargo->estado)
                        <span class="badge fs-6 rounded-pill bg-success-subtle text-success-emphasis">Activo</span>
                    @else
                        <span class="badge fs-6 rounded-pill bg-danger-subtle text-danger-emphasis">Inactivo</span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- TARJETA DE DOCUMENTOS --}}
    <div class="card border-light-subtle shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-folder-fill me-2 text-primary"></i> Documentos Adjuntos</h5>
            <a href="{{ route('archivo.gdodocsempleados.create', ['empleado_cedula' => $empleado->cedula]) }}" class="btn btn-outline-primary btn-sm btnCrear">
                <i class="bi bi-plus-lg me-1"></i>
                <span>Añadir Documento</span>
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    {{-- El thead se omite para un look más limpio, las columnas son auto-explicativas --}}
                    <tbody>
                        @forelse ($documentosDelEmpleado as $doc)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="fw-semibold mb-0">{{ $doc->tipoDocumento->nombre ?? 'Documento sin tipo' }}</p>
                                    <small class="text-muted">Subido: {{ $doc->fecha_subida ? $doc->fecha_subida->format('d/m/Y') : 'Fecha no disponible' }}</small>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('gdodocsempleados.ver', $doc->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Ver</a>
                                    <a href="{{ route('gdodocsempleados.download', $doc->id) }}" class="btn btn-sm btn-outline-secondary">Descargar</a>
                                    <div class="dropdown d-inline">
                                        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item btnEditar" href="{{ route('archivo.gdodocsempleados.edit', $doc->id) }}"><i class="bi bi-pencil-square me-2"></i> Editar</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('archivo.gdodocsempleados.destroy', $doc->id) }}" method="POST" class="formEliminar d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash-fill me-2"></i> Eliminar</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center py-5">Este empleado no tiene documentos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($documentosDelEmpleado->hasPages())
            <div class="card-footer bg-white py-2">
                {{ $documentosDelEmpleado->links('pagination::bootstrap-5-sm') }}
            </div>
        @endif
    </div>
    
    {{-- Botón para volver al listado --}}
    <div class="text-center mt-4">
        <a href="{{ route('archivo.empleado.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Volver al Listado de Empleados
        </a>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body { background-color: #f4f7f9; }
        .card { border-radius: 0.75rem; border: none; }
        .avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: rgba(var(--bs-primary-rgb), 0.1);
            color: var(--bs-primary);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .avatar-text { font-size: 1.5rem; font-weight: 600; }
        .badge.rounded-pill { padding: 0.5em 1em; font-weight: 600; }
        .btn { border-radius: 0.5rem; }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Tu script de SweetAlert2 sigue siendo perfectamente válido aquí.
        // No es necesario modificarlo.
        document.addEventListener('DOMContentLoaded', function () {
            // ...
        });
    </script>
    @endpush
</x-base-layout>