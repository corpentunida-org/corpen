<x-base-layout>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <h4 class="fw-bold text-dark mb-0">
                    Soportes Sin Asignar 
                    <span class="badge bg-warning fs-6 ms-2">
                        {{ $soportesSinAsignar->count() }}
                    </span>
                </h4>
                <div id="exportButtons"></div>
            </div>

            <!-- Aquí podrías reusar la misma card de filtros que hicimos para Pendientes -->

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle small soporteTable" style="font-size: 0.875rem; width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Fecha Creado</th>
                            <th>Creado Por</th>
                            <th>Área</th>
                            <th>Categoria</th>
                            <th>Tipo</th>
                            <th>SubTipo</th>
                            <th>Prioridad</th>
                            <th>Descripción</th>
                            <th>Asignado</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($soportesSinAsignar as $soporte)
                            <tr>
                                <td>{{ $soporte->id }}</td>
                                <td>{{ $soporte->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $soporte->usuario->name ?? 'N/A' }}</td>
                                <td>{{ $soporte->cargo->gdoArea->nombre ?? 'Soporte' }}</td>
                                <td>{{ $soporte->categoria->nombre ?? 'N/A' }}</td>
                                <td>{{ $soporte->tipo->nombre ?? 'N/A' }}</td>
                                <td>{{ $soporte->subTipo->nombre ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $soporte->prioridad?->nombre === 'Alta' ? 'danger' : ($soporte->prioridad?->nombre === 'Media' ? 'warning' : 'success') }}">
                                        {{ $soporte->prioridad->nombre ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($soporte->detalles_soporte, 50) }}</td>
                                <td>{{ $soporte->scpUsuarioAsignado->maeTercero->nom_ter ?? 'Sin escalar' }}</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $soporte->estadoSoporte->nombre ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('soportes.soportes.show', $soporte) }}" class="btn btn-sm btn-info">Ver</a>
                                    <a href="{{ route('soportes.soportes.edit', $soporte) }}" class="btn btn-sm btn-primary">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center">No hay soportes sin asignar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-base-layout>
