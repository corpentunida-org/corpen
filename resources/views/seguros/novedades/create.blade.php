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
                        <form method="POST" action="{{ route('seguros.novedades.store') }}" id="formAddNovedad"
                            novalidate>
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
                                <div class="col-lg-4 mb-4">
                                    <label class="form-label">Plan</label>
                                    <input type="text"
                                        class="form-control uppercase-input"value="{{ $asegurado->polizas->first()->plan->name }}"
                                        disabled>
                                </div>
                                <div class="col-lg-4 mb-4">
                                    <label class="form-label">Valor asegurado</label>
                                    <input type="text" id="valoraseguradoplan" name="valorAsegurado"
                                        class="form-control"
                                        value="$ {{ number_format($asegurado->polizas->first()->valor_asegurado) }}"
                                        disabled>
                                </div>
                                <div class="col-lg-4 mb-4">
                                    <label class="form-label">Valor prima</label>
                                    <input type="text" id="primaplan" class="form-control" name="valorPrima"
                                        value="$ {{ number_format($asegurado->polizas->first()->plan->prima) }}"
                                        disabled>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-4">
                                    <label class="form-label">Asignar Plan<span class="text-danger">*</span></label>
                                    <select class="form-control" name="planid" id="selectPlan">
                                        @foreach ($planes as $plan)
                                            <option value="{{ $plan->id }}" data-valor="{{ $plan->valor }}"
                                                data-prima="{{ $plan->prima }}"
                                                {{ $asegurado->polizas->first()->plan->id == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->convenio->nombre }} - {{ $plan->name }} -
                                                ${{ number_format($plan->valor) }} -
                                                ${{ number_format($plan->prima) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span
                                        class="fs-12 fw-normal text-muted text-truncate-1-line pt-1">{{ $condicion->descripcion }}</span>
                                </div>
                                <div class="col-lg-2 mb-4">
                                    <label class="form-label">Valor Descuento<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="valordescuento"
                                        value="{{ $asegurado->polizas->first()->descuento }}">
                                </div>
                                <div class="col-lg-2 mb-4">
                                    <label class="form-label">Porcentaje Descuento<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="descuento"
                                        value="{{ $asegurado->polizas->first()->descuentopor }}">
                                </div>
                                <div class="col-lg-2 mb-4">
                                    <label class="form-label">Extra Prima<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="extra_prima"
                                        value="{{ $asegurado->polizas->first()->extra_prima }}">
                                </div>
                            </div>
                            
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr class="border-top">
                                                <th>Cedula</th>
                                                <th>Nombre</th>
                                                <th>Parentesco</th>
                                                <th>Plan</th>
                                                <th>Valor Asegurado</th>
                                                <th>Prima</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($grupoFamiliar as $gf)
                                                <tr>
                                                    <td><a href="{{ route('seguros.poliza.show', ['poliza' => 'ID']) . '?id=' . $gf->cedula; }}">{{ $gf->cedula }}</a></td>
                                                    <td>{{ $gf->tercero->nombre }}</td>
                                                    <td>{{ $gf->parentesco }}</td>
                                                    <td><span
                                                            class="badge bg-soft-warning text-warning">{{ $gf->polizas->first()->plan->name }}</span>
                                                    </td>
                                                    <td>$ {{ number_format($gf->polizas->first()->valor_asegurado) }}
                                                    </td>
                                                    <td>$ {{ number_format($gf->polizas->first()->valor_prima) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="5" class="text-end">Total</td>
                                                <td><span class="badge bg-gray-200 text-dark"
                                                        style="font-size: 15px;">$
                                                        {{ number_format($totalPrima) }} </span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row align-items-center justify-content-end">
                                    <div class="col-lg-9 text-end mt-4">
                                        <label class="form-label">Valor a pagar titular <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-3 mt-4">
                                        <div class="input-group">
                                            <div class="input-group-text">$</div>
                                            <input class="form-control" value="{{ $asegurado->terceroAF->asegurados->first()->valorpAseguradora }}"
                                                type="text" name="valorpagaraseguradora">
                                        </div>
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

                <div class="tab-pane fade p-4 " id="TaskTab" role="tabpanel">
                    <form method="POST" class="p-4"
                        action="{{ route('seguros.poliza.destroy', ['poliza' => $asegurado->polizas->first()->id]) }}">
                        @csrf
                        @method('DELETE')
                        <div class="row">
                            <div class="col-lg-4 mb-4">
                                <label class="form-label">Cédula Asegurado</label>
                                <input type="text" class="form-control" name="aseguradoid"
                                    value="{{ $asegurado->cedula }}" readonly>
                            </div>
                            <div class="col-lg-8 mb-4">
                                <label class="form-label">Nombre Asegurado</label>
                                <input type="text" class="form-control" value="{{ $asegurado->tercero->nombre }}"
                                    disabled>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Plan </span></label>
                            <input class="form-control" name="planid"
                                value="{{ $asegurado->polizas->first()->plan->convenio->nombre }} - {{ $asegurado->polizas->first()->plan->name }} - ${{ number_format($asegurado->polizas->first()->plan->valor) }} - ${{ number_format($asegurado->polizas->first()->plan->prima) }}"
                                disabled>
                            <span
                                class="fs-12 fw-normal text-muted text-truncate-1-line pt-1">{{ $condicion->descripcion }}</span>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Observación<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="observacionretiro" required>
                        </div>
                        <div class="d-flex flex-row-reverse gap-2 mt-2">
                            <button class="btn btn-danger mt-4" data-bs-toggle="tooltip" title="Timesheets"
                                type="submit">
                                <span>Cancelar Poliza</span>
                            </button>
                        </div>
                    </form>
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

        $('#selectPlan').on('change', function() {
            const selected = $(this).find('option:selected');
            $('#valoraseguradoplan').val("$ " + selected.data('valor'));
            $('#primaplan').val("$ " + selected.data('prima'));
        });
    </script>
</x-base-layout>
