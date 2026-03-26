<x-base-layout>
    <div class="container-fluid py-4 px-lg-5">
        
        {{-- Header con Estilo Pro --}}
        <div class="d-md-flex align-items-center justify-content-between mb-5">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-dots mb-2">
                        <li class="breadcrumb-item"><a href="#" class="text-muted text-decoration-none">Configuración</a></li>
                        <li class="breadcrumb-item active">Tipos de Interacción</li>
                    </ol>
                </nav>
                <h2 class="fw-black tracking-tight text-dark mb-1">Tipos de Interacción</h2>
                <p class="text-secondary opacity-75">Categoriza la naturaleza de tus comunicaciones con los clientes.</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('interactions.types.create') }}" class="btn btn-dark btn-lg rounded-pill px-4 shadow-sm d-flex align-items-center gap-2">
                    <i class="feather-plus-circle"></i> <span>Nuevo Tipo</span>
                </a>
            </div>
        </div>

        {{-- Mini Stats --}}
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape bg-warning-soft text-warning rounded-3">
                            <i class="feather-tag"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Categorías Definidas</small>
                            <span class="h4 fw-bold mb-0">{{ $types->total() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card de Tabla --}}
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <form action="{{ route('interactions.types.index') }}" method="GET" class="position-relative">
                    <i class="feather-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" name="search" 
                           class="form-control form-control-lg ps-5 border-0 bg-light rounded-3" 
                           placeholder="Buscar tipo de interacción..." 
                           value="{{ request('search') }}">
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="ps-4 py-3 border-0 text-uppercase fs-xs fw-bold text-muted" style="letter-spacing: 1px;">Nombre de Categoría</th>
                            <th class="text-center border-0 text-uppercase fs-xs fw-bold text-muted">Uso en Sistema</th>
                            <th class="text-end pe-4 border-0 text-uppercase fs-xs fw-bold text-muted">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($types as $type)
                            <tr>
                                <td class="ps-4 py-4">
                                    {{-- Nombre clicable que lleva al Show --}}
                                    <a href="{{ route('interactions.types.show', $type->id) }}" class="d-flex align-items-center text-decoration-none link-name-container">
                                        <div class="avatar-letter bg-soft-primary text-primary me-3">
                                            {{ substr($type->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark name-text">{{ $type->name }}</h6>
                                            <small class="text-muted italic fs-xs">ID Interno: #{{ $type->id }}</small>
                                        </div>
                                    </a>
                                </td>
                                <td class="text-center">
                                    @if($type->interactions_count > 0)
                                        <div class="d-inline-flex align-items-center px-3 py-1 rounded-pill bg-dark text-white fs-xs fw-bold">
                                            <i class="feather-layers me-1 opacity-50"></i> {{ $type->interactions_count }} REGISTROS
                                        </div>
                                    @else
                                        <span class="text-muted fs-xs fw-medium italic">Sin actividad registrada</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        {{-- El botón de editar se mantiene --}}
                                        <a href="{{ route('interactions.types.edit', $type->id) }}" class="btn btn-icon-modern" data-bs-toggle="tooltip" title="Editar Categoría">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                        
                                        @if($type->interactions_count == 0)
                                            <form action="{{ route('interactions.types.destroy', $type->id) }}" method="POST" class="formEliminar d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-icon-modern text-danger" data-bs-toggle="tooltip" title="Eliminar">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="btn btn-icon-modern text-muted opacity-25" 
                                                  data-bs-toggle="tooltip" 
                                                  title="Protegido: se está usando en {{ $type->interactions_count }} interacciones">
                                                <i class="feather-lock"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <div class="py-4">
                                        <img src="https://illustrations.popsy.co/gray/data-analysis.svg" alt="Empty" style="width: 180px;" class="mb-3 opacity-75">
                                        <h5 class="fw-bold text-dark">No hay tipos definidos</h5>
                                        <p class="text-muted">Crea una categoría para empezar a organizar las interacciones.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($types->hasPages())
                <div class="card-footer bg-white border-0 py-4 px-4">
                    {{ $types->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Estilos integrados para no depender de archivos externos --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');

        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f7f6; }
        .fw-black { font-weight: 800; }
        .fs-xs { font-size: 0.7rem; }
        .tracking-tight { letter-spacing: -0.02em; }
        
        /* Icon Shapes */
        .icon-shape { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
        .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
        .bg-soft-primary { background-color: #f0f7ff; }

        /* Avatars Modernos */
        .avatar-letter {
            width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;
            border-radius: 10px; font-weight: 700; border: 1px solid rgba(0,0,0,0.05);
            text-transform: uppercase;
        }

        /* Efecto de enlace en el nombre */
        .link-name-container:hover .name-text {
            color: #0d6efd !important;
            text-decoration: underline;
        }
        .link-name-container:hover .avatar-letter {
            transform: scale(1.05);
            transition: transform 0.2s ease;
        }

        /* Botones de Icono Modernos */
        .btn-icon-modern {
            width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 10px; border: 1px solid #eee; background: white; color: #555; transition: all 0.2s;
            text-decoration: none;
        }
        .btn-icon-modern:hover { background: #000; color: #fff; border-color: #000; }
        .btn-icon-modern.text-danger:hover { background: #ff4d4d; color: #fff; border-color: #ff4d4d; }

        /* Estilo de Tabla */
        .table tbody tr { border-bottom: 1px solid #f8f9fa; transition: background 0.2s ease; }
        .table tbody tr:hover { background-color: #fcfcfc; }
        .breadcrumb-dots .breadcrumb-item + .breadcrumb-item::before { content: "•"; color: #ccc; padding: 0 1rem; }
    </style>
</x-base-layout>