<x-base-layout>
    @section('titlepage', 'Calculo de Liquidación')
    <div class="col-12">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="page-header-right ms-auto">
                            <div class="page-header-right-items">
                                <div class="mb-4 mb-lg-0">
                                    <div class="d-flex gap-4 align-items-center">
                                        <div class="avatar-text avatar-lg bg-gray-200">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <div>
                                            <div class="fs-4 fw-bold text-dark"><span
                                                    class="counter">{{ $tercero->nom_ter }}</span></div>
                                            <h3 class="fs-13 fw-semibold text-truncate-1-line">
                                                {{ $tercero->cod_ter }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-md-none d-flex align-items-center">
                                <a href="javascript:void(0)" class="page-header-right-open-toggle">
                                    <i class="feather-align-right fs-20"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 d-flex justify-content-end align-items-center">
                        <form action="{{ route('cinco.retiros.show', ['calculoretiro' => 'ID']) }}" method="GET"
                            class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                            <label for="search-input" class="mb-0 me-2">Buscar:</label>
                            <input type="text" name="id" class="form-control form-control-sm" id="valueCedula"
                                placeholder="cédula" aria-controls="customerList">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <a href="{{ url('terceros/ID') }}?id={{ $tercero->cod_ter }}"
                            class="avatar-text avatar-lg bg-soft-primary text-primary border-soft-primary rounded"
                            data-bs-toggle="tooltip" title="" data-bs-original-title="Actualizar Fecha">
                            <i class="feather-briefcase"></i>
                        </a>
                        <div class="text-end">
                            <p class="fs-11 fw-medium text-uppercase text-muted mb-1">Fecha Ingreso al Ministerio</p>
                            <h5 class="bg-soft-primary text-primary">{{ optional($tercero->fec_minis)->format('Y-m-d')?? 'sin fecha' }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <a href="{{ url('terceros/ID') }}?id={{ $tercero->cod_ter }}"
                            class="avatar-text avatar-lg bg-soft-primary text-primary border-soft-primary rounded"
                            data-bs-toggle="tooltip" title="" data-bs-original-title="Actualizar Fecha">
                            <i class="feather-bar-chart-2"></i>
                        </a>
                        <div class="text-end">
                            <p class="fs-11 fw-medium text-uppercase text-muted mb-1">Fecha Primer Aporte</p>
                            <h5>{{ optional($tercero->fec_aport)->format('Y-m-d') ?? 'sin fecha' }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <a href="{{ url('terceros/ID') }}?id={{ $tercero->cod_ter }}"
                            class="avatar-text avatar-lg bg-soft-primary text-primary border-soft-primary rounded"
                            data-bs-toggle="tooltip" title="" data-bs-original-title="Actualizar Fecha">
                            <i class="feather-user-plus"></i>
                        </a>
                        <div class="text-end">
                            <p class="fs-11 fw-medium text-uppercase text-muted mb-1">Fecha Ingreso a Corpentunida</p>
                            <h5>{{ optional($tercero->fecha_ipuc)->format('Y-m-d') ?? 'sin fecha' }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Cálculo Liquidación</h5>
                <a href="#cardGenerarRetiro" class="btn btn-danger">
                    <i class="bi bi-people me-2"></i>
                    <span>Retirar Pastor</span>
                </a>
            </div>
            <div class="card-body custom-card-action p-3">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr class="border-b">
                                <th scope="row">Fecha</th>
                                <th>Valor</th>
                                <th>Plus</th>
                                <th>Total Plus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalAcomulado = 0;
                            @endphp
                            @foreach ($arrayliquidacion as $a => $valor)
                                <tr>
                                    <td>
                                        <a href="#">Liquidación {{ $a }}</a>
                                    </td>
                                    <td>
                                        @if (is_array($valor))
                                            ${{ number_format($valor[0]) }}
                                        @else
                                            ${{ number_format($valor) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if (is_array($valor))
                                            ${{ number_format($valor[1]) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if (is_array($valor))
                                            <span class="badge bg-gray-200 text-dark">
                                                @php
                                                    $total = $valor[0] + $valor[1];
                                                @endphp
                                                ${{ number_format($total) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @php
                                    if (is_array($valor)) {
                                        $totalAcomulado += $valor[0] + $valor[1];
                                    } else {
                                        $totalAcomulado += $valor;
                                    }
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <p class="mt-3">Suma Total: </p>
                    <h3 class="badge bg-soft-primary text-primary pt-3 px-3 fs-6">
                        ${{ number_format(ceil($totalAcomulado)) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Perfil del Tercero</h5>
                <a href="{{ route('maestras.terceros.edit', $tercero->cod_ter) }}" class="btn btn-warning">
                    <i class="feather-plus me-2"></i>
                    <span>Actualizar Tercero</span>
                </a>
            </div>
            <div class="card-body custom-card-action p-4">
                <div class="profile-details mb-5">
                    <div class="row g-0 mb-4">
                        <div class="col-sm-6 text-muted">Nombre Completo:</div>
                        <div class="col-sm-6 fw-semibold">{{$tercero->nom_ter}}</div>
                    </div>
                    <div class="row g-0 mb-4">
                        <div class="col-sm-6 text-muted">Distrito:</div>
                        <div class="col-sm-6 fw-semibold">{{ $tercero->cod_dist ? substr($tercero->cod_dist, 2) : '' }}</div>
                    </div>
                    <div class="row g-0 mb-4">
                        <div class="col-sm-6 text-muted">Ingreso al Ministerio:</div>
                        <div class="col-sm-6 fw-semibold">{{ optional($tercero->fec_minis)->format('Y-m-d') ?? 'sin fecha'}}</div>
                    </div>
                    <div class="row g-0 mb-4">
                        <div class="col-sm-6 text-muted">Primer Aporte:</div>
                        <div class="col-sm-6 fw-semibold">{{ optional($tercero->fec_aport)->format('Y-m-d') ?? 'sin fecha'}}</div>
                    </div>
                    <div class="row g-0 mb-4">
                        <div class="col-sm-6 text-muted">Ingreso a Corpentunida:</div>
                        <div class="col-sm-6 fw-semibold">{{ optional($tercero->fec_ing)->format('Y-m-d') ?? 'sin fecha'}}</div>
                    </div>
                    <div class="row g-0 mb-4">
                        <div class="col-sm-6 text-muted">Fecha de Nacimiento</div>
                        <div class="col-sm-6 fw-semibold">{{ optional($tercero->fec_nac)->format('Y-m-d') ?? 'sin fecha'}}</div>
                    </div>                    
                    <div class="row g-0 mb-4">
                        <div class="col-sm-6 text-muted">Edad</div>
                        <div class="col-sm-6 fw-semibold">{{ $tercero->edad ?? ' '}}</div>
                    </div>                    
                    <div class="row g-0 mb-4">
                        <div class="col-sm-6 text-muted">Declara renta</div>
                        <div class="col-sm-6 fw-semibold"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12" id="cardGenerarRetiro">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <a href="{{ route('cinco.liquidacionretiro', ['id' => $tercero->cod_ter]) }}"
                    class="btn btn-md bg-soft-teal text-teal border-soft-teal">Generar PDF</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <label class="form-label">Tipo de Retiro<span class="text-danger">*</span></label>
                        <select class="form-select" name="TipoRetiro" required>                            
                            @foreach ($tiposselect as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Observación<span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase" name="observación" required>
                    </div>                    
                </div>
                <div class="row">
                    <div class="col-3 mt-4">
                        <label class="form-label">Fecha ultimo aporte<span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="FechaUltimoAporte" required>
                    </div>
                    <div class="col-3 mt-4">
                        <label class="form-label">Fecha Incial Liquidación<span class="text-danger">*</span></label>
                        <select class="form-select" name="TipoRetiro" required>
                            <option value="">Ingreso al ministerio {{ optional($tercero->fec_minis)->format('Y-m-d') }}</option>
                            <option value="">Primer Aporte {{ optional($tercero->fec_aport)->format('Y-m-d') }}</option>
                        </select>
                    </div>
                    <div class="col-3 mt-4">
                        <label class="form-label">Fecha Retiro<span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="FechaRetiro" required>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
