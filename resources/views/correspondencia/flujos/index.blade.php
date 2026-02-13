<x-base-layout>
    <div class="app-container">
        <header class="main-header mb-4">
            <div class="header-content">
                <h1 class="page-title">Flujos de Trabajo</h1>
                <p class="page-subtitle">Defina las rutas y responsables de los procesos documentales.</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('correspondencia.flujos.create') }}" class="btn-primary-neo">
                    <i class="bi bi-diagram-3-fill"></i> Nuevo Flujo
                </a>
            </div>
        </header>

        {{-- ALERTAS DE SISTEMA --}}
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            @forelse($flujos as $flujo)
                <div class="col-md-6 col-lg-4">
                    {{-- Agregamos position-relative para que el enlace expandido no se salga de la tarjeta --}}
                    <div class="card h-100 border-0 shadow-sm hover-up position-relative" style="border-radius: 15px; cursor: pointer;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="icon-box bg-soft-indigo text-indigo">
                                    <i class="bi bi-signpost-split-fill fs-4"></i>
                                </div>
                                
                                {{-- El menú de opciones debe tener un z-index superior para seguir siendo clickable --}}
                                <div class="dropdown" style="z-index: 10;">
                                    <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('correspondencia.flujos.edit', $flujo) }}">
                                                <i class="bi bi-pencil me-2"></i> Editar
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            @if($flujo->correspondencias_count > 0)
                                                <button class="dropdown-item text-muted opacity-50" disabled>
                                                    <i class="bi bi-trash me-2"></i> Eliminar (En uso)
                                                </button>
                                            @else
                                                <form action="{{ route('correspondencia.flujos.destroy', $flujo) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar este flujo?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash me-2"></i> Eliminar
                                                    </button>
                                                </form>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            {{-- ENLACE PRINCIPAL: Este es el que hace clickable toda la tarjeta --}}
                            <a href="{{ route('correspondencia.flujos.show', $flujo) }}" class="stretched-link text-decoration-none text-dark">
                                <h5 class="fw-bold mb-1">{{ $flujo->nombre }}</h5>
                            </a>
                            
                            <div class="mb-2">
                                <span class="badge bg-light text-primary border-0 px-0">
                                    <i class="bi bi-building me-1"></i> {{ $flujo->area->nombre ?? 'Sin Área' }}
                                </span>
                            </div>

                            <p class="text-muted small mb-3 text-truncate-2" style="min-height: 3em;">
                                {{ $flujo->detalle ?? 'Sin descripción detallada.' }}
                            </p>

                            {{-- INDICADOR DE ESTADO --}}
                            <div class="mb-4">
                                @if($flujo->correspondencias_count > 0)
                                    <span class="badge rounded-pill bg-soft-secondary text-muted border px-3" style="font-size: 10px;">
                                        <i class="bi bi-lock-fill me-1"></i> EN USO: {{ $flujo->correspondencias_count }}
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-soft-success text-success border px-3" style="font-size: 10px;">
                                        <i class="bi bi-shield-check me-1"></i> DISPONIBLE
                                    </span>
                                @endif
                            </div>
                            
                            <div class="d-flex align-items-center pt-3 border-top">
                                <div class="avatar-xs bg-primary text-white me-2 shadow-sm">
                                    {{ strtoupper(substr($flujo->usuario->name ?? '?', 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block" style="font-size: 10px;">RESPONSABLE (JEFE)</small>
                                    <span class="small fw-bold">{{ $flujo->usuario->name ?? 'No asignado' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No hay flujos de trabajo creados aún.</p>
                </div>
            @endforelse
        </div>
        
        <div class="mt-4">
            {{ $flujos->links() }}
        </div>
    </div>

    <style>
        /* Estilo para que la tarjeta se sienta interactiva */
        .hover-up { 
            transition: all 0.25s ease; 
        }
        .hover-up:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 0.8rem 2rem rgba(79, 70, 229, 0.1) !important; 
            border: 1px solid #4f46e5 !important;
        }
        
        /* Asegura que el nombre no pierda el estilo de título al ser un <a> */
        .stretched-link::after {
            z-index: 1; /* El enlace cubre la tarjeta pero queda debajo del dropdown */
        }

        .bg-soft-indigo { background-color: #eef2ff; }
        .text-indigo { color: #4f46e5; }
        .bg-soft-success { background-color: #ecfdf5; }
        .bg-soft-secondary { background-color: #f8f9fa; }
        .icon-box { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
        .text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .avatar-xs { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; }
        
        .btn-primary-neo {
            background-color: #4f46e5;
            color: white !important;
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            display: inline-block;
        }
    </style>
</x-base-layout>