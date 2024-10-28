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
                <a href="{{route('exequial.asociados.edit', $asociado['documentId'])}}" class="btn btn-warning"
                    data-bs-toggle="tooltip" title="Timesheets">
                    <i class="feather-clock me-2"></i>
                    <span>Actualizar Titular</span>
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
                            <div class="table-responsive tickets-items-wrapper">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <td style="width: 50px;">
                                                <a href="javascript:void(0);">Cédula</a>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);">{{ $asociado['documentId'] }} <span
                                                        class="fs-12 fw-normal text-muted">{{ $asociado['name'] }}</span></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <a href="javascript:void(0);">Observación</a>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control fs-12 fw-normal text-muted"
                                                    value="{{ $asociado['observation'] }}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <a href="javascript:void(0);">Plan</a>
                                            </td>
                                            <td>
                                            <select class="form-select select2-hidden-accessible fw-normal text-muted"
                                                    data-select2-selector="icon" data-select2-id="select2-data-7-19rj"
                                                    tabindex="-1" aria-hidden="true" name="planes[]">
                                                @foreach($plans as $plan)
                                                    <option value="{{ $plan['code'] }}" 
                                                            data-icon="feather-check text-success"
                                                            data-select2-id="select2-data-9-2vkf" 
                                                            class="fs-12 fw-normal text-muted"
                                                            {{ $asociado['codePlan'] == $plan['code'] ? 'selected' : '' }}>
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
                                            <input type="text" class="form-control fs-12 fw-normal text-muted"
                                            value="{{ $asociado['discount'] }}">

                                        </tr>
                                        <tr class="mb-1">
                                            <td>
                                                <a href="javascript:void(0);">Contrato</a>
                                            </td>
                                            <td>
                                                <span
                                                    class="fs-12 fw-normal text-muted">{{ $asociado['agreement'] }}</span>
                                            </td>
                                        </tr>
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

@endsection