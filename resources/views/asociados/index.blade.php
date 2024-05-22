<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>
        Asociados
    </title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no'
        name='viewport' />

    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css"
        integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../assets/css/now-ui-dashboard.css?v=1.5.0" rel="stylesheet" />

    <link href="../assets/demo/demo.css" rel="stylesheet" />

    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css"
        integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    {{-- <link rel="stylesheet" href="{{ asset('assets/css/pruebacss.css') }}"> --}}
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/now-ui-dashboard.css?v=1.5.0') }}" rel="stylesheet">
    <link href="{{ asset('assets/demo/demo.css') }}" rel="stylesheet">
</head>

<body>
    <div class="wrapper ">
        @include('navbar')

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
                        <a class="navbar-brand" href="#">Asociados</a>
                    </div>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation"
                        aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-bar navbar-kebab"></span>
                        <span class="navbar-toggler-bar navbar-kebab"></span>
                        <span class="navbar-toggler-bar navbar-kebab"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end" id="navigation">

                        {{-- Buscador Cedulaaaa --}}
                        <form action="{{ route('asociados.show', ['asociado' => 'ID']) }}" method="GET">
                            <div class="input-group no-border">
                                <input type="text" name="id" value="" class="form-control"
                                    placeholder="Search...">
                                <div class="input-group-append">
                                    <button class="input-group-text" type="submit">
                                        <i class="now-ui-icons ui-1_zoom-bold"></i>
                                    </button>
                                </div>
                            </div>
                        </form>

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
                                                        <td><button type="button"
                                                                class="btn btn-success py-1">ACTIVO</button></td>
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

                                    @for ($i = $start; $i <= $end; $i++)
                                        <li class="page-item {{ $asociados->currentPage() == $i ? 'active' : '' }}">
                                            <a class="page-link"
                                                href="{{ $asociados->url($i) }}">{{ $i }}</a>
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
                        <button class="btn btn-primary">Agregar Cliente</button>
                    </div>
                </div>
            </div>
        </div>
        @include('footer')
</body>

</html>

