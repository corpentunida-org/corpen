<x-base-layout>
    <x-warning />
    <x-success />
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body lead-status">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Cargar Archivo</span>
                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">El archivo debe contener la cédula
                            en la columna 'COD_TER' y el valor a pagar en la columna 'DEB_MOV'.</span>
                    </h5>
                </div>
                <form class="row" action="{{ route('seguros.poliza.upload') }}" method="POST"
                    enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="col-10">
                        <input type="file" class="form-control" name="file" required>
                        @if ($errors->has('file'))
                            <div class="invalid-feedback">
                                Solo se permiten archivos de tipo: .xls, .xlsx
                            </div>
                        @endif
                    </div>
                    <div class="col-2 pt-1">
                        <button type="submit" class="btn btn-primary">Agregar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @if (isset($failedRows))
    @if(count($failedRows) > 0)
    <div class="card stratch">
            <div class="card-header">
                <h5 class="card-title">Datos que no se pudieron actualizar</h5>
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
                                <th>Valor a Pagar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($failedRows as $reg)
                                <tr>
                                    <td>{{$reg['cod_ter']}}</td>
                                    <td class="text-primary">$ {{number_format($reg['deb_mov'])}}</td>                                                                      
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
    @endif

</x-base-layout>
