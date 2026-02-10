<x-base-layout>
    <div class="app-container">
        {{-- Mensajes de Éxito --}}
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        <header class="main-header mb-4">
            <div class="header-content">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('correspondencia.procesos.index') }}" class="btn btn-sm btn-light border-0 shadow-sm rounded-circle">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="page-title h3 mb-0">Detalle del Proceso #{{ $proceso->id }}</h1>
                </div>
                <span class="badge bg-primary px-3 py-2 mt-2" style="background-color: #4f46e5 !important;">
                    <i class="fas fa-project-diagram me-1"></i> {{ $proceso->flujo->nombre }}
                </span>
            </div>
        </header>

        <div class="row g-4">
            {{-- Columna Info --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 15px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold m-0">Descripción del Proceso</h5>
                        <a href="{{ route('correspondencia.procesos.edit', $proceso) }}" class="btn btn-sm btn-outline-secondary border-0">
                            <i class="fas fa-edit me-1"></i> Editar
                        </a>
                    </div>
                    <p class="text-muted" style="line-height: 1.6;">
                        {{ $proceso->detalle ?: 'No se proporcionaron detalles adicionales para este proceso.' }}
                    </p>
                    <hr class="opacity-10">
                    <div class="row text-center mt-3">
                        <div class="col-6 border-end">
                            <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Iniciado por</small>
                            <div class="d-flex align-items-center justify-content-center mt-1">
                                <div class="avatar-xs bg-light rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.7rem;">
                                    {{ substr($proceso->creador->name, 0, 1) }}
                                </div>
                                <strong class="text-dark">{{ $proceso->creador->name }}</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Fecha de Apertura</small>
                            <strong class="text-dark"><i class="far fa-calendar-alt me-1"></i> {{ $proceso->created_at->format('d/m/Y') }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna Usuarios --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold m-0">Equipo de Trabajo</h5>
                        <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAsignar">
                            <i class="fas fa-user-plus me-1"></i> Asignar
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <ul class="list-group list-group-flush">
                            @forelse($proceso->usuarios as $user)
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-soft-indigo text-indigo rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; background-color: #eef2ff; color: #4f46e5;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->name }}</div>
                                        <small class="text-muted d-block" style="font-size: 0.8rem;">
                                            <i class="fas fa-tag me-1 small"></i> {{ $user->pivot->detalle ?: 'Sin rol definido' }}
                                        </small>
                                    </div>
                                </div>
                                {{-- RUTA ACTUALIZADA A: procesos.removerUsuario --}}
                                <form action="{{ route('correspondencia.procesos.removerUsuario', [$proceso->id, $user->id]) }}" method="POST">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-2" onclick="return confirm('¿Remover a este usuario del proceso?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </li>
                            @empty
                            <div class="text-center py-4">
                                <i class="fas fa-users-slash fa-2x text-light mb-2"></i>
                                <p class="text-muted small">No hay usuarios asignados a este proceso todavía.</p>
                            </div>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL ASIGNAR - RUTA ACTUALIZADA A: procesos.asignarUsuario --}}
    <div class="modal fade" id="modalAsignar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('correspondencia.procesos.asignarUsuario', $proceso) }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 15px;">
                @csrf
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">Asignar integrante al equipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Seleccionar Usuario</label>
                        <select name="user_id" class="form-select select-picker shadow-none" required>
                            <option value="">Seleccione un usuario...</option>
                            @foreach(\App\Models\User::all() as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Rol o Responsabilidad</label>
                        <input type="text" name="detalle" class="form-control shadow-none" placeholder="Ej: Revisor Técnico, Aprobador Final...">
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: #4f46e5;">
                        Confirmar Asignación
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-base-layout>