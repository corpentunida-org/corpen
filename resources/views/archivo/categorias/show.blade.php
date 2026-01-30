<x-base-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                {{-- Navegación superior (Breadcrumbs) --}}
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb small">
                        <li class="breadcrumb-item"><a href="{{ route('archivo.categorias.index') }}" class="text-decoration-none text-muted">Categorías</a></li>
                        <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">{{ $categoria->nombre }}</li>
                    </ol>
                </nav>

                {{-- Encabezado de Acción --}}
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="bi bi-folder2 fs-4"></i>
                        </div>
                        <div>
                            <h2 class="fw-light m-0">{{ $categoria->nombre }}</h2>
                            <p class="text-muted small m-0">Visualizando desglose de la categoría</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('archivo.categorias.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        <a href="{{ route('archivo.categorias.edit', $categoria) }}" class="btn btn-dark rounded-pill px-4 shadow-sm">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>
                    </div>
                </div>

                <div class="row g-4">
                    {{-- Columna de Información (Sidebar de detalles) --}}
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                            <h6 class="text-uppercase text-muted small fw-bold mb-4">Datos del Registro</h6>
                            
                            <div class="mb-4">
                                <label class="text-muted small d-block mb-1">Nombre Oficial</label>
                                <span class="fw-bold text-dark fs-5">{{ $categoria->nombre }}</span>
                            </div>

                            <div class="mb-4">
                                <label class="text-muted small d-block mb-1">Total Documentos Vinculados</label>
                                <span class="badge bg-light text-primary border px-3 py-2 fs-6 fw-normal">
                                    <i class="bi bi-files me-1"></i> {{ $categoria->tipos_documento_count }} registros
                                </span>
                            </div>

                            <div class="mb-0">
                                <label class="text-muted small d-block mb-1">Fecha de Registro</label>
                                <span class="text-dark small">{{ $categoria->created_at->format('d/m/Y - h:i A') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Columna de Lista de Documentos (Contenido Principal) --}}
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-header bg-white border-0 py-3 ps-4">
                                <h6 class="m-0 fw-bold text-dark">Tipos de Documentos Asociados</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light text-uppercase small fw-bold text-muted">
                                            <tr>
                                                <th class="ps-4 py-3 border-0">Nombre del Documento</th>
                                                <th class="py-3 border-0">Estado</th>
                                                <th class="text-end pe-4 py-3 border-0">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody class="border-top-0">
                                            @forelse($categoria->tiposDocumento as $tipo)
                                                <tr class="align-middle">
                                                    <td class="ps-4 py-3">
                                                        <span class="fw-medium text-dark">{{ $tipo->nombre }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="small text-success">
                                                            <i class="bi bi-check-circle-fill me-1"></i> Activo
                                                        </span>
                                                    </td>
                                                    <td class="text-end pe-4">
                                                        {{-- Botón a la vista individual del tipo de documento si existiera --}}
                                                        <button class="btn btn-sm btn-light rounded-pill px-3 text-muted">Ver Documento</button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center py-5">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="40" class="opacity-25 mb-3">
                                                        <p class="text-muted small">No hay tipos de documentos asignados a esta categoría todavía.</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-base-layout>