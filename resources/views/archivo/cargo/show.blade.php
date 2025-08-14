<x-base-layout>

    {{-- Se asume que los estilos de card-friendly, btn-hover-lift, etc., ya están en un CSS global --}}
    {{-- Si no es así, puedes añadir aquí el bloque <style> de las respuestas anteriores. --}}

    <div class="row g-4 animate-on-load">

        {{-- COLUMNA PRINCIPAL (8) --}}
        <div class="col-lg-8">
            {{-- TARJETA DE DETALLES DEL CARGO --}}
            <div class="card card-friendly card-friendly-primary h-100">
                <div class="card-body p-4">
                    {{-- Cabecera --}}
                    <div class="d-flex align-items-center mb-4">
                        <i class="bi bi-briefcase-fill fs-1 text-primary me-3"></i>
                        <div>
                            <h4 class="card-title mb-0">{{ $cargo->nombre_cargo }}</h4>
                            <small class="text-muted">Detalles del Puesto</small>
                        </div>
                    </div>
                    
                    {{-- Sección de Detalles Generales --}}
                    <div class="row g-3">
                        <div class="col-md-6 detail-item">
                            <i class="bi bi-building text-secondary"></i>
                            <div>
                                <small class="text-muted">Área Funcional</small>
                                <p class="fw-medium mb-0">{{ $cargo->gdoArea->nombre ?? 'Sin área asignada' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6 detail-item">
                            <i class="bi bi-toggle-on text-success"></i>
                            <div>
                                <small class="text-muted">Estado</small>
                                <p class="fw-medium mb-0">
                                    @if($cargo->estado)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 detail-item">
                            <i class="bi bi-cash-coin text-success"></i>
                            <div>
                                <small class="text-muted">Salario Base</small>
                                <p class="fw-medium mb-0">{{ $cargo->salario_base !== null ? '$'.number_format($cargo->salario_base, 2, ',', '.') : 'No definido' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6 detail-item">
                            <i class="bi bi-clock-history text-secondary"></i>
                            <div>
                                <small class="text-muted">Jornada</small>
                                <p class="fw-medium mb-0">{{ $cargo->jornada ?? 'No definida' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Sección de Observación --}}
                    <hr class="my-4">
                    <div class="detail-item">
                        <i class="bi bi-journal-text text-secondary"></i>
                        <div>
                            <small class="text-muted">Observación</small>
                            <p class="mb-0">{{ $cargo->observacion ?? 'Sin observaciones.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLUMNA SECUNDARIA (4) --}}
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4">

                {{-- TARJETA DEL EMPLEADO ASIGNADO --}}
                <div class="card card-friendly card-friendly-secondary">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-4">
                        <h6 class="text-muted fw-bold text-uppercase small mb-3">Empleado Asignado</h6>
                        @if($cargo->empleado)
                            <i class="bi bi-person-check-fill text-success mb-2" style="font-size: 3rem;"></i>
                            <p class="fw-bold mb-0">{{ $cargo->empleado->nombre_completo }}</p>
                            <small class="text-muted">C.C. {{ $cargo->empleado->cedula }}</small>
                        @else
                            <i class="bi bi-person-x-fill text-muted mb-2" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-0">Cargo Vacante</p>
                        @endif
                    </div>
                </div>

                {{-- TARJETA DE CONTACTO CORPORATIVO --}}
                <div class="card card-friendly">
                    <div class="card-body p-4">
                        <h6 class="text-muted fw-bold text-uppercase small mb-3">Contacto Corporativo</h6>
                        <div class="d-flex flex-column gap-3">
                            <div class="detail-item"><i class="bi bi-telephone text-secondary"></i> <div>{{ $cargo->telefono_corporativo ?? 'N/A' }}</div></div>
                            <div class="detail-item"><i class="bi bi-phone text-secondary"></i> <div>{{ $cargo->celular_corporativo ?? 'N/A' }}</div></div>
                            <div class="detail-item"><i class="bi bi-hash text-secondary"></i> <div>Ext. {{ $cargo->ext_corporativo ?? 'N/A' }}</div></div>
                            <div class="detail-item"><i class="bi bi-envelope text-secondary"></i> <div>{{ $cargo->correo_corporativo ?? 'N/A' }}</div></div>
                        </div>
                    </div>
                </div>

                {{-- TARJETA DEL MANUAL DE FUNCIONES --}}
                <div class="card card-friendly">
                    <div class="card-body text-center p-4">
                         <h6 class="text-muted fw-bold text-uppercase small mb-3">Manual de Funciones</h6>
                        @if ($cargo->manual_funciones)
                            <i class="bi bi-file-earmark-check-fill text-success fs-1 mb-2"></i><br>
                            <a href="{{ route('archivo.cargo.verManual', $cargo->id) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Ver Documento
                            </a>
                        @else
                            <i class="bi bi-file-earmark-excel-fill text-muted fs-1 mb-2"></i>
                            <p class="text-muted mb-0 mt-2"><small>No se ha cargado.</small></p>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Botones de acción alineados y con diseño consistente --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-end gap-3">
                <a href="{{ route('archivo.cargo.index') }}" class="btn btn-light rounded-pill px-4 py-2">
                    Volver
                </a>
                <a href="{{ route('archivo.cargo.edit', $cargo->id) }}" class="btn btn-primary rounded-pill px-4 py-2 btn-hover-lift">
                    <i class="bi bi-pencil-square me-1"></i> Editar Cargo
                </a>
            </div>
        </div>
    </div>
</x-base-layout>