<x-base-layout>
    @section('titlepage', 'Crear Reclamación')
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
                <div class="mb-4 mb-lg-0">
                    <div class="d-flex gap-4 align-items-center">
                        <div class="avatar-text avatar-lg bg-gray-200">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <div class="fs-12 text-muted">Titular: </div>
                            <div class="fs-4 fw-bold text-dark"><span
                                    class="counter">{{$asegurado->terceroAF->nombre}}</span></div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">{{$asegurado->terceroAF->cedula}}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 d-flex justify-content-end align-items-center">
                    <x-input-search-seguros></x-input-search-seguros>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card stretch stretch-full">
            <div class="card-body">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <label class="form-label">Asegurado</label>
                    <input type="text" class="form-control" value="{{$asegurado->cedula}}" readonly>
                </div>
                <div class="col-lg-8 mb-4">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" value="{{$asegurado->tercero->nombre}}" readonly>
                </div>
            </div>
                <div class="row">
                    <div class="col-lg-4 mb-4">
                        <label class="form-label">Tipo de Afiliado<span class="text-danger">*</span></label>
                        <input class="form-control datepicker-input" name="contacto" value="{{$asegurado->parentesco}}" readonly>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <label class="form-label">Genero <span class="text-danger">*</span></label>
                        <input class="form-control datepicker-input" value="{{ $asegurado->tercero->genero == 'V' ? 'Masculino' : 'Femenino' }}" readonly>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <label class="form-label">Distrito <span class="text-danger">*</span></label>
                        <input class="form-control datepicker-input" value="" readonly>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Cobertura</label>
                    <input type="text" class="form-control" value="">
                </div>
                <div class="row">
                    <div class="col-lg-7 mb-4">
                        <label class="form-label">Contacto 2 <span class="text-danger">*</span></label>
                        <input class="form-control datepicker-input" name="contacto2" value="">
                    </div>
                    <div class="col-lg-5 mb-4">
                        <label class="form-label">Telefono 2 <span class="text-danger">*</span></label>
                        <input type="" class="form-control datepicker-input" name="telefonoContacto2"
                            value="">
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button class="btn btn-warning" title="Prestar servicio" type="submit">
                        <i class="feather-plus me-2"></i>
                        <span>Actualizar datos</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label">Titular</label>
                    <input type="text" class="form-control" value="">
                </div>
                <div class="mb-4">
                    <label class="form-label">Parentesco</label>
                    <input type="text" class="form-control" value="">
                </div>
                <div class="row">
                    <div class="col-lg-7 mb-4">
                        <label class="form-label">Contacto 1 <span class="text-danger">*</span></label>
                        <input class="form-control datepicker-input" name="contacto" value="">
                    </div>
                    <div class="col-lg-5 mb-4">
                        <label class="form-label">Telefono 1 <span class="text-danger">*</span></label>
                        <input type="" class="form-control datepicker-input" value=""
                            name="telefonoContacto">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-7 mb-4">
                        <label class="form-label">Contacto 2 <span class="text-danger">*</span></label>
                        <input class="form-control datepicker-input" name="contacto2" value="">
                    </div>
                    <div class="col-lg-5 mb-4">
                        <label class="form-label">Telefono 2 <span class="text-danger">*</span></label>
                        <input type="" class="form-control datepicker-input" name="telefonoContacto2"
                            value="">
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button class="btn btn-warning" title="Prestar servicio" type="submit">
                        <i class="feather-plus me-2"></i>
                        <span>Actualizar datos</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
