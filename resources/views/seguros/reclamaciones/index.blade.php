<x-base-layout>
    <x-success />
    <x-error />
    <div class="col-xxl-12">
        <div class="card stratch">
            <div class="card-header">
                <h5 class="card-title">Lista de reclamación</h5>
                <div class="card-header-action">
                    <div class="card-header-btn">
                        <div data-bs-toggle="tooltip" title="Delete">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger" data-bs-toggle="remove">
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
                            <a href="javascript:void(0);" class="dropdown-item"><i class="feather-bell"></i>Snoozed</a>
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
                                                <span
                                                    class="fs-12 d-block fw-normal text-muted">{{ $r->asegurado->tercero->nombre }}</span>
                                            </a>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-gray-200 text-dark">{{ $r->asegurado->parentesco }}</span>
                                    </td>
                                    <td>{{ $r->cobertura->nombre }}</td>
                                    <td>$ {{ number_format($r->asegurado->valorAsegurado)}} </td>
                                    <td><span class="badge bg-soft-primary text-primary">{{ $r->estadoReclamacion->nombre }}</span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <a href="javascript:void(0)" class="avatar-text avatar-md "
                                                data-bs-toggle="dropdown" data-bs-offset="0,21">
                                                <i class="feather feather-more-vertical"></i>
                                            </a>
                                            <ul class="dropdown-menu" data-popper-placement="bottom-end"
                                                style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate3d(-195px, 51px, 0px);">
                                                <li>                                                    
                                                    <a href="{{ route('seguros.reclamacion.edit', ['reclamacion' => $r->id]) }}" class="dropdown-item">
                                                        <i class="feather feather-edit-3 me-3"></i>
                                                        <span>Editar</span>
                                                    </a>                                                
                                                </li>
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
