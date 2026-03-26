<x-base-layout>
    <div class="container-fluid py-4 px-lg-5">
        
        {{-- Header con Navegación y Acciones --}}
        <div class="d-md-flex align-items-center justify-content-between mb-5">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-dots mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('interactions.outcomes.index') }}" class="text-muted text-decoration-none">Resultados</a></li>
                        <li class="breadcrumb-item active">Detalle de Gestión</li>
                    </ol>
                </nav>
                <h2 class="fw-black tracking-tight text-dark mb-1">{{ $outcome->name }}</h2>
                <p class="text-secondary opacity-75">Visualización completa de la métrica de éxito de este resultado.</p>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
                <a href="{{ route('interactions.outcomes.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border text-decoration-none">
                    <i class="feather-arrow-left me-1"></i> Volver
                </a>
                <a href="{{ route('interactions.outcomes.edit', $outcome->id) }}" class="btn btn-dark rounded-pill px-4 shadow-sm d-flex align-items-center gap-2">
                    <i class="feather-edit-3"></i> <span>Editar</span>
                </a>
            </div>
        </div>

        <div class="row g-4">
            {{-- Columna Principal --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold text-dark mb-0">Información del Registro</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block" style="letter-spacing: 1px;">Identificador</label>
                                <div class="p-3 bg-light rounded-3 fw-bold text-dark border-start border-success border-3">
                                    <span class="text-success me-1">#</span>{{ $outcome->id }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block" style="letter-spacing: 1px;">Nombre del Resultado</label>
                                <div class="p-3 bg-light rounded-3 fw-bold text-dark">
                                    <i class="feather-check-circle me-2 text-success"></i> {{ $outcome->name }}
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block" style="letter-spacing: 1px;">Cronología de Creación</label>
                                <div class="p-3 bg-light rounded-3 text-dark">
                                    <i class="feather-calendar me-2 text-muted"></i> 
                                    @if($outcome->created_at)
                                        Registrado el {{ $outcome->created_at->format('d \d\e F, Y \a \l\a\s H:i') }}
                                        <span class="badge bg-white text-success border ms-2 rounded-pill shadow-sm px-3">
                                            {{ $outcome->created_at->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="text-muted italic">Fecha de registro no disponible</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card de Integridad y Eliminación --}}
                @php 
                    $totalRelaciones = ($outcome->interactions_count ?? 0) + ($outcome->seguimientos_count ?? 0); 
                @endphp
                
                <div class="card border-0 shadow-sm rounded-4 bg-white border-start border-4 border-{{ $totalRelaciones > 0 ? 'info' : 'success' }} overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="d-flex gap-3">
                                <div class="icon-shape bg-light rounded-circle text-{{ $totalRelaciones > 0 ? 'info' : 'success' }}">
                                    <i class="feather-{{ $totalRelaciones > 0 ? 'lock' : 'trash-2' }}"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">Estado de Integridad y Borrado</h6>
                                    @if($totalRelaciones > 0)
                                        <p class="mb-0 small text-muted">
                                            Este resultado es una <strong>pieza clave</strong>. No puede ser eliminado porque forma parte de {{ $outcome->interactions_count }} interacciones y {{ $outcome->seguimientos_count }} seguimientos.
                                        </p>
                                    @else
                                        <p class="mb-3 small text-muted">Este registro no tiene actividad vinculada y puede ser eliminado permanentemente.</p>
                                        <form action="{{ route('interactions.outcomes.destroy', $outcome->id) }}" method="POST" class="formEliminar">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-4">
                                                <i class="feather-trash-2 me-1"></i> Eliminar ahora
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna Lateral: Impacto --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-lg rounded-4 bg-success text-white overflow-hidden mb-4">
                    <div class="card-body p-4 position-relative">
                        <i class="feather-trending-up position-absolute end-0 bottom-0 mb-n4 me-n3 opacity-25" style="font-size: 8rem;"></i>
                        <h5 class="fw-bold mb-4 opacity-75">Impacto en Gestiones</h5>
                        <div class="display-3 fw-black mb-1 tracking-tight">{{ $totalRelaciones }}</div>
                        <p class="fw-bold small mb-4">Usos totales en el sistema</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="feather-info me-2 text-success"></i> Recomendación</h6>
                    <p class="small text-muted mb-0">
                        Los resultados permiten medir la efectividad de las interacciones. Asegúrate de que los nombres sean claros para los reportes.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f7f6; }
        .fw-black { font-weight: 800; }
        .fs-xs { font-size: 0.7rem; }
        .tracking-tight { letter-spacing: -0.03em; }
        .btn-white { background: #fff; color: #444; border: 1px solid #eee; }
        .btn-white:hover { background: #f8f9fa; color: #000; border-color: #ddd; }
        .icon-shape { width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .breadcrumb-dots .breadcrumb-item + .breadcrumb-item::before { content: "•"; color: #ccc; padding: 0 1rem; }
    </style>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const forms = document.querySelectorAll('.formEliminar');
            forms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Confirmar eliminación?',
                        text: "Esta acción borrará el resultado de forma permanente.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    }).then((result) => { if (result.isConfirmed) this.submit(); });
                });
            });
        });
    </script>
    @endpush
</x-base-layout>