<x-base-layout>
    @section('titlepage', 'Editar Plan')
    <x-success />
    <x-error />
    <div class="col-xxl-12 col-xl-12">
        <div class="card border-top-0">
            <div class="card-header p-0">
                <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#securityTab"
                            aria-selected="true">Editar Plan</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 active show" id="securityTab" role="tabpanel">
                    <div class="col-lg-12 p-4">
                        <form id="formEditPlan" method="POST" action="{{ route('seguros.planes.update', ['plan' => $segPlan->id]) }}" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <label class="form-label">Convenio<span class="text-danger">*</span></label>
                                        <select class="form-control" name="convenio" id="convenio_id" required>
                                            @foreach ($convenios as $convenio)
                                                <option value="{{ $convenio->id }}"
                                                    {{ $convenio->idConvenio == $segPlan->seg_convenio_id ? 'selected' : '' }}>
                                                    {{ $convenio->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-5">
                                        <label class="form-label">Nombre Plan<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control uppercase-input" name="name"
                                            value="{{ $segPlan->name }}" required>
                                    </div>
                                    <div class="col-lg-4">
                                        <label class="form-label">Condición<span class="text-danger">*</span></label>
                                        <select class="form-control" name="condicion_id">
                                            <option value="0" selected>Sin Condición</option>
                                            @foreach ($condiciones as $condicion)
                                                <option value="{{ $condicion->id }}"
                                                    {{ $condicion['id'] == $segPlan['condicion_id'] ? 'selected' : '' }}>
                                                    {{ $condicion->descripcion }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label class="form-label">Valor Plan<span class="text-danger">*</span></label>
                                        <input type="number" class="uppercase-input form-control"
                                            name="valorPlanAsegurado" value="{{ $segPlan->valor }}" required>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Total Prima<span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="prima" id="prima"
                                            value="{{ $segPlan->prima }}" required>
                                        <div class="invalid-feedback">
                                            La suma de las coberturas debe dar el valor total de la prima.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="coberturas-container">
                                @foreach ($segPlan->coberturas as $cobertura)
                                    <div class="mb-4 cobertura-row">
                                        <div class="row">
                                            <div class="col-lg-5">
                                                <label class="form-label">Cobertura <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="cobertura[]"
                                                    value="{{ $cobertura->nombre }}">
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="form-label">Valor Asegurado<span
                                                        class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="valorAsegurado[]"
                                                    value="{{ $cobertura->pivot->valorAsegurado }}" >
                                            </div>
                                            <div class="col-lg-2">
                                                <label class="form-label">Valor Cobertura<span
                                                        class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="valorPrima[]"
                                                    value="{{ $cobertura->pivot->valorCobertura }}">
                                            </div>
                                            <div class="col-lg-2">
                                                <label class="form-label">Extraprima<span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" name="desextraprima[]" value="{{$cobertura->pivot->extra}}">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="cobertura-row d-none mb-4" id="cobertura-template">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label class="form-label">Cobertura <span class="text-danger">*</span></label>
                                        <select class="form-control" name="cobertura[]">
                                            @foreach ($coberturas as $c)
                                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="form-label">Valor Asegurado<span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="valorAsegurado[]">
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="form-label">Valor Cobertura<span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="valorPrima[]">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <a class="btn btn-primary wd-200" id="add-cobertura">
                                    <i class="feather-plus me-2"></i>
                                    <span>Agregar cobertura</span>
                                </a>
                            </div>
                            {{-- hidden --}}
                            <div class="d-flex flex-row-reverse gap-2 mt-2">
                                <button type="submit" class="btn btn-warning">Actualizar Plan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#add-cobertura').click(function() {
                const template = document.getElementById('cobertura-template');
                const container = document.querySelector('.coberturas-container');
                const cloned = template.cloneNode(true);
                cloned.classList.remove('d-none');
                cloned.removeAttribute('id');
                cloned.querySelectorAll('input').forEach(input => input.value = '');
                container.appendChild(cloned);
            });
            $('#formEditPlan').submit(function(event) {
                console.log('Form submitted');
                var form = this;
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    $(form).addClass('was-validated');
                    $(form).find(':input').each(function() {
                        if (!this.checkValidity()) {
                            console.warn(`Campo inválido: name=${this.name}, value=${this.value}`);
                        }
                    });
                } else {
                    $(form).addClass('was-validated');
                    this.submit();
                    console.log('Form is valid, submitting...');
                }
            });
        });
    </script>
</x-base-layout>
