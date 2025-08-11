<x-base-layout>
    @section('titlepage', 'Editar Área')

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body d-lg-flex align-items-center justify-content-between">
                <div class="mb-4 mb-lg-0">
                    <div class="d-flex gap-4 align-items-center">
                        <div class="avatar-text avatar-lg bg-gray-200">
                            <i class="bi bi-diagram-3"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold text-dark">
                                {{ $area->nombre }}
                            </div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">
                                Estado: {{ ucfirst($area->estado) }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-12 col-xl-12 mt-3">
        <div class="card border-top-0">
            <div class="card-header p-0">
                <ul class="nav nav-tabs flex-wrap w-100 text-center" role="tablist">
                    <li class="nav-item flex-fill border-top">
                        <a href="javascript:void(0);" class="nav-link active" data-bs-toggle="tab"
                           data-bs-target="#infoTab" role="tab" aria-selected="true">
                            Información del Área
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 active show" id="infoTab" role="tabpanel">
                    <form method="POST" action="{{ route('archivo.area.update', $area->id) }}">
                        @csrf
                        @method('PUT')
                        @include('archivo.area.form')

                        <div class="d-flex flex-row-reverse gap-2 mt-4">
                            <button class="btn btn-success" type="submit">
                                <i class="bi bi-save me-2"></i> Actualizar Área
                            </button>
                            <a href="{{ route('archivo.area.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
