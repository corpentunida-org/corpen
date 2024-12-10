<x-base-layout>
<div class="col-12">
    <div class="card stretch stretch-full">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="page-header-right ms-auto">
                        <div class="page-header-right-items">
                            <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                                <a href="javascript:void(0);" class="btn btn-icon btn-light-brand"
                                    data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                    <i class="feather-bar-chart"></i>
                                </a>                                
                            </div>
                        </div>
                        <div class="d-md-none d-flex align-items-center">
                            <a href="javascript:void(0)" class="page-header-right-open-toggle">
                                <i class="feather-align-right fs-20"></i>
                            </a>
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
</div>
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="col-12">
    <div class="card stretch stretch-full">
        <div class="card-body">
            <form action="{{ route('exequial.asociados.store')}}" method="POST" id="formAddTitular" novalidate>
                @csrf
                <div class="table-responsive tickets-items-wrapper">
                    <table class="table table-hover mb-0">
                        <tbody>
                            <tr>
                                <td style="width: 50px;">
                                    <a href="javascript:void(0);">Cédula</a>
                                </td>
                                <td>
                                    <input class="form-control fs-12 fw-normal text-muted p-2" name="documentId" required>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="javascript:void(0);">Observación</a>
                                </td>
                                <td>
                                    <input type="text" class="form-control fs-12 fw-normal text-muted p-2"
                                        name="observaciones" required>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="javascript:void(0);">Plan</a>
                                </td>
                                <td>
                                    <select class="form-select select2-hidden-accessible fw-normal text-muted p-2"
                                        data-select2-selector="icon" data-select2-id="select2-data-7-19rj" tabindex="-1"
                                        aria-hidden="true" name="plan">
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan['code'] }}">
                                                {{ $plan['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="javascript:void(0);">Descuento</a>
                                </td>
                                <td>
                                    <input type="number" class="form-control fs-12 fw-normal text-muted p-2" name="discount" required>
                                </td>
                            </tr>
                            <tr></tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex flex-row-reverse gap-2 mt-2">
                    <button class="btn btn-success" data-bs-toggle="tooltip" title="Timesheets" type="submit">
                        <i class="feather-plus me-2"></i>
                        <span>Crear Titular</span>
                    </button>
                </div>
            </form>
            <script>
                $('#formAddTitular').submit(function (event) {
                    var form = this;
                    if (!form.checkValidity()) {
                        $(form).addClass('was-validated'); // Aplicar la clase al formulario que estás enviando
                        event.preventDefault();
                        event.stopPropagation();
                    } else {
                        console.log($(this).serialize()); // Serializa y muestra los datos solo si la validación pasa
                    }
                });
            </script>
        </div>
    </div>
</div>
</x-base-layout>
