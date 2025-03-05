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
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" data-bs-target="#taskTab" aria-selected="false">Crear
                            Rol</a>
                    </li>
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" data-bs-target="#ActivityTab"
                            aria-selected="false">Asignar Permisos</a>
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
                <div class="tab-pane fade p-4" id="taskTab" role="tabpanel">
                    <div class="col-lg-12 p-4">
                        <form method="POST" action="{{ route('admin.rol.store') }}" id="formAddRole" novalidate>
                            @csrf
                            @method('POST')
                            <div class="mb-4">
                                <label class="form-label">Nombre del Rol<span class="text-danger">*</span></label>
                                <input type="text" class="form-control uppercase-input" name="namerole" required>
                                <div class="invalid-feedback">
                                    Por favor digite el nombre del rol.
                                </div>
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
                            $('#formAddRole').submit(function(event) {
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
                <div class="tab-pane fade p-4" id="ActivityTab" role="tabpanel">
                    <div class="col-lg-12 p-4">
                        <div class="mb-4 rol-row">
                            <label class="form-label">Asignar Rol<span class="text-danger">*</span></label>
                            <select class="form-control" name="role_id" id="select-role">
                                @foreach ($roles as $rol)
                                    <option value="{{ $rol->id }}">
                                        {{ strtoupper($rol->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex gap-2 mt-1">
                            <button type="submit" class="btn btn-primary mt-1" data-bs-toggle="tooltip"type="submit">
                                <i class="feather-plus me-2"></i>
                                <span>Buscar Permisos</span>
                            </button>
                        </div>

                        <form method="POST" action="{{ route('admin.permissions.update') }}"
                            id="permissions-container" class="mt-4 pl-3">
                            @csrf
                            @method('PUT')
                        </form>

                        <script>
                            $(document).ready(function() {
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    }
                                });
                                $('#select-role').change(function() {
                                    var roleId = $(this).val();
                                    $.ajax({
                                        url: '{{ route('admin.permissions.show') }}',
                                        type: 'POST',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            role_id: roleId
                                        },
                                        success: function(data) {
                                            $('#permissions-container').empty();
                                            if (data.permissions.length > 0) {
                                                data.permissions.forEach(function(permission) {
                                                    var isChecked = data.assigned_permissions.includes(
                                                        permission.id) ? 'checked' : '';

                                                    var checkbox =
                                                        '<div><input type="checkbox" class="form-check-input ml-3" name="permissions[]" value="' +
                                                        permission.id + '" ' + isChecked + '> <label>' +
                                                        permission.name + '</label></div>';
                                                    $('#permissions-container').append(checkbox);
                                                });
                                                var csrfTokenInput =
                                                    '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
                                                var methodInput =
                                                '<input type="hidden" name="_method" value="PUT">';
                                                var hiddenInput =
                                                '<input type="hidden" name="roleid" value="' + roleId + '">';
                                                var button =
                                                    '<div class="d-flex flex-row-reverse gap-2 mt-2"><button type="submit" class="btn btn-success mt-3" data-bs-toggle="tooltip"><i class="feather-plus me-2"></i><span>Actualizar Permisos</span></button></div>';
                                                $('#permissions-container').append(hiddenInput);
                                                $('#permissions-container').append(csrfTokenInput);
                                                $('#permissions-container').append(methodInput);
                                                $('#permissions-container').append(button);
                                            } else {
                                                $('#permissions-container').append(
                                                    '<p>No hay permisos disponibles para este rol.</p>');
                                            }
                                        },
                                        error: function() {
                                            $('#permissions-container').empty().append(
                                                '<p>Error al cargar los permisos.</p>');
                                        }
                                    });
                                });
                            });
                        </script>
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
