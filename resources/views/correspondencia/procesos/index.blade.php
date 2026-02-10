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
                                <small class="text-muted d-block text-truncate" style="max-width: 250px;">
                                    {{ $proceso->detalle ?: 'Sin descripción' }}
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-soft-primary px-3 py-2" style="background-color: #eef2ff; color: #4f46e5; border-radius: 8px;">
                                    {{ $proceso->flujo->nombre ?? 'N/A' }}
                                </span>
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
                                    {{-- Usamos la relación corregida para evitar errores --}}
                                    @foreach($proceso->usuariosAsignados->take(3) as $asignacion)
                                        <div class="avatar-circle" title="{{ $asignacion->usuario->name }}">
                                            {{ substr($asignacion->usuario->name, 0, 1) }}
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
        .avatar-stack { display: flex; align-items: center; }
        .avatar-circle { 
            width: 32px; height: 32px; background: #6366f1; color: white; 
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 10px; font-weight: bold; border: 2px solid white; margin-left: -12px;
            transition: transform 0.2s;
        }
        .avatar-circle:first-child { margin-left: 0; }
        .avatar-circle:hover { transform: translateY(-3px); z-index: 10; }
    </style>
</x-base-layout>