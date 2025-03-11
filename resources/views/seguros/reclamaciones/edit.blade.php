<x-base-layout>
    @section('titlepage', 'Editar Reclamación')
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
    <form action="{{ route('seguros.reclamacion.update', ['reclamacion' => $reclamacion->id]) }}" method="POST" id="formUpdateReclamacion" novalidate>
        @csrf
        @method('PUT')
        <div class="col-12">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <label class="form-label">Cédula Asegurado</label>
                            <input type="text" class="form-control" name="asegurado" value="{{ $asegurado->cedula }}" readonly>
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
                            <input class="form-control datepicker-input" value="" readonly>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Cobertura<span class="text-danger">*</span></label>
                        <select name="cobertura_id" class="form-control">
                            @foreach ($poliza->plan->coberturas as $cobertura)
                                <option value="{{ $cobertura->id }}" 
                                @if ($cobertura->id == $reclamacion->idCobertura) selected @endif>
                                {{ $cobertura->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-lg-7 mb-4">
                            <label class="form-label">Diagnóstico <span class="text-danger">*</span></label>
                            <select name="diagnostico_id" class="form-control">
                                <option value="1">opcion 1</option>
                                <option value="2">opcion 2</option>
                            </select>
                        </div>
                        <div class="col-lg-5 mb-4">
                            <label class="form-label">Otro</label>
                            <input type="text" class="form-control" name="otrodiagnostico" value="{{ $reclamacion->otro }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <label class="form-label">Fecha del siniestro <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fechasiniestro" value="{{$reclamacion->fechaSiniestro}}" readonly>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <label class="form-label">Estado de la reclamación</label>
                            <select name="estado_id" class="form-control">
                                @foreach ($estados as $e)
                                    <option value="{{ $e->id }}"
                                    @if ($e->id == $reclamacion->estado) selected @endif>{{ $e->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12 mb-4">
                        <label class="form-label">Observaciones</label>
                        <input type="text" class="form-control uppercase-input" name="observacion" required>                        
                        <input type="text" value="{{ $poliza->id }}" name="poliza_id" hidden>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button class="btn btn-warning" title="Prestar servicio" type="submit">
                            <i class="feather-plus me-2"></i>
                            <span>Actualiza Cambios</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <script>
        $('#formUpdateReclamacion').submit(function(event) {
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