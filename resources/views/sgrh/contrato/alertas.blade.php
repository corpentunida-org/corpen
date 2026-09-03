<x-base-layout>

    {{-- ENCABEZADO --}}
    <div class="row mb-4 align-items-end">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Contratos próximos a vencer</h2>
            <p class="text-muted mb-0">Vencidos y próximos a vencer en los siguientes {{ $dias }} días.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('sgrh.contrato.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver a contratos
            </a>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0" style="background-color: #ffe4e6;">
                <div class="card-body">
                    <p class="text-uppercase small fw-bold mb-1" style="color: #e11d48; letter-spacing: .04em;">Vencidos</p>
                    <h3 class="fw-bold mb-0" style="color: #e11d48;">{{ $totalVencidos }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 bg-warning-subtle">
                <div class="card-body">
                    <p class="text-uppercase small fw-bold text-warning mb-1" style="letter-spacing: .04em;">Próximos a vencer</p>
                    <h3 class="fw-bold text-warning mb-0">{{ $totalPorVencer }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <p class="text-uppercase small fw-bold text-muted mb-1" style="letter-spacing: .04em;">Total en el rango</p>
                    <h3 class="fw-bold mb-0">{{ $totalVencidos + $totalPorVencer }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('sgrh.contrato.alertas') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select name="dias" class="form-select" onchange="this.form.submit()">
                        <option value="30" @selected($dias === 30)>Próximos 30 días</option>
                        <option value="60" @selected($dias === 60)>Próximos 60 días</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="filtro" class="form-select" onchange="this.form.submit()">
                        <option value="" @selected(!$filtro)>Vencidos y próximos a vencer</option>
                        <option value="vencido" @selected($filtro === 'vencido')>Solo vencidos</option>
                        <option value="por_vencer" @selected($filtro === 'por_vencer')>Solo próximos a vencer</option>
                    </select>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="text-muted small">Total: <strong>{{ $contratos->total() }}</strong></span>
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
                        <th class="ps-4">Colaborador</th>
                        <th>Tipo</th>
                        <th>Vencimiento</th>
                        <th class="text-center">Situación</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contratos as $contrato)
                        <tr>
                            <td class="ps-4 py-3 fw-bold">{{ $contrato->empleado->nombre_completo ?: 'Tercero no encontrado' }}</td>
                            <td class="text-muted small">{{ $contrato->tipoContrato->nombre }}</td>
                            <td>{{ $contrato->fecha_vencimiento->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @if ($contrato->estaVencido)
                                    <span class="badge rounded-pill px-2 py-1" style="background-color: #ffe4e6; color: #e11d48; font-weight: 600;">
                                        <i class="bi bi-exclamation-triangle"></i> Vencido
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">
                                        Vence en {{ now()->diffInDays($contrato->fecha_vencimiento, false) }} días
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('sgrh.contrato.edit', $contrato) }}" class="small">
                                    <i class="bi bi-pencil-square"></i> Editar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center text-muted">
                                No hay contratos vencidos ni próximos a vencer en este rango.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($contratos->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="small text-muted mb-0">
                        Mostrando registros del {{ $contratos->firstItem() }} al {{ $contratos->lastItem() }}
                    </p>
                    {{ $contratos->links() }}
                </div>
            </div>
        @endif
    </div>
</x-base-layout>
