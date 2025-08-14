<x-base-layout>

    {{-- Contenedor principal --}}
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12">

            {{-- Tarjeta --}}
            <div class="card card-friendly animate-on-load">

                {{-- Encabezado de la Tarjeta --}}
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-list-ul fs-3 text-primary me-3"></i>
                        <div>
                            <h4 class="fw-bold mb-0">Categorías de Documento</h4>
                            <small class="text-muted">Gestiona las categorías existentes.</small>
                        </div>
                    </div>
                    <a href="{{ route('archivo.categorias.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Nueva Categoría
                    </a>
                </div>

                {{-- Cuerpo de la Tarjeta --}}
                <div class="card-body p-3 p-lg-4">

                    <!-- Formulario de Búsqueda -->
                    <form method="GET" action="{{ route('archivo.categorias.index') }}" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Buscar por nombre..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Mensaje de Éxito -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Tabla de Categorías -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Nombre</th>
                                    <th scope="col" class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categorias as $categoria)
                                    <tr>
                                        <td>{{ $categoria->nombre }}</td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="{{ route('archivo.categorias.edit', $categoria) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                                <form action="{{ route('archivo.categorias.destroy', $categoria) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta categoría?');" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">No se encontraron categorías.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center">
                        {{ $categorias->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-base-layout>