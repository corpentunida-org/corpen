<x-base-layout>
    <x-warning />
    <div class="col-12">
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
                                    <a href="" class="btn btn-primary">
                                        <i class="feather-plus me-2"></i>
                                        <span>Crear Poliza</span>
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
                        <x-input-search-seguros></x-input-search-seguros>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="content-area ps ps--active-y">
        <div class="content-area-header bg-white sticky-top">
            <div class="page-header-right ms-auto">
                <div class="hstack gap-2">
                    <div class="hstack">
                        <a href="javascript:void(0)" class="search-form-open-toggle">
                            <div class="avatar-text avatar-md" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                title="" data-bs-original-title="Search">
                                <i class="feather-search"></i>
                            </div>
                        </a>
                        <form class="search-form" style="display: none">
                            <div class="search-form-inner">
                                <a href="javascript:void(0)" class="search-form-close-toggle">
                                    <div class="avatar-text avatar-md" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                        title="" data-bs-original-title="Back">
                                        <i class="feather-arrow-left"></i>
                                    </div>
                                </a>
                                <input type="search" class="py-3 px-0 border-0 w-100" id="emailSearch"
                                    placeholder="Search..." autocomplete="off">
                            </div>
                        </form>
                    </div>
                    <div class="dropdown d-none d-sm-flex">
                        <a href="javascript:void(0)" class="btn btn-light-brand btn-sm rounded-pill dropdown-toggle"
                            data-bs-toggle="dropdown" data-bs-offset="0,23" aria-expanded="false">1-15 of 762
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="javascript:void(0)">Oldest</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0)">Newest</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0)">Replied</a></li>
                            <li class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="javascript:void(0)">Ascending</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0)">Descending</a></li>
                        </ul>
                    </div>
                    <div class="hstack d-none d-sm-flex">
                        <a href="javascript:void(0)" class="d-flex me-1">
                            <div class="avatar-text avatar-md" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                title="" data-bs-original-title="Previous">
                                <i class="feather-chevron-left"></i>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="d-flex me-1">
                            <div class="avatar-text avatar-md" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                title="" data-bs-original-title="Next">
                                <i class="feather-chevron-right"></i>
                            </div>
                        </a>
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
                        <span class="fs-12 text-muted">Asegurados</span>
                    </div>
                    <a href="javascript:void(0);" class="btn btn-light-brand">Ver Todo</a>
                </div>
                <div class="row g-4">
                    <div class="col-xxl-4 col-md-4">
                        <div class="card-body border border-dashed border-gray-5 rounded-3 position-relative">
                            <div class="hstack justify-content-between gap-4">
                                <div>
                                    <h6 class="fs-14 text-truncate-1-line">Total Asociados</h6>
                                    <div class="fs-12 text-muted"><span class="text-dark fw-medium">Mes:</span>
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
                    <div class="col-xxl-4 col-md-4">
                        <div class="card-body border border-dashed border-gray-5 rounded-3 position-relative">
                            <div class="hstack justify-content-between gap-4">
                                <div>
                                    <h6 class="fs-14 text-truncate-1-line">Total Beneficiarios</h6>
                                    <div class="fs-12 text-muted"><span class="text-dark fw-medium">Deadiline:</span>
                                        20
                                        days left</div>
                                </div>
                                <div class="project-progress-3"></div>
                            </div>
                            <div class="badge bg-gray-200 text-dark project-mini-card-badge">Updates</div>
                        </div>
                    </div>
                    <div class="col-xxl-4 col-md-4">
                        <div class="card-body border border-dashed border-gray-5 rounded-3 position-relative">
                            <div class="hstack justify-content-between gap-4">
                                <div>
                                    <h6 class="fs-14 text-truncate-1-line">Cuenta de cobro</h6>
                                    <div class="fs-12 text-muted"><span class="text-dark fw-medium">Deadiline:</span>
                                        20
                                        days left</div>
                                </div>
                                <div class="project-progress-1"></div>
                            </div>
                            <div class="badge bg-gray-200 text-dark project-mini-card-badge">Updates</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-base-layout>
