@php
    $usuariosDisponibles = \App\Models\User::orderBy('name', 'asc')->get();
    $rolesDisponibles = \App\Models\Interacciones\IntRole::orderBy('name', 'asc')->get();
@endphp

<div class="modal fade" id="modalInviteUsers" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content shadow-lg border-0">
            <form action="{{ route('interactions.conversations.addParticipants') }}" method="POST" id="form-invite-participants">
                @csrf
                <input type="hidden" name="conversation_id" value="{{ $activeConversation->id ?? '' }}">
                
                <div class="modal-header border-0 pt-7 px-7">
                    <h2 class="fw-bolder">
                        <i class="bi bi-person-plus-fill text-primary fs-2 me-2"></i>Invitar a la Sala
                    </h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="bi bi-x fs-1"></i>
                    </div>
                </div>
                
                <div class="modal-body py-5 px-7">
                    <div class="text-muted mb-7 fs-6">
                        Agrega nuevos miembros a <span class="fw-bolder text-dark">{{ $activeConversation->name ?? 'esta sala' }}</span> y define sus permisos.
                    </div>

                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-bold text-gray-700 mb-2">Buscar y seleccionar</label>
                        <select class="form-select form-select-solid" id="select-invite-participant" data-control="select2">
                            <option value="">Escribe un nombre...</option>
                            @foreach($usuariosDisponibles as $user)
                                {{-- Evitamos mostrar al usuario actual --}}
                                @if($user->id !== auth()->id())
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="rounded bg-light-neutral p-4 custom-scroll" style="min-height: 100px; max-height: 300px; overflow-y: auto;">
                        <h6 class="fs-8 fw-bolder text-gray-600 text-uppercase mb-4">Invitados preparados:</h6>
                        
                        <div id="invite-list-container">
                            <div class="text-center py-5 opacity-50" id="empty-invite-msg">
                                <i class="bi bi-person-plus fs-2x mb-2 d-block"></i>
                                <span class="fs-7">Selecciona usuarios arriba para verlos aquí</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-0 pt-0 pb-10 px-7 justify-content-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-10">
                        <i class="bi bi-check-circle-fill me-2"></i> Confirmar Invitaciones
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let inviteIndex = 0;
        
        // Roles disponibles inyectados para el JS
        const rolesOptions = `
            <option value="">Asignar Rol...</option>
            @foreach($rolesDisponibles as $rol)
                <option value="{{ $rol->id }}">{{ $rol->name }}</option>
            @endforeach
        `;

        // Reinicializar Select2 cada vez que se abre
        $('#modalInviteUsers').on('shown.bs.modal', function () {
            $('#select-invite-participant').select2({
                dropdownParent: $('#modalInviteUsers'),
                placeholder: "Buscar usuarios...",
                allowClear: true
            });
        });

        // Al seleccionar un usuario en el buscador
        $('#select-invite-participant').on('select2:select', function (e) {
            const userId = e.params.data.id;
            const userName = e.params.data.text;

            // No duplicar en la lista
            if ($(`#invite-row-${userId}`).length > 0) {
                $(this).val(null).trigger('change');
                return;
            }

            $('#empty-invite-msg').hide();

            // Fila responsiva idéntica a la del modal de creación
            let rowHtml = `
                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between bg-white rounded p-3 mb-3 shadow-sm border invite-row" id="invite-row-${userId}">
                    <div class="d-flex align-items-center mb-2 mb-sm-0">
                        <div class="symbol symbol-35px symbol-circle me-3">
                            <span class="symbol-label bg-light-primary text-primary fw-bold">${userName.substring(0, 1).toUpperCase()}</span>
                        </div>
                        <div class="fw-bolder text-gray-800 fs-7 text-truncate" style="max-width: 180px;">${userName}</div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <input type="hidden" name="participants[${inviteIndex}][user_id]" value="${userId}">
                        <select class="form-select form-select-sm form-select-solid w-150px" name="participants[${inviteIndex}][role_id]" required>
                            ${rolesOptions}
                        </select>
                        <button type="button" class="btn btn-icon btn-sm btn-light-danger rounded-circle remove-invite" data-id="${userId}">
                            <i class="bi bi-trash fs-6"></i>
                        </button>
                    </div>
                </div>
            `;

            $('#invite-list-container').append(rowHtml);
            inviteIndex++;
            $(this).val(null).trigger('change');
        });

        // Quitar de la lista
        $(document).on('click', '.remove-invite', function() {
            const userId = $(this).data('id');
            $(`#invite-row-${userId}`).remove();
            if ($('.invite-row').length === 0) {
                $('#empty-invite-msg').show();
            }
        });

        // Limpiar al cerrar
        $('#modalInviteUsers').on('hidden.bs.modal', function () {
            $('#select-invite-participant').val(null).trigger('change');
            $('.invite-row').remove();
            $('#empty-invite-msg').show();
            document.getElementById('form-invite-participants').reset();
        });
    });
</script>
@endpush