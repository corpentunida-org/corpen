<x-base-layout>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
            <i class="feather-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            {{-- 📌 Encabezado --}}
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="fw-bold mb-0 text-primary">
                    <i class="feather-clock me-2"></i> Próximas Acciones
                </h5>
                <a href="{{ route('interactions.next_actions.create') }}" class="btn btn-success d-flex align-items-center gap-2">
                    <i class="feather-plus"></i> <span>Crear Nueva</span>
                </a>
            </div>

            {{-- 📌 Buscador --}}
            <div class="px-4 pb-4">
                <form action="{{ route('interactions.next_actions.index') }}" method="GET">
                    <div class="input-group shadow-sm rounded overflow-hidden">
                        <span class="input-group-text bg-white border-end-0"><i class="feather-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0"
                               placeholder="Buscar próxima acción..."
                               value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="feather-arrow-right d-none d-sm-inline me-1"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>

            {{-- 📊 Tabla --}}
            <div class="table-responsive excel-grid-wrapper">
                <table class="table excel-grid table-hover mb-0 align-middle small">
                    <thead class="table-light sticky-top shadow-sm">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($nextActions as $nextAction)
                            <tr>
                                <td>#{{ $nextAction->id }}</td>
                                <td>{{ $nextAction->name }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        <a href="{{ route('interactions.next_actions.show', $nextAction->id) }}" class="btn btn-sm btn-light border" data-bs-toggle="tooltip" title="Ver">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="{{ route('interactions.next_actions.edit', $nextAction->id) }}" class="btn btn-sm btn-light border" data-bs-toggle="tooltip" title="Editar">
                                            <i class="feather-edit-3"></i>
                                        </a>
                                        <form action="{{ route('interactions.next_actions.destroy', $nextAction->id) }}" method="POST" class="formEliminar d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger" data-bs-toggle="tooltip" title="Eliminar">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    <i class="feather-info me-1"></i> No hay próximas acciones disponibles
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($nextActions->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $nextActions->appends(request()->except('page'))->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tooltipList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipList.forEach(el => new bootstrap.Tooltip(el));

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
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => { if (result.isConfirmed) this.submit(); });
                });
            });
        });
    </script>
    @endpush

    <style>
        .excel-grid-wrapper { max-height: 70vh; overflow: auto; }
        .excel-grid { border-collapse: collapse; white-space: nowrap; width: 100%; table-layout: auto; }
        .excel-grid th, .excel-grid td { border: 1px solid #dee2e6; padding: 6px 10px; }
        .excel-grid thead th { background: #f8f9fa; font-weight: 600; text-align: left; position: sticky; top: 0; z-index: 2; }
    </style>
</x-base-layout>
