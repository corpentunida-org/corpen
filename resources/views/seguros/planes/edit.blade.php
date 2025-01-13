<x-base-layout>
    <style>
        .uppercase-input {
            text-transform: uppercase;
        }
    </style>
    @section('titlepage', 'Editar Plan')
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
                        <form method="POST" id="formAddPlan" novalidate>
                            <div class="mb-4">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <label class="form-label">Convenio<span class="text-danger">*</span></label>
                                        <select class="form-control" name="convenio" id="convenio_id" required>
                                            @foreach ($convenios as $convenio)
                                                <option value="{{ $convenio->id }}" {{ $convenio['id'] == $segPlan['seg_convenio_id'] ? 'selected' : '' }}>
                                                {{ $convenio->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-5">
                                        <label class="form-label">Nombre Plan<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control uppercase-input" name="name" value="{{$segPlan->name}}" required>
                                    </div>
                                    <div class="col-lg-4">
                                        <label class="form-label">Condición<span class="text-danger">*</span></label>
                                        <select class="form-control" name="condicion_id">
                                            <option value="0" selected>Sin Condición</option>
                                            @foreach ($condiciones as $condicion)
                                                <option value="{{ $condicion->id }}" {{ $condicion['id'] == $segPlan['condicion_id'] ? 'selected' : '' }}>
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
                                            name="valorPlanAsegurado" value="{{$segPlan->valor}}" required>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Total Prima<span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="prima" id="prima" value="{{$segPlan->prima}}" required>
                                        <div class="invalid-feedback">
                                            La suma de las coberturas debe dar el valor total de la prima.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @foreach($segPlan->coberturas as $cobertura)
                                <div class="mb-4 cobertura-row">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <label class="form-label">Cobertura <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="cobertura[]" value="{{$cobertura->nombre}}" required>
                                        </div>
                                        <div class="col-lg-3">
                                            <label class="form-label">Valor Asegurado<span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="valorAsegurado[]" value="{{$cobertura->pivot->valorAsegurado}}" required>
                                        </div>
                                        <div class="col-lg-3">
                                            <label class="form-label">Valor Cobertura<span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="valorPrima[]" value="{{$cobertura->pivot->valorCobertura}}" required>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="mb-4">
                                <a href="javascript:void(0);" class="btn btn-primary wd-200" id="add-cobertura">
                                    <i class="feather-plus me-2"></i>
                                    <span>Agregar cobertura</span>
                                </a>
                            </div>
                            {{-- hidden --}}
                            <div class="d-flex flex-row-reverse gap-2 mt-2">
                                <button class="btn btn-warning mt-4" data-bs-toggle="tooltip" title="Timesheets"
                                    type="submit">
                                    <i class="feather feather-edit-3 me-3"></i>
                                    <span>Actualizar Plan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>                
            </div>
        </div>
    </div>

</x-base-layout>