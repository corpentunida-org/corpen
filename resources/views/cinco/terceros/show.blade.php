<x-base-layout>

    <div class="col-12">
        <x-warning />
        <div class="card stretch stretch-full">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <form method="GET" class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                            <label for="search-input" class="mb-0 me-2">Buscar:</label>
                            <input type="text" name="id" class="form-control form-control-sm" id="valueCedula"
                                placeholder="nombre titular" aria-controls="customerList">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                        </form>
                    </div>
                    <div class="col-sm-12 col-md-6 d-flex justify-content-end align-items-center">
                        <form method="GET" class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
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

    <div class="col-12">
        <div class="card">
            <div class="card-body personal-info">
                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">{{$tercero->Cod_Ter}}</span>
                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">Información Personal:</span>
                    </h5>
                    <a href="javascript:void(0);" class="btn btn-sm btn-light-brand">Add New</a>
                </div>
                
                <div class="row mb-4 align-items-center">
                    <div class="col-lg-4">
                        <label for="fullnameInput" class="fw-semibold">Nombre: </label>
                    </div>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <div class="input-group-text"><i class="feather-user"></i></div>
                            <input type="text" class="form-control" disabled>
                        </div>
                    </div>
                </div>                
                
                <div class="row mb-4 align-items-center">
                    <div class="col-lg-4">
                        <label for="phoneInput" class="fw-semibold">Phone: </label>
                    </div>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <div class="input-group-text"><i class="feather-phone"></i></div>
                            <input type="text" class="form-control" id="phoneInput" placeholder="Phone">
                        </div>
                    </div>
                </div>
                <div class="row mb-4 align-items-center">
                    <div class="col-lg-4">
                        <label for="companyInput" class="fw-semibold">Company: </label>
                    </div>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <div class="input-group-text"><i class="feather-compass"></i></div>
                            <input type="text" class="form-control" id="companyInput" placeholder="Company">
                        </div>
                    </div>
                </div>
                <div class="row mb-4 align-items-center">
                    <div class="col-lg-4">
                        <label for="designationInput" class="fw-semibold">Designation: </label>
                    </div>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <div class="input-group-text"><i class="feather-briefcase"></i></div>
                            <input type="text" class="form-control" id="designationInput"
                                placeholder="Designation">
                        </div>
                    </div>
                </div>
                <div class="row mb-4 align-items-center">
                    <div class="col-lg-4">
                        <label for="websiteInput" class="fw-semibold">Website: </label>
                    </div>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <div class="input-group-text"><i class="feather-link"></i></div>
                            <input type="text" class="form-control" id="websiteInput" placeholder="Website">
                        </div>
                    </div>
                </div>
                <div class="row mb-4 align-items-center">
                    <div class="col-lg-4">
                        <label for="VATInput" class="fw-semibold">VAT: </label>
                    </div>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <div class="input-group-text"><i class="feather-dollar-sign"></i></div>
                            <input type="text" class="form-control" id="VATInput" placeholder="VAT">
                        </div>
                    </div>
                </div>
                <div class="row mb-4 align-items-center">
                    <div class="col-lg-4">
                        <label for="addressInput_2" class="fw-semibold">Address: </label>
                    </div>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <div class="input-group-text"><i class="feather-map-pin"></i></div>
                            <textarea class="form-control" id="addressInput_2" cols="30" rows="3" placeholder="Address"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row mb-4 align-items-center">
                    <div class="col-lg-4">
                        <label for="aboutInput" class="fw-semibold">About: </label>
                    </div>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <div class="input-group-text"><i class="feather-type"></i></div>
                            <textarea class="form-control" id="aboutInput" cols="30" rows="5" placeholder="About"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-base-layout>
