<x-base-layout>
    <div class="app-container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <a href="{{ route('correspondencia.notificaciones.index') }}" class="btn btn-link text-decoration-none p-0">
                            <i class="fas fa-chevron-left me-1"></i> Volver a la lista
                        </a>
                        @if($notificacion->estado == 'pendiente')
                            <span class="badge bg-soft-primary text-primary px-3 py-2">No leído</span>
                        @else
                            <span class="badge bg-soft-success text-success px-3 py-2">Leído el {{ $notificacion->fecha_leida->format('d/m/Y H:i') }}</span>
                        @endif
                    </div>

                    <div class="card-body p-5">
                        <div class="d-flex align-items-center mb-5">
                            <div class="avatar-lg bg-indigo text-white rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 60px; height: 60px; font-size: 1.5rem; background: #4f46e5;">
                                {{ substr($notificacion->usuarioEnvia->name, 0, 1) }}
                            </div>
                            <div class="ms-4">
                                <h4 class="fw-bold mb-0">{{ $notificacion->usuarioEnvia->name }}</h4>
                                <p class="text-muted mb-0">Para: {{ $notificacion->usuarioDestino->name }}</p>
                            </div>
                        </div>

                        <div class="p-4 bg-light rounded-4 mb-4" style="border-left: 4px solid #4f46e5;">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">Mensaje</h6>
                            <p class="fs-5 mb-0" style="white-space: pre-line;">{{ $notificacion->mensaje }}</p>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card border bg-transparent p-3 rounded-3">
                                    <small class="text-muted d-block fw-bold text-uppercase">Proceso Origen</small>
                                    <a href="{{ route('correspondencia.procesos.show', $notificacion->proceso_origen_id) }}" class="text-decoration-none fw-bold">
                                        <i class="fas fa-folder-open me-1"></i> #{{ $notificacion->proceso_origen_id }} - {{ $notificacion->procesoOrigen->flujo->nombre ?? 'Ver detalle' }}
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border bg-transparent p-3 rounded-3">
                                    <small class="text-muted d-block fw-bold text-uppercase">Proceso Destino</small>
                                    <a href="{{ route('correspondencia.procesos.show', $notificacion->proceso_destino_id) }}" class="text-decoration-none fw-bold text-success">
                                        <i class="fas fa-share-square me-1"></i> #{{ $notificacion->proceso_destino_id }} - {{ $notificacion->procesoDestino->flujo->nombre ?? 'Ver detalle' }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        @if($notificacion->estado == 'pendiente')
                        <div class="mt-5 text-center">
                            <form action="{{ route('correspondencia.notificaciones.marcarLeida', $notificacion->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary px-5 rounded-pill shadow">
                                    <i class="fas fa-check-double me-2"></i> Marcar como leído
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>