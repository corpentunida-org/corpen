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
    <div class="card">
        <div class="subscription-plan px-4 pt-4 mb-3">
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center justify-content-start gap-4">
                    <div class="btn btn-sm btn-light-brand p-3 bg-soft-primary">{{ $poliza->asegurado->parentesco }}
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">{{ $poliza->tercero->nombre }}:</h5>
                        <div class="fs-12 text-muted">{{ $poliza->seg_asegurado_id }}</div>
                    </div>
                </div>
                @if ($poliza->reclamacion != 0)
                    <a href="{{ route('seguros.reclamacion.edit', ['reclamacion' => $poliza->esreclamacion->id]) }}"
                        class="btn btn-sm bg-soft-danger text-danger">Activo en una reclamación</a>
                @else
                    @if($poliza->active)
                    @can('seguros.poliza.update')
                        <a href="{{ route('seguros.novedades.create', ['a' => $poliza->seg_asegurado_id]) }}"
                            class="btn btn-light-brand">
                            <i class="feather-check-circle me-2"></i>
                            <span>Generar Novedad</span>
                        </a>
                    @endcan
                    @else
                        <a class="btn bg-soft-danger text-danger">
                            <i class="feather-x-circle me-2"></i>
                            <span>Poliza Cancelada</span>
                        </a>
                    @endif

                @endif
            </div>
            <div
                class="p-4 mb-4 d-xxl-flex d-xl-block d-md-flex align-items-center justify-content-between gap-4 border border-dashed border-gray-5 rounded-1">
                <div>
                    <div class="fs-14 fw-bold text-dark mb-1">Seguro de vida
                        <a
                            href="javascript:void(0);"class="badge bg-primary text-white ms-2">{{ $poliza->plan->name }}</a>
                    </div>
                    <div class="fs-12 text-muted">{{ $poliza->plan->condicion->descripcion }}</div>
                </div>
                <div class="my-3 my-xxl-0 my-md-3 my-md-0">
                    <div class="fs-20 text-dark"><span class="fw-bold">$
                            {{ number_format($poliza->valor_prima) }}</span> / <em class="fs-11 fw-medium">Mes</em>
                    </div>
                    <div class="fs-12 text-muted mt-1"><strong class="text-dark">EXTRA:
                        </strong>{{ $poliza->extra_prima }}%</div>
                </div>
                <div class="hstack gap-3">
                    @if($poliza->active)
                        @if ($poliza->reclamacion == 0)
                            @can('seguros.reclamacion.store')
                                <a href="{{ route('seguros.reclamacion.create', ['a' => $poliza->seg_asegurado_id]) }}"
                                    class="text-danger">Generar Reclamación</a>
                            @endcan
                        @else
                            <a class="text-danger">{{ $poliza->esreclamacion->estadoReclamacion->nombre }}</a>
                        @endif
                    @endif
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-center" style="height: auto;">
                @foreach ($poliza->plan->coberturas as $cobertura)
                    <div class="d-flex flex-column align-items-center justify-content-between text-center p-4 mb-4 bg-soft-100 border border-dashed border-gray-5 rounded-1 position-relative"
                        style="flex: 1 1 calc(100% / 7); max-width: calc(100% / 7); min-width: 150px; margin: 5px;">
                        <div style="height: 60%;">
                            <h6 class="fs-13 fw-bold">{{ $cobertura->nombre }}</h6>
                            <p class="fs-12 fw-normal text-muted">Seguros de vida</p>
                        </div>
                        @if ($cobertura->id == 1)
                            @php $valoracalcular = $cobertura->pivot->valorAsegurado @endphp
                        @endif
                        <div class="mt-4">
                            <span class="fs-16 fw-bold text-dark">
                                $
                                @if ($cobertura->pivot->valorAsegurado == 0 || $cobertura->pivot->valorAsegurado == null)
                                    @php
                                        $valorcalculado = $valoracalcular * ($cobertura->pivot->porcentaje / 100);
                                    @endphp
                                    {{ number_format($valorcalculado) }}
                                @else
                                    {{ number_format($cobertura->pivot->valorAsegurado) }}
                                @endif
                            </span>
                            <p class="fs-12 fw-normal text-muted">Valor asegurado</p>
                        </div>
                        <div class="position-absolute top-0 start-50 translate-middle">
                            <i class="feather-check fs-12 bg-primary text-white p-1 rounded-circle"></i>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
        @if ($grupoFamiliar->count() >= 1 && $poliza->active)
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
                            <th>Valor Asegurado</th>
                            <th>Prima</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($grupoFamiliar as $familiar)
                            <tr>
                                <td><a href="javascript:void(0);">{{ $familiar->cedula }}</a></td>
                                <td>{{ $familiar->tercero->nombre }}</td>
                                <td>{{ $familiar->parentesco }}</td>
                                <td><span
                                        class="badge bg-soft-warning text-warning">{{ $familiar->polizas->first()->plan->name }}</span>
                                </td>
                                <td>$ {{ number_format($familiar->polizas->first()->valor_asegurado) }}</td>
                                <td>$ {{ number_format($familiar->polizas->first()->valor_prima) }}</td>
                                <td class="hstack justify-content-end gap-4 text-end">
                                    <form action="{{ route('seguros.poliza.show', ['poliza' => 'ID']) }}"
                                        method="GET">
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
                <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne"
                    data-bs-parent="#accordionFaqGroup" style="">
                    <div class="accordion-body">
                        <ul class="list-unstyled activity-feed">
                            @foreach ($registrosnov as $i => $nov)
                                <li
                                    class="d-flex justify-content-between feed-item feed-item-{{ $colors[$i % count($colors)] }}">
                                    <div>
                                        <span class="text">{{ $nov->created_at }}
                                            <a
                                                class="badge bg-soft-{{ $colors[$i % count($colors)] }} text-{{ $colors[$i % count($colors)] }} ms-1">
                                                @if (Str::contains(Str::lower($nov->observaciones), 'cambio de convenio'))
                                                    Plan Anterior ${{ number_format($nov->valorAsegurado) }}
                                                @elseif (Str::contains(Str::lower($nov->observaciones), 'valor a pagar'))
                                                    Valor a Pagar ${{ number_format($nov->valorpagar) }}
                                                @elseif ($nov->valorDescuento)
                                                    Descuento ${{ number_format($nov->valorDescuento) }}
                                                @elseif ($nov->retiro)
                                                    Se canceló la póliza
                                                @else
                                                    Se Inicio un proceso de reclamación
                                                @endif
                                            </a></span>
                                        <span class="text-truncate-1-line">
                                            {{ strtoupper($nov->observaciones) }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @if($poliza->active)
            <div class="d-flex gap-2">
                <div class="filter-dropdown undefined">
                    <button class="btn btn-light-brand" id="btnCardBeneficios">
                        <i class="feather-star me-2"></i>
                        <span>Registrar un Beneficio</span>
                    </button>
                </div>
            </div>
        @endif

        @if ($poliza->asegurado->parentesco == 'AF' && $poliza->active)
            <div class="p-4 d-xxl-flex d-xl-block d-md-flex align-items-center justify-content-end gap-4">
                <div class="d-flex gap-4 align-items-center justify-content-sm-end justify-content-between">
                    <a href="javascript:void(0);" class="text-bold">Valor Titular</a>
                    <a href="javascript:void(0);" class="btn bg-soft-info" style="font-size:20px;">$
                        {{ number_format($poliza->valorpagaraseguradora) }}</a>
                </div>
                <div class="d-flex gap-4 align-items-center justify-content-sm-end justify-content-between">
                    <div class="text-bold">Subsidio</div>
                    <div class="btn bg-soft-warning collapsed" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne" aria-expanded="false" style="font-size:20px;">$
                        @if (!$poliza->asegurado->valorpAseguradora)
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
                    <div href="" class="text-bold">Valor Aseguradora</div>
                    <div href="" class="btn bg-soft-primary" style="font-size:20px;">$
                        {{ number_format($totalPrima) }}</div>
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
                                            {{ number_format($beneficio->valorDescuento) }}</td>
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
    <div id="CardAddBeneficios" class="col-lg-12" style="display: none;">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="fw-bold mb-0">
                    <span class="d-block mb-2">Crear Beneficio </span>
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('seguros.beneficios.store') }}" id="formAddBeneficio"
                    novalidate>
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="col-lg-3 mb-4">
                            <label class="form-label">Valor Beneficio<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="valorbene">
                            </div>
                        </div>
                        <div class="col-lg-3 mb-4">
                            <label class="form-label">Porcentaje Beneficio<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="porbene">
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
                    $(document).ready(function() {
                        $('#formAddBeneficio').submit(function(event) {
                            var form = this;
                            if (!form.checkValidity()) {
                                $(form).addClass('was-validated');
                                event.preventDefault();
                                event.stopPropagation();
                            }
                        });
                        $('#btnCardBeneficios').on('click', function() {
                            $('#CardAddBeneficios').show();
                            $('#CardAddBeneficios').slideDown('fast', function() {
                                $('html, body').animate({
                                    scrollTop: $('#CardAddBeneficios').offset().top
                                }, 500);
                            });
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</x-base-layout>