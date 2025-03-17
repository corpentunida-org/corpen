<x-base-layout>
    <x-success />
    <x-error />
    <div class="col-lg-12" data-select2-id="select2-data-39-f2jf">
        <div class="card stretch stretch-full" data-select2-id="select2-data-38-lija">
            <div class="card-body lead-status">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Generar Informe:</span>
                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                            Seleccione solo una de las siguiente opciones</span>
                    </h5>
                </div>
                <form class="row" method="post" action="{{route('seguros.reclamacion.generarpdf')}}">
                    @csrf
                    <div class="col-xxl-3 col-md-6">
                        <div class="form-check">
                            <label class="form-check-label">Todas las Reclamaciones</label>
                            <input class="form-check-input" type="radio" value="todos" name="pdfreclamacion"
                                checked="">
                        </div>
                    </div>
                    <div class="col-xxl-3 col-md-6">
                        <div class="form-check">
                            <label class="form-check-label">Solo Afiliados Titulares</label>
                            <input class="form-check-input" type="radio" value="af" name="pdfreclamacion">
                        </div>
                    </div>
                    <div class="col-xxl-3 col-md-6">
                        <div class="form-check">
                            <label class="form-check-label" for="LRTdirection">Conyugues y Mujeres</label>
                            <input class="form-check-input" type="radio" value="co" name="pdfreclamacion">
                        </div>
                    </div>
                
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-md btn-primary">Descargar PDF</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <h5 class="fs-4">{{ $reclamaciones->count() }}</h5>
                        <span class="text-muted">TOTAL RECLAMACIONES</span>
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
                        <h5 class="fs-4">{{ $coberturas->reclamaciones_count }}</h5>
                        <span class="text-muted">COBERTURA</span>
                        <span class="fs-11 text-dark badge bg-gray-100">{{ $coberturas->nombre }}</span>
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
                        <h5 class="fs-4">*</h5>
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
                        <h5 class="fs-4">*</h5>
                        <span class="text-muted">MUJERES</span>
                    </div>
                    <div class="avatar-text avatar-lg bg-warning text-white rounded">
                        <i class="feather-bi bi-person-standing-dress"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-12">
        <div class="card stratch">
            <div class="card-header">
                <h5 class="card-title">Lista de reclamación</h5>
                <div class="card-header-action">
                    <div class="card-header-btn">
                        <div data-bs-toggle="tooltip" title="Delete">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger"
                                data-bs-toggle="remove">
                            </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="Refresh">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning"
                                data-bs-toggle="refresh"> </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="Maximize/Minimize">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success"
                                data-bs-toggle="expand"> </a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown"
                            data-bs-offset="25, 25">
                            <div data-bs-toggle="tooltip" title="Options">
                                <i class="feather-more-vertical"></i>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item"><i class="feather-at-sign"></i>New</a>
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="feather-calendar"></i>Event</a>
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="feather-bell"></i>Snoozed</a>
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="feather-trash-2"></i>Deleted</a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="feather-settings"></i>Settings</a>
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="feather-life-buoy"></i>Tips&Tricks</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body custom-card-action p-0">
                <div class="table-responsive">
                    <table class="table table-hover" id="customerList">
                        <thead>
                            <tr>
                                <th>Titular</th>
                                <th>Asegurado</th>
                                <th>Parentesco</th>
                                <th>Cobertura</th>
                                <th>Valor Asegurado</th>
                                <th>Estado</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reclamaciones as $r)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <a href="javascript:void(0);">
                                                <span class="d-block">{{ $r->asegurado->terceroAF->cedula }}</span>
                                                <span
                                                    class="fs-12 d-block fw-normal text-muted">{{ $r->asegurado->terceroAF->nombre }}</span>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <a href="javascript:void(0);">
                                                <span class="d-block">{{ $r->cedulaAsegurado }}</span>
                                                <span class="fs-12 d-block fw-normal text-muted">{{ $r->asegurado->tercero->nombre }}</span>
                                            </a>
                                        </div>
                                    </td>
                                    <td><span
                                            class="badge bg-gray-200 text-dark">{{ $r->asegurado->parentesco }}</span>
                                    </td>
                                    <td>{{ $r->cobertura->nombre }}</td>
                                    <td>$ {{ number_format($r->asegurado->valorAsegurado) }} </td>
                                    <td><span
                                            class="badge bg-soft-primary text-primary">{{ $r->estadoReclamacion->nombre }}</span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <a href="javascript:void(0)" class="avatar-text avatar-md "
                                                data-bs-toggle="dropdown" data-bs-offset="0,21">
                                                <i class="feather feather-more-vertical"></i>
                                            </a>
                                            <ul class="dropdown-menu" data-popper-placement="bottom-end"
                                                style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate3d(-195px, 51px, 0px);">
                                                @can('seguros.reclamacion.update')
                                                    <li>
                                                        <a href="{{ route('seguros.reclamacion.edit', ['reclamacion' => $r->id]) }}"
                                                            class="dropdown-item">
                                                            <i class="feather feather-edit-3 me-3"></i>
                                                            <span>Editar</span>
                                                        </a>
                                                    </li>
                                                @endcan
                                                <li>
                                                    <a class="dropdown-item" href="">
                                                        <i class="feather feather-printer me-3"></i>
                                                        <span>Imprimir</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="javascript:void(0)">
                                                        <i class="feather feather-trash-2 me-3"></i>
                                                        <span>Eliminar</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
