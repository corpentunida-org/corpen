<x-base-layout>
    <div class="container-fluid py-4 px-lg-5">
        
        {{-- Header con Glassmorphism --}}
        <div class="d-md-flex align-items-center justify-content-between mb-5">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-dots mb-2">
                        <li class="breadcrumb-item"><a href="#" class="text-muted">Configuración</a></li>
                        <li class="breadcrumb-item active">Canales</li>
                    </ol>
                </nav>
                <h2 class="fw-black tracking-tight text-dark mb-1">Canales de Interacción</h2>
                <p class="text-secondary opacity-75">Configura y monitorea los medios de entrada de tus leads.</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('interactions.channels.create') }}" class="btn btn-dark btn-lg rounded-pill px-4 shadow-sm d-flex align-items-center gap-2">
                    <i class="feather-plus-circle"></i> <span>Añadir Canal</span>
                </a>
            </div>
        </div>

        {{-- Stats Cards (Le da un look de Dashboard profesional) --}}
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape bg-primary-soft text-primary rounded-3">
                            <i class="feather-radio"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Total Canales</small>
                            <span class="h4 fw-bold mb-0">{{ $channels->total() }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape bg-success-soft text-success rounded-3">
                            <i class="feather-activity"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Canales en Uso</small>
                            <span class="h4 fw-bold mb-0">{{ $channels->where('interactions_count', '>', 0)->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros y Tabla --}}
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <form action="{{ route('interactions.channels.index') }}" method="GET" class="position-relative">
                    <i class="feather-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" name="search" 
                           class="form-control form-control-lg ps-5 border-0 bg-light rounded-3" 
                           placeholder="Filtrar por nombre..." 
                           value="{{ request('search') }}">
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="ps-4 py-3 border-0 text-uppercase fs-xs fw-bold text-muted" style="letter-spacing: 1px;">Canal de Comunicación</th>
                            <th class="text-center border-0 text-uppercase fs-xs fw-bold text-muted">Volumen</th>
                            <th class="text-end pe-4 border-0 text-uppercase fs-xs fw-bold text-muted">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($channels as $channel)
                            <tr>
                                <td class="ps-4 py-4">
                                    {{-- El nombre ahora es un enlace --}}
                                    <a href="{{ route('interactions.channels.show', $channel->id) }}" class="d-flex align-items-center text-decoration-none link-name-container">
                                        <div class="avatar-letter bg-{{ ['primary','info','warning','danger'][($channel->id % 4)] }} text-white me-3">
                                            {{ substr($channel->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark name-text">{{ $channel->name }}</h6>
                                            <small class="text-muted">Creado el {{ $channel->created_at->format('d M, Y') }}</small>
                                        </div>
                                    </a>
                                </td>
                                <td class="text-center">
                                    @if($channel->interactions_count > 0)
                                        <div class="d-inline-flex align-items-center px-3 py-1 rounded-pill bg-dark text-white fs-xs fw-bold">
                                            {{ $channel->interactions_count }} <span class="opacity-50 ms-1">REGISTROS</span>
                                        </div>
                                    @else
                                        <span class="text-muted fs-xs fw-medium italic">Sin actividad</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('interactions.channels.edit', $channel->id) }}" class="btn btn-icon-modern" data-bs-toggle="tooltip" title="Editar">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                        
                                        @if($channel->interactions_count == 0)
                                            <form action="{{ route('interactions.channels.destroy', $channel->id) }}" method="POST" class="formEliminar d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-icon-modern text-danger" data-bs-toggle="tooltip" title="Eliminar">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="btn btn-icon-modern text-muted opacity-25" data-bs-toggle="tooltip" title="Canal protegido (tiene registros)">
                                                <i class="feather-lock"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <img src="https://illustrations.popsy.co/gray/no-messages.svg" alt="Empty" style="width: 200px;" class="mb-3">
                                    <h5 class="text-dark fw-bold">No hay canales aquí</h5>
                                    <p class="text-muted">Empieza creando uno para organizar tus interacciones.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($channels->hasPages())
                <div class="card-footer bg-white border-0 py-4 px-4">
                    {{ $channels->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap');

        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .fw-black { font-weight: 800; }
        .fs-xs { font-size: 0.75rem; }
        
        /* Icon Shapes */
        .icon-shape { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
        .bg-primary-soft { background-color: #e7f0ff; }
        .bg-success-soft { background-color: #e6f9f0; }

        /* Avatars */
        .avatar-letter {
            width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;
            border-radius: 12px; font-weight: bold; text-transform: uppercase;
        }

        /* Modern Table */
        .table > :not(caption) > * > * { padding: 1rem 0.5rem; border-bottom: 1px solid #f0f0f0; }
        .table tbody tr { transition: all 0.2s ease; }
        .table tbody tr:hover { background-color: #fafafa; transform: scale(1.002); }

        /* Link Name Effect */
        .link-name-container:hover .name-text {
            color: #0d6efd !important; /* Cambia a azul primary al pasar el mouse */
            text-decoration: underline;
        }

        /* Icon Buttons */
        .btn-icon-modern {
            width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 10px; border: 1px solid #eee; background: white; color: #666; transition: all 0.2s;
        }
        .btn-icon-modern:hover { background: #000; color: #fff; border-color: #000; }
        .btn-icon-modern.text-danger:hover { background: #dc3545; color: #fff; border-color: #dc3545; }

        /* Breadcrumbs */
        .breadcrumb-dots .breadcrumb-item + .breadcrumb-item::before { content: "•"; color: #ccc; }
    </style>
</x-base-layout>