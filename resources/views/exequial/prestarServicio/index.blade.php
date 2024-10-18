<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <x-component-header />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/perfect-scrollbar/1.5.3/css/perfect-scrollbar.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        td,
        th {
            font-size: 12px;
        }

        .uppercase-input {
            text-transform: uppercase;
        }

        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.7);
            /* Semi-transparent background */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            visibility: hidden;
        }

        .required-asterisk {
            color: red;
        }
    </style>
</head>

<body>
    @section('titlenav', 'EXEQUIALES')
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
                    <a class="navbar-brand" href="#">Servicios Prestados</a>
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
        <div class="panel-header panel-header-lg">
            <canvas id="bigDashboardChart"></canvas>
        </div>


        <div class="content">
            <div class="row">
                
            <div class="col-lg-4 col-md-6">
                <div class="card" style="height: 295px;">
                        <div class="card-header">
                            <h5 class="card-category">2024</h5>
                            <h4 class="card-title">Total Servicios Prestados</h4>                            
                        </div>
                        <div class="card-body">                            
                            <h1 class="text-center" style >{{$totalRegistros}}</h1>
                        </div>
                    </div>
                </div>               

                <div class="col-lg-4 col-md-6">
                    <div class="card card-chart">
                        <div class="card-header">
                            <h5 class="card-category">2024</h5>
                            <h4 class="card-title">Mujeres - Hombres</h4>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="barChartSimpleGradientsNumbers"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-category">2024 Octubre</h5>
                            <h4 class="card-title">Servicios prestados</h4>
                        </div>
                        <div class="card-body">
                            <h1 class="text-center">{{$mesRegistros}}</h1>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div id="overlay">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <form id="formFiltroMes">
                                        <select name="months" class="form-select" id="selectMes">
                                            <option value="12">Diciembre</option>
                                            <option value="11">Noviembre</option>
                                            <option value="10">Octubre</option>
                                            <option value="9">Septiembre</option>
                                            <option value="8">Agosto</option>
                                            <option value="7">Julio</option>
                                            <option value="6">Junio</option>
                                            <option value="5">Mayo</option>
                                            <option value="4">Abril</option>
                                            <option value="3">Marzo</option>
                                            <option value="2">Febrero</option>
                                            <option value="1">Enero</option>
                                        </select>
                                    </form>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('prestarServicio.generarpdf') }}" target="_blank">Generar PDF</a>
                                </div>
                                <div class="col-md-2">
                                    <!-- <a href="/exportar-datos" class="btn btn-secondary">Exportar Datos</a> -->
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
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
                                        <th></th>
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
                                        <td>{{ $reg->nombreTitular }}</td>
                                        <td>{{ $reg->nombreFallecido }}</td>
                                        <td>{{ $reg->lugarFallecimiento }}</td>
                                        <td>{{ $reg->parentesco}}</td>
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
                                        <td>
                                            <a class="btn btn-warning ml-1 py-2 btn-detallePrestarServicio" data-index={{ $loop->index }} data-bs-toggle="modal" data-bs-target="#ModalFormServicio">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <a class="text-primary btn-pdf" href="" data-index={{ $loop->index }}>PDF</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                            <div id="SuccessMsj"></div>
                        </div>
                    </div>
                </div>

                
            </div>
        </div>

        <!-- MODAL SERVICIOS -->
        <div class="modal fade" id="ModalFormServicio" tabindex="-1" aria-labelledby="ModalFormServicio" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><a id="verTitular" class="link-underline-danger">Click para ver Titular</a></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="FormularioPrestarServicio" novalidate>
                        @method('DELETE')
                        <div class="modal-body">
                            <div class="row">
                                <input type="hidden" name="idRow" id="idRow">
                                <div class="col-md-5 pr-1">
                                    <div class="form-group">
                                        <label>Lugar Fallecimiento <span class="required-asterisk">*</span></label>
                                        <input type="text" id="lugarFallecimiento" class="form-control uppercase-input" name="lugarFallecimiento" required>
                                    </div>
                                </div>
                                <div class="col-md-3 px-1">
                                    <div class="form-group">
                                        <label>Fecha <span class="required-asterisk">*</span></label>
                                        <input type="date" id="fechaFallecimiento" class="form-control" name="fechaFallecimiento" required>
                                    </div>
                                </div>
                                <div class="col-md-4 pl-1">
                                    <div class="form-group">
                                        <label>Hora <span class="required-asterisk">*</span></label>
                                        <input type="time" id="horaFallecimiento" class="form-control" name="horaFallecimiento" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 pr-1">
                                    <div class="form-group">
                                        <label>Contacto 1 <span class="required-asterisk">*</span></label>
                                        <input type="text" id="contacto" class="form-control uppercase-input" name="contacto" required>
                                    </div>
                                </div>
                                <div class="col-md-6 pl-1">
                                    <div class="form-group">
                                        <label>Teléfono de Contacto <span class="required-asterisk">*</span></label>
                                        <input type="text" id="telcontacto" class="form-control" name="telefonoContacto" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 pr-1">
                                    <div class="form-group">
                                        <label>Contacto 2</label>
                                        <input type="text" id="contactodos" class="form-control uppercase-input" name="contacto2">
                                    </div>
                                </div>
                                <div class="col-md-6 pl-1">
                                    <div class="form-group">
                                        <label>Teléfono de Contacto</label>
                                        <input type="text" id="telcontactodos" class="form-control" name="telefonoContacto2">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 pr-1">
                                    <div class="form-group">
                                        <label>Traslado</label>
                                        <select class="form-control" name="traslado" id="selectTraslado">
                                            <option value="1">Sí</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 pl-1">
                                    <div class="form-group">
                                        <label>Factura </label>
                                        <input type="text" class="form-control uppercase-input" name="factura" id="factura">
                                    </div>
                                </div>
                                <div class="col-md-5 pl-1">
                                    <div class="form-group">
                                        <label>Valor </label>
                                        <input type="number" class="form-control" name="valor" id="valor">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                            <!-- <button type="button" class="btn btn-danger" id="btnEliminar">Eliminar</button> -->
                            <button type="submit" class="btn btn-warning">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Confirmar envio Eliminar Registro -->
        <div class="modal fade" id="modalConfirmarEliminacion" data-bs-backdrop="static" tabindex="-1" aria-labelledby="modalConfirmarEliminacion" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de que desea eliminar el registro?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            aria-label="Close">No</button>
                        <button type="button" id="btnConfirmarEliminacion" class="btn btn-danger">Si</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmar envio Actualizar Registro-->
        <div class="modal fade mt-auto" id="modalConfirmarUpdate" data-bs-backdrop="static" tabindex="-1" aria-labelledby="ConfirmarEnvioUpdate" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de que deseas actualizar los datos?
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnConfirmarEnvio" class="btn btn-warning">Si</button>
                        <button type="button" id="btnNOEnvio" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/plugins/chartjs.min.js')}}"></script>
    <script src="{{ asset('assets/demo/demo.js')}}"></script>
    <script src="{{ asset('assets/js/now-ui-dashboard.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/perfect-scrollbar/1.5.3/perfect-scrollbar.min.js"></script>


    <script>
        $(document).ready(function() {
            window.onload = function() {
                demo.initDashboardPageCharts();
            }        

            var currentMonth = new Date().getMonth() + 1;
            $("#selectMes").val(currentMonth);

            var url = window.location.href;

            var match = url.match(/\/(\d+)$/);

            if (match) {
                var mesSeleccionado = match[1];
                localStorage.setItem('mesSeleccionado', mesSeleccionado);
            } else {
                localStorage.setItem('mesSeleccionado', currentMonth);
            }
            var valorSeleccionado = localStorage.getItem('mesSeleccionado') || 1;

            $('#selectMes').val(valorSeleccionado);

            var serviciosjson = @json($registros);
            $('#selectMes').change(function() {
                valorSeleccionado = $(this).val();
                window.location.href = '/prestarServicio/mes/' + valorSeleccionado;
            });


            var message = localStorage.getItem('successMessage');
            if (message) {
                $("#SuccessMsj").html('<div class="alert alert-success p-1 pl-3" style=color:black;>' + message + '</div>');
                localStorage.removeItem('successMessage');
            }

            document.querySelectorAll('.uppercase-input').forEach(input => {
                input.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
            });

            document.querySelectorAll('.btn-detallePrestarServicio').forEach(click => {
                click.addEventListener('click', function() {
                    $("#SuccessMsj").html('<div></div>');
                    const i = this.getAttribute('data-index');
                    $('#verTitular').attr('href', '/beneficiarios/ID?id=' + serviciosjson[i].cedulaTitular);
                    $('#idRow').val(serviciosjson[i].id);
                    $('#lugarFallecimiento').val(serviciosjson[i].lugarFallecimiento);
                    $('#fechaFallecimiento').val(serviciosjson[i].fechaFallecimiento);
                    $('#horaFallecimiento').val(serviciosjson[i].horaFallecimiento);
                    $('#contacto').val(serviciosjson[i].contacto);
                    $('#telcontacto').val(serviciosjson[i].telefonoContacto);
                    $('#contactodos').val(serviciosjson[i].Contacto2);
                    $('#telcontactodos').val(serviciosjson[i].telefonoContacto2);
                    $('#selectTraslado').val(serviciosjson[i].traslado);
                    $('#factura').val(serviciosjson[i].factura);
                    $('#valor').val(serviciosjson[i].valor);
                });
            });

            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            $('#FormularioPrestarServicio').submit(function(event) {
                event.preventDefault();
                var formData = $(this).serialize();
                var form = document.getElementById('FormularioPrestarServicio');
                if (form.checkValidity()) {
                    $('#modalConfirmarUpdate').modal('show');
                    $('#btnConfirmarEnvio').off('click').on('click', function() {
                        $('#overlay').css('visibility', 'visible');
                        $.ajax({
                            url: "{{ route('exequial.prestarServicio.update', ['prestarServicio' => ':id']) }}"
                                .replace(':id', $("#idRow").val()),
                            method: 'PATCH',
                            data: formData,
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            success: function(response) {
                                console.log(response);
                                localStorage.setItem('successMessage', response.message);
                                location.reload();
                            },
                            error: function(error) {
                                console.log(error)
                            },
                            complete: function() {
                                $('#overlay').css('visibility', 'hidden');
                            }
                        });
                    });
                    $('#btnNOEnvio').on('click', function() {
                        $('#FormularioPrestarServicio')[0].reset();
                    })
                } else {
                    $("#FormularioPrestarServicio").addClass('was-validated');
                    event.preventDefault();
                    event.stopPropagation();
                }
            });

            $('#btnEliminar').on('click', function() {
                $('#modalConfirmarEliminacion').modal('show');
                $('#btnConfirmarEliminacion').on('click', function() {
                    $('#overlay').css('visibility', 'visible');
                    console.log("val? " + $("#idRow").val());
                    $.ajax({
                        url: "{{ route('exequial.prestarServicio.destroy', ['prestarServicio' => ':id']) }}"
                            .replace(':id', $("#idRow").val()),
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            console.log(response);
                            localStorage.setItem('successMessage', response.message);
                            $('#overlay').css('visibility', 'hidden');
                            //location.reload();
                        },
                        error: function(error) {
                            console.log(error)
                        }
                    });
                });
            });

            document.querySelectorAll('.btn-pdf').forEach(function(link) {
                const i = link.getAttribute('data-index');
                link.setAttribute('href', 'prestarServicio/' + serviciosjson[i].id + '/generarpdf');
            });
        });
    </script>
</body>

</html>