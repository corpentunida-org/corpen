<x-base-layout>
    <style>
        .cursor-pointer {
            cursor: pointer;
        }
    </style>
    <x-warning />
    <x-success />
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
                                            <form action="{{ route('poliza.search', ['name' => 'ID']) }}" method="GET"
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
                        <x-input-search-seguros></x-input-search-seguros>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card stretch stretch-full" data-select2-id="select2-data-38-lija">
            <div class="card-body">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Importar o Exportar Datos:</span>
                    </h5>
                </div>
                <div class="row">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center justify-content-center">
                            @can('seguros.poliza.update')
                            <a href="{{ route('seguros.poliza.edit', ['poliza' => 'excel']) }}"
                                class="d-flex me-1 btn btn-light-brand">
                                <i class="feather feather-edit-3 me-2"></i>
                                <span>Actualizar Valor a Pagar</span>
                            </a>
                            @endcan
                            <a href="{{ route('seguros.poliza.download') }}"
                                class="d-flex me-1 px-3 bg-soft-indigo text-indigo border border-soft-indigo"
                                style="padding: 12px 0 12px 0;">
                                <i class="feather-folder-plus me-2"></i>
                                <span class="text-uppercase cursor-pointer" style="font-size: 10px;">Descargar CxC</span>
                            </a>
                        </div>
                        <div class="d-flex align-items-center justify-content-center">
                            @can('seguros.poliza.store')
                                <a href="{{route('seguros.poliza.create')}}" class="d-flex me-1 btn btn-primary">
                                    <i class="feather-plus me-2"></i>
                                    <span>Crear Póliza Individual</span>
                                </a>                            
                                <a href="{{ route('seguros.poliza.viewupload') }}"
                                    class="d-flex me-1 btn btn-light-brand ml-1">
                                    <i class="feather-upload me-2"></i>
                                    <span>Subir Excel Crear Póliza</span>
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- @include('seguros.novedades.index') --}}

</x-base-layout>