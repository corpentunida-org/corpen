<x-base-layout>
    <div class="app-container py-4">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center" style="border-radius: 12px; background-color: #ecfdf5; color: #065f46;">
                <i class="fas fa-check-circle me-3 fs-4"></i> {{ session('success') }}
            </div>
        @endif

        <header class="main-header mb-4">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('correspondencia.flujos.show', $proceso->flujo_id) }}" class="btn btn-white shadow-sm rounded-circle border d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Volver al flujo">
                        <i class="fas fa-arrow-left text-muted"></i>
                    </a>
                    <div>
                        <h1 class="h3 fw-bold mb-0">{{ $proceso->nombre }}</h1>
                        <span class="badge bg-soft-primary mt-1" style="background-color: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe;">
                            <i class="fas fa-project-diagram me-1"></i> {{ $proceso->flujo->nombre }}
                        </span>
                    </div>
                </div>

                <a href="{{ route('correspondencia.flujos.show', $proceso->flujo_id) }}" class="btn btn-outline-indigo rounded-pill px-3 fw-bold shadow-sm border-2">
                    <i class="bi bi-diagram-3 me-2"></i>Ver Estructura del Flujo
                </a>
            </div>
        </header>

        <div class="row g-4">
            {{-- COLUMNA PRINCIPAL: INFO Y CORRESPONDENCIAS --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px;">
                    <h5 class="fw-bold mb-3 d-flex align-items-center">
                        <i class="fas fa-info-circle me-2 text-primary"></i> Detalles del Paso
                    </h5>
                    <p class="text-muted mb-4" style="line-height: 1.7;">
                        {{ $proceso->detalle ?: 'Sin descripción adicional para este paso del flujo.' }}
                    </p>
                    
                    <div class="row g-3 bg-light p-3 rounded-4">
                        <div class="col-md-6">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Configurado por</small>
                            <div class="fw-bold text-dark">{{ $proceso->creador->name ?? 'Sistema' }}</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Fecha de Creación</small>
                            <div class="fw-bold text-dark">{{ $proceso->created_at->format('d/m/Y') }}</div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold m-0 d-flex align-items-center">
                            <i class="fas fa-envelope-open-text me-2 text-indigo"></i> Documentos en este paso
                        </h5>
                        <span class="badge bg-indigo text-white rounded-pill px-3">
                            {{ $proceso->flujo->correspondencias->where('proceso_actual_id', $proceso->id)->count() }} Activos
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 small fw-bold text-muted ps-3 text-uppercase">Radicado / Asunto</th>
                                    <th class="border-0 small fw-bold text-muted text-center text-uppercase">Gestión</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($proceso->flujo->correspondencias->where('proceso_actual_id', $proceso->id) as $corr)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold text-indigo mb-0">{{ $corr->nro_radicado }}</div>
                                            <div class="small text-muted">{{ Str::limit($corr->asunto, 60) }}</div>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('correspondencia.correspondencias.show', $corr) }}" 
                                               class="btn btn-sm btn-white border shadow-sm rounded-pill px-3 fw-bold text-primary">
                                                <i class="fas fa-eye me-1"></i> Ver Trámite
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-5">
                                            <div class="opacity-25 mb-3">
                                                <i class="fas fa-folder-open fa-3x text-muted"></i>
                                            </div>
                                            <p class="text-muted small">No hay correspondencias pendientes en esta etapa.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- COLUMNA LATERAL: EQUIPO --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold m-0">Responsables</h5>
                        <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAsignar">
                            <i class="fas fa-plus me-1"></i> Añadir
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <div class="list-group list-group-flush">
                            @forelse($proceso->usuariosAsignados as $asignacion)
                            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center bg-transparent border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm text-white rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold shadow-sm" 
                                         style="width: 38px; height: 38px; background: linear-gradient(135deg, #6366f1, #4f46e5); font-size: 0.8rem;">
                                        {{ strtoupper(substr($asignacion->usuario->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small">{{ $asignacion->usuario->name }}</div>
                                        <span class="text-muted" style="font-size: 0.7rem;">
                                            {{ $asignacion->detalle ?: 'Integrante' }}
                                        </span>
                                    </div>
                                </div>
                                <form action="{{ route('correspondencia.procesos.removerUsuario', [$proceso->id, $asignacion->user_id]) }}" method="POST">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0 border-0" onclick="return confirm('¿Remover del equipo?')">
                                        <i class="fas fa-minus-circle"></i>
                                    </button>
                                </form>
                            </div>
                            @empty
                            <div class="text-center py-5">
                                <i class="fas fa-users text-light fa-3x mb-3 opacity-50"></i>
                                <p class="text-muted mt-2 small">Sin responsables asignados.</p>
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
                        <label class="form-label fw-bold small text-muted">USUARIO RESPONSABLE</label>
                        <select name="user_id" class="form-select border-0 bg-light p-3" style="border-radius: 12px;" required>
                            <option value="">Seleccione un usuario...</option>
                            @foreach($usuarios_disponibles as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">FUNCIÓN O ROL</label>
                        <input type="text" name="detalle" class="form-control border-0 bg-light p-3" style="border-radius: 12px;" placeholder="Ej: Revisor principal, Aprobador final...">
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow" style="background: #4f46e5;">Confirmar Asignación</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .text-indigo { color: #4f46e5 !important; }
        .bg-indigo { background-color: #4f46e5 !important; }
        .btn-outline-indigo { color: #4f46e5; border-color: #4f46e5; }
        .btn-outline-indigo:hover { background-color: #4f46e5; color: white; }
        .bg-soft-primary { background-color: #f0f4ff; color: #4f46e5; }
        .table-hover tbody tr:hover { background-color: #f8faff; transition: 0.2s; }
    </style>
</x-base-layout>