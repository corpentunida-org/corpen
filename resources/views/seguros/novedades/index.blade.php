<x-base-layout>
    <x-success />
    <x-error />
    <div class="col-xxl-12">
        <div class="card stratch">
            <div class="card-header">
                <h5 class="card-title">Titulares de Pólizas con Datos Erroneos</h5>
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
                            <a href="" class="avatar-text avatar-xs bg-success" data-bs-toggle="expand"> </a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown"
                            data-bs-offset="25, 25">
                            <div data-bs-toggle="tooltip" title="Options">
                                <i class="feather-more-vertical"></i>
                            </div>
                        </a>

                    </div>
                </div>
            </div>
            <div class="card-body custom-card-action p-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Titular</th>
                                <th>Plan</th>
                                <th>Valor Asegurado</th>
                                <th>Valor a Pagar</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($update as $reg)
                                <tr>
                                    <td><a class="hstack gap-3">
                                            <i class="bi bi-person-circle"></i>
                                            <div>
                                                <span class="text-truncate-1-line">{{ $reg->cedula }}</span>
                                                <small
                                                    class="fs-12 fw-normal text-muted">{{ $reg->tercero->nombre }}</small>
                                            </div>
                                        </a></td>
                                    <td class="text-primary">{{ $reg->polizas->first()->plan->name }}</td>
                                    <td class="fw-bold text-dark">$
                                        {{ number_format($reg->polizas->first()->valor_asegurado) }}</td>
                                    @if ($reg->valorpAseguradora == null)
                                        <td>
                                            <div class="badge bg-soft-warning text-warning">Valor a modificar</div>
                                        </td>
                                    @endif
                                    <td>
                                        <a href="{{ route('seguros.novedades.create', ['a' => $reg->cedula]) }}"
                                            class="avatar-text avatar-md ms-auto"><i class="feather-arrow-right"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-12">
        <div class="card stratch">
            <div class="card-header">
                <h5 class="card-title">Lista de Novedades</h5>
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
                            <a href="" class="avatar-text avatar-xs bg-success" data-bs-toggle="expand"> </a>
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
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Titular</th>
                                <th>Asegurado</th>
                                <th>Plan actual</th>
                                <th>Valor Asegurado</th>
                                <th>Valor a Pagar</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($novedades as $nov)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <a>
                                                <span class="d-block">{{ $nov->asegurado->titular }}</span>
                                                <span
                                                    class="fs-12 d-block fw-normal text-muted">{{ $nov->asegurado->terceroAF->nombre }}</span>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <a>
                                                <span class="d-block">{{ $nov->id_asegurado }}</span>
                                                <span
                                                    class="fs-12 d-block fw-normal text-muted">{{ $nov->tercero->nombre }}</span>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <a>
                                                <span class="badge bg-soft-primary text-primary">{{$nov->plan->name}}</span>
                                                <span
                                                    class="fs-12 d-block fw-normal text-muted">$ {{ number_format($nov->plan->valor) }}</span>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="text-dark fw-bold">$ {{number_format($nov->valorAsegurado)}}</td>
                                    <td class="text-dark fw-bold">$ {{number_format($nov->valorpAseguradora)}}</td>
                                    <td>
                                        <a href="{{ route('seguros.novedades.create', ['a' => $nov->id_asegurado]) }}"
                                            class="avatar-text avatar-md ms-auto"><i
                                                class="feather-arrow-right"></i></a>
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
