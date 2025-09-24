<x-base-layout>
    @section('titlepage', 'Novedad')
    <x-error />
    @php
        $activeTab = $activeTab ?? 'generalTab';
    @endphp
    @php
        $activeTab = $activeTab ?? 'generalTab';
    @endphp
    <div class="col-xxl-12 col-xl-12">
        <div class="card border-top-0">
            <div class="card-header p-0">
                <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a class="nav-link {{ $activeTab == 'generalTab' ? 'active' : '' }}" data-bs-toggle="tab"
                            data-bs-target="#securityTab" aria-selected="true">MODIFICACIÓN</a>
                    </li>
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" data-bs-target="#TaskTab"
                            aria-selected="true">INGRESO</a>
                    </li>
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a class="nav-link {{ $activeTab == 'projectTab' ? 'active' : '' }}" data-bs-toggle="tab"
                            data-bs-target="#projectTab" aria-selected="true">RETIRO</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 {{ $activeTab == 'generalTab' ? 'show active' : '' }}" id="securityTab"
                    role="tabpanel">
                    <div class="col-lg-12 p-4">
                        <div class="row">
                            <form action="{{ route('seguros.novedades.show', ['novedade' => 'ID']) }}"
                                class="col-lg-2 mb-4">
                                <label class="form-label">Cédula Buscar</label>
                                <input type="text" class="form-control" name="a">
                            </form>
                            <div class="col-lg-3 mb-4">
                                <label class="form-label">Cedula Asegurado</label>
                                <input type="text" class="form-control" value="{{ $asegurado->cedula ?? ' ' }}"
                                    disabled>
                            </div>
                            <div class="col-lg-7 mb-4">
                                <label class="form-label">Nombre Asegurado</label>
                                <input type="text" class="form-control"
                                    value="{{ $asegurado->nombre_titular ?? ' ' }}" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 mb-4">
                                <label class="form-label">Plan</label>
                                <input type="text" class="form-control uppercase-input"
                                    value="{{ @$asegurado?->polizas?->first()?->plan?->name ?? '' }}" disabled>
                            </div>
                            <div class="col-lg-4 mb-4">
                                <label class="form-label">Valor Asegurado</label>
                                <input type="text" id="valoraseguradoplan" name="valorAsegurado" class="form-control"
                                    value="$ {{ number_format(@$asegurado?->polizas?->first()?->valor_asegurado ?? 0) }}"
                                    disabled>
                            </div>
                            <div class="col-lg-4 mb-4">
                                <label class="form-label">Valor a Pagar Mensualidad</label>
                                <input type="text" id="primaplan" class="form-control" name="valorPrima"
                                    value="$ {{ number_format(@$asegurado?->polizas?->first()?->primapagar ?? 0) }}"
                                    disabled>
                            </div>
                        </div>
                        @if (isset($asegurado))
                            <form method="POST" action="{{ route('seguros.novedades.store') }}" id="formAddNovedad"
                                novalidate>
                                @csrf
                                @method('POST')
                                <div class="row">
                                    <div class="col-lg-6 mb-4">
                                        <input type="hidden" name="asegurado" value="{{ $asegurado->cedula }}">
                                        <input type="hidden" name="tipoNovedad" value="1">
                                        <label class="form-label">Asignar Plan<span class="text-danger">*</span></label>
                                        <select class="form-control" name="planid" id="selectPlan">
                                            @foreach ($planes as $plan)
                                                <option value="{{ $plan->id }}" data-valor="{{ $plan->valor }}"
                                                    data-prima="{{ $plan->prima_aseguradora }}"
                                                    {{ $asegurado->polizas->first()->plan->id == $plan->id ? 'selected' : '' }}>
                                                    {{ $plan->convenio->nombre ?? '' }} - {{ $plan->name }} -
                                                    ${{ number_format($plan->valor) }} -
                                                    ${{ number_format($plan->prima_aseguradora) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span
                                            class="fs-12 fw-normal text-muted text-truncate-1-line pt-1">{{ $condicion->descripcion }}</span>
                                    </div>
                                    <div class="col-lg-2 mb-4">
                                        <label class="form-label">Porcentaje Extra Prima<span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="inputExtraPrima"
                                                name="extra_prima"
                                                value="{{ $asegurado->polizas->first()->extra_prima }}" min=0
                                                max="100">
                                            <span class="input-group-text">%</span>
                                            <input type="hidden" id="valorprimaaumentar" name="valorprimaaumentar">
                                        </div>
                                        <div class="fs-12 fw-normal text-muted text-truncate-1-line text-wrap pt-1">
                                            <div class="custom-control custom-checkbox" id="checkextraprimaval"
                                                style="display: none;">
                                                <input type="checkbox" class="form-check-input ml-3" id="checkbox2"
                                                    name="checkconfirmarextra" value=true>
                                                <label class="form-check-label text-wrap" for="checkbox2"
                                                    id="labeltextextra"
                                                    style="white-space: normal; word-wrap: break-word; max-width: 100%;"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-4">
                                        <label class="form-label">Valor a Pagar<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="primacorpen"
                                            value="{{ $asegurado->polizas->first()->primapagar ?? 0 }}"
                                            id="valorapagarcorpen">
                                    </div>
                                </div>
                                <div class="table-responsive mt-4">
                                    <label class="form-label">Grupo Familiar</span></label>
                                    <table class="table mb-0">
                                        <thead>
                                            <tr class="border-top">
                                                <th>Cedula</th>
                                                <th>Nombre</th>
                                                <th>Parentesco</th>
                                                <th>Plan</th>
                                                <th class="text-center">Valor Asegurado</th>
                                                <th class="text-end">Prima Plan</th>
                                                <th class="text-end">Valor a Pagar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($grupoFamiliar as $gf)
                                                <tr>
                                                    <td><a
                                                            href="{{ route('seguros.poliza.show', ['poliza' => 'ID']) . '?id=' . $gf->cedula }}">{{ $gf->cedula }}</a>
                                                    </td>
                                                    <td>{{ $gf->nombre_tercero ?? ' ' }}</td>
                                                    <td>{{ $gf->parentesco }}</td>
                                                    <td><span
                                                            class="badge bg-soft-warning text-warning">{{ $gf->polizas->first()->plan->name }}</span>
                                                    </td>
                                                    <td class="text-center">$
                                                        {{ number_format($gf->polizas->first()->valor_asegurado) }}
                                                    </td>
                                                    <td class="text-end">$
                                                        {{ number_format($gf->polizas->first()->valor_prima) }}</td>
                                                    <td class="text-end">$
                                                        {{ number_format($gf->polizas->first()->primapagar) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="6" class="text-end">Total
                                                    <span class="badge bg-gray-200 text-dark"
                                                        style="font-size: 15px;">$
                                                        {{ number_format($totalPrima) }} </span>
                                                </td>
                                                <td class="text-end">Total
                                                    <span class="badge bg-gray-200 text-dark"
                                                        style="font-size: 15px;">$
                                                        {{ number_format($totalPrimaCorpen) }} </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @if ($asegurado->parentesco == 'AF' || $asegurado->viuda)
                                    <div class="row align-items-center justify-content-end">
                                        <div class="col-lg-9 text-end mt-4">
                                            <label class="form-label">Total Valor a Pagar Titular <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-lg-3 mt-4">
                                            <div class="input-group">
                                                <div class="input-group-text">$</div>
                                                <input class="form-control"
                                                    value="{{ $asegurado->polizas->first()->valorpagaraseguradora }}"
                                                    type="text" name="valorpagaraseguradora">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-lg-2 mb-4">
                                        <label class="form-label">Estado de la Novedad<span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" name="estado">
                                            <option value="1">SOLICITUD</option>
                                            <option value="2">RADICADO</option>
                                            <option value="3">APROBADO</option>
                                            <option value="4">NEGADO</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-10 mb-4">
                                        <label class="form-label">Observación<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control text-uppercase"
                                            name="observaciones" required>
                                        <input type="hidden" name="id_poliza"
                                            value="{{ $asegurado->polizas->first()->id }}">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <label for="ubicacion_foto" class="form-label">Formulario</label>
                                    <input class="form-control @error('ubicacion_foto') is-invalid @enderror"
                                        type="file" id="ubicacion_foto" name="ubicacion_foto" accept="image/*">
                                    @error('ubicacion_foto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Formato permitidos: PDF</small>
                                </div>
                                <div class="d-flex flex-row-reverse gap-2 mt-2">
                                    <button class="btn btn-success mt-4" data-bs-toggle="tooltip" title="Timesheets"
                                        type="submit">
                                        <i class="feather-plus me-2"></i>
                                        <span>Crear Novedad</span>
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="tab-pane fade p-4 " id="TaskTab" role="tabpanel">
                    <form action={{ route('seguros.novedades.store') }} method="POST" id="formAddPoliza" novalidate>
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
                                <input type="number" class="form-control" name="asegurado" required>
                                <input type="hidden" name="tipoNovedad" value="2">
                            </div>
                            <div class="col-lg-8 mb-4">
                                <label class="form-label">Nombre<span class="text-danger">*</span></label>
                                <input type="text" class="form-control text-uppercase" name="ternombre" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 mb-4">
                                <label class="form-label">Fecha Nacimiento<span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="fechaNacimiento"
                                    id="fechaNacimiento" required>
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
                            <div class="col-lg-8 mb-4">
                                <label class="form-label">Plan<span class="text-danger">*</span></label>
                                <select name="planid" class="form-control" id="selectPlanes" required>
                                </select>
                            </div>
                            <div class="col-lg-2 mb-4">
                                <label class="form-label">Porcentaje Extraprima</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="extra_prima"
                                        id="inputextraprima" value="0" required>
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
                            <label class="form-label">Valor Total a Pagar Grupo Familiar<span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-text">$</div>
                                <input class="form-control" type="number" name="valorpagaraseguradora"
                                    id="valorpagaraseguradora" required>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-4">
                            <label class="form-label">Valor a Pagar Individual<span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-text">$</div>
                                <input class="form-control" type="number" name="primacorpen" id="valorindividual"
                                    required>
                                <input type="hidden" id="primaaseguradora">
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
                        <div class="col-lg-12 mb-4">
                            <label class="form-label">Observación<span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="observaciones" required>
                        </div>
                        <div class="d-flex flex-row-reverse gap-2 mt-2">
                            <button type="submit" class="btn btn-success mt-4">
                                <i class="feather-plus me-2"></i>
                                <span>Crear Novedad</span>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade p-4 {{ $activeTab == 'projectTab' ? 'show active' : '' }}" id="projectTab"
                    role="tabpanel">
                    <div class="row p-4">
                        <form action="{{ route('seguros.novedades.show', ['novedade' => 'ID']) }}" class="col-lg-2">
                            <label class="form-label">Cédula Buscar</label>
                            <input type="text" class="form-control" name="a">
                            <input type="hidden" name="activeTab" value="projectTab">
                        </form>
                        <div class="col-lg-3">
                            <label class="form-label">Cedula Asegurado</label>
                            <input type="text" class="form-control" value="{{ $asegurado->cedula ?? ' ' }}"
                                disabled>
                        </div>
                        <div class="col-lg-7">
                            <label class="form-label">Nombre Asegurado</label>
                            <input type="text" class="form-control"
                                value="{{ $asegurado->nombre_titular ?? ' ' }}" disabled>
                        </div>
                    </div>
                    @if (isset($asegurado))
                        <form method="POST" class="px-4"
                            action="{{ route('seguros.novedades.destroy', ['novedade' => $asegurado->cedula]) }}">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="tipoNovedad" value="3">
                            <input type="hidden" name="id_poliza" value="{{ $asegurado->polizas->first()->id }}">
                            <div class="mb-4">
                                <label class="form-label">Plan</label>
                                <select class="form-control" name="planid" readonly>
                                    <option value="{{ $plan->id }}">
                                        {{ $plan->convenio->nombre ?? '' }} - {{ $plan->name }} -
                                        ${{ number_format($plan->valor) }} -
                                        ${{ number_format($plan->prima_aseguradora) }}
                                    </option>
                                </select>                                
                                <span
                                    class="fs-12 fw-normal text-muted text-truncate-1-line pt-1">{{ $condicion->descripcion }}</span>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Observación<span class="text-danger">*</span></label>
                                <input type="text" class="form-control uppercase-input" name="observacionretiro" required>
                            </div>
                            <div class="d-flex flex-row-reverse gap-2 mt-2">
                                <button class="btn btn-danger mt-4" data-bs-toggle="tooltip" title="Timesheets"
                                    type="submit">
                                    <span>Cancelar Poliza</span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#formAddNovedad').submit(function(event) {
            var form = this;
            if (!form.checkValidity()) {
                $(form).addClass('was-validated');
                event.preventDefault();
                event.stopPropagation();
            } else {
                console.log($(this).serialize());
            }
        });

        $('#selectPlan').on('change', function() {
            const selected = $(this).find('option:selected');
            $('#valoraseguradoplan').val("$ " + selected.data('valor'));
            $('#primaplan').val("$ " + selected.data('prima'));
            $('#valorapagarcorpen').val(selected.data('prima'));
        });

        $('#inputExtraPrima').on('input', function() {
            var valor = parseFloat($(this).val().trim());
            var valorOriginal = parseFloat("{{ @$asegurado?->polizas?->first()?->plan?->prima_aseguradora }}");
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
            valorpagar = parseFloat($("#valorapagarcorpen").val()) || 0;
            if ($(this).is(":checked")) {
                valorFinal = valor + valorpagar;
            } else {
                valorFinal = valorpagar - valor;
            }
            $("#valorapagarcorpen").val(valorFinal);
        });

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
                            //console.log(plan);
                            $select.append(
                                `<option value="${plan.id}"
                                        data-as="${plan.prima_asegurado}" 
                                        data-pas="${plan.prima_pastor}" data-base="${plan.prima_aseguradora}">                                        
                                        ${plan.convenio?.nombre ?? 'SIN CONVENIO'} - ${plan.name} - 
                                        $${Number(plan.valor).toLocaleString('es-CO')} - 
                                        $${Number(plan.prima_aseguradora).toLocaleString('es-CO')}
                                    </option>`
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

        function crearbeneficio() {
            const val1 = parseFloat($('#inputbenepor').val());
            const val2 = parseFloat($('#inputbeneval').val());
            if ((val1 > 0 && !isNaN(val1)) || (val2 > 0 && !isNaN(val2))) {
                $('#inputbeneficios').show();
            } else {
                $('#inputbeneficios').hide();
            }
        }
        $('#inputbenepor, #inputbeneval').on('input', crearbeneficio);
        $('#inputbenepor, #inputbeneval').on('input', crearbeneficio);

        function selectValorDeTexto(texto) {
            let matches = texto.match(/\$\d{1,3}(?:\.\d{3})*/g);
            if (matches && matches.length > 0) {
                let ultimoValor = matches[matches.length - 1];
                let numero = parseInt(ultimoValor.replace(/\$/g, '').replace(/\./g, ''));
                return numero;
            }
            return 0;
        }

        function selectvalorapagar() {
            $('#selectPlanes, #checkbox1').on('change', function() {
                let option = $('#selectPlanes').find("option:selected");
                let estaTitular = $('#checkbox1').is(':checked');
                let base = estaTitular ?
                    option.data('pas') :
                    option.data('as');

                $('#valorindividual').val(base);
                console.log(option.data('base'));
                $('#primaaseguradora').val(option.data('base'));
            });
        }
        $('#selectPlanes').on('change', selectvalorapagar);
        $('#checkbox1').on('change', selectvalorapagar);

        selectvalorapagar();

        $('#inputextraprima').on('blur', function() {
            console.log($('#primaaseguradora').val());
            valorBase = parseFloat($('#primaaseguradora').val());
            valormasextra = valorBase * (1 + (parseFloat($(this).val()) / 100))
            $('#valorindividual').val(Math.floor(valormasextra));
        });
    </script>
</x-base-layout>
