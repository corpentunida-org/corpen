<x-base-layout>
    <x-success />
    <x-error />
    <div class="col-lg-12">
        <div class="card stretch stretch-full" data-select2-id="select2-data-38-lija">
            <div class="card-body">
                <div class="row">
                    <div class="d-flex align-items-center justify-content-start gap-2">
                        <a href="{{route('seguros.novedades.create')}}" class="btn btn-primary mr-3">
                            <i class="feather-plus me-2"></i>
                            <span>Crear Solicitud</span>
                        </a>
                        <a href="" class="d-flex me-1 px-3 bg-soft-indigo text-indigo border border-soft-indigo"
                            style="padding: 12px 0 12px 0;">
                            <i class="feather-activity me-2"></i>
                            <span class="text-uppercase cursor-pointer" style="font-size: 10px;">Informes</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @if (isset($solicitud) && $solicitud->count() > 0)
        <div class="col-xxl-12">
            <div class="card stratch">
                <div class="card-header">
                    <h5 class="card-title">Lista de Novedades Solicitadas</h5>
                    <div class="card-header-action">
                        <div class="card-header-btn">
                            <div data-bs-toggle="tooltip" title="Delete">
                                <a class="avatar-text avatar-xs bg-danger" data-bs-toggle="remove">
                                </a>
                            </div>
                            <div data-bs-toggle="tooltip" title="Refresh">
                                <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning"
                                    data-bs-toggle="refresh">
                                </a>
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
                                    <th>Asegurado</th>
                                    <th>Plan</th>
                                    <th>Valor a Pagar</th>
                                    <th>Observación</th>
                                    <th class="text-end">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($solicitud as $reg)
                                    <tr>
                                        <td><a href="{{ route('seguros.poliza.show', ['poliza' => 'ID']) . '?id=' . $reg->id_asegurado }}"
                                                class="hstack gap-3">
                                                <i class="bi bi-person-circle"></i>
                                                <div>
                                                    <span class="text-truncate-1-line">{{ $reg->id_asegurado }}</span>
                                                    <small
                                                        class="fs-12 fw-normal text-muted">{{ $reg->tercero->nom_ter ?? '' }}</small>
                                                </div>
                                            </a></td>
                                        <td class="d-flex gap-3 align-items-center">                                                         
                                            <div>
                                                <div class="fw-semibold text-dark">$
                                                        {{ number_format($reg->valorAsegurado) }}</div>
                                                <div class="fs-12 text-muted">$ 
                                                        {{ number_format($reg->primaAseguradora) }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            $ {{ number_format($reg->primaCorpen) }}
                                        </td>
                                        <td>
                                            {{ $reg->cambiosEstado->last()->observaciones ?? '' }}
                                        </td>
                                        <td class="text-end">
                                            <div class="hstack gap-2 justify-content-end">
                                                <a href="{{ route('seguros.novedades.edit', $reg->id) }}"
                                                        class="avatar-text avatar-md" data-bs-toggle="tooltip" title=""
                                                        data-bs-original-title="Actualizar Novedad">
                                                        <i class="feather-arrow-right"></i>
                                                </a>
                                            </div>
                                        </td>                                        
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{-- {{ $update->links() }} --}}
                </div>
            </div>
        </div>
    @endif

    @if (isset($radicado) && $radicado->count() > 0)
        <div class="col-xxl-12">
            <div class="card stratch">
                <div class="card-header">
                    <h5 class="card-title">Lista de Novedades Radicadas</h5>
                    <div class="card-header-action">
                        <div class="card-header-btn">
                            <div data-bs-toggle="tooltip" title="Delete">
                                <a class="avatar-text avatar-xs bg-danger" data-bs-toggle="remove">
                                </a>
                            </div>
                            <div data-bs-toggle="tooltip" title="Refresh">
                                <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning"
                                    data-bs-toggle="refresh">
                                </a>
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
                                    <th>Asegurado</th>
                                    <th>Fecha de Solicitud</th>
                                    <th>Valor Asegurado Actual</th>
                                    <th>Valor Asegurado Solicitado</th>
                                    <th>Observaciones</th>
                                    <th class="text-end">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($radicado as $reg)
                                    <tr>
                                        <td><a href="{{ route('seguros.poliza.show', ['poliza' => 'ID']) . '?id=' . $reg->cedula }}"
                                                class="hstack gap-3">
                                                <i class="bi bi-person-circle"></i>
                                                <div>
                                                    <span class="text-truncate-1-line">{{ $reg->cedula }}</span>
                                                    <small
                                                        class="fs-12 fw-normal text-muted">{{ $reg->tercero->nom_ter ?? '' }}</small>
                                                </div>
                                            </a></td>
                                        <td></td>
                                        <td class="fw-bold text-dark">$
                                            {{ number_format($reg->polizas->valor_asegurado) }}
                                        </td>
                                        <td class="fw-bold text-dark">$
                                            {{ number_format($reg->valorAsegurado) }}
                                        </td>
                                        <td>
                                            {{ $reg->observaciones }}
                                        </td>
                                        @can('seguros.poliza.update')
                                            <td class="text-end">
                                                <div class="hstack gap-2 justify-content-end">
                                                    <a href="{{ route('seguros.novedades.create', ['a' => $reg->cedula]) }}"
                                                        class="avatar-text avatar-md" data-bs-toggle="tooltip" title=""
                                                        data-bs-original-title="Crear Novedad">
                                                        <i class="feather-arrow-right"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        @endcan
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $update->links() }}
                </div>
            </div>
        </div>
    @endif
</x-base-layout>