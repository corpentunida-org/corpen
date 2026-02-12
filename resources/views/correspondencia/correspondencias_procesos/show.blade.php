<x-base-layout>
    <div class="app-container py-4">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 800px; border-radius: 20px;">
            <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <a href="{{ route('correspondencia.correspondencias-procesos.index') }}" class="btn btn-link text-muted p-0 text-decoration-none small">
                    <i class="fas fa-chevron-left me-1"></i> Volver al listado
                </a>
                <div class="d-flex gap-2">
                    <a href="{{ route('correspondencia.correspondencias-procesos.edit', $correspondenciaProceso) }}" class="btn btn-sm btn-light border">
                        <i class="fas fa-edit me-1"></i> Editar
                    </a>
                    <a href="{{ route('correspondencia.correspondencias.show', $correspondenciaProceso->id_correspondencia) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                        <i class="fas fa-external-link-alt me-1"></i> Ver Expediente
                    </a>
                </div>
            </div>
            <div class="card-body p-5 pt-2">
                <div class="text-center mb-5">
                    <div class="icon-circle text-primary mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 70px; border-radius: 50%; background: #eef2ff; font-size: 1.5rem;">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <h2 class="fw-bold mb-1">Detalle del Movimiento</h2>
                    <span class="text-muted">Radicado: <strong class="text-dark">#{{ $correspondenciaProceso->id_correspondencia }}</strong></span>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-6">
                        <p class="text-muted small text-uppercase fw-bold mb-1">Estado de la Gestión</p>
                        <span class="badge bg-primary px-3 shadow-sm">{{ ucfirst(str_replace('_', ' ', $correspondenciaProceso->estado)) }}</span>
                        
                        @if($correspondenciaProceso->finalizado)
                            <span class="badge bg-danger px-3 shadow-sm ms-2">
                                <i class="fas fa-lock me-1"></i> FINALIZADO
                            </span>
                        @endif
                    </div>
                    <div class="col-6 text-end">
                        <p class="text-muted small text-uppercase fw-bold mb-1">Responsable</p>
                        <p class="fw-bold text-dark mb-0">{{ $correspondenciaProceso->usuario->name }}</p>
                        <small class="text-muted">{{ $correspondenciaProceso->usuario->email }}</small>
                    </div>
                    
                    <div class="col-12">
                        <div class="p-4 bg-light rounded-4 border-start border-4 border-primary">
                            <p class="text-muted small text-uppercase fw-bold mb-2">Observación de la Gestión</p>
                            <p class="mb-0 text-dark" style="white-space: pre-line; line-height: 1.6;">{{ $correspondenciaProceso->observacion ?: 'Sin observaciones.' }}</p>
                        </div>
                    </div>

                    @if($correspondenciaProceso->finalizado)
                    <div class="col-12">
                        <div class="alert alert-danger border-0 rounded-4 d-flex align-items-center shadow-sm" style="background-color: #fdf2f2;">
                            <i class="fas fa-archive me-3 fs-3 text-danger"></i>
                            <div>
                                <div class="fw-bold text-danger">Cierre de Expediente</div>
                                <small class="text-dark">Esta gestión marcó el radicado como <strong>completado/archivado</strong>.</small>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($correspondenciaProceso->documento_arc)
                    <div class="col-12">
                        <p class="text-muted small text-uppercase fw-bold mb-2">Documento de Soporte</p>
                        <div class="d-flex align-items-center p-3 border rounded-3 bg-white">
                            <i class="fas fa-file-pdf text-danger fs-3 me-3"></i>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">Archivo adjunto en S3</small>
                                <a href="{{ Storage::disk('s3')->url($correspondenciaProceso->documento_arc) }}" target="_blank" class="fw-bold text-decoration-none">Visualizar Documento</a>
                            </div>
                            <a href="{{ Storage::disk('s3')->url($correspondenciaProceso->documento_arc) }}" download class="btn btn-light border-0"><i class="fas fa-download"></i></a>
                        </div>
                    </div>
                    @endif

                    <div class="col-md-6">
                        <p class="text-muted small text-uppercase fw-bold mb-1">Etapa / Proceso</p>
                        <p class="text-dark fw-medium">{{ $correspondenciaProceso->proceso->nombre ?? 'N/A' }}</p>
                        <small class="text-muted">{{ $correspondenciaProceso->proceso->flujo->nombre ?? 'Sin flujo asociado' }}</small>
                    </div>
                    <div class="col-md-6 text-end">
                        <p class="text-muted small text-uppercase fw-bold mb-1">Fecha Gestión</p>
                        <p class="text-dark">{{ $correspondenciaProceso->fecha_gestion ? $correspondenciaProceso->fecha_gestion->format('d/m/Y h:i A') : 'N/A' }}</p>
                    </div>
                </div>

                <div class="mt-5">
                    @if(!$correspondenciaProceso->notificado_email)
                        <div class="alert alert-warning border-0 rounded-4 d-flex align-items-center">
                            <i class="fas fa-info-circle me-3 fs-4"></i>
                            <small>Esta gestión aún no ha sido notificada al remitente por correo.</small>
                        </div>
                    @else
                        <div class="alert alert-success border-0 rounded-4 d-flex align-items-center shadow-sm">
                            <i class="fas fa-check-circle me-3 fs-3"></i>
                            <div>
                                <div class="fw-bold">Gestión Notificada</div>
                                <small>El remitente ya fue informado por correo electrónico de este movimiento.</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-base-layout>