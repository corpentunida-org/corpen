{{-- Requiere: $user, $roles, $permisosAsignados. Debe incluirse DENTRO del <form> que hace submit a
     admin.users.update (comparte el POST con nombre/correo/contraseña; solo envía 'rolnuevo').

     Los permisos NO se editan por persona: provienen únicamente de los roles (perfiles/grupos) y se
     definen en la Matriz de Permisos. Aquí solo se ve qué tiene activo y de qué rol viene. --}}

<!-- Tarjeta: Asignar Perfil (rol) — un solo perfil por usuario -->
@php
    $perfilesActuales = $user->actions->map(fn ($a) => $a->role)->filter();
    $nombresActuales = $perfilesActuales->pluck('name')->map(fn ($n) => strtoupper($n))->values();
    $idsActuales = $perfilesActuales->pluck('id')->all();
@endphp
<div class="ui-card mb-4">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex align-items-center mb-3">
            <div class="ui-icon-box ui-pastel-blue me-3" style="width: 38px; height: 38px; font-size: 1rem;">
                <i class="bi bi-person-badge"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-0 fs-15">Perfil de la persona</h6>
                <p class="text-muted fs-13 mb-0 mt-1">
                    Cada usuario tiene <strong>un solo perfil</strong> (por ejemplo: Cartera, Contabilidad, Asociado). El perfil define qué menús ve y qué puede hacer;
                    elegir uno nuevo <strong>reemplaza</strong> el anterior.
                    <a href="{{ route('admin.guia.permisos') }}" target="_blank">¿Cómo funciona? Ver guía paso a paso</a>
                </p>
            </div>
        </div>

        @if ($nombresActuales->count() > 1)
            <div class="alert alert-warning fs-13" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                Esta persona tiene <strong>{{ $nombresActuales->count() }} perfiles</strong> ({{ $nombresActuales->implode(', ') }}) de la forma antigua.
                La regla ahora es un solo perfil: al elegir uno abajo y guardar, quedará únicamente con ese.
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <label class="form-label fs-13 fw-semibold mb-1">
                    Perfil actual: {{ $nombresActuales->isEmpty() ? 'ninguno' : $nombresActuales->implode(', ') }}
                </label>
                <select class="form-select ui-input cursor-pointer" name="rolnuevo" id="selectPerfilNuevo">
                    <option value="" selected>{{ $nombresActuales->isEmpty() ? 'Seleccione un perfil de la lista...' : 'Mantener el perfil actual (o elegir otro para cambiarlo)' }}</option>
                    @foreach ($roles as $rol)
                        @continue(in_array($rol->id, $idsActuales) && count($idsActuales) === 1)
                        <option value="{{ $rol->id }}">{{ strtoupper($rol->name) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mt-2 mt-md-0">
                <a href="{{ route('admin.roles.matriz', ['nuevo' => 1]) }}#cardNuevoPerfil" class="btn btn-outline-primary w-100 fw-semibold">
                    <i class="bi bi-plus-circle me-1"></i> Crear perfil nuevo
                </a>
            </div>
        </div>
        <p class="text-muted fs-12 mb-0 mt-2">¿No existe el perfil que necesitas (ej. el de un área nueva)? Créalo en la Matriz de Permisos, márcale sus permisos y luego asígnalo aquí.</p>
    </div>
</div>

<script>
    (function () {
        const select = document.getElementById('selectPerfilNuevo');
        if (!select) return;
        const actuales = @json($nombresActuales);
        select.closest('form').addEventListener('submit', function (e) {
            if (!select.value || !actuales.length) return;
            const nuevo = select.options[select.selectedIndex].text;
            if (!confirm('Se asignará el perfil ' + nuevo + ' y se quitará: ' + actuales.join(', ') + '.\n\nLa persona perderá los menús y permisos del perfil anterior. ¿Continuar?')) e.preventDefault();
        });
    })();
</script>
<!-- Encabezado: permisos activos (solo lectura) -->
<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-2 mt-4 px-1">
    <div>
        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-shield-check me-2 text-primary"></i>Perfiles y Permisos Activos</h6>
        <p class="text-muted fs-13 mb-0">Solo lectura. Los permisos provienen del perfil; para cambiarlos edita el perfil en la Matriz (o consulta la <a href="{{ route('admin.guia.permisos') }}" target="_blank">guía</a>).</p>
    </div>
    <span class="badge ui-pastel-purple rounded-pill px-3 py-2 fs-12 shadow-sm border border-light">
        {{ count($user->actions) }} Perfil(es) · {{ count($permisosAsignados) }} permiso(s)
    </span>
</div>

<div class="alert alert-info d-flex align-items-start gap-2 fs-13 mb-3" role="alert">
    <i class="bi bi-info-circle-fill mt-1"></i>
    <div>
        Para dar o quitar un permiso a <strong>todos</strong> los que tienen un perfil, usa la
        <a href="{{ route('admin.roles.matriz') }}" class="alert-link">Matriz de Permisos</a>.
        El cambio se aplica de inmediato a cada persona con ese perfil.
    </div>
</div>

<!-- Acordeón de perfiles (solo lectura) -->
<div class="accordion ui-accordion mb-4" id="accordionRolesPermissions">
    @forelse ($user->actions as $index => $rol)
        @php
            $permisosDelRol = ($rol->role?->permissions ?? collect())->sortBy('name');
            $porModulo = $permisosDelRol->groupBy(fn ($p) => explode('.', $p->name)[0]);
        @endphp
        <div class="accordion-item shadow-sm">
            <h2 class="accordion-header" id="heading-{{ $index }}">
                <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}"
                        type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse-{{ $index }}"
                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                    <i class="bi bi-shield-check text-success me-3 fs-5"></i>
                    PERFIL: {{ strtoupper($rol->role->name) }}
                    <span class="badge bg-light text-secondary border ms-3">{{ $permisosDelRol->count() }} permisos</span>
                </button>
            </h2>
            <div id="collapse-{{ $index }}"
                 class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                 data-bs-parent="#accordionRolesPermissions">
                <div class="accordion-body p-3 p-md-4 bg-light">

                    @if ($permisosDelRol->isEmpty())
                        <div class="alert alert-warning d-flex align-items-start gap-2 mb-0" role="alert">
                            <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                            <div class="fs-13 mb-0">
                                Este perfil no tiene ningún permiso configurado todavía. Configúralo en la
                                <a href="{{ route('admin.roles.matriz', ['rol' => $rol->role_id]) }}#rol-{{ $rol->role_id }}" class="alert-link">Matriz de Permisos</a>.
                            </div>
                        </div>
                    @else
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <div class="input-group input-group-sm" style="max-width: 260px;">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0" placeholder="Buscar permiso..."
                                       oninput="filtrarPermisos({{ $index }}, this.value)">
                            </div>
                            <a href="{{ route('admin.roles.matriz', ['rol' => $rol->role_id]) }}#rol-{{ $rol->role_id }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-grid-3x3-gap me-1"></i> Editar permisos de este perfil en la Matriz
                            </a>
                        </div>

                        <div id="matriz-{{ $index }}">
                            @foreach ($porModulo as $modulo => $permisosModulo)
                                <div class="text-uppercase text-muted fw-bold fs-11 mt-3 mb-2 pb-1 border-bottom">{{ $modulo }}</div>
                                <div class="row g-2">
                                    @foreach ($permisosModulo as $permiso)
                                        <div class="col-lg-6 col-xl-4 permiso-item" data-nombre="{{ strtolower($permiso->name) }}">
                                            <div class="d-flex align-items-center gap-2 bg-white border rounded-3 px-3 py-2" title="{{ $permiso->name }}">
                                                @if (in_array($permiso->id, $permisosAsignados))
                                                    <i class="bi bi-check-circle-fill text-success"></i>
                                                @else
                                                    <i class="bi bi-dash-circle text-secondary" title="Aún no se ha aplicado a esta persona"></i>
                                                @endif
                                                <span class="text-truncate" style="font-size: 0.85rem; font-family: monospace;">{{ $permiso->name }}</span>
                                            </div>
                                        </div>
                                    @endforeach
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
            <h6 class="fw-bold text-dark">Sin Perfiles Asignados</h6>
            <p class="text-muted fs-13 mb-0">Esta persona no tiene perfiles activos, así que no tiene permisos.</p>
        </div>
    @endforelse
</div>

<script>
    // Filtra en vivo los permisos visibles dentro de un perfil, por nombre.
    function filtrarPermisos(index, texto) {
        const termino = texto.trim().toLowerCase();
        const items = document.querySelectorAll('#matriz-' + index + ' .permiso-item');
        let visibles = 0;
        items.forEach(function (item) {
            const coincide = item.dataset.nombre.includes(termino);
            item.classList.toggle('d-none', !coincide);
            if (coincide) visibles++;
        });
        const sinResultados = document.getElementById('sin-resultados-' + index);
        if (sinResultados) {
            sinResultados.classList.toggle('d-none', visibles > 0);
        }
    }
</script>

<!-- Botonera de Guardado: sticky al fondo de la ventana -->
<div class="ui-card p-3 p-md-4 mb-4 shadow" style="position: sticky; bottom: 1rem; z-index: 20;">
    <div class="d-flex flex-column flex-sm-row justify-content-end align-items-center gap-3">
        <button type="submit" class="ui-btn-primary w-100 w-sm-auto">
            <i class="bi bi-save me-2"></i> Guardar Cambios
        </button>
    </div>
</div>
