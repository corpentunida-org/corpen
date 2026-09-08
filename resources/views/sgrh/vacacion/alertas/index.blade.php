<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-12">
            <h2 class="fw-bold text-dark mb-1">Alertas de vacaciones</h2>
            <p class="text-muted mb-0">Calculadas en vivo cada vez que abres esta página.</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-clock-history text-warning"></i> Solicitudes represadas (pendientes hace más de 5 días)
            </h5>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Colaborador</th>
                            <th>Fechas</th>
                            <th>Desde</th>
                            <th class="text-end pe-3">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($represadas as $solicitud)
                            <tr>
                                <td class="fw-bold">{{ $solicitud->empleado->nombre_completo }}</td>
                                <td>{{ $solicitud->fecha_inicio->format('d/m/Y') }} - {{ $solicitud->fecha_fin->format('d/m/Y') }}</td>
                                <td class="text-muted small">{{ $solicitud->created_at->diffForHumans() }}</td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('sgrh.vacacion.solicitud.show', $solicitud) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-3 text-center text-muted">Sin solicitudes represadas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-calendar-event text-primary"></i> Vacaciones colectivas próximas (30 días)
            </h5>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fechas</th>
                            <th>Tipo</th>
                            <th>Alcance</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($colectivasProximas as $colectiva)
                            <tr>
                                <td class="fw-bold">{{ $colectiva->fecha_inicio->format('d/m/Y') }} - {{ $colectiva->fecha_fin->format('d/m/Y') }}</td>
                                <td class="text-capitalize">{{ $colectiva->tipo }}</td>
                                <td class="text-capitalize">{{ $colectiva->alcance }}</td>
                                <td class="text-muted small">{{ $colectiva->descripcion }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-3 text-center text-muted">Sin decretos próximos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-exclamation-triangle text-danger"></i> Saldo negativo por adelanto
            </h5>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Colaborador</th>
                            <th>Saldo</th>
                            <th class="text-end pe-3">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($saldoNegativo as $item)
                            <tr>
                                <td class="fw-bold">{{ $item['empleado']->nombre_completo }}</td>
                                <td class="text-danger fw-bold">{{ $item['saldo'] }}</td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('sgrh.vacacion.saldo.show', $item['empleado']) }}" class="btn btn-sm btn-outline-primary">Ver saldo</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-3 text-center text-muted">Ningún colaborador con saldo negativo.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-base-layout>
