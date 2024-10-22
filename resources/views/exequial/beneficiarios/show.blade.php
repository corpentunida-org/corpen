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
                        <div class="fs-4 fw-bold text-dark"><span class="counter">{{ $asociado['name'] }}</span></div>
                        <h3 class="fs-13 fw-semibold text-truncate-1-line">{{ $asociado['documentId'] }}</h3>
                    </div>
                </div>
            </div>
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
                <a href="javascript:void(0);" class="btn btn-success" data-bs-toggle="tooltip" title="Timesheets">
                    <i class="feather-clock me-2"></i>
                    <span>Start Timer</span>
                </a>
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
                <div class="col-lg-12">
                    <div class="card stretch stretch-full">
                        <div class="card card-body lead-info">
                            <div class="mb-4 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0">
                                    <span class="d-block mb-2">Información:</span>
                                </h5>
                            </div>
                            <div class="row mb-4">
                                <div class="col-lg-2 fw-medium">Cédula</div>
                                <div class="col-lg-10"><a href="javascript:void(0);">{{ $asociado['documentId'] }}</a>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-lg-2 fw-medium">Observación</div>
                                <div class="col-lg-10"><a href="javascript:void(0);">{{ $asociado['observation'] }}</a>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-lg-2 fw-medium">Plan</div>
                                <div class="col-lg-10"><a href="javascript:void(0);">{{ $asociado['codePlan'] }}</a>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-lg-2 fw-medium">Descuento:</div>
                                <div class="col-lg-10"><a href="javascript:void(0);">{{ $asociado['discount'] }}</a>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-lg-2 fw-medium">Fecha de Inicio</div>
                                <div class="col-lg-10"><a href="javascript:void(0);">{{ $asociado['dateInit'] }}</a>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-lg-2 fw-medium">Contrato</div>
                                <div class="col-lg-10"><a href="javascript:void(0);">{{ $asociado['agreement'] }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade p-4" id="securityTab" role="tabpanel">
                <div class="col-lg-12">
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <div id="proposalList_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                                    <div class="row dt-row">
                                        <div class="col-sm-12">
                                            <table class="table table-hover dataTable no-footer" id="proposalList"
                                                aria-describedby="proposalList_info">
                                                <thead>
                                                    <tr>                                                        
                                                        <th class="text-start sorting" tabindex="0" aria-controls="proposalList"
                                                            rowspan="1" colspan="1"
                                                            aria-label="Proposal: activate to sort column ascending"
                                                            style="width: 67.9844px;">
                                                            Cédula</th>                                                        
                                                        <th class="sorting" tabindex="0" aria-controls="proposalList"
                                                            rowspan="1" colspan="1"
                                                            aria-label="Client: activate to sort column ascending"
                                                            style="width: 247.75px;">
                                                            Nombre</th>
                                                        <th class="sorting" tabindex="0" aria-controls="proposalList"
                                                            rowspan="1" colspan="1"
                                                            aria-label="Amount: activate to sort column ascending"
                                                            style="width: 94.8281px;">
                                                            Parentesco</th>                                                        
                                                        <th class="sorting" tabindex="0" aria-controls="proposalList"
                                                            rowspan="1" colspan="1"
                                                            aria-label="Status: activate to sort column ascending"
                                                            style="width: 50.7812px;">
                                                            Fecha Nacimiento</th>
                                                            <th class="sorting" tabindex="0" aria-controls="proposalList"
                                                            rowspan="1" colspan="1"
                                                            aria-label="Status: activate to sort column ascending"
                                                            style="width: 50.7812px;">
                                                            Edad</th>
                                                        <th class="text-end sorting" tabindex="0"
                                                            aria-controls="proposalList" rowspan="1" colspan="1"
                                                            aria-label="Actions: activate to sort column ascending"
                                                            style="width: 127.484px;">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($beneficiarios as $beneficiario)
                                                {{-- Table de beneficiarios activos --}}
                                                @if ($beneficiario['type'] == 'A')
                                                    <tr class="single-item odd">                                                    
                                                        <td>{{ $beneficiario['documentId'] }}</td>
                                                        <td>{{ $beneficiario['names'] }}</td>
                                                        <td>{{ $beneficiario['relationship'] }}</td>
                                                        <td>{{ $beneficiario['dateBirthday'] }}</td>
                                                        @php
                                                            $fecNac = new DateTime($beneficiario['dateBirthday']);
                                                            $fechaActual = new DateTime();
                                                            $diferencia = $fecNac->diff($fechaActual);
                                                            $edad = $diferencia->y;
                                                        @endphp
                                                        <td>{{ $edad }}</td>
                                                        <td>
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <a href="javascript:void(0)"
                                                                    class="avatar-text avatar-md"
                                                                    data-bs-toggle="offcanvas"
                                                                    data-bs-target="#proposalSent">
                                                                    <i class="feather feather-send"></i>
                                                                </a>
                                                                <a href="proposal-view.html"
                                                                    class="avatar-text avatar-md">
                                                                    <i class="feather feather-eye"></i>
                                                                </a>
                                                                <div class="dropdown">
                                                                    <a href="javascript:void(0)"
                                                                        class="avatar-text avatar-md"
                                                                        data-bs-toggle="dropdown" data-bs-offset="0,21">
                                                                        <i class="feather feather-more-horizontal"></i>
                                                                    </a>
                                                                    <ul class="dropdown-menu">
                                                                        <li>
                                                                            <a class="dropdown-item"
                                                                                href="proposal-edit.html">
                                                                                <i
                                                                                    class="feather feather-edit-3 me-3"></i>
                                                                                <span>Edit</span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a class="dropdown-item printBTN"
                                                                                href="javascript:void(0)">
                                                                                <i
                                                                                    class="feather feather-printer me-3"></i>
                                                                                <span>Print</span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a class="dropdown-item"
                                                                                href="javascript:void(0)">
                                                                                <i
                                                                                    class="feather feather-clock me-3"></i>
                                                                                <span>Remind</span>
                                                                            </a>
                                                                        </li>
                                                                        <li class="dropdown-divider"></li>
                                                                        <li>
                                                                            <a class="dropdown-item"
                                                                                href="javascript:void(0)">
                                                                                <i
                                                                                    class="feather feather-archive me-3"></i>
                                                                                <span>Archive</span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a class="dropdown-item"
                                                                                href="javascript:void(0)">
                                                                                <i
                                                                                    class="feather feather-alert-octagon me-3"></i>
                                                                                <span>Report Spam</span>
                                                                            </a>
                                                                        </li>
                                                                        <li class="dropdown-divider"></li>
                                                                        <li>
                                                                            <a class="dropdown-item"
                                                                                href="javascript:void(0)">
                                                                                <i
                                                                                    class="feather feather-trash-2 me-3"></i>
                                                                                <span>Delete</span>
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                                @endforeach                                                     
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection