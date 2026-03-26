<x-base-layout>
    <div class="container-fluid py-4 px-lg-5">
        
        {{-- Header Pro --}}
        <div class="d-md-flex align-items-center justify-content-between mb-5">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-dots mb-2">
                        <li class="breadcrumb-item"><a href="#" class="text-muted text-decoration-none">Configuración</a></li>
                        <li class="breadcrumb-item active">Resultados de Interacción</li>
                    </ol>
                </nav>
                <h2 class="fw-black tracking-tight text-dark mb-1">Resultados de Interacción</h2>
                <p class="text-secondary opacity-75">Define los estados finales para tus gestiones comerciales.</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('interactions.outcomes.create') }}" class="btn btn-success btn-lg rounded-pill px-4 shadow-sm d-flex align-items-center gap-2">
                    <i class="feather-plus-circle"></i> <span>Nuevo Resultado</span>
                </a>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape bg-success-soft text-success rounded-3">
                            <i class="feather-check-circle"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Resultados Totales</small>
                            <span class="h4 fw-bold mb-0">{{ $outcomes->total() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla / Listado --}}
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <form action="{{ route('interactions.outcomes.index') }}" method="GET" class="position-relative">
                    <i class="feather-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" name="search" 
                           class="form-control form-control-lg ps-5 border-0 bg-light rounded-3" 
                           placeholder="Buscar resultado (ej: Venta, No contesta...)" 
                           value="{{ request('search') }}">
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="ps-4 py-3 border-0 text-uppercase fs-xs fw-bold text-muted" style="letter-spacing: 1px;">Nombre del Resultado</th>
                            <th class="text-center border-0 text-uppercase fs-xs fw-bold text-muted">Uso (Interacciones / Seguimientos)</th>
                            <th class="text-end pe-4 border-0 text-uppercase fs-xs fw-bold text-muted">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($outcomes as $outcome)
                            <tr>
                                <td class="ps-4 py-4">
                                    <a href="{{ route('interactions.outcomes.show', $outcome->id) }}" class="d-flex align-items-center text-decoration-none link-name-container">
                                        <div class="avatar-letter bg-soft-success text-success me-3">
                                            {{ substr($outcome->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark name-text">{{ $outcome->name }}</h6>
                                            <small class="text-muted fs-xs italic">Resultado de gestión</small>
                                        </div>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <span class="badge rounded-pill bg-light text-dark border px-3">
                                            Int: {{ $outcome->interactions_count }}
                                        </span>
                                        <span class="badge rounded-pill bg-light text-dark border px-3">
                                            Seg: {{ $outcome->seguimientos_count }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('interactions.outcomes.edit', $outcome->id) }}" class="btn btn-icon-modern" title="Editar">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                        
                                        @php $total = $outcome->interactions_count + $outcome->seguimientos_count; @endphp

                                        @if($total == 0)
                                            <form action="{{ route('interactions.outcomes.destroy', $outcome->id) }}" method="POST" class="formEliminar d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-icon-modern text-danger" title="Eliminar">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-icon-modern text-muted opacity-25" 
                                                    data-bs-toggle="tooltip" 
                                                    title="Bloqueado: se usa en {{ $total }} registros">
                                                <i class="feather-lock"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <img src="https://illustrations.popsy.co/gray/success.svg" alt="Empty" style="width: 150px;" class="mb-3 opacity-50">
                                    <h5 class="fw-bold text-muted">No hay resultados</h5>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-0 py-4 px-4">
                {{ $outcomes->links() }}
            </div>
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f7f6; }
        .fw-black { font-weight: 800; }
        .fs-xs { font-size: 0.7rem; }
        .bg-success-soft { background-color: rgba(40, 167, 69, 0.1); }
        .bg-soft-success { background-color: #f0fff4; }
        .avatar-letter { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-weight: 700; text-transform: uppercase; }
        .link-name-container:hover .name-text { color: #28a745 !important; text-decoration: underline; }
        .btn-icon-modern { width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; border: 1px solid #eee; background: white; color: #555; transition: 0.2s; }
        .btn-icon-modern:hover { background: #000; color: #fff; }
        .btn-icon-modern.text-danger:hover { background: #dc3545; color: #fff; }
    </style>
</x-base-layout>