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
                            <a class="btn btn-icon" data-bs-toggle="offcanvas" data-bs-target="#proposalSent" title="Prestar Servicio">
                                <i class="fa-regular fa-bell"></i>
                            </a>
                            <a href="javascript:void(0);" class="btn btn-icon" data-bs-toggle="tooltip"
                                title="Statistics">
                                <i class="feather-bar-chart-2"></i>
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
    </div>
</div>


@endsection