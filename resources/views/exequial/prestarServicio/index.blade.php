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
                        <h5 class="fs-4">{{ $registros->count() }}</h5>
                        <span class="text-muted">TOTAL SERVICIOS PRESTADOS</span>
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
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover" id="customerList">
                        <thead>
                            <tr>
                                <th>Fallecimiento</th>
                                <th>Fallecido</th>
                                <th>Parentesco</th>
                                <th>Titular</th>
                                <th>Factura</th>
                                <th>Traslado</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($registros->sortByDesc('id') as $r)
                                <tr>
                                    <td>
                                        <div class="fw-semibold mb-1">{{ $r->fechaFallecimiento }}</div>
                                        <div class="d-flex gap-3">
                                            <a href="javascript:void(0);"
                                                class="hstack gap-1 fs-11 fw-normal text-primary">
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
                                                <i class="feather-grid calendar-icon fs-10 text-primary"></i>
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
                                                href="javascript:void(0);"class="hstack gap-1 fs-11 fw-normal text-muted">{{ $r->cedulaTitular }}</a>
                                        </div>
                                    </td>
                                    {{-- <td>
                                        <select class="form-control" data-select2-selector="status">
                                            <option value="success" data-bg="bg-success">{{ $r->factura }}
                                            </option>
                                            <option value="success" data-bg="bg-success">{{ $r->valor }}
                                            </option>
                                        </select>
                                    </td> --}}
                                    <td>
                                        <div>
                                            <a href="javascript:void(0);"
                                                class="fs-12 fw-normal text-muted d-block">{{ $r->factura }}</a>
                                            <a href="javascript:void(0);" class="d-block">$ {{ $r->valor }}</a>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($r->traslado)
                                            <div class="badge bg-soft-success text-success">Si</div>
                                        @else
                                            <div class="badge bg-soft-danger text-danger">No</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="hstack gap-2 justify-content-end">

                                            <a href="{{ route('exequial.prestarServicio.edit', $r->id) }}"
                                                class="avatar-text avatar-md" data-bs-toggle="tooltip" title="EDITAR"
                                                data-bs-original-title="EDITAR">
                                                <i class="feather feather-eye"></i>
                                            </a>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end gap-2 m-3 ">
                        <a href={{ route('prestarServicio.generarpdf') }} class="btn btn-md btn-primary">Descargar
                            PDF</a>
                    </div>

                </div>
            </div>

        </div>
    </div>
    </div>

</x-base-layout>
