<x-base-layout>
    @section('titlepage', 'Movimientos Contables')
    <x-success />
    <x-warning />
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
                <div class="mb-4 mb-lg-0">
                    <div class="d-flex gap-4 align-items-center">
                        <div class="avatar-text avatar-lg bg-gray-200">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold text-dark"><span
                                    class="counter">{{ $movimientos->first()->tercero?->Nom_Ter ?? $fechas->nom_ter }}</span>
                            </div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">{{ $id }}</h3>
                        </div>
                        
                        @if ($tercero != null && $tercero->verificado)
                        <div class="d-flex gap-3 align-items-center">                            
                            <div>
                                <div class="fw-semibold text-dark"><span class="badge bg-soft-success text-success">VERIFICADO</span> {{$tercero->verificadousuario}}</div>
                                <div class="fs-12 text-muted">{{$tercero->observacion}}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 d-flex justify-content-end align-items-center">
                    <form action="{{ route('cinco.movcontables.show', ['cinco' => 'ID']) }}" method="GET"
                        class="d-flex align-items-center gap-2 page-header-right-items-wrapper" validate>
                        <label class="mb-0 me-2">Buscar:</label>
                        <input type="text" name="id" class="form-control form-control-sm" placeholder="número de cédula"
                            aria-controls="customerList" required>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <a href="{{ route('cinco.tercero.show', ['tercero' => $id, 'id' => $id]) }}"
                            class="avatar-text avatar-lg bg-soft-primary text-primary border-soft-primary rounded"
                            data-bs-toggle="tooltip" title="Actualizar Fecha" data-bs-original-title="Update">
                            <i class="feather-user-plus"></i>
                        </a>
                        <div class="text-end">
                            <p class="fs-11 fw-medium text-uppercase text-muted mb-1">Fecha Ingreso a Corpentunida</p>
                            <h5>{{$fechas->fecha_ipuc->format('Y-m-d') ?? 'sin fecha'}}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <a href="{{ route('cinco.tercero.show', ['tercero' => $id, 'id' => $id]) }}"
                            class="avatar-text avatar-lg bg-soft-primary text-primary border-soft-primary rounded"
                            data-bs-toggle="tooltip" title="Actualizar Fecha" data-bs-original-title="Update">
                            <i class="feather-bar-chart-2"></i>
                        </a>
                        <div class="text-end">
                            <p class="fs-11 fw-medium text-uppercase text-muted mb-1">Fecha Primer Aporte</p>
                            <h5>{{$fechas->fec_aport->format('Y-m-d') ?? 'sin fecha'}}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <a href="{{ route('cinco.tercero.show', ['tercero' => $id, 'id' => $id]) }}"
                            class="avatar-text avatar-lg bg-soft-primary text-primary border-soft-primary rounded"
                            data-bs-toggle="tooltip" title="Actualizar Fecha" data-bs-original-title="Update">
                            <i class="feather-briefcase"></i>
                        </a>
                        <div class="text-end">
                            <p class="fs-11 fw-medium text-uppercase text-muted mb-1">Fecha Ingreso al Ministerio</p>
                            <h5>{{$fechas->fec_minis->format('Y-m-d') ?? 'sin fecha'}}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Movimientos contables</h5>
                <a href="{{ route('cinco.reportepdf', ['id' => $id]) }}"
                    class="btn btn-md bg-soft-teal text-teal border-soft-teal">
                    <i class="feather-plus me-2"></i>
                    <span>Imprimir Reporte</span>
                </a>
            </div>
            <div class="card-body">
                <div class="accordion proposal-faq-accordion" id="accordionFaqGroup">
                    @foreach ($cuentasAgrupadas as $i => $c)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapse{{ $i }}" aria-expanded="false"
                                    aria-controls="flush-collapse{{ $i }}">{{ $c->cuenta }} <span
                                        class="fw-normal text-muted"> _
                                        {{ $c->cuentaContable->Descripcion }}</span></button>
                            </h2>
                            <div id="flush-collapse{{ $i }}" class="accordion-collapse collapse"
                                aria-labelledby="flush-heading{{ $i }}" data-bs-parent="#accordionFaqGroup" style="">
                                <div class="accordion-body">
                                    <table class="table table-responsive table-hover">
                                        <thead>
                                            <tr>
                                                <th>Comprobante</th>
                                                <th>Número</th>
                                                <th>Fecha</th>
                                                <th>Observacion</th>
                                                <th>Debitos</th>
                                                <th>Creditos</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $contadorcreditos = 0;
                                                $contadordebitos = 0;
                                            @endphp

                                            @foreach ($movimientos as $mov)
                                                @if ($mov->Cuenta == $c->cuenta)
                                                    <tr>
                                                        <td>{{ $mov->CodComprob }}</td>
                                                        <td>{{ $mov->NumComprob }}</td>
                                                        <td>{{ $mov->Fecha }}</td>
                                                        <td>{{ $mov->Observacion }}</td>
                                                        <td>{{ number_format($mov->VrDebitos) }}</td>
                                                        <td>{{ number_format($mov->VrCreditos) }}</td>
                                                    </tr>
                                                    @php
                                                        $contadorcreditos += $mov->VrCreditos;
                                                        $contadordebitos += $mov->VrDebitos;
                                                    @endphp
                                                @endif
                                            @endforeach
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td><span class=fw-bold>Débitos: </span>$
                                                    {{ number_format($contadordebitos) }}
                                                </td>
                                                <td><span class=fw-bold>Créditos: </span>$
                                                    {{ number_format($contadorcreditos) }}
                                                </td>
                                            <tr>
                                        </tbody>
                                    </table>
                                </div>
                                @if (!empty($faltantes) && $c->cuenta == '416542')
                                    <div class="mx-4">
                                        <h5>Fecha de aportes faltantes:</h5>
                                        <span class="fw-normal text-muted">
                                            @foreach ($faltantes as $f)
                                                <li>{{ $f }}</li>
                                            @endforeach
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-base-layout>