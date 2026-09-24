<x-base-layout>
    <div class="container-fluid py-4 px-lg-5">

        {{-- Header con Estilo Pro --}}
        <div class="d-md-flex align-items-center justify-content-between mb-5">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-dots mb-2">
                        <li class="breadcrumb-item"><a href="#" class="text-muted text-decoration-none">Configuración</a></li>
                        <li class="breadcrumb-item active">Líneas</li>
                    </ol>
                </nav>
                <h2 class="fw-black tracking-tight text-dark mb-1">Líneas</h2>
                <p class="text-secondary opacity-75">La línea de obligación/producto que se relaciona con una interacción (créditos de Cartera, pólizas de Seguros, etc — propio de cada área).</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('interactions.lineas.create') }}" class="btn btn-dark btn-lg rounded-pill px-4 shadow-sm d-flex align-items-center gap-2">
                    <i class="feather-plus-circle"></i> <span>Nueva Línea</span>
                </a>
            </div>
        </div>

        {{-- Mini Stats --}}
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape bg-warning-soft text-warning rounded-3">
                            <i class="feather-list"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Líneas Definidas</small>
                            <span class="h4 fw-bold mb-0">{{ $lineas->total() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card de Tabla --}}
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <form action="{{ route('interactions.lineas.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                    <div class="position-relative flex-grow-1" style="min-width: 240px;">
                        <i class="feather-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" name="search"
                               class="form-control form-control-lg ps-5 border-0 bg-light rounded-3"
                               placeholder="Buscar línea..."
                               value="{{ request('search') }}">
                    </div>
                    @if ($puedeElegirArea)
                        <select name="area" class="form-select form-select-lg border-0 bg-light rounded-3"
                                style="max-width: 220px;" onchange="this.form.submit()">
                            <option value="">Todas las áreas</option>
                            <option value="compartido" @selected(request('area') === 'compartido')>Compartido</option>
                            @foreach ($areasDisponibles as $area)
                                <option value="{{ $area }}" @selected(request('area') === $area)>{{ strtoupper($area) }}</option>
                            @endforeach
                        </select>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="ps-4 py-3 border-0 text-uppercase fs-xs fw-bold text-muted" style="letter-spacing: 1px;">Nombre</th>
                            @if ($puedeElegirArea)
                                <th class="border-0 text-uppercase fs-xs fw-bold text-muted">Área</th>
                            @endif
                            <th class="text-center border-0 text-uppercase fs-xs fw-bold text-muted">Uso en Sistema</th>
                            <th class="text-end pe-4 border-0 text-uppercase fs-xs fw-bold text-muted">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lineas as $linea)
                            <tr>
                                <td class="ps-4 py-4">
                                    <a href="{{ route('interactions.lineas.show', $linea->id) }}" class="d-flex align-items-center text-decoration-none link-name-container">
                                        <div class="avatar-letter bg-soft-primary text-primary me-3">
                                            {{ substr($linea->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark name-text">{{ $linea->name }}</h6>
                                            <small class="text-muted italic fs-xs">ID Interno: #{{ $linea->id }}</small>
                                        </div>
                                    </a>
                                </td>
                                @if ($puedeElegirArea)
                                    <td>
                                        @if ($linea->area)
                                            <span class="badge bg-soft-primary text-primary">{{ strtoupper($linea->area) }}</span>
                                        @else
                                            <span class="badge bg-light text-muted border">Compartido</span>
                                        @endif
                                    </td>
                                @endif
                                <td class="text-center">
                                    @if($linea->interactions_count > 0)
                                        <div class="d-inline-flex align-items-center px-3 py-1 rounded-pill bg-dark text-white fs-xs fw-bold">
                                            <i class="feather-layers me-1 opacity-50"></i> {{ $linea->interactions_count }} REGISTROS
                                        </div>
                                    @else
                                        <span class="text-muted fs-xs fw-medium italic">Sin actividad registrada</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('interactions.lineas.edit', $linea->id) }}" class="btn btn-icon-modern" data-bs-toggle="tooltip" title="Editar">
                                            <i class="feather-edit-2"></i>
                                        </a>

                                        @if($linea->interactions_count == 0)
                                            <form action="{{ route('interactions.lineas.destroy', $linea->id) }}" method="POST" class="formEliminar d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-icon-modern text-danger" data-bs-toggle="tooltip" title="Eliminar">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="btn btn-icon-modern text-muted opacity-25"
                                                  data-bs-toggle="tooltip"
                                                  title="Protegido: se está usando en {{ $linea->interactions_count }} interacciones">
                                                <i class="feather-lock"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $puedeElegirArea ? 4 : 3 }}" class="text-center py-5">
                                    <div class="py-4">
                                        <img src="https://illustrations.popsy.co/gray/data-analysis.svg" alt="Empty" style="width: 180px;" class="mb-3 opacity-75">
                                        <h5 class="fw-bold text-dark">No hay líneas definidas</h5>
                                        <p class="text-muted">Crea una línea para empezar a clasificar las interacciones.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($lineas->hasPages())
                <div class="card-footer bg-white border-0 py-4 px-4">
                    {{ $lineas->links() }}
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

        .icon-shape { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
        .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
        .bg-soft-primary { background-color: #f0f7ff; }

        .avatar-letter {
            width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;
            border-radius: 10px; font-weight: 700; border: 1px solid rgba(0,0,0,0.05);
            text-transform: uppercase;
        }

        .link-name-container:hover .name-text {
            color: #0d6efd !important;
            text-decoration: underline;
        }
        .link-name-container:hover .avatar-letter {
            transform: scale(1.05);
            transition: transform 0.2s ease;
        }

        .btn-icon-modern {
            width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 10px; border: 1px solid #eee; background: white; color: #555; transition: all 0.2s;
            text-decoration: none;
        }
        .btn-icon-modern:hover { background: #000; color: #fff; border-color: #000; }
        .btn-icon-modern.text-danger:hover { background: #ff4d4d; color: #fff; border-color: #ff4d4d; }

        .table tbody tr { border-bottom: 1px solid #f8f9fa; transition: background 0.2s ease; }
        .table tbody tr:hover { background-color: #fcfcfc; }
        .breadcrumb-dots .breadcrumb-item + .breadcrumb-item::before { content: "•"; color: #ccc; padding: 0 1rem; }
    </style>
</x-base-layout>
