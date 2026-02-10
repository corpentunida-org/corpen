<x-base-layout>
    <div class="app-container py-4">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 700px; border-radius: 20px;">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <a href="{{ route('correspondencia.correspondencias-procesos.index') }}" class="btn btn-link text-muted p-0 text-decoration-none small">
                    <i class="fas fa-chevron-left me-1"></i> Volver a trazabilidad
                </a>
            </div>
            <div class="card-body p-5 pt-2">
                <div class="text-center mb-5">
                    <div class="icon-circle bg-soft-primary text-primary mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 70px; border-radius: 50%; background: #eef2ff; font-size: 1.5rem;">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h2 class="fw-bold mb-1">Detalle del Movimiento</h2>
                    <span class="text-muted">Radicado: #{{ $correspondenciaProceso->correspondencia->nro_radicado }}</span>
                </div>

                <div class="row g-4">
                    <div class="col-6">
                        <p class="text-muted small text-uppercase fw-bold mb-1">Estado</p>
                        <p class="fw-bold text-dark fs-5">{{ ucfirst($correspondenciaProceso->estado) }}</p>
                    </div>
                    <div class="col-6 text-end">
                        <p class="text-muted small text-uppercase fw-bold mb-1">Responsable</p>
                        <p class="fw-bold text-dark fs-5">{{ $correspondenciaProceso->usuario->name }}</p>
                    </div>
                    
                    <div class="col-12">
                        <div class="p-4 bg-light rounded-4 border-start border-4 border-primary">
                            <p class="text-muted small text-uppercase fw-bold mb-2">Observación</p>
                            <p class="mb-0 text-dark">{{ $correspondenciaProceso->observacion ?: 'Sin observaciones registradas.' }}</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <p class="text-muted small text-uppercase fw-bold mb-1">Etapa / Proceso</p>
                        <p class="text-dark">{{ $correspondenciaProceso->proceso->flujo->nombre ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <p class="text-muted small text-uppercase fw-bold mb-1">Fecha Gestión</p>
                        <p class="text-dark">{{ $correspondenciaProceso->fecha_gestion->format('d/m/Y h:i A') }}</p>
                    </div>
                </div>

                @if(!$correspondenciaProceso->notificado_email)
                <div class="mt-5">
                    <form action="{{ route('correspondencia.correspondencias-procesos.marcarNotificado', $correspondenciaProceso->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-success w-100 rounded-pill">
                            <i class="fas fa-envelope me-2"></i> Marcar como Notificado vía Email
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-base-layout>