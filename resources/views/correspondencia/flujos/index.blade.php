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

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm mb-4 alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            @forelse($flujos as $flujo)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm hover-up" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="icon-box bg-soft-indigo text-indigo">
                                    <i class="bi bi-signpost-split-fill fs-4"></i>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('correspondencia.flujos.show', $flujo) }}">
                                                <i class="bi bi-eye me-2"></i> Ver Detalle
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('correspondencia.flujos.edit', $flujo) }}">
                                                <i class="bi bi-pencil me-2"></i> Editar
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            @if($flujo->correspondencias_count > 0)
                                                <button class="dropdown-item text-muted opacity-50" title="No se puede eliminar: tiene correspondencias vinculadas" disabled>
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
                            
                            <h5 class="fw-bold mb-1">{{ $flujo->nombre }}</h5>
                            <p class="text-muted small mb-3 text-truncate-2" style="min-height: 3em;">
                                {{ $flujo->detalle ?? 'Sin descripción detallada.' }}
                            </p>

                            {{-- INDICADOR DE ESTADO / USO --}}
                            <div class="mb-4">
                                @if($flujo->correspondencias_count > 0)
                                    <span class="badge rounded-pill bg-soft-secondary text-muted border px-3" style="font-size: 10px;">
                                        <i class="bi bi-lock-fill me-1"></i> EN USO: {{ $flujo->correspondencias_count }} REGISTROS
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-soft-success text-success border px-3" style="font-size: 10px;">
                                        <i class="bi bi-shield-check me-1"></i> DISPONIBLE PARA ELIMINAR
                                    </span>
                                @endif
                            </div>
                            
                            <div class="d-flex align-items-center pt-3 border-top">
                                <div class="avatar-xs bg-primary text-white me-2">
                                    {{ strtoupper(substr($flujo->usuario->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 10px;">RESPONSABLE</small>
                                    <span class="small fw-bold">{{ $flujo->usuario->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm border border-dashed">
                        <i class="bi bi-diagram-2 display-1 text-light"></i>
                        <p class="text-muted mt-3">No hay flujos de trabajo creados aún.</p>
                        <a href="{{ route('correspondencia.flujos.create') }}" class="btn btn-sm btn-outline-primary">Crear el primero</a>
                    </div>
                </div>
            @endforelse
        </div>
        
        <div class="mt-4">
            {{ $flujos->links() }}
        </div>
    </div>

    <style>
        .hover-up { transition: all 0.25s ease; }
        .hover-up:hover { transform: translateY(-5px); box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08) !important; }
        
        .bg-soft-indigo { background-color: #eef2ff; }
        .text-indigo { color: #4f46e5; }
        
        .bg-soft-success { background-color: #ecfdf5; }
        .bg-soft-secondary { background-color: #f8f9fa; }
        
        .icon-box { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
        .text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .avatar-xs { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; }
        
        .btn-primary-neo {
            background-color: #4f46e5;
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s;
            display: inline-block;
        }
        .btn-primary-neo:hover { background-color: #4338ca; color: white; }
    </style>
</x-base-layout>