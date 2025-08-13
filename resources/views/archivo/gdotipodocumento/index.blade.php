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
                <h5 class="fw-bold mb-0">Tipos de Documento Registrados</h5>
                <a href="{{ route('archivo.gdotipodocumento.create') }}" class="btn btn-success btnCrear">
                    <i class="bi bi-plus-lg me-2"></i>
                    <span>Crear Nuevo</span>
                </a>
            </div>

            {{-- Buscador --}}
            <div class="px-4 pb-4">
                <form action="{{ route('archivo.gdotipodocumento.index') }}" method="GET">
                    <div class="input-group shadow-sm rounded">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Buscar por nombre..." value="{{ request('search') }}">
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
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tipos as $tipo)
                            <tr>
                                <td>{{ $tipo->id }}</td>
                                <td>{{ $tipo->nombre }}</td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <a href="#" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item btnVer" href="{{ route('archivo.gdotipodocumento.show', $tipo->id) }}">
                                                    <i class="bi bi-eye me-3"></i> Ver
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item btnEditar" href="{{ route('archivo.gdotipodocumento.edit', $tipo->id) }}">
                                                    <i class="bi bi-pencil-square me-3"></i> Editar
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('archivo.gdotipodocumento.destroy', $tipo->id) }}" method="POST" class="formEliminar">
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
                                <td colspan="3" class="text-center py-4">No se encontraron tipos de documento.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($tipos->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $tipos->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

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
                        confirmButtonText: 'Sí, continuar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) callback();
                    });
                }

                document.querySelectorAll('.btnCrear, .btnVer, .btnEditar').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        confirmarAccion('¿Deseas continuar?', '', 'info', () => {
                            window.location.href = btn.getAttribute('href');
                        });
                    });
                });

                document.querySelectorAll('.formEliminar').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        confirmarAccion('¿Eliminar este tipo de documento?', 'Esta acción no se puede deshacer.', 'warning', () => {
                            form.submit();
                        });
                    });
                });
            });
        </script>
    @endpush
</x-base-layout>
