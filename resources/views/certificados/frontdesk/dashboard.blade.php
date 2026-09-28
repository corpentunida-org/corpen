<x-base-layout>
    <style>
        /* --- ESTILOS MINIMALISTAS --- */
        body { background-color: #f8f9fa; }
        .bg-soft-primary { background-color: #f0f4ff; color: #3b82f6; }
        .bg-soft-success { background-color: #f0fdf4; color: #16a34a; }
        .bg-soft-warning { background-color: #fefce8; color: #ca8a04; }
        .bg-soft-secondary { background-color: #f1f5f9; color: #64748b; }

        .card-minimal {
            background: #ffffff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        .table-minimal th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 1rem;
        }

        .table-minimal td {
            vertical-align: middle;
            border-bottom: 1px solid #f8fafc;
            padding: 1rem 0;
            color: #334155;
        }

        .table-hover tbody tr:hover td { background-color: #fcfdfe; }

        /* Botones y Badges */
        .badge-status { padding: 0.4rem 0.8rem; font-weight: 600; font-size: 0.75rem; border-radius: 6px; }
        .btn-action { border-radius: 8px; padding: 0.4rem 1rem; font-size: 0.85rem; font-weight: 500; transition: all 0.2s; }
        .btn-action:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }

        /* Modal Minimalista */
        .modal-content-minimal { border: none; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .modal-header-minimal { border-bottom: 1px solid #f1f5f9; padding: 1.5rem; }
        .modal-footer-minimal { border-top: 1px solid #f1f5f9; padding: 1.2rem; }
    </style>

    <div class="container py-5 max-w-7xl">

        {{-- HEADER MINIMALISTA --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-soft-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fas fa-user-circle fs-4"></i>
                </div>
                <div>
                    <h2 class="h5 fw-bold text-dark mb-1">Hola, {{ $tercero->nom_ter }} {{ $tercero->apl1 ?? '' }}</h2>
                    <p class="text-muted small mb-0">NIT: {{ $tercero->cod_ter }} &bull; {{ $tercero->ciudad ?? 'Ciudad no registrada' }}</p>
                </div>
            </div>
            <form action="{{ route('certificados.frontdesk.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-light text-danger fw-medium rounded-pill px-3 shadow-sm border-0">
                    Cerrar sesión <i class="fas fa-sign-out-alt ms-1"></i>
                </button>
            </form>
        </div>

        {{-- BARRA DE FILTROS POR MES Y AÑO --}}
        <div class="card card-minimal mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('certificados.frontdesk.dashboard') }}" class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <label for="mes" class="form-label small text-muted fw-bold mb-1">Filtrar por Mes</label>
                        <select name="mes" id="mes" class="form-select form-select-sm rounded-pill">
                            <option value="">Todos los meses</option>
                            @foreach([
                                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                            ] as $numMes => $nombreMes)
                                <option value="{{ $numMes }}" {{ $mesSeleccionado == $numMes ? 'selected' : '' }}>
                                    {{ $nombreMes }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="anio" class="form-label small text-muted fw-bold mb-1">Filtrar por Año</label>
                        <select name="anio" id="anio" class="form-select form-select-sm rounded-pill">
                            @foreach($aniosDisponibles as $anio)
                                <option value="{{ $anio }}" {{ $anioSeleccionado == $anio ? 'selected' : '' }}>
                                    {{ $anio }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end gap-2 pt-2 pt-md-0">
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 w-100 shadow-sm">
                            <i class="fas fa-filter me-1"></i> Aplicar Filtros
                        </button>
                        <a href="{{ route('certificados.frontdesk.dashboard') }}" class="btn btn-sm btn-light rounded-pill px-3 text-secondary w-100">
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- TARJETA DE OPERACIONES --}}
        <div class="card card-minimal mb-4">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h3 class="h6 fw-bold text-slate-800 mb-0">Historial de Certificados</h3>
                    <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill">
                        Total registros: {{ $operaciones->count() }}
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-minimal mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">Radicado / Lote</th>
                                <th>Fecha Solicitud</th>
                                <th>Estado</th>
                                <th>Obligaciones</th>
                                <th class="text-end pe-3">Documentos Disponibles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($operaciones as $operacion)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold text-dark"># {{ $operacion->numero_radicado }}</div>
                                        <div class="text-muted small">Lote API-{{ str_pad($operacion->numero_bloque ?? 0, 4, '0', STR_PAD_LEFT) }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-dark">{{ $operacion->created_at->format('d/m/Y') }}</div>
                                        <div class="text-muted small">{{ $operacion->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $ultimoEstado = $operacion->estados->sortByDesc('created_at')->first();
                                            $estadoStr = $ultimoEstado && $ultimoEstado->estado ? $ultimoEstado->estado->nombre : 'En Trámite';
                                            $claseEstado = match($estadoStr) {
                                                'Aprobado', 'Completado' => 'bg-soft-success',
                                                'En Trámite', 'Pendiente' => 'bg-soft-warning',
                                                default => 'bg-soft-secondary'
                                            };
                                        @endphp
                                        <span class="badge-status {{ $claseEstado }}">
                                            {{ strtoupper($estadoStr) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{-- BOTÓN PARA ABRIR EL DETALLE DE FACTURAS --}}
                                        <button type="button" class="btn btn-sm btn-light rounded-pill fw-medium text-primary border-0 shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#modalDetalle-{{ $operacion->id }}">
                                            <i class="fas fa-search me-1"></i> {{ $operacion->lineas->count() }} facturas
                                        </button>
                                    </td>
                                    <td class="text-end pe-3">
                                        {{-- SECCIÓN DE DESCARGA DIRECTA DINÁMICA (SE VEN DE UNA) --}}
                                        <div class="d-flex flex-wrap justify-content-end gap-1.5">
                                            @forelse($operacion->historialTiposDisponibles as $regTipo)
                                                @if($regTipo->tipo)
                                                    <a href="{{ route('certificados.operaciones.pdf_individual', ['id' => $operacion->id, 'tipo_id' => $regTipo->tipo->id, 'hash' => $regTipo->hashActual]) }}" target="_blank" class="btn btn-sm btn-light border text-primary fw-medium rounded-pill px-2.5 py-1 shadow-sm d-inline-flex align-items-center" style="font-size: 0.75rem;" title="Descargar {{ $regTipo->tipo->nombre }}">
                                                        <i class="fas fa-file-pdf me-1 text-danger"></i> {{ $regTipo->tipo->nombre }}
                                                    </a>
                                                @endif
                                            @empty
                                                <span class="text-muted small fst-italic">Sin documentos</span>
                                            @endforelse
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="opacity-50 mb-3">
                                            <i class="fas fa-folder-open fa-3x text-muted"></i>
                                        </div>
                                        <h5 class="fw-medium text-dark">No hay certificados generados</h5>
                                        <p class="text-muted small">Sus operaciones procesadas en este periodo aparecerán aquí.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ZONA DE MODALES (Uno por cada operación para mostrar las líneas/facturas) --}}
    @foreach($operaciones as $operacion)
        <div class="modal fade" id="modalDetalle-{{ $operacion->id }}" tabindex="-1" aria-labelledby="modalLabel-{{ $operacion->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content modal-content-minimal">
                    <div class="modal-header modal-header-minimal bg-light">
                        <div>
                            <h5 class="modal-title fw-bold text-dark" id="modalLabel-{{ $operacion->id }}">Detalle de Obligaciones</h5>
                            <p class="text-muted small mb-0">Radicado #{{ $operacion->numero_radicado }}</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">

                        @if($operacion->lineas->isEmpty())
                            <div class="text-center py-4 text-muted">
                                No se encontraron facturas registradas en este bloque.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless align-middle">
                                    <thead class="border-bottom">
                                        <tr class="text-muted small text-uppercase">
                                            <th class="pb-2">Línea / Cartera</th>
                                            <th class="pb-2 text-center">Días Mora</th>
                                            <th class="pb-2 text-center">Calificación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($operacion->lineas as $linea)
                                            <tr>
                                                <td class="py-3">
                                                    <div class="fw-bold text-dark">{{ $linea->lineaSia->nombre ?? 'Línea Desconocida' }}</div>
                                                    <div class="text-muted small" style="font-size: 0.75rem;">Ref interna: {{ $linea->id }}</div>
                                                </td>
                                                <td class="py-3 text-center">
                                                    @if(($linea->dias_mora_automaticos ?? 0) <= 0)
                                                        <span class="badge bg-soft-success text-success border-0">Al día</span>
                                                    @else
                                                        <span class="badge bg-soft-warning text-danger border-0">{{ $linea->dias_mora_automaticos }} días</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 text-center">
                                                    <span class="fw-medium text-secondary">{{ strtoupper($linea->calificacion ?? 'N/A') }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                    </div>
                    <div class="modal-footer modal-footer-minimal border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

</x-base-layout>
