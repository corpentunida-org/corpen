@php
    // Volvemos a traer los usuarios y roles directamente aquí
    $usuariosDisponibles = \App\Models\User::orderBy('name', 'asc')->get();
    $rolesDisponibles = \App\Models\Interacciones\IntRole::orderBy('name', 'asc')->get();
@endphp

<div class="modal fade" id="modalInviteUsers" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('interactions.conversations.addParticipants') }}" method="POST">
                @csrf
                <input type="hidden" name="conversation_id" value="{{ $activeConversation->id }}">
                
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-user-plus me-2"></i>Invitar a Sala: {{ $activeConversation->name ?? 'Chat Grupal' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Buscar y Seleccionar Usuarios</label>
                        <select class="form-select" id="select-invite-users" name="user_ids[]" multiple="multiple" required style="width: 100%;">
                            @foreach($usuariosDisponibles as $user)
                                @if($user->id !== auth()->id())
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <small class="text-muted mt-1 d-block">Escribe el nombre para buscar. Puedes elegir varios.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Rol en esta Sala</label>
                        <select class="form-select" name="role_id" required>
                            <option value="">Seleccione el nivel de permisos...</option>
                            @foreach($rolesDisponibles as $rol)
                                <option value="{{ $rol->id }}">{{ $rol->name }} ({{ $rol->description }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-check me-1"></i> Agregar Participantes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Inicializar Select2 al abrir el modal para evitar bugs visuales
        $('#modalInviteUsers').on('shown.bs.modal', function () {
            $('#select-invite-users').select2({
                dropdownParent: $('#modalInviteUsers'), // CRÍTICO: Para que no se esconda detrás del modal
                placeholder: "Buscar usuarios...",
                allowClear: true,
                language: {
                    noResults: function() {
                        return "No se encontraron usuarios";
                    }
                }
            });
        });

        // Limpiar al cerrar por si el usuario se arrepiente
        $('#modalInviteUsers').on('hidden.bs.modal', function () {
            $('#select-invite-users').val(null).trigger('change');
        });
    });
</script>
@endpush