<x-base-layout>
    @section('titlepage', 'Role Administración')
    <x-error />
    <x-success />

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="fw-bold mb-0">
                    <span class="d-block mb-2">Lista de Roles </span>
                    <span class="fs-12 fw-normal text-muted d-block">Con sus permisos asignados </span>
                </h5>
                <a href="#cardAddRole" class="btn btn-success" data-bs-toggle="tooltip">
                    <i class="feather-plus me-2"></i>
                    <span>Agregar Rol</span>
                </a>
            </div>
            <div class="card-body">
                <div class="accordion proposal-faq-accordion">
                    @foreach ($roles as $i => $rol)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapse{{ $i }}" aria-expanded="false"
                                    aria-controls="flush-collapse{{ $i }}">{{ strtoupper($rol->name) }}</button>
                            </h2>
                            <div id="flush-collapse{{ $i }}" class="accordion-collapse collapse"
                                aria-labelledby="flush-heading{{ $i }}" data-bs-parent="#accordionFaqGroup"
                                style="">
                                <div class="accordion-body">
                                    <ul class="nav flex-column nxl-content-sidebar-item">
                                        <li
                                            class="px-4 my-2 fs-10 fw-bold text-uppercase text-muted text-spacing-1 d-flex align-items-center justify-content-between">
                                            <span>Permisos</span>
                                            <a href="#cardAddPermisos">
                                                <span class="avatar-text avatar-sm" data-bs-toggle="tooltip"
                                                    data-bs-trigger="hover" title=""
                                                    data-bs-original-title="Agregar Nuevo"> <i class="feather-plus"></i>
                                                </span>
                                            </a>
                                        </li>
                                        <form action="{{ route('admin.roles.update', $rol->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            @foreach ($rol->permissionsRole as $listapermisos)
                                                <li class="nav-item">
                                                    <div class="nav-link">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="checkbox{{ $listapermisos->id }}"
                                                                name="permissions[]" value="{{ $listapermisos->id }}"
                                                                @if (in_array($listapermisos->id, $rol->permissions->pluck('id')->toArray())) checked @endif>
                                                            <label class="custom-control-label c-pointer"
                                                                for="checkbox{{ $listapermisos->id }}">{{ $listapermisos->name }}</label>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                            <div class="d-flex flex-row-reverse gap-2 mt-2">
                                                <button class="btn btn-warning" data-bs-toggle="tooltip"
                                                    title="Permisos" type="submit">
                                                    <i class="feather-plus me-2"></i>
                                                    <span>Actualizar</span>
                                                </button>
                                            </div>
                                        </form>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card stretch stretch-full" id="cardAddRole">
            <div class="card-header">
                <h5 class="fw-bold mb-0">
                    <span class="d-block mb-2">Agregar Rol </span>
                    <span class="fs-12 fw-normal text-muted d-block">Ingrese el nombre del rol </span>
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.roles.store') }}" id="formAddRole" novalidate>
                    @csrf
                    @method('POST')
                    <div class="mb-4">
                        <label class="form-label">Nombre del Rol<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="namerole" required>
                    </div>

                    <div class="d-flex flex-row-reverse gap-2 mt-2">
                        <button class="btn btn-success mt-4" data-bs-toggle="tooltip" title="Timesheets"type="submit">
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
    </div>
    <div class="col-lg-12">
        <div class="card stretch stretch-full" id="cardAddPermisos">
            <div class="card-header">
                <h5 class="fw-bold mb-0">
                    <span class="d-block mb-2">Crear un permiso </span>
                    <span class="fs-12 fw-normal text-muted d-block">Nombre del permiso y su rol asignado</span>
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <div class="col-lg-3 fw-medium">Ejemplo para asignar un nombre rol</div>
                    <div class="col-lg-9 hstack gap-1">
                        <span class="badge bg-soft-primary text-primary">Nombre Rol</span> .
                        <span class="badge bg-soft-success text-success">Area Especifica</span> .
                        <span class="badge bg-soft-warning text-warning">Funcionalidad</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.permisos.store') }}" id="formAddPermiso" novalidate>
                    @csrf
                    @method('POST')
                    <div class="mb-4">
                        <label class="form-label">Nombre del Permiso<span class="text-danger">*</span></label>
                        <input type="text" class="form-control uppercase-input" name="permisoName" required>
                    </div>
                    <div class="mb-4">
                        <select class="form-control" name="permisoRol">
                            @foreach ($roles as $i => $rol)
                                <option value="{{ $rol->id }}">{{ strtoupper($rol->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex flex-row-reverse gap-2 mt-2">
                        <button class="btn btn-success mt-4" data-bs-toggle="tooltip"
                            title="Timesheets"type="submit">
                            <i class="feather-plus me-2"></i>
                            <span>Agregar Permiso</span>
                        </button>
                    </div>
                </form>
                <script>
                    $('#formAddPermiso').submit(function(event) {
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
</x-base-layout>
