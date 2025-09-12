<x-base-layout>
    @section('titlepage', 'Actualizar Novedad')

    <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
                <div class="mb-4 mb-lg-0">
                    <div class="d-flex gap-4 align-items-center">
                        <div class="avatar-text avatar-lg bg-gray-200">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <div class="fs-12 text-muted">Titular: </div>
                            <div class="fs-4 fw-bold text-dark"><span class="counter">MEJIA ACEVEDO JOSE FELIX</span></div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">10055058
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 d-flex justify-content-end align-items-center">
                    <form action="http://localhost:83/poliza/ID" method="GET" class="row gx-2 align-items-center ml-2">
    <div class="col-auto">
        <label for="search-input" class="col-form-label mb-0">Buscar por:</label>
    </div>
    <div class="col">
        <input type="text" name="id" class="form-control" id="valueCedula" placeholder="cédula" aria-controls="customerList">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-search"></i>
        </button>
    </div>
</form>                </div>
            </div>

    <div class="col-lg-12 p-4 card">
                        <div class="row">
                                <div class="col-lg-3 mb-4">
                                    <label class="form-label">Cedula Asegurado</label>
                                    <input type="text" class="form-control" value=""
                                        disabled>
                                </div>
                                <div class="col-lg-7 mb-4">
                                    <label class="form-label">Nombre Asegurado</label>
                                    <input type="text" class="form-control" value=""
                                        disabled>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 mb-4">
                                    <label class="form-label">Plan</label>
                                    <input type="text" class="form-control uppercase-input" value="" disabled>
                                </div>
                                <div class="col-lg-4 mb-4">
                                    <label class="form-label">Valor Asegurado</label>
                                    <input type="text" id="valoraseguradoplan" name="valorAsegurado"
                                        class="form-control"
                                        value="$ {{ number_format(@$asegurado?->polizas?->first()?->valor_asegurado ?? 0) }}"
                                        disabled>
                                </div>
                                <div class="col-lg-4 mb-4">
                                    <label class="form-label">Valor a Pagar Mensualidad</label>
                                    <input type="text" id="primaplan" class="form-control" name="valorPrima"
                                        value="$ {{ number_format(@$asegurado?->polizas?->first()?->primapagar ?? 0) }}"
                                        disabled>
                                </div>
                            </div>
                        @if (isset($asegurado))                           
                            <form method="POST" action="{{ route('seguros.novedades.store') }}" id="formAddNovedad"
                                novalidate>
                                @csrf
                                @method('POST')
                                <div class="row">
                                    <div class="col-lg-6 mb-4">
                                        <input type="hidden" name="asegurado" value="{{$asegurado->cedula}}">
                                        <input type="hidden" name="tipoNovedad" value="1">
                                        <label class="form-label">Asignar Plan<span class="text-danger">*</span></label>
                                        <select class="form-control" name="planid" id="selectPlan">
                                            @foreach ($planes as $plan)
                                                <option value="{{ $plan->id }}" data-valor="{{ $plan->valor }}"
                                                    data-prima="{{ $plan->prima_aseguradora }}"
                                                    {{ $asegurado->polizas->first()->plan->id == $plan->id ? 'selected' : '' }}>
                                                    {{ $plan->convenio->nombre ?? ''}} - {{ $plan->name }} -
                                                    ${{ number_format($plan->valor) }} -
                                                    ${{ number_format($plan->prima_aseguradora) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span
                                            class="fs-12 fw-normal text-muted text-truncate-1-line pt-1">{{ $condicion->descripcion }}</span>
                                    </div>                                                                    
                                    <div class="col-lg-2 mb-4">
                                        <label class="form-label">Porcentaje Extra Prima<span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="inputExtraPrima" name="extra_prima"
                                                value="{{ $asegurado->polizas->first()->extra_prima }}" min=0 max="100">
                                            <span class="input-group-text">%</span>
                                            <input type="hidden" id="valorprimaaumentar" name="valorprimaaumentar">
                                        </div>
                                        <div class="fs-12 fw-normal text-muted text-truncate-1-line text-wrap pt-1">
                                            <div class="custom-control custom-checkbox" id="checkextraprimaval" style="display: none;">
                                                <input type="checkbox" class="form-check-input ml-3" id="checkbox2"
                                                    name="checkconfirmarextra" value=true>
                                                <label class="form-check-label text-wrap" for="checkbox2" id="labeltextextra" style="white-space: normal; word-wrap: break-word; max-width: 100%;"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-4">
                                        <label class="form-label">Valor a Pagar<span
                                                class="text-danger">*</span></label>                                        
                                            <input type="text" class="form-control" name="primacorpen" value="{{ $asegurado->polizas->first()->primapagar ?? 0 }}" id="valorapagarcorpen">                                        
                                    </div>
                                </div>                        
                                <div class="table-responsive mt-4">
                                    <label class="form-label">Grupo Familiar</span></label>
                                    <table class="table mb-0">
                                        <thead>
                                            <tr class="border-top">
                                                <th>Cedula</th>
                                                <th>Nombre</th>
                                                <th>Parentesco</th>
                                                <th>Plan</th>
                                                <th class="text-center">Valor Asegurado</th>
                                                <th class="text-end">Prima Plan</th>
                                                <th class="text-end" >Valor a Pagar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($grupoFamiliar as $gf)
                                                <tr>
                                                    <td><a
                                                            href="{{ route('seguros.poliza.show', ['poliza' => 'ID']) . '?id=' . $gf->cedula }}">{{ $gf->cedula }}</a>
                                                    </td>
                                                    <td>{{ $gf->nombre_tercero ?? ' '}}</td>
                                                    <td>{{ $gf->parentesco }}</td>
                                                    <td><span
                                                            class="badge bg-soft-warning text-warning">{{ $gf->polizas->first()->plan->name }}</span>
                                                    </td>
                                                    <td class="text-center">$ {{ number_format($gf->polizas->first()->valor_asegurado) }}</td>
                                                    <td class="text-end">$ {{ number_format($gf->polizas->first()->valor_prima) }}</td>
                                                    <td class="text-end">$ {{ number_format($gf->polizas->first()->primapagar) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr>                                                
                                                <td colspan="6" class="text-end">Total
                                                    <span class="badge bg-gray-200 text-dark" style="font-size: 15px;">$
                                                        {{ number_format($totalPrima) }} </span>
                                                </td>
                                                <td class="text-end">Total
                                                    <span class="badge bg-gray-200 text-dark" style="font-size: 15px;">$
                                                        {{ number_format($totalPrimaCorpen) }} </span>
                                                </td>
                                            </tr>                                            
                                        </tbody>
                                    </table>
                                </div>                            
                                @if ($asegurado->parentesco == 'AF' || $asegurado->viuda)
                                    <div class="row align-items-center justify-content-end">
                                        <div class="col-lg-9 text-end mt-4">
                                            <label class="form-label">Total Valor a Pagar Titular <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-lg-3 mt-4">
                                            <div class="input-group">
                                                <div class="input-group-text">$</div>
                                                <input class="form-control"
                                                    value="{{ $asegurado->polizas->first()->valorpagaraseguradora }}"
                                                    type="text" name="valorpagaraseguradora">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-lg-2 mb-4">
                                        <label class="form-label">Estado de la Novedad<span class="text-danger">*</span></label>
                                        <select class="form-control" name="estado">
                                            <option value="1">SOLICITUD</option>
                                            <option value="2">RADICADO</option>
                                            <option value="3">APROBADO</option>
                                            <option value="4">NEGADO</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-10 mb-4">
                                        <label class="form-label">Observación<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control text-uppercase" name="observaciones" required>
                                        <input type="hidden" name="id_poliza" value="{{ $asegurado->polizas->first()->id }}">
                                    </div>
                                </div>
                                <div class="d-flex flex-row-reverse gap-2 mt-2">
                                    <button class="btn btn-success mt-4" data-bs-toggle="tooltip" title="Timesheets"
                                        type="submit">
                                        <i class="feather-plus me-2"></i>
                                        <span>Crear Novedad</span>
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
</x-base-layout>