<x-base-layout>
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
                            <div class="fs-12 text-muted">Asegurado: </div>
                            <div class="fs-4 fw-bold text-dark"><span
                                    class="counter">{{ $poliza->tercero->nom_ter ?? ' ' }}</span></div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">{{ $poliza->seg_asegurado_id }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 d-flex justify-content-end align-items-center">
                    <x-input-search-seguros></x-input-search-seguros>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Editar Beneficio: </span>
                    </h5>
                </div>
                <form class="row" method="post" action="{{ route('seguros.beneficios.update', $SegBeneficios->id) }}"
                    id="formEditBeneficio" novalidate>
                    @method('PUT')
                    @csrf
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="form-label">Descuento Valor</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="desval" id="inputValorBene"
                                    value="{{$SegBeneficios->valorDescuento}}" required>
                                <div class="fs-12 fw-normal text-muted text-truncate-1-line pt-1">
                                    <div class="custom-control custom-checkbox" style="display: none;"
                                        id="checkvalbeneficio">
                                        <input type="checkbox" class="form-check-input ml-3" id="checkbox2"
                                            name="checkconfirmarbene" value=true>
                                        <label class="form-check-label" for="checkbox2" id="labeltextbeneficio"></label>
                                    </div>
                                </div>
                                <input type="hidden" min="0" name="valorPrima" id="inputvalorprima">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label">Descuento Porcentaje</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="porval" id="inputporbene"
                                    value="{{$SegBeneficios->porcentajeDescuento}}">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <label class="form-label">Observación</label>
                            <input type="text" class="form-control" name="observacion"
                                value="{{$SegBeneficios->observaciones}}" readonly>
                        </div>
                        <input type="hidden" name="titular" value="{{ $poliza->asegurado->titular }}">
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="submit" class="btn btn-md btn-warning">Actualizar</button>
                    </div>
            </div>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('#formEditBeneficio').submit(function (event) {
                var form = this;
                if (!form.checkValidity()) {
                    $(form).addClass('was-validated');
                    event.preventDefault();
                    event.stopPropagation();
                }
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
</x-base-layout>