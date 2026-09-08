<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">Saldos de vacaciones</h2>
            <p class="text-muted mb-0">Días causados menos tomados, más ajustes manuales — calculado en vivo con la política vigente.</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('sgrh.vacacion.saldo.index') }}" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="search" name="search" class="form-control border-start-0"
                               placeholder="Buscar colaborador..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                    <span class="text-muted small ms-2">Total: <strong>{{ $empleados->total() }}</strong></span>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Colaborador</th>
                        <th>Cargo</th>
                        <th class="text-center">Saldo actual</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($empleados as $empleado)
                        <tr>
                            <td class="ps-4 py-3 fw-bold">{{ $empleado->nombre_completo ?: 'Tercero no encontrado' }}</td>
                            <td class="text-muted small">{{ $empleado->contratoActivo?->cargo->nombre ?? '—' }}</td>
                            <td class="text-center">
                                @php $saldo = $saldos[$empleado->id] ?? null; @endphp
                                @if ($saldo === null)
                                    <span class="badge bg-secondary-subtle text-secondary">Antigüedad no determinada</span>
                                @elseif ($saldo < 0)
                                    <span class="badge bg-danger-subtle text-danger">{{ $saldo }} días</span>
                                @else
                                    <span class="badge bg-success-subtle text-success">{{ $saldo }} días</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('sgrh.vacacion.saldo.show', $empleado) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Ver detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-5 text-center text-muted">No hay colaboradores activos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($empleados->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                {{ $empleados->links() }}
            </div>
        @endif
    </div>
</x-base-layout>
