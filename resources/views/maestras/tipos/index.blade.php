<x-base-layout>
    {{-- Mensaje de éxito --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            {{-- Encabezado: Título y Botón de Crear --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 px-4 pt-3">
                <h5 class="fw-bold mb-0">Tipos de Maestras</h5>
                <a href="{{ route('maestras.tipos.create') }}" class="btn btn-success">
                    <i class="feather-plus me-2"></i>
                    <span>Crear Nuevo</span>
                </a>
            </div>

            {{-- Formulario de Búsqueda y Filtros MEJORADO --}}
            <div class="px-4 pb-4 border-bottom mb-3">
                <form action="{{ route('maestras.tipos.index') }}" method="GET" class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <label for="search" class="visually-hidden">Buscar</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="feather-search"></i></span>
                            <input type="text" name="search" id="search" class="form-control border-start-0" placeholder="Buscar por código, nombre..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="visually-hidden">Estado</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">-- Todos los estados --</option>
                            <option value="1" @selected(request('status') == '1')>Activo</option>
                            <option value="0" @selected(request('status') == '0')>Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary w-100" type="submit">Buscar</button>
                        <a href="{{ route('maestras.tipos.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros">
                            <i class="feather-rotate-ccw"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tabla de Datos MEJORADA --}}
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            @php
                                function sort_link($label, $column) {
                                    $direction = (request('sort') === $column && request('direction') === 'asc') ? 'desc' : 'asc';
                                    $iconClass = '';
                                    if (request('sort') === $column) {
                                        $iconClass = request('direction') === 'asc' ? 'feather-chevron-down' : 'feather-chevron-up';
                                    }
                                    $url = route('maestras.tipos.index', array_merge(request()->query(), ['sort' => $column, 'direction' => $direction]));
                                    return "<a href='{$url}' class='text-decoration-none text-dark fw-bold'>{$label} <i class='feather {$iconClass} ms-1'></i></a>";
                                }
                            @endphp
                            <th scope="col" class="py-2 px-3">{!! sort_link('Código', 'codigo') !!}</th>
                            <th scope="col" class="py-2 px-3">{!! sort_link('Nombre', 'nombre') !!}</th>
                            <th scope="col" class="py-2 px-3">{!! sort_link('Grupo', 'grupo') !!}</th>
                            <th scope="col" class="py-2 px-3">Categoría</th>
                            <th scope="col" class="py-2 px-3">{!! sort_link('Orden', 'orden') !!}</th>
                            <th scope="col" class="py-2 px-3">{!! sort_link('Estado', 'activo') !!}</th>
                            <th scope="col" class="py-2 px-3 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tipos as $tipo)
                            <tr>
                                <td class="py-2 px-3 fw-bold">{{ $tipo->codigo }}</td>
                                <td class="py-2 px-3">{{ $tipo->nombre }}</td>
                                <td class="py-2 px-3">{{ $tipo->grupo ?? '-' }}</td>
                                <td class="py-2 px-3">{{ $tipo->categoria ?? '-' }}</td>
                                <td class="py-2 px-3">{{ $tipo->orden ?? '-' }}</td>
                                <td class="py-2 px-3">
                                    <span class="badge rounded-pill {{ $tipo->activo ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }}">
                                        {{ $tipo->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                {{-- ### INICIO DEL CÓDIGO DE ACCIONES ORIGINAL (EL QUE FUNCIONA) ### --}}
                                <td class="hstack justify-content-end gap-4 text-end py-1 px-2">
                                    <div class="dropdown open">
                                        <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                            <i class="feather feather-more-horizontal"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item btnVer" href="{{ route('maestras.tipos.show', $tipo) }}">
                                                    <i class="feather-eye me-3"></i> Ver
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item btnEditar" href="{{ route('maestras.tipos.edit', $tipo) }}">
                                                    <i class="feather-edit-3 me-3"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('maestras.tipos.destroy', $tipo) }}" method="POST" class="formEliminar">
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
                                {{-- ### FIN DEL CÓDIGO DE ACCIONES ORIGINAL ### --}}
                            </tr>
                        @empty
                            {{-- Mensaje de "No hay resultados" MEJORADO --}}
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    @if(request()->hasAny(['search', 'status']) && request()->input('search') != '' || request()->input('status') != '')
                                        <h5 class="mb-2">No se encontraron resultados</h5>
                                        <p class="text-muted">Prueba a modificar o <a href="{{ route('maestras.tipos.index') }}">limpiar los filtros</a>.</p>
                                    @else
                                        <h5 class="mb-2">Aún no hay tipos de maestras</h5>
                                        <p class="text-muted">¡Crea el primero para empezar a organizar tu información!</p>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if ($tipos->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $tipos->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        {{-- Script de SweetAlert SIMPLIFICADO Y MEJORADO --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Confirmación solo para eliminar
                document.querySelectorAll('.formEliminar').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        Swal.fire({
                            title: '¿Estás seguro?',
                            text: "¡Esta acción no se puede deshacer!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Sí, ¡eliminar!',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
</x-base-layout>