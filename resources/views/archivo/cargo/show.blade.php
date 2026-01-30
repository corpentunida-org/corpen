<x-base-layout>
    {{-- Estilos locales para mantener la consistencia visual --}}
    <style>
        .card-friendly {
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        .detail-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .detail-item i {
            font-size: 1.25rem;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            border-radius: 8px;
        }
        .animate-on-load {
            animation: fadeInUp 0.5s ease-out;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .btn-hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>

    <div class="row g-4 animate-on-load">

        {{-- COLUMNA PRINCIPAL (8) --}}
        <div class="col-lg-8">
            {{-- TARJETA DE DETALLES DEL CARGO --}}
            <div class="card card-friendly h-100">
                <div class="card-body p-4">
                    {{-- Cabecera --}}
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-light-primary rounded-3 p-3 me-3">
                             <i class="bi bi-briefcase-fill fs-1 text-primary"></i>
                        </div>
                        <div>
                            <h4 class="card-title mb-0 fw-bold">{{ $cargo->nombre_cargo }}</h4>
                            <span class="text-muted small">ID de Cargo: #{{ $cargo->id }}</span>
                        </div>
                    </div>
                    
                    {{-- Sección de Detalles Generales --}}
                    <div class="row g-4 mt-2">
                        <div class="col-md-6 detail-item">
                            <i class="bi bi-building text-primary"></i>
                            <div>
                                <small class="text-muted d-block uppercase fw-bold" style="font-size: 0.65rem;">Área Funcional</small>
                                <p class="fw-medium mb-0">{{ $cargo->gdoArea->nombre ?? 'Sin área asignada' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6 detail-item">
                            <i class="bi bi-toggle-on text-success"></i>
                            <div>
                                <small class="text-muted d-block uppercase fw-bold" style="font-size: 0.65rem;">Estado Operativo</small>
                                <p class="fw-medium mb-0">
                                    @if($cargo->estado)
                                        <span class="badge bg-light-success text-success border border-success-subtle px-3">Activo</span>
                                    @else
                                        <span class="badge bg-light-danger text-danger border border-danger-subtle px-3">Inactivo</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 detail-item">
                            <i class="bi bi-cash-coin text-success"></i>
                            <div>
                                <small class="text-muted d-block uppercase fw-bold" style="font-size: 0.65rem;">Remuneración</small>
                                <p class="fw-bold mb-0 text-success">{{ $cargo->salario_base !== null ? '$'.number_format($cargo->salario_base, 0, ',', '.') : 'No definido' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6 detail-item">
                            <i class="bi bi-clock-history text-secondary"></i>
                            <div>
                                <small class="text-muted d-block uppercase fw-bold" style="font-size: 0.65rem;">Jornada Laboral</small>
                                <p class="fw-medium mb-0">{{ $cargo->jornada ?? 'No definida' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Sección de Observación --}}
                    <hr class="my-4" style="border-style: dashed;">
                    <div class="detail-item align-items-start">
                        <i class="bi bi-journal-text text-secondary mt-1"></i>
                        <div>
                            <small class="text-muted d-block uppercase fw-bold" style="font-size: 0.65rem;">Observaciones y Notas</small>
                            <p class="mb-0 text-dark">{{ $cargo->observacion ?? 'Sin observaciones adicionales registradas.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLUMNA SECUNDARIA (4) --}}
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4">

                {{-- TARJETA DEL EMPLEADO ASIGNADO --}}
                <div class="card card-friendly">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-4">
                        <h6 class="text-muted fw-bold text-uppercase small mb-3">Responsable del Cargo</h6>
                        @if($cargo->empleado)
                            <div class="symbol symbol-100px symbol-circle mb-3">
                                <i class="bi bi-person-check-fill text-success" style="font-size: 3.5rem;"></i>
                            </div>
                            <p class="fw-bold mb-0 fs-5 text-dark">{{ $cargo->empleado->nombre1 }} {{ $cargo->empleado->apellido1 }}</p>
                            <span class="badge bg-light-primary text-primary mt-1">C.C. {{ $cargo->empleado->cedula }}</span>
                        @else
                            <div class="symbol symbol-100px symbol-circle mb-3">
                                <i class="bi bi-person-x text-muted" style="font-size: 3.5rem;"></i>
                            </div>
                            <p class="text-muted mb-0 fw-bold">CARGO VACANTE</p>
                            <small class="text-muted">No hay un empleado asignado</small>
                        @endif
                    </div>
                </div>

                {{-- TARJETA DE CONTACTO CORPORATIVO --}}
                <div class="card card-friendly">
                    <div class="card-body p-4">
                        <h6 class="text-muted fw-bold text-uppercase small mb-4">Canales Corporativos</h6>
                        <div class="d-flex flex-column gap-3">
                            <div class="detail-item">
                                <i class="bi bi-telephone text-info"></i> 
                                <span class="small fw-medium">{{ $cargo->telefono_corporativo ?? 'N/A' }} 
                                    @if($cargo->ext_corporativo) <span class="text-muted">(Ext. {{ $cargo->ext_corporativo }})</span> @endif
                                </span>
                            </div>
                            <div class="detail-item">
                                <i class="bi bi-phone text-info"></i> 
                                <span class="small fw-medium">{{ $cargo->celular_corporativo ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-item">
                                <i class="bi bi-envelope-at text-info"></i> 
                                <span class="small fw-medium text-truncate" style="max-width: 200px;">{{ $cargo->correo_corporativo ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TARJETA DEL MANUAL DE FUNCIONES (S3 INTEGRADO) --}}
                <div class="card card-friendly bg-light-dark border-dashed border-secondary">
                    <div class="card-body text-center p-4">
                         <h6 class="text-muted fw-bold text-uppercase small mb-3">Documentación Técnica</h6>
                        @if ($cargo->manual_funciones)
                            <div class="mb-3">
                                <i class="bi bi-file-earmark-pdf-fill text-danger" style="font-size: 3rem;"></i>
                            </div>
                            <p class="small fw-bold mb-3 text-dark">Manual de Funciones Cargado</p>
                            <a href="{{ route('archivo.cargo.verManual', $cargo->id) }}" target="_blank" class="btn btn-danger btn-sm w-100 rounded-pill btn-hover-lift">
                                <i class="bi bi-eye me-2"></i> Visualizar PDF
                            </a>
                        @else
                            <div class="mb-3">
                                <i class="bi bi-file-earmark-x text-muted" style="font-size: 3rem;"></i>
                            </div>
                            <p class="text-muted small mb-0">Sin manual de funciones adjunto.</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- BOTONES DE ACCIÓN --}}
    <div class="row mt-5">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-4 shadow-sm">
                <a href="{{ route('archivo.cargo.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-2"></i> Regresar al Listado
                </a>
                <div class="d-flex gap-2">
                    <a href="{{ route('archivo.cargo.edit', $cargo->id) }}" class="btn btn-primary rounded-pill px-4 btn-hover-lift">
                        <i class="bi bi-pencil-square me-2"></i> Modificar Información
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>