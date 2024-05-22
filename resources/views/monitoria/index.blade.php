<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/now-ui-dashboard.css?v=1.5.0') }}" rel="stylesheet">
    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link href="{{ asset('assets/demo/demo.css') }}" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        td,
        th {
            font-size: 12px;
        }
    </style>
</head>

<body>
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
                    <a class="navbar-brand" href="#">MONITORIA</a>
                </div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-bar navbar-kebab"></span>
                    <span class="navbar-toggler-bar navbar-kebab"></span>
                    <span class="navbar-toggler-bar navbar-kebab"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navigation">

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


        <div class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <form id="formFiltroMes">
                                        <select name="months" class="form-select" id="selectMes">
                                            <option value="5">Mayo</option>
                                            <option value="4">Abril</option>
                                            <option value="3">Marzo</option>
                                            <option value="2">Febrero</option>
                                            <option value="1">Enero</option>
                                        </select>
                                    </form>
                                </div>
                                <div class="col-md-2">
                                    <a href="/exportar-datos" class="btn btn-secondary">Exportar Datos</a>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('monitoria.generarpdf') }}" target="_blank" class="btn btn-primary">Generar PDF</a>                                    
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tr id="encabezado">
                                        <th class="text-primary" style="width: 100px;">Fecha</th>
                                        <th class="text-primary">Cedula Titular</th>
                                        <th class="text-primary">Nombre Titular</th>
                                        <th class="text-primary">Nombre Fallecido</th>
                                        <th class="text-primary">Lugar Fallecimiento</th>
                                        <th class="text-primary">Parentesco</th>
                                        <th class="text-primary">Traslado</th>
                                        <th class="text-primary">Contacto</th>
                                        <th class="text-primary">Tel. Contacto</th>
                                        <th class="text-primary">Contacto</th>
                                        <th class="text-primary">Tel. Contacto</th>
                                        <th class="text-primary">Factura</th>
                                        <th class="text-primary">Valor</th>
                                    </tr>
                                    <tbody id="datosFiltro">

                                    </tbody>
                                </table>
                                <table id="Tabla" class="table">


                                    <tr>
                                        <th class="text-primary" style="width: 100px;">Fecha</th>
                                        <th class="text-primary">Cedula Titular</th>
                                        <th class="text-primary">Nombre Titular</th>
                                        <th class="text-primary">Nombre Fallecido</th>
                                        <th class="text-primary">Lugar Fallecimiento</th>
                                        <th class="text-primary">Parentesco</th>
                                        <th class="text-primary">Traslado</th>
                                        <th class="text-primary">Contacto</th>
                                        <th class="text-primary">Tel. Contacto</th>
                                        <th class="text-primary">Contacto</th>
                                        <th class="text-primary">Tel. Contacto</th>
                                        <th class="text-primary">Factura</th>
                                        <th class="text-primary">Valor</th>
                                    </tr>

                                    @foreach ($registros as $reg)
                                    <tr>                                        
                                        <td>
                                            <!-- <p style="background-color: aqua;" class="width: 100px;">{{ $reg->fechaRegistro }}</p> -->
                                            <p>{{ $reg->fechaFallecimiento }}</p>
                                            <p>{{ $reg->horaFallecimiento }}</p>
                                            @php
                                            Carbon\Carbon::setLocale('es');
                                            $dia = Carbon\Carbon::parse($reg->fechaFallecimiento)->translatedFormat('l');
                                            @endphp
                                            <p>{{ $dia }}</p>
                                        </td>
                                        <td>{{ $reg->cedulaTitular }}</td>
                                        <td>{{ $reg->asociado->apellido . " " . $reg->asociado->nombre}}</td>
                                        <td>{{ $reg->beneficiario->nombre }}</td>
                                        <td>{{ $reg->lugarFallecimiento }}</td>
                                        <td>{{ $reg->parentescoo->nomPar}}</td>
                                        @if( $reg->traslado)
                                        <td>SI</td>
                                        @else
                                        <td>NO</td>
                                        @endif
                                        <td>{{ $reg->contacto }}</td>
                                        <td>{{ $reg->telefonoContacto }}</td>
                                        <td>{{ $reg->Contacto2 }}</td>
                                        <td>{{ $reg->telefonoContacto2 }}</td>
                                        <td>{{ $reg->factura }}</td>
                                        <td>{{ $reg->valor }}</td>
                                    </tr>
                                    @endforeach

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(function() {
                    $('#encabezado').hide();
                    $('#selectMes').change(function() {
                        $('#Tabla').hide();
                        var valorSeleccionado = $(this).val();
                        $.ajax({
                            type: 'GET',
                            url: '/mes/' + valorSeleccionado,
                            success: function(response) {
                                $('#encabezado').show();
                                $('#datosFiltro').empty();
                                response.forEach(function(registro) {                                    
                                    var fila = $('<tr>');
                                    fila.append('<td>' + '<p>' + registro.fechaFallecimiento + '</p>' + 
                                    '<p>' + registro.horaFallecimiento + '</p>' + 
                                    '<p>' +  + '</p>' + '</td>');
                                    fila.append('<td>' + registro.cedulaTitular + '</td>');
                                    fila.append('<td>' + registro.nombreTitular + '</td>');
                                    fila.append('<td>' + registro.nombreFallecido + '</td>');
                                    fila.append('<td>' + registro.lugarFallecimiento + '</td>');
                                    fila.append('<td>' + registro.parentesco + '</td>');
                                    fila.append('<td>' + registro.traslado + '</td>');
                                    fila.append('<td>' + registro.contacto + '</td>');
                                    fila.append('<td>' + registro.telefonoContacto + '</td>');
                                    fila.append('<td>' + registro.contacto2 + '</td>');
                                    fila.append('<td>' + registro.telefonoContacto2 + '</td>');
                                    fila.append('<td>' + registro.factura + '</td>');
                                    fila.append('<td>' + registro.valor + '</td>');
                                    $('#datosFiltro').append(fila);
                                });

                                console.log(response)
                            },
                            error: function(xhr) {
                                console.log(xhr)
                            }
                        });
                    });
                });
            </script>
        </div>
    </div>
</body>

</html>