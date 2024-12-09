<x-base-layout>
    @section('titlepage', 'Beneficiarios')
<div class="col-12">
    <x-warning />
    <div class="card stretch stretch-full">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="page-header-right ms-auto">
                        <div class="page-header-right-items">
                            <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                                <a href="javascript:void(0);" class="btn btn-icon btn-light-brand"
                                    data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                    <i class="feather-bar-chart"></i>
                                </a>
                                <a href="{{route('exequial.asociados.create')}}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>
                                    <span>Crear Titular</span>
                                </a>
                            </div>
                        </div>
                        <div class="d-md-none d-flex align-items-center">
                            <a href="javascript:void(0)" class="page-header-right-open-toggle">
                                <i class="feather-align-right fs-20"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 d-flex justify-content-end align-items-center">
                    <form action="{{ route('exequial.asociados.show', ['asociado' => 'ID']) }}" method="GET"
                        class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                        <label for="search-input" class="mb-0 me-2">Buscar:</label>
                        <input type="text" name="id" class="form-control form-control-sm" id="valueCedula"
                            placeholder="cédula titular" aria-controls="customerList">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="col-12">
    <div class="card stretch stretch-full">
        <div class="card-body">
            <div class="hstack justify-content-between mb-4 pb-">
                <div>
                    <h5 class="mb-1">Estadisticas</h5>
                    <span class="fs-12 text-muted">Beneficiarios</span>
                </div>
                <a href="javascript:void(0);" class="btn btn-light-brand">Ver Todos</a>
            </div>
            <div class="row g-4">
                <div class="col-xxl-3 col-md-6">
                    <div class="card-body border border-dashed border-gray-5 rounded-3 position-relative">
                        <div class="hstack justify-content-between gap-4">
                            <div>
                                <h6 class="fs-14 text-truncate-1-line">Beneficiarios Agregados</h6>
                                <div class="fs-12 text-muted"><span class="text-dark fw-medium">Mes 2024:</span>
                                    <script>
                                        const meses = [
                                            "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                                            "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
                                        ];
                                        const fecha = new Date();
                                        const mesActual = meses[fecha.getMonth()];
                                        document.write(mesActual)
                                    </script>
                                </div>
                            </div>
                            <div class="project-progress-4"></div>
                        </div>
                        <div class="badge bg-gray-200 text-dark project-mini-card-badge">Updates</div>
                    </div>
                </div>
                <div class="col-xxl-3 col-md-6">
                    <div class="card-body border border-dashed border-gray-5 rounded-3 position-relative">
                        <div class="hstack justify-content-between gap-4">
                            <div>
                                <h6 class="fs-14 text-truncate-1-line">Beneficiarios Actualizados</h6>
                                <div class="fs-12 text-muted"><span class="text-dark fw-medium">Deadiline:</span> 20
                                    days left</div>
                            </div>
                            <div class="project-progress-3"></div>
                        </div>
                        <div class="badge bg-gray-200 text-dark project-mini-card-badge">Updates</div>
                    </div>
                </div>
                <div class="col-xxl-3 col-md-6">
                    <div class="card-body border border-dashed border-gray-5 rounded-3 position-relative">
                        <div class="hstack justify-content-between gap-4">
                            <div>
                                <h6 class="fs-14 text-truncate-1-line">Beneficiarios Eliminados</h6>
                                <div class="fs-12 text-muted"><span class="text-dark fw-medium">Deadiline:</span> 20
                                    days left</div>
                            </div>
                            <div class="project-progress-1"></div>
                        </div>
                        <div class="badge bg-gray-200 text-dark project-mini-card-badge">Updates</div>
                    </div>
                </div>
                <div class="col-xxl-3 col-md-6">
                    <div class="card-body border border-dashed border-gray-5 rounded-3 position-relative">
                        <div class="hstack justify-content-between gap-4">
                            <div>
                                <h6 class="fs-14 text-truncate-1-line">Servicios Prestados</h6>
                                <div class="fs-12 text-muted"><span class="text-dark fw-medium">Deadiline:</span> 20
                                    days left</div>
                            </div>
                            <div class="project-progress-2"></div>
                        </div>
                        <div class="badge bg-gray-200 text-dark project-mini-card-badge">Updates</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</x-base-layout>>
