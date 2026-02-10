<x-base-layout>
    <div class="app-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title h3 fw-bold m-0">Tablas de Retención Documental (TRD)</h1>
                <p class="text-muted">Gestión de series documentales, tiempos de retención y flujos automáticos.</p>
            </div>
            <a href="{{ route('correspondencia.trds.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4">
                <i class="fas fa-plus me-2"></i> Nueva TRD
            </a>
        </div>

        {{-- Alertas de éxito o error --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4">Serie Documental</th>
                            <th>Flujo de Trabajo</th>
                            <th class="text-center">T. Gestión</th>
                            <th class="text-center">T. Central</th>
                            <th>Disposición</th>
                            <th>Responsable</th>
                            <th class="text-end px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trds as $trd)
                        <tr>
                            <td class="px-4">
                                <div class="fw-bold text-dark">{{ $trd->serie_documental }}</div>
                                <small class="text-muted">ID: TRD-{{ str_pad($trd->id_trd, 3, '0', STR_PAD_LEFT) }}</small>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-soft-primary text-primary border border-primary border-opacity-25 px-3">
                                    <i class="fas fa-route me-1"></i> {{ $trd->flujo->nombre ?? 'No asignado' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-soft-info text-info px-3">{{ $trd->tiempo_gestion }} años</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-soft-warning text-warning px-3">{{ $trd->tiempo_central }} años</span>
                            </td>
                            <td>
                                @if($trd->disposicion_final == 'conservar')
                                    <span class="text-success small fw-bold"><i class="fas fa-leaf me-1"></i> Conservar</span>
                                @else
                                    <span class="text-danger small fw-bold"><i class="fas fa-eraser me-1"></i> Eliminar</span>
                                @endif
                            </td>
                            <td class="small">
                                <i class="fas fa-user-circle text-muted me-1"></i> {{ $trd->usuario->name }}
                            </td>

                            <td class="text-end px-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('correspondencia.trds.show', $trd) }}" class="btn btn-sm btn-light border" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('correspondencia.trds.edit', $trd) }}" class="btn btn-sm btn-light border text-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
    
                                    <!-- <form action="{{ route('correspondencia.trds.destroy', $trd) }}" method="POST" 
                                          onsubmit="return confirm('¿Está seguro de eliminar esta serie? Esta acción no se puede deshacer.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form> -->
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 d-block opacity-25"></i>
                                No hay series documentales registradas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($trds->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $trds->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        /* Estilos para Badges suaves (Soft Badges) */
        .bg-soft-info { background-color: #e0f7fa; color: #00acc1; }
        .bg-soft-warning { background-color: #fff8e1; color: #ffb300; }
        .bg-soft-primary { background-color: #e8f0fe; color: #1a73e8; }
        
        .table thead th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            color: #6c757d;
        }
    </style>
</x-base-layout>