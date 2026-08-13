<x-base-layout>
    @section('titlepage', 'Indicadores')

    <x-success />
    <x-error />

    <!-- TARJETA DE RESUMEN (HEADER) -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm stretch stretch-full short-info-card rounded-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">

                    <!-- Info Principal -->
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg bg-soft-primary text-primary rounded-3 shadow-sm icon">
                            <i class="bi bi-file-earmark-bar-graph fs-3"></i>
                        </div>
                        <div>
                            <h2 class="fs-4 fw-bold text-dark mb-1">Panel de Indicadores</h2>
                            @if($lastReport)
                                <a href="{{ $lastReport->getFile($lastReport->archivo) }}" target="_blank"
                                   class="text-muted fs-13 text-decoration-none hover-primary transition-all">
                                    <i class="bi bi-clock-history me-1"></i> Último informe:
                                    <span class="fw-semibold text-dark">{{ $lastReport->fecha_descarga }}</span>
                                </a>
                            @else
                                <span class="text-muted fs-13"><i class="bi bi-info-circle me-1"></i> Sin informes previos</span>
                            @endif
                        </div>
                    </div>

                    <!-- Acción -->
                    <form method="POST" action="{{ route('indicators.indicadores.descargar') }}" target="_blank">
                        @csrf
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-medium shadow-sm rounded-pill d-flex align-items-center gap-2 transition-all">
                            <i class="bi bi-cloud-arrow-down-fill fs-5"></i>
                            <span>Descargar Informe</span>
                        </button>
                    </form>
                </div>

                <!-- Progreso Dinámico -->
                <div class="bg-light p-3 rounded-3 mt-2">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fs-13 fw-semibold text-muted text-uppercase tracking-wide">Promedio de Indicadores Alcanzados</span>

                        @php
                            // Lógica de color según el porcentaje
                            $colorClass = $promedioAlcanzados >= 80 ? 'bg-success' : ($promedioAlcanzados >= 50 ? 'bg-warning' : 'bg-danger');
                            $textColor = $promedioAlcanzados >= 80 ? 'text-success' : ($promedioAlcanzados >= 50 ? 'text-warning' : 'text-danger');
                        @endphp

                        <div class="text-end">
                            <span class="fs-5 fw-bold {{ $textColor }}">{{ number_format($promedioAlcanzados, 0) }}%</span>
                        </div>
                    </div>
                    <div class="progress ht-5 rounded-pill bg-gray-200">
                        <div class="progress-bar {{ $colorClass }} rounded-pill transition-all"
                             role="progressbar"
                             style="width: {{ $promedioAlcanzados }}%"
                             aria-valuenow="{{ $promedioAlcanzados }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LISTADO DE ÁREAS E INDICADORES -->
    @forelse ($indicators as $area => $indicadoresArea)
        <div class="card border-0 shadow-sm stretch stretch-full function-table mb-4 rounded-4">
            <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2 px-4 d-flex align-items-center gap-2">
                <i class="bi bi-diagram-3-fill text-muted fs-5"></i>
                <h4 class="mb-0 fw-bold text-dark fs-5">{{ $area }}</h4>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive px-4 pb-4">
                    <table class="table table-hover table-borderless align-middle mb-0">
                        <thead class="table-light text-muted fs-12 text-uppercase fw-semibold">
                            <tr>
                                <th class="ps-3 py-3 rounded-start">ID</th>
                                <th class="py-3">Nombre</th>
                                <th class="py-3" style="max-width: 250px;">Cálculo</th>
                                <th class="py-3">Meta</th>
                                <th class="py-3">Frecuencia</th>
                                <th class="py-3">Indicador</th>
                                <th class="text-end pe-3 py-3 rounded-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse ($indicadoresArea as $ind)
                                <tr class="border-bottom">
                                    <td class="ps-3 text-muted fw-medium">#{{ $ind->id }}</td>
                                    <td class="fw-semibold text-dark">{{ $ind->nombre }}</td>
                                    <td class="text-muted fs-13 text-truncate" style="max-width: 250px;" data-bs-toggle="tooltip" title="{{ $ind->calculo }}">
                                        {{ $ind->calculo }}
                                    </td>
                                    <td class="fw-bold text-dark">{{ $ind->meta }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match($ind->frecuencia) {
                                                'Trimestral' => 'bg-soft-warning text-warning',
                                                'Semestral'  => 'bg-soft-info text-info',
                                                'Mensual'    => 'bg-soft-primary text-primary',
                                                default      => 'bg-soft-success text-success',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }} rounded-pill px-3 py-2 fw-medium">
                                            {{ $ind->frecuencia }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($ind->indicador_calculado !== null)
                                            <div class="d-inline-block fw-bold {{ $ind->indicador_calculado >= $ind->meta ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($ind->indicador_calculado, 1) }}%
                                                <i class="bi {{ $ind->indicador_calculado >= $ind->meta ? 'bi-arrow-up-short' : 'bi-arrow-down-short' }}"></i>
                                            </div>
                                        @else
                                            <span class="text-muted fst-italic fs-12">N/D</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <!-- Botón Ver Detalles -->
                                            <a href="{{ route('indicators.indicadores.show', $ind->id) }}"
                                               class="btn btn-sm btn-light text-primary avatar-text avatar-md rounded-circle"
                                               data-bs-toggle="tooltip" title="Ver Detalle">
                                                <i class="feather-eye"></i>
                                            </a>
                                            <!-- Botón Editar -->
                                            <a href="{{ route('indicators.indicadores.edit', $ind->id) }}"
                                               class="btn btn-sm btn-light text-secondary avatar-text avatar-md rounded-circle"
                                               data-bs-toggle="tooltip" title="Editar Meta/Indicador">
                                                <i class="feather-edit-2"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        No hay indicadores registrados en esta área.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <!-- Empty state Global -->
        <div class="text-center py-5">
            <div class="avatar-text avatar-xl bg-soft-secondary text-secondary rounded-circle mb-3 mx-auto">
                <i class="bi bi-folder-x fs-1"></i>
            </div>
            <h4 class="fw-bold text-dark">No hay áreas configuradas</h4>
            <p class="text-muted">Actualmente no existen indicadores para mostrar en el panel.</p>
        </div>
    @endforelse

    <!-- MODAL DE ACTUALIZACIÓN -->
    <div class="modal fade" id="modalCalculo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold">Actualizar Indicador</h5>
                        <button type="button" class="btn-close bg-light rounded-circle p-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body px-4 pt-3 pb-4">
                        <input type="hidden" name="id" id="ind_id">

                        <div class="mb-4">
                            <label class="form-label text-muted fw-medium fs-13">Nombre del Indicador</label>
                            <input type="text" class="form-control bg-light border-0" id="ind_nombre" disabled readonly>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-medium text-dark">Meta Objetivo</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" name="meta" id="ind_meta" placeholder="Ej: 80.5">
                                <span class="input-group-text bg-light text-muted">%</span>
                            </div>
                            <div class="form-text mt-2">Define el valor objetivo que se espera alcanzar para este indicador.</div>
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-base-layout>
