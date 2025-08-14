<x-base-layout>
    @section('titlepage', 'Crear Reclamación')
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body d-lg-flex align-items-center justify-content-between">
                <div class="mb-4 mb-lg-0">
                    <div class="d-flex gap-4 align-items-center">
                        <div class="avatar-text avatar-lg bg-gray-200">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <div class="fs-12 text-muted">Titular: </div>
                            <div class="fs-4 fw-bold text-dark"><span
                                    class="counter">{{ $poliza->asegurado->nombre_titular ?? ' ' }}</span></div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">{{ $asegurado->tercero_preferido->cod_ter }}</h3>
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
        <div class="card stretch stretch-full">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-2 mb-4">
                        <label class="form-label">Cédula Asegurado</label>
                        <input type="text" class="form-control" value="{{ $asegurado->cedula }}" readonly>
                    </div>
                    <div class="col-lg-5 mb-4">
                        <label class="form-label">Nombre Asegurado</label>
                        <input type="text" class="form-control" value="{{ $asegurado->nombre_tercero ?? 'No hay un nombre asignado' }}" readonly>
                    </div>
                    <div class="col-lg-2 mb-4">
                        <label class="form-label">Tipo de Afiliado</label>
                        <input class="form-control datepicker-input" name="contacto"
                            value="{{ $asegurado->parentesco }}" readonly>
                    </div>
                    <div class="col-lg-2 mb-4">
                        <label class="form-label">Genero</label>
                        <input class="form-control datepicker-input"
                            value="{{ $asegurado->tercero?->sexo == 'V' ? 'Masculino' : ($asegurado->tercero?->sexo == 'H' ? 'Femenino' : 'Asignar') }}" readonly>
                    </div>
                    <div class="col-lg-1 mb-4">
                        <label class="form-label">Distrito</label>
                        <input class="form-control datepicker-input" value="{{ $asegurado->tercero->cod_dist ?? ' ' }}" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body">
                @if ($reclamaciones->isNotEmpty())
                    <div class="table-responsive mb-4">
                        <label class="form-label"><span class="text-danger">HISTORIAL DE AFECTACIONES A ESTA PÓLIZA</span></label>
                        <table class="table mb-4">
                            <thead>
                                <tr class="border-top">
                                    <th>Cobertura</th>
                                    <th>Diagnóstico </th>
                                    <th>Porcentaje</th>
                                    <th>Fecha desembolso</th>
                                    <th>Valor Asegurado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reclamaciones as $rec)
                                    <tr>
                                        <td>{{ $rec->cobertura->nombre }}</td>
                                        <td class="text-wrap">{{ $rec->diagnostico->diagnostico ?? $rec->otro }}</td>
                                        <td>{{ $rec->cobertura->porcentajeReclamacion }} % </td>
                                        <td>{{ $rec->fecha_desembolso }}</td>
                                        <td>${{ number_format($rec->valor_asegurado) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                <label class="form-label"><span class="text-danger">CREAR UN PROCESO DE RECLAMACIÓN</span></span></label>
                <form method="post" action="{{ route('seguros.reclamacion.store') }}" class="row"
                    id="formAddReclamacion" novalidate>
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <label class="form-label">Cobertura<span class="text-danger">*</span></label>
                            <select name="cobertura_id" class="form-control" id="selectCobertura">
                                @foreach ($poliza->plan->coberturas as $cobertura)
                                    <option value="{{ $cobertura->id }}" data-porcentaje="{{ $cobertura->porcentajeReclamacion }}">{{ $cobertura->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <label class="form-label">Diagnóstico <span class="text-danger">*</span></label>
                            <select name="diagnostico_id" class="form-control">
                                <option value="">SELECCIONAR</option>
                                @foreach ($diagnosticos as $d)
                                    <option value="{{ $d->id }}">{{ $d->diagnostico }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <label class="form-label">Otro Diagnóstico</label>
                            <input type="text" class="form-control uppercase-input" name="otrodiagnostico"
                                id="otrodiagnostico">
                            <div class="fs-12 fw-normal text-muted text-truncate-1-line pt-1">
                                <div class="custom-control custom-checkbox" style="display: none;"
                                    id="checkadddiagnostico">
                                    <input type="checkbox" class="form-check-input ml-3" id="checkbox2"
                                        name="adddiagnostico" value=true>
                                    <label class="form-check-label" for="checkbox2">Agregar a la lista de
                                        diagnósticos</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3">
                            <label class="form-label">Valor Asegurado <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="valorAsegurado"
                                    value="{{ $poliza->valor_asegurado }}">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label">Porcentaje <span class="text-danger">*</span></label>
                            <input type="number" maxlength="100" class="form-control" name="porValorAsegurado"
                                value="100" required>
                        </div>
                        <div class="col-lg-3">
                            <label class="form-label">Fecha del siniestro <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fechasiniestro" required>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Estado de la reclamación</label>
                            <select name="estado_id" class="form-control" id="SelectReclamacionEstado">
                                @foreach ($estados as $e)
                                    <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                                @endforeach
                            </select>                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-5">
                            <span class="fs-12 fw-normal text-muted d-block pt-1">
                                El valor asegurado corresponde al total del plan, sobre el cual se aplicará
                                el descuento según el porcentaje indicado
                            </span>
                        </div>
                        <div class="col-lg-3"></div>
                        <div class="col-lg-4 nav-link mt-1" id="checkconfirmarvalorasegurado">
                            <div class="custom-control custom-checkbox" style="padding-left: 30px;">
                                <input type="checkbox" class="custom-control-input"
                                        name="cambiovalasegurado" checked>
                                <label class="custom-control-label c-pointer">Modificar el valor asegurado</label>
                            </div>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-end">
                        <div class="col-lg-4" id="divFechaDesembolso">
                            <label class="form-label">Fecha del desembolso <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fechadesembolso">
                            <div class="nav-link mt-1" id="checkconfirmarfinrec">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input"
                                        name="finreclamacion">
                                    <label class="custom-control-label c-pointer">Marcar como cierre de
                                        la reclamación</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-lg-3 mb-4">
                            <label class="form-label">Cédula contacto<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="cedulacontacto" required>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <label class="form-label">Nombre contacto<span class="text-danger">*</span></label>
                            <input type="text" name="nombrecontacto" class="form-control uppercase-input"
                                required>
                        </div>
                        <div class="col-lg-3 mb-4">
                            <label class="form-label">Parentesco <span class="text-danger">*</span></label>
                            <select name="parentesco_id" class="form-control">
                                <option value="TITULAR">TITULAR</option>
                                @foreach ($parentescos as $p)
                                    <option value="{{ $p['code'] }}">{{ $p['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <label class="form-label">Telefono contacto<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="telcontacto" required>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <label class="form-label">Hora de contacto<span class="text-danger">*</span></label>
                            <input type="time" id="horaActual" name="horacontacto" class="form-control" required>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <label class="form-label">Fecha de contacto<span class="text-danger">*</span></label>
                            <input type="date" id="fechaActual" name="fechacontacto" class="form-control"
                                required>
                        </div>
                    </div>
                    <div class="col-lg-12 mb-4">
                        <label class="form-label">Beneficiario <span class="text-danger">*</span></label>
                        <select name="beneficiario_id" class="form-control">
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="col-lg-12 mb-4">
                        <label class="form-label">Observaciones</label>
                        <input type="text" class="form-control uppercase-input" name="observacion" required>
                        <input type="text" value="{{ $poliza->id }}" name="poliza_id" hidden>
                        <input type="hidden" name="asegurado" value="{{ $asegurado->cedula }}">
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button class="btn btn-success" title="Prestar servicio" type="submit">
                            <i class="feather-plus me-2"></i>
                            <span>Agregar Reclamación</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            var fechaActual = new Date();
            var fecha = fechaActual.toISOString().split('T')[0];
            var hora = fechaActual.toTimeString().split(' ')[0].slice(0, 5);
            $('#fechaActual').val(fecha);
            $('#horaActual').val(hora);

            $('#formAddReclamacion').submit(function(event) {
                var form = this;
                if (!form.checkValidity()) {
                    $(form).addClass('was-validated');
                    event.preventDefault();
                    event.stopPropagation();
                    $(form).find(':invalid').each(function() {
                        console.warn('Campo inválido:', this.name || this.id, this);
                    });
                } else {
                    console.log($(this).serialize());
                }
            });

            $('#selectCobertura').on('change', function () {
            var porcentaje = $(this).find(':selected').data('porcentaje');
                $('input[name="porValorAsegurado"]').val(porcentaje ?? '');
            });
            $('#selectCobertura').trigger('change');

            function toggleDiv() {
                if ($('#SelectReclamacionEstado').val() === '4') {
                    $('#divFechaDesembolso').show();
                    $('#checkconfirmarvalorasegurado').show();                    
                    $('#divFechaDesembolso input[name="fechadesembolso"]').attr('required', true);
                    $('input[name="finreclamacion"]').prop('checked', true);
                } else {
                    $('#divFechaDesembolso').hide();
                    $('#checkconfirmarvalorasegurado').hide();  
                    $('#divFechaDesembolso input[name="fechadesembolso"]').removeAttr('required');
                    $('input[name="finreclamacion"]').prop('checked', false);
                }
            }
            toggleDiv();

            $('#SelectReclamacionEstado').on('change', toggleDiv);
            $('#otrodiagnostico').on('input', function() {
                var valor = $(this).val().trim();
                if (valor !== "") {
                    $('#checkadddiagnostico').slideDown();
                } else {
                    $('#checkadddiagnostico').slideUp();
                    $('#checkbox2').prop('checked', false);
                }
            });
        });
    </script>
</x-base-layout>
