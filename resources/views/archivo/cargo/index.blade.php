<x-base-layout>
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Cargos</h5>
                <a href="{{ route('archivo.cargo.create') }}" class="btn btn-success btnCrear">
                    <i class="feather-plus me-2"></i>
                    <span>Crear Nuevo</span>
                </a>
            </div>

            {{-- Buscador --}}
            <div class="px-4 pb-4">
                <form action="{{ route('archivo.cargo.index') }}" method="GET">
                    <div class="input-group shadow-sm rounded">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="feather-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Buscar por nombre de cargo..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="feather-arrow-right"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tabla --}}
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle small">
                    <thead>
                        <tr class="border-top">
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Extensión</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cargos as $cargo)
                            <tr>
                                <td>{{ $cargo->id }}</td>
                                <td>{{ $cargo->nombre_cargo }}</td>
                                <td>{{ $cargo->telefono_coporativo ?? '-' }}</td>
                                <td>{{ $cargo->ext_coporativo ?? '-' }}</td>
                                <td>
                                    @if ($cargo->estado === 'ACTIVO')
                                        <span class="badge bg-soft-success text-success">Activo</span>
                                    @elseif ($cargo->estado === 'INACTIVO')
                                        <span class="badge bg-soft-danger text-danger">Inactivo</span>
                                    @else
                                        <span class="badge bg-secondary text-white">{{ $cargo->estado ?? '-' }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <a href="#" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                            <i class="feather-more-horizontal"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item btnVer" href="{{ route('archivo.cargo.show', $cargo->id) }}">
                                                    <i class="feather-eye me-3"></i> Ver
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item btnEditar" href="{{ route('archivo.cargo.edit', $cargo->id) }}">
                                                    <i class="feather-edit-3 me-3"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('archivo.cargo.destroy', $cargo->id) }}" method="POST" class="formEliminar">
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
                {{ $cargos->links('components.maestras.congregaciones.pagination') }}
            </div>
        </div>
    </div>

    @push('scripts')
        @include('components.scripts.confirmaciones', [
            'crear' => '¿Crear un nuevo cargo?',
            'ver' => '¿Ver detalles del cargo?',
            'editar' => '¿Editar este cargo?',
            'eliminar' => '¿Eliminar este cargo?'
        ])
    @endpush
</x-base-layout>
