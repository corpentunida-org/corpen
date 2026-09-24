<x-base-layout>
    <div class="container-fluid py-4 px-lg-5">

        <div class="d-md-flex align-items-center justify-content-between mb-5">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-dots mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('interactions.lineas.index') }}" class="text-muted text-decoration-none">Líneas</a></li>
                        <li class="breadcrumb-item active">Detalle de Línea</li>
                    </ol>
                </nav>
                <h2 class="fw-black tracking-tight text-dark mb-1">{{ $linea->name }}</h2>
                <p class="text-secondary opacity-75">Configuración y frecuencia de uso para esta línea.</p>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
                <a href="{{ route('interactions.lineas.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border">
                    <i class="feather-arrow-left me-1"></i> Volver
                </a>
                <a href="{{ route('interactions.lineas.edit', $linea->id) }}" class="btn btn-dark rounded-pill px-4 shadow-sm d-flex align-items-center gap-2">
                    <i class="feather-edit-3"></i> <span>Editar Línea</span>
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold text-dark mb-0">Especificaciones</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block" style="letter-spacing: 1px;">ID del Sistema</label>
                                <div class="p-3 bg-light rounded-3 fw-bold text-dark border-start border-primary border-3">
                                    <span class="text-primary me-1">#</span>{{ $linea->id }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block" style="letter-spacing: 1px;">Área</label>
                                <div class="p-3 bg-light rounded-3 fw-bold text-dark">
                                    @if ($linea->area)
                                        <span class="badge bg-soft-primary text-primary">{{ strtoupper($linea->area) }}</span>
                                    @else
                                        <span class="badge bg-light text-muted border">Compartido</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block" style="letter-spacing: 1px;">Cronología</label>
                                <div class="p-3 bg-light rounded-3 text-dark d-flex align-items-center justify-content-between">
                                    <span><i class="feather-clock me-2 text-muted"></i> Registrada el {{ $linea->created_at->format('d/m/Y H:i') }}</span>
                                    <span class="badge bg-white text-dark border shadow-sm px-3">{{ $linea->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 bg-white border-start border-4 border-{{ $linea->interactions_count > 0 ? 'danger' : 'success' }}">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-shape rounded-circle {{ $linea->interactions_count > 0 ? 'bg-danger-soft text-danger' : 'bg-success-soft text-success' }}">
                                <i class="feather-{{ $linea->interactions_count > 0 ? 'lock' : 'unlock' }}"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">Estado de Eliminación</h6>
                                @if($linea->interactions_count > 0)
                                    <p class="mb-0 small text-muted">Protegida. Esta línea está vinculada a <strong>{{ $linea->interactions_count }}</strong> interacciones y no puede borrarse para mantener la integridad histórica.</p>
                                @else
                                    <p class="mb-0 small text-muted">Segura. No se han encontrado registros vinculados; esta línea puede ser eliminada del catálogo.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-lg rounded-4 bg-warning text-dark overflow-hidden mb-4">
                    <div class="card-body p-4 position-relative">
                        <i class="feather-layers position-absolute end-0 bottom-0 mb-n3 me-n2 opacity-10" style="font-size: 7rem;"></i>
                        <h5 class="fw-bold mb-4 opacity-75">Frecuencia de Uso</h5>
                        <div class="display-4 fw-black mb-1 tracking-tight text-white">{{ $linea->interactions_count }}</div>
                        <p class="fw-bold small mb-4">Interacciones Totales</p>

                        <div class="pt-3 border-top border-dark border-opacity-10">
                            <div class="d-flex justify-content-between fs-xs fw-bold opacity-75 mb-2">
                                <span>RELEVANCIA</span>
                                <span>{{ $linea->interactions_count > 0 ? 'ALTA' : 'NULA' }}</span>
                            </div>
                            <div class="progress bg-dark bg-opacity-10" style="height: 8px;">
                                <div class="progress-bar bg-white shadow-sm" role="progressbar" style="width: {{ min($linea->interactions_count * 5, 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <h6 class="fw-bold text-dark mb-3 small text-uppercase">Nota de Gestión</h6>
                    <p class="small text-muted mb-0">
                        La línea es el producto/obligación al que se refiere la interacción (crédito, póliza, etc). Una interacción puede tocar hasta 5 líneas a la vez.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');

        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f7f6; }
        .fw-black { font-weight: 800; }
        .fs-xs { font-size: 0.7rem; }
        .tracking-tight { letter-spacing: -0.03em; }

        .btn-white { background: #fff; color: #444; border: 1px solid #eee; }
        .btn-white:hover { background: #f8f9fa; color: #000; border-color: #ddd; }

        .icon-shape { width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
        .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }
        .bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
        .bg-soft-primary { background-color: #f0f7ff; }

        .breadcrumb-dots .breadcrumb-item + .breadcrumb-item::before { content: "•"; color: #ccc; padding: 0 1rem; }
    </style>
</x-base-layout>
