{{-- Requiere: $user, $roles, $permisosPorRol, $permisosAsignados. Debe incluirse DENTRO del
     <form> que hace submit a admin.users.update — comparte el mismo POST que nombre/correo/
     contraseña (ver UserController::sincronizarRolesYPermisos()), no tiene su propio <form>. --}}

<!-- Tarjeta: Asignar Nuevo Rol -->
<div class="ui-card mb-4">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex align-items-center mb-3">
            <div class="ui-icon-box ui-pastel-blue me-3" style="width: 38px; height: 38px; font-size: 1rem;">
                <i class="bi bi-plus-lg"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-0 fs-15">Vincular Perfil Adicional</h6>
                <p class="text-muted fs-13 mb-0 mt-1">Expande los privilegios operativos del usuario.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <select class="form-select ui-input cursor-pointer" name="rolnuevo">
                    <option value="" disabled selected>Seleccione un perfil de la lista...</option>
                    @foreach ($roles as $rol)
                        <option value="{{ $rol->id }}" {{ $user->actions->first()?->role?->id === $rol->id ? 'selected' : '' }}>
                            {{ strtoupper($rol->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Encabezado Matriz de Roles -->
<div class="d-flex justify-content-between align-items-end mb-3 mt-4 px-1">
    <div>
        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-sliders me-2 text-primary"></i>Roles Activos & Permisos</h6>
        <p class="text-muted fs-13 mb-0">Personaliza los permisos dentro de cada rol asignado.</p>
    </div>
    <span class="badge ui-pastel-purple rounded-pill px-3 py-2 fs-12 shadow-sm border border-light">
        {{ count($user->actions) }} Rol(es) Activo(s)
    </span>
</div>

<!-- Acordeón de Permisos -->
<div class="accordion ui-accordion mb-4" id="accordionRolesPermissions">
    @forelse ($user->actions as $index => $rol)
        <div class="accordion-item shadow-sm">
            <h2 class="accordion-header" id="heading-{{ $index }}">
                <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}"
                        type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse-{{ $index }}"
                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                        <i class="bi bi-shield-check text-success me-3 fs-5"></i>
                        PERFIL: {{ strtoupper($rol->role->name) }}
                </button>
            </h2>
            <div id="collapse-{{ $index }}"
                 class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                 data-bs-parent="#accordionRolesPermissions">
                <div class="accordion-body p-3 p-md-4 bg-light">

                    @php $permisosDelRol = $permisosPorRol->get($rol->role_id) ?? collect(); @endphp

                    @if ($permisosDelRol->isEmpty())
                        <div class="alert alert-warning d-flex align-items-start gap-2 mb-0" role="alert">
                            <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                            <div class="fs-13 mb-0">
                                Este perfil no tiene ningún permiso configurado todavía — contacta a sistemas para
                                que lo vincule antes de poder activarle accesos a este usuario aquí.
                            </div>
                        </div>
                    @else
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3 pb-3 border-bottom border-light-subtle">
                            <h6 class="fw-bold text-dark fs-14 mb-0">Matriz de Configuración</h6>
                            <div class="d-flex gap-2 align-items-center">
                                <div class="input-group input-group-sm" style="max-width: 220px;">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" class="form-control border-start-0" placeholder="Buscar permiso..."
                                           oninput="filtrarPermisos({{ $index }}, this.value)">
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary text-nowrap" onclick="marcarPermisos({{ $index }}, true)">Marcar todos</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary text-nowrap" onclick="marcarPermisos({{ $index }}, false)">Ninguno</button>
                            </div>
                        </div>

                        <div class="row g-3" id="matriz-{{ $index }}">
                            @foreach ($permisosDelRol as $permiso)
                                <div class="col-lg-6 col-xl-4 permiso-item" data-nombre="{{ strtolower($permiso->name) }}">
                                    <div class="form-check form-switch ui-switch d-flex align-items-center">
                                        <input type="checkbox" name="permissions[]" value="{{ $permiso->id }}"
                                            class="form-check-input flex-shrink-0" id="perm_{{ $rol->role_id }}_{{ $permiso->id }}"
                                            @if (in_array($permiso->id, $permisosAsignados)) checked @endif>
                                        <label class="form-check-label user-select-none text-truncate" for="perm_{{ $rol->role_id }}_{{ $permiso->id }}" style="font-size: 0.85rem;" title="{{ $permiso->name }}">
                                            {{ $permiso->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-muted fs-13 mb-0 mt-2 d-none" id="sin-resultados-{{ $index }}">Ningún permiso coincide con la búsqueda.</p>
                    @endif

                </div>
            </div>
        </div>
    @empty
        <div class="ui-card p-4 text-center bg-white shadow-sm border-dashed">
            <div class="ui-icon-box bg-light text-muted mx-auto mb-2" style="width: 50px; height: 50px; font-size: 1.5rem;">
                <i class="bi bi-inbox"></i>
            </div>
            <h6 class="fw-bold text-dark">Sin Roles Asignados</h6>
            <p class="text-muted fs-13 mb-0">Este usuario no cuenta con perfiles operativos activos.</p>
        </div>
    @endforelse
</div>

<!-- Botonera de Guardado: sticky al fondo de la ventana, siempre visible aunque la
     matriz de permisos crezca mucho — antes había que volver a subir/bajar toda la
     página para guardar. -->
<div class="ui-card p-3 p-md-4 mb-4 shadow" style="position: sticky; bottom: 1rem; z-index: 20;">
    <div class="d-flex flex-column flex-sm-row justify-content-end align-items-center gap-3">
        <button type="submit" class="ui-btn-primary w-100 w-sm-auto">
            <i class="bi bi-save me-2"></i> Guardar Cambios
        </button>
    </div>
</div>
