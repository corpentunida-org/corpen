<x-base-layout>
    <div class="app-container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title h3 fw-bold m-0">Trazabilidad de Correspondencia</h1>
                <p class="text-muted">Historial de gestiones y cambios de estado por radicado.</p>
            </div>
            <a href="{{ route('correspondencia.correspondencias-procesos.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4">
                <i class="fas fa-plus me-2"></i> Registrar Gestión
            </a>
        </div>

        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-body">
                <form action="{{ route('correspondencia.correspondencias-procesos.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Buscar Radicado u Observación..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="proceso_id" class="form-select">
                            <option value="">Todas las Etapas</option>
                            @foreach($procesos_maestros as $pm)
                                <option value="{{ $pm->id }}" {{ request('proceso_id') == $pm->id ? 'selected' : '' }}>{{ $pm->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="usuario_id" class="form-select">
                            <option value="">Todos los Responsables</option>
                            @foreach($usuarios as $u)
                                <option value="{{ $u->id }}" {{ request('usuario_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-dark">Filtrar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4">Radicado</th>
                            <th>Proceso/Etapa</th>
                            <th>Estado</th>
                            <th>Responsable</th>
                            <th class="text-center">Doc</th>
                            <th class="text-center">Email</th>
                            <th>Fecha Gestión</th>
                            <th class="text-end px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($procesos as $registro)
                        <tr>
                            <td class="px-4">
                                <span class="fw-bold text-dark">#{{ $registro->id_correspondencia }}</span>
                            </td>
                            <td>
                                <div class="small fw-bold">{{ $registro->proceso->nombre ?? 'General' }}</div>
                                <div class="text-muted small">{{ $registro->proceso->flujo->nombre ?? '' }}</div>
                            </td>
                            <td>
                                @php
                                    $color = match($registro->estado) {
                                        'finalizado' => 'success',
                                        'en_tramite' => 'primary',
                                        'devuelto'   => 'danger',
                                        default      => 'info',
                                    };
                                @endphp
                                <span class="badge bg-soft-{{ $color }} text-{{ $color }} border border-{{ $color }} px-3">
                                    {{ ucfirst(str_replace('_', ' ', $registro->estado)) }}
                                </span>
                            </td>
                            <td class="small">{{ $registro->usuario->name }}</td>
                            <td class="text-center">
                                @if($registro->documento_arc)
                                    <a href="{{ Storage::disk('s3')->url($registro->documento_arc) }}" target="_blank" class="text-primary">
                                        <i class="fas fa-file-pdf fs-4"></i>
                                    </a>
                                @else
                                    <i class="fas fa-minus text-muted"></i>
                                @endif
                            </td>
                            <td class="text-center">
                                <i class="fas {{ $registro->notificado_email ? 'fa-envelope-open-text text-success' : 'fa-envelope text-muted opacity-50' }}"></i>
                            </td>
                            <td class="small">{{ $registro->fecha_gestion ? $registro->fecha_gestion->format('d/m/Y H:i') : 'N/A' }}</td>
                            <td class="text-end px-4">
                                <div class="btn-group">
                                    <a href="{{ route('correspondencia.correspondencias-procesos.show', $registro) }}" class="btn btn-sm btn-light border" title="Ver Detalle"><i class="fas fa-search"></i></a>
                                    <a href="{{ route('correspondencia.correspondencias-procesos.edit', $registro) }}" class="btn btn-sm btn-light border" title="Editar"><i class="fas fa-edit text-warning"></i></a>
                                    <form action="{{ route('correspondencia.correspondencias-procesos.destroy', $registro) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este registro?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No se encontraron registros de gestión.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0">
                {{ $procesos->links() }}
            </div>
        </div>
    </div>
</x-base-layout>