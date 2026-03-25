@php
    // Inyectamos las áreas activas directamente usando tu modelo.
    $areasDisponibles = \App\Models\Archivo\GdoArea::active()->orderBy('nombre', 'asc')->get();
@endphp

<div class="modal fade" id="modalCreateWorkspace" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px"> {{-- Ancho profesional Metronic --}}
        <div class="modal-content shadow-lg border-0">
            
            <form action="{{ route('interactions.workspaces.store') }}" method="POST" id="form-create-workspace">
                @csrf
                
                <div class="modal-header border-0 pt-7 px-7">
                    <h2 class="fw-bolder">
                        <i class="bi bi-folder-plus text-success fs-2 me-2"></i>Nuevo Espacio de Trabajo
                    </h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="bi bi-x fs-1"></i>
                    </div>
                </div>

                <div class="modal-body py-5 px-7">
                    <div class="text-muted mb-8 fs-6">
                        Define un nuevo entorno para organizar salas de chat por departamentos o proyectos específicos.
                    </div>

                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 text-gray-700 mb-2">Nombre del Espacio</label>
                        <input type="text" 
                               class="form-control form-control-solid" 
                               name="name" 
                               placeholder="Ej: Cartera VIP o Auditoría 2024" 
                               required>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 text-gray-700 mb-2">Área (Departamento)</label>
                        <select class="form-select form-select-solid" 
                                name="area_id" 
                                data-control="select2" 
                                data-placeholder="Selecciona un departamento..."
                                required>
                            <option value=""></option>
                            @foreach($areasDisponibles as $area)
                                <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">Vincular un área ayuda a segmentar los permisos del equipo.</div>
                    </div>

                    <div class="fv-row mb-3">
                        <label class="form-label fw-bold fs-6 text-gray-700 mb-2">Descripción (Opcional)</label>
                        <textarea class="form-control form-control-solid" 
                                  name="description" 
                                  rows="3" 
                                  placeholder="¿De qué trata este espacio de trabajo?"></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 pb-10 px-7 justify-content-center">
                    <button type="button" class="btn btn-light me-3 px-8" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-10 shadow-sm">
                        <i class="bi bi-check-circle-fill me-2"></i> Crear Workspace
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Inicializar select2 si el modal se abre para que no falle el diseño
        $('#modalCreateWorkspace').on('shown.bs.modal', function () {
            $(this).find('[data-control="select2"]').select2({
                dropdownParent: $('#modalCreateWorkspace'),
                minimumResultsForSearch: 5
            });
        });

        // Limpiar formulario al cerrar
        $('#modalCreateWorkspace').on('hidden.bs.modal', function () {
            document.getElementById('form-create-workspace').reset();
            $(this).find('[data-control="select2"]').val(null).trigger('change');
        });
    });
</script>
@endpush