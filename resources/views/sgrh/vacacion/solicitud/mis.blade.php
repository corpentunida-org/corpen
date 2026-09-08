<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">Mis solicitudes de vacaciones</h2>
            <p class="text-muted mb-0">{{ $empleado->nombre_completo }}</p>
        </div>
        <div class="col-md-4 text-md-end">
            @can('sgrh.vacacion.solicitud.store')
                <a href="{{ route('sgrh.vacacion.solicitud.create') }}" class="btn btn-primary px-4">
                    <i class="bi bi-plus-circle"></i> Solicitar vacaciones
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
                        <th>Días</th>
                        <th>Tipo</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($solicitudes as $solicitud)
                        <tr>
                            <td class="ps-4 py-3 fw-bold">
                                <a href="{{ route('sgrh.vacacion.solicitud.show', $solicitud) }}">
                                    {{ $solicitud->fecha_inicio->format('d/m/Y') }} - {{ $solicitud->fecha_fin->format('d/m/Y') }}
                                </a>
                            </td>
                            <td>{{ $solicitud->dias_habiles }}</td>
                            <td class="text-muted small">
                                {{ $solicitud->tipo === 'colectiva_obligatoria' ? 'Colectiva obligatoria' : 'Individual' }}
                                @if ($solicitud->es_adelantada)
                                    <span class="badge bg-info-subtle text-info ms-1">Adelantada</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @switch($solicitud->estado)
                                    @case('aprobada')
                                        <span class="badge bg-success-subtle text-success">Aprobada</span>
                                        @break
                                    @case('pendiente')
                                        <span class="badge bg-warning-subtle text-warning">Pendiente</span>
                                        @break
                                    @case('rechazada')
                                        <span class="badge bg-danger-subtle text-danger">Rechazada</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary-subtle text-secondary">Cancelada</span>
                                @endswitch
                            </td>
                            <td class="text-end pe-4">
                                @if (in_array($solicitud->estado, ['pendiente', 'aprobada']) && $solicitud->fecha_inicio->isFuture() && $solicitud->tipo === 'individual')
                                    <form action="{{ route('sgrh.vacacion.solicitud.cancelar', $solicitud) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Cancelar esta solicitud?');">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-x-circle"></i> Cancelar
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center text-muted">No tienes solicitudes registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($solicitudes->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                {{ $solicitudes->links() }}
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
