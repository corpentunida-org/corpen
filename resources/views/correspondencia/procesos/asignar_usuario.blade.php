<div class="modal fade" id="modalAsignar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('correspondencia.procesos.asignarUsuario', $proceso) }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 15px;">
            @csrf
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Asignar Integrante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Seleccionar Usuario</label>
                    <select name="user_id" class="form-select select-picker" required>
                        <option value="">Buscar usuario...</option>
                        @foreach(\App\Models\User::all() as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Nota o Rol del usuario</label>
                    <input type="text" name="detalle" class="form-control" placeholder="Ej: Aprobador nivel 1, Revisor técnico...">
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary px-4">Confirmar Asignación</button>
            </div>
        </form>
    </div>
</div>