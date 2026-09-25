<x-base-layout>
    <div class="container-fluid py-4 px-lg-5">

        {{-- Alertas Flotantes --}}
        @if (session('success') || session('error'))
            <div class="position-fixed top-0 end-0 p-4" style="z-index: 9999;">
                <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} border-0 shadow-lg animate__animated animate__fadeInRight rounded-4 d-flex align-items-center" role="alert">
                    <i class="feather-{{ session('success') ? 'check-circle' : 'alert-octagon' }} me-3 fs-4"></i>
                    <div>
                        <span class="fw-bold">{{ session('success') ? '¡Logrado!' : 'Atención' }}</span>
                        <p class="mb-0 small opacity-75">{{ session('success') ?? session('error') }}</p>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        {{-- Header con Estilo SaaS --}}
        <div class="d-md-flex align-items-center justify-content-between mb-5">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-dots mb-2">
                        <li class="breadcrumb-item"><a href="#" class="text-muted text-decoration-none">Configuración</a></li>
                        <li class="breadcrumb-item active">Motivos No Efectivo</li>
                    </ol>
                </nav>
                <h2 class="fw-black tracking-tight text-dark mb-1">Motivos No Efectivo</h2>
                <p class="text-secondary opacity-75">Define las razones por las que una gestión resulta No Efectiva.</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('interactions.motivos_no_efectivo.create') }}" class="btn btn-indigo btn-lg rounded-pill px-4 shadow-sm d-flex align-items-center gap-2">
                    <i class="feather-plus-circle"></i> <span>Nuevo Motivo</span>
                </a>
            </div>
        </div>

        {{-- Stats Card --}}
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-dark">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape bg-indigo-soft text-indigo rounded-3">
                            <i class="feather-x-circle"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Motivos Definidos</small>
                            <span class="h4 fw-bold mb-0">{{ $motivos->total() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contenedor de Tabla Moderna --}}
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <form action="{{ route('interactions.motivos_no_efectivo.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                    <div class="position-relative flex-grow-1" style="min-width: 240px;">
                        <i class="feather-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" name="search"
                               class="form-control form-control-lg ps-5 border-0 bg-light rounded-3"
                               placeholder="Buscar motivo..."
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
                            <th class="ps-4 py-3 border-0 text-uppercase fs-xs fw-bold text-muted" style="letter-spacing: 1px;">Motivo</th>
                            @if ($puedeElegirArea)
                                <th class="border-0 text-uppercase fs-xs fw-bold text-muted">Área</th>
                            @endif
                            <th class="text-center border-0 text-uppercase fs-xs fw-bold text-muted">Frecuencia en Seguimientos</th>
                            <th class="text-end pe-4 border-0 text-uppercase fs-xs fw-bold text-muted">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($motivos as $motivo)
                            <tr>
                                <td class="ps-4 py-4">
                                    <a href="{{ route('interactions.motivos_no_efectivo.show', $motivo->id) }}" class="d-flex align-items-center text-decoration-none link-name-container">
                                        <div class="avatar-letter bg-soft-indigo text-indigo me-3">
                                            {{ substr($motivo->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark name-text">{{ $motivo->name }}</h6>
                                            <small class="text-muted fs-xs italic">ID: #{{ $motivo->id }}</small>
                                        </div>
                                    </a>
                                </td>
                                @if ($puedeElegirArea)
                                    <td>
                                        @if ($motivo->area)
                                            <span class="badge bg-soft-indigo text-indigo">{{ strtoupper($motivo->area) }}</span>
                                        @else
                                            <span class="badge bg-light text-muted border">Compartido</span>
                                        @endif
                                    </td>
                                @endif
                                <td class="text-center">
                                    @php $usos = $motivo->seguimientos_count; @endphp

                                    @if($usos > 0)
                                        <span class="badge rounded-pill bg-indigo text-white px-3 py-2 fs-xs shadow-sm">
                                            <i class="feather-message-square me-1"></i> {{ $usos }} Vinculados
                                        </span>
                                    @else
                                        <span class="text-muted fs-xs fw-medium italic opacity-50">Sin registros</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('interactions.motivos_no_efectivo.edit', $motivo->id) }}" class="btn btn-icon-modern" data-bs-toggle="tooltip" title="Editar">
                                            <i class="feather-edit-2"></i>
                                        </a>

                                        @if($usos == 0)
                                            <form action="{{ route('interactions.motivos_no_efectivo.destroy', $motivo->id) }}" method="POST" class="formEliminar d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-icon-modern text-danger" data-bs-toggle="tooltip" title="Eliminar">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="btn btn-icon-modern text-muted opacity-25"
                                                  data-bs-toggle="tooltip"
                                                  title="Protegido: se usa en {{ $usos }} seguimientos">
                                                <i class="feather-lock"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $puedeElegirArea ? 4 : 3 }}" class="text-center py-5">
                                    <div class="py-4 opacity-50">
                                        <i class="feather-x-circle mb-3 d-block" style="font-size: 3rem;"></i>
                                        <h5 class="fw-bold">No se encontraron motivos</h5>
                                        <p class="small">Intenta con otro término o crea un nuevo motivo.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-0 py-4 px-4">
                {{ $motivos->links() }}
            </div>
        </div>
    </div>

    {{-- Estilos SaaS Indigo --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');

        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f7f6; }
        .fw-black { font-weight: 800; }
        .fs-xs { font-size: 0.7rem; }
        .tracking-tight { letter-spacing: -0.02em; }

        /* Indigo Theme */
        .btn-indigo { background-color: #6610f2; color: white; border: none; }
        .btn-indigo:hover { background-color: #520dc2; color: white; }
        .text-indigo { color: #6610f2; }
        .bg-indigo { background-color: #6610f2; }
        .bg-indigo-soft { background-color: rgba(102, 16, 242, 0.1); }
        .bg-soft-indigo { background-color: #f5f0ff; }

        .avatar-letter {
            width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;
            border-radius: 10px; font-weight: 700; text-transform: uppercase;
        }

        .link-name-container:hover .name-text { color: #6610f2 !important; text-decoration: underline; }

        .btn-icon-modern {
            width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 10px; border: 1px solid #eee; background: white; color: #555; transition: 0.2s;
            text-decoration: none;
        }
        .btn-icon-modern:hover { background: #000; color: #fff; }
        .btn-icon-modern.text-danger:hover { background: #dc3545; color: #fff; }

        .breadcrumb-dots .breadcrumb-item + .breadcrumb-item::before { content: "•"; color: #ccc; padding: 0 1rem; }

        /* Animación para las alertas */
        .animate__fadeInRight { animation: fadeInRight 0.5s; }
        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>

    {{-- Script de confirmación SweetAlert --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Iniciar tooltips
            const tooltipList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipList.forEach(el => new bootstrap.Tooltip(el));

            // Confirmación de eliminación
            const forms = document.querySelectorAll('.formEliminar');
            forms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Confirmar eliminación?',
                        text: "Esta acción borrará el motivo permanentemente.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    }).then((result) => {
                        if (result.isConfirmed) this.submit();
                    });
                });
            });
        });
    </script>
    @endpush
</x-base-layout>
