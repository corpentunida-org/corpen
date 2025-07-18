{{-- resources/views/creditos/congregaciones/index.blade.php --}}

<x-base-layout>
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Congregaciones</h5>
                <a href="{{ route('maestras.congregacion.create') }}" class="btn btn-success btnCrear">
                    <i class="feather-plus me-2"></i>
                    <span>Crear Nueva</span>
                </a>
            </div>

            {{-- INICIO DEL FORMULARIO DE BÚSQUEDA --}}
            <div class="px-4 pb-4">
                <form action="{{ route('maestras.congregacion.index') }}" method="GET">
                    <div class="input-group shadow-sm rounded">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="feather-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Buscar por código, nombre, municipio o pastor..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="feather-arrow-right"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>

            {{-- FIN DEL FORMULARIO DE BÚSQUEDA --}}

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="border-top">
                            <th>Código</th>
                            <th>Nombre del Templo</th>
                            <th>Estado</th>
                            <th>Clase</th>
                            <th>Distrito</th>
                            <th>Municipio</th>
                            <th>CC Pastor</th>
                            <th>Nombre Pastor</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($congregaciones as $congregacion)
                            <tr>
                                <td><a href="javascript:void(0);">{{ $congregacion->codigo }}</a></td>
                                <td>{{ $congregacion->nombre }}</td>
                                <td>
                                    @if ($congregacion->estado)
                                        <span class="badge bg-soft-success text-success">Activo</span>
                                    @else
                                        <span class="badge bg-soft-danger text-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>{{ $congregacion->claseCongregacion->nombre ?? '' }}</td>
                                <td>{{ $congregacion->maeDistritos->NOM_DIST ?? '' }}</td>
                                <td>{{ $congregacion->maeMunicipios->nombre ?? '' }}</td>
                                <td>{{ $congregacion->pastor }}</td>
                                <td>{{ $congregacion->maeTerceros->nom_ter ?? '' }}</td>
                                <td class="hstack justify-content-end gap-4 text-end">
                                    <div class="dropdown open">
                                        <a href="javascript:void(0)" class="avatar-text avatar-md"
                                           data-bs-toggle="dropdown" data-bs-offset="0,21">
                                            <i class="feather feather-more-horizontal"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item btnVer" href="{{ route('maestras.congregacion.show', $congregacion->codigo) }}">
                                                    <i class="feather feather-eye me-3"></i>
                                                    <span>Ver</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item btnEditar" href="{{ route('maestras.congregacion.edit', $congregacion->codigo) }}">
                                                    <i class="feather feather-edit-3 me-3"></i>
                                                    <span>Editar</span>
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('maestras.congregacion.destroy', $congregacion->codigo) }}" method="POST" class="formEliminar">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item btnEliminar">
                                                        <i class="feather feather-trash-2 me-3"></i>
                                                        <span>Eliminar</span>
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
    {{ $congregaciones->appends(request()->except('page'))->links('components.maestras.congregaciones.pagination') }}
</div>



        </div>
    </div>

    @push('scripts')
    {{-- Animaciones y SweetAlert --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Alerta para botón "Crear Nueva"
            document.querySelectorAll('.btnCrear').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const url = btn.getAttribute('href');

                    Swal.fire({
                        title: '¿Crear una nueva congregación?',
                        text: 'Serás redirigido al formulario para registrar una nueva congregación.',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#198754',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, crear',
                        cancelButtonText: 'Cancelar',
                        showClass: {
                            popup: 'animate__animated animate__zoomIn'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__zoomOut'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = url;
                        }
                    });
                });
            });

            // Alerta para botón "Ver"
            document.querySelectorAll('.btnVer').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const url = btn.getAttribute('href');

                    Swal.fire({
                        title: '¿Deseas ver esta congregación?',
                        text: 'Serás dirigido al detalle completo.',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#0d6efd',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, ver',
                        cancelButtonText: 'Cancelar',
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = url;
                        }
                    });
                });
            });

            // Alerta para botón "Editar"
            document.querySelectorAll('.btnEditar').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const url = btn.getAttribute('href');

                    Swal.fire({
                        title: '¿Deseas editar esta congregación?',
                        text: 'Serás dirigido al formulario de edición.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, editar',
                        cancelButtonText: 'Cancelar',
                        showClass: {
                            popup: 'animate__animated animate__fadeInUp'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutDown'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = url;
                        }
                    });
                });
            });

            // Alerta para botón "Eliminar"
            document.querySelectorAll('.formEliminar').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: 'Esta acción no se puede deshacer.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        showClass: {
                            popup: 'animate__animated animate__shakeX'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOut'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
    @endpush
</x-base-layout>
