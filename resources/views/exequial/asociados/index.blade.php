<x-base-layout>
    @section('titlepage', 'Beneficiarios')
    <x-warning />
    <style>
        #circle svg {
            width: 65px !important;
            height: 65px !important;
        }
        #circle .circle-progress-value {
            stroke: #198754 !important;
            stroke-width: 8 !important;
            stroke-linecap: round !important;
        }
    </style>
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
                                    @candirect('exequial.asociados.store')
                                    <a href="{{ route('exequial.asociados.create') }}" class="btn btn-primary">
                                        <i class="feather-plus me-2"></i>
                                        <span>Crear Titular</span>
                                    </a>
                                    @endcandirect
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
</x-base-layout>
