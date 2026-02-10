<x-base-layout>
    <div class="app-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title h3 fw-bold m-0">Trazabilidad de Correspondencia</h1>
                <p class="text-muted">Historial de gestiones y cambios de estado por radicado.</p>
            </div>
            <a href="{{ route('correspondencia.correspondencias-procesos.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4">
                <i class="fas fa-history me-2"></i> Registrar Gestión
            </a>
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
                            <th class="text-center">Email</th>
                            <th>Fecha Gestión</th>
                            <th class="text-end px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($procesos as $registro)
                        <tr>
                            <td class="px-4">
                                <span class="fw-bold text-dark">#{{ $registro->correspondencia->nro_radicado ?? $registro->id_correspondencia }}</span>
                            </td>
                            <td>
                                <div class="small fw-bold">{{ $registro->proceso->flujo->nombre ?? 'Proceso '.$registro->id_proceso }}</div>
                            </td>
                            <td>
                                <span class="badge bg-soft-info text-info border border-info px-3">
                                    {{ ucfirst($registro->estado) }}
                                </span>
                            </td>
                            <td class="small">{{ $registro->usuario->name }}</td>
                            <td class="text-center">
                                @if($registro->notificado_email)
                                    <i class="fas fa-envelope-open-text text-success" title="Notificado"></i>
                                @else
                                    <i class="fas fa-envelope text-muted opacity-50" title="Sin notificar"></i>
                                @endif
                            </td>
                            <td class="small">{{ $registro->fecha_gestion ? $registro->fecha_gestion->format('d/m/Y H:i') : $registro->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-end px-4">
                                <div class="btn-group">
                                    <a href="{{ route('correspondencia.correspondencias-procesos.show', $registro) }}" class="btn btn-sm btn-light border"><i class="fas fa-search"></i></a>
                                    <a href="{{ route('correspondencia.correspondencias-procesos.edit', $registro) }}" class="btn btn-sm btn-light border"><i class="fas fa-edit"></i></a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                {{ $procesos->links() }}
            </div>
        </div>
    </div>
</x-base-layout>