<x-base-layout>
    @section('titlepage', 'Indicadores')
    <div class="col-xxl-12 col-md-12">
        <div class="card stretch stretch-full short-info-card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-4">
                    <div class="d-flex gap-4 align-items-center">
                        <div class="avatar-text avatar-lg bg-gray-200 icon"><i class="bi bi-file-earmark-bar-graph"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold text-dark">Indicadores</div>
                            <a href="{{ $lastReport->getFile($lastReport->archivo) }}" target="_blank"
                                class="fs-13 fw-medium text-muted text-truncate-1-line">Ultimo informe guardado <span
                                    class="fw-semibold">{{ $lastReport->fecha_descarga }}</span></a>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('indicators.indicadores.descargar') }}" target="_blank"> @csrf
                        <button type="submit" class="btn btn-primary"><i class="bi bi-cloud-arrow-down-fill me-2"></i>
                            Descargar Informe</button></form>
                </div>
                <div class="">
                    {{-- <div class="d-flex align-items-center justify-content-between"><a class="fs-12 fw-medium text-muted text-truncate-1-line" href="#">Promedio de Indicadores Alcanzados</a>
                        <div class="w-100 text-end"><span class="fs-12 text-dark">56%</span></div>
                    </div>
                    <div class="progress mt-2 ht-3">
                        <div class="progress-bar progress-1" role="progressbar" style="width:56%"></div>
                    </div> --}}
                    <div class="d-flex align-items-center justify-content-between">
                        <a class="fs-12 fw-medium text-muted text-truncate-1-line" href="#">Promedio de Indicadores Alcanzados</a>
                        <div class="w-100 text-end">
                            <span class="fs-12 text-dark">{{ number_format($promedioAlcanzados, 0) }}%</span>
                        </div>
                    </div>

                    <div class="progress mt-2 ht-3">
                        <div class="progress-bar" role="progressbar" style="width: {{ $promedioAlcanzados }}%"
                            aria-valuenow="{{ $promedioAlcanzados }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card stretch stretch-full function-table">
            <div class="card-body p-0">
                <div class="table-responsive p-4">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Cálculo</th>
                                <th>Meta</th>
                                <th>Frecuencia</th>
                                <th>Indicador</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($indicators as $ind)
                                <tr>
                                    <td>{{ $ind->id }}</td>
                                    <td>{{ $ind->nombre }}</td>
                                    <td>{{ $ind->calculo }}</td>
                                    <td class="fw-bold text-dark">{{ $ind->meta }}</td>
                                    <td>
                                        @if ($ind->frecuencia == 'Trimestral')
                                            <div class="badge bg-soft-warning text-warning">{{ $ind->frecuencia }}
                                            </div>
                                        @elseif($ind->frecuencia == 'Semestral')
                                            <div class="badge bg-soft-info text-info">{{ $ind->frecuencia }}</div>
                                        @elseif($ind->frecuencia == 'Mensual')
                                            <div class="badge bg-soft-primary text-primary">{{ $ind->frecuencia }}
                                            </div>
                                        @else
                                            <div class="badge bg-soft-success text-success">{{ $ind->frecuencia }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $ind->indicador_calculado !== null ? number_format($ind->indicador_calculado, 1) . ' %' : '' }}
                                    </td>
                                    <td class="text-end">
                                        <div class="hstack gap-2 justify-content-end">
                                            <div class="dropdown">
                                                <a class="avatar-text avatar-md ms-auto" data-bs-toggle="dropdown">
                                                    <i class="feather-more-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="#" data-bs-toggle="modal"
                                                        data-bs-target="#modalUpdateCalculo"
                                                        data-id="{{ $ind->id }}"
                                                        data-nombre="{{ $ind->nombre }}"
                                                        data-meta="{{ $ind->meta }}" class="dropdown-item">Agregar
                                                        Cálculo Indicador</a>
                                                </div>
                                            </div>
                                            <a href="#" class="avatar-text avatar-md" data-bs-toggle="tooltip"
                                                title="" data-bs-original-title="Ver detalle">
                                                <i class="feather-arrow-right"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCalculo" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Actualizar Indicador</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <input type="hidden" name="id" id="ind_id">

                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="ind_nombre" disabled>
                        </div>


                        <div class="mb-3">
                            <label class="form-label">Meta</label>
                            <input type="text" class="form-control" name="meta" id="ind_meta">
                        </div>


                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</x-base-layout>
