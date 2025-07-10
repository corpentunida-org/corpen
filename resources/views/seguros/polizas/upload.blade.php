<x-base-layout>
    <x-success />
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body lead-status">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Cargar Archivo</span>
                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">El archivo debe tener el siguiente
                            formato:</span>
                    </h5>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('seguros.poliza.formato') }}" class="btn btn-primary p-2 mb-2">
                        <i class="feather-download me-2"></i>
                        <span>Descargar Archivo</span>
                    </a>
                </div>
                <ul class="list-unstyled text-muted mb-0">
                    <li class="d-flex align-items-start mb-1">
                        <span class="text-danger">
                            <i class="feather-check fs-10"></i>
                        </span>
                        <span class="fs-12 fw-normal text-truncate-1-line">"POLIZA": Número de convenio de la
                            póliza</span>
                    </li>
                    <li class="d-flex align-items-start mb-1">
                        <span class="text-danger">
                            <i class="feather-check fs-10"></i>
                        </span>
                        <span class="fs-12 fw-normal text-truncate-1-line">"GENERO": Género del asegurado. "V": Varón.
                            "H": Hembra.</span>
                    </li>
                    <li class="d-flex align-items-start mb-1">
                        <span class="text-danger">
                            <i class="feather-check fs-10"></i>
                        </span>
                        <span class="fs-12 fw-normal text-truncate-1-line">"PARENTESCO": Parentesco al titular principal
                            ('AF', 'CO', 'HE', 'HI')</span>
                    </li>
                    <li class="d-flex align-items-start mb-1">
                        <span class="text-danger">
                            <i class="feather-check fs-10"></i>
                        </span>
                        <span class="fs-12 fw-normal text-truncate-1-line">"TITULAR": Cédula del titular asociado</span>
                    </li>
                    <li class="d-flex align-items-start mb-1">
                        <span class="text-danger">
                            <i class="feather-check fs-10"></i>
                        </span>
                        <span class="fs-12 fw-normal text-truncate-1-line">"EXTRA_PRIM": Numero del porcentaje de
                            extraprima, sin simbolo %</span>
                    </li>
                    <li class="d-flex align-items-start mb-1">
                        <span class="text-danger">
                            <i class="feather-check fs-10"></i>
                        </span>
                        <span class="fs-12 fw-normal text-truncate-1-line">"PRIMA": Valor pago mensual del plan, en
                            valores numericos</span>
                    </li>
                    <li class="d-flex align-items-start mb-1">
                        <span class="text-danger">
                            <i class="feather-check fs-10"></i>
                        </span>
                        <span class="fs-12 fw-normal text-truncate-1-line">"VALOR_TITULAR": Valor a pagar del titular,
                            en valores numericos (solo si aplica)</span>
                    </li>
                </ul>
                <form class="row" action="{{route('seguros.poliza.createupload')}}" method="POST"
                    enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="my-3">
                        <label for="observacion" class="form-label">Importar Excel <span
                                class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="file" required>
                        @if ($errors->has('file'))
                            <div class="invalid-feedback">
                                Solo se permiten archivos de tipo: .xls, .xlsx
                            </div>
                        @endif
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary">Agregar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @php
        $failedRows = session('failedRows');
    @endphp
    @if (isset($failedRows))
        @if (count($failedRows) > 0)
            <div class="col-lg-12">
                <div class="card stratch">
                    <div class="card-header">
                        <h5 class="card-title">Datos que no se pudieron actualizar</h5>
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
                            </div>
                        </div>
                    </div>
                    <div class="card-body custom-card-action p-0">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Titular</th>
                                        <th>Error</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($failedRows as $reg)
                                        <tr>
                                            <td>{{ $reg['cedula'] }}</td>
                                            <td class="text-primary">{{$reg['obser'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</x-base-layout>