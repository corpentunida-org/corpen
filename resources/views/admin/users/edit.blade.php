<x-base-layout>
    @section('titlepage', 'Perfil Usuario')
    <x-success />
    <x-error />
    <div class="col-12">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <div class="mb-4 text-center">
                    <div class="wd-150 ht-150 mx-auto mb-3 position-relative">
                        <div class="avatar-image wd-150 ht-150 border border-5 border-gray-3">
                            <img src="assets/images/avatar/1.png" alt="" class="img-fluid">
                        </div>
                        <div class="wd-10 ht-10 text-success rounded-circle position-absolute translate-middle"
                            style="top: 76%; right: 10px">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                    </div>
                    <div class="mb-4">
                        <a class="fs-14 fw-bold d-block"> {{ $user->name }}</a>
                        <a class="fs-12 fw-normal text-muted d-block">{{ $user->email }}</a>
                    </div>
                    <div class="fs-12 fw-normal text-muted text-center d-flex flex-wrap gap-3 mb-4">
                        <div class="flex-fill py-3 px-4 rounded-1 d-none d-sm-block border border-dashed border-gray-5">
                            <h6 class="fs-15 fw-bolder">{{ $acciones }}</h6>
                            <p class="fs-12 text-muted mb-0">Actividades en la app</p>
                        </div>
                        <div class="flex-fill py-3 px-4 rounded-1 d-none d-sm-block border border-dashed border-gray-5">
                            <h6 class="fs-15 fw-bolder">{{ strtoupper($user->actions->first()->role->name) }}</h6>
                            <p class="fs-12 text-muted mb-0">Rol asignado</p>
                        </div>
                        <div class="flex-fill py-3 px-4 rounded-1 d-none d-sm-block border border-dashed border-gray-5">
                            <h6 class="fs-15 fw-bolder">{{ $fecha->fechaRegistro ?? ' ' }}</h6>
                            <p class="fs-12 text-muted mb-0">Ultima actividad</p>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.users.update', $user) }}" id="formUpdateUser" novalidate>
                    @csrf
                    @method('PUT')
                    <ul class="list-unstyled mb-4">
                        <div class="row mb-4 align-items-center">
                            <div class="col-lg-2">
                                <span class="text-muted fw-medium hstack gap-3 mr-3"><i
                                        class="feather-phone"></i>Telefono</span>
                            </div>
                            <div class="col-lg-10">
                                <div class="input-group">
                                    <input class="form-control" value="+01 (375) 2589 645">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4 align-items-center">
                            <div class="col-lg-2">
                                <span class="text-muted fw-medium hstack gap-3 mr-3"><i
                                        class="feather-mail"></i>Email</span>
                            </div>
                            <div class="col-lg-10">
                                <div class="input-group">
                                    <input class="form-control" value="{{ $user->email }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4 align-items-center">
                            <div class="col-lg-2">
                                <span class="text-muted fw-medium hstack gap-3 mr-3"><i
                                        class="feather-user"></i>Name</span>
                            </div>
                            <div class="col-lg-10">
                                <div class="input-group">
                                    <input class="form-control" value="{{ $user->name }}" name="name" required>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4 align-items-center">
                            <div class="col-lg-2">
                                <span class="text-muted fw-medium hstack gap-3 mr-3"><i
                                        class="bi bi-lock-fill"></i>Contraseña</span>
                            </div>
                            <div class="col-lg-10">
                                <div class="input-group">
                                    <input class="form-control" type="password" name="pass">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-lg-2 fw-medium">Roles Asignados</div>
                            <div class="col-lg-10 hstack gap-1">
                                @foreach ($user->actions as $rol)
                                    <a href="#" class="badge bg-soft-primary text-primary">{{ strtoupper($rol->role->name) }}</a>     
                                @endforeach
                            </div>
                        </div>
                        <div class="row mb-4 align-items-center">
                            <div class="col-lg-2">
                                <span class="text-muted fw-medium hstack gap-3 mr-3"><i class="bi bi-ui-checks-grid"></i>Permisos</span>
                            </div>
                            <div class="col-lg-10">
                                <div class="row">
                                    @foreach ($permisosUsuario as $index => $permiso)
                                        @if ($index % 3 == 0 && $index > 0)
                                </div>
                                <div class="row">
                                    @endif
                                    <div class="col-lg-4">
                                        <div class="form-check">
                                            <input type="checkbox" name="permissions[]" value="{{ $permiso->id }}"
                                                class="form-check-input ml-3" id="permission_{{ $permiso->id }}"
                                                @if (in_array($permiso->id, $permisosAsignados)) checked @endif>
                                            <label class="form-check-label" for="permission_{{ $permiso->id }}">
                                                {{ $permiso->name }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </ul>
                    <div id="contenedor-roles">
                        <div class="mb-4 rol-row">
                            <label class="form-label">Adicionar un Rol<span class="text-danger">*</span></label>
                            <select class="form-control" name="rolnuevo">
                                @foreach ($roles as $rol)
                                    <option value="{{ $rol->id }}" {{ $user->actions->first()?->role?->id === $rol->id ? 'selected' : '' }}>
                                        {{ strtoupper($rol->name) }}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                        </div>
                    <div class="d-flex gap-2 text-center pt-4">
                        <a href="javascript:void(0);" class="w-50 btn btn-light-brand">
                            <i class="feather-trash-2 me-2"></i>
                            <span>Delete</span>
                        </a>
                        <button type="submit" class="w-50 btn btn-primary">
                            <i class="feather-edit me-2"></i>
                            <span>Guardar Cambios</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $('#formUpdateUser').submit(function(event) {
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
