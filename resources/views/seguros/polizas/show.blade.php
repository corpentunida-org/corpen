@php
    $colors = ['info', 'warning', 'danger', 'success', 'primary'];
@endphp
<x-base-layout>
    @section('titlepage', 'Polizas')
    <x-success />
    <x-error />
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
                <div class="mb-4 mb-lg-0">
                    <div class="d-flex gap-4 align-items-center">
                        <div class="avatar-text avatar-lg bg-gray-200">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <div class="fs-12 text-muted">Titular: </div>
                            <div class="fs-4 fw-bold text-dark"><span
                                    class="counter">{{ $poliza->asegurado->terceroAF->nombre }}</span></div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">{{ $poliza->asegurado->titular }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 d-flex justify-content-end align-items-center">
                    <x-input-search-seguros></x-input-search-seguros>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 pb-4">
        <div class="card stretch stretch-full px-4 py-4 mb-3">
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center justify-content-start gap-4">
                    <div class="btn btn-sm btn-light-brand p-3 bg-soft-primary">{{ $poliza->asegurado->parentesco }}
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">{{ $poliza->tercero->nombre }} @if ($poliza->asegurado->viuda)
                            <span class="badge bg-danger text-white ms-2">VIUDA</span>
                        @endif
                        </h5>
                        <div class="fs-12 text-muted">{{ $poliza->seg_asegurado_id }}</div>
                    </div>
                </div>
                @if (!$poliza->active || $poliza->esreclamacion->contains('idCobertura', 1))
                    <a href="#flush-collapseOne" class="btn bg-soft-danger text-danger">
                        <i class="feather-x-circle me-2"></i>
                        <span>Póliza Cancelada</span>
                    </a>
                @elseif($poliza->active && $poliza->esreclamacion->contains('finReclamacion', 0))
                    @php
                        $reclamacionActiva = $poliza->esreclamacion->firstWhere('finReclamacion', 0);
                    @endphp
                    <a href="{{ route('seguros.reclamacion.edit', ['reclamacion' => $reclamacionActiva->id]) }}"
                        class="btn btn-sm bg-soft-danger text-danger">Activo en una reclamación</a>
                @elseif ($poliza->active)
                    @can('seguros.poliza.update')
                        <a href="{{ route('seguros.novedades.create', ['a' => $poliza->seg_asegurado_id]) }}"
                            class="btn btn-light-brand">
                            <i class="feather-check-circle me-2"></i>
                            <span>Generar Novedad</span>
                        </a>
                    @endcan
                @endif
            </div>
            <div
                class="p-4 mb-4 d-xxl-flex d-xl-block d-md-flex align-items-center justify-content-between gap-4 border border-dashed border-gray-5 rounded-1">
                <div>
                    <div class="fs-14 fw-bold text-dark mb-1">Seguro de vida
                        <a href="javascript:void(0);"
                            class="badge bg-primary text-white ms-2">{{ $poliza->plan->name ?? '' }}</a>
                    </div>
                    <div class="fs-12 text-muted">{{ $poliza->plan->condicion->descripcion ?? '' }}</div>
                </div>
                <div class="my-3 my-xxl-0 my-md-3 my-md-0">
                    <div class="fs-20 text-dark">
                        <span class="fw-bold">$
                            @if ($poliza->valor_prima === $poliza->primapagar)
                                {{ number_format($poliza->valor_prima) }}</span> / <em class="fs-11 fw-medium">Mes</em>
                            @else
                            {{ number_format($poliza->primapagar) }}</span> / <em
                                class="badge bg-soft-success text-success">Beneficio</em>
                        @endif
                    </div>
                    <div class="fs-12 text-muted mt-1">EXTRA:
                        <strong class="text-dark">{{ $poliza->extra_prima }}%</strong>
                    </div>
                </div>
                <div class="hstack gap-3">
                    @if ($poliza->active && $poliza->esreclamacion->contains('finReclamacion', 0))
                        @php
                            $reclamacionActiva = $poliza->esreclamacion->firstWhere('finReclamacion', 0);
                        @endphp
                        <span class="text-danger">{{ $reclamacionActiva->estadoReclamacion->nombre }}</span>
                    @elseif ($poliza->active)
                        @can('seguros.reclamacion.store')
                            <a href="{{ route('seguros.reclamacion.create', ['a' => $poliza->seg_asegurado_id]) }}"
                                class="text-danger">Generar Reclamación</a>
                        @endcan
                    @endif
                </div>
            </div>
            <div class="d-flex flex-wrap justify-content-center mb-4" style="height: auto;">
                @foreach ($poliza->plan->coberturas as $cobertura)
                    @if ($poliza->esreclamacion->contains('idCobertura', $cobertura->id))
                        <div class="d-flex flex-column align-items-center justify-content-between text-center p-4 mb-4 bg-soft-danger border border-dashed border-gray-5 rounded-1 position-relative"
                            style="flex: 1 1 auto; max-width: 13%; min-width: 150px; margin: 5px;">
                    @else
                            <div class="d-flex flex-column align-items-center justify-content-between text-center p-4 mb-4 bg-soft-100 border border-dashed border-gray-5 rounded-1 position-relative"
                                style="flex: 1 1 auto; max-width: 13%; min-width: 150px; margin: 5px;">
                        @endif
                            <div style="height: 60%;">
                                <h6 class="fs-13 fw-bold">{{ $cobertura->nombre }}</h6>
                                <p class="fs-12 fw-normal text-muted">Seguros de vida</p>
                            </div>
                            @if ($cobertura->id == 1)
                                {{-- @php $valoracalcular = $cobertura->pivot->valorAsegurado @endphp //valor full plan --}}
                                @php $valoracalcular = $poliza->valor_asegurado @endphp
                            @endif
                            <div class="mt-4">
                                <span class="fs-15 fw-bold text-dark">
                                    $
                                    @php
                                        $valorcalculado = $valoracalcular * ($cobertura->pivot->porcentaje / 100) ?? 0;
                                    @endphp
                                    {{ number_format($valorcalculado) }}
                                </span>
                                <p class="fs-12 fw-normal text-muted">Valor asegurado</p>
                            </div>
                            <div class="position-absolute top-0 start-50 translate-middle">
                                @if ($poliza->esreclamacion->contains('idCobertura', $cobertura->id))
                                    <i class="feather-info fs-12 bg-danger text-white p-1 mt-1 rounded-circle"></i>
                                @else
                                    <i class="feather-check fs-12 bg-primary text-white p-1 rounded-circle"></i>
                                @endif
                            </div>
                        </div>
                @endforeach
                </div>

                @if ($grupoFamiliar->count() >= 1)
                    <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold mb-0">Grupo Familiar:</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr class="border-top">
                                    <th>Cedula</th>
                                    <th>Nombre</th>
                                    <th>Parentesco</th>
                                    <th>Plan</th>
                                    <th>Beneficio</th>
                                    <th>Prima </th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($grupoFamiliar as $familiar)
                                    <tr>
                                        <td><a href="javascript:void(0);">{{ $familiar->cedula }}</a></td>
                                        <td>{{ $familiar->tercero->nombre }}</td>
                                        <td>{{ $familiar->parentesco }}</td>
                                        <td>
                                            @if($familiar->polizas->first())
                                            <div class="fw-semibold mb-1">$ {{ number_format($familiar->polizas->first()->valor_asegurado) }}</div>
                                            <div class="d-flex gap-3"><a href="#"
                                                    class="hstack gap-1 fs-11 fw-normal">
                                                    <span class="badge bg-soft-warning text-warning">{{ $familiar->polizas->first()->plan->name ?? '' }}</span></a>
                                                    <a
                                                    href="#" class="hstack gap-1 fs-11 fw-normal">
                                                        $ {{ number_format($familiar->polizas->first()->plan->prima) ?? '' }}</a></div>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $valortotalbene = null;
                                                $poliza = $familiar->polizas->first();

                                                if ($poliza && $poliza->plan) {
                                                    $valortotalbene = $poliza->plan->prima - $poliza->primapagar;
                                                }
                                            @endphp

                                            @if (!is_null($valortotalbene))
                                                <span class="{{ $valortotalbene != 0 ? 'badge bg-soft-success text-success' : '' }}">
                                                    $ {{ number_format($valortotalbene) }}
                                                </span>
                                            @endif
                                        </td>                                        
                                        <td>@if($familiar->polizas->first())
                                        $ {{ number_format($familiar->polizas->first()->primapagar) }}@endif</td>
                                        <td class="hstack justify-content-end gap-4 text-end">
                                            <form action="{{ route('seguros.poliza.show', ['poliza' => 'ID']) }}" method="GET">
                                                <div data-bs-toggle="tooltip" data-bs-trigger="hover"
                                                    data-bs-original-title="Ver Poliza">
                                                    <input type="hidden" name="id" id="valueCedula"
                                                        value="{{ $familiar->cedula }}">
                                                    <button class="w-50 btn">
                                                        <i class="feather feather-send fs-12"></i>
                                                        <span></span>
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                <div class="accordion proposal-faq-accordion mt-4">
                    <div class="accordion-item mt-2">
                        <h2 class="accordion-header" id="flush-headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseOne" aria-expanded="false"
                                aria-controls="flush-collapseOne">Actividad de la Póliza</button>
                        </h2>
                        <div id="flush-collapseOne" class="accordion-collapse collapse"
                            aria-labelledby="flush-headingOne" data-bs-parent="#accordionFaqGroup" style="">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr class="border-top">
                                                <th>Fecha </th>
                                                <th>Acción </th>
                                                <th>Observación </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $poliza->created_at }}</td>
                                                <td><span class="badge bg-soft-warning text-warning">NOVEDADES</span>
                                                </td>
                                                <td>SE CREÓ LA PÓLIZA</td>
                                            </tr>
                                            @foreach ($registrosnov as $i)
                                                <tr>
                                                    <td>{{ $i->updated_at }}</td>
                                                    @php
                                                        $colorspan = 'primary';
                                                        $modelo = class_basename($i);
                                                        if ($modelo === 'SegNovedades') {
                                                            $colorspan = 'warning';
                                                        } elseif ($modelo === 'SegReclamaciones') {
                                                            $colorspan = 'danger';
                                                        } elseif ($modelo === 'SegBeneficios') {
                                                            $colorspan = 'success';
                                                        }
                                                    @endphp
                                                    <td><span
                                                            class="badge bg-soft-{{ $colorspan }} text-{{ $colorspan }}">{{strtoupper(substr($modelo, 3))}}</span>
                                                    </td>
                                                    <td>
                                                        {{ strtoupper($i->observaciones ?? $i->cambiosEstado->last()->observacion) }}
                                                        @php
                                                            $modelo = class_basename($i);
                                                        @endphp
                                                        @if ($modelo === 'SegBeneficios')
                                                            <span class="fs-13 fw-semibold"> $
                                                                {{ number_format($i->valorDescuento) }} </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($poliza->active)
                    <div class="d-flex gap-2">
                        <div class="filter-dropdown undefined">
                            <button class="btn btn-light-brand" id="btnCardBeneficios">
                                <i class="feather-star me-2"></i>
                                <span>Registrar un Beneficio</span>
                            </button>
                        </div>
                    </div>
                @endif
                @if (($poliza->asegurado->parentesco == 'AF' || $poliza->asegurado->viuda) && $poliza->active)
                    <div class="p-4 d-xxl-flex d-xl-block d-md-flex align-items-center justify-content-end gap-4">

                        <div class="d-flex gap-4 align-items-center justify-content-sm-end justify-content-between">
                            <div href="" class="text-bold">Valor Aseguradora</div>
                            <div href="" class="btn bg-soft-primary" style="font-size:20px;">$
                                {{ number_format($totalPrima) }}
                            </div>
                        </div>
                        <div class="d-flex gap-4 align-items-center justify-content-sm-end justify-content-between">
                            <div class="text-bold">Subsidio</div>
                            <div class="btn bg-soft-warning collapsed" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="false" style="font-size:20px;">$
                                @if (!$poliza->asegurado->valorpAseguradora || $totalPrima == 0)
                                    0
                                    @php
                                        $subsidio = 0;
                                        $s = 0;
                                    @endphp
                                @else
                                    @php
                                        $sub = 0;
                                        foreach ($beneficios as $beneficio) {
                                            $sub += $beneficio->valorDescuento;
                                        }
                                        $s = $totalPrima - $sub - $poliza->valorpagaraseguradora;
                                        $subsidio = $sub + $s;
                                    @endphp
                                    {{ number_format($subsidio) }}
                                @endif
                            </div>
                        </div>
                        <div class="d-flex gap-4 align-items-center justify-content-sm-end justify-content-between">
                            <a href="javascript:void(0);" class="text-bold">Valor Titular</a>
                            <a href="javascript:void(0);" class="btn bg-soft-info" style="font-size:20px;">$
                                {{ number_format($poliza->valorpagaraseguradora) }}</a>
                        </div>
                    </div>
                    <div id="collapseOne" class="accordion-collapse page-header-collapse collapse" style="">
                        <div class="accordion-body pb-2">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        @foreach ($beneficios as $beneficio)
                                            <tr>
                                                <td colspan="3"></td>
                                                <td class="fw-semibold text-dark bg-gray-100 text-lg-end">Beneficio</td>
                                                <td class="fw-bold text-dark bg-gray-100"> $
                                                    {{ number_format($beneficio->valorDescuento) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="3"></td>
                                            <td class="fw-semibold text-dark bg-gray-100 text-lg-end">Subsidio Inicial</td>
                                            <td class="fw-bold text-dark bg-gray-100"> $ {{ number_format($s) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if ($poliza->valorpagaraseguradora + $subsidio != $totalPrima || $subsidio < 0)
                        <small class="form-text text-danger text-end mb-4" style="margin-right: 30px;">El valor a pagar del
                            titular es erroneo.
                            @can('seguros.poliza.update')
                                <a href="{{ route('seguros.novedades.create', ['a' => $poliza->seg_asegurado_id]) }}"
                                    class="text-danger"> Para corregirlo click aqui</a>
                            @endcan
                        </small>
                    @endif
                @endif
                @if ($beneficiarios->isnotEmpty() && $poliza->active)
                    @include('seguros.beneficiarios.show')
                @endif
                @if ($poliza->active)
                    @can('seguros.beneficiarios.store')
                        <div class="my-4 d-flex align-items-center justify-content-start">
                            <div class="d-flex gap-2">
                                <a href="{{ route('seguros.beneficiario.create', ['a' => $poliza->seg_asegurado_id, 'p' => $poliza->id]) }}"
                                    class="btn btn-success">
                                    <i class="feather-plus me-2"></i>
                                    <span>Agregar Beneficiario</span>
                                </a>
                            </div>
                        </div>
                    @endcan
                @endif
            </div>
        </div>
    </div>
    <div id="CardAddBeneficios" class="col-lg-12" style="display: none;">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="fw-bold mb-0">
                    <span class="d-block mb-2">Crear Beneficio </span>
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('seguros.beneficios.store') }}" id="formAddBeneficio" novalidate>
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="col-lg-3 mb-4">
                            <label class="form-label">Valor Beneficio<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="valorbene" id="inputValorBene">
                                <div class="fs-12 fw-normal text-muted text-truncate-1-line pt-1">
                                    <div class="custom-control custom-checkbox" style="display: none;"
                                        id="checkvalbeneficio">
                                        <input type="checkbox" class="form-check-input ml-3" id="checkbox2"
                                            name="checkconfirmarbene" value=true>
                                        <label class="form-check-label" for="checkbox2" id="labeltextbeneficio"></label>
                                    </div>
                                </div>
                                <input type="hidden" name="valorPrima" id="inputvalorprima">
                            </div>
                        </div>
                        <div class="col-lg-3 mb-4">
                            <label class="form-label">Porcentaje Beneficio<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" min="0" max="100" name="porbene"
                                    id="inputporbene">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <label class="form-label">Observaciones<span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="observacionesbene" required>
                        </div>
                        <input type="hidden" name="aseguradoId" value="{{ $poliza->seg_asegurado_id }}">
                        <input type="hidden" name="polizaId" value="{{ $poliza->id }}">
                    </div>
                    <div class="d-flex flex-row-reverse gap-2 mt-2">
                        <button class="btn btn-success mt-4" data-bs-toggle="tooltip" type="submit"
                            data-bs-original-title="crear">
                            <i class="feather-plus me-2"></i>
                            <span>Registrar Beneficio</span>
                        </button>
                    </div>
                </form>
                <script>
                    $(document).ready(function () {
                        $('#formAddBeneficio').submit(function (event) {
                            var form = this;
                            if (!form.checkValidity()) {
                                $(form).addClass('was-validated');
                                event.preventDefault();
                                event.stopPropagation();
                            }
                        });
                        $('#btnCardBeneficios').on('click', function () {
                            $('#CardAddBeneficios').show();
                            $('#CardAddBeneficios').slideDown('fast', function () {
                                $('html, body').animate({
                                    scrollTop: $('#CardAddBeneficios').offset().top
                                }, 500);
                            });
                        });
                        $('#inputValorBene').on('input', function () {
                            var valor = parseFloat($(this).val().trim());
                            var valorOriginal = parseFloat("{{ $poliza->valor_prima}}");
                            if (!isNaN(valor) && valor > 0) {
                                $('#checkvalbeneficio').slideDown();
                            } else {
                                $('#checkvalbeneficio').slideUp();
                                $('#checkbox2').prop('checked', false);
                            }
                            var valorprima = valorOriginal - (isNaN(valor) ? 0 : valor);
                            $('#labeltextbeneficio').text('Confirmar valor prima $' + valorprima);
                            $('#inputvalorprima').val(valorprima);
                        });
                        $('#inputporbene').on('input', function () {
                            var porcentaje = parseFloat($(this).val().trim());
                            var valorOriginal = parseFloat("{{ $poliza->valor_prima}}");

                            if (!isNaN(porcentaje) && porcentaje > 0) {
                                $('#checkvalbeneficio').slideDown();
                            } else {
                                $('#inputValorBene').val('');
                                $('#checkvalbeneficio').slideUp();
                                $('#checkbox2').prop('checked', false);
                            }
                            let valorDescuento = (valorOriginal * porcentaje) / 100;
                            $('#inputValorBene').val(valorDescuento);
                            var valorprima = Math.ceil(valorOriginal - valorDescuento);
                            $('#labeltextbeneficio').text('Confirmar valor prima $' + valorprima);
                            $('#inputvalorprima').val(valorprima);

                        });
                    });
                </script>
            </div>
        </div>
    </div>
</x-base-layout>