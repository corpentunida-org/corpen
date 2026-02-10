<x-base-layout>
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="app-container">
        <header class="main-header">
            <div class="header-content">
                <h1 class="page-title">Gestión de Correspondencia</h1>
                <p class="page-subtitle">Listado maestro de todos los radicados del sistema.</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('correspondencia.correspondencias.create') }}" class="btn-primary-neo">
                    <i class="bi bi-plus-circle-fill"></i> Nuevo Registro
                </a>
            </div>
        </header>

        <div class="toolbar-panel mb-4">
            <form action="{{ route('correspondencia.correspondencias.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="search-group">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por radicado, asunto o remitente...">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="estado" class="form-select" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        {{-- Iterar estados aquí --}}
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">Filtrar</button>
                </div>
            </form>
        </div>

        <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Radicado</th>
                            <th>Asunto</th>
                            <th>Remitente</th>
                            <th>Flujo</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($correspondencias as $corr)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-primary">#{{ $corr->id_radicado }}</div>
                                <small class="text-muted">{{ $corr->fecha_solicitud->format('d/m/Y') }}</small>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 250px;" title="{{ $corr->asunto }}">
                                    {{ $corr->asunto }}
                                </div>
                                @if($corr->es_confidencial)
                                    <span class="badge bg-soft-danger text-danger" style="font-size: 10px;">CONFIDENCIAL</span>
                                @endif
                            </td>
                            <td>{{ $corr->remitente->nom_ter ?? 'N/A' }}</td>
                            <td>
                                <div class="d-flex align-items-center">                                    
                                    <span>{{ $corr->flujo->nombre ?? 'Sin asignar' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge-status {{ Str::slug($corr->estado->nombre ?? 'default') }}">
                                    {{ $corr->estado->nombre ?? 'S/E' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('correspondencia.correspondencias.show', $corr) }}" class="btn btn-sm btn-light border" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('correspondencia.correspondencias.edit', $corr) }}" class="btn btn-sm btn-light border" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                {{ $correspondencias->links() }}
            </div>
        </div>
    </div>
</x-base-layout>