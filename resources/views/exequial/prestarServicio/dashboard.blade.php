<x-base-layout>
    @section('titlepage', 'Dashboard reclamaciones')
    {{-- <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body lead-status">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Generar Informe:</span>
                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                            Exportar todo el listado de reclamaciones en formato PDF o Excel, según las opciones seleccionadas.</span>
                    </h5>
                </div>
                <form class="row" method="post">
                    @csrf
                    <div class="col-xxl-3 col-md-6">
                        <div class="form-check">
                            <label class="form-label">Año<span class="text-danger">*</span></label>
                            <select class="form-select" name="anio">
                                @for ($i = date('Y'); $i >= 2024; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-md-6">
                        <div class="form-check">
                            <label class="form-label">Lista Datos<span class="text-danger">*</span></label>
                            <select class="form-select" name="opcion">
                                <option value="todos">Todas las reclamaciones</option>
                                <option value="af">Solo Afiliados Titulares</option>
                                <option value="co">Conyugues y Mujeres</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-md btn-primary">Descargar PDF</button>
                        <a href="{{ route('seguros.reclamacion.download') }}" class="btn btn-light-brand">
                            <i class="feather-folder-plus me-2"></i>
                            <span>Descargar Excel</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}

    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Servicios por Distrito:</h5>
            </div>
            <div class="card-body">
                <canvas id="pieChart" height="100"></canvas>
            </div>
            <div class="card-footer hstack justify-content-around">
                <div class="text-center">
                    <a href="javascript:void(0);" class="fs-16 fw-bold">{{ $totalservicios }}</a>
                    <div class="fs-11 text-muted">Numero Total</div>
                </div>
                <span class="vr"></span>
                <div class="text-center">
                    <a href="{{ route('seguros.reclamacion.exportarInformeCompleto') }}" class="btn btn-light-brand">
                        <i class="feather-folder-plus me-2"></i>
                        <span>Descargar Excel</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="col-md-9">
        <div class="card border-top-0">
            <div class="card-header p-0">
                <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab" role="tablist">
                    @foreach ($registrosPorEstado as $estado => $registros)
                        @php $paneId = 'estado-' . Str::slug($estado); @endphp
                        <li class="nav-item flex-fill border-top" role="presentation">
                            <a href="javascript:void(0);" class="nav-link {{ $loop->first ? 'active' : '' }}"
                                data-bs-toggle="tab" data-bs-target="#{{ $paneId }}" role="tab"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                {{ $estado }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="tab-content">
                @foreach ($registrosPorEstado as $estado => $registros)
                @php $paneId = 'estado-' . Str::slug($estado); @endphp
                    <div class="tab-pane fade {{ $loop->first ? 'active show' : '' }}" id="{{ $paneId }}" role="tabpanel">
                        <div class="table-responsive mt-3">
                            <table class="table table-hover tablasporestado">
                                <thead class="mb-4">
                                    <tr>
                                        <th>Asegurado</th>
                                        <th>Monto a Reclamar</th>
                                        <th>Cobertura</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($registros as $registro)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <a>
                                                        <span class="d-block">{{ $registro->cedulaAsegurado }}</span>
                                                        <span class="fs-12 d-block fw-normal text-muted">{{ $registro->tercero->nom_ter ?? ' ' }}</span>
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                {{ empty($registro->valor_asegurado) ? '' : '$' . number_format($registro->valor_asegurado, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                <span class="badge bg-gray-200 text-dark">{{ $registro->cobertura->nombre }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <div class="hstack justify-content-between mb-4">
                    <div>
                        <h5 class="mb-1">Reclamaciones por Cobertura y Género</h5>
                    </div>
                    <a href="" class="btn btn-light-brand">Descargar Excel</a>
                </div>
                <div class="row g-4">
                    <div class="row mt-3">
                        @foreach ($registrosporcobertura as $registro)
                            <div class="col-xxl-4 col-md-6 mb-3">
                                <div class="card-body border border-dashed border-gray-5 rounded-3 position-relative">
                                    <div class="hstack justify-content-between gap-4">
                                        <div>
                                            <h6 class="fs-14 text-truncate-1-line">
                                                {{ $registro->cobertura_nombre }}
                                            </h6>
                                            <div class="d-flex align-items-center gap-2">
                                                <div
                                                    class="wd-50 ht-50 bg-soft-warning text-warning lh-1 d-flex align-items-center justify-content-center flex-column rounded-2 schedule-date">
                                                    <span
                                                        class="fs-18 fw-bold mb-1 d-block">{{ $registro->mujeres }}</span>
                                                    <span class="fs-10 fw-semibold text-uppercase d-block">MUJ</span>
                                                </div>
                                                <div
                                                    class="wd-50 ht-50 bg-soft-teal text-teal lh-1 d-flex align-items-center justify-content-center flex-column rounded-2 schedule-date">
                                                    <span
                                                        class="fs-18 fw-bold mb-1 d-block">{{ $registro->hombres }}</span>
                                                    <span class="fs-10 fw-semibold text-uppercase d-block">HOM</span>
                                                </div>
                                                <div
                                                    class="wd-50 ht-50 bg-soft-primary text-primary lh-1 d-flex align-items-center justify-content-center flex-column rounded-2 schedule-date">
                                                    <span
                                                        class="fs-18 fw-bold mb-1 d-block">{{ $registro->sin_genero }}</span>
                                                    <span class="fs-10 fw-semibold text-uppercase d-block">NN</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div> --}}


    <script>
        let labels = @json($arraydata['labels']);
        let dataValues = @json($arraydata['valores']);

        const ctx = document.getElementById('pieChart');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Cantidad',
                    data: dataValues,                    
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'rect',
                            padding: 15,
                            boxWidth: 30,
                            boxHeight: 30
                        }
                    }
                }
            }
        });
    </script>
</x-base-layout>
