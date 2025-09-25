<x-base-layout>
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    
    {{-- ================= ESTADOS ================= --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Estados</h5>
                <a href="{{ route('soportes.estados.create') }}" class="btn btn-success btnCrear">
                    <i class="feather-plus me-2"></i>
                    <span>Crear Nuevo</span>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle small" style="font-size: 0.875rem;">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Color</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($estados as $estado)
                            <tr>
                                <td>{{ $estado->nombre }}</td>
                                <td>{{ $estado->descripcion }}</td>
                                <td><span class="badge" style="background: {{ $estado->color }}">{{ $estado->color }}</span></td>
                                <td class="hstack justify-content-end gap-4 text-end">
                                    <div class="dropdown open">
                                        <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                            <i class="feather feather-more-horizontal"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item btnEditar" href="{{ route('soportes.estados.edit', $estado) }}">
                                                    <i class="feather-edit-3 me-3"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('soportes.estados.destroy', $estado) }}" method="POST" class="formEliminar">
                                                    @csrf @method('DELETE')
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
                <div class="mt-2">
                    {{ $estados->links() }}
                </div>
            </div>
        </div>
    </div>


    {{-- ================= PRIORIDADES ================= --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Prioridades</h5>
                <a href="{{ route('soportes.prioridades.create') }}" class="btn btn-success btnCrear">
                    <i class="feather-plus me-2"></i>
                    <span>Crear Nuevo</span>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle small">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prioridades as $prioridad)
                            <tr>
                                <td>{{ $prioridad->nombre }}</td>
                                <td class="hstack justify-content-end gap-4 text-end">
                                    <div class="dropdown open">
                                        <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                            <i class="feather feather-more-horizontal"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item btnEditar" href="{{ route('soportes.prioridades.edit', $prioridad) }}">
                                                    <i class="feather-edit-3 me-3"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('soportes.prioridades.destroy', $prioridad) }}" method="POST" class="formEliminar">
                                                    @csrf @method('DELETE')
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
                <div class="mt-2">
                    {{ $prioridades->links() }}
                </div>
            </div>
        </div>
    </div>


    {{-- ================= TIPOS OBSERVACIÓN ================= --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Tipos de Observación</h5>
                <a href="{{ route('soportes.tipoObservaciones.create') }}" class="btn btn-success btnCrear">
                    <i class="feather-plus me-2"></i>
                    <span>Crear Nuevo</span>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle small">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tiposObservacion as $tipoObs)
                            <tr>
                                <td>{{ $tipoObs->nombre }}</td>
                                <td class="hstack justify-content-end gap-4 text-end">
                                    <div class="dropdown open">
                                        <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                            <i class="feather feather-more-horizontal"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item btnEditar" href="{{ route('soportes.tipoObservaciones.edit', $tipoObs) }}">
                                                    <i class="feather-edit-3 me-3"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('soportes.tipoObservaciones.destroy', $tipoObs) }}" method="POST" class="formEliminar">
                                                    @csrf @method('DELETE')
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
                <div class="mt-2">
                    {{ $tiposObservacion->links() }}
                </div>
            </div>
        </div>
    </div>


    {{-- ================= TIPOS DE SOPORTE ================= --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Tipos de Soporte</h5>
                <a href="{{ route('soportes.tipos.create') }}" class="btn btn-success btnCrear">
                    <i class="feather-plus me-2"></i>
                    <span>Crear Nuevo</span>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle small">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tipos as $tipo)
                            <tr>
                                <td>{{ $tipo->nombre }}</td>
                                <td>{{ $tipo->descripcion ?? '-' }}</td>
                                <td class="hstack justify-content-end gap-4 text-end">
                                    <div class="dropdown open">
                                        <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                            <i class="feather feather-more-horizontal"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item btnEditar" href="{{ route('soportes.tipos.edit', $tipo) }}">
                                                    <i class="feather-edit-3 me-3"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('soportes.tipos.destroy', $tipo) }}" method="POST" class="formEliminar">
                                                    @csrf @method('DELETE')
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
                <div class="mt-2">
                    {{ $tipos->links() }}
                </div>
            </div>
        </div>
    </div>


    {{-- ================= SUB TIPOS DE SOPORTE ================= --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Sub Tipos de Soporte</h5>
                    <a href="{{ route('soportes.subtipos.create') }}" class="btn btn-success btnCrear">
                    <i class="feather-plus me-2"></i>
                    <span>Crear Nuevo</span>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle small">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Tipo Padre</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subTipos as $sub)
                            <tr>
                                <td>{{ $sub->nombre }}</td>
                                <td>{{ $sub->descripcion ?? '-' }}</td>
                                <td>{{ $sub->tipo->nombre ?? '-' }}</td>
                                <td class="hstack justify-content-end gap-4 text-end">
                                    <div class="dropdown open">
                                        <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                            <i class="feather feather-more-horizontal"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item btnEditar" href="{{ route('soportes.subtipos.edit', $sub) }}">
                                                    <i class="feather-edit-3 me-3"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('soportes.subtipos.destroy', $sub) }}" method="POST" class="formEliminar">
                                                    @csrf @method('DELETE')
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
                <div class="mt-2">
                    {{ $subTipos->links() }}
                </div>
            </div>
        </div>
    </div>



    {{-- Scripts de confirmación con SweetAlert2 --}}
    @push('scripts')
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
                    }).then((result) => {
                        if (result.isConfirmed) callback();
                    });
                }

                document.querySelectorAll('.btnCrear').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        confirmarAccion('¿Crear un nuevo registro?', 'Serás redirigido al formulario de creación.', 'info', () => {
                            window.location.href = btn.getAttribute('href');
                        });
                    });
                });

                document.querySelectorAll('.btnEditar').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        confirmarAccion('¿Editar este registro?', 'Serás redirigido al formulario de edición.', 'question', () => {
                            window.location.href = btn.getAttribute('href');
                        });
                    });
                });

                document.querySelectorAll('.formEliminar').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        confirmarAccion('¿Eliminar este registro?', 'Esta acción no se puede deshacer.', 'warning', () => {
                            form.submit();
                        });
                    });
                });
            });
        </script>
    @endpush
</x-base-layout>
