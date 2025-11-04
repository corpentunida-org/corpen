{{-- resources/views/soportes/tipos/create.blade.php --}}

<x-base-layout>
    @section('titlepage', 'Crear Nuevo Tipo de Soporte')

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
                <div class="mb-4 mb-lg-0">
                    <div class="d-flex gap-4 align-items-center">
                        <div class="avatar-text avatar-lg bg-info text-white">
                            <i class="bi bi-lightbulb"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold text-dark">Crear Nuevo Tipo de Soporte</div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">Clasifica tus soportes con nuevos tipos.</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-12 col-xl-12 mt-3">
        <div class="card border-top-0">
            <div class="card-header p-0">
                <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a href="javascript:void(0);" class="nav-link active" data-bs-toggle="tab" data-bs-target="#infoTab" role="tab" aria-selected="true">
                            Información del Tipo
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 active show" id="infoTab" role="tabpanel">
                    {{-- Incluimos el parcial del formulario y le pasamos la acción de almacenamiento --}}
                    @include('soportes.tipos._form', ['formAction' => route('soportes.tipos.store')])
                </div>
            </div>
        </div>
    </div>
</x-base-layout>