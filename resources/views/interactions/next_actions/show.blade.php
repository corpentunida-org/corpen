<x-base-layout>
    <div class="container-fluid py-4 px-lg-5">
        
        {{-- Header con Estilo SaaS --}}
        <div class="d-md-flex align-items-center justify-content-between mb-5">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-dots mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('interactions.next_actions.index') }}" class="text-muted text-decoration-none">Acciones</a></li>
                        <li class="breadcrumb-item active">Detalle</li>
                    </ol>
                </nav>
                <h2 class="fw-black tracking-tight text-dark mb-1">{{ $nextAction->name }}</h2>
                <p class="text-secondary opacity-75">Resumen de la acción programada y su impacto en la agenda.</p>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
                <a href="{{ route('interactions.next_actions.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border text-decoration-none text-dark">
                    <i class="feather-arrow-left me-1"></i> Volver
                </a>
                <a href="{{ route('interactions.next_actions.edit', $nextAction->id) }}" class="btn btn-indigo rounded-pill px-4 shadow-sm d-flex align-items-center gap-2 text-decoration-none">
                    <i class="feather-edit-3"></i> <span>Editar</span>
                </a>
            </div>
        </div>

        <div class="row g-4">
            {{-- Columna Principal: Datos --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold text-dark mb-0">Información del Registro</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block" style="letter-spacing: 1px;">Identificador</label>
                                <div class="p-3 bg-light rounded-3 fw-bold text-dark border-start border-indigo border-3">
                                    <span class="text-indigo me-1">#</span>{{ $nextAction->id }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block" style="letter-spacing: 1px;">Nombre de la Acción</label>
                                <div class="p-3 bg-light rounded-3 fw-bold text-dark">
                                    <i class="feather-zap me-2 text-indigo"></i> {{ $nextAction->name }}
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block" style="letter-spacing: 1px;">Fecha de Registro</label>
                                <div class="p-3 bg-light rounded-3 text-dark d-flex align-items-center justify-content-between">
                                    <span>
                                        <i class="feather-calendar me-2 text-muted"></i> 
                                        @if($nextAction->created_at)
                                            {{ $nextAction->created_at->format('d \d\e F, Y \a \l\a\s H:i') }}
                                        @else
                                            <span class="text-muted italic">Sin fecha registrada</span>
                                        @endif
                                    </span>
                                    @if($nextAction->created_at)
                                        <span class="badge bg-white text-indigo border ms-2 rounded-pill shadow-sm px-3">
                                            {{ $nextAction->created_at->diffForHumans() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card de Integridad (UX Preventiva) --}}
                @php $usos = $nextAction->seguimientos_count ?? 0; @endphp
                
                <div class="card border-0 shadow-sm rounded-4 bg-white border-start border-4 border-{{ $usos > 0 ? 'info' : 'success' }} overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="icon-shape rounded-circle bg-light text-indigo">
                                <i class="feather-{{ $usos > 0 ? 'lock' : 'trash-2' }}"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">Estado de Eliminación</h6>
                                @if($usos > 0)
                                    <p class="mb-0 small text-muted">
                                        Este registro está <strong>protegido</strong> porque se encuentra vinculado a <strong>{{ $usos }}</strong> seguimientos. No puede ser eliminado para no romper el historial de la agenda.
                                    </p>
                                @else
                                    <p class="mb-3 small text-muted">Esta acción no tiene actividad vinculada y puede ser eliminada de forma segura.</p>
                                    <form action="{{ route('interactions.next_actions.destroy', $nextAction->id) }}" method="POST" class="formEliminar">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-4">
                                            <i class="feather-trash-2 me-1"></i> Eliminar Registro
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna Lateral: Estadísticas --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-lg rounded-4 bg-indigo text-white overflow-hidden mb-4">
                    <div class="card-body p-4 position-relative">
                        <i class="feather-calendar position-absolute end-0 bottom-0 mb-n4 me-n3 opacity-25" style="font-size: 8rem;"></i>
                        <h5 class="fw-bold mb-4 opacity-75">Impacto Operativo</h5>
                        <div class="display-3 fw-black mb-1 tracking-tight">{{ $usos }}</div>
                        <p class="fw-bold small mb-0">Tareas programadas</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 text-center">
                    <img src="https://illustrations.popsy.co/gray/success.svg" alt="KPI" style="width: 120px;" class="mb-3 mx-auto">
                    <h6 class="fw-bold text-dark mb-2">Agenda Eficiente</h6>
                    <p class="small text-muted mb-0">Las próximas acciones ayudan a que los agentes nunca olviden un compromiso.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Estilos Indigo --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f7f6; }
        .fw-black { font-weight: 800; }
        .fs-xs { font-size: 0.7rem; }
        .tracking-tight { letter-spacing: -0.03em; }
        
        .btn-indigo { background-color: #6610f2; color: white; border: none; }
        .btn-indigo:hover { background-color: #520dc2; color: white; }
        .text-indigo { color: #6610f2; }
        .bg-indigo { background-color: #6610f2; }
        .btn-white { background: #fff; color: #444; border: 1px solid #eee; }
        .btn-white:hover { background: #f8f9fa; color: #000; }

        .icon-shape { width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .breadcrumb-dots .breadcrumb-item + .breadcrumb-item::before { content: "•"; color: #ccc; padding: 0 1rem; }
    </style>

    {{-- Script de confirmación --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const forms = document.querySelectorAll('.formEliminar');
            forms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Confirmar eliminación?',
                        text: "Esta acción borrará la acción permanentemente.",
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