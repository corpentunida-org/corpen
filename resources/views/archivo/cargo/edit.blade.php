{{-- resources/views/archivo/cargo/edit.blade.php --}}

<x-base-layout>
    @section('titlepage', 'Editar Cargo')

    {{-- Encabezado Principal (Se mantiene el diseño) --}}
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
                <div class="mb-4 mb-lg-0">
                    <div class="d-flex gap-4 align-items-center">
                        <div class="avatar-text avatar-lg bg-gray-200">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold text-dark">
                                {{ $cargo->nombre_cargo }}
                            </div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">
                                Cédula Asociada: {{ $cargo->GDO_empleados_cedula ?? 'N/A' }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pestañas y Contenido (Se mantiene el diseño) --}}
    <div class="col-xxl-12 col-xl-12 mt-3">
        <div class="card border-top-0">
            <div class="card-header p-0">
                <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a href="javascript:void(0);" class="nav-link active" data-bs-toggle="tab"
                            data-bs-target="#infoTab" role="tab" aria-selected="true">
                            Información General del Cargo
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 active show" id="infoTab" role="tabpanel">

                    {{-- 
                        Formulario de Actualización 
                        Ahora es mucho más simple y reutiliza el mismo formulario de 'create'.
                    --}}
                    <form method="POST" action="{{ route('archivo.cargo.update', $cargo->id) }}" enctype="multipart/form-data">

                        @csrf
                        @method('PUT')

                        {{-- ¡LA MAGIA OCURRE AQUÍ! --}}
                        {{-- Incluimos el formulario parcial. Laravel pasará automáticamente la variable $cargo a esta vista. --}}
                        @include('archivo.cargo.form')

                        {{-- Botones de Acción --}}
                        <div class="d-flex flex-row-reverse gap-2 mt-4">
                            <button class="btn btn-success" type="submit">
                                <i class="bi bi-save me-2"></i> Actualizar Cargo
                            </button>
                            <a href="{{ route('archivo.cargo.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>