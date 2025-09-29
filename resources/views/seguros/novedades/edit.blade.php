<x-base-layout>
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
                    <label class="form-label">Valor Asegurado</label>
                    <input type="text" id="valoraseguradoplan" name="valorAsegurado" class="form-control"
                        value="$ {{ number_format($novedad->poliza->valor_asegurado) }}" disabled>
                </div>
                <div class="col-lg-4 mb-4">
                    <label class="form-label">Prima Plan</label>
                    <input type="text" id="valoraseguradoplan" name="valorAsegurado" class="form-control"
                        value="$ {{ number_format($novedad->poliza->valor_prima) }}" disabled>
                </div>
                <div class="col-lg-4 mb-4">
                    <label class="form-label">Valor a Pagar Mensualidad</label>
                    <input type="text" id="primaplan" class="form-control" name="valorPrima"
                        value="$ {{ number_format($novedad->poliza->primapagar) }}" disabled>
                </div>
            </div>
            <form method="POST" action="{{ route('seguros.novedades.update', $novedad->id) }}" id="formUpdateNovedad"
                novalidate>
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-2 mb-4">
                        <label class="form-label">Tipo</label>
                        <input class="form-control" name="tipo"
                            value="{{ $novedad->tipo == '1' ? 'MODIFICACIÓN' : 'INGRESO' }}" readonly>
                    </div>
                    <div class="col-lg-3 mb-4">
                        <label class="form-label">Valor Asegurado<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="valAsegurado"
                            value="{{ $novedad->valorAsegurado }}">
                        <input type="hidden" class="form-control" name="planid" value="{{ $novedad->id_plan }}">
                    </div>
                    <div class="col-lg-2 mb-4">
                        <label class="form-label">Prima Aseguradora</label>
                        <input type="number" class="form-control" name="primaAseguradora" id="primaaseguradora"
                            value="{{ $novedad->primaAseguradora }}">
                    </div>
                    <div class="col-lg-2 mb-4">
                        <label class="form-label">Prima Pagar</label>
                        <input type="number" class="form-control" name="primaPagar"
                            value="{{ $novedad->primaCorpen }}" id="valorapagarcorpen">
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
                        <input type="text" class="form-control text-uppercase" name="observaciones"
                            value="{{ $novedad->cambiosEstado->last()->observaciones }}" required>
                        <input type="hidden" class="form-control" name="individual" value=true>
                    </div>
                    @if ($novedad->formulario)
                        <a href="{{ route('seguros.novedades.formulario', $novedad->id) }}" target="_blank">
                            Ver Formulario PDF
                        </a>
                    @endif
                </div>
                <div class="d-flex flex-row-reverse gap-2 mt-2">
                    <button class="btn btn-warning mt-4" type="submit">
                        <span>Actualizar Novedad</span>
                    </button>
                </div>
            </form>
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
