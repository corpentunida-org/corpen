<x-base-layout>
    <div class="app-container py-4">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center" style="border-radius: 12px; background-color: #ecfdf5; color: #065f46;">
                <i class="fas fa-check-circle me-3 fs-4"></i> {{ session('success') }}
            </div>
        @endif

        <header class="main-header mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('correspondencia.procesos.index') }}" class="btn btn-white shadow-sm rounded-circle border d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fas fa-arrow-left text-muted"></i>
                </a>
                <div>
                    <h1 class="h3 fw-bold mb-0">{{ $proceso->nombre }}</h1>
                    <span class="badge bg-soft-primary mt-1" style="background-color: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe;">
                        <i class="fas fa-project-diagram me-1"></i> {{ $proceso->flujo->nombre }}
                    </span>
                </div>
            </div>
        </header>

        <div class="row g-4">
            {{-- Columna Info --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                    <h5 class="fw-bold mb-3 d-flex align-items-center">
                        <i class="fas fa-align-left me-2 text-primary"></i> Información General
                    </h5>
                    <p class="text-muted mb-4" style="line-height: 1.7;">
                        {{ $proceso->detalle ?: 'Sin descripción adicional.' }}
                    </p>
                    
                    <div class="row g-3 bg-light p-3 rounded-4 mt-auto">
                        <div class="col-6">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Iniciado por</small>
                            <div class="fw-bold text-dark">{{ $proceso->creador->name }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Fecha</small>
                            <div class="fw-bold text-dark">{{ $proceso->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna Equipo --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold m-0">Equipo de Trabajo</h5>
                        <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAsignar">
                            <i class="fas fa-user-plus me-1"></i> Añadir
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <div class="list-group list-group-flush">
                            @forelse($proceso->usuariosAsignados as $asignacion)
                            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center bg-transparent">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm text-white rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold" style="width: 42px; height: 42px; background: linear-gradient(135deg, #6366f1, #4f46e5);">
                                        {{ substr($asignacion->usuario->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $asignacion->usuario->name }}</div>
                                        <span class="badge bg-light text-muted border fw-normal" style="font-size: 0.75rem;">
                                            {{ $asignacion->detalle ?: 'Integrante' }}
                                        </span>
                                    </div>
                                </div>
                                <form action="{{ route('correspondencia.procesos.removerUsuario', [$proceso->id, $asignacion->user_id]) }}" method="POST">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0 border-0" onclick="return confirm('¿Remover del equipo?')">
                                        <i class="fas fa-times-circle fs-5"></i>
                                    </button>
                                </form>
                            </div>
                            @empty
                            <div class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/3330/3330842.png" style="width: 60px; opacity: 0.3;" alt="No team">
                                <p class="text-muted mt-3 small">No se han asignado responsables aún.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL ASIGNAR --}}
    <div class="modal fade" id="modalAsignar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('correspondencia.procesos.asignarUsuario', $proceso) }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                @csrf
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">Asignar integrante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Usuario Responsable</label>
                        <select name="user_id" class="form-select border-0 bg-light p-3" style="border-radius: 12px;" required>
                            <option value="">Seleccione...</option>
                            @foreach($usuarios_disponibles as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Función dentro del Proceso</label>
                        <input type="text" name="detalle" class="form-control border-0 bg-light p-3" style="border-radius: 12px;" placeholder="Ej: Revisor, Aprobador...">
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow" style="background: #4f46e5;">Asignar ahora</button>
                </div>
            </form>
        </div>
    </div>
</x-base-layout>