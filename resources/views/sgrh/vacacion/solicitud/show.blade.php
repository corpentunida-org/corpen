<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">Solicitud de vacaciones #{{ $solicitud->id }}</h2>
            <p class="text-muted mb-0">{{ $solicitud->empleado->nombre_completo }}</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <p class="text-muted small text-uppercase mb-1">Fechas</p>
                    <p class="fw-bold mb-0">{{ $solicitud->fecha_inicio->format('d/m/Y') }} - {{ $solicitud->fecha_fin->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-2">
                    <p class="text-muted small text-uppercase mb-1">Días hábiles</p>
                    <p class="fw-bold mb-0">{{ $solicitud->dias_habiles }}</p>
                </div>
                <div class="col-md-3">
                    <p class="text-muted small text-uppercase mb-1">Cargo</p>
                    <p class="fw-bold mb-0">{{ $solicitud->empleado->contratoActivo?->cargo->nombre ?? '—' }}</p>
                </div>
                <div class="col-md-2">
                    <p class="text-muted small text-uppercase mb-1">Saldo actual</p>
                    <p class="fw-bold mb-0 {{ ($saldoActual ?? 0) < 0 ? 'text-danger' : '' }}">{{ $saldoActual ?? 'Sin determinar' }}</p>
                </div>
                <div class="col-md-2">
                    <p class="text-muted small text-uppercase mb-1">Estado</p>
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
                </div>

                @if ($solicitud->es_adelantada)
                    <div class="col-12">
                        <span class="badge bg-info-subtle text-info">Vacaciones adelantadas</span>
                        <span class="text-muted small">autorizadas por {{ $solicitud->autorizadaPorRrhh->name ?? '—' }}</span>
                    </div>
                @endif

                @if ($solicitud->vacacionColectiva)
                    <div class="col-12">
                        <span class="badge bg-primary-subtle text-primary">Generada por decreto colectivo #{{ $solicitud->vacacionColectiva->id }}</span>
                    </div>
                @endif

                @if ($solicitud->observaciones)
                    <div class="col-12">
                        <p class="text-muted small text-uppercase mb-1">Observaciones</p>
                        <p class="mb-0">{{ $solicitud->observaciones }}</p>
                    </div>
                @endif

                @if ($solicitud->estado !== 'pendiente')
                    <div class="col-12">
                        <hr class="my-1">
                        <p class="text-muted small mb-0">
                            Resuelta por {{ $solicitud->aprobador->name ?? '—' }} ({{ $solicitud->rol_aprobador }}) el {{ $solicitud->fecha_resolucion?->format('d/m/Y H:i') }}
                        </p>
                        @if ($solicitud->estado === 'rechazada' && $solicitud->motivo_rechazo)
                            <p class="text-danger small mb-0">Motivo: {{ $solicitud->motivo_rechazo }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($puedeResolver)
        <div class="card">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Resolver solicitud</h5>
                <form action="{{ route('sgrh.vacacion.solicitud.aprobar', $solicitud) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('¿Aprobar esta solicitud?');">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Aprobar
                    </button>
                </form>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalRechazar">
                    <i class="bi bi-x-circle"></i> Rechazar
                </button>
            </div>
        </div>

        <div class="modal fade" id="modalRechazar" tabindex="-1" aria-hidden="true">
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
