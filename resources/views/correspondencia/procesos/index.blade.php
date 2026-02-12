<x-base-layout>
    <div class="app-container py-4">
        <header class="main-header mb-4 d-flex justify-content-between align-items-center">
            <div class="header-content">
                <h1 class="page-title h3 fw-bold">Instancias de Procesos</h1>
                <p class="page-subtitle text-muted">Ejecuciones activas de flujos de trabajo documentales.</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('correspondencia.procesos.create') }}" class="btn btn-primary px-4 shadow-sm" style="border-radius: 10px; background: linear-gradient(45deg, #4f46e5, #6366f1);">
                    <i class="fas fa-play-circle me-2"></i> Nuevo Proceso
                </a>
            </div>
        </header>

        <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th>Proceso / Nombre</th>
                            <th>Estado</th>
                            <th>Flujo</th>
                            <th>Creador</th>
                            <th>Equipo</th>
                            <th class="text-end px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($procesos as $proceso)
                        <tr>
                            <td class="px-4 text-muted fw-bold">#{{ $proceso->id }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $proceso->nombre }}</div>
                                <small class="text-muted d-block text-truncate" style="max-width: 200px;">
                                    {{ $proceso->detalle ?: 'Sin descripción' }}
                                </small>
                            </td>
                            <td>
                                @if($proceso->activo)
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3">Activo</span>
                                @else
                                    <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                {{-- COLUMNA MODIFICADA: Ahora es un enlace --}}
                                <a href="{{ route('correspondencia.flujos.show', $proceso->flujo_id) }}" 
                                   class="badge px-3 py-2 text-decoration-none d-inline-flex align-items-center gap-2 flujo-link" 
                                   title="Ver estructura del flujo">
                                    <i class="fas fa-project-diagram"></i>
                                    {{ $proceso->flujo->nombre ?? 'N/A' }}
                                </a>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs bg-light rounded-circle me-2 d-flex align-items-center justify-content-center border" style="width: 28px; height: 28px; font-size: 0.7rem;">
                                        {{ substr($proceso->creador->name, 0, 1) }}
                                    </div>
                                    <small>{{ $proceso->creador->name }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="avatar-stack">
                                    @foreach($proceso->usuariosAsignados->take(3) as $asignacion)
                                        <div class="avatar-circle" title="{{ $asignacion->usuario->name }}">
                                            {{ strtoupper(substr($asignacion->usuario->name, 0, 1)) }}
                                        </div>
                                    @endforeach
                                    @if($proceso->usuariosAsignados->count() > 3)
                                        <div class="avatar-circle bg-secondary text-white">+{{ $proceso->usuariosAsignados->count() - 3 }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end px-4">
                                <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                    <a href="{{ route('correspondencia.procesos.show', $proceso) }}" class="btn btn-white btn-sm border" title="Gestionar Equipo">
                                        <i class="fas fa-users-cog text-primary"></i>
                                    </a>
                                    <a href="{{ route('correspondencia.procesos.edit', $proceso) }}" class="btn btn-white btn-sm border">
                                        <i class="fas fa-edit text-warning"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">
            {{ $procesos->links() }}
        </div>
    </div>

    <style>
        .flujo-link {
            background-color: #eef2ff;
            color: #4f46e5;
            border: 1px solid #c7d2fe;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .flujo-link:hover {
            background-color: #4f46e5;
            color: white !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }
        .avatar-stack { display: flex; align-items: center; }
        .avatar-circle { 
            width: 32px; height: 32px; background: #6366f1; color: white; 
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 10px; font-weight: bold; border: 2px solid white; margin-left: -12px;
            transition: transform 0.2s;
        }
        .avatar-circle:first-child { margin-left: 0; }
        .avatar-circle:hover { transform: translateY(-3px); z-index: 10; }
        .bg-success-subtle { background-color: #d1e7dd; }
        .bg-danger-subtle { background-color: #f8d7da; }
    </style>
</x-base-layout>