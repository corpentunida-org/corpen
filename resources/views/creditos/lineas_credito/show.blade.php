<x-base-layout>
    @section('titlepage', $lineas_credito->nombre)
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-11">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb breadcrumb-dots">
                        <li class="breadcrumb-item"><a href="{{ route('lineas_credito.index') }}" class="text-muted text-decoration-none">Líneas de Crédito</a></li>
                        <li class="breadcrumb-item active">{{ $lineas_credito->nombre }}</li>
                    </ol>
                </nav>
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h3 class="fw-black text-dark mb-1">{{ $lineas_credito->nombre }}</h3>
                                <p class="text-secondary opacity-75 mb-0">Cuenta: {{ $lineas_credito->cuenta }}</p>
                            </div>
                            <a href="{{ route('lineas_credito.edit', $lineas_credito) }}" class="btn btn-dark rounded-pill px-4">
                                <i class="feather-edit-2 me-2"></i>Editar
                            </a>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <small class="text-uppercase fs-xs fw-bold text-muted d-block mb-1">Tipo de Crédito</small>
                                <span class="fw-semibold text-dark">{{ $lineas_credito->tipoCredito->nombre ?? '—' }}</span>
                            </div>
                            <div class="col-md-6">
                                <small class="text-uppercase fs-xs fw-bold text-muted d-block mb-1">Garantía</small>
                                <span class="fw-semibold text-dark">{{ $lineas_credito->garantia->nombre ?? '—' }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-uppercase fs-xs fw-bold text-muted d-block mb-1">Tasa de Interés</small>
                                <span class="fw-semibold text-dark">{{ $lineas_credito->tasa_interes }}%</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-uppercase fs-xs fw-bold text-muted d-block mb-1">Plazo</small>
                                <span class="fw-semibold text-dark">{{ $lineas_credito->plazo_minimo }}–{{ $lineas_credito->plazo_maximo }} meses</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-uppercase fs-xs fw-bold text-muted d-block mb-1">Edad Permitida</small>
                                <span class="fw-semibold text-dark">{{ $lineas_credito->edad_minima }}–{{ $lineas_credito->edad_maxima }} años</span>
                            </div>
                            <div class="col-md-6">
                                <small class="text-uppercase fs-xs fw-bold text-muted d-block mb-1">Fecha Apertura</small>
                                <span class="fw-semibold text-dark">{{ optional($lineas_credito->fecha_apertura)->format('d/m/Y') ?? '—' }}</span>
                            </div>
                            <div class="col-md-6">
                                <small class="text-uppercase fs-xs fw-bold text-muted d-block mb-1">Fecha Cierre</small>
                                <span class="fw-semibold text-dark">{{ optional($lineas_credito->fecha_cierre)->format('d/m/Y') ?? 'Vigente' }}</span>
                            </div>
                            @if ($lineas_credito->observacion)
                                <div class="col-12">
                                    <small class="text-uppercase fs-xs fw-bold text-muted d-block mb-1">Observación</small>
                                    <p class="text-dark mb-0">{{ $lineas_credito->observacion }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .fw-black { font-weight: 800; }
        .fs-xs { font-size: 0.7rem; }
        .breadcrumb-dots .breadcrumb-item + .breadcrumb-item::before { content: "•"; color: #ccc; padding: 0 1rem; }
    </style>
</x-base-layout>
