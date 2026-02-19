<x-base-layout>
    <div class="app-container py-5 px-4" style="background-color: #fcfcfd; min-height: 100vh;">
        <header class="main-header mb-5 d-flex justify-content-between align-items-end">
            <div class="header-content">
                <h1 class="page-title h3 fw-semibold text-dark mb-1" style="letter-spacing: -0.5px;">Instancias de Procesos</h1>
                <p class="page-subtitle text-muted mb-0" style="font-size: 0.9rem;">Ejecuciones activas de flujos de trabajo documentales.</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('correspondencia.procesos.create') }}" class="btn btn-pastel-primary px-4 py-2 shadow-sm rounded-pill d-flex align-items-center gap-2 transition-all">
                    <i class="fas fa-plus" style="font-size: 0.8rem;"></i> <span>Nuevo Proceso</span>
                </a>
            </div>
        </header>

        <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden; background: #ffffff;">
            <div class="table-responsive">
                <table class="table table-borderless table-hover align-middle mb-0 custom-table">
                    <thead style="background-color: #f8fafc; border-bottom: 2px solid #f1f5f9;">
                        <tr>
                            <th class="px-4 py-3 text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">ID</th>
                            <th class="py-3 text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Proceso / Nombre</th>
                            <th class="py-3 text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Estado</th>
                            <th class="py-3 text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Flujo</th>
                            <th class="py-3 text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Creador</th>
                            <th class="py-3 text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Equipo</th>
                            <th class="text-end px-4 py-3 text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($procesos as $proceso)
                        <tr style="border-bottom: 1px solid #f8fafc;">
                            <td class="px-4 text-muted" style="font-size: 0.85rem;">#{{ str_pad($proceso->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <div class="fw-medium text-dark" style="font-size: 0.95rem;">{{ $proceso->nombre }}</div>
                                <small class="text-muted d-block text-truncate mt-1" style="max-width: 250px; font-size: 0.8rem;">
                                    {{ $proceso->detalle ?: 'Sin descripción adicional' }}
                                </small>
                            </td>
                            <td>
                                @if($proceso->activo)
                                    <span class="badge rounded-pill px-3 py-2 fw-medium pastel-badge-success">Activo</span>
                                @else
                                    <span class="badge rounded-pill px-3 py-2 fw-medium pastel-badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('correspondencia.flujos.show', $proceso->flujo_id) }}" 
                                   class="badge rounded-pill px-3 py-2 text-decoration-none d-inline-flex align-items-center gap-2 pastel-badge-info transition-all" 
                                   title="Ver estructura del flujo">
                                    <i class="fas fa-project-diagram" style="font-size: 0.75rem;"></i>
                                    <span class="fw-medium">{{ $proceso->flujo->nombre ?? 'N/A' }}</span>
                                </a>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs rounded-circle me-2 d-flex align-items-center justify-content-center text-primary fw-medium" 
                                         style="width: 32px; height: 32px; font-size: 0.75rem; background-color: #e0e7ff;">
                                        {{ substr($proceso->creador->name, 0, 1) }}
                                    </div>
                                    <span class="text-dark" style="font-size: 0.85rem;">{{ $proceso->creador->name }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="avatar-stack">
                                    @foreach($proceso->usuariosAsignados->take(3) as $asignacion)
                                        <div class="avatar-circle" style="background-color: #e0e7ff; color: #4f46e5; border: 2px solid #fff;" title="{{ $asignacion->usuario->name }}">
                                            {{ strtoupper(substr($asignacion->usuario->name, 0, 1)) }}
                                        </div>
                                    @endforeach
                                    @if($proceso->usuariosAsignados->count() > 3)
                                        <div class="avatar-circle text-muted" style="background-color: #f1f5f9; border: 2px solid #fff;">
                                            +{{ $proceso->usuariosAsignados->count() - 3 }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end px-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('correspondencia.procesos.show', $proceso) }}" class="btn btn-icon-pastel text-primary" title="Gestionar Equipo">
                                        <i class="fas fa-users-cog"></i>
                                    </a>
                                    <a href="{{ route('correspondencia.procesos.edit', $proceso) }}" class="btn btn-icon-pastel text-warning" title="Editar">
                                        <i class="fas fa-pen" style="font-size: 0.8rem;"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background-color: #f1f5f9; color: #cbd5e1;">
                                        <i class="fas fa-inbox fs-3"></i>
                                    </div>
                                    <h6 class="fw-medium text-dark mb-1">No hay procesos activos</h6>
                                    <p class="text-muted small mb-3">Comienza creando una nueva instancia de proceso.</p>
                                    <a href="{{ route('correspondencia.procesos.create') }}" class="btn btn-pastel-primary btn-sm rounded-pill px-4">
                                        Crear mi primer proceso
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($procesos->hasPages())
        <div class="mt-4 d-flex justify-content-center custom-pagination">
            {{ $procesos->links() }}
        </div>
        @endif
    </div>

    <style>
        /* Tipografía y transiciones generales */
        .transition-all { transition: all 0.2s ease-in-out; }
        
        /* Botón Principal Pastel */
        .btn-pastel-primary {
            background-color: #e0e7ff;
            color: #4338ca;
            border: none;
            font-weight: 500;
        }
        .btn-pastel-primary:hover {
            background-color: #c7d2fe;
            color: #3730a3;
            transform: translateY(-1px);
        }

        /* Tabla Minimalista */
        .custom-table tbody tr { transition: background-color 0.2s ease; }
        .custom-table tbody tr:hover { background-color: #fafbfc; }
        .custom-table td { vertical-align: middle; }

        /* Badges Pasteles */
        .pastel-badge-success { background-color: #dcfce7; color: #166534; }
        .pastel-badge-danger { background-color: #fee2e2; color: #991b1b; }
        .pastel-badge-info { background-color: #f1f5f9; color: #475569; }
        .pastel-badge-info:hover { background-color: #e2e8f0; color: #334155; transform: translateY(-1px); }

        /* Botones de Icono Pasteles */
        .btn-icon-pastel {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background-color: #f8fafc;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }
        .btn-icon-pastel:hover {
            background-color: #ffffff;
            border-color: #e2e8f0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            transform: translateY(-1px);
        }
        .btn-icon-pastel.text-primary:hover { color: #4338ca !important; }
        .btn-icon-pastel.text-warning:hover { color: #d97706 !important; }

        /* Avatares Apilados */
        .avatar-stack { display: flex; align-items: center; }
        .avatar-circle { 
            width: 32px; height: 32px; 
            border-radius: 50%; 
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 600; 
            margin-left: -10px;
            transition: transform 0.2s, z-index 0.2s;
            position: relative;
        }
        .avatar-circle:first-child { margin-left: 0; }
        .avatar-circle:hover { transform: translateY(-3px); z-index: 10; }

        /* Paginación Minimalista (Ajustes para Tailwind/Bootstrap defaults) */
        .custom-pagination nav svg { height: 1.25rem; }
        .custom-pagination .page-link {
            border: none;
            color: #64748b;
            background-color: transparent;
            border-radius: 8px;
            margin: 0 2px;
        }
        .custom-pagination .page-item.active .page-link {
            background-color: #e0e7ff;
            color: #4338ca;
            font-weight: 500;
        }
    </style>
</x-base-layout>