<x-base-layout>
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Terceros</h5>
                <a href="{{ route('maestras.terceros.create') }}" class="btn btn-success btnCrear">
                    <i class="feather-plus me-2"></i>
                    <span>Crear Nuevo</span>
                </a>
            </div>

            {{-- Formulario de búsqueda --}}
            <div class="px-4 pb-4">
                <form action="{{ route('maestras.terceros.index') }}" method="GET">
                    <div class="input-group shadow-sm rounded">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="feather-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Buscar por código, nombre o apellidos..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="feather-arrow-right"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tabla --}}
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle small" style="font-size: 0.875rem;">
                    <thead>
                        <tr class="border-top">
                            <th class="py-1 px-2">ID</th>
                            <th class="py-1 px-2">Cédula</th>
                            <th class="py-1 px-2">Nombre Completo</th>
                            <th class="py-1 px-2">Celular</th>
                            <th class="py-1 px-2">Estado</th>
                            <th class="py-1 px-2 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($terceros as $tercero)
                            <tr class="py-1">
                                <td class="py-1 px-2">{{ $tercero->id }}</td>
                                <td class="py-1 px-2">{{ $tercero->cod_ter }}</td>
                                <td class="py-1 px-2">{{ $tercero->nom_ter }}</td>
                                <td class="py-1 px-2">{{ $tercero->cel ?? '-' }}</td>
                                <td class="py-1 px-2">
                                    @if ($tercero->estado === 'ACTIVO')
                                        <span class="badge bg-soft-success text-success">Activo</span>
                                    @elseif ($tercero->estado === 'INACTIVO')
                                        <span class="badge bg-soft-danger text-danger">Inactivo</span>
                                    @else
                                        <span class="badge bg-secondary text-white">{{ $tercero->estado ?? '-' }}</span>
                                    @endif
                                </td>
                                <td class="hstack justify-content-end gap-4 text-end py-1 px-2">
                                    <div class="dropdown open">
                                        <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                            <i class="feather feather-more-horizontal"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item btnVer" href="{{ route('maestras.terceros.show', $tercero->cod_ter) }}">
                                                    <i class="feather-eye me-3"></i> Ver
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item btnEditar" href="{{ route('maestras.terceros.edit', $tercero->cod_ter) }}">
                                                    <i class="feather-edit-3 me-3"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('maestras.terceros.destroy', $tercero->cod_ter) }}" method="POST" class="formEliminar">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item btnEliminar">
                                                        <i class="feather-trash-2 me-3"></i> Eliminar
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $terceros->appends(request()->except('page'))->links('components.maestras.congregaciones.pagination') }}
            </div>
        </div>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                function confirmarAccion(titulo, texto, icono, callback) {
                    Swal.fire({
                        title: titulo,
                        text: texto,
                        icon: icono,
                        showCancelButton: true,
                        confirmButtonColor: '#198754',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí',
                        cancelButtonText: 'Cancelar',
                        showClass: { popup: 'animate__animated animate__fadeIn' },
                        hideClass: { popup: 'animate__animated animate__fadeOut' }
                    }).then((result) => {
                        if (result.isConfirmed) callback();
                    });
                }

                document.querySelectorAll('.btnCrear').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        confirmarAccion('¿Crear un nuevo tercero?', 'Serás redirigido al formulario de creación.', 'info', () => {
                            window.location.href = btn.getAttribute('href');
                        });
                    });
                });

                document.querySelectorAll('.btnVer').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        confirmarAccion('¿Ver detalles del tercero?', 'Serás redirigido a la vista detallada.', 'info', () => {
                            window.location.href = btn.getAttribute('href');
                        });
                    });
                });

                document.querySelectorAll('.btnEditar').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        confirmarAccion('¿Editar este tercero?', 'Serás redirigido al formulario de edición.', 'question', () => {
                            window.location.href = btn.getAttribute('href');
                        });
                    });
                });

                document.querySelectorAll('.formEliminar').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        confirmarAccion('¿Eliminar este tercero?', 'Esta acción no se puede deshacer.', 'warning', () => {
                            form.submit();
                        });
                    });
                });
            });
        </script>
    @endpush
</x-base-layout>
