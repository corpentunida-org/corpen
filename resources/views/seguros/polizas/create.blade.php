<x-base-layout>
    <x-error />
    <div class="col-xxl-12 col-xl-12">
        <div class="card border-top-0">
            <div class="card-header p-0">
                <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#securityTab"
                            aria-selected="true">Crear una Poliza</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 active show" id="securityTab" role="tabpanel">
                    <form action={{ route('seguros.poliza.store') }} method="POST" id="formAddPoliza"
                        class="col-lg-12 p-4" novalidate>
                        @csrf
                        @method('POST')
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <h5 class="fw-bold mb-0 me-4">
                                <span class="d-block mb-2">Tercero: </span>
                            </h5>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 mb-4">
                                <label class="form-label">Cédula<span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="tercedula" required>
                            </div>
                            <div class="col-lg-8 mb-4">
                                <label class="form-label">Nombre<span class="text-danger">*</span></label>
                                <input type="text" class="form-control text-uppercase" name="ternombre" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 mb-4">
                                <label class="form-label">Fecha Nacimiento<span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="fechaNacimiento" id="fechaNacimiento"
                                    required>
                            </div>
                            <div class="col-lg-3 mb-4">
                                <label class="form-label">Género<span class="text-danger">*</span></label>
                                <select name="tergenero" class="form-control">
                                    <option value="V">Masculino</option>
                                    <option value="H">Femenino</option>
                                </select>
                            </div>
                            <div class="col-lg-3 mb-4">
                                <label class="form-label">Teléfono<span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="tertelefono">
                            </div>
                            <div class="col-lg-3 mb-4">
                                <label class="form-label">Distrito<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="terdistrito">
                            </div>
                        </div>
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <h5 class="fw-bold mb-0 me-4">
                                <span class="d-block mb-2">Póliza: </span>
                            </h5>
                        </div>
                        <div class="row">
                            <div class="col-lg-2 mb-4">
                                <label class="form-label">Edad</label>
                                <input type="text" class="form-control" id="edad" name="edad" disabled>
                            </div>
                            <div class="col-lg-10 mb-4">
                                <label class="form-label">Plan<span class="text-danger">*</span></label>
                                <select name="selectPlanes" class="form-control" id="selectPlanes" required>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 mb-4">
                                <label class="form-label">Porcentaje Extraprima</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="extra_prima" value="0">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-4">
                                <label class="form-label">Descuento Valor</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="desval" value="0">
                                </div>
                            </div>
                            <div class="col-lg-4 mb-4">
                                <label class="form-label">Descuento Porcentaje</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="despor" value="0">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <h5 class="fw-bold mb-0 me-4">
                                <span class="d-block mb-2">Asegurado: </span>
                            </h5>
                        </div>
                        <div class="nav-link mb-4" id="checkchangevalaseg">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkbox1" name="estitular"
                                    value="1" checked>
                                <label class="custom-control-label c-pointer" for="checkbox1">El registro que esta
                                    creando es un TITULAR</label>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-4" id="divestitular">
                            <label class="form-label">Valor a pagar<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-text">$</div>
                                <input class="form-control" type="text" name="valorpagaraseguradora" required>
                            </div>
                        </div>
                        <div class="row" id="divnottitular">
                            <div class="col-lg-3 mb-4">
                                <label class="form-label">Titular Asociado<span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="titularasegurado" required>
                            </div>
                            <div class="col-lg-3 mb-4">
                                <label class="form-label">Parentesco<span class="text-danger">*</span></label>
                                <select name="parentesco" class="form-control">
                                    <option value="CO">CONYUGUE</option>
                                    <option value="HI">HERMANO</option>
                                    <option value="HI">HIJO</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex flex-row-reverse gap-2 mt-2">
                            <button type="submit" class="btn btn-success mt-4">
                                <i class="feather-plus me-2"></i>
                                <span>Crear Poliza</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            function toggleContenedor() {
                if ($('#checkbox1').is(':checked')) {
                    $('#divnottitular').hide();
                    $('#divestitular').show();
                    $('[name="titularasegurado"]').prop('required', false);
                } else {
                    $('#divnottitular').show();
                    $('#divestitular').hide();
                    $('[name="valorpagaraseguradora"]').prop('required', false);
                }
            }
            $('#checkbox1').change(function() {
                toggleContenedor();
            });
            toggleContenedor();


            $('#fechaNacimiento').on('change', function() {
                const fechaNac = new Date(this.value);
                const hoy = new Date();

                if (isNaN(fechaNac)) {
                    $('#edad').val('');
                    return;
                }
                let edad = hoy.getFullYear() - fechaNac.getFullYear();
                const cumpleEsteAño = hoy.getMonth() > fechaNac.getMonth() ||
                    (hoy.getMonth() === fechaNac.getMonth() && hoy.getDate() >= fechaNac.getDate());
                if (!cumpleEsteAño) {
                    edad--;
                }

                if (edad >= 0) {
                    $('#edad').val(edad);
                    $.ajax({
                        url: "{{ route('seguros.planes.getplanes', ['edad' => '__EDAD__']) }}"
                            .replace('__EDAD__', edad),
                        type: 'GET',
                        success: function(planes) {
                            const $select = $('#selectPlanes');
                            $select.empty().append(
                                '<option value="">Seleccione un plan</option>');
                            planes.forEach(function(plan) {
                                $select.append(
                                    `<option value="${plan.id}">${plan.convenio.nombre} - ${plan.name} - $${plan.valor.toLocaleString('es-CL')} - $${plan.prima.toLocaleString('es-CL')}</option>`
                                );
                            });
                        },
                        error: function() {
                            alert('No se pudieron cargar los planes.');
                        }
                    });
                } else {
                    $('#edad').val('');
                }
            });

            $('#formAddPoliza').submit(function(event) {
                var form = this;
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    $(form).addClass('was-validated');
                }
            });
        });
    </script>
</x-base-layout>
