<x-base-layout>
    @section('titlepage', 'Filtro por nombre')
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
                                                    data-bs-trigger="hover" title=""
                                                    data-bs-original-title="Search">
                                                    <i class="feather-search"></i>
                                                </div>
                                            </a>
                                            <form action="{{ route('poliza.search', ['name' => 'ID']) }}" method="GET"
                                                class="search-form" style="display: none">
                                                <div class="search-form-inner pt-4">
                                                    <a href="" class="search-form-close-toggle">
                                                        <div class="avatar-text avatar-md" data-bs-toggle="tooltip"
                                                            data-bs-trigger="hover" title=""
                                                            data-bs-original-title="Back">
                                                            <i class="feather-arrow-left"></i>
                                                        </div>
                                                    </a>
                                                    <input type="search" name="id"
                                                        class="px-0 border-0 w-100"
                                                        placeholder="Buscar por nombre..." autocomplete="off">
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
                        <x-input-search-seguros></x-input-search-seguros>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <div class="col-xxl-12">
        <div class="card stratch">
            <div class="card-header">
                <h5 class="card-title">Listado Asegurados</h5>
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
                            <a href="javascript:void(0);" class="dropdown-item"><i class="feather-life-buoy"></i>Tips
                                &
                                Tricks</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body custom-card-action p-0">
                <div class="table-responsive">
                    <table class="table table-hover" id="customerList">
                        <thead>
                            <tr>
                                <th scope="col">Asegurado</th>
                                <th scope="col">Parentesco</th>
                                <th scope="col">Valor Asegurado</th>
                                <th scope="col">Valor Prima</th>
                                <th scope="col">Valor Prima Pagar</th>                                
                                <th scope="col" class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>                        
                            @foreach ($asegurados as $a)
                                <tr>
                                    <td>
                                        <div class="hstack gap-4">
                                            <i class="bi bi-person-circle"></i>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $a->tercero->nombre }}</div>
                                                <div class="fs-12 text-muted">Cédula:
                                                    <a class="badge bg-soft-primary text-primary">{{ $a->cedula }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-gray-200 text-dark">{{ $a->parentesco }}</span>
                                    </td>
                                    <td>$ {{ number_format($a->polizas->first()->valor_asegurado ?? '') }}</td>
                                    <td>$ {{ number_format($a->polizas->first()->valor_prima) ?? '' }}</td>
                                    <td>$ {{ number_format($a->polizas->first()->primapagar) ?? '' }}</td>
                                    <td>
                                        <form action="{{ route('seguros.poliza.show',['poliza' => 'ID']) }}" class="avatar-text avatar-md ms-auto" method="GET">
                                            <input type="hidden" name="id" value="{{ $a->cedula }}">
                                            <button type="submit" class="avatar-text avatar-md ms-auto"><i class="feather-arrow-right"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
