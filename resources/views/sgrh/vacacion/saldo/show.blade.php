<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">{{ $empleado->nombre_completo }}</h2>
            <p class="text-muted mb-0">{{ $empleado->contratoActivo?->cargo->nombre ?? 'Sin cargo activo' }}</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('sgrh.vacacion.saldo.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver al listado
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <p class="text-muted small text-uppercase mb-1">Saldo actual</p>
                    @if ($saldo === null)
                        <h4 class="fw-bold text-secondary mb-0">Sin determinar</h4>
                        <p class="small text-muted mt-1 mb-0">Falta fecha de inicio de contrato.</p>
                    @else
                        <h3 class="fw-bold {{ $saldo < 0 ? 'text-danger' : 'text-success' }} mb-0">{{ $saldo }}</h3>
                        <p class="small text-muted mb-0">días</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <p class="text-muted small text-uppercase mb-1">Días causados</p>
                    <h3 class="fw-bold text-dark mb-0">{{ $diasCausados ?? '—' }}</h3>
                    <p class="small text-muted mb-0">antigüedad: {{ $empleado->antiguedad_en_meses ?? '—' }} meses</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <p class="text-muted small text-uppercase mb-1">Días tomados</p>
                    <h3 class="fw-bold text-dark mb-0">{{ $diasTomados }}</h3>
                    <p class="small text-muted mb-0">solicitudes aprobadas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <p class="text-muted small text-uppercase mb-1">Política vigente</p>
                    <h3 class="fw-bold text-dark mb-0">{{ $politica->dias_por_anio ?? '—' }}</h3>
                    <p class="small text-muted mb-0">días/año</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Historial de solicitudes</h5>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fechas</th>
                            <th>Días</th>
                            <th>Tipo</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($empleado->vacacionSolicitudes as $solicitud)
                            <tr>
                                <td>
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-muted">Sin solicitudes registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Ajustes manuales</h5>
                @can('sgrh.vacacion.ajuste.store')
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAjuste">
                        <i class="bi bi-plus-circle"></i> Nuevo ajuste
                    </button>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Días</th>
                            <th>Motivo</th>
                            <th>Registrado por</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($empleado->vacacionAjustes as $ajuste)
                            <tr>
                                <td>{{ $ajuste->created_at->format('d/m/Y') }}</td>
                                <td class="{{ $ajuste->dias < 0 ? 'text-danger' : 'text-success' }} fw-bold">
                                    {{ $ajuste->dias > 0 ? '+' : '' }}{{ $ajuste->dias }}
                                </td>
                                <td class="text-muted small">{{ $ajuste->motivo }}</td>
                                <td class="text-muted small">{{ $ajuste->usuario->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-muted">Sin ajustes registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @can('sgrh.vacacion.ajuste.store')
        <div class="modal fade" id="modalAjuste" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('sgrh.vacacion.ajuste.store', $empleado) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Nuevo ajuste de saldo</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Días</label>
                                    <input type="number" name="dias" step="0.5" class="form-control" placeholder="Ej. 2 o -1.5" required>
                                    <div class="form-text">Positivo suma al saldo, negativo resta.</div>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Motivo</label>
                                    <input type="text" name="motivo" class="form-control" maxlength="255" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

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
