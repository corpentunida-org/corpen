<x-base-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                
                {{-- Encabezado Minimalista --}}
                <div class="d-flex align-items-center justify-content-between mb-5">
                    <div>
                        <h2 class="fw-light text-dark m-0">Categorías</h2>
                        <p class="text-muted small m-0">Administra los grupos lógicos de tus documentos.</p>
                    </div>
                    <a href="{{ route('archivo.categorias.create') }}" class="btn btn-dark btn-lg rounded-pill px-4 shadow-sm transition-all">
                        <i class="bi bi-plus-lg me-2"></i>Nueva Categoría
                    </a>
                </div>

                {{-- Card Principal --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        
                        {{-- Buscador e Información de Filtros --}}
                        <div class="p-4 border-bottom bg-white">
                            <form method="GET" action="{{ route('archivo.categorias.index') }}" class="row g-3 align-items-center">
                                <div class="col-md-8">
                                    <div class="input-group input-group-merge bg-light border-0 rounded-3 px-3">
                                        <span class="input-group-text bg-transparent border-0 text-muted">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text" name="search" class="form-control bg-transparent border-0 py-3" 
                                               placeholder="Buscar por nombre..." value="{{ request('search') }}">
                                        
                                        @if(request('search'))
                                            <a href="{{ route('archivo.categorias.index') }}" class="btn btn-transparent border-0 text-muted align-self-center">
                                                <i class="bi bi-x-circle-fill"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <span class="text-muted small">
                                        Mostrando {{ $categorias->count() }} de {{ $categorias->total() }} resultados
                                    </span>
                                </div>
                            </form>
                        </div>

                        {{-- Notificaciones --}}
                        @if (session('success'))
                            <div class="mx-4 mt-3 alert alert-soft-success border-0 small d-flex align-items-center" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <div>{{ session('success') }}</div>
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mx-4 mt-3 alert alert-soft-danger border-0 small d-flex align-items-center" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div>{{ session('error') }}</div>
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Tabla de Contenido --}}
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light text-uppercase small fw-bold text-muted">
                                    <tr>
                                        <th class="ps-4 py-3 border-0">Nombre</th>
                                        <th class="py-3 border-0">Documentos</th>
                                        <th class="text-end pe-4 py-3 border-0">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    @forelse ($categorias as $categoria)
                                        <tr class="align-middle">
                                            <td class="ps-4 py-4">
                                                {{-- Nombre cliqueable para ir al detalle (Show) --}}
                                                <a href="{{ route('archivo.categorias.show', $categoria) }}" class="text-decoration-none">
                                                    <span class="fw-medium text-dark d-block mb-0 h6 group-hover-text-primary transition-all">
                                                        {{ $categoria->nombre }}
                                                    </span>
                                                </a>
                                                <small class="text-muted">Creado {{ $categoria->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <span class="badge rounded-pill bg-white text-dark border px-3 py-2 fw-normal">
                                                    <i class="bi bi-files me-1 text-primary"></i>
                                                    {{ $categoria->tipos_documento_count ?? 0 }} 
                                                    <span class="text-muted">vínculos</span>
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end align-items-center gap-2">
                                                    
                                                    {{-- BOTÓN RÁPIDO VER: Corregido para ir a Show --}}
                                                    <a href="{{ route('archivo.categorias.show', $categoria) }}" class="btn btn-light btn-sm rounded-circle p-2" title="Ver detalles">
                                                        <i class="bi bi-eye text-muted"></i>
                                                    </a>

                                                    <div class="dropdown">
                                                        <button class="btn btn-light btn-sm rounded-circle p-2" data-bs-toggle="dropdown">
                                                            <i class="bi bi-three-dots-vertical"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                                            <li>
                                                                <a class="dropdown-item py-2" href="{{ route('archivo.categorias.show', $categoria) }}">
                                                                    <i class="bi bi-eye me-2 text-muted"></i> Ver detalles
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item py-2" href="{{ route('archivo.categorias.edit', $categoria) }}">
                                                                    <i class="bi bi-pencil me-2 text-muted"></i> Editar
                                                                </a>
                                                            </li>
                                                            <li><hr class="dropdown-divider opacity-50"></li>
                                                            <li>
                                                                {{-- Lógica de protección contra eliminación --}}
                                                                @if(($categoria->tipos_documento_count ?? 0) > 0)
                                                                    <button class="dropdown-item py-2 text-muted opacity-50" disabled title="No se puede eliminar: tiene documentos vinculados">
                                                                        <i class="bi bi-trash3 me-2"></i> Eliminar <small>(Bloqueado)</small>
                                                                    </button>
                                                                @else
                                                                    <form action="{{ route('archivo.categorias.destroy', $categoria) }}" method="POST" 
                                                                          onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?');">
                                                                        @csrf @method('DELETE')
                                                                        <button type="submit" class="dropdown-item py-2 text-danger">
                                                                            <i class="bi bi-trash3 me-2"></i> Eliminar
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-5">
                                                <div class="py-4">
                                                    <i class="bi bi-folder2-open display-4 text-light"></i>
                                                    <p class="text-muted mt-3">No hay categorías que coincidan con la búsqueda.</p>
                                                    <a href="{{ route('archivo.categorias.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">Limpiar filtros</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Paginación --}}
                <div class="d-flex justify-content-between align-items-center mt-4 px-2">
                    <div class="text-muted small">
                        Página {{ $categorias->currentPage() }} de {{ $categorias->lastPage() }}
                    </div>
                    <div>
                        {{ $categorias->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .transition-all { transition: all 0.2s ease; }
        .transition-all:hover { transform: translateY(-1px); }
        .group-hover-text-primary:hover { color: #0d6efd !important; }
        .input-group-merge:focus-within { 
            background-color: #fff !important;
            border: 1px solid #dee2e6 !important;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05) !important;
        }
        .alert-soft-success { background-color: #e6fcf5; color: #0ca678; }
        .alert-soft-danger { background-color: #fff5f5; color: #e03131; }
        .dropdown-item:active { background-color: #f8f9fa; color: inherit; }
    </style>
</x-base-layout>