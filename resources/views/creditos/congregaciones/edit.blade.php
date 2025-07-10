{{-- resources/views/creditos/congregaciones/edit.blade.php --}}

<x-base-layout>
    @section('titlepage', 'Editar Congregación')

    {{-- Encabezado con el nombre de la congregación --}}
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
                <div class="mb-4 mb-lg-0">
                    <div class="d-flex gap-4 align-items-center">
                        <div class="avatar-text avatar-lg bg-success text-white">
                            <i class="bi bi-church"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold text-dark">
                                {{ $congregacion->Nombre_templo }}
                            </div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">
                                Código: {{ $congregacion->Codigo }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Formulario dentro de una tarjeta con pestañas --}}
    <div class="col-xxl-12 col-xl-12 mt-3">
        <div class="card border-top-0">
            <div class="card-header p-0">
                <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a href="javascript:void(0);" class="nav-link active" data-bs-toggle="tab" data-bs-target="#infoTab" role="tab" aria-selected="true">
                            Información General
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 active show" id="infoTab" role="tabpanel">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <form method="POST" action="{{ route('creditos.congregaciones.update', $congregacion->codigo) }}" id="formUpdateCongregacion" novalidate>
                                @csrf
                                @method('PUT') {{-- Directiva para indicar que es una actualización --}}
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Código <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="Codigo" value="{{ old('Codigo', $congregacion->codigo) }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre del Templo <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control text-uppercase" name="Nombre_templo" value="{{ old('Nombre_templo', $congregacion->Nombre_templo) }}" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Pastor</label>
                                        <input type="text" class="form-control text-uppercase" name="Pastor" value="{{ old('Pastor', $congregacion->Pastor) }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Clase</label>
                                        <input type="text" class="form-control" name="Clase" value="{{ old('Clase', $congregacion->Clase) }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Estado <span class="text-danger">*</span></label>
                                        <select class="form-select" name="Estado" required>
                                            <option value="A" {{ old('Estado', $congregacion->Estado) == 'A' ? 'selected' : '' }}>Activo</option>
                                            <option value="I" {{ old('Estado', $congregacion->Estado) == 'I' ? 'selected' : '' }}>Inactivo</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Municipio</label>
                                        <input type="text" class="form-control" name="Municipio" value="{{ old('Municipio', $congregacion->Municipio) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Dirección</label>
                                        <input type="text" class="form-control" name="Direccion" value="{{ old('Direccion', $congregacion->Direccion) }}">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" name="Telefono" value="{{ old('Telefono', $congregacion->Telefono) }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Celular</label>
                                        <input type="tel" class="form-control" name="Celular" value="{{ old('Celular', $congregacion->Celular) }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Distrito</label>
                                        <input type="text" class="form-control" name="Dist" value="{{ old('Dist', $congregacion->Dist) }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha Apertura</label>
                                        <input type="date" class="form-control" name="Fecha_Ap" value="{{ old('Fecha_Ap', $congregacion->Fecha_Ap) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha Cierre</label>
                                        <input type="date" class="form-control" name="Fecha_Cie" value="{{ old('Fecha_Cie', $congregacion->Fecha_Cie) }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Observaciones</label>
                                    <textarea class="form-control" name="Obser" rows="3">{{ old('Obser', $congregacion->Obser) }}</textarea>
                                </div>

                                <div class="d-flex flex-row-reverse gap-2 mt-4">
                                    <button class="btn btn-warning" type="submit">
                                        <i class="feather-save me-2"></i>
                                        <span>Actualizar Congregación</span>
                                    </button>
                                    <a href="{{ route('creditos.congregaciones.index') }}" class="btn btn-light">Cancelar</a>
                                </div>
                            </form>
                            
                            <script>
                                (function () {
                                    'use strict'
                                    const form = document.getElementById('formUpdateCongregacion');
                                    form.addEventListener('submit', function (event) {
                                        if (!form.checkValidity()) {
                                            event.preventDefault();
                                            event.stopPropagation();
                                        }
                                        form.classList.add('was-validated');
                                    }, false);
                                })();
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>