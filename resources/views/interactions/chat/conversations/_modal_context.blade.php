@php
    $usuariosDisponibles = \App\Models\User::orderBy('name', 'asc')->get();
    $rolesDisponibles = \App\Models\Interacciones\IntRole::orderBy('name', 'asc')->get();
@endphp

<div class="modal fade" id="modalContextChat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px"> {{-- Ancho optimizado Metronic --}}
        <div class="modal-content shadow-lg border-0">
            <form action="{{ route('interactions.conversations.storeContextual') }}" method="POST" id="form-create-room">
                @csrf
                <input type="hidden" name="workspace_id" value="{{ $activeWorkspace->id }}">
                
                <div class="modal-header border-0 pt-7 px-7">
                    <h2 class="fw-bolder">
                        <i class="bi bi-chat-square-dots-fill text-primary fs-2 me-2"></i>Nueva Sala
                    </h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="bi bi-x fs-1"></i>
                    </div>
                </div>
                
                <div class="modal-body py-5 px-7">
                    <div class="mb-7">
                        <label class="form-label fw-bold fs-6 text-gray-700">Nombre de la Sala</label>
                        <input type="text" class="form-control form-control-solid" name="name" placeholder="Ej: Revisión de Casos #102" required>
                    </div>

                    <div class="mb-7">
                        <label class="form-label fw-bold fs-6 mb-3">Visibilidad</label>
                        <div class="d-flex flex-stack border border-dashed border-gray-300 rounded p-4 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="form-check form-check-custom form-check-solid me-3">
                                    <input class="form-check-input" type="radio" name="visibility" value="internal" id="vis_internal" checked />
                                </div>
                                <label class="ms-2" for="vis_internal">
                                    <span class="fw-bolder text-gray-800 d-block">Privada</span>
                                    <span class="text-muted fs-7">Solo invitados pueden verla</span>
                                </label>
                            </div>
                        </div>
                        <div class="d-flex flex-stack border border-dashed border-gray-300 rounded p-4">
                            <div class="d-flex align-items-center">
                                <div class="form-check form-check-custom form-check-solid me-3">
                                    <input class="form-check-input" type="radio" name="visibility" value="external" id="vis_external" />
                                </div>
                                <label class="ms-2" for="vis_external">
                                    <span class="fw-bolder text-gray-800 d-block">Pública</span>
                                    <span class="text-muted fs-7">Abierta para todo el equipo</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="fv-row mb-7 border-top pt-5">
                        <label class="fs-6 fw-bold text-gray-700 mb-2">
                            <i class="bi bi-person-plus-fill me-1 text-primary"></i> Invitar Miembros
                        </label>
                        
                        <div class="mb-3">
                            <select class="form-select form-select-solid" id="select-add-participant" data-control="select2">
                                <option value="">Buscar usuario...</option>
                                @foreach($usuariosDisponibles as $user)
                                    @if($user->id !== auth()->id())
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div id="participants-container" class="rounded bg-light-neutral p-3 custom-scroll" style="min-height: 80px; max-height: 250px; overflow-y: auto;">
                            <div class="text-center py-4 opacity-50" id="empty-participants-msg">
                                <i class="bi bi-people fs-2x mb-2 d-block"></i>
                                <span class="fs-7">No hay invitados aún</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded bg-light-info p-5 border border-info border-dashed">
                        <label class="fs-7 fw-bolder text-info mb-3 text-uppercase">
                            <i class="bi bi-link-45deg me-1"></i> Vincular Registro (Opcional)
                        </label>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <select class="form-select form-select-sm form-select-solid" name="chatable_type">
                                    <option value="">No vincular</option>
                                    <option value="App\Models\Interacciones\IntSeguimiento">Seguimiento</option>
                                    <option value="App\Models\Interacciones\Interaction">Interacción</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <input type="number" class="form-control form-control-sm form-control-solid" name="chatable_id" placeholder="ID del registro">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-0 pt-0 pb-10 px-7 justify-content-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-10">
                        <span class="indicator-label"><i class="fa fa-save me-1"></i> Crear Sala</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let participantIndex = 0;
        
        const rolesHtml = `
            <option value="">Rol...</option>
            @foreach($rolesDisponibles as $rol)
                <option value="{{ $rol->id }}">{{ $rol->name }}</option>
            @endforeach
        `;

        $('#modalContextChat').on('shown.bs.modal', function () {
            $('#select-add-participant').select2({
                dropdownParent: $('#modalContextChat'),
                placeholder: "Buscar y agregar usuario...",
                allowClear: true
            });
        });

        $('#select-add-participant').on('select2:select', function (e) {
            let userId = e.params.data.id;
            let userName = e.params.data.text;

            if ($(`#participant-row-${userId}`).length > 0) {
                $(this).val(null).trigger('change');
                return;
            }

            $('#empty-participants-msg').hide();

            // MEJORA UX: Fila responsiva (se adapta a móvil)
            let rowHtml = `
                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between bg-white rounded p-3 mb-2 shadow-sm border participant-row" id="participant-row-${userId}">
                    <div class="d-flex align-items-center mb-2 mb-sm-0">
                        <div class="symbol symbol-30px symbol-circle me-3">
                            <span class="symbol-label bg-light-primary text-primary fw-bold text-uppercase">${userName.substring(0, 1)}</span>
                        </div>
                        <div class="fw-bolder text-gray-800 text-truncate fs-7" style="max-width: 150px;">${userName}</div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <input type="hidden" name="participants[${participantIndex}][user_id]" value="${userId}">
                        <select class="form-select form-select-sm form-select-solid w-125px" name="participants[${participantIndex}][role_id]" required>
                            ${rolesHtml}
                        </select>
                        <button type="button" class="btn btn-icon btn-sm btn-light-danger btn-remove-participant" data-id="${userId}">
                            <i class="bi bi-trash-fill fs-6"></i>
                        </button>
                    </div>
                </div>
            `;

            $('#participants-container').append(rowHtml);
            participantIndex++;
            $(this).val(null).trigger('change');
        });

        $(document).on('click', '.btn-remove-participant', function() {
            let userId = $(this).data('id');
            $(`#participant-row-${userId}`).remove();
            if ($('.participant-row').length === 0) {
                $('#empty-participants-msg').show();
            }
        });

        $('#modalContextChat').on('hidden.bs.modal', function () {
            $('#select-add-participant').val(null).trigger('change');
            $('.participant-row').remove();
            $('#empty-participants-msg').show();
            document.getElementById('form-create-room').reset();
        });
    });
</script>
@endpush