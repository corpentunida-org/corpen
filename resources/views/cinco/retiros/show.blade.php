<x-base-layout>
    @section('titlepage', 'Calculo de Liquidación')
    <x-warning />
    <x-success />
    <x-error />
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
                            <h5 class="bg-soft-primary text-primary">
                                {{ optional($tercero->fec_minis)->format('Y-m-d') ?? 'sin fecha' }}</h5>
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
                @if ($retiro->isNotEmpty())
                    <a href="{{route('cinco.liquidacionretiro', $tercero->cod_ter)}}" class="btn btn-md bg-soft-teal text-teal border-soft-teal">Descargar PDF Liquidación</a>
                @elseif($tercero->estado)
                    <a href="#cardGenerarRetiro" class="btn btn-danger" id="btnCardRetiros">
                        <i class="bi bi-people me-2"></i>
                        <span>Generar Retiro</span>
                    </a>
                @endif
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
                        <div class="col-sm-6 fw-semibold">{{ $tercero->nom_ter }}</div>
                    </div>
                    <div class="row g-0 mb-4">
                        <div class="col-sm-6 text-muted">Distrito:</div>
                        <div class="col-sm-6 fw-semibold">
                            {{ $tercero->cod_dist ? substr($tercero->cod_dist, 2) : '' }}</div>
                    </div>
                    <div class="row g-0 mb-4">
                        <div class="col-sm-6 text-muted">Ingreso al Ministerio:</div>
                        <div class="col-sm-6 fw-semibold">
                            {{ optional($tercero->fec_minis)->format('Y-m-d') ?? 'sin fecha' }}</div>
                    </div>
                    <div class="row g-0 mb-4">
                        <div class="col-sm-6 text-muted">Primer Aporte:</div>
                        <div class="col-sm-6 fw-semibold">
                            {{ optional($tercero->fec_aport)->format('Y-m-d') ?? 'sin fecha' }}</div>
                    </div>
                    <div class="row g-0 mb-4">
                        <div class="col-sm-6 text-muted">Ingreso a Corpentunida:</div>
                        <div class="col-sm-6 fw-semibold">
                            {{ optional($tercero->fec_ing)->format('Y-m-d') ?? 'sin fecha' }}</div>
                    </div>
                    <div class="row g-0 mb-4">
                        <div class="col-sm-6 text-muted">Fecha de Nacimiento</div>
                        <div class="col-sm-6 fw-semibold">
                            {{ optional($tercero->fec_nac)->format('Y-m-d') ?? 'sin fecha' }}</div>
                    </div>
                    <div class="row g-0 mb-4">
                        <div class="col-sm-6 text-muted">Edad</div>
                        <div class="col-sm-6 fw-semibold">{{ $tercero->edad ?? ' ' }}</div>
                    </div>
                    <div class="row g-0 mb-4">
                        <div class="col-sm-6 text-muted">Declara renta</div>
                        <div class="col-sm-6 fw-semibold"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if ($retiro->isEmpty())
        <div class="col-lg-12" id="cardGenerarRetiro" style="display: none;">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title">Generar Retiro</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cinco.retiros.store') }}" id="formAddRetiro" method="POST" novalidate>
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <input type="hidden" name="cod_ter" value="{{ $tercero->cod_ter }}">
                                <label class="form-label">Tipo de Retiro<span class="text-danger">*</span></label>
                                <select class="form-select" name="TipoRetiro" required>
                                    @foreach ($tiposselect as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Observación<span class="text-danger">*</span></label>
                                <input type="text" class="form-control text-uppercase" name="observación"
                                    required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3 mt-4">
                                <label class="form-label">Fecha último aporte<span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="FechaUltimoAporte" required>
                            </div>
                            <div class="col-3 mt-4">
                                <label class="form-label">Fecha Incial Liquidación<span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="fechaInicialLiquidacion" required>
                                    <option value="{{ $tercero->fec_minis }}">Ingreso al ministerio
                                        {{ optional($tercero->fec_minis)->format('Y-m-d') }}</option>
                                    <option value="{{ $tercero->fec_aport }}">Primer Aporte
                                        {{ optional($tercero->fec_aport)->format('Y-m-d') }}</option>
                                </select>
                            </div>
                            <div class="col-3 mt-4">
                                <label class="form-label">Fecha Retiro IPUC<span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="FechaRetiro" required>
                            </div>
                            <div class="col-3 mt-4">
                                <label class="form-label">Inactivar Tercero en:<span
                                        class="text-danger">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="maeterdesactivar">
                                    <label class="form-check-label" for="maeterdesactivar">Maestra Terceros</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="segurosdesactivar">
                                    <label class="form-check-label" for="segurosdesactivar">Seguros</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="exequialdesactivar">
                                    <label class="form-check-label" for="exequialdesactivar">Exequiales</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4 mt-4">
                                <label class="form-label">Beneficios</label>
                                @foreach ($opciones[1] as $o)
                                    <div class="row mb-4 align-items-center">
                                        <div class="col-lg-4"><label>{{ $o->nombre }}</label></div>
                                        <div class="col-lg-7">
                                            <input type="number" class="form-control input-beneficio"
                                                name="opcion[{{ $o->id }}]"
                                                @if ($o->id == 1) value="{{ number_format($totalAcomulado, 0, '', '') }}" @endif
                                                required>
                                        </div>
                                    </div>
                                @endforeach
                                <p class="text-end" style="padding-right: 50px">Total
                                    <span class="badge bg-gray-200 text-dark" style="font-size: 15px;"
                                        id="total-beneficios"></span>
                                </p>
                            </div>
                            <div class="col-4 mt-4">
                                <label class="form-label">Base Retención</label>
                                @foreach ($opciones[2] as $o)
                                    <div class="row mb-4 align-items-center">
                                        <div class="col-lg-4"><label>{{ $o->nombre }}</label></div>
                                        <div class="col-lg-7">
                                            <input type="number" class="form-control input-retencion"
                                                name="opcion[{{ $o->id }}]" required>
                                        </div>
                                    </div>
                                @endforeach
                                <p class="text-end" style="padding-right: 50px">Total
                                    <span class="badge bg-gray-200 text-dark" style="font-size: 15px;"
                                        id="total-retencion"></span>
                                </p>
                            </div>
                            <div class="col-4 mt-4">
                                <label class="form-label">Saldos a favor</label>
                                @foreach ($opciones[3] as $o)
                                    <div class="row mb-4 align-items-center">
                                        <div class="col-lg-4"><label>{{ $o->nombre }}</label></div>
                                        <div class="col-lg-7">
                                            <input type="number" class="form-control input-saldos"
                                                name="opcion[{{ $o->id }}]" required>
                                        </div>
                                    </div>
                                @endforeach
                                <p class="text-end" style="padding-right: 50px">Total
                                    <span class="badge bg-gray-200 text-dark" style="font-size: 15px;"
                                        id="total-saldos"></span>
                                </p>
                                <input type="hidden" name="beneficiovalor" id="beneficiovalor">
                                <input type="hidden" name="retencionvalor" id="retencionvalor">
                                <input type="hidden" name="saldosvalor" id="saldosvalor">
                            </div>
                        </div>
                        <div class="col-12 mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-plus me-2"></i>
                                <span>Generar Retiro</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <script>
        $(document).ready(function() {
            $('#formAddRetiro').submit(function(event) {
                var form = this;
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    $(form).addClass('was-validated');
                }
            });
            $('#btnCardRetiros').on('click', function() {
                $('#cardGenerarRetiro').show();
                $('#cardGenerarRetiro').slideDown('fast', function() {
                    $('html, body').animate({
                        scrollTop: $('#cardGenerarRetiro').offset().top
                    }, 500);
                });
            });

            function calcularTotal(selectorInputs, selectorTotal, inputHidden) {
                let total = 0;
                $(selectorInputs).each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $(selectorTotal).text('$ ' + total.toLocaleString('es-CO'));
                $(inputHidden).val(total);
            }

            $(document).on('input', '.input-beneficio, .input-retencion, .input-saldos', function() {
                calcularTotal('.input-beneficio', '#total-beneficios', '#beneficiovalor');
                calcularTotal('.input-retencion', '#total-retencion', '#retencionvalor');
                calcularTotal('.input-saldos', '#total-saldos', '#saldosvalor');
            });

            calcularTotal('.input-beneficio', '#total-beneficios', '#beneficiovalor');
            calcularTotal('.input-retencion', '#total-retencion', '#retencionvalor');
            calcularTotal('.input-saldos', '#total-saldos', '#saldosvalor');
        });
    </script>
</x-base-layout>
