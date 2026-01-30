<x-base-layout>

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
        }

        .view-container { animation: fadeIn 0.5s ease-out; }
        
        .card-modern {
            border: none;
            border-radius: 1.25rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            background: var(--glass-bg);
            overflow: hidden;
        }

        /* Cabecera con gradiente sutil */
        .card-header-custom {
            background: #f8fafc;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.5rem;
        }

        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            font-weight: 700;
            display: block;
            margin-bottom: 0.25rem;
        }

        .info-value {
            color: #1e293b;
            font-weight: 600;
            font-size: 1rem;
        }

        /* Estilo para el Avatar del Jefe */
        .jefe-avatar-wrapper {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto 1.5rem;
        }

        .jefe-avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 4px solid #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .btn-hover-lift {
            transition: all 0.3s ease;
        }

        .btn-hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="view-container">
        {{-- BREADCRUMB / CABECERA --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('archivo.area.index') }}" class="text-decoration-none">Áreas</a></li>
                        <li class="breadcrumb-item active">Detalle</li>
                    </ol>
                </nav>
                <h3 class="fw-bold text-dark mb-0">{{ $area->nombre }}</h3>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('archivo.area.index') }}" class="btn btn-light border-0 px-3 rounded-3 btn-hover-lift">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
                <a href="{{ route('archivo.area.edit', $area->id) }}" class="btn btn-primary px-4 rounded-3 btn-hover-lift shadow-sm">
                    <i class="bi bi-pencil-square me-1"></i> Editar Área
                </a>
            </div>
        </div>

        <div class="row g-4">
            {{-- COLUMNA IZQUIERDA: DATOS --}}
            <div class="col-lg-8">
                <div class="card card-modern h-100">
                    <div class="card-header-custom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary-subtle p-2 rounded-3 me-3">
                                <i class="bi bi-info-circle-fill text-primary fs-4"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">Información General</h5>
                        </div>
                        @if($area->estado === 'activo')
                            <span class="status-badge bg-success-subtle text-success border border-success-subtle">
                                <i class="bi bi-check-circle-fill me-1"></i> Área Activa
                            </span>
                        @else
                            <span class="status-badge bg-danger-subtle text-danger border border-danger-subtle">
                                <i class="bi bi-x-circle-fill me-1"></i> Inactiva
                            </span>
                        @endif
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="row mb-4">
                            <div class="col-12">
                                <span class="info-label">Descripción del Área</span>
                                <p class="text-muted lh-base">
                                    {{ $area->descripcion ?? 'No se ha proporcionado una descripción detallada para este departamento organizacional.' }}
                                </p>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-light-subtle">
                                    <span class="info-label">Fecha de Apertura</span>
                                    <div class="info-value d-flex align-items-center">
                                        <i class="bi bi-calendar-event me-2 text-primary"></i>
                                        {{ $area->created_at ? $area->created_at->format('d/m/Y') : 'Sin registro' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-light-subtle">
                                    <span class="info-label">Última Modificación</span>
                                    <div class="info-value d-flex align-items-center">
                                        <i class="bi bi-clock-history me-2 text-primary"></i>
                                        {{ $area->updated_at ? $area->updated_at->diffForHumans() : 'Sin registro' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-light-subtle">
                                    <span class="info-label">Cargos Vinculados</span>
                                    <div class="info-value d-flex align-items-center">
                                        <i class="bi bi-person-gear me-2 text-primary"></i>
                                        {{ $area->cargos->count() }} Cargos registrados
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-light-subtle">
                                    <span class="info-label">ID de Área</span>
                                    <div class="info-value text-muted font-monospace">
                                        #AREA-{{ str_pad($area->id, 4, '0', STR_PAD_LEFT) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: JEFE / RESPONSABLE --}}
            <div class="col-lg-4">
                <div class="card card-modern">
                    <div class="card-body p-4 text-center">
                        <span class="info-label mb-4">Liderazgo / Coordinación</span>
                        
                        <div class="jefe-avatar-wrapper">
                            <div class="jefe-avatar">
                                @if($area->jefeCargo && $area->jefeCargo->empleado)
                                    <span class="fs-1 fw-bold text-primary">{{ strtoupper(substr($area->jefeCargo->empleado->nombre1, 0, 1)) }}</span>
                                @else
                                    <i class="bi bi-person-x fs-1 text-muted"></i>
                                @endif
                            </div>
                            @if($area->jefeCargo && $area->jefeCargo->empleado)
                                <span class="position-absolute bottom-0 end-0 bg-success border border-white border-3 p-2 rounded-circle" title="Asignado"></span>
                            @endif
                        </div>

                        @if($area->jefeCargo)
                            <h5 class="fw-bold mb-1">
                                {{ $area->jefeCargo->empleado ? $area->jefeCargo->empleado->nombre_completo : 'Puesto Vacante' }}
                            </h5>
                            <p class="text-primary small fw-bold text-uppercase mb-3">{{ $area->jefeCargo->nombre_cargo }}</p>
                            
                            @if($area->jefeCargo->empleado)
                                <div class="d-grid">
                                    <a href="{{ route('archivo.cargo.show', $area->jefeCargo->id) }}" class="btn btn-outline-primary btn-sm rounded-pill py-2">
                                        <i class="bi bi-eye me-1"></i> Ver Perfil Profesional
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-warning py-2 small mb-0">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Requiere contratación
                                </div>
                            @endif
                        @else
                            <h5 class="text-muted fw-bold mb-1">No Asignado</h5>
                            <p class="text-muted small px-3">Esta área no tiene un cargo de mando definido en el organigrama.</p>
                            <div class="d-grid mt-3">
                                <a href="{{ route('archivo.area.edit', $area->id) }}" class="btn btn-light btn-sm rounded-pill">
                                    Asignar Jefe de Área
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- PEQUEÑA TARJETA DE ACCESO RÁPIDO --}}
                <div class="card card-modern mt-4 bg-primary text-white">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-diagram-3 fs-3 me-3"></i>
                            <div>
                                <h6 class="mb-1 fw-bold">Organigrama</h6>
                                <p class="small mb-0 opacity-75">Visualizar cómo encaja esta área en la empresa.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-base-layout>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el) });
    });
</script>
@endpush