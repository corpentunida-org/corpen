<x-base-layout>
    @section('titlepage', 'Home')
    <div class="col-12">
        <div class="alert alert-dismissible mb-4 p-4 d-flex alert-soft-warning-message profile-overview-alert"
            role="alert">
            <div class="me-4 d-none d-md-block">
                <i class="feather feather-alert-triangle fs-1"></i>
            </div>
            <div>
                <p class="fw-bold mb-1 text-truncate-1-line">Tu perfil no ha sido actualizado!!!</p>
                <p class="fs-10 fw-medium text-uppercase text-truncate-1-line">Last Update: <strong>26 Dec, 2023</strong>
                </p>
                <a href="javascript:void(0);" class="btn btn-sm bg-soft-warning text-warning d-inline-block">ASIGNAR UN ROL AHORA</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
</x-base-layout>
