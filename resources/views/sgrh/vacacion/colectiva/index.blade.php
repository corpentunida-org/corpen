<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">Vacaciones colectivas</h2>
            <p class="text-muted mb-0">Decretos de fechas obligatorias (generan solicitudes ya aprobadas) o fechas bloqueadas (nadie del alcance puede solicitar ahí).</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            @can('sgrh.vacacion.colectiva.store')
                <a href="{{ route('sgrh.vacacion.colectiva.create') }}" class="btn btn-primary px-4">
                    <i class="bi bi-plus-circle"></i> Nuevo decreto
                </a>
            @endcan
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Fechas</th>
                        <th>Tipo</th>
                        <th>Alcance</th>
                        <th>Descripción</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($colectivas as $colectiva)
                        <tr>
                            <td class="ps-4 py-3 fw-bold">{{ $colectiva->fecha_inicio->format('d/m/Y') }} - {{ $colectiva->fecha_fin->format('d/m/Y') }}</td>
                            <td>
                                @if ($colectiva->tipo === 'obligatoria')
                                    <span class="badge bg-primary-subtle text-primary">Obligatoria</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Bloqueo</span>
                                @endif
                            </td>
                            <td class="text-muted small text-capitalize">{{ $colectiva->alcance }}</td>
                            <td class="text-muted small">{{ $colectiva->descripcion }}</td>
                            <td class="text-center">
                                @if ($colectiva->estado === 'activa')
                                    <span class="badge bg-success-subtle text-success">Activa</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Anulada</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @can('sgrh.vacacion.colectiva.destroy')
                                    @if ($colectiva->estado === 'activa')
                                        <form action="{{ route('sgrh.vacacion.colectiva.destroy', $colectiva) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Anular este decreto? {{ $colectiva->tipo === 'obligatoria' ? 'Las solicitudes ya generadas quedarán canceladas.' : '' }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-x-circle"></i> Anular
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-5 text-center text-muted">No hay decretos de vacaciones colectivas todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($colectivas->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                {{ $colectivas->links() }}
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
        </script>
    @endpush
</x-base-layout>
