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
                                        <span></span>
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
                        <form action="{{ route('cinco.movcontables.show', ['cinco' => 'ID']) }}" method="GET"
                            class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                            <label class="mb-0 me-2">Buscar:</label>
                            <input type="text" name="id" class="form-control form-control-sm"
                                placeholder="número de cédula" aria-controls="customerList">
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
                <h5 class="card-title">Base de datos aplicativo antiguo 2007</h5>
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
                                <h6 class="fw-bold mb-3 text-truncate-1-line">Website design and development</h6>
                                <p class="text-muted mb-4 text-truncate-3-line">Website design and development -
                                    designing and building a website to meet specific requirements, such as the look and
                                    feel, functionality, and content.</p>
                                <a href="javascript:void(0);"
                                    class="d-block fs-10 fw-bold text-dark text-uppercase text-spacing-1">Learn More
                                    →</a>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
