<x-base-layout>
    <x-warning />
    <div class="col-12">
        <div class="card">
            <div class="card-body py-1 m-0">
                <div class="row">
                    <div class="col-sm-12 col-md-8 mr-2">
                        <div class="content-area ps ps--active-y" style="height: 100px;">
                            <div class="content-area-header bg-white sticky-top m-0 p-0">
                                <div class="page-header-right ms-auto justify-content-end m-0 pt-4">
                                    <div class="hstack gap-2 m-0">
                                        <p class="fs-12 fw-bold text-dark pt-3">Buscar por nombre: </p>
                                        <div class="hstack">
                                            <a href="" class="search-form-open-toggle">
                                                <div class="avatar-text avatar-md" data-bs-toggle="tooltip"
                                                    data-bs-trigger="hover" title=""
                                                    data-bs-original-title="Search">
                                                    <i class="feather-search"></i>
                                                </div>
                                            </a>
                                            <form action="{{ route('cinco.retiros.search', ['name' => 'ID']) }}" method="GET"
                                                class="search-form" style="display: none">
                                                <div class="search-form-inner pt-4">
                                                    <a href="" class="search-form-close-toggle">
                                                        <div class="avatar-text avatar-md" data-bs-toggle="tooltip"
                                                            data-bs-trigger="hover" title=""
                                                            data-bs-original-title="Back">
                                                            <i class="feather-arrow-left"></i>
                                                        </div>
                                                    </a>
                                                    <input type="search" name="id" class="px-0 border-0 w-100"
                                                        placeholder="Buscar por nombre..." autocomplete="off">
                                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4 my-auto">
                        <form action="{{ route('cinco.movcontables.show', ['cinco' => 'ID']) }}" method="GET"
                            class="d-flex align-items-center gap-2 page-header-right-items-wrapper" validate>
                            <label class="mb-0 me-2">Buscar:</label>
                            <input type="text" name="id" class="form-control form-control-sm"
                                placeholder="número de cédula" aria-controls="customerList" required>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Movimientos Contables del aplicacativo CINCO antes del año 2007</h5>
                <a href="javascript:void(0);" class="btn btn-md btn-light-brand">
                    <i class="feather-plus me-2"></i>
                    <span>Services</span>
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-xxl-12 col-md-12">
                        <div class="card stretch stretch-full">
                            <div class="card-body">
                                <div class="avatar-text rounded-2 mb-4">
                                    <i class="feather-archive"></i>
                                </div>
                                <h6 class="fw-bold mb-3 text-truncate-1-line">Buscar por número de cedula</h6>
                                <p class="text-muted mb-4 text-truncate-3-line">Toda la información de cuentas contables
                                    de los pastores, viudas y musicos, antiguas al año 2007. En la parte inferior, se
                                    puede generar un informe PDF de todas las cuentas.</p>
                                <a href="javascript:void(0);"
                                    class="d-block fs-10 fw-bold text-dark text-uppercase text-spacing-1">Buscar →</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
