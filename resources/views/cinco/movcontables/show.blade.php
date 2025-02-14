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
                            <div class="fs-4 fw-bold text-dark"><span class="counter">{{ $movimientos->first()->tercero->Nom_Ter ?? 'Identificar Nombre' }}</span></div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">
                                {{ $id }}</h3>
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
                <h5 class="card-title">Movimientos contables</h5>
                <a href="{{ route('cinco.reportepdf', ['id' =>  $id ]) }}" class="btn btn-md bg-soft-teal text-teal border-soft-teal">
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
                                    aria-controls="flush-collapse{{ $i }}">{{$c->cuenta }}  <span class="fw-normal text-muted"> _ {{$c->cuentaContable->Descripcion}}</span></button>
                            </h2>
                            <div id="flush-collapse{{ $i }}" class="accordion-collapse collapse"
                                aria-labelledby="flush-heading{{ $i }}" data-bs-parent="#accordionFaqGroup"
                                style="">
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
                                                <td><span class=fw-bold>Débitos: </span>$ {{ number_format($contadordebitos) }}</td>
                                                <td><span class=fw-bold>Créditos: </span>$ {{ number_format($contadorcreditos)}}</td>
                                            <tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</x-base-layout>
