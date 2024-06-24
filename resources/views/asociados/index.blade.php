<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <x-component-header />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="wrapper ">
        @include('layouts.navbar')
        <div class="main-panel" id="main-panel">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-transparent  bg-primary  navbar-absolute">
                <div class="container-fluid">
                    <div class="navbar-wrapper">
                        <div class="navbar-toggle">
                            <button type="button" class="navbar-toggler">
                                <span class="navbar-toggler-bar bar1"></span>
                                <span class="navbar-toggler-bar bar2"></span>
                                <span class="navbar-toggler-bar bar3"></span>
                            </button>
                        </div>
                        <a class="navbar-brand" href="#">Titular Cliente</a>
                    </div>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-bar navbar-kebab"></span>
                        <span class="navbar-toggler-bar navbar-kebab"></span>
                        <span class="navbar-toggler-bar navbar-kebab"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end" id="navigation">

                    <x-input-search></x-input-search>

                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="/user/profile">
                                    <i class="now-ui-icons users_single-02"></i>
                                    <p>
                                        <span class="d-lg-none d-md-block">Account</span>
                                    </p>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- End Navbar -->
            <div class="panel-header panel-header-sm">
            </div>


            {{-- Tabla --}}
            <div class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped h-100">
                                        <thead class=" text-primary">
                                            <th>Cédula</th>
                                            <th>Nombre</th>
                                            <th>Distrito</th>
                                            <th>Ciudad</th>
                                            {{-- <th>Dirección</th> --}}
                                            <th>Estado</th>
                                            <th>Celular</th>
                                            {{-- <th>Email</th>
                                            <th>Observación Familia</th>
                                            <th>Observación</th>
                                            <th class="text-right">
                                                Salary
                                            </th> --}}
                                        </thead>
                                        <tbody>
                                            @foreach ($asociados as $asociado)
                                            <tr>
                                                <td>{{ $asociado->cedula }}</td>
                                                <td>{{ $asociado->apellido . ' ' . $asociado->nombre }}</td>

                                                @if ($asociado->distrito_id !== 0)
                                                <td>{{ $asociado->distrito->id }}</td>
                                                @else
                                                <td></td>
                                                @endif

                                                @if ($asociado->ciudad_id !== null && $asociado->ciudad_id !== 0)
                                                <td>{{$asociado->ciudade->nombre}}
                                                </td>
                                                @else
                                                <td></td>
                                                @endif

                                                {{-- <td>{{ $asociado->direccion }}</td> --}}

                                                @if ($asociado->estado)
                                                <td><button type="button" class="btn btn-success py-1">ACTIVO</button></td>
                                                @else
                                                <td></td>
                                                @endif

                                                <td>{{ $asociado->celular }}</td>
                                                {{-- <td>{{ $asociado->email }}</td>
                                                <td>{{ $asociado->observacion }}</td>
                                                <td>{{ $asociado->observacion_familia }}</td> --}}
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <nav aria-label="Page navigation example">
                                <ul class="pagination justify-content-center">

                                    <li class="page-item {{ $asociados->previousPageUrl() ? '' : 'disabled' }}">

                                        <a class="page-link" href="{{ $asociados->previousPageUrl() }}">
                                            <i class="now-ui-icons arrows-1_minimal-left"></i>
                                        </a>
                                    </li>
                                    @php
                                    $start = max(1, $asociados->currentPage() - 1);
                                    $end = min($start + 2, $asociados->lastPage());
                                    @endphp

                                    @for ($i = $start; $i <= $end; $i++) <li class="page-item {{ $asociados->currentPage() == $i ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $asociados->url($i) }}">{{ $i }}</a>
                                        </li>
                                        @endfor

                                        <li class="page-item {{ $asociados->nextPageUrl() ? '' : 'disabled' }}">
                                            <a class="page-link" href="{{ $asociados->nextPageUrl() }}">
                                                <i class="now-ui-icons arrows-1_minimal-right"></i>
                                            </a>
                                        </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalVerCedula">
                            Agregar Cliente
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer')

        {{-- Modal Filtro Cedula --}}
        <div class="modal fade" id="ModalVerCedula" tabindex="-1" aria-labelledby="ModalVerCedula" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalInsert">Next</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal No se añade Titular --}}
        <div class="modal fade" id="ModalVerCedula" tabindex="-1" aria-labelledby="ModalVerCedula" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        No se añadio el titular
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalInsert">Next</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal agregar Titular --}}
        <div class="modal fade" id="ModalInsert" tabindex="-1" aria-labelledby="ModalInsertLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ModalInsertLabel">Agregar Cliente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="row">
                                <div class="col-md-4 pr-1">
                                    <div class="form-group">
                                        <label>Cedula</label>
                                        <input type="number" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4 px-1">
                                    <div class="form-group">
                                        <label>Apellidos</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4 pl-1">
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 pr-1">
                                    <div class="form-group">
                                        <label>Parentesco</label>
                                        <select class="form-control" name="parentesco" aria-label="Default select example" id="selectParentesco"></select>
                                    </div>
                                </div>
                                <div class="col-md-4 px-1">
                                    <div class="form-group">
                                        <label>Distrito</label>
                                        <input type="number" max="99" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4 pl-1">
                                    <div class="form-group">
                                        <label>Cuidad</label>
                                        <select class="form-control" name="parentesco" aria-label="Default select example" id="selectDistrito"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-7 pr-1">
                                    <div class="form-group">
                                        <label>Dirección</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-5 pl-1">
                                    <div class="form-group">
                                        <label>Fecha Nacimiento</label>
                                        <input type="date" class="form-control">
                                    </div>
                                </div>
                                                            
                            </div>
                            <div class="row">                                
                                <div class="col-md-5 pr-1">
                                    <div class="form-group">
                                        <label>Celular</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-7 pl-1">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Observación</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
</body>

</html>