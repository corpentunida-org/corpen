<x-base-layout>
    <div class="app-container">
        <header class="main-header">
            <div class="header-content">
                <h1 class="page-title">Instancias de Procesos</h1>
                <p class="page-subtitle">Ejecuciones activas de flujos de trabajo documentales.</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('correspondencia.procesos.create') }}" class="btn-primary-neo">
                    <i class="fas fa-play-circle"></i> Nuevo Proceso
                </a>
            </div>
        </header>

        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4">ID</th>
                            <th>Flujo de Trabajo</th>
                            <th>Detalle / Nota</th>
                            <th>Creador</th>
                            <th>Usuarios</th>
                            <th class="text-end px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($procesos as $proceso)
                        <tr>
                            <td class="px-4 text-muted fw-bold">#{{ $proceso->id }}</td>
                            <td>
                                <span class="badge bg-soft-primary text-primary px-3 py-2">
                                    {{ $proceso->flujo->nombre ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <small class="text-dark d-block text-truncate" style="max-width: 200px;">
                                    {{ $proceso->detalle ?: 'Sin descripción' }}
                                </small>
                            </td>
                            <td>{{ $proceso->creador->name ?? 'N/A' }}</td>
                            <td>
                                <div class="avatar-stack">
                                    @foreach($proceso->usuarios->take(3) as $u)
                                        <div class="avatar-circle" title="{{ $u->name }}">
                                            {{ substr($u->name, 0, 1) }}
                                        </div>
                                    @endforeach
                                    @if($proceso->usuarios->count() > 3)
                                        <div class="avatar-circle bg-gray">+{{ $proceso->usuarios->count() - 3 }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end px-4">
                                <div class="btn-group">
                                    <a href="{{ route('correspondencia.procesos.show', $proceso) }}" class="btn btn-sm btn-outline-secondary" title="Gestionar Usuarios">
                                        <i class="fas fa-users-cog"></i>
                                    </a>
                                    <a href="{{ route('correspondencia.procesos.edit', $proceso) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .bg-soft-primary { background-color: #e0e7ff; color: #4338ca; }
        .avatar-stack { display: flex; }
        .avatar-circle { 
            width: 30px; height: 30px; background: #4f46e5; color: white; 
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: bold; border: 2px solid white; margin-left: -10px;
        }
        .avatar-circle:first-child { margin-left: 0; }
        .avatar-circle.bg-gray { background: #94a3b8; }
    </style>
</x-base-layout>