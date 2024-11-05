
@extends('layouts.base')
@section('contentpage')
<h1>Vista de edit</h1>
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
                <form action="{{ route('exequial.beneficiarios.show', ['beneficiario' => 'ID']) }}" method="GET"
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
                        <div class="table-responsive">
                            <div id="proposalList_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                                <div class="row">
                                    
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
