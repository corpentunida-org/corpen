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
                                    class="counter">{{ $beneficiario->asegurado->tercero->nombre }}</span></div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">{{ $beneficiario->asegurado->cedula }}
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
                                <form method="POST" action="{{ route('seguros.beneficiario.update', $beneficiario->id) }}" id="formUpdateBeneficiario" novalidate>
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-4">
                                        <label class="form-label">Documento</label>
                                        <div class="row">
                                            <div class="col-lg-2">
                                                <input type="text" class="form-control"
                                                    value="{{ $beneficiario->tipo_documento_id }}" name="tipoDocumento"
                                                    readonly>
                                            </div>
                                            <div class="col-lg-10">
                                                <input type="text" class="form-control"
                                                    value="{{ $beneficiario->cedula }}" name="cedula" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" class="uppercase-input form-control" name="names"
                                            value="{{ $beneficiario->nombre }}" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Parentescos <span class="text-danger">*</span></label>
                                        <select class="form-control" name="parentesco">
                                            @foreach ($parentescos as $parentesco)
                                                <option value="{{ $parentesco->code }}"
                                                    {{ $beneficiario->parentesco == $parentesco->code ? 'selected' : '' }}>
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
                                                    <input type="number" class="form-control"
                                                        value="{{ $beneficiario->porcentaje }}" name="porcentaje"
                                                        required>
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="form-label">Telefono <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" class="form-control"
                                                    value="{{ $beneficiario->telefono }}" name="telefono">
                                            </div>
                                            <div class="col-lg-6">
                                                <label class="form-label">Correo <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control"
                                                    value="{{ $beneficiario->correo }}" name="correo">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-row-reverse gap-2 mt-2">
                                        <input type="hidden" value="{{$beneficiario->id_asegurado}}" name="aseguradoId">
                                        <button class="btn btn-warning" data-bs-toggle="tooltip" title="Timesheets"
                                            type="submit">
                                            <i class="feather-clock me-2"></i>
                                            <span>Actualizar Beneficiario</span>
                                        </button>
                                    </div>
                                </form>
                                <script>
                                    $('#formUpdateBeneficiario').submit(function(event) {
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
