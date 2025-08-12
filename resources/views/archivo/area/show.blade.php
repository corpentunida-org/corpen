{{-- resources/views/archivo/area/show.blade.php --}}
<x-base-layout>
    <div class="row">
        {{-- Tarjeta principal del Área --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0" style="border-left: 5px solid #f4a261;">
                <div class="card-header text-white" style="background-color: #264653;">
                    <i class="bi bi-building me-2"></i> Área: {{ $area->nombre ?? '-' }}
                </div>
                <div class="card-body" style="background-color: #fffefc;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Estado:</strong><br>
                                @if($area->estado === 'activo')
                                    <span class="badge" style="background-color: #2a9d8f;">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </p>
                            <p class="mb-2"><strong>Fecha de Creación:</strong><br>
                                {{ $area->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Última Actualización:</strong><br>
                                {{ $area->updated_at->format('d/m/Y') }}
                            </p>
                            <p class="mb-2"><strong>Cargo de coordinación:</strong><br>
                                {{ $area->jefeCargo->nombre_cargo ?? 'Sin asignar' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-3">
                        <p><strong>Descripción:</strong><br> {{ $area->descripcion ?? 'Sin descripción.' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tarjeta secundaria: detalle del jefe asignado --}}
<div class="col-md-4">
    <div class="card shadow-sm border-0" style="border-left: 5px solid #e9c46a;">
        <div class="card-header text-white" style="background-color: #2a9d8f;">
            <i class="bi bi-person-badge-fill me-2"></i> Encargo de supervisión
        </div>
        <div class="card-body" style="background-color: #fdfcfb;">
            @if($area->jefeCargo)
                <p class="mb-2"><strong>Cargo:</strong><br> {{ $area->jefeCargo->nombre_cargo }}</p>

                @if($area->jefeCargo->empleado)
                    <p class="mb-2"><strong>Nombre:</strong><br> {{ $area->jefeCargo->empleado->nombre_completo }}</p>
                @else
                    <a class="text-decoration-none" href="{{ route('archivo.cargo.show', $area->jefeCargo->id) }}">
                        <i class="bi bi-eye me-2"></i> Ver los detalles de este cargo
                    </a>
                @endif
            @else
                <p class="text-muted">No hay "Puesto de mando" asignado a esta área.</p>
            @endif
        </div>
    </div>
</div>

    </div>

    {{-- Botones de acción --}}
    <div class="text-end mt-4">
        <a href="{{ route('archivo.area.edit', $area->id) }}"
           class="btn px-4 py-2 me-2"
           style="background-color: #e76f51; color: #fff; border-radius: 8px; transition: 0.3s;">
            <i class="bi bi-pencil-square me-2"></i> Editar
        </a>
        <a href="{{ route('archivo.area.index') }}"
           class="btn px-4 py-2"
           style="background-color: #2a9d8f; color: #fff; border-radius: 8px; transition: 0.3s;">
            <i class="bi bi-arrow-left me-2"></i> Volver
        </a>
    </div>

    {{-- Hover para botones --}}
    <style>
        a.btn:hover {
            opacity: 0.85;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
    </style>
</x-base-layout>
