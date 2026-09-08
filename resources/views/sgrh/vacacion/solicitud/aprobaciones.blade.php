<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">Aprobaciones de vacaciones</h2>
            <p class="text-muted mb-0">Solicitudes de tu equipo bajo tu responsabilidad.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <select class="form-select" onchange="window.location.href = this.value">
                <option value="{{ route('sgrh.vacacion.solicitud.aprobaciones') }}" {{ !request('estado') ? 'selected' : '' }}>Pendientes</option>
                <option value="{{ route('sgrh.vacacion.solicitud.aprobaciones', ['estado' => 'aprobada']) }}" {{ request('estado') === 'aprobada' ? 'selected' : '' }}>Aprobadas</option>
                <option value="{{ route('sgrh.vacacion.solicitud.aprobaciones', ['estado' => 'rechazada']) }}" {{ request('estado') === 'rechazada' ? 'selected' : '' }}>Rechazadas</option>
            </select>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Colaborador</th>
                        <th>Fechas</th>
                        <th>Días</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($solicitudes as $solicitud)
                        <tr>
                            <td class="ps-4 py-3 fw-bold">{{ $solicitud->empleado->nombre_completo }}</td>
                            <td>
                                <a href="{{ route('sgrh.vacacion.solicitud.show', $solicitud) }}">
                                    {{ $solicitud->fecha_inicio->format('d/m/Y') }} - {{ $solicitud->fecha_fin->format('d/m/Y') }}
                                </a>
                                @if ($solicitud->es_adelantada)
                                    <span class="badge bg-info-subtle text-info ms-1">Adelantada</span>
                                @endif
                            </td>
                            <td>{{ $solicitud->dias_habiles }}</td>
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
                                @if ($solicitud->estado === 'pendiente')
                                    <form action="{{ route('sgrh.vacacion.solicitud.aprobar', $solicitud) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Aprobar esta solicitud?');">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-check-circle"></i> Aprobar
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalRechazar{{ $solicitud->id }}">
                                        <i class="bi bi-x-circle"></i> Rechazar
                                    </button>

                                    <div class="modal fade" id="modalRechazar{{ $solicitud->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('sgrh.vacacion.solicitud.rechazar', $solicitud) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Rechazar solicitud</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label class="form-label fw-bold text-dark small text-uppercase">Motivo del rechazo</label>
                                                        <textarea name="motivo_rechazo" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-danger">Rechazar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center text-muted">No hay solicitudes en este estado.</td>
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
