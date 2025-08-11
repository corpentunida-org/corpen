{{-- resources/views/archivo/area/show.blade.php --}}
<x-base-layout>
    <div class="card">
        <div class="card-body">
            <h5 class="fw-bold mb-4">Detalle del Área</h5>

            <div class="row g-3">
                {{-- Columna Izquierda --}}
                <div class="col-md-6">
                    <p class="mb-2"><strong>Nombre del Área:</strong><br> {{ $area->nombre ?? '-' }}</p>
                    <p class="mb-2"><strong>Estado:</strong><br> 
                        @if($area->estado === 'activo')
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-danger">Inactivo</span>
                        @endif
                    </p>
                </div>

                {{-- Columna Derecha --}}
                <div class="col-md-6">
                    <p class="mb-2"><strong>Fecha de Creación:</strong><br> {{ $area->created_at->format('d/m/Y') }}</p>
                    <p class="mb-2"><strong>Última Actualización:</strong><br> {{ $area->updated_at->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jefe del Área</label>
                    <input type="text" class="form-control" 
                        value="{{ $area->jefeCargo->nombre_cargo ?? 'Sin asignar' }}" 
                        disabled>
                </div>


                {{-- Sección de Descripción --}}
                <div class="col-md-12 mt-4">
                    <p><strong>Descripción:</strong><br> {{ $area->descripcion ?? 'Sin descripción.' }}</p>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('archivo.area.edit', $area->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil-square me-2"></i> Editar
                </a>
                <a href="{{ route('archivo.area.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i> Volver
                </a>
            </div>
        </div>
    </div>
</x-base-layout>
