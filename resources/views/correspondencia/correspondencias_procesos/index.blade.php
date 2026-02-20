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

        {{-- Filtros --}}
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
                        <select name="proceso_id" class="form-select shadow-none">
                            <option value="">Todas las Etapas</option>
                            @foreach($procesos_maestros as $pm)
                                <option value="{{ $pm->id }}" {{ request('proceso_id') == $pm->id ? 'selected' : '' }}>{{ $pm->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="usuario_id" class="form-select shadow-none">
                            <option value="">Todos los Responsables</option>
                            @foreach($usuarios as $u)
                                <option value="{{ $u->id }}" {{ request('usuario_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-dark rounded-3">Filtrar</button>
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
                            <th class="text-center">Terminado</th>
                            <th>Responsable</th>
                            <th class="text-center">Doc(s)</th>
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
                            <td class="text-center">
                                @if($registro->finalizado)
                                    <span class="badge rounded-pill bg-success px-2" title="Proceso Finalizado">
                                        <i class="fas fa-check-double text-white p-1"></i>
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-light border text-muted px-2" title="Pendiente">
                                        <i class="fas fa-clock p-1"></i>
                                    </span>
                                @endif
                            </td>
                            <td class="small">{{ $registro->usuario->name }}</td>
                            
                            {{-- ========================================== --}}
                            {{-- CORRECCIÓN APLICADA AQUÍ (AWS S3)          --}}
                            {{-- ========================================== --}}
                            <td class="text-center">
                                @php 
                                    // Usamos la misma función que funciona en la vista show.blade.php
                                    $urlsArchivos = $registro->getArchivosUrls(); 
                                @endphp

                                @if(count($urlsArchivos) > 0)
                                    <div class="d-flex justify-content-center gap-1">
                                        @foreach($urlsArchivos as $archivo)
                                            <a href="{{ $archivo['url'] }}" target="_blank" class="text-danger hover-elevate" title="{{ $archivo['nombre'] ?? 'Ver Documento' }}">
                                                <i class="fas fa-file-pdf fs-5"></i>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <i class="fas fa-minus text-muted opacity-50"></i>
                                @endif
                            </td>
                            {{-- ========================================== --}}

                            <td class="text-center">
                                <i class="fas {{ $registro->notificado_email ? 'fa-envelope-open-text text-success' : 'fa-envelope text-muted opacity-50' }}"></i>
                            </td>
                            <td class="small">{{ $registro->fecha_gestion ? $registro->fecha_gestion->format('d/m/Y H:i') : 'N/A' }}</td>
                            <td class="text-end px-4">
                                <div class="btn-group shadow-sm">
                                    <a href="{{ route('correspondencia.correspondencias-procesos.show', $registro) }}" class="btn btn-sm btn-white border" title="Ver Detalle"><i class="fas fa-eye text-primary"></i></a>
                                    <a href="{{ route('correspondencia.correspondencias-procesos.edit', $registro) }}" class="btn btn-sm btn-white border" title="Editar"><i class="fas fa-pen text-warning"></i></a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" style="width: 60px; opacity: .3" class="mb-3">
                                <p class="text-muted small">No se encontraron registros de gestión.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                {{ $procesos->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <style>
        .bg-soft-success { background-color: #ecfdf5; }
        .bg-soft-primary { background-color: #eff6ff; }
        .bg-soft-danger { background-color: #fef2f2; }
        .bg-soft-info { background-color: #f0f9ff; }
        .hover-elevate:hover { transform: translateY(-2px); transition: 0.2s; }
        .btn-white { background: white; border: 1px solid #dee2e6 !important; }
    </style>
</x-base-layout>