<x-base-layout>

    {{-- ENCABEZADO --}}
    <div class="row mb-4 align-items-end">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Cargos</h2>
            <p class="text-muted mb-0">Catálogo de cargos de RR. HH.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('sgrh.cargo.create') }}" class="btn btn-primary px-4">
                <i class="bi bi-plus-circle"></i> Registrar cargo
            </a>
        </div>
    </div>

    {{-- BÚSQUEDA Y FILTROS --}}
    <div class="card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('sgrh.cargo.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="search" name="search" class="form-control border-start-0"
                               placeholder="Buscar por nombre..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="area_id" class="form-select">
                        <option value="">Todas las áreas</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area->id }}" @selected(request('area_id') == $area->id)>{{ $area->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 text-md-end">
                    <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                    <span class="text-muted small ms-2">Total: <strong>{{ $cargos->total() }}</strong></span>
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
                        <th>Área</th>
                        <th>Jornada</th>
                        <th class="text-end">Salario base</th>
                        <th class="text-center">Colaboradores</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cargos as $cargo)
                        <tr>
                            <td class="ps-4 py-3 fw-bold">{{ $cargo->nombre }}</td>
                            <td class="text-muted small">{{ $cargo->area->nombre ?? '—' }}</td>
                            <td class="text-muted small">{{ $cargo->jornada ?: '—' }}</td>
                            <td class="text-end">{{ $cargo->salario_base ? '$' . number_format($cargo->salario_base, 0, ',', '.') : '—' }}</td>
                            <td class="text-center">{{ $cargo->empleados_count }}</td>
                            <td class="text-center">
                                @if ($cargo->activo)
                                    <span class="badge bg-success-subtle text-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                        <i class="feather feather-more-horizontal"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('sgrh.cargo.edit', $cargo) }}">
                                                <i class="feather-edit-3 me-2"></i> Editar
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('sgrh.cargo.destroy', $cargo) }}" method="POST"
                                                  onsubmit="return confirm('¿Eliminar el cargo {{ $cargo->nombre }}?');">
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
                            <td colspan="7" class="py-5 text-center text-muted">No hay cargos registrados todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($cargos->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="small text-muted mb-0">
                        Mostrando registros del {{ $cargos->firstItem() }} al {{ $cargos->lastItem() }}
                    </p>
                    {{ $cargos->links() }}
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
