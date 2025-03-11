<x-base-layout>
    @section('titlepage', 'Administrador')
    <x-error />
    <x-success />
    <div class="col-xxl-12 col-xl-12">
        <div class="card border-top-0">
            <div class="card-header p-0">
                <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#securityTab"
                            aria-selected="true">Crear Usuario</a>
                    </li>                    
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 active show" id="securityTab" role="tabpanel">
                    <div class="col-lg-12 p-4">
                        <form method="POST" action="{{ route('admin.users.store') }}" id="formAddUser" novalidate>
                            @csrf
                            @method('POST')
                            <div class="mb-4">
                                <label class="form-label">Nombre y Apellidos<span class="text-danger">*</span></label>
                                <input type="text" class="form-control uppercase-input" name="name" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Correo corporativo<span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" required>
                            </div>


                            <div class="mb-4">
                                <label class="form-label">Contraseña<span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="pass"required>
                            </div>

                            <div id="contenedor-roles">
                                <div class="mb-4 rol-row">
                                    <label class="form-label">Asignar Rol<span class="text-danger">*</span></label>
                                    <select class="form-control" name="rol[]">
                                        @foreach ($roles as $rol)
                                            <option value="{{ $rol->id }}">
                                                {{ strtoupper($rol->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-4">
                                <a href="javascript:void(0);" class="btn btn-primary wd-200" id="add-rol">
                                    <i class="feather-plus me-2"></i>
                                    <span>Agregar Rol</span>
                                </a>
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
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#add-rol').click(function() {
                const container = document.getElementById('contenedor-roles');
                const coberturaRow = document.querySelector('.rol-row');

                const clonedRow = coberturaRow.cloneNode(true);

                const inputs = clonedRow.querySelectorAll('input');
                inputs.forEach(input => input.value = '');

                container.appendChild(clonedRow);
            });
            $('#formAddUser').submit(function(event) {
                var form = this;
                if (!form.checkValidity()) {
                    $(form).addClass('was-validated');
                    event.preventDefault();
                    event.stopPropagation();
                }
            });
        });
    </script>
</x-base-layout>
