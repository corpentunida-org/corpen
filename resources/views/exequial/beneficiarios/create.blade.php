@extends('layouts.base')
@section('contentpage')

<div class="col-lg-12">
    <div class="card stretch stretch-full">
        <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
            <div class="mb-4 mb-lg-0">
                <div class="d-flex gap-4 align-items-center">
                    <div class="avatar-text avatar-lg bg-gray-200">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-dark"><span class="counter"></span></div>
                        <h3 class="fs-13 fw-semibold text-truncate-1-line"></h3>
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
<div class="col-xxl-12 col-xl-12">
    <div class="card border-top-0">
        <div class="card-header p-0">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab" role="tablist">
                <li class="nav-item flex-fill border-top" role="presentation">
                    <a href="javascript:void(0);" class="nav-link active" data-bs-toggle="tab"
                        data-bs-target="#connectionTab" role="tab" aria-selected="true">Beneficiario</a>
                </li>
            </ul>
        </div>
        <!-- "codePastor": "string",
  "name": "string",
  "codeParentesco": "string",
  "type": "string",
  "dateEntry": "2024-11-21T17:18:28.874Z",
  "documentBeneficiaryId": "string",
  "dateBirthDate": "2024-11-21T17:18:28.874Z" -->
        <div class="tab-content">
            <div class="tab-pane fade active show" id="connectionTab" role="tabpanel">
                <div class="col-lg-12">                    
                        <div class="card-body lead-info">
                            <div class="mb-4 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0">
                                    <span class="d-block mb-2">Información:</span>
                                </h5>
                                <div class="d-flex gap-2">
                                    <a href="javascript:void(0);" class="btn btn-icon" data-bs-toggle="tooltip" title="Make as Complete">
                                        <i class="feather-check-circle"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-icon" data-bs-toggle="tooltip" title="Timesheets">
                                        <i class="feather-calendar"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-icon" data-bs-toggle="tooltip" title="Statistics">
                                        <i class="feather-bar-chart-2"></i>
                                    </a>
                                    
                                </div>
                            </div>
                            <form method="POST" id="formUpdateTitular" novalidate>
                                @csrf
                                @method('PUT')
                                <div class="table-responsive tickets-items-wrapper">
                                    <table class="table table-hover mb-0">
                                        <tbody>
                                            <tr>
                                                <td style="width: 50px;">
                                                    <a href="javascript:void(0);">Cédula</a>
                                                </td>
                                                <td>                                                    
                                                    <input class="form-control fs-12 fw-normal text-muted p-2" name="documentid">                                    
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="javascript:void(0);">Nombre</a>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control fs-12 fw-normal text-muted p-2"
                                                        name="observation" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="javascript:void(0);">Parentesco</a>
                                                </td>
                                                
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="javascript:void(0);">Fecha de nacimiento</a>                                                    
                                                </td>
                                                <td>                                                
                                                <input type="date" class="form-control fs-12 fw-normal text-muted p-2" name="discount" required>
                                            </tr> 
                                            <tr></tr>                                           
                                            
                                        </tbody>
                                    </table>
                                </div>                            
                            <div class="d-flex flex-row-reverse gap-2 mt-2">
                                <button class="btn btn-success" data-bs-toggle="tooltip" title="Beneficiario" type="submit">
                                
                                <i class="feather-plus me-2"></i>
                                <span>Agregar</span>
                            
                                </button>
                            </div>
                            </form>
                            <script>
                                $('#formUpdateTitular').submit(function (event) {
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
        </div>
    </div>
</div>

@endsection