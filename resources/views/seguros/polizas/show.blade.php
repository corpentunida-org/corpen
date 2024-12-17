<x-base-layout>
    @section('titlepage', 'Polizas')
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
                                    class="counter">{{ $poliza->asegurado->terceroAF->nombre }}</span></div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">{{ $poliza->asegurado->titular }}</h3>
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

    {{-- <div class="col-xxl-12 col-xl-12">
    <div class="card">
        <div class="card-body">
            
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Asegurado:</h5>
                <a href="javascript:void(0);" class="btn btn-sm btn-light-brand">4 days remaining</a>
            </div>

            <div class="card-body border border-dashed border-gray-5 rounded-3 position-relative mb-5 d-flex justify-content-end-between" style="padding-left: 40px;">
                <div class="hstack gap-4"style="margin-left: 40px;">
                    <div class="avatar-image wd-70 ht-70 border border-5 border-gray-3">
                        <p class="mt-3">{{ $poliza->asegurado->parentesco }}</p>
                    </div>
                    <div>
                        <h6 class="fs-14 text-truncate-1-line">{{ $poliza->tercero->nombre }}</h6>
                        <p class="fs-12 fw-normal text-muted">{{ $poliza->seg_asegurado_id }}</p>
                        <div class="fs-12 text-muted"><span class="text-dark fw-medium">Fecha novedad:</span>
                            {{ $poliza->fecha_novedad }}
                        </div>
                    </div>
                    <div class="position-absolute top-0 start-50 translate-middle">
                        <i class="feather-check fs-12 bg-primary text-white p-1 rounded-circle"></i>
                    </div>
                </div>
                <div class="hstack gap-4">
                
                <div class="my-3 my-xxl-4 my-md-3 my-md-2">
                            <div class="fs-20 text-dark"><span class="fw-bold">$41079</span> /
                                <em class="fs-11 fw-medium">Mes</em>
                            </div>
                            <div class="btn btn-light-brand w-25">EXTRA: 0%</div>
                        </div>
                <div class="badge bg-gray-200 text-dark project-mini-card-badge">Información</div>
                </div>
            </div>


            <div class="payment-history mb-5">
                <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0">Grupo Familiar:</h5>
                    <a href="javascript:void(0);" class="btn btn-sm btn-light-brand">Alls History</a>
                </div>
                <div class="table-responsive mb-4 d-block bg-soft-100 border border-dashed border-gray-5 rounded-1">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Cedula</th>
                                <th>Nombre</th>
                                <th>Parentesco</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($grupoFamiliar as $familiar)
                            @if ($familiar->cedula != $poliza->seg_asegurado_id)
                                <tr>
                                    <td><a href="">{{ $familiar->cedula }}</a></td>
                                    <td>{{$familiar->tercero->nombre}}</td>
                                    <td>{{$familiar->parentesco}}</td>
                                    
                                    <td class="hstack justify-content-end gap-4 text-end">
                                        <a data-bs-toggle="tooltip" data-bs-trigger="hover"
                                            data-bs-original-title="Ver Poliza">
                                            <i class="feather feather-send fs-12"></i>
                                        </a>                                    
                                    </td>
                                </tr>
                            @endif                                                 
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>



            <div class="row mb-5">                
                    <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold mb-0">Subscripciones y Planes:</h5>
                    </div>
                
                <div class="col-xxl-6 col-xl-12 col-lg-6">
                    <div class="p-4 mb-4 d-xxl-block d-xl-block d-md-block align-items-center gap-4 border border-dashed border-gray-5 rounded-1">
                        <div>
                            <div class="fs-14 fw-bold text-dark mb-1">Seguro de vida: <a href="javascript:void(0);"
                                    class="badge bg-primary text-white ms-2">{{ $poliza->plan->name}}</a></div>
                            <div class="fs-12 text-muted">{{ $poliza->plan->condicion->descripcion}}</div>
                        </div>
                        <div class="my-3 my-xxl-4 my-md-3 my-md-2">
                            <div class="fs-20 text-dark"><span class="fw-bold">${{$poliza->valor_prima}}</span> /
                                <em class="fs-11 fw-medium">Mes</em>
                            </div>
                            <div class="btn btn-light-brand w-25">EXTRA: {{$poliza->extra_prima}}%</div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6 col-xl-12 col-lg-6">
                    <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold mb-0">Coberturas:</h5>
                        <a href="javascript:void(0);" class="btn btn-sm btn-light-brand">Alls History</a>
                    </div>
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <tbody>
                                    @foreach ($poliza->plan->coberturas as $cobertura)
                                        <tr>
                                            <td><span class="badge bg-soft-success text-success"><i
                                                        class="bi bi-check-circle"></i></span></td>
                                            <td>{{ $cobertura->nombre }}</td>
                                            <td><a href="javascript:void(0);"
                                                    class="fs-14 fw-bold text-dark mb-1">${{ $cobertura->pivot->valorAsegurado }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    
                </div>
            </div>
        </div>
    </div>
</div> --}}
    <div class="card">
        <div class="subscription-plan px-4 pt-4 mb-3">
            <div class="mb-4 d-flex align-items-center justify-content-start gap-4">
                <div class="btn btn-sm btn-light-brand p-3 bg-soft-primary">{{ $poliza->asegurado->parentesco }}</div>
                <div>
                    <h5 class="fw-bold mb-0">{{ $poliza->tercero->nombre }}:</h5>
                    <div class="fs-12 text-muted">{{ $poliza->seg_asegurado_id }}</div>
                </div>
            </div>
            <div class="p-4 mb-4 d-xxl-flex d-xl-block d-md-flex align-items-center justify-content-between gap-4 border border-dashed border-gray-5 rounded-1">
                <div>
                    <div class="fs-14 fw-bold text-dark mb-1">Seguro de vida<a href="javascript:void(0);"
                            class="badge bg-primary text-white ms-2">{{ $poliza->plan->name }}</a></div>
                    <div class="fs-12 text-muted">{{ $poliza->plan->condicion->descripcion }}</div>
                </div>
                <div class="my-3 my-xxl-0 my-md-3 my-md-0">
                    <div class="fs-20 text-dark"><span class="fw-bold">$ {{ number_format($poliza->valor_prima) }}</span> / <em
                            class="fs-11 fw-medium">Mes</em></div>
                    <div class="fs-12 text-muted mt-1"><strong class="text-dark">EXTRA:
                        </strong>{{ $poliza->extra_prima }}%</div>
                </div>
                <div class="hstack gap-3">
                    <a href="javascript:void(0);" class="text-danger">Cancel Plan</a>
                </div>
            </div>
            {{-- <style>
                .flex-container {
                    display: flex;
                    flex-wrap: nowrap;
                    justify-content: space-between;
                    align-items: stretch;
                    height: 200px;
                }

                .flex-item {
                    flex: 1 1 auto;
                    max-width: calc(100% / 7);
                    margin: 5px;
                }
            </style>
            <div class="flex-container">
                @foreach ($poliza->plan->coberturas as $cobertura)
                    <div class="flex-item">
                        <a href="javascript:void(0);" style="height: 100%; align-items: center;"
                            class="p-4 mb-4 d-block bg-soft-100 border border-dashed border-gray-5 rounded-1 position-relative">
                            <div style="height: 60%">
                                <h6 class="fs-13 fw-bold">{{ $cobertura->nombre }}</h6>
                                <p class="fs-12 fw-normal text-muted">Seguros de vida</p>
                            </div>
                            <div class="mt-4"><span
                                    class="fs-16 fw-bold text-dark">${{ $cobertura->pivot->valorAsegurado }}</span>
                                    <p class="fs-12 fw-normal text-muted">Valor asegurado</p>
                            </div>
                            <div class="position-absolute top-0 start-50 translate-middle">
                                <i class="feather-check fs-12 bg-primary text-white p-1 rounded-circle"></i>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div> --}}

            <div class="d-flex flex-wrap justify-content-center" style="height: auto;">
                @foreach ($poliza->plan->coberturas as $cobertura)
                    <a href="javascript:void(0);"
                        class="d-flex flex-column align-items-center justify-content-between text-center p-4 mb-4 bg-soft-100 border border-dashed border-gray-5 rounded-1 position-relative"
                        style="flex: 1 1 calc(100% / 7); max-width: calc(100% / 7); min-width: 150px; margin: 5px;">
                        <div style="height: 60%;">
                            <h6 class="fs-13 fw-bold">{{ $cobertura->nombre }}</h6>
                            <p class="fs-12 fw-normal text-muted">Seguros de vida</p>
                        </div>
                        <div class="mt-4">
                            <span class="fs-16 fw-bold text-dark">$ {{ number_format($cobertura->pivot->valorAsegurado) }}</span>
                            <p class="fs-12 fw-normal text-muted">Valor asegurado</p>
                        </div>
                        <div class="position-absolute top-0 start-50 translate-middle">
                            <i class="feather-check fs-12 bg-primary text-white p-1 rounded-circle"></i>
                        </div>
                    </a>
                @endforeach
            </div>

        </div>
        <hr class="mt-2">
        <div class="payment-history">
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Grupo Familiar:</h5>                
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
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($grupoFamiliar as $familiar)
                            @if ($familiar->cedula != $poliza->seg_asegurado_id)
                                <tr>
                                    <td><a href="javascript:void(0);">{{ $familiar->cedula }}</a></td>
                                    <td>{{ $familiar->tercero->nombre }}</td>
                                    <td>{{ $familiar->parentesco }}</td>
                                    <td><span class="badge bg-soft-warning text-warning">{{ $familiar->polizas->first()->plan->name}}</span></td>
                                    <td>$ {{ number_format($familiar->polizas->first()->plan->valor, 2) }}</td>
                                    <td class="hstack justify-content-end gap-4 text-end">
                                        {{-- <a data-bs-toggle="tooltip" data-bs-trigger="hover"
                                            data-bs-original-title="Ver Poliza"
                                            href="{{ route('seguros.poliza.show', ['poliza' => $familiar->cedula]) }}?id={{ $familiar->cedula }}">
                                            <i class="feather feather-send fs-12"></i>
                                        </a> --}}
                                        <form action="{{ route('seguros.poliza.show', ['poliza' => 'ID']) }}"
                                            method="GET">
                                            <div data-bs-toggle="tooltip" data-bs-trigger="hover"
                                                data-bs-original-title="Ver Poliza">
                                                <input type="hidden" name="id" id="valueCedula"
                                                    value="{{ $familiar->cedula }}">
                                                <button class="w-50 btn">
                                                    <i class="feather feather-send fs-12"></i>
                                                    <span></span>
                                                </button>
                                            </div>
                                        </form>

                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 mb-4 d-xxl-flex d-xl-block d-md-flex align-items-center justify-content-end gap-4">                
                <div class="hstack gap-3">
                    <a href="javascript:void(0);" class="text-bold">Valor total</a>
                    <a href="javascript:void(0);" class="btn btn-light-brand bg-soft-primary">$ {{ number_format($totalPrima)}}</a>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
