@php
    // Inyectamos las áreas activas directamente usando tu modelo.
    // Esto garantiza que el select siempre se llene correctamente.
    $areasDisponibles = \App\Models\Archivo\GdoArea::active()->orderBy('nombre', 'asc')->get();
@endphp

<div class="modal fade" id="modalCreateWorkspace" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('interactions.workspaces.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Crear Espacio de Trabajo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Espacio</label>
                        <input type="text" class="form-control" name="name" required placeholder="Ej: Cartera VIP">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Área (Departamento)</label>
                        <select class="form-select" name="area_id" required>
                            <option value="">Seleccione un área...</option>
                            @foreach($areasDisponibles as $area)
                                <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Workspace</button>
                </div>
            </form>
        </div>
    </div>
</div>