<x-base-layout>
    @section('titlepage', 'Lista Créditos')
    <div class="col-12">
        <div class="card">
            <div class="card-body py-1 m-0">
                <div class="row">
                    <div class="col-sm-12 col-md-7">
                        <div class="content-area ps ps--active-y" style="height: 100px;">
                            <div class="content-area-header bg-white sticky-top m-0 p-0">
                                <div class="page-header-right ms-auto justify-content-end m-0 pt-4">
                                    <div class="hstack gap-2 m-0">
                                        <p class="fs-12 fw-bold text-dark pt-3">Buscar por nombre: </p>
                                        <div class="hstack">
                                            <a href="" class="search-form-open-toggle">
                                                <div class="avatar-text avatar-md" data-bs-toggle="tooltip"
                                                    data-bs-trigger="hover" title="" data-bs-original-title="Search">
                                                    <i class="feather-search"></i>
                                                </div>
                                            </a>
                                            <form action="" method="GET"
                                                class="search-form" style="display: none">
                                                <div class="search-form-inner pt-4">
                                                    <a href="" class="search-form-close-toggle">
                                                        <div class="avatar-text avatar-md" data-bs-toggle="tooltip"
                                                            data-bs-trigger="hover" title=""
                                                            data-bs-original-title="Back">
                                                            <i class="feather-arrow-left"></i>
                                                        </div>
                                                    </a>
                                                    <input type="search" name="id" class="px-0 border-0 w-100" placeholder="Buscar por nombre..." autocomplete="off">
                                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-5 d-flex justify-content-end align-items-center">
                        <x-input-search-creditos></x-input-search-creditos>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-12">
    <div class="card stratch">
        <div class="card-header">
            <h5 class="card-title">{{$creditos->first()->estado->nombre}} - {{$creditos->first()->estado->etapa->nombre}}</h5>
            <a href="" class="d-flex me-1 btn btn-primary"><i class="feather-plus me-2"></i><span>Crear una Solicitud</span>
            </a>
        </div>
        <div class="card-body custom-card-action p-0">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Titular</th>
                            <th>Linea de Crédito</th>
                            <th>Valor Asegurado</th>
                            <th>Valor a Pagar</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($creditos as $c)
                            <tr>
                                <td><a href="" class="hstack gap-3">
                                        <i class="bi bi-person-circle"></i>
                                        <div>
                                            <span class="text-truncate-1-line">{{$c->mae_terceros_cod_ter}}</span>
                                            <small class="fs-12 fw-normal text-muted">{{$c->tercero->nom_ter}}</small>
                                        </div>
                                    </a></td>
                                <td class="text-primary">{{ strtoupper($c->lineaCredito->nombre)}} 
                                <span class="badge bg-gray-200 text-dark">{{$c->lineaCredito->tipoCredito->nombre}}</span>
                                </td>
                                <td class="fw-bold text-dark">$
                                    
                                </td>
                                <td>
                                    
                                </td>                                
                               
                                    <td class="text-end">
                                        <div class="hstack gap-2 justify-content-end">
                                            <a href="" class="avatar-text avatar-md" data-bs-toggle="tooltip" title="" data-bs-original-title="Ir al credito">
                                                <i class="feather-arrow-right"></i>
                                            </a>
                                        </div>
                                    </td>
                               
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $creditos->links() }}
        </div>
    </div>
</div>
</x-base-layout>