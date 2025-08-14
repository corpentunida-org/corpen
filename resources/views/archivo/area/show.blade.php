<x-base-layout>

    {{-- Estilos para una apariencia más amigable y refinada --}}
    <style>
        .card-friendly {
            border-radius: .75rem; /* Esquinas más suaves */
            border: 1px solid #e9ecef; /* Un borde muy sutil */
            transition: all 0.3s ease;
        }
        /* Sombra de color sutil que reemplaza el borde lateral */
        .card-friendly-primary {
            box-shadow: -.25rem 0 1rem -.5rem rgba(var(--bs-primary-rgb), 0.5);
        }
        .card-friendly-secondary {
            box-shadow: -.25rem 0 1rem -.5rem rgba(var(--bs-secondary-rgb), 0.5);
        }
        /* Efecto de "levantarse" al pasar el mouse sobre los botones */
        .btn-hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        /* Alineación y espaciado para los detalles con iconos */
        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.75rem; /* Espacio entre el icono y el texto */
        }
        .detail-item i {
            font-size: 1.25rem;
        }
    </style>

    <div class="row g-4">
        {{-- Tarjeta de Información del Área --}}
        <div class="col-lg-8">
            <div class="card card-friendly card-friendly-primary h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <i class="bi bi-building fs-1 text-primary me-3"></i>
                        <div>
                            <h4 class="card-title mb-0">{{ $area->nombre ?? '-' }}</h4>
                            <small class="text-muted">Detalles del Área</small>
                        </div>
                    </div>

                    <div class="row gy-3">
                        <div class="col-md-6 detail-item">
                            <i class="bi bi-toggle-on text-success"></i>
                            <div>
                                <small class="text-muted">Estado</small>
                                <p class="fw-medium mb-0">
                                    @if($area->estado === 'activo')
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 detail-item">
                            <i class="bi bi-person-workspace text-secondary"></i>
                            <div>
                                <small class="text-muted">Cargo de Coordinación</small>
                                <p class="fw-medium mb-0">{{ $area->jefeCargo->nombre_cargo ?? 'No asignado' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6 detail-item">
                            <i class="bi bi-calendar-plus text-secondary"></i>
                            <div>
                                <small class="text-muted">Fecha de Creación</small>
                                <p class="fw-medium mb-0">{{ $area->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                         <div class="col-md-6 detail-item">
                            <i class="bi bi-calendar-check text-secondary"></i>
                            <div>
                                <small class="text-muted">Última Actualización</small>
                                <p class="fw-medium mb-0">{{ $area->updated_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="detail-item">
                         <i class="bi bi-file-text text-secondary"></i>
                         <div>
                            <small class="text-muted">Descripción</small>
                            <p class="mb-0">{{ $area->descripcion ?? 'Sin descripción proporcionada.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tarjeta de Encargado --}}
        <div class="col-lg-4">
            <div class="card card-friendly card-friendly-secondary h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-4">
                    @if ($area->jefeCargo)
                        @if ($area->jefeCargo->empleado)
                            <i class="bi bi-person-check-fill text-success mb-3" style="font-size: 4.5rem;"></i>
                            <h6 class="card-title mb-0"><strong>{{ $area->jefeCargo->empleado->nombre_completo }}</strong></h6>
                            <p class="text-muted">{{ $area->jefeCargo->nombre_cargo }}</p>
                            <a class="btn btn-sm btn-outline-secondary mt-2 btn-hover-lift" href="{{ route('archivo.cargo.show', $area->jefeCargo->id) }}">
                                <i class="bi bi-eye me-1"></i> Ver Perfil
                            </a>
                        @else
                            <i class="bi bi-person-fill-exclamation text-warning mb-3" style="font-size: 4.5rem;"></i>
                            <h6 class="card-title fw-bold">Puesto Vacante</h6>
                            <p class="text-muted mb-3">El cargo de "{{ $area->jefeCargo->nombre_cargo }}" necesita un responsable.</p>
                            <a class="btn btn-primary btn-hover-lift" href="{{ route('archivo.cargo.show', $area->jefeCargo->id) }}">
                                <i class="bi bi-search me-1"></i> Gestionar Cargo
                            </a>
                        @endif
                    @else
                        <i class="bi bi-person-x-fill text-muted mb-3" style="font-size: 4.5rem;"></i>
                        <h6 class="card-title fw-bold">No Definido</h6>
                        <p class="text-muted">No hay un puesto de mando para esta área.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Botones de Acción perfectamente alineados --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-end gap-3">
                <a href="{{ route('archivo.area.index') }}" class="btn btn-outline-secondary rounded-pill px-4 btn-hover-lift" data-bs-toggle="tooltip" data-bs-title="Regresar al listado de áreas">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
                <a href="{{ route('archivo.area.edit', $area->id) }}" class="btn btn-primary rounded-pill px-4 btn-hover-lift" data-bs-toggle="tooltip" data-bs-title="Modificar esta área">
                    <i class="bi bi-pencil-square me-1"></i> Editar
                </a>
            </div>
        </div>
    </div>

</x-base-layout>

{{-- Script para inicializar los tooltips de Bootstrap --}}
@push('scripts')
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush