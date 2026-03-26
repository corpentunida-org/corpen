<x-base-layout>
    <div class="container-fluid py-4 px-lg-5">
        
        {{-- Header con Navegación --}}
        <div class="d-md-flex align-items-center justify-content-between mb-5">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-dots mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('interactions.channels.index') }}" class="text-muted">Canales</a></li>
                        <li class="breadcrumb-item active">Detalle del Canal</li>
                    </ol>
                </nav>
                <h2 class="fw-black tracking-tight text-dark mb-1">{{ $channel->name }}</h2>
                <p class="text-secondary opacity-75">Resumen y métricas de uso del canal en el sistema.</p>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
                <a href="{{ route('interactions.channels.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border">
                    <i class="feather-arrow-left me-1"></i> Volver
                </a>
                <a href="{{ route('interactions.channels.edit', $channel->id) }}" class="btn btn-dark rounded-pill px-4 shadow-sm d-flex align-items-center gap-2">
                    <i class="feather-edit-3"></i> <span>Editar Canal</span>
                </a>
            </div>
        </div>

        <div class="row g-4">
            {{-- Columna Principal: Información General --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold text-dark mb-0">Información General</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block" style="letter-spacing: 1px;">Identificador</label>
                                <div class="p-3 bg-light rounded-3 fw-bold text-dark">
                                    <i class="feather-hash me-2 text-primary"></i> #{{ $channel->id }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block" style="letter-spacing: 1px;">Nombre del Canal</label>
                                <div class="p-3 bg-light rounded-3 fw-bold text-dark">
                                    <i class="feather-radio me-2 text-primary"></i> {{ $channel->name }}
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block" style="letter-spacing: 1px;">Fecha de Registro</label>
                                <div class="p-3 bg-light rounded-3 text-dark">
                                    <i class="feather-calendar me-2 text-primary"></i> 
                                    {{ $channel->created_at->format('d \d\e F, Y') }} 
                                    <span class="text-muted ms-2">({{ $channel->created_at->diffForHumans() }})</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sección de Seguridad UX --}}
                <div class="card border-0 shadow-sm rounded-4 bg-{{ $channel->interactions_count > 0 ? 'light' : 'white' }} border-start border-4 border-{{ $channel->interactions_count > 0 ? 'warning' : 'info' }}">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-shape bg-white shadow-sm rounded-circle text-{{ $channel->interactions_count > 0 ? 'warning' : 'info' }}">
                                <i class="feather-shield"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">Estado de Integridad</h6>
                                @if($channel->interactions_count > 0)
                                    <p class="mb-0 small text-muted">Este canal <strong>no puede ser eliminado</strong> porque tiene {{ $channel->interactions_count }} interacciones vinculadas.</p>
                                @else
                                    <p class="mb-0 small text-muted">Este canal no tiene actividad registrada y puede ser eliminado de forma segura.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna Lateral: Estadísticas Rápidas --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-lg rounded-4 bg-primary text-white overflow-hidden">
                    <div class="card-body p-4 position-relative">
                        <i class="feather-activity position-absolute end-0 bottom-0 mb-n3 me-n3 opacity-25" style="font-size: 8rem;"></i>
                        <h5 class="fw-bold mb-4">Uso del Canal</h5>
                        <div class="display-4 fw-black mb-1">{{ $channel->interactions_count }}</div>
                        <p class="opacity-75 mb-4">Interacciones registradas</p>
                        
                        <div class="pt-3 border-top border-white border-opacity-25">
                            <div class="d-flex justify-content-between small opacity-75 mb-2">
                                <span>Volumen de uso</span>
                                <span>{{ $channel->interactions_count > 0 ? 'Activo' : 'Inactivo' }}</span>
                            </div>
                            <div class="progress bg-white bg-opacity-25" style="height: 6px;">
                                <div class="progress-bar bg-white" role="progressbar" style="width: {{ min($channel->interactions_count, 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Card Informativa Extra --}}
                <div class="card border-0 shadow-sm rounded-4 mt-4 bg-white p-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="feather-info me-2 text-primary"></i> Sugerencia de Sistemas</h6>
                    <p class="small text-muted mb-0">
                        Si planeas cambiar el nombre de un canal con muchos registros, asegúrate de que no afecte el entendimiento histórico de las comunicaciones pasadas.
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
        
        .btn-white { background: #fff; color: #444; }
        .btn-white:hover { background: #f8f9fa; color: #000; }

        .icon-shape { width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        
        /* Breadcrumbs modernos */
        .breadcrumb-dots .breadcrumb-item + .breadcrumb-item::before { content: "•"; color: #ccc; padding-right: 1rem; padding-left: 1rem; }

        .bg-primary-soft { background-color: #eef5ff; }
        .tracking-tight { letter-spacing: -0.02em; }
    </style>
</x-base-layout>