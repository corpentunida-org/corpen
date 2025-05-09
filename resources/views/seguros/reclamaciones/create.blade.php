<x-base-layout>
    @section('titlepage', 'Crear Reclamación')
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
                                    class="counter">{{ $asegurado->terceroAF->nombre }}</span></div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">{{ $asegurado->terceroAF->cedula }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 d-flex justify-content-end align-items-center">
                    <x-input-search-seguros></x-input-search-seguros>
                </div>
            </div>
        </div>
    </div>
    <form method="post" action="{{ route('seguros.reclamacion.store') }}" class="row" id="formAddReclamacion"
        novalidate>
        @csrf
        @method('POST')
        <div class="col-xl-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <label class="form-label">Cédula Asegurado</label>
                            <input type="text" class="form-control" name="asegurado" value="{{ $asegurado->cedula }}"
                                readonly>
                        </div>
                        <div class="col-lg-8 mb-4">
                            <label class="form-label">Nombre Asegurado</label>
                            <input type="text" class="form-control" value="{{ $asegurado->tercero->nombre }}"
                                readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <label class="form-label">Tipo de Afiliado<span class="text-danger">*</span></label>
                            <input class="form-control datepicker-input" name="contacto"
                                value="{{ $asegurado->parentesco }}" readonly>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <label class="form-label">Genero <span class="text-danger">*</span></label>
                            <input class="form-control datepicker-input"
                                value="{{ $asegurado->tercero->genero == 'V' ? 'Masculino' : 'Femenino' }}" readonly>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <label class="form-label">Distrito <span class="text-danger">*</span></label>
                            <input class="form-control datepicker-input" value="{{ $asegurado->tercero->distrito }}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-7 mb-4">
                            <label class="form-label">Cobertura<span class="text-danger">*</span></label>
                            <select name="cobertura_id" class="form-control">
                                @foreach ($poliza->plan->coberturas as $cobertura)
                                    <option value="{{ $cobertura->id }}">{{ $cobertura->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-5 mb-4">
                            <label class="form-label">Valor Asegurado a reclamar<span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="valorAsegurado"
                                    value="{{ $poliza->plan->valor }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-7 mb-4">
                            <label class="form-label">Diagnóstico <span class="text-danger">*</span></label>
                            <select name="diagnostico_id" class="form-control">
                                @foreach ($diagnosticos as $d)
                                    <option value="{{ $d->id }}">{{ $d->diagnostico }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-5 mb-4">
                            <label class="form-label">Otro</label>
                            <input type="text" class="form-control" name="otrodiagnostico">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <label class="form-label">Fecha del siniestro <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fechasiniestro" required>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <label class="form-label">Estado de la reclamación</label>
                            <select name="estado_id" class="form-control" id="SelectReclamacionEstado">
                                @foreach ($estados as $e)
                                    <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4" id="divFechaDesembolso">
                        <label class="form-label">Fecha del desembolso <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="fechadesembolso" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <label class="form-label">Cédula contacto</label>
                            <input type="number" class="form-control" name="cedulacontacto" required>
                        </div>
                        <div class="col-lg-8 mb-4">
                            <label class="form-label">Nombre contacto</label>
                            <input type="text" name="nombrecontacto" class="form-control uppercase-input"
                                required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-7 mb-4">
                            <label class="form-label">Parentesco <span class="text-danger">*</span></label>
                            <select name="parentesco_id" class="form-control">
                                <option value="TITULAR">TITULAR</option>
                                @foreach ($parentescos as $p)
                                    <option value="{{ $p['code'] }}">{{ $p['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-5 mb-4">
                            <label class="form-label">Telefono contacto<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="telcontacto" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <label class="form-label">Hora de contacto<span class="text-danger">*</span></label>
                            <input type="time" id="horaActual" name="horacontacto" class="form-control" required>
                        </div>
                        <div class="col-lg-6 mb-4">
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
                        <input type="text" class="form-control uppercase-input" name="observacion">
                        <input type="text" value="{{ $poliza->id }}" name="poliza_id" hidden>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button class="btn btn-success" title="Prestar servicio" type="submit">
                            <i class="feather-plus me-2"></i>
                            <span>Agregar Reclamación</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
                } else {
                    console.log($(this).serialize());
                }
            });

            function toggleDiv() {               
                if ($('#SelectReclamacionEstado').val() === '4') {
                    $('#divFechaDesembolso').show();
                } else {
                    $('#divFechaDesembolso').hide();
                }
            }

            toggleDiv();

            $('#SelectReclamacionEstado').on('change', toggleDiv);
        
        });
    </script>
</x-base-layout>
