<x-base-layout>
    @section('titlepage', 'Crear Nuevo Tipo de Observación')

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
                <div class="mb-4 mb-lg-0">
                    <div class="d-flex gap-4 align-items-center">
                        <div class="avatar-text avatar-lg bg-dark text-white">
                            <i class="bi bi-chat-dots"></i> {{-- Icono para Tipos de Observación --}}
                        </div>
                        <div>
                            <div class="fs-4 fw-bold text-dark">
                                Crear Nuevo Tipo de Observación
                            </div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">
                                Define nuevas categorías para tus observaciones de soporte.
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
                <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a href="javascript:void(0);" class="nav-link active" data-bs-toggle="tab"
                            data-bs-target="#infoTab" role="tab" aria-selected="true">
                            Información del Tipo de Observación
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 active show" id="infoTab" role="tabpanel">

                    {{-- Formulario para crear un nuevo tipo de observación --}}
                    <form method="POST" action="{{ route('soportes.tipoObservaciones.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label text-capitalize">Nombre</label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex flex-row-reverse gap-2 mt-4">
                            <button class="btn btn-success" type="submit">
                                <i class="feather-plus me-2"></i> Crear Tipo de Observación
                            </button>
                            <a href="{{ route('soportes.tablero') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-base-layout>