<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <x-component-header />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
                    <a class="navbar-brand" href="#pablo">TITULAR - BENEFICIARIOS</a>
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
        <div class="panel-header panel-header-sm">
        </div>

        <div class="content">
            <div class="row">
                <div class="col-md-11 ml-auto mr-auto">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-category">Información del Titular</h5>
                            <div class="d-flex flex-row align-items-center">
                                <h5 class="title mb-0">{{ $asociado['name'] }}</h5>
                                <button class="btn btn-success ml-4 py-2"><i class="bi bi-pencil-square"></i></button>
                                <button class="btn btn-danger mx-2 py-2"><i class="bi bi-x-square"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2 pr-1">
                                    <div class="form-group">
                                        <label for="cedula">Cédula</label>
                                        <div class="form-control">{{ $asociado['documentId'] }}</div>
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
                                        </div>
                                    </form>
                                </div>

                                <div class="col-md-1 pl-1">
                                    <!-- <div class="form-group">
                                        @php
                                        $fecNac = new DateTime();
                                        $fechaActual = new DateTime();
                                        $diferencia = $fecNac->diff($fechaActual);
                                        $edad = $diferencia->y;
                                        @endphp
                                        <label>Edad</label>
                                        <div class="form-control">
                                            @if($edad == 2024) 0
                                            @else {{ $edad }}
                                            @endif
                                        </div>
                                    </div> -->
                                </div>

                                <div class="col-md-3 pl-1">
                                    <div class="form-group">
                                        <label>Dirección</label>
                                        <div class="form-control">
                                            <div class="p-2"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 pl-1">
                                    <div class="form-group">
                                        <label>Teléfono</label>
                                        <div class="form-control">
                                            <div class="p-2"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2 pr-1">
                                        <div class="form-group">
                                            <label>Plan</label>
                                            <div class="form-control">

                                            </div>
                                            <label>Distrito</label>
                                            <div class="form-control"></div>


                                            <label>Ciudad</label>
                                            <div class="form-control"></div>

                                            <label>Email</label>
                                            <div class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <label>Observaciones</label>
                                        <p class="border border-secondary-subtle p-3 rounded">

                                        </p>

                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-category">Beneficiarios</h5>
                                            </div>
                                            <div class="card-body">                                               
                                                    
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
                                                            @if($beneficiario["type"] == "A")
                                                            <tr>
                                                                <form method="POST" class="formDeleteBene">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <input type="hidden" name="documentId" value="{{ $beneficiario["documentId"] }}">                                                            
                                                                    <td>{{ $beneficiario["documentId"] }}</td>
                                                                </form>
                                                                <td>{{ $beneficiario["names"] }}</td>
                                                                <td>{{ $beneficiario["relationship"] }}</td>
                                                                <td>{{ $beneficiario["dateBirthday"] }}</td>
                                                                @php
                                                                $fecNac = new DateTime($beneficiario["dateBirthday"]);
                                                                $fechaActual = new DateTime();
                                                                $diferencia = $fecNac->diff($fechaActual);
                                                                $edad = $diferencia->y;
                                                                @endphp
                                                                <td>{{ $edad }}</td>
                                                                <td class="d-flex flex-row align-items-center">
                                                                    <a class="btn btn-info py-2 px-1 botonEliminarBene m-0" data-bs-toggle="modal" data-bs-target="#exampleModalServicio">
                                                                        Prestar Servicio
                                                                    </a>
                                                                    <a class="btn btn-danger py-2 px-1 ml-1 botonEliminarBene"><i class="bi bi-x-square"></i></a>
                                                                </td>
                                                                <!-- {{-- <td><a class="btn btn-danger py-3 px-1"><i class="bi bi-x-square"></i></a></td> --}} -->
                                                            </tr>
                                                            @endif
                                                            @endforeach
                                                        </table>
                                                    </div>

                                                    <div class="d-flex">
                                                        <button class="btn btn-secondary m-3 p-2" type="submit">Actualizar fechas</button>
                                                        <button type="button" class="btn btn-primary m-3 p-2" data-bs-toggle="modal" data-bs-target="#addBeneficiario">
                                                            Agregar Beneficiario
                                                        </button>
                                                    </div>
                                                
                                            </div>
                                            @if(session('PrestarServicio'))
                                            <div class="alert alert-success p-1">
                                                {{ session('PrestarServicio') }}
                                            </div>
                                            @endif
                                            <div id="SuccessAddBeneficiario"></div>
                                        </div>
                                        <!-- tabla registros SERVICIO PRESTADO -->    
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
                                                            @if($beneficiario["type"] != "A")
                                                            <tr>
                                                                <td>{{ $beneficiario["cedulaBeneficiario"] }}</td>
                                                                <td>{{ $beneficiario["names"] }}</td>
                                                                <td>{{ $beneficiario["relationship"] }}</td>

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

                                    {{-- GENERAR PDF --}}
                                    <div class="row">
                                        <div class="col-md-auto">
                                            <div class="form-group">
                                                <a href="{{ route('asociados.generarpdf', ['id' => $beneficiario['cedulaBeneficiario']]) }}" target="_blank" class="btn btn-primary">Generar PDF</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- MODAL -->
            <!-- ADD Beneficiario -->
            <div class="modal fade" id="addBeneficiario" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addBeneficiario" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="" id="FormularioAddBeneficiario" method="post">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Agregar Beneficiario</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-4 pr-1">
                                        <div class="form-group">
                                            <label>Cedula</label>
                                            <input type="hidden" value="{{ $asociado["documentId"] }}" name="cedulaAsociado">
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
            <!-- Eliminar Beneficiario -->
            <div class="modal fade" id="eliminarBeneficiario" tabindex="-1" aria-labelledby="elimiarBeneficiario" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            ¿Estás seguro de que desea eliminar el registro?
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="confirmarElimacionBene" class="btn btn-primary">Si</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">No</button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(function() {
                    /* Añadir Beneficiario */
                    var message = localStorage.getItem('successMessage');
                    if (message) {
                        $("#SuccessAddBeneficiario").html('<div class="alert alert-success p-1">' + message + '</div>');
                        localStorage.removeItem('successMessage');
                    }
                    $('#FormularioAddBeneficiario').submit(function(event) {
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
                                localStorage.setItem('successMessage', "Registro Añadido Exitosamente");
                                location.reload();
                            },
                            error: function(xhr) {
                                var errorMessage = JSON.parse(xhr.responseText).message;
                                $('#msjAjax').text(errorMessage);
                                console.error(xhr.responseText);
                            }
                        });
                    });

                    /* Parentescos Formulario addBeneficiario */
                    $.ajax({
                        url: "{{ route('parentescosall') }}",
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {                 
                            var select = $('#selectParentesco');
                            response.forEach(function(data) {                                
                                select.append($('<option>', {
                                    value: data.code,
                                    text: data.name
                                }));
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });

                    /* Eliminar Beneficiario */
                    $('.botonEliminarBene').on('click', function() {
                        var button = $(this);
                        $('#eliminarBeneficiario').modal('show');
                        $('#confirmarElimacionBene').on('click', function() {
                            var fila = button.closest('tr');
                            var documentId = fila.find('input[name="documentId"]').val();
                            console.log('Valor de documentId:', documentId);

                            var csrfToken = $('meta[name="csrf-token"]').attr('content');
                            $.ajax({
                                url: "{{ route('beneficiarios.destroy', ['beneficiario' => ':id']) }}".replace(':id', documentId),
                                type: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                success: function(response) {
                                    console.log("eliminado")
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error al eliminar beneficiario:', error);
                                    // Manejar el error según sea necesario
                                }
                            });
                        });
                    });
                });                
            </script>
</body>
</html>