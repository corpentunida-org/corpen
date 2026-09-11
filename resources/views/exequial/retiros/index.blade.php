<x-base-layout>
    @section('titlepage', 'Informe de Retirados')
    <x-success />
    <x-warning />

    <div class="col-12">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <form action="{{ route('exequial.retiros.index') }}" method="GET" class="row g-3 align-items-end" id="formFiltros">
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label small text-muted mb-1">Desde</label>
                        <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label small text-muted mb-1">Hasta</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ request('fecha_hasta') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label class="form-label small text-muted mb-1">Reportado al aliado</label>
                        <select name="reportado" class="form-select form-select-sm">
                            <option value="todos" @selected(request('reportado', 'todos') === 'todos')>Todos</option>
                            <option value="si" @selected(request('reportado') === 'si')>Sí</option>
                            <option value="no" @selected(request('reportado') === 'no')>No</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label class="form-label small text-muted mb-1 d-block">Rangos rápidos</label>
                        <div class="btn-group btn-group-sm w-100">
                            <button type="button" class="btn btn-outline-secondary" onclick="rangoRapido('mes')">Este mes</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="rangoRapido('anio')">Este año</button>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill">Filtrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Retirados</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('exequial.retiros.excel', request()->query()) }}" data-no-loading class="btn btn-sm btn-warning">
                        <i class="bi bi-file-earmark-excel me-1"></i> Excel
                    </a>
                    <a href="{{ route('exequial.retiros.pdf', request()->query()) }}" data-no-loading class="btn btn-sm btn-primary">
                        <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>Fecha afiliación</th>
                                <th>Fecha retiro</th>
                                <th>Observaciones</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Reportado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($retiros as $r)
                                <tr>
                                    <td class="fw-semibold">{{ $r->cod_cli }}</td>
                                    <td>{{ $r->nombre }}</td>
                                    <td class="text-muted small">{{ optional($r->fecha_afiliacion)->format('d/m/Y') ?? '—' }}</td>
                                    <td class="text-muted small">{{ $r->fecha_retiro->format('d/m/Y') }}</td>
                                    <td class="text-muted small">{{ $r->observaciones }}</td>
                                    <td class="text-center">
                                        @if ($r->fecha_reafiliacion)
                                            <span class="badge bg-soft-primary text-primary" data-bs-toggle="tooltip"
                                                title="{{ $r->observacion_reafiliacion }}">Reafiliado {{ $r->fecha_reafiliacion->format('d/m/Y') }}</span>
                                        @else
                                            <span class="badge bg-soft-warning text-warning">Vigente</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($r->reportado_aliado)
                                            <span class="badge bg-soft-success text-success" data-bs-toggle="tooltip"
                                                title="{{ optional($r->reportado_en)->format('d/m/Y H:i') }}">Reportado</span>
                                        @else
                                            <span class="badge bg-soft-warning text-warning">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @candirect('exequial.retiros.reportar')
                                            @unless ($r->reportado_aliado)
                                                <form method="POST" action="{{ route('exequial.retiros.reportar', $r) }}" class="d-inline"
                                                    onsubmit="return confirm('¿Marcar como reportado al aliado comercial?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                                        <i class="bi bi-check2"></i> Marcar reportado
                                                    </button>
                                                </form>
                                            @endunless
                                        @endcandirect
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">No hay retiros para los filtros seleccionados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($retiros->hasPages())
                    <div class="card-footer bg-white border-top py-3 px-4">
                        {{ $retiros->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function rangoRapido(tipo) {
                var hoy = new Date();
                var desde;
                if (tipo === 'mes') {
                    desde = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
                } else {
                    desde = new Date(hoy.getFullYear(), 0, 1);
                }
                document.getElementById('fecha_desde').value = desde.toISOString().slice(0, 10);
                document.getElementById('fecha_hasta').value = hoy.toISOString().slice(0, 10);
                document.getElementById('formFiltros').submit();
            }
        </script>
    @endpush
</x-base-layout>
