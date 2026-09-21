{{-- Requiere: $user, $usuarios (otros usuarios del mismo tipo, para copiar su acceso),
     $rolesPorUsuario (user_id => [{id, name}, ...]). --}}
@php
    $misRoles = $user->actions->pluck('role_id')->map(fn ($id) => (int) $id)->all();
@endphp
<div class="ui-card mb-4">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex align-items-center mb-3">
            <div class="ui-icon-box ui-pastel-amber me-3" style="width: 38px; height: 38px; font-size: 1rem;">
                <i class="bi bi-copy"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-0 fs-15">Copiar Perfil de Otro Usuario</h6>
                <p class="text-muted fs-13 mb-0 mt-1">
                    Asigna a este usuario el mismo perfil que tiene un compañero de referencia (ej. alguien de su misma área). Sus permisos y menús llegan solos desde ese perfil.
                    Si ya tenía otro perfil, se reemplaza.
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.copiar-permisos', $user) }}" id="formCopiarPerfil">
            @csrf
            <div class="row g-2">
                <div class="col-md-8">
                    <select class="form-select ui-input cursor-pointer" name="usuario_referencia_id" id="usuarioReferencia" required>
                        <option value="" disabled selected>Seleccione un usuario de referencia...</option>
                        @foreach ($usuarios as $otro)
                            <option value="{{ $otro->id }}" data-roles="{{ json_encode($rolesPorUsuario[$otro->id] ?? []) }}">{{ $otro->name }} ({{ $otro->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-primary w-100 fw-semibold" id="btnCopiarPerfil" disabled>
                        <i class="bi bi-copy me-1"></i> Copiar acceso
                    </button>
                </div>
            </div>
            <div id="avisoCopiarPerfil" class="alert alert-light border fs-13 mt-3 mb-0 d-none" role="status"></div>
        </form>
    </div>
</div>

<script>
    (function () {
        const misRoles = @json($misRoles);
        const select = document.getElementById('usuarioReferencia');
        const aviso = document.getElementById('avisoCopiarPerfil');
        const boton = document.getElementById('btnCopiarPerfil');
        const form = document.getElementById('formCopiarPerfil');
        const nombreDestino = @json($user->name);
        let mensajeConfirmacion = '';

        const esc = t => String(t).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

        const actuales = @json($user->actions->map(fn ($a) => $a->role?->name)->filter()->map(fn ($n) => strtoupper($n))->values());

        select.addEventListener('change', function () {
            const opcion = select.options[select.selectedIndex];
            const roles = JSON.parse(opcion.dataset.roles || '[]');
            const origen = opcion.text.split(' (')[0];
            let html, boton_ok = true;
            mensajeConfirmacion = '';

            if (!roles.length) {
                html = '<i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> <strong>' + esc(origen) + '</strong> no tiene ningún perfil asignado, no hay nada que copiar.';
                boton_ok = false;
            } else if (roles.length > 1) {
                html = '<i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> <strong>' + esc(origen) + '</strong> tiene varios perfiles (' + roles.map(r => esc(r.name)).join(', ') + ') de la forma antigua. Cada usuario debe tener uno solo: elige otro compañero o asigna el perfil manualmente arriba.';
                boton_ok = false;
            } else if (misRoles.length === 1 && misRoles[0] === roles[0].id) {
                html = '<i class="bi bi-info-circle-fill text-primary me-1"></i> Este usuario ya tiene el perfil <strong>' + esc(roles[0].name) + '</strong>. No hay nada que copiar.';
                boton_ok = false;
            } else {
                const otros = actuales.filter(n => n !== roles[0].name);
                html = '<i class="bi bi-check-circle-fill text-success me-1"></i> Se asignará el perfil <span class="badge bg-primary-subtle text-primary border">' + esc(roles[0].name) + '</span>'
                     + (otros.length ? '<div class="text-danger mt-1"><i class="bi bi-arrow-repeat me-1"></i>Reemplaza el perfil actual: ' + otros.map(esc).join(', ') + '</div>' : '')
                     + '<div class="text-muted mt-1">Sus permisos y menús llegan automáticamente desde ese perfil.</div>';
                mensajeConfirmacion = 'Se asignará el perfil ' + roles[0].name + ' a ' + nombreDestino + (otros.length ? ' y se quitará: ' + otros.join(', ') : '') + '. ¿Continuar?';
            }
            aviso.innerHTML = html;
            aviso.classList.remove('d-none');
            boton.disabled = !boton_ok;
        });
        form.addEventListener('submit', function (e) {
            if (!mensajeConfirmacion || !confirm(mensajeConfirmacion)) e.preventDefault();
        });
    })();
</script>
