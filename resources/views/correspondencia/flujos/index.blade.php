<x-base-layout>
    <div class="app-container">
        <header class="main-header">
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
                                    <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        <li><a class="dropdown-item" href="{{ route('correspondencia.flujos.edit', $flujo) }}"><i class="bi bi-pencil me-2"></i> Editar</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('correspondencia.flujos.destroy', $flujo) }}" method="POST" onsubmit="return confirm('¿Eliminar este flujo?')">
                                                @csrf @method('DELETE')
                                                <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i> Eliminar</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <h5 class="fw-bold mb-1">{{ $flujo->nombre }}</h5>
                            <p class="text-muted small mb-4 text-truncate-2">{{ $flujo->detalle ?? 'Sin descripción detallada.' }}</p>
                            
                            <div class="d-flex align-items-center pt-3 border-top">
                                <div class="avatar-xs bg-primary text-white me-2">
                                    {{ substr($flujo->usuario->name ?? '?', 0, 1) }}
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
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                        <i class="bi bi-diagram-2 display-1 text-light"></i>
                        <p class="text-muted mt-3">No hay flujos de trabajo creados aún.</p>
                    </div>
                </div>
            @endforelse
        </div>
        
        <div class="mt-4">
            {{ $flujos->links() }}
        </div>
    </div>

    <style>
        .hover-up { transition: transform 0.2s; }
        .hover-up:hover { transform: translateY(-5px); }
        .bg-soft-indigo { background-color: #eef2ff; }
        .text-indigo { color: #4f46e5; }
        .icon-box { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
        .text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .avatar-xs { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; }
    </style>
</x-base-layout>