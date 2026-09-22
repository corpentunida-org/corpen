{{-- Requiere: $user. --}}
<div class="ui-danger-zone shadow-sm">
    <div class="row align-items-center">
        <div class="col-lg-6 mb-3 mb-lg-0">
            <h6 class="fw-bold text-danger mb-1">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>Revocar Perfil
            </h6>
            <p class="text-danger opacity-75 fs-13 mb-0 pe-md-3">
                Acción irreversible. El usuario perderá acceso a los módulos de inmediato.
            </p>
        </div>

        <div class="col-lg-6 border-start border-danger border-opacity-25 ps-lg-3">
            {{-- Sin novalidate: el navegador ya bloquea el envío si no se elige un perfil
                 (select required). El servidor también lo valida (RoleController::destroy). --}}
            <form method="POST" action="{{ route('admin.roles.destroy', $user->id) }}">
                @csrf
                @method('DELETE')

                <div class="d-flex flex-column flex-sm-row gap-2 mt-1">
                    <select class="form-select flex-grow-1 border-danger border-opacity-50 text-danger bg-white shadow-none fs-14" name="rol" required>
                        <option value="" disabled selected>Seleccione rol a revocar...</option>
                        @foreach ($user->actions as $rol)
                            <option value="{{ $rol->role_id }}">
                                {{ strtoupper($rol->role->name) }}
                            </option>
                        @endforeach
                    </select>

                    <button class="ui-btn-danger text-nowrap fs-14" data-bs-toggle="tooltip" title="Revocar acceso" type="submit">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
