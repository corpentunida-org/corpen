<x-base-layout>
    @section('titlepage', 'Movimientos Contables')
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
                <div class="mb-4 mb-lg-0">
                    <div class="d-flex gap-4 align-items-center">
                        <div class="avatar-text avatar-lg bg-gray-200">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold text-dark"><span class="counter">ASOCIADO NAME SSEFC</span></div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">23401841</h3>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 d-flex justify-content-end align-items-center">
                    <form action="{{ route('seguros.poliza.show', ['poliza' => 'ID']) }}" method="GET"
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

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">FAQ's</h5>
                <a href="javascript:void(0);" class="btn btn-md btn-light-brand">
                    <i class="feather-plus me-2"></i>
                    <span>Add New FAQ</span>
                </a>
            </div>
            <div class="card-body">
                <div class="accordion proposal-faq-accordion" id="accordionFaqGroup">
                    <div class="accordion-item">
                        <h2 class="accordion-header" >
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseOne" aria-expanded="false"
                                aria-controls="flush-collapseOne">01. Can I change my package?</button>
                        </h2>
                        <div id="flush-collapseOne" class="accordion-collapse collapse"
                            aria-labelledby="flush-headingOne" data-bs-parent="#accordionFaqGroup" style="">
                            <div class="accordion-body">Yes! Youc can change your package at any time. Upgrades will
                                apply immediately to all your live and drafted events, so you can take advantage of
                                professional product capabolities.</div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" >
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseTwo" aria-expanded="false"
                                aria-controls="flush-collapseTwo">01. Can I change my package?</button>
                        </h2>
                        <div id="flush-collapseTwo" class="accordion-collapse collapse"
                            aria-labelledby="flush-collapseTwo" data-bs-parent="#accordionFaqGroup" style="">
                            <div class="accordion-body">Yes! Youc can change your package at any time. Upgrades will
                                apply immediately to all your live and drafted events, so you can take advantage of
                                professional product capabolities.</div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

</x-base-layout>
