@php
    use Carbon\Carbon;
    $meses = [
        1 => 'ENERO',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre',
    ];
    $mesActual = $meses[Carbon::now()->month];
@endphp
<x-base-layout>
    @section('titlepage', 'Prestar Servicio')
    <x-success />

    <div class="row">
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <h5 class="fs-4">{{ $totalGeneral }}</h5>
                        <span class="text-muted">TOTAL SERVICIOS</span>
                    </div>
                    <div class="avatar-text avatar-lg bg-success text-white rounded">
                        <i class="feather-pie-chart"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <h5 class="fs-4">{{ $mReg }}</h5>
                        <span class="text-muted">ÚLTIMO MES</span>
                        <span class="fs-11 text-dark badge bg-gray-100">{{ $mesActual }}</span>
                    </div>
                    <div class="avatar-text avatar-lg bg-primary text-white rounded">
                        <i class="feather-activity"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <h5 class="fs-4">{{ $nmen }}</h5>
                        <span class="text-muted">HOMBRES</span>
                    </div>
                    <div class="avatar-text avatar-lg bg-info text-white rounded">
                        <i class="bi bi-person-standing"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <h5 class="fs-4">{{ $nwomen }} </h5>
                        <span class="text-muted">MUJERES</span>
                    </div>
                    <div class="avatar-text avatar-lg bg-warning text-white rounded">
                        <i class="feather-bi bi-person-standing-dress"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <form action="{{ route('exequial.prestarServicio.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label small text-muted mb-1">Desde</label>
                        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label small text-muted mb-1">Hasta</label>
                        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                        @if (request()->filled('fecha_desde') || request()->filled('fecha_hasta'))
                            <a href="{{ route('exequial.prestarServicio.index') }}" class="btn btn-outline-secondary btn-sm">Quitar filtro (último mes)</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">
                    Lista de servicios prestados
                    <span class="fs-12 fw-normal text-muted">
                        {{ request()->filled('fecha_desde') || request()->filled('fecha_hasta') ? 'periodo filtrado' : 'último mes' }}
                    </span>
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('exequial.asociados.index') }}" class="d-flex me-1 btn btn-danger"
                        data-bs-toggle="tooltip" title="Busca al titular y usa el botón 'Prestar Servicio' en su ficha">
                        <i class="fa-regular fa-bell me-2"></i>
                        <span>Registrar nuevo servicio</span>
                    </a>
                    <a href="{{ route('exequial.prestarServicio.dashboard') }}" class="d-flex me-1 btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>Abrir dashboard informe</span>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover" id="customerList">
                        <thead>
                            <tr>
                                <th>Fallecimiento</th>
                                <th>Fallecido</th>
                                <th>Parentesco</th>
                                <th>Titular</th>
                                <th>Estado</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($registros->sortByDesc('id') as $r)
                                <tr>
                                    <td>
                                        <div class="fw-semibold mb-1">{{ $r->fechaFallecimiento }}</div>
                                        <div class="d-flex gap-3">
                                            <a class="hstack gap-1 fs-11 fw-normal text-primary">
                                                <i class="feather-clock fs-10 text-primary"></i>
                                                <span>{{ $r->horaFallecimiento }}</span>
                                            </a>
                                            @php
                                                $fecha = \Carbon\Carbon::parse($r->fechaFallecimiento)->format('d/m/Y');
                                                $diaSemana = \Carbon\Carbon::createFromFormat('d/m/Y', $fecha)
                                                    ->locale('es')
                                                    ->isoFormat('dddd');
                                            @endphp
                                            <a href="javascript:void(0);"
                                                class="hstack gap-1 fs-11 fw-normal text-primary">
                                                <span>{{ ucfirst($diaSemana) }}</span>
                                            </a>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="fw-semibold mb-1">{{ $r->nombreFallecido }}</div>
                                        <div class="d-flex gap-3">
                                            <a
                                                href="javascript:void(0);"class="hstack gap-1 fs-11 fw-normal text-muted">{{ $r->cedulaFallecido }}</a>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($r->parentesco == 'TITULAR')
                                            <span class="badge bg-soft-primary text-primary">
                                            @else
                                                <span class="badge bg-gray-200 text-dark">
                                        @endif
                                        {{ $r->parentesco }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold mb-1">{{ $r->nombreTitular }}</div>
                                        <div class="d-flex gap-3">
                                            <a
                                                class="hstack gap-1 fs-11 fw-normal text-muted">{{ $r->cedulaTitular }}</a>
                                        </div>
                                    </td>
                                    <td class="fw-semibold text-primary">
                                        {{ $r->estado == 1 ? 'PENDIENTE' : ($r->estado == 2 ? 'EN PROCESO' : 'CERRADO') }}
                                    </td>
                                    <td>
                                        <div class="hstack gap-2 justify-content-end">
                                            <a href="{{ route('exequial.prestarServicio.edit', $r->id) }}"
                                                class="avatar-text avatar-md" data-bs-toggle="tooltip" title="EDITAR"
                                                data-bs-original-title="VER/EDITAR">
                                                <i class="feather feather-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end gap-2 m-3 ">
                        <a href={{ route('prestarServicio.generarpdf', request()->query()) }} data-no-loading class="btn btn-md btn-primary">Descargar
                            PDF</a>
                        <a href={{ route('prestarServicio.generate.excel', request()->query()) }} data-no-loading class="btn btn-md btn-warning">Descargar
                            Excel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
