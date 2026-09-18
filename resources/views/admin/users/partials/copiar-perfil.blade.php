{{-- Requiere: $user, $usuarios (otros usuarios del mismo tipo, para copiar su acceso). --}}
<div class="ui-card mb-4">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex align-items-center mb-3">
            <div class="ui-icon-box ui-pastel-amber me-3" style="width: 38px; height: 38px; font-size: 1rem;">
                <i class="bi bi-copy"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-0 fs-15">Copiar Perfil de Otro Usuario</h6>
                <p class="text-muted fs-13 mb-0 mt-1">
                    Suma a este usuario los perfiles y permisos que ya tiene un compañero de referencia,
                    sin quitarle nada de lo que ya tiene.
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.copiar-permisos', $user) }}"
              onsubmit="return confirm('¿Copiar el acceso de este usuario? Se sumarán sus perfiles y permisos a los que ya tiene {{ $user->name }}, sin quitar nada.');">
            @csrf
            <div class="row g-2">
                <div class="col-md-8">
                    <select class="form-select ui-input cursor-pointer" name="usuario_referencia_id" required>
                        <option value="" disabled selected>Seleccione un usuario de referencia...</option>
                        @foreach ($usuarios as $otro)
                            <option value="{{ $otro->id }}">{{ $otro->name }} ({{ $otro->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-primary w-100 fw-semibold">
                        <i class="bi bi-copy me-1"></i> Copiar acceso
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
