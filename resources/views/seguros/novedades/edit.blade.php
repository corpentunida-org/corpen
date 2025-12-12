@php
    $estados = [
        1 => ['color' => 'primary'],
        2 => ['color' => 'warning'],
        3 => ['color' => 'success'],
        4 => ['color' => 'danger'],
        5 => ['color' => 'info'],
    ];
@endphp
<x-base-layout>
    <x-error />
    @if (isset($error))
        <div class="alert alert-dismissible p-4 mt-3 alert-soft-danger-message" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <p class="mb-0">
                {{ $error }}
            </p>
        </div>
    @endif
    @section('titlepage', 'Actualizar Novedad')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body mb-4 mb-lg-0">
                <div class="d-flex gap-4 align-items-center">
                    <div class="avatar-text avatar-lg bg-gray-200">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <div class="fs-12 text-muted">Asegurado: </div>
                        <div class="fs-4 fw-bold text-dark"><span class="counter">{{ $novedad->nombre_tercero }}</span>
                        </div>
                        <h3 class="fs-13 fw-semibold text-truncate-1-line">{{ $novedad->id_asegurado }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 p-4 card">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <label class="form-label">Valor Asegurado Actual</label>
                    <input type="text" id="valoraseguradoplan" name="valorAsegurado" class="form-control"
                        value="{{ optional($novedad->poliza)->valor_asegurado ? '$ ' . number_format($novedad->poliza->valor_asegurado) : '' }}"
                        disabled>
                </div>
                <div class="col-lg-4 mb-4">
                    <label class="form-label">Prima Plan Actual</label>
                    <input type="text" id="valoraseguradoplan" name="valorAsegurado" class="form-control"
                        value="{{ optional($novedad->poliza)->valor_prima ? '$ '.number_format($novedad->poliza->valor_prima) : '' }}" disabled>
                </div>
                <div class="col-lg-4 mb-4">
                    <label class="form-label">Valor a Pagar Mensualidad</label>
                    <input type="text" id="primaplan" class="form-control" name="valorPrima"
                        value="{{ optional($novedad->poliza)->primapagar ? '$ '.number_format($novedad->poliza->primapagar) : '' }}" disabled>
                </div>
            </div>
            @if ($editar)
                <form method="POST" action="{{ route('seguros.novedades.update', $novedad->id) }}"
                    id="formUpdateNovedad" novalidate>
                    @csrf
                    @method('PUT')
            @endif
            <div class="row">
                <div class="col-lg-2 mb-4">
                    <label class="form-label">Tipo</label>
                    <input class="form-control" name="tipo"
                        value="{{ $novedad->tipo == '1' ? 'MODIFICACIÓN' : 'INGRESO' }}" readonly>
                </div>
                <div class="col-lg-3 mb-4">
                    <label class="form-label">Valor Asegurado Solicitado<span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="valAsegurado"
                        value="{{ $novedad->valorAsegurado }}">
                    <input type="hidden" class="form-control" name="planid" value="{{ $novedad->id_plan }}">
                </div>
                <div class="col-lg-2 mb-4">
                    <label class="form-label">Prima Aseguradora Solicitado</label>
                    <input type="number" class="form-control" name="primaAseguradora" id="primaaseguradora"
                        value="{{ $novedad->primaAseguradora }}">
                </div>
                <div class="col-lg-2 mb-4">
                    <label class="form-label">Prima Pagar</label>
                    <input type="number" class="form-control" name="primaPagar" value="{{ $novedad->primaCorpen }}"
                        id="valorapagarcorpen">
                </div>
                <div class="col-lg-3 mb-4">
                    <label class="form-label">Extraprima</label>
                    <input type="number" class="form-control" name="extraprima" value="{{ $novedad->extraprima }}"
                        id="inputextraprima">
                    <input type="hidden" id="valorprimaaumentar" name="valorprimaaumentar">
                    <div class="fs-12 fw-normal text-muted text-truncate-1-line text-wrap pt-1">
                        <div class="custom-control custom-checkbox" id="checkextraprimaval" style="display: none;">
                            <input type="checkbox" class="form-check-input ml-3" id="checkbox2"
                                name="checkconfirmarextra" value=true>
                            <label class="form-check-label text-wrap" for="checkbox2" id="labeltextextra"
                                style="white-space: normal; word-wrap: break-word; max-width: 100%;"></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-2 mb-4">
                    <label class="form-label">Fecha de Inicio<span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="fecha_inicio"
                        value="{{ optional($novedad->cambiosEstado->last())->fechaInicio?->format('Y-m-d') }}"
                        readonly>
                </div>
                <div class="col-lg-2 mb-4">
                    <label class="form-label">Estado de la Novedad<span class="text-danger">*</span></label>
                    <select class="form-control" name="estado" readonly>
                        <option value="1">SOLICITUD</option>
                        <option value="2">RADICADO</option>
                        <option value="3">APROBADO</option>
                        <option value="4">NEGADO</option>
                    </select>
                </div>
                <div class="col-lg-8 mb-4">
                    <label class="form-label">Observación<span class="text-danger">*</span></label>
                    <input type="text" class="form-control text-uppercase" name="observaciones" required>
                    <span
                        class="fs-12 fw-bold text-uppercase text-truncate-1-line pt-1">{{ $novedad->cambiosEstado->last()->observaciones }}</span>
                    <input type="hidden" class="form-control" name="individual" value=true>
                </div>
                @if ($novedad->formulario)
                    <a href="{{ $novedad->getFile($novedad->formulario) }}" target="_blank">Ver Formulario PDF</a>
                @endif
            </div>
            <div class="accordion proposal-faq-accordion mt-4">
                <div class="accordion-item mt-2">
                    <h2 class="accordion-header" id="flush-headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#flush-collapseOne" aria-expanded="false"
                            aria-controls="flush-collapseOne">Actividad de la solicitud</button>
                    </h2>
                    <div id="flush-collapseOne" class="accordion-collapse collapse"
                        aria-labelledby="flush-headingOne" data-bs-parent="#accordionFaqGroup" style="">
                        <div class="accordion-body">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr class="border-top">
                                            <th>Fecha </th>
                                            <th>Estado </th>
                                            <th>Observación </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($actividadnov as $actividad)
                                            @php
                                                $color = $estados[$actividad->estado]['color'] ?? 'secondary';
                                            @endphp
                                            <tr>
                                                <td>{{ $actividad->fechaInicio }}</td>
                                                <td><span
                                                        class="badge bg-soft-{{ $color }} text-{{ $color }}">{{ $actividad->estadosname->nombre ?? ' ' }}</span>
                                                </td>
                                                <td>{{ strtoupper($actividad->observaciones) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if ($editar)
                <div class="d-flex flex-row-reverse gap-2 mt-2">
                    <button class="btn btn-warning mt-4" type="submit">
                        <span>Actualizar Novedad</span>
                    </button>
                </div>
                </form>
            @endif
        </div>
        <script>
            $(document).ready(function() {
                $('#inputextraprima').on('input', function() {
                    var valor = parseFloat($(this).val().trim());
                    var valorOriginal = parseFloat({{ floatval($novedad->primaAseguradora) }});
                    console.log(valorOriginal);
                    if (!isNaN(valor) && valor > 0) {
                        $('#checkextraprimaval').slideDown();
                    } else {
                        $('#checkextraprimaval').slideUp();
                        $('#checkbox2').prop('checked', false);
                    }
                    var valorprima = Math.round(valorOriginal * (isNaN(valor) ? 0 : valor / 100));
                    $('#labeltextextra').text('Confirmar valor prima + $' + valorprima);
                    $('#valorprimaaumentar').val(valorprima);
                });
                $("#checkbox2").on("change", function() {
                    valor = parseFloat($("#valorprimaaumentar").val()) || 0;
                    console.log(valor);
                    valorpagar = parseFloat($("#valorapagarcorpen").val()) || 0;
                    if ($(this).is(":checked")) {
                        valorFinal = valor + valorpagar;
                    } else {
                        valorFinal = valorpagar - valor;
                    }
                    $("#valorapagarcorpen").val(valorFinal);
                });
            });
        </script>
</x-base-layout>
