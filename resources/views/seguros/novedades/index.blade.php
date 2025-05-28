    <div class="col-xxl-12">
        <div class="card stratch">
            <div class="card-header">
                <h5 class="card-title">Titulares de Pólizas con Datos Erroneos</h5>
                <div class="card-header-action">
                    <div class="card-header-btn">
                        <div data-bs-toggle="tooltip" title="Delete">
                            <a class="avatar-text avatar-xs bg-danger" data-bs-toggle="remove">
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
                                    <td>
                                        @if ($reg->valorpAseguradora == null)
                                            <div class="badge bg-soft-warning text-warning">Valor a modificar</div>
                                        @endif
                                    </td>
                                    <td>
                                        @can('seguros.poliza.update')
                                        <a href="{{ route('seguros.novedades.create', ['a' => $reg->cedula]) }}"
                                            class="avatar-text avatar-md ms-auto"><i
                                                class="feather-arrow-right"></i></a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
