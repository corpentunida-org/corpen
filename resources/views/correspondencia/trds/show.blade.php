<x-base-layout>
    <div class="app-container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                {{-- Encabezado con acciones rápidas --}}
                <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
                    <a href="{{ route('correspondencia.trds.index') }}" class="btn btn-sm btn-light border">
                        <i class="fas fa-arrow-left me-2"></i> Volver al listado
                    </a>
                    <div class="d-flex gap-2">
                        <a href="{{ route('correspondencia.trds.edit', $trd) }}" class="btn btn-sm btn-warning shadow-sm">
                            <i class="fas fa-edit me-1"></i> Editar TRD
                        </a>
                        <button onclick="window.print()" class="btn btn-sm btn-secondary shadow-sm">
                            <i class="fas fa-print me-1"></i> Imprimir Ficha
                        </button>
                    </div>
                </div>

                <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
                    {{-- Cabecera Estilo Documental --}}
                    <div class="bg-primary p-4 text-white">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="text-uppercase opacity-75 mb-1" style="letter-spacing: 2px;">Ficha Técnica - Serie Documental</h6>
                                <h2 class="fw-bold mb-0">{{ $trd->serie_documental }}</h2>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <span class="badge bg-white text-primary px-3 py-2 fs-6 rounded-pill">
                                    ID: TRD-{{ str_pad($trd->id_trd, 4, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-5">
                        {{-- Sección de Flujo de Trabajo (NUEVO) --}}
                        <div class="mb-5">
                            <h5 class="fw-bold mb-3 text-primary"><i class="fas fa-route me-2"></i>Flujo de Trabajo Operativo</h5>
                            <div class="p-4 rounded-4 border bg-light d-flex align-items-center">
                                <div class="icon-box bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fas fa-project-diagram"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-bold">{{ $trd->flujo->nombre ?? 'Sin flujo asignado' }}</h5>
                                    <p class="text-muted small mb-0">Esta serie sigue el protocolo automático definido en el módulo de flujos.</p>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mb-5">
                            {{-- Bloque de Tiempos --}}
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 bg-light border-start border-4 border-info">
                                    <h6 class="fw-bold text-info text-uppercase small">Archivo de Gestión</h6>
                                    <div class="d-flex align-items-baseline">
                                        <span class="display-6 fw-bold text-dark">{{ $trd->tiempo_gestion }}</span>
                                        <span class="ms-2 text-muted">años</span>
                                    </div>
                                    <p class="small text-muted mb-0">Permanencia en la oficina productora.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 bg-light border-start border-4 border-warning">
                                    <h6 class="fw-bold text-warning text-uppercase small">Archivo Central</h6>
                                    <div class="d-flex align-items-baseline">
                                        <span class="display-6 fw-bold text-dark">{{ $trd->tiempo_central }}</span>
                                        <span class="ms-2 text-muted">años</span>
                                    </div>
                                    <p class="small text-muted mb-0">Permanencia tras transferencia primaria.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Disposición Final --}}
                        <div class="mb-5">
                            <h5 class="fw-bold mb-3 text-muted"><i class="fas fa-gavel me-2"></i>Disposición Final</h5>
                            <div class="card border p-4 bg-white shadow-sm" style="border-radius: 15px;">
                                <div class="d-flex align-items-center">
                                    @if($trd->disposicion_final == 'conservar')
                                        <div class="icon-box bg-soft-success text-success me-4 d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                            <i class="fas fa-archive"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-success fw-bold mb-1">CONSERVACIÓN TOTAL</h4>
                                            <p class="text-muted mb-0">Los documentos poseen valores secundarios (históricos, científicos o culturales) y se conservarán permanentemente.</p>
                                        </div>
                                    @else
                                        <div class="icon-box bg-soft-danger text-danger me-4 d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                            <i class="fas fa-trash-alt"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-danger fw-bold mb-1">ELIMINACIÓN</h4>
                                            <p class="text-muted mb-0">Al cumplir el tiempo de retención legal, los documentos serán destruidos bajo acta oficial.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr class="opacity-10">

                        {{-- Footer de auditoría --}}
                        <div class="row align-items-center mt-4">
                            <div class="col-md-6">
                                <p class="small text-muted mb-0">Responsable del Registro:</p>
                                <div class="d-flex align-items-center mt-1">
                                    <div class="avatar-xs bg-dark text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                        {{ strtoupper(substr($trd->usuario->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-bold">{{ $trd->usuario->name }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                <p class="small text-muted mb-0">Fecha y Hora de Creación:</p>
                                <span class="fw-bold text-dark"><i class="far fa-calendar-alt me-1"></i> {{ $trd->created_at->format('d/m/Y h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-soft-success { background-color: #d1fae5; }
        .bg-soft-danger { background-color: #fee2e2; }
        
        @media print {
            .btn, nav, .main-header, .d-print-none { display: none !important; }
            body { background-color: white !important; }
            .card { box-shadow: none !important; border: 1px solid #eee !important; }
            .app-container { padding: 0 !important; }
        }
    </style>
</x-base-layout>