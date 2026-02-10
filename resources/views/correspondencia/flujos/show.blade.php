<x-base-layout>
    <div class="app-container">
        <header class="main-header mb-5">
            <div class="header-content">
                <nav class="small text-muted mb-2">
                    <a href="{{ route('correspondencia.flujos.index') }}" class="text-decoration-none">Flujos</a> / Detalle
                </nav>
                <h1 class="page-title">{{ $flujo->nombre }}</h1>
            </div>
            <div class="header-actions">
                <a href="{{ route('correspondencia.flujos.edit', $flujo) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil"></i> Editar
                </a>
            </div>
        </header>

        <div class="row">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h6 class="text-muted fw-bold small text-uppercase mb-4">Información del Responsable</h6>
                        <div class="text-center py-3">
                            <div class="avatar-lg bg-primary text-white mx-auto mb-3">
                                {{ substr($flujo->usuario->name ?? '?', 0, 1) }}
                            </div>
                            <h5 class="fw-bold mb-0">{{ $flujo->usuario->name ?? 'N/A' }}</h5>
                            <p class="text-muted small">{{ $flujo->usuario->email ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                    <h6 class="text-muted fw-bold small text-uppercase mb-3">Descripción del Flujo</h6>
                    <p class="lead" style="font-size: 1.1rem; line-height: 1.6;">
                        {{ $flujo->detalle ?: 'Este flujo no tiene una descripción detallada definida.' }}
                    </p>
                    
                    <hr class="my-4 opacity-10">
                    
                    <div class="alert alert-light border-0">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle-fill text-primary fs-4 me-3"></i>
                            <div>
                                <span class="d-block fw-bold">Configuración de Pasos</span>
                                <small class="text-muted">Este flujo está configurado para la asignación directa al responsable principal.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .avatar-lg { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 800; }
    </style>
</x-base-layout>