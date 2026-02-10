<x-base-layout>
    <style>
        :root {
            --bg-corporate: #f8fafc;
            --pastel-blue: #eef2ff;
            --text-dark: #334155;
            --border-color: #f1f5f9;
        }
        .app-container { padding: 1.5rem; background-color: var(--bg-corporate); }
        
        /* Tabla Ultra-Compacta */
        .table-compact thead th {
            padding: 8px 12px;
            font-size: 0.65rem;
            text-transform: uppercase;
            background: #fff;
            border-bottom: 2px solid var(--border-color);
            color: #64748b;
        }
        .table-compact tbody td { 
            padding: 6px 12px; /* Reducción de altura */
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        /* Badges Minimal */
        .b-pill {
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .b-status { background: var(--pastel-blue); color: #4338ca; }
        .b-confidencial { background: #fff1f2; color: #be123c; font-size: 0.6rem; }

        /* Botón Show Estilizado y Angosto */
        .btn-action-show {
            padding: 4px 10px;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 6px;
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #475569;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-action-show:hover { background: #f1f5f9; color: #000; border-color: #cbd5e1; }

        .search-mini {
            max-width: 400px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 5px 12px;
            background: #fff;
        }
    </style>

    <div class="app-container">
        {{-- Header Compacto --}}
        <header class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="fw-bold m-0" style="font-size: 1.25rem; color: var(--text-dark);">Correspondencia</h2>
            <a href="{{ route('correspondencia.correspondencias.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                <i class="bi bi-plus-lg"></i> Nuevo
            </a>
        </header>

        {{-- Toolbar en una sola línea --}}
        <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-white rounded-3 shadow-sm border">
            <form action="{{ route('correspondencia.correspondencias.index') }}" method="GET" class="d-flex gap-2 w-100">
                <div class="search-mini d-flex align-items-center flex-grow-1">
                    <i class="bi bi-search text-muted me-2"></i>
                    <input type="text" name="search" class="border-0 w-100" style="outline:none; font-size: 0.85rem;" placeholder="Buscar...">
                </div>
                <select name="estado" class="form-select form-select-sm w-auto border-0 bg-light" onchange="this.form.submit()">
                    <option value="">Todos los estados</option>
                    @foreach($estados as $est)
                        <option value="{{ $est->id_estado }}" {{ request('estado') == $est->id_estado ? 'selected' : '' }}>
                            {{ $est->nombre }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-dark btn-sm px-3">Filtrar</button>
            </form>
        </div>

        {{-- Tabla Maestra --}}
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover table-compact mb-0">
                    <thead>
                        <tr>
                            <th>Radicado</th>
                            <th>Asunto</th>
                            <th>Remitente</th>
                            <th>Flujo</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach($correspondencias as $corr)
                        <tr>
                            {{-- Fusión ID + Fecha --}}
                            <td>
                                <span class="fw-bold d-block" style="font-size: 0.85rem; color: #1e293b;">#{{ $corr->id_radicado }}</span>
                                <small class="text-muted" style="font-size: 0.7rem;">{{ $corr->fecha_solicitud->format('d/m/y') }}</small>
                            </td>

                            {{-- Asunto + Badge Confidencial --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-truncate d-inline-block" style="max-width: 200px; font-size: 0.85rem;">{{ $corr->asunto }}</span>
                                    @if($corr->es_confidencial)
                                        <span class="b-pill b-confidencial text-uppercase">Privado</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Remitente con Icono --}}
                            <td>
                                <span class="text-muted" style="font-size: 0.8rem;">
                                    <i class="bi bi-person me-1"></i>{{ Str::limit($corr->remitente->nom_ter ?? 'N/A', 20) }}
                                </span>
                            </td>

                            {{-- Flujo --}}
                            <td>
                                <span class="badge bg-light text-dark fw-normal border" style="font-size: 0.7rem;">
                                    {{ $corr->flujo->nombre ?? 'S/A' }}
                                </span>
                            </td>

                            {{-- Estado --}}
                            <td class="text-center">
                                <span class="b-pill b-status">
                                    {{ $corr->estado->nombre ?? 'S/E' }}
                                </span>
                            </td>

                            {{-- Botón Show Único y Minimalista --}}
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('correspondencia.correspondencias.show', $corr) }}" class="btn-action-show">
                                        Revisar <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                    <a href="{{ route('correspondencia.correspondencias.edit', $corr) }}" class="text-muted p-1 hover-primary">
                                        <i class="bi bi-pencil" style="font-size: 0.85rem;"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="p-2 px-3 border-top bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">{{ $correspondencias->count() }} registros</small>
                    <div class="pagination-sm">{{ $correspondencias->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>