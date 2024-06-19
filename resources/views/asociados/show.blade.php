<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <x-component-header />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

    

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
                    <a class="navbar-brand" href="#pablo">ASOCIADO - BENEFICIARIOS</a>
                </div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-bar navbar-kebab"></span>
                    <span class="navbar-toggler-bar navbar-kebab"></span>
                    <span class="navbar-toggler-bar navbar-kebab"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navigation">
                    {{-- Buscador Cedulaaaa --}}
                    <form action="{{ route('asociados.show', ['asociado' => 'ID']) }}" method="GET">
                        <div class="input-group no-border">
                            <input type="text" name="id" value="" class="form-control" placeholder="Search...">
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
        <div class="panel-header panel-header-sm">
        </div>

        <div class="content">
            <div class="row">
                <div class="col-md-11 ml-auto mr-auto">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="title">Información del Titular</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2 pr-1">
                                    <div class="form-group">
                                        <label for="cedula">Cedula</label>
                                        <div class="form-control">{{ $asociado->cedula }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4 px-1">
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <div class="form-control">{{ $asociado->apellido . ' ' . $asociado->nombre }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <form id="FormFechaUpdate" method="POST">
                                        <div class="form-group">
                                            <label for="fechaNacimiento" class="form-label">Fecha Nacimiento:</label>
                                            <div class="input-group">
                                                <input class="form-control" id="fechaInput" type="date" name="fechaNacimiento">
                                                <button class="btn form-control m-0 p-0" data-bs-toggle="modal" data-bs-target="#exampleModal" type="submit">Actualizar</button>
                                            </div>
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    var fechaDesdeBD = "{{ $asociado->fechaNacimiento }}";
                                                    document.getElementById('fechaInput').value = fechaDesdeBD;
                                                });
                                            </script>
                                        </div>
                                    </form>
                                </div>

                                <div class="col-md-2 pl-1">
                                    <div class="form-group">
                                        @php
                                        $fecNac = new DateTime($asociado->fechaNacimiento);
                                        $fechaActual = new DateTime();
                                        $diferencia = $fecNac->diff($fechaActual);
                                        $edad = $diferencia->y;
                                        @endphp
                                        <label>Edad</label>
                                        <div class="form-control">{{ $edad }}</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2 pr-1">
                                        <div class="form-group">
                                            <label>Distrito</label>

                                            @if ($asociado->distrito !== null && $asociado->distrito !== 0)
                                            <div class="form-control">{{ $asociado->distrito->id }}</div>
                                            @else
                                            <div class="form-control">{{ $asociado->distrito }}</div>
                                            @endif

                                            <label>Dirección</label>
                                            <div class="form-control">{{ $asociado->direccion }}</div>

                                            <label>Teléfono</label>
                                            <div class="form-control">{{ $asociado->celular }}</div>

                                            <label>Ciudad</label>
                                            @if ($asociado->ciudad_id !== null && $asociado->ciudad_id !== 0)
                                            <div class="form-control">{{ $asociado->ciudade->nombre }}</div>
                                            @else
                                            <div class="form-control">{{ $asociado->ciudade }}</div>
                                            @endif
                                            <label>Email</label>
                                            <div class="form-control">
                                                {{ $asociado->email }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <label>Observaciones</label>
                                        <p class="border border-secondary-subtle p-3 rounded">
                                            {{ $asociado->observacion_familia }}
                                        </p>

                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-category">Beneficiarios</h5>
                                                <div class="card-body">
                                                    <form method="POST" id="fechasBeneficiarios">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="table-responsive">
                                                            <table class="table">
                                                                <tr>
                                                                    <th>Cédula</th>
                                                                    <th>Apellidos Nombres</th>
                                                                    <th>Parentesco</th>
                                                                    <th>Fecha Nacimiento</th>
                                                                    <th>Edad</th>
                                                                    <th> </th>
                                                                </tr>

                                                                @foreach ($beneficiarios as $beneficiario)
                                                                @if($beneficiario->estado)
                                                                <tr>
                                                                    <td>{{ $beneficiario->cedula }}</td>
                                                                    <td>{{ $beneficiario->apellido . $beneficiario->nombre }}
                                                                    </td>

                                                                    @if ($beneficiario->parentescoo !== null)
                                                                    <td>{{ $beneficiario->parentescoo->nomPar }}
                                                                    </td>
                                                                    @else
                                                                    <td>{{ $beneficiario->parentesco }}</td>
                                                                    @endif


                                                                    <input type="hidden" name="ids[]" value="{{ $beneficiario->cedula }}">
                                                                    <td><input type="date" name="fechas[]" class="form-control" value="{{ $beneficiario->fechaNacimiento }}">
                                                                    </td>
                                                                    @php
                                                                    $fecNac = new DateTime($beneficiario->fechaNacimiento);
                                                                    $fechaActual = new DateTime();
                                                                    $diferencia = $fecNac->diff($fechaActual);
                                                                    $edad = $diferencia->y;
                                                                    @endphp

                                                                    <td>{{ $edad }}</td>
                                                                    <td>
                                                                        <a class="btn btn-success p-2 botonPrestarServicio" data-bs-toggle="modal" data-bs-target="#exampleModalServicio">
                                                                            Prestar Servicio
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                @endif
                                                                @endforeach
                                                            </table>
                                                        </div>

                                                        <div class="d-flex">
                                                            <button class="btn btn-secondary m-3 p-2" type="submit">Actualizar fechas</button>
                                                            <button type="button" class="btn btn-primary m-3 p-2" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                                                                Agregar Beneficiario
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                                @if(session('PrestarServicio'))
                                                <div class="alert alert-success p-1">
                                                    {{ session('PrestarServicio') }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="card" id="cardServiciosPrestados">
                                            <div class="card-header">
                                                <h5 class="card-category">Servicios Prestados</h5>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <tr>
                                                                <th>Cédula</th>
                                                                <th>Apellidos Nombres</th>
                                                                <th>Parentesco</th>
                                                            </tr>
                                                            @php
                                                            $totalRegistros = 0;
                                                            @endphp
                                                            @foreach ($beneficiarios as $beneficiario)
                                                            @if($beneficiario->estado != '1')
                                                            <tr>
                                                                <td>{{ $beneficiario->cedula }}</td>
                                                                <td>{{ $beneficiario->apellido . $beneficiario->nombre }}
                                                                </td>

                                                                @if ($beneficiario->parentescoo !== null)
                                                                <td>{{ $beneficiario->parentescoo->nomPar }}
                                                                </td>
                                                                @else
                                                                <td>{{ $beneficiario->parentesco }}</td>
                                                                @endif
                                                            </tr>
                                                            @php
                                                            $totalRegistros++;
                                                            @endphp
                                                            @endif
                                                            @endforeach
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if($totalRegistros == 0)
                                        <script>
                                            $("#cardServiciosPrestados").hide()
                                        </script>
                                        @endif
                                    </div>


                                    <div class="row">
                                        <div class="col-md-auto">
                                            <div class="form-group">
                                                <a href="{{ route('asociados.generarpdf', ['id' => $asociado->cedula]) }}" target="_blank" class="btn btn-primary">Generar PDF</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Agregar Beneficiario -->
            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('beneficiarios.store') }}" id="FormularioAñadirBeneficiario" method="post">
                            @csrf
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Agregar Beneficiario</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-4 pr-1">
                                        <div class="form-group">
                                            <label>Cedula</label>
                                            <input type="hidden" value='{{ $asociado->cedula }}' name="cedulaAsociado">
                                            <input type="text" class="form-control" placeholder="Cedula" id="cedula" name="cedula">
                                        </div>
                                    </div>
                                    <div class="col-md-4 pr-1">
                                        <div class="form-group">
                                            <label>Parentesco</label>
                                            <select class="form-control" name="parentesco" aria-label="Default select example" id="selectParentesco"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 pl-1">
                                        <div class="form-group">
                                            <label>Fecha de Nacimiento</label>
                                            <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 pr-1">
                                        <div class="form-group">
                                            <label>Apellidos</label>
                                            <input type="text" class="form-control" placeholder="Apellidos" id="apellidos" name="apellidos">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-1">
                                        <div class="form-group">
                                            <label>Nombres</label>
                                            <input type="text" class="form-control" placeholder="Nombres" id="nombres" name="nombres">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <p class="text-danger col-md-12" id="msjAjax"></p>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="botonEnviar" class="btn btn-primary">Enviar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(function() {                    
                    $.ajax({
                        url: "{{ route('parentescosall') }}",
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {                            
                            var select = $('#selectParentesco');
                            response.forEach(function(data) {
                                select.append($('<option>', {
                                    //BD LOCAL
                                    // value: data.codPar,
                                    // text: data.nomPar

                                    //consumo api
                                    value: data.code,
                                    text: data.name
                                }));
                            });
                        },
                        error: function(xhr, status, error) {
                            // Manejar errores aquí
                            console.error('Error:', error);
                        }
                    });
                    $('#FormularioAñadirBeneficiario').submit(function(event) {
                        event.preventDefault();

                        var formData = $(this).serialize();
                        var csrfToken = $('meta[name="csrf-token"]').attr('content');
                        $.ajax({
                            url: "{{ route('beneficiarios.store') }}",
                            type: 'POST',
                            data: formData,
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            success: function(response) {
                                console.log(response);
                                alert("se añadio el beneficiario")                                                            
                                location.reload();
                            },
                            error: function(xhr) {
                                var errorMessage = JSON.parse(xhr.responseText).message;
                                $('#msjAjax').text(errorMessage);
                                console.error(xhr.responseText);
                            }
                        });
                    });
                });
            </script>

            <!-- Modal Prestar servicio -->
            <div class="modal fade" id="exampleModalServicio" tabindex="-1" aria-labelledby="exampleModalServicioLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="DatosPrestarServicio">holi?</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('monitoria.store') }}" id="FormularioPrestarServicio">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" id="cedulaTitular" name="cedulaTitular" value="{{ $asociado->cedula }}">
                                <input type="hidden" id="cedulaFallecido" name="cedulaFallecido">
                                <input type="hidden" id="parentesco" name="parentesco">
                                <div class="row">
                                    <div class="col-md-5 pr-1">
                                        <div class="form-group">
                                            <label>Lugar Fallecimiento</label>
                                            <input type="text" class="form-control" name="lugarFallecimiento">
                                        </div>
                                    </div>
                                    <div class="col-md-4 px-1">
                                        <div class="form-group">
                                            <label>Fecha Fallecimiento</label>
                                            <input type="date" class="form-control" name="fechaFallecimiento">
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-1">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Hora</label>
                                            <input type="time" class="form-control" name="horaFallecimiento">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 pr-1">
                                        <div class="form-group">
                                            <label>Contacto 1</label>
                                            <input type="text" class="form-control" name="contacto">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-1">
                                        <div class="form-group">
                                            <label>Telefono de Contacto</label>
                                            <input type="text" class="form-control" name="telefonoContacto">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 pr-1">
                                        <div class="form-group">
                                            <label>Contacto 2</label>
                                            <input type="text" class="form-control" name="contacto2">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-1">
                                        <div class="form-group">
                                            <label>Telefono de Contacto</label>
                                            <input type="text" class="form-control" name="telefonoContacto2">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group">
                                            <label>Traslado</label>
                                            <select class="form-control" name="traslado">
                                                <option value="1">Sí</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 pl-1">
                                        <div class="form-group">
                                            <label>Factura </label>
                                            <input type="text" class="form-control" name="factura">
                                        </div>
                                    </div>
                                    <div class="col-md-5 pl-1">
                                        <div class="form-group">
                                            <label>Valor </label>
                                            <input type="number" class="form-control" name="valor">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Servicio Prestado</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <script>
                $('.botonPrestarServicio').on('click', function() {
                    var fila = $(this).closest('tr');
                    var cedula = fila.find('td:eq(0)').text();
                    var nombre = fila.find('td:eq(1)').text();
                    var parentesco = fila.find('td:eq(2)').text();
                    $('#cedulaFallecido').val(cedula);
                    $('#parentesco').val(parentesco);
                    $('#DatosPrestarServicio').text(nombre);
                });
            </script>
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            ¿Estás seguro de que deseas actualizar la fecha
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="confirmarEnvio" class="btn btn-primary">Si</button>
                            <button type="button" class="btn btn-secondary" id="botonNo">No</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="ModalBeneficiarios" tabindex="-1" aria-labelledby="ModalBeneficiariosLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            ¿Estás seguro de que deseas actualizar la fecha?
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="confirmarEnvioBene" class="btn btn-primary">Si</button>
                            <button type="button" class="btn btn-secondary" id="botonNoBene">No</button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    //asociado
                    const formulario = document.getElementById('FormFechaUpdate');
                    const botonConfirmar = document.getElementById('confirmarEnvio');

                    formulario.addEventListener('submit', function(event) {
                        event.preventDefault();
                        $('#exampleModal').modal('show');
                    });

                    const botonCerrarModal = document.getElementById('botonNo')
                    botonCerrarModal.addEventListener('click', function() {
                        $('#exampleModal').modal('hide');
                        location.reload();
                    })

                    botonConfirmar.addEventListener('click', function() {
                        $('#exampleModal').modal('hide');
                        var cedula = "{{ $asociado->cedula }}";
                        var fechaNacimiento = $('#fechaNacimiento').val();

                        const nuevaFecha = fechaInput.value;
                        //const url = `/asociados/${cedula}`;

                        $.ajax({
                            url: "{{ route('asociados.update', ['asociado' => $asociado->cedula]) }}",
                            method: 'PUT',
                            data: {
                                fechaNacimiento: nuevaFecha,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                console.log('Datos actualizados correctamente');
                                location.reload();
                            },
                            error: function(xhr, status, error) {
                                console.error('Error al enviar los datos al servidor:', error);
                            }
                        });
                    });

                    //beneficiarios
                    const formularioBene = document.getElementById('fechasBeneficiarios');
                    const confirmarEnvioBene = document.getElementById('confirmarEnvioBene');

                    formularioBene.addEventListener('submit', function(event) {
                        event.preventDefault();
                        $('#ModalBeneficiarios').modal('show');
                    });

                    const botonCerrarBene = document.getElementById('botonNoBene')
                    botonCerrarBene.addEventListener('click', function() {
                        $('#ModalBeneficiarios').modal('hide');
                        location.reload();
                    })

                    confirmarEnvioBene.addEventListener('click', function() {
                        $('#ModalBeneficiarios').modal('hide');

                        var formData = $(formularioBene).serialize();
                        $.ajax({
                            type: 'PUT',
                            url: "{{ route('beneficiarios.update', ['beneficiario' => $asociado->cedula]) }}",
                            data: formData,
                            _token: '{{ csrf_token() }}',
                            success: function(response) {
                                console.log("Datos actualizados");
                                location.reload();
                            },
                            error: function(xhr, status, error) {
                                console.error(error);
                            }
                        });
                    });
                });
            </script>
</body>

</html>