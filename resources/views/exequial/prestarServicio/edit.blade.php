<x-base-layout>
    @section('titlepage', 'Editar')
    @php
        $tipos = [
            1 => ['color' => 'primary', 'text' => 'Notificacion a C.F'],
            2 => ['color' => 'teal', 'text' => 'Corpentunida'],
            3 => ['color' => 'warning', 'text' => 'Coorserpark'],
            4 => ['color' => 'success', 'text' => 'Fin Servicio'],
        ];
    @endphp
    <x-success />
    <x-error />
    <form method="POST" action="{{ route('exequial.prestarServicio.update', $registro->id) }}" class="row"
        id="formupdateprestarservicio" novalidate>
        @csrf
        @method('PUT')
        <div class="col-xl-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="fw-bold">PRESTAR SERVICIO:</h5>
                        <div class="fs-12 text-muted"><span class="fs-13 fw-semibold">Fecha de registro:
                            </span>{{ $registro->fechaRegistro }}</div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Fallecido</label>
                        <div class="row">
                            <div class="col-lg-4">
                                <input type="text" class="form-control" value="{{ $registro->cedulaFallecido }}"
                                    name="cedulaFallecido" readonly>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control"
                                    value="{{ $registro->nombreFallecido }}"readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <label class="form-label">Fecha Fallecimiento<span class="text-danger">*</span></label>
                            <input type="date" class="form-control datepicker-input" name="fechaFallecimiento"
                                value="{{ $registro->fechaFallecimiento }}" required>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <label class="form-label">Hora Fallecimiento<span class="text-danger">*</span></label>
                            <input type="time" class="form-control datepicker-input" name="horaFallecimiento"
                                value="{{ $registro->horaFallecimiento }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <label class="form-label">Region<span class="text-danger">*</span></label>
                            <select id="region" class="form-select" required>
                                <option value="">Seleccione región</option>
                                @foreach ($regiones as $region)
                                    <option value="{{ $region->id }}"
                                        {{ optional($regionsel)->id == $region->id ? 'selected' : '' }}>{{ $region->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <label class="form-label">Departamento<span class="text-danger">*</span></label>
                            <select id="departamento" class="form-select" disabled required>                                
                                <option value="">Seleccione departamento</option>
                            </select>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <label class="form-label">Municipio <span class="text-danger">*</span></label>
                            <select id="municipio" name="municipioid" class="form-select" disabled required>
                                <option value="">Seleccione municipio</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Lugar Fallecimiento<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="lugarFallecimiento"
                            value="{{ $registro->lugarFallecimiento }}" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label">Titular</label>
                        <input type="text" class="form-control"
                            value="{{ $registro->cedulaTitular }} - {{ $registro->nombreTitular }}" readonly>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Parentesco</label>
                        <input type="text" class="form-control" value="{{ $registro->parentesco }}" readonly>
                    </div>
                    <div class="row">
                        <div class="col-lg-7 mb-4">
                            <label class="form-label">Contacto 1 <span class="text-danger">*</span></label>
                            <input class="form-control datepicker-input" name="contacto"
                                value="{{ $registro->contacto }}">
                        </div>
                        <div class="col-lg-5 mb-4">
                            <label class="form-label">Telefono 1 <span class="text-danger">*</span></label>
                            <input type="" class="form-control datepicker-input"
                                value="{{ $registro->telefonoContacto }}" name="telefonoContacto">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-7 mb-4">
                            <label class="form-label">Contacto 2 <span class="text-danger">*</span></label>
                            <input class="form-control datepicker-input" name="contacto2"
                                value="{{ $registro->Contacto2 }}">
                        </div>
                        <div class="col-lg-5 mb-4">
                            <label class="form-label">Telefono 2 <span class="text-danger">*</span></label>
                            <input type="" class="form-control datepicker-input" name="telefonoContacto2"
                                value="{{ $registro->telefonoContacto2 }}">
                        </div>
                    </div>
                    @candirect('exequial.prestarServicio.update')
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button class="btn btn-warning" title="Prestar servicio" type="submit">
                            <i class="bi bi-pencil-square me-2"></i>
                            <span>Actualizar datos</span>
                        </button>
                    </div>
                    @endcandirect
                </div>
            </div>
        </div>
        <div class="col-xl-12">
            <div class="accordion proposal-faq-accordion mt-4">
                <div class="accordion-item mt-2">
                    <h2 class="accordion-header" id="flush-headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#flush-collapseOne" aria-expanded="false"
                            aria-controls="flush-collapseOne">Actividad sobre el servicio</button>
                    </h2>
                    <div id="flush-collapseOne" class="accordion-collapse collapse"
                        aria-labelledby="flush-headingOne" data-bs-parent="#accordionFaqGroup" style="">
                        <div class="accordion-body">
                            @if (!$registro->comments->isEmpty())
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr class="border-top">
                                                <th>Fecha </th>
                                                <th>Tipo </th>
                                                <th>Observación </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($registro->comments as $comment)
                                                <tr>
                                                    <td>{{ $comment->fecha }}</td>
                                                    <td>
                                                        @php
                                                            $t = $tipos[$comment->tipo] ?? [
                                                                'color' => 'bg-secondary',
                                                                'text' => 'Desconocido',
                                                            ];
                                                        @endphp
                                                        <span
                                                            class="badge bg-soft-{{ $t['color'] }} text-{{ $t['color'] }}">{{ $t['text'] }}</span>
                                                    </td>
                                                    <td class="text-wrap">{{ $comment->observacion }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            <button type="button" id="btnCardComentarios" class="btn btn-primary mt-3"
                                title="Agregar Comentario">
                                <i class="feather-plus me-2"></i>
                                <span>Agregar comentario</span>
                                </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div id="CardAddComentarios" class="col-lg-12" style="display: none;">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="fw-bold mb-0">
                    <span class="d-block mb-2">Crear Comentario </span>
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('prestarServicio.comentario.store', $registro->id) }}"
                    id="formAddComentarios" novalidate>
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="col-lg-3 mb-4">
                            <label class="form-label">Tipo de comentario<span class="text-danger">*</span></label>
                            <select class="form-select" name="tipocomment" required>
                                <option value="1">Notificacion a C.F</option>
                                <option value="2">Corpentunida</option>
                                <option value="3">Coorserpark</option>
                                <option value="4">Fin Servicio</option>
                            </select>
                        </div>
                        <div class="col-lg-9 mb-4">
                            <label class="form-label">Observacion<span class="text-danger">*</span></label>
                            <textarea type="text" class="form-control" name="observacioncomment"required></textarea>
                        </div>
                    </div>
                    <div class="d-flex flex-row-reverse gap-2 mt-2">
                        <button class="btn btn-success mt-4" data-bs-toggle="tooltip" type="submit"
                            data-bs-original-title="crear">
                            <i class="feather-plus me-2"></i>
                            <span>Agregar comentario</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $('#formupdateprestarservicio').submit(function(event) {
            var form = this;
            if (!form.checkValidity()) {
                $(form).addClass('was-validated');
                event.preventDefault();
                event.stopPropagation();
            } else {
                console.log($(this).serialize());
            }
        });
        $('#btnCardComentarios').on('click', function() {
            console.log('edit prestar servicio loaded');
            $('#CardAddComentarios').show();
            $('#CardAddComentarios').slideDown('fast', function() {
                $('html, body').animate({
                    scrollTop: $('#CardAddComentarios').offset().top
                }, 500);
            });
        });

        $(document).ready(function() {
            const urlDepartamentos = "{{ route('maestras.departamentos.listar', ':id') }}";
            const urlMunicipios = "{{ route('maestras.municipios.listar', ':id') }}";

            const regionSeleccionada = "{{ $regionsel->id ?? '' }}";
            const departamentoSeleccionado = "{{ $departamento->codigo_Dane ?? '' }}";
            const municipioSeleccionado = "{{ $municipio->id ?? '' }}";

            function cargarDepartamentos(regionId, departamentoPreseleccionado = null) {

                if (!regionId) return;

                $('#departamento')
                    .prop('disabled', true)
                    .html('<option>Cargando...</option>');

                $('#municipio')
                    .prop('disabled', true)
                    .html('<option></option>');

                const url = urlDepartamentos.replace(':id', regionId);

                $.get(url, function(data) {
                    let options = '<option value=""></option>';

                    data.forEach(dep => {
                        const selected = dep.codigo_Dane == departamentoPreseleccionado ?
                            'selected' : '';
                        options +=
                            `<option value="${dep.codigo_Dane}" ${selected}>${dep.nombre}</option>`;
                    });

                    $('#departamento').html(options).prop('disabled', false);

                    
                    if (departamentoPreseleccionado) {
                        cargarMunicipios(departamentoPreseleccionado, municipioSeleccionado);
                    }
                });
            }


            function cargarMunicipios(departamentoId, municipioPreseleccionado = null) {

                if (!departamentoId) return;

                $('#municipio')
                    .prop('disabled', true)
                    .html('<option>Cargando...</option>');

                const url = urlMunicipios.replace(':id', departamentoId);

                $.get(url, function(data) {
                    let options = '<option value=""></option>';

                    data.forEach(mun => {
                        const selected = mun.id == municipioPreseleccionado ? 'selected' : '';
                        options += `<option value="${mun.id}" ${selected}>${mun.nombre}</option>`;
                    });

                    $('#municipio').html(options).prop('disabled', false);
                });
            }

            $('#region').on('change', function() {
                cargarDepartamentos($(this).val());
            });

            $('#departamento').on('change', function() {
                cargarMunicipios($(this).val());
            });

            if (regionSeleccionada) {
                $('#region').val(regionSeleccionada);
                cargarDepartamentos(regionSeleccionada, departamentoSeleccionado);
            }
        });
    </script>
</x-base-layout>
