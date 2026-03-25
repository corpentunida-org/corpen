@php
    // Traemos a los usuarios activos de tu sistema para poder invitarlos
    // Ajusta el modelo 'App\Models\User' si el tuyo está en otra ruta o si usas un 'scopeActive'
    $usuariosDisponibles = \App\Models\User::orderBy('name', 'asc')->get();
@endphp

<div class="modal fade" id="modalContextChat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('interactions.conversations.storeContextual') }}" method="POST">
                @csrf
                <input type="hidden" name="workspace_id" value="{{ $activeWorkspace->id }}">
                
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-comments me-2"></i>Nueva Sala en {{ $activeWorkspace->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nombre de la Sala</label>
                        <input type="text" class="form-control" name="name" placeholder="Ej: Revisión de Deuda Cliente X" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Visibilidad de la Sala</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="visibility" id="vis_internal" value="internal" checked>
                            <label class="form-check-label" for="vis_internal">
                                <strong>Privada:</strong> Solo los miembros invitados podrán verla.
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="visibility" id="vis_external" value="external">
                            <label class="form-check-label" for="vis_external">
                                <strong>Pública:</strong> Cualquier miembro del Workspace puede unirse.
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Invitar Miembros (Opcional)</label>
                        <select class="form-select" name="user_ids[]" multiple style="height: 100px;">
                            @foreach($usuariosDisponibles as $user)
                                @if($user->id !== auth()->id())
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <small class="text-muted">Mantén presionado Ctrl (Windows) o Cmd (Mac) para seleccionar varios.</small>
                    </div>

                    <div class="mb-3 border p-3 rounded bg-light">
                        <label class="form-label fw-bold text-muted mb-2">
                            <i class="fa fa-link me-1"></i> Vincular a un registro existente (Opcional)
                        </label>
                        <div class="row g-2">
                            <div class="col-md-5">
                                <select class="form-select form-select-sm" name="chatable_type">
                                    <option value="">No vincular</option>
                                    <option value="App\Models\Interacciones\IntSeguimiento">Seguimiento</option>
                                    <option value="App\Models\Interacciones\Interaction">Interacción</option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <input type="number" class="form-control form-select-sm" name="chatable_id" placeholder="ID del registro (Ej: 105)">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i> Crear Sala
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>