<x-base-layout>
    @section('titlepage', 'Distritos')
    <x-success />

    <div class="card">
        <div class="card-body">
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Distritos</h5>
                <a href="{{ route('maestras.distrito.create') }}" class="btn btn-success">
                    <i class="feather-plus me-2"></i>
                    <span>Crear Nuevo</span>
                </a>
            </div>

            <div class="px-4 pb-4">
                <form action="{{ route('maestras.distrito.index') }}" method="GET">
                    <div class="input-group shadow-sm rounded">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="feather-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0"
                            placeholder="Buscar por código o nombre..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="feather-arrow-right"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle small" style="font-size: 0.875rem;">
                    <thead>
                        <tr class="border-top">
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>CC. Supervisor</th>
                            <th class="text-center">Congregaciones</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($distritos as $distrito)
                            <tr>
                                <td class="py-1 px-2">{{ $distrito->COD_DIST }}</td>
                                <td class="py-1 px-2">{{ $distrito->NOM_DIST }}</td>
                                <td class="py-1 px-2" title="{{ $distrito->supervisor->nom_ter ?? '' }}">
                                    {{ $distrito->cc_supervisor ?? '—' }}
                                </td>
                                <td class="py-1 px-2 text-center">{{ $distrito->congregaciones_count }}</td>
                                <td class="hstack justify-content-end gap-4 text-end py-1 px-2">
                                    <div class="dropdown open">
                                        <a href="javascript:void(0)" class="avatar-text avatar-md"
                                            data-bs-toggle="dropdown" data-bs-offset="0,21">
                                            <i class="feather feather-more-horizontal"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('maestras.distrito.edit', $distrito->COD_DIST) }}">
                                                    <i class="feather feather-edit-3 me-3"></i>
                                                    <span>Editar</span>
                                                </a>
                                            </li>
                                            <li>
                                                <form
                                                    action="{{ route('maestras.distrito.destroy', $distrito->COD_DIST) }}"
                                                    method="POST" class="formEliminar">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="feather feather-trash-2 me-3"></i>
                                                        <span>Eliminar</span>
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No hay distritos para la búsqueda actual.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $distritos->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
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
