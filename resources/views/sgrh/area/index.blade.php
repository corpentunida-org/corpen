<x-base-layout>

    {{-- ENCABEZADO --}}
    <div class="row mb-4 align-items-end">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Áreas</h2>
            <p class="text-muted mb-0">Estructura organizacional de RR. HH.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('sgrh.area.create') }}" class="btn btn-primary px-4">
                <i class="bi bi-plus-circle"></i> Registrar área
            </a>
        </div>
    </div>

    {{-- BÚSQUEDA Y FILTROS --}}
    <div class="card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('sgrh.area.index') }}" class="row g-2 align-items-center">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="search" name="search" class="form-control border-start-0"
                               placeholder="Buscar por nombre..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                    <span class="text-muted small ms-2">Total: <strong>{{ $areas->total() }}</strong></span>
                </div>
            </form>
        </div>
    </div>

    {{-- LISTADO --}}

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nombre</th>
                        <th>Responsable</th>
                        <th>Descripción</th>
                        <th class="text-center">Cargos</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($areas as $area)
                        <tr>
                            <td class="ps-4 py-3 fw-bold">{{ $area->nombre }}</td>
                            <td class="text-muted small">{{ $area->cargoResponsable->nombre ?? '—' }}</td>
                            <td class="text-muted small">{{ $area->descripcion ?: '—' }}</td>
                            <td class="text-center">{{ $area->cargos_count }}</td>
                            <td class="text-center">
                                @if ($area->activo)
                                    <span class="badge bg-success-subtle text-success">Activa</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Inactiva</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown" data-bs-strategy="fixed">
                                        <i class="feather feather-more-horizontal"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('sgrh.area.edit', $area) }}">
                                                <i class="feather-edit-3 me-2"></i> Editar
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('sgrh.area.destroy', $area) }}" method="POST"
                                                  onsubmit="return confirm('¿Eliminar el área {{ $area->nombre }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="feather-trash-2 me-2"></i> Eliminar
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-5 text-center text-muted">No hay áreas registradas todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($areas->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="small text-muted mb-0">
                        Mostrando registros del {{ $areas->firstItem() }} al {{ $areas->lastItem() }}
                    </p>
                    {{ $areas->links() }}
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif
            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif
            @if ($errors->any())
                toastr.error("{{ $errors->first() }}");
            @endif
        </script>
    @endpush
</x-base-layout>
