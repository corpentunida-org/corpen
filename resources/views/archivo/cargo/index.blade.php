<x-base-layout>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Cargos Registrados</h5>
                {{-- 1. CLASE AÑADIDA AQUÍ --}}
                <a href="{{ route('archivo.cargo.create') }}" class="btn btn-success btnCrear">
                    <i class="bi bi-plus-lg me-2"></i>
                    <span>Crear Nuevo</span>
                </a>
            </div>

            {{-- Buscador --}}
            <div class="px-4 pb-4">
                <form action="{{ route('archivo.cargo.index') }}" method="GET">
                    <div class="input-group shadow-sm rounded">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Buscar por nombre de cargo..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">Buscar</button>
                    </div>
                </form>
            </div>

            {{-- Tabla --}}
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle small">
                    <thead class="table-light">
                        <tr class="border-top">
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th class="text-center">Manual</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cargos as $cargo)
                            <tr>
                                <td>{{ $cargo->id }}</td>
                                <td>{{ $cargo->nombre_cargo }}</td>
                                <td>{{ $cargo->telefono_corporativo ?? '-' }}</td>
                                <td class="text-center">
                                    @if ($cargo->manual_funciones)
                                        <span class="text-success" data-bs-toggle="tooltip" title="Manual cargado">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </span>
                                    @else
                                        <span class="text-muted" data-bs-toggle="tooltip" title="Sin manual">
                                            <i class="bi bi-x-circle"></i>
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($cargo->estado)
                                        <span class="badge bg-success-soft text-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger-soft text-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <a href="#" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                {{-- 2. CLASE AÑADIDA AQUÍ --}}
                                                <a class="dropdown-item btnVer" href="{{ route('archivo.cargo.show', $cargo->id) }}">
                                                    <i class="bi bi-eye me-3"></i> Ver
                                                </a>
                                            </li>
                                            <li>
                                                {{-- 3. CLASE AÑADIDA AQUÍ --}}
                                                <a class="dropdown-item btnEditar" href="{{ route('archivo.cargo.edit', $cargo->id) }}">
                                                    <i class="bi bi-pencil-square me-3"></i> Editar
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                {{-- 4. CLASE AÑADIDA AQUÍ --}}
                                                <form action="{{ route('archivo.cargo.destroy', $cargo->id) }}" method="POST" class="formEliminar">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash-fill me-3"></i> Eliminar
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No se encontraron cargos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($cargos->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $cargos->appends(request()->query())->links('components.maestras.congregaciones.pagination') }}
                </div>
            @endif
        </div>
    </div>

    {{-- 5. SECCIÓN DE SCRIPTS REEMPLAZADA POR LA VERSIÓN COMPLETA --}}
    @push('scripts')
        {{-- Dependencias para las alertas --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Función reutilizable para las alertas de confirmación
                function confirmarAccion(titulo, texto, icono, callback) {
                    Swal.fire({
                        title: titulo,
                        text: texto,
                        icon: icono,
                        showCancelButton: true,
                        confirmButtonColor: '#198754',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, continuar',
                        cancelButtonText: 'Cancelar',
                        showClass: { popup: 'animate__animated animate__fadeIn' },
                        hideClass: { popup: 'animate__animated animate__fadeOut' }
                    }).then((result) => {
                        if (result.isConfirmed) callback();
                    });
                }

                // Evento para el botón Crear
                document.querySelectorAll('.btnCrear').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        confirmarAccion('¿Crear un nuevo cargo?', 'Serás redirigido al formulario de creación.', 'info', () => {
                            window.location.href = btn.getAttribute('href');
                        });
                    });
                });

                // Evento para el botón Ver
                document.querySelectorAll('.btnVer').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        confirmarAccion('¿Ver detalles del cargo?', 'Se mostrarán los detalles completos del cargo.', 'info', () => {
                            window.location.href = btn.getAttribute('href');
                        });
                    });
                });

                // Evento para el botón Editar
                document.querySelectorAll('.btnEditar').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        confirmarAccion('¿Editar este cargo?', 'Serás redirigido al formulario de edición.', 'question', () => {
                            window.location.href = btn.getAttribute('href');
                        });
                    });
                });

                // Evento para el formulario de Eliminar
                document.querySelectorAll('.formEliminar').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        confirmarAccion('¿Eliminar este cargo?', 'Esta acción no se puede deshacer.', 'warning', () => {
                            form.submit();
                        });
                    });
                });
                
                // Script para inicializar los tooltips de Bootstrap
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                  return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            });
        </script>
    @endpush
</x-base-layout>