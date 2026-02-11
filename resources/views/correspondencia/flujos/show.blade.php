<x-base-layout>
    <div class="app-container">
        <header class="main-header mb-4">
            <div class="header-content">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('correspondencia.flujos.index') }}">Flujos</a></li>
                        <li class="breadcrumb-item active">Detalle</li>
                    </ol>
                </nav>
                <h1 class="page-title">{{ $flujo->nombre }}</h1>
            </div>
            <div class="header-actions">
                <a href="{{ route('correspondencia.flujos.edit', $flujo) }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil"></i> Editar Flujo
                </a>
            </div>
        </header>

        <div class="row">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 text-center">
                        <div class="icon-box bg-soft-indigo text-indigo mx-auto mb-3" style="width: 70px; height: 70px; border-radius: 15px;">
                            <i class="bi bi-diagram-3-fill fs-2"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Estado del Flujo</h5>
                        <span class="badge rounded-pill {{ $flujo->correspondencias_count > 0 ? 'bg-soft-warning text-warning' : 'bg-soft-success text-success' }} mb-3">
                            {{ $flujo->correspondencias_count > 0 ? 'En Uso Activo' : 'Disponible' }}
                        </span>
                        
                        <div class="text-start mt-3">
                            <div class="p-3 bg-light rounded-3 mb-3">
                                <small class="text-muted d-block">Responsable General</small>
                                <span class="fw-bold"><i class="bi bi-person-check me-1"></i>{{ $flujo->usuario->name ?? 'Sin asignar' }}</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">Correspondencias</small>
                                    <span class="fw-bold text-primary">{{ $flujo->correspondencias_count }}</span>
                                </div>
                                <div class="col-6 text-end">
                                    <small class="text-muted d-block">Pasos/Procesos</small>
                                    <span class="fw-bold text-indigo">{{ $flujo->procesos->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="text-uppercase fw-bold text-muted small mb-3">Descripción</h6>
                        <p class="text-secondary">{{ $flujo->detalle ?? 'Sin descripción.' }}</p>
                        
                        <hr class="my-4">

                        <h6 class="text-uppercase fw-bold text-indigo mb-4">
                            <i class="bi bi-list-check me-2"></i>Secuencia de Procesos vinculados
                        </h6>

                        @forelse($flujo->procesos as $proceso)
                            <div class="d-flex mb-4 position-relative">
                                @if(!$loop->last)
                                    <div class="position-absolute h-100 border-start border-2 border-light" style="left: 15px; top: 35px; z-index: 1;"></div>
                                @endif
                                
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-indigo text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; z-index: 2; position: relative;">
                                        {{ $loop->iteration }}
                                    </div>
                                </div>
                                <div class="flex-grow-1 p-3 border rounded-3 bg-white shadow-sm hover-up">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="fw-bold mb-1 text-dark">{{ $proceso->nombre }}</h6>
                                        <span class="badge bg-light text-muted fw-normal" style="font-size: 10px;">ID: #{{ $proceso->id }}</span>
                                    </div>
                                    <p class="small text-muted mb-2">{{ $proceso->detalle }}</p>
                                    
                                    <div class="d-flex align-items-center flex-wrap gap-1">
                                        <small class="text-muted me-2" style="font-size: 11px;">USUARIOS EN ESTE PASO:</small>
                                        @foreach($proceso->usuarios as $user)
                                            <span class="badge border text-dark bg-white" title="{{ $user->pivot->detalle }}">
                                                <i class="bi bi-person me-1"></i>{{ $user->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 border border-dashed rounded-4">
                                <i class="bi bi-stack text-light display-4"></i>
                                <p class="text-muted mt-2">Este flujo aún no tiene procesos definidos.</p>
                                <a href="#" class="btn btn-sm btn-outline-indigo mt-2">
                                    <i class="bi bi-plus-circle me-1"></i>Configurar Procesos
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-soft-indigo { background-color: #eef2ff; }
        .text-indigo { color: #4f46e5; }
        .bg-indigo { background-color: #4f46e5; }
        .btn-outline-indigo { color: #4f46e5; border-color: #4f46e5; }
        .btn-outline-indigo:hover { background-color: #4f46e5; color: white; }
        .hover-up { transition: transform 0.2s; }
        .hover-up:hover { transform: translateY(-3px); }
    </style>
</x-base-layout>