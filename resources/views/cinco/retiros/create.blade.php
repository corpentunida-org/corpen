<x-base-layout>
    <x-success />
    <x-error />
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Crear Retiro: </span>
                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">Verifique los datos que va a exportar en formato pdf </span>
                    </h5>
                </div>
                <form class="row" method="post" action="{{ route('seguros.beneficios.list') }}"
                    id="formFiltroBeneficios" novalidate>
                    @method('POST')
                    @csrf
                    <div class="row">
                        <div class="col-12 col-lg-4 mb-4">
                            <label class="form-label">Edad</label>
                            <div class="row pt-3">
                                <div class="col-xxl-6 col-md-6">
                                    <input type="number" class="form-control" name="edad_minima"
                                        placeholder="Edad mínima" required>
                                </div>
                                <div class="col-xxl-6 col-md-6">
                                    <input type="number" class="form-control" name="edad_maxima"
                                        placeholder="Edad máxima" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3 mb-4">
                            <label class="form-label">Tipo Afiliado</label>
                            <select class="form-control mt-3" name="tipo">
                                <option value="AF">TITULAR</option>
                                <option value="CO">CONYUGUE</option>
                                <option value="HI">HIJO</option>
                                <option value="HE">HERMANO</option>
                                <option value="VIUDA">VIUDA</option>
                                <option value="TODOS">TODOS</option>
                            </select>
                        </div>
                        <div class="col-12 col-lg-5 mb-4">
                            <label class="form-label">Planes</label>
                            
                        </div>
                    </div>
                </form>
            </div>
        </div>
<x-base-layout>