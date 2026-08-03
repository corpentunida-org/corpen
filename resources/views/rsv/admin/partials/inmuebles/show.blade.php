<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center pt-4 pb-2">
        <div>
            <h5 class="fw-bold mb-1">Detalle del Inmueble: {{ $inmueble->name }}</h5>
            <p class="text-muted small mb-0">Información completa de la propiedad ID #{{ $inmueble->id }}</p>
        </div>
        <a href="{{ route('rsv.admin.dashboard') }}" class="btn btn-secondary btn-sm rounded-pill px-3">
            ← Volver al Listado
        </a>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-6">
                <p class="mb-1 text-muted small">Nombre de la Propiedad</p>
                <h6 class="fw-bold text-dark">{{ $inmueble->name }}</h6>
            </div>
            <div class="col-md-6">
                <p class="mb-1 text-muted small">Precio Base por Noche</p>
                <h6 class="fw-bold text-success">${{ number_format($inmueble->precio_base_noche, 2) }}</h6>
            </div>
            <div class="col-md-6">
                <p class="mb-1 text-muted small">Capacidad Máxima</p>
                <h6 class="fw-bold">{{ $inmueble->capacidad_maxima ?? 'N/A' }} personas</h6>
            </div>
            <div class="col-md-6">
                <p class="mb-1 text-muted small">Ciudad</p>
                <h6 class="fw-bold">{{ $inmueble->city ?? 'N/A' }}</h6>
            </div>
            <div class="col-md-6">
                <p class="mb-1 text-muted small">Ubicación / Dirección</p>
                <h6 class="fw-bold">{{ $inmueble->ubicacion ?? 'N/A' }}</h6>
            </div>
            <div class="col-md-6">
                <p class="mb-1 text-muted small">Tipo de Inmueble (ID)</p>
                <h6 class="fw-bold">{{ $inmueble->tipo_inmueble_id }}</h6>
            </div>
            <div class="col-md-6">
                <p class="mb-1 text-muted small">Estado Actual</p>
                <h6>
                    @if($inmueble->active)
                        <span class="badge bg-success bg-opacity-15 text-success px-2 py-1">Activo</span>
                    @else
                        <span class="badge bg-secondary bg-opacity-15 text-secondary px-2 py-1">Inactivo</span>
                    @endif
                </h6>
            </div>
        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('rsv.admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">Volver al Listado</a>
        </div>
    </div>
</div>
