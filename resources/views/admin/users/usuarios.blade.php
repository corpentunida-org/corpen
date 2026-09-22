<x-base-layout>
    @section('titlepage', 'Usuarios')

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <form action="{{ route('admin.auditoria.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label small text-muted mb-1">Usuario</label>
                        <select name="usuario_id" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->usuario_id }}" @selected(request('usuario_id') == $u->usuario_id)>
                                    {{ $u->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label small text-muted mb-1">Área</label>
                        <select name="area" class="form-select form-select-sm">
                            <option value="">Todas</option>
                            @foreach ($areas as $a)
                                <option value="{{ $a }}" @selected(request('area') === $a)>{{ $a }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label class="form-label small text-muted mb-1">Desde</label>
                        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label class="form-label small text-muted mb-1">Hasta</label>
                        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                        @if (request()->anyFilled(['usuario_id', 'area', 'fecha_desde', 'fecha_hasta']))
                            <a href="{{ route('admin.auditoria.index') }}" class="btn btn-outline-secondary btn-sm">Quitar filtros</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="fw-bold">
                    Actividad
                    <span class="fs-12 fw-normal text-muted">
                        {{ $registros->total() }} registro(s)
                    </span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <div class="col-sm-12">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th>Area</th>
                                    <th>Actividad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($registros as $r)
                                    <tr>
                                        <td>
                                        <span class="wd-10 ht-10 bg-warning me-2 d-inline-block rounded-circle"></span>
                                            {{ $r->fechaRegistro }} {{ $r->horaRegistro }}
                                        </td>
                                        <td>
                                            {{ strtoupper($r->usuario) }}
                                            @if (!empty($cedulas[$r->usuario_id]))
                                                <div class="text-muted fs-12">C.C. {{ $cedulas[$r->usuario_id] }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ( $r->area == 'EXEQUIALES')
                                                <span class="badge bg-soft-primary text-primary">
                                            @elseif ( $r->area == 'SEGUROS')
                                                <span class="badge bg-soft-success text-success">
                                            @elseif ( $r->area == 'CINCO')
                                                <span class="badge bg-soft-info text-info">
                                            @elseif ( $r->area == 'CREDITOS')
                                                <span class="badge bg-soft-danger text-danger">
                                            @else
                                                <span class="badge bg-soft-danger text-warning">
                                            @endif
                                            {{ $r->area }} </span>
                                        </td>
                                        <td>
                                            {{ strtoupper($r->accion) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No hay actividad para los filtros seleccionados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($registros->hasPages())
                    <div class="card-footer bg-white border-top py-3 px-4">
                        {{ $registros->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

</x-base-layout>
