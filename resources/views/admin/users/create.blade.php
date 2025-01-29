<x-base-layout>
    @section('titlepage', 'Administrador')

    <div class="col-xxl-12 col-xl-12">
        <div class="card border-top-0">
            <div class="card-header p-0">
                <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#securityTab"
                            aria-selected="true">Crear Usuario</a>
                    </li>
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" data-bs-target="#taskTab" aria-selected="false">Crear
                            Rol</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 active show" id="securityTab" role="tabpanel">
                    <div class="col-lg-12 p-4">
                        <form method="POST" action="{{ route('seguros.planes.store') }}" id="formAddPlan" novalidate>
                            @csrf
                            @method('POST')
                            <div class="mb-4">
                                <label class="form-label">Nombre y Apellidos<span class="text-danger">*</span></label>
                                <input type="text" class="form-control uppercase-input" name="name" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Correo corporativo<span class="text-danger">*</span></label>
                                <input type="email" class="form-control uppercase-input" name="email" required>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label class="form-label">Asignar Rol<span class="text-danger">*</span></label>
                                        <select class="form-control" name="cobconvenio">
                                            @foreach ($roles as $rol)
                                                <option value="">
                                                    {{ strtoupper($rol->name) }}
                                                </option>
                                            @endforeach
                                        </select>                                        
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label class="form-label">Contraseña<span class="text-danger">*</span></label>
                                        <input type="password" class="form-control uppercase-input" name="pass"required>
                                    </div>
                                </div>
                            </div>


                            <div class="d-flex flex-row-reverse gap-2 mt-2">
                                <button class="btn btn-success mt-4" data-bs-toggle="tooltip" title="Timesheets"
                                    type="submit">
                                    <i class="feather-plus me-2"></i>
                                    <span>Agregar Usuario</span>
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
                <div class="tab-pane fade p-4" id="taskTab" role="tabpanel">
                    <div class="col-lg-12 p-4">
                        <form method="POST" action="{{ route('seguros.cobertura.store') }}"
                            id="formAddCobertura"novalidate>
                            @csrf
                            @method('POST')
                            <div class="mb-4">
                                
                                        <label class="form-label">Nombre del Rol<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control uppercase-input" name="cobname"
                                            required>
                                    
                            </div>
                            


                            <div class="d-flex flex-row-reverse gap-2 mt-2">
                                <button class="btn btn-success mt-4" data-bs-toggle="tooltip"
                                    title="Timesheets"type="submit">
                                    <i class="feather-plus me-2"></i>
                                    <span>Agregar Rol</span>
                                </button>
                            </div>

                        </form>
                        <script>
                            $('#formAddCobertura').submit(function(event) {
                                var form = this;
                                if (!form.checkValidity()) {
                                    $(form).addClass('was-validated');
                                    event.preventDefault();
                                    event.stopPropagation();
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-base-layout>
