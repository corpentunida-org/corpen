<x-base-layout>
    @section('titlepage', 'Crear Convenio')
    <x-error />
    <div class="col-xxl-12 col-xl-12">
        <div class="card border-top-0">
            <div class="card-header p-0">
                <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#securityTab"
                            aria-selected="true">Copiar Convenio</a>
                    </li>
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" data-bs-target="#taskTab" aria-selected="true">Crear
                            Convenio</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 active show" id="securityTab" role="tabpanel">
                    <div class="col-lg-12 p-4">
                        <form method="POST" action="{{ route('seguros.convenio.store') }}" id="formAddPlan" novalidate>
                            @csrf
                            @method('POST')
                            <div class="mb-4">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <label class="form-label">Id Convenio<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control uppercase-input"
                                            value="{{ $convenio->idAseguradora }}" name="idAseguradora" required>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Nombre Convenio<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control uppercase-input" name="nombre"
                                            required>
                                    </div>
                                    <div class="col-lg-2">
                                        <label class="form-label">Año<span class="text-danger">*</span></label>
                                        <input type="number" class="form-control uppercase-input" name="anio"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-lg-4">
                                    <label class="form-label">Proveedor<span class="text-danger">*</span></label>
                                    <select class="form-control" name="proveedor" required>
                                        <option value="1">Allianz Seguros de Vida S.A</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Fecha Inicio<span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="fechaInicio" required>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Fecha Fin<span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="fechaFin" required>
                                </div>
                            </div>
                            <h5 class="fw-bold my-4">Los planes se copian con cada una de las coberturas. </h5>
                            @foreach ($planes as $i => $segPlans)
                                <div class="mb-4 cobertura-row">
                                    @foreach ($segPlans as $plan)
                                        <div class="row my-2">
                                            <div class="col-lg-2">
                                                <label class="form-label">Nombre Plan<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control"
                                                    name="planes[{{ $plan->id }}][name]" value="{{ $plan->name }}"
                                                    required>
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="form-label">Valor Asegurado<span
                                                        class="text-danger">*</span></label>
                                                <input type="number" class="form-control"
                                                    name="planes[{{ $plan->id }}][vasegurado]"
                                                    value="{{ $plan->valor }}" required>
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="form-label">Valor Prima<span
                                                        class="text-danger">*</span></label>
                                                <input type="number" class="form-control"
                                                    name="planes[{{ $plan->id }}][vprima]"
                                                    value="{{ $plan->prima }}" required>
                                            </div>
                                            <div class="col-lg-4">
                                                <label class="form-label">Condición<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control"
                                                    name="planes[{{ $plan->id }}][condicion]">
                                                    @foreach ($condiciones as $c)
                                                        <option value="{{ $c->id }}"
                                                            {{ $plan->condicion_id == $c->id ? 'selected' : '' }}>
                                                            {{ $c->descripcion }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endforeach
                                    <hr class="my-3">
                                </div>
                            @endforeach

                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3 me-3">
                                    <div class="custom-control custom-checkbox me-2">
                                        <input type="checkbox" class="custom-control-input" id="customCheckTask10"
                                            data-checked-action="task-action" name="CheckVigencia">
                                        <label class="custom-control-label c-pointer" for="customCheckTask10"></label>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <a href="javascript:void(0);" class="single-task-list-link"
                                            data-bs-toggle="offcanvas" data-bs-target="#tasksDetailsOffcanvas">
                                            <div class="fs-13 fw-bold text-truncate-1-line">Configurar este convenio como
                                                <span class="ms-2 badge bg-soft-success text-success"> Vigente</span>
                                            </div>
                                            <div class="fs-12 fw-normal text-muted text-truncate-1-line">Se actualizará
                                                los valores de este nuevo convenio para todos los asegurados.</div>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-row-reverse gap-2 mt-2">
                                <button class="btn btn-success mt-4" data-bs-toggle="tooltip" title="Timesheets"
                                    type="submit">
                                    <i class="feather-plus me-2"></i>
                                    <span>Copiar Convenio</span>
                                </button>
                            </div>
                        </form>
                        <script>
                            $(document).ready(function() {
                                $('#formAddPlan').submit(function(event) {
                                    var form = this;
                                    if (!form.checkValidity()) {
                                        $(form).addClass('was-validated');
                                        event.preventDefault();
                                        event.stopPropagation();
                                    }
                                });
                            });
                        </script>
                    </div>
                </div>
                <div class="tab-pane fade p-4" id="taskTab" role="tabpanel">
                    <div class="col-lg-12 p-4">
                        <form method="POST" id="formAddConvenio" novalidate>
                            @csrf
                            @method('POST')
                            <div class="row mb-4">
                                <div class="col-lg-3">
                                    <label class="form-label">Id Convenio<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control uppercase-input"
                                        name="nameConvenio"required>
                                </div>
                                <div class="col-lg-7">
                                    <label class="form-label">Nombre Convenio<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control uppercase-input"
                                        name="nameConvenio"required>
                                </div>
                                <div class="col-lg-2">
                                    <label class="form-label">Año<span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="anioConvenio"required>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-lg-4">
                                    <label class="form-label">Proveedor<span class="text-danger">*</span></label>
                                    <select class="form-control" name="convenio" id="convenio_id" required>
                                        <option value="1">Allianz Seguros de Vida S.A</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Fecha Inicio<span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="fechaInicio"required>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Fecha Fin<span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="fechaInicio"required>
                                </div>
                            </div>
                            <div class="pass-hint">
                                <ul class="fs-12 ps-1 ms-2 text-muted">
                                    <li class="mb-1">El convenio se crea sin planes. </li>
                                    <li class="mb-1">Debe crearlos manualmente, en la vista del convenio una vez
                                        creado. </li>

                                </ul>
                            </div>
                            <div class="d-flex flex-row-reverse gap-2 mt-2">
                                <button class="btn btn-success mt-4" data-bs-toggle="tooltip" title="Timesheets"
                                    type="submit">
                                    <i class="feather-plus me-2"></i>
                                    <span>Agregar Convenio</span>
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

</x-base-layout>
