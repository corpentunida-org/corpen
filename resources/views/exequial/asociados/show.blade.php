<x-base-layout>
@section('titlepage', 'Asociado')
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
                    <a href="javascript:void(0);" class="nav-link active" data-bs-toggle="tab"
                        data-bs-target="#connectionTab" role="tab" aria-selected="true">Titular</a>
                </li>
                <li class="nav-item flex-fill border-top" role="presentation">
                    <a href="javascript:void(0);" class="nav-link" data-bs-toggle="tab" data-bs-target="#securityTab"
                        role="tab" aria-selected="false">Beneficiarios</a>
                </li>
            </ul>
        </div>
        <div class="tab-content">
            <div class="tab-pane fade active show" id="connectionTab" role="tabpanel">
                <div class="card-body lead-info">
                    <div class="mb-4 d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold mb-0">
                            <span class="d-block mb-2">Información:</span>
                        </h5>
                        <div class="d-flex gap-2">
                            <!-- <a href="javascript:void(0);" class="btn btn-icon" data-bs-toggle="tooltip" title="Prestar Servicio" data-bs-target="#proposalSent">
                                <i class="fa-regular fa-bell"></i>
                            </a> -->
                            <a href="javascript:void(0);" class="btn btn-icon" data-bs-toggle="offcanvas" data-bs-target="#proposalSent2">
                                <i class="fa-regular fa-bell"></i>
                            </a>

                            <a href="{{route('exequial.asociados.edit', $asociado['documentId'])}}"
                                class="btn btn-warning" data-bs-toggle="tooltip">
                                <i class="feather-clock me-2"></i>
                                <span>Actualizar Titular</span>
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive tickets-items-wrapper">
                        <table class="table table-hover mb-0">
                            <tbody>
                                <tr>
                                    <td style="width: 50px;">
                                        <a href="javascript:void(0);">Cédula</a>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);">{{ $asociado['documentId'] }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="javascript:void(0);">Observación</a>
                                    </td>
                                    <td>
                                        <span class="fs-12 fw-normal text-muted">{{ $asociado['observation'] }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="javascript:void(0);">Plan</a>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);">{{ $asociado['codePlan'] }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="javascript:void(0);">Descuento</a>
                                    </td>
                                    <td>
                                        <span
                                            class="fs-12 text-muted text-truncate-1-line tickets-sort-desc">{{ $asociado['discount'] }}</span>

                                </tr>
                                <tr class="mb-1">
                                    <td>
                                        <a href="javascript:void(0);">Fecha de Inicio</a>
                                    </td>
                                    <td>
                                        <span class="fs-12 fw-normal text-muted">{{ $asociado['dateInit'] }}</span>
                                    </td>
                                </tr>
                                <tr class="mb-1">
                                    <td>
                                        <a href="javascript:void(0);">Contrato</a>
                                    </td>
                                    <td>
                                        <span class="fs-12 fw-normal text-muted">{{ $asociado['agreement'] }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade p-4" id="securityTab" role="tabpanel">
                <div class="col-lg-12">
                    @include('exequial.beneficiarios.show')
                </div>
            </div>           
        </div>

        <div class="d-flex justify-content-end gap-2 m-3 ">
            <a href={{ route('asociados.generarpdf', ['id' => $asociado['documentId'], 'active' => 'false'])}} class="btn btn-md bg-soft-teal text-teal border-soft-teal">Descargar PDF</a>
            <a href={{ route('asociados.generarpdf', ['id' => $asociado['documentId'], 'active' => 'true'])}} class="btn btn-md btn-primary">Descargar certificado</a>
        </div>
    </div>
</div>


<div class="offcanvas offcanvas-end" tabindex="-1" id="proposalSent2">
    <div class="offcanvas-header ht-80 px-4 border-bottom border-gray-5">
        <div>
            <h2 class="fs-16 fw-bold text-truncate-1-line">PRESTAR SERVICIO</h2>
            <small class="fs-12 text-muted">25 MAY, 2023</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <form method="POST" action="{{ route('exequial.prestarServicio.store') }}" id="FormularioPrestarServicioTitular"novalidate>
        @csrf
        <div
            class="py-3 px-4 d-flex justify-content-between align-items-center border-bottom border-bottom-dashed border-gray-5 bg-gray-100">
            <div>
                <input class="fw-bold text-dark p-0 m-0 border border-0 bg-gray-100" id="nameTitular"
                    style="width: 100%;" name="nameBeneficiary" value="{{ $asociado['name'] }}" readonly>
                <input class="fs-12 fw-medium text-muted p-0 m-0 border border-0 bg-gray-100" id="idTitular"
                    name="cedulaFallecido" value="{{ $asociado['documentId'] }}" readonly>
            </div>
        </div>
        <input type="hidden" name="pastor" id="ispastor" value="true">
        <input type="hidden" id="cedulaTitular" name="cedulaTitular" value="{{ $asociado['documentId'] }}">
        <input type="hidden" value="{{ $asociado['name'] }}" name="nameTitular">
        <input type="hidden" id="parentescoServicio" name="parentesco">
        <input type="hidden" name="fecNacFallecido" id="fecNacFallecido">
        <input type="hidden" name="dateInit" id="dateInit" value="{{ $asociado['dateInit'] }}">

        <div class="offcanvas-body">
            <div class="form-group mb-4">
                <label class="form-label">Lugar Fallecimiento<span class="text-danger">*</span></label>
                <input type="text" class="form-control uppercase-input" name="lugarFallecimiento" required>
            </div>
            <div class="row">
                <div class="form-group col-lg-6 mb-4">
                    <label class="form-label">Fecha<span class="text-danger">*</span></label>
                    <input type="date" class="form-control datepicker-input" name="fechaFallecimiento" required>
                </div>
                <div class="form-group col-lg-6 mb-4">
                    <label class="form-label">Hora<span class="text-danger">*</span></label>
                    <input type="time" class="form-control" name="horaFallecimiento" required>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label">Contacto 1<span class="text-danger">*</span></label>
                <input type="text" class="form-control uppercase-input" name="contacto">
            </div>
            <div class="form-group mb-4">
                <label class="form-label">Contacto 2</label>
                <input type="text" class="form-control uppercase-input" name="contacto2">
            </div>
            <div class="row">
                <div class="form-group col-lg-6 mb-4">
                    <label class="form-label">Telefono 1<span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="telefonoContacto">
                </div>
                <div class="form-group col-lg-6 mb-4">
                    <label class="form-label">Telefono 2</label>
                    <input type="number" class="form-control" name="telefonoContacto2">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-lg-4 mb-4">
                    <label class="form-label">Factura<span class="text-danger">*</span></label>
                    <input type="text" class="form-control uppercase-input" name="factura">
                </div>
                <div class="form-group col-lg-4 mb-4">
                    <label class="form-label">Valor<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="valor">
                </div>
                <div class="form-group col-lg-4 mb-4">
                    <label class="form-label">Traslado<span class="text-danger">*</span></label>
                    <select class="form-control" name="traslado">
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="px-4 gap-2 d-flex align-items-center ht-80 border border-end-0 border-gray-2">
            <a href="javascript:void(0);" class="btn btn-danger w-50" data-bs-dismiss="offcanvas">Cancel</a>
            <button type="submit" class="btn btn-success w-50">Prestar Servicio</button>
        </div>
    </form>
</div>

<script>
    const today = new Date();

    const day = today.getDate();
    const month = today.toLocaleString('en-US', { month: 'short' }).toUpperCase(); // Nombre del mes en MAY
    const year = today.getFullYear();

    // Formatear la fecha
    const formattedDate = `${day} ${month}, ${year}`;

    // Mostrar la fecha en el elemento con id 'currentDate'
    document.getElementsByClassName('fechahoy').textContent = formattedDate;

    $(document).ready(function () {
        $('#FormularioPrestarServicioTitular').submit(function (event) {
        
        var form = this;
        if (!form.checkValidity()) {
            $(form).addClass('was-validated');
            event.preventDefault();
            event.stopPropagation();
        } else {
            console.log($(this).serialize());
        }
    });
    });
    
</script>
</x-base-layout>