<x-base-layout>
    <div class="col-12">
        <x-warning />
        <div class="card stretch stretch-full">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6">                    
                        <form method="GET" class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                            <label for="search-input" class="mb-0 me-2">Buscar:</label>
                            <input type="text" name="id" class="form-control form-control-sm"
                                placeholder="nombre titular" aria-controls="customerList">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                        </form>
                    </div>
                    <div class="col-sm-12 col-md-6 d-flex justify-content-end align-items-center">
                        <form action="{{ route('cinco.tercero.show', ['tercero' => 'id']) }}" method="GET" class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                            <label for="search-input" class="mb-0 me-2">Buscar:</label>
                            <input type="text" name="id" class="form-control form-control-sm" placeholder="cédula titular" aria-controls="customerList">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-12">
        <div class="card stratch">
            <div class="card-header">
                <h5 class="card-title">Listado Terceros</h5>
                <div class="card-header-action">
                    <div class="card-header-btn">
                        <div data-bs-toggle="tooltip" title="Delete">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger"
                                data-bs-toggle="remove"> </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="Refresh">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning"
                                data-bs-toggle="refresh"> </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="Maximize/Minimize">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success"
                                data-bs-toggle="expand"> </a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown"
                            data-bs-offset="25, 25">
                            <div data-bs-toggle="tooltip" title="Options">
                                <i class="feather-more-vertical"></i>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item"><i class="feather-at-sign"></i>New</a>
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="feather-calendar"></i>Event</a>
                            <a href="javascript:void(0);" class="dropdown-item"><i class="feather-bell"></i>Snoozed</a>
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="feather-trash-2"></i>Deleted</a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="feather-settings"></i>Settings</a>
                            <a href="javascript:void(0);" class="dropdown-item"><i class="feather-life-buoy"></i>Tips &
                                Tricks</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body custom-card-action p-0">
                <div class="table-responsive project-report-table">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Tercero</th>
                                <th scope="col">Fecha Ingreso</th>
                                <th scope="col">Fecha Primer Aporte</th>
                                <th scope="col">Fecha Ministerio</th>
                                <th scope="col" class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($terceros as $tercero)
                            <tr>
                                <td>
                                    <div class="hstack gap-4">
                                        
                                                <i class="bi bi-person-circle"></i>
                                        
                                        <div>
                                            <div class="fw-bold text-dark">{{$tercero->Nom_Ter}}</div>
                                            <div class="fs-12 text-muted">Cedula: 
                                            <a class="badge bg-soft-primary text-primary">{{$tercero->Cod_Ter}}</a></div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{$tercero->Fec_Ing}}</td>
                                <td>{{$tercero->Fec_Aport}}</td>
                                <td>{{$tercero->Fec_Minis}}</td>                                
                                <td>
                                    <a href="{{ route('cinco.tercero.show', ['tercero' => $tercero->Cod_Ter]) }}" class="avatar-text avatar-md ms-auto"><i
                                            class="feather-arrow-right"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <ul class="list-unstyled d-flex align-items-center gap-2 mb-0 pagination-common-style">
                    <li>
                        <a href="javascript:void(0);"><i class="bi bi-arrow-left"></i></a>
                    </li>
                    <li><a href="javascript:void(0);" class="active">1</a></li>
                    <li><a href="javascript:void(0);">2</a></li>
                    <li>
                        <a href="javascript:void(0);"><i class="bi bi-dot"></i></a>
                    </li>
                    <li><a href="javascript:void(0);">8</a></li>
                    <li><a href="javascript:void(0);">9</a></li>
                    <li>
                        <a href="javascript:void(0);"><i class="bi bi-arrow-right"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</x-base-layout>
