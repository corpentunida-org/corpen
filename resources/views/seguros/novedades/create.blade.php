<x-base-layout>
    @section('titlepage', 'Novedad')
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
                <div class="mb-4 mb-lg-0">
                    <div class="d-flex gap-4 align-items-center">
                        <div class="avatar-text avatar-lg bg-gray-200">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <div class="fs-12 text-muted">Titular: </div>
                            <div class="fs-4 fw-bold text-dark"><span
                                    class="counter">{{ $asegurado->terceroAF->nombre }}</span></div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">{{ $asegurado->titular }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-12 col-xl-12">
        <div class="card border-top-0">
            <div class="card-header p-0">
                <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#securityTab"
                            aria-selected="true">Actualizar Poliza</a>
                    </li>
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" data-bs-target="#TaskTab" aria-selected="true">Retiro
                            Poliza</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 active show" id="securityTab" role="tabpanel">
                    <div class="col-lg-12 p-4">
                        <form method="POST" action="{{ route('seguros.novedades.store') }}"
                            id="formAddNovedad" novalidate>
                            @csrf
                            @method('POST')
                            <div class="row">
                                <div class="col-lg-4 mb-4">
                                    <label class="form-label">Cédula Asegurado</label>
                                    <input type="text" class="form-control" name="asegurado"
                                        value="{{ $asegurado->cedula }}" readonly>
                                        
                                </div>
                                <div class="col-lg-8 mb-4">
                                    <label class="form-label">Nombre Asegurado</label>
                                    <input type="text" class="form-control" value="{{ $asegurado->tercero->nombre }}"
                                        disabled>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 mb-4">
                                    <label class="form-label">Plan</label>
                                    <input type="text"
                                        class="form-control uppercase-input"value="{{ $asegurado->polizas->first()->plan->name }}"
                                        disabled>
                                </div>
                                <div class="col-lg-3 mb-4">
                                    <label class="form-label">Valor prima</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">$</span>
                                        <input type="text" class="form-control" name="valorPrima"
                                            value="{{ number_format($asegurado->polizas->first()->plan->prima) }}">
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-4">
                                    <label class="form-label">Valor asegurado</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">$</span>
                                        <input type="text" name="valorAsegurado" class="form-control"
                                            value="{{ number_format($asegurado->polizas->first()->valor_asegurado) }}">
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-4">
                                    <label class="form-label">Valor a pagar</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">$</span>
                                        <input type="text" class="form-control" name="valorpagaraseguradora"
                                            value="{{ $asegurado->valorpAseguradora }}"
                                            @if ($asegurado->parentesco == 'AF') required @endif>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-4">
                                    <label class="form-label">Asignar Plan<span class="text-danger">*</span></label>
                                    <select class="form-control" name="planid">
                                        @foreach ($planes as $plan)
                                            <option value="{{ $plan->id }}"
                                                {{ $asegurado->polizas->first()->plan->id == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->convenio->nombre }} - {{ $plan->name }} -
                                                ${{ number_format($plan->valor) }} - ${{ number_format($plan->prima) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line pt-1">{{$condicion->descripcion}}</span>
                                </div>
                                <div class="col-lg-3 mb-4">
                                    <label class="form-label">Porcentaje Descuento<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="descuento" value="{{ $asegurado->polizas->first()->descuento }}" required>
                                </div>
                                <div class="col-lg-3 mb-4">
                                    <label class="form-label">Extra Prima<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="extra_prima" value="{{ $asegurado->polizas->first()->extra_prima }}" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Observacion<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="observaciones" required>
                                <input type="hidden" name="id_poliza"
                                    value="{{ $asegurado->polizas->first()->id }}">
                            </div>
                            <div class="d-flex flex-row-reverse gap-2 mt-2">
                                <button class="btn btn-success mt-4" data-bs-toggle="tooltip" title="Timesheets"
                                    type="submit">
                                    <i class="feather-plus me-2"></i>
                                    <span>Crear Novedad</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane fade p-4 active show" id="TaskTab" role="tabpanel">
                    <div class="col-lg-12 p-4">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#formAddNovedad').submit(function(event) {
            var form = this;
            if (!form.checkValidity()) {
                $(form).addClass('was-validated');
                event.preventDefault();
                event.stopPropagation();
            } else {
                console.log($(this).serialize());
            }
        });
    </script>
</x-base-layout>
