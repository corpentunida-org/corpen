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
    <form method="POST" action="{{ route('exequial.prestarServicio.update', $registro->id) }}" class="row"
        id="formupdateprestarservicio" novalidate>
        @csrf
        @method('PUT')
        <div class="col-xl-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="fw-bold">PRESTAR SERVICIO:</h5>
                        <div class="fs-12 text-muted">{{ $registro->fechaRegistro }}</div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Fallecido</label>
                        <div class="row">
                            <div class="col-lg-4">
                                <input type="text" class="form-control" value="{{ $registro->cedulaFallecido }}"
                                    name="cedulaFallecido" readonly>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" value="{{ $registro->nombreFallecido }}"
                                    readonly>
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
                    <div class="mb-4">
                        <label class="form-label">Lugar Fallecimiento<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="lugarFallecimiento"
                            value="{{ $registro->lugarFallecimiento }}" required>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <label class="form-label">Traslado <span class="text-danger">*</span></label>
                            <select class="form-control" name="traslado">
                                <option value="1" {{ $registro->traslado ? 'selected' : '' }}>Sí</option>
                                <option value="0" {{ !$registro->traslado ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <label class="form-label">Factura <span class="text-danger">*</span></label>
                            <input class="form-control datepicker-input" name="factura"
                                value="{{ $registro->factura }}">
                        </div>
                        <div class="col-lg-4 mb-4">
                            <label class="form-label">Valor <span class="text-danger">*</span></label>
                            <input class="form-control datepicker-input" name="valor" value="{{ $registro->valor }}">
                        </div>
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
                            <i class="feather-plus me-2"></i>
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
                                                        $t = $tipos[$comment->tipo_id] ?? [
                                                            'color' => 'bg-secondary',
                                                            'texto' => 'Desconocido',
                                                        ];
                                                    @endphp
                                                    <span
                                                        class="badge bg-soft-{{ $t['color'] }} text-{{ $t['color'] }}" >{{ $t['texto'] }}</span>
                                                </td>
                                                <td><span class="badge bg-soft-warning text-warning"></span>
                                                </td>
                                                <td></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
    </script>
</x-base-layout>
