<x-base-layout>
    <div class="app-container">
        <header class="main-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title h3 fw-bold">Centro de Notificaciones</h1>
                <p class="text-muted">Gestión de alertas y comunicaciones entre procesos.</p>
            </div>
        </header>

        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4">Estado</th>
                            <th>Remitente</th>
                            <th>Mensaje</th>
                            <th>Proceso (Origen/Destino)</th>
                            <th>Fecha</th>
                            <th class="text-end px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notificaciones as $notificacion)
                        <tr class="{{ $notificacion->estado == 'pendiente' ? 'fw-bold border-start border-4 border-primary' : 'opacity-75' }}">
                            <td class="px-4">
                                @if($notificacion->estado == 'pendiente')
                                    <span class="badge rounded-pill bg-primary">Nuevo</span>
                                @else
                                    <span class="badge rounded-pill bg-light text-muted border">Leído</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs bg-soft-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        {{ substr($notificacion->usuarioEnvia->name, 0, 1) }}
                                    </div>
                                    <span>{{ $notificacion->usuarioEnvia->name }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 250px;">
                                    {{ $notificacion->mensaje }}
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    <span class="text-primary">#{{ $notificacion->proceso_origen_id }}</span> 
                                    <i class="fas fa-long-arrow-alt-right mx-1 text-muted"></i> 
                                    <span class="text-success">#{{ $notificacion->proceso_destino_id }}</span>
                                </div>
                            </td>
                            <td class="small text-muted">
                                {{ $notificacion->created_at->diffForHumans() }}
                            </td>
                            <td class="text-end px-4">
                                <div class="btn-group">
                                    <a href="{{ route('correspondencia.notificaciones.show', $notificacion) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($notificacion->estado == 'pendiente')
                                    <form action="{{ route('correspondencia.notificaciones.marcarLeida', $notificacion->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Marcar como leída">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-bell-slash fa-3x mb-3 opacity-20"></i>
                                <p>No tienes notificaciones en este momento.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-transparent border-0">
                {{ $notificaciones->links() }}
            </div>
        </div>
    </div>
</x-base-layout>