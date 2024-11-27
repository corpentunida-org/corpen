<x-base-layout>
<div class="col-lg-12">
    <div class="card stretch stretch-full">
        <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
            <div class="mb-4 mb-lg-0">
                <div class="d-flex gap-4 align-items-center">
                    <div class="avatar-text avatar-lg bg-gray-200">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-dark"><span class="counter">{{ $asociado['name'] }}</span></div>
                        <h3 class="fs-13 fw-semibold text-truncate-1-line">{{ $asociado['documentId'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 d-flex justify-content-end align-items-center">
                <form action="{{ route('exequial.asociados.show', ['asociado' => 'ID']) }}" method="GET"
                    class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <label for="search-input" class="mb-0 me-2">Buscar:</label>
                    <input type="text" name="id" class="form-control form-control-sm" id="valueCedula"
                        placeholder="cédula titular" aria-controls="customerList">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
<div class="col-xxl-12 col-xl-12">
    <div class="card border-top-0">
        <div class="card-header p-0">
            <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab" role="tablist">
                <li class="nav-item flex-fill border-top" role="presentation">
                    <a href="javascript:void(0);" class="nav-link" data-bs-toggle="tab" data-bs-target="#securityTab"
                        role="tab" aria-selected="false">Beneficiarios</a>
                </li>
            </ul>
        </div>
        <div class="tab-content">
            <div class="tab-pane fade p-4 active show" id="securityTab" role="tabpanel">
                <div class="col-lg-12">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <form method="POST" action="{{ route('exequial.beneficiarios.update', $id )}}" id="formUpdateBeneficiario" novalidate>
                                @csrf
                                @method('PUT')
                                <div class="mb-4">
                                    <label class="form-label">Cedula <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $id }}" name="cedula" readonly>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $beneficiario['name'] }}" name="names" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Parentescos <span class="text-danger">*</span></label>
                                    <select class="form-control" name="parentesco">
                                        @foreach($parentescos as $par)
                                        <option value="{{ $par['code'] }}" {{ $par['name'] == $beneficiario['relationship'] ? 'selected' : '' }}>
                                            {{ $par['name'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Fecha de nacimiento <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" value="{{ $beneficiario['dateBirthday'] }}" name="fechaNacimiento" required>
                                </div>
                                <input type="hidden" name="documentid" value="{{ $asociado['documentId'] }}">
                                <div class="d-flex flex-row-reverse gap-2 mt-2">
                                <button class="btn btn-warning" data-bs-toggle="tooltip" title="Timesheets" type="submit">
                                    <i class="feather-clock me-2"></i>
                                    <span>Actualizar Beneficiario</span>
                                </button>
                                </div>
                            </form>
                            <script>
                                $('#formUpdateBeneficiario').submit(function (event) {
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
</div>
</x-base-layout>
