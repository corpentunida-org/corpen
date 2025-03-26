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
                                    <a href="{{ route('seguros.poliza.edit', ['poliza' => 'excel']) }}" class="btn btn-light-brand">
                                        <i class="feather feather-archive me-2"></i>
                                        <span>Actualizar Valor a Pagar</span>
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

    <div class="content-area ps ps--active-y" style="height: 100px;">
        <div class="content-area-header bg-white sticky-top">
            <div class="page-header-right ms-auto">
                <div class="hstack gap-2">
                    <p class="fs-12 fw-bold text-dark pt-3">Buscar por nombre: </p>
                    <div class="hstack">
                        <a href="javascript:void(0)" class="search-form-open-toggle">
                            <div class="avatar-text avatar-md" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                title="" data-bs-original-title="Search">
                                <i class="feather-search"></i>
                            </div>
                        </a>
                        <form action="{{ route('poliza.search', ['name' => 'ID']) }}" method="GET" class="search-form"
                            style="display: none">
                            <div class="search-form-inner">
                                <a href="javascript:void(0)" class="search-form-close-toggle">
                                    <div class="avatar-text avatar-md" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                        title="" data-bs-original-title="Back">
                                        <i class="feather-arrow-left"></i>
                                    </div>
                                </a>
                                <input type="search" name="id" class="py-3 px-0 border-0 w-100"
                                    placeholder="Buscar por nombre..." autocomplete="off">
                                <button type="submit" class="btn btn-primary">Buscar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('seguros.novedades.index')


</x-base-layout>
