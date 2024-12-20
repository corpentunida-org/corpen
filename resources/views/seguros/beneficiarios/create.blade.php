<x-base-layout>
    <style>
        .uppercase-input {
            text-transform: uppercase;
        }
    </style>
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
                <div class="mb-4 mb-lg-0">
                    <div class="d-flex gap-4 align-items-center">
                        <div class="avatar-text avatar-lg bg-gray-200">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold text-dark"><span
                                    class="counter">{{ $asegurado->tercero->nombre }}</span></div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">{{ $asegurado->cedula }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 d-flex justify-content-end align-items-center">
                    <x-input-search-seguros></x-input-search-seguros>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-12 col-xl-12">
        <div class="card border-top-0">
            <div class="card-header p-0">
                <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a href="javascript:void(0);" class="nav-link" data-bs-toggle="tab"
                            data-bs-target="#securityTab" role="tab" aria-selected="false">Beneficiarios</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 active show" id="securityTab" role="tabpanel">
                    <div class="col-lg-12">
                        <div class="card stretch stretch-full">
                            <div class="card-body">
                                <form method="POST" action="{{ route('seguros.beneficiario.store') }}" id="formAddBeneficiario" novalidate>
                                    @csrf
                                    @method('POST')
                                    <div class="mb-4">
                                        <div class="row">
                                            <div class="col-lg-2">
                                                <label class="form-label">Tipo de Documento<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control"name="tipoDocumento">
                                                    <option value="CC">CC</option>
                                                    <option value="TI">TI</option>
                                                    <option value="CE">CE</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-10">
                                                <label class="form-label">Número Documento<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="cedula" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" class="uppercase-input form-control" name="names"
                                            required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Parentescos <span class="text-danger">*</span></label>
                                        <select class="form-control" name="parentesco">
                                            @foreach ($parentescos as $parentesco)
                                                <option value="{{ $parentesco->code }}">
                                                    {{ $parentesco->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <label class="form-label">Porcentaje <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" name="porcentaje"
                                                        required>
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="form-label">Telefono <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="telefono">
                                            </div>
                                            <div class="col-lg-6">
                                                <label class="form-label">Correo <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="correo">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="row">
                                            <div class="col-lg-2">
                                                <label class="form-label">Beneficiario Contingente <span class="text-danger">*</span></label>
                                                <div class="form-check form-switch form-switch-sm ps-5 mt-2">
                                                    <input class="form-check-input c-pointer" type="checkbox" value="false" name="contigente">
                                                    <label class="form-check-label fw-500 text-dark c-pointer"
                                                        for="commentSwitch">Si</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="form-label">Cedula Contingente <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="cedulaContingente">
                                            </div>
                                            <div class="col-lg-7">
                                                <label class="form-label">Nombre Contingente <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="nombreContingente">
                                            </div>
                                        </div>
                                        <input type="hidden" value="{{$poliza}}" name="poliza">
                                        <input type="hidden" value="{{$asegurado->cedula}}" name="asegurado">
                                        <div class="d-flex flex-row-reverse gap-2 mt-2">
                                            <button class="btn btn-success mt-4" data-bs-toggle="tooltip"
                                                title="Timesheets" type="submit">
                                                <i class="feather-plus me-2"></i>
                                                <span>Agregar Beneficiario</span>
                                            </button>
                                        </div>
                                </form>
                                <script>
                                    $('#formAddBeneficiario').submit(function(event) {
                                        var form = this;
                                        if (!form.checkValidity()) {
                                            $(form).addClass('was-validated');
                                            event.preventDefault();
                                            event.stopPropagation();
                                        } else {
                                            console.log($(this).serialize());
                                        }
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
