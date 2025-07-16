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
                <a href="{{ route('creditos.congregaciones.create') }}" class="btn btn-success">
                    <i class="feather-plus me-2"></i>
                    <span>Crear Nueva</span>
                </a>
            </div>

            {{-- INICIO DEL FORMULARIO DE BÚSQUEDA AÑADIDO --}}
            <div class="px-4 pb-4">
                <form action="{{ route('creditos.congregaciones.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por código, nombre, municipio o pastor..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">Buscar</button>
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
                            <th>Estado congregacion</th>
                            <th>Clase congregacion</th>
                            <th>Municipio</th>
                            <th>Cedula Pastor</th>
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
                                <td>{{ $congregacion->municipio }}</td>
                                <td>{{ $congregacion->pastor }}</td>
                                <td class="hstack justify-content-end gap-4 text-end">
                                    <div class="dropdown open">
                                        <a href="javascript:void(0)" class="avatar-text avatar-md"
                                            data-bs-toggle="dropdown" data-bs-offset="0,21">
                                            <i class="feather feather-more-horizontal"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('creditos.congregaciones.edit', $congregacion->codigo) }}">
                                                    <i class="feather feather-edit-3 me-3"></i>
                                                    <span>Editar</span>
                                                </a>
                                            </li>
                                            <li>
                                                <form
                                                    action="{{ route('creditos.congregaciones.destroy', $congregacion->codigo) }}"
                                                    method="POST">
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
                {{-- Esto ya funciona con la búsqueda gracias al ->withQueryString() del controlador --}}
                {{ $congregaciones->links() }}
            </div>
        </div>
    </div>

    {{-- Script para la confirmación de eliminación (opcional pero recomendado) --}}
    <script>
        // Tu script de eliminación se mantiene igual...
    </script>
</x-base-layout>