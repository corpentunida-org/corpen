<style>
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
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        visibility: hidden;
    }

    td {
        font-size: 14px;
    }

    .required-asterisk {
        color: red;
    }
</style>

@extends('layouts.appTemplate')
@section('titlepage', 'Detalle Cliente')
@section('titlenav', 'EXEQUIALES')
@section('contentpage')
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
<div id="overlay">
    <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
<div class="card-header">
    <h5 class="card-category">Información del Titular</h5>
    <div class="d-flex flex-row align-items-center">
        <h5 class="title mb-0">{{ $asociado['name'] }}</h5>
        @if ($asociado['stade'])
            @can('exequial.asociados.update')
                <button class="btn btn-warning ml-4 py-2" id="btnActualizarTitular"><i class="bi bi-pencil-square"></i></button>
            @endcan
            {{-- <button class="btn btn-danger mx-2 py-2"><i class="bi bi-x-square"></i></button> --}}
            <a class="btn btn-success py-2 ml-1 px-2 botonPrestarServicio" data-bs-toggle="modal"
                data-bs-target="#ModalFormServicio" style="color: black">Prestar Servicio</a>
        @else
            <button class="btn btn-danger mx-2 py-2">Pastor Inactivo</button>
        @endif
    </div>
</div>
<div class="card-body">
    @if(session('messageTit'))
        <div class="alert alert-success p-1 pl-3" style=color:black;>
            {{ session('messageTit') }}
        </div>
    @endif
    <div id="messageTit"></div>
    <div class="row px-3">
        <div class="col-md-2 pr-1">
            <div class="form-group">
                {{-- Form / data del Titular --}}
                <form id="formUpdateTitular">
                    <label>Cédula</label>
                    <div class="form-control">{{ $asociado['documentId'] }}</div>

                    <label class="mt-3">Observaciones</label>
                    <div class="form-control inputTitularAct">{{ $asociado['observation'] }}</div>

                    <label class="mt-3">Plan</label>
                    <div class="form-control" id="planTitular">{{ $asociado['codePlan'] }}</div>
                    <select style="display: none;" class="form-control border-warning" id="selectTitularUpdate"
                        name="planes">
                        <option value="01">Plan Basico</option>
                        <option value="02">Plan Ejecutivo</option>
                        <option value="03">Plan Unipersonal</option>
                        <option value="04">Plan Exento Pago</option>
                    </select>
                    <label class="mt-3">Descuento</label>
                    <div class="form-control inputTitularAct">{{ $asociado['discount'] }}</div>

                    <label class="mt-3">Fecha de Inicio</label>
                    <div class="form-control" id="fechaInicioTitular"></div>

                    <label class="mt-3">Contrato</label>
                    <div class="form-control">{{ $asociado['agreement'] }}</div>

                    <button type="submit" class="btn btn-warning mt-2" id="btnUpdateTitular">
                        <i class="bi bi-pencil-square"></i> Actualizar</button>
                </form>
            </div>
        </div>
        <div class="col-md-10">
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
                                {{-- Table de beneficiarios activos --}}
                                @if ($beneficiario['type'] == 'A')
                                    <tr>
                                        <form method="POST" class="formDeleteBene">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="documentId" value="{{ $beneficiario['documentId'] }}">
                                            <td>{{ $beneficiario['documentId'] }}</td>
                                        </form>
                                        <td>{{ $beneficiario['names'] }}</td>
                                        <td>{{ $beneficiario['relationship'] }}</td>
                                        <td class="dateBeneficiarios">{{ $beneficiario['dateBirthday'] }}</td>
                                        @php
                                            $fecNac = new DateTime($beneficiario['dateBirthday']);
                                            $fechaActual = new DateTime();
                                            $diferencia = $fecNac->diff($fechaActual);
                                            $edad = $diferencia->y;
                                        @endphp
                                        <td>{{ $edad }}</td>
                                        @if ($asociado['stade'])
                                            <td>
                                                <a class="btn btn-success py-2 ml-1 px-2 botonPrestarServicio"data-bs-toggle="modal" data-bs-target="#ModalFormServicio"style="color: black">Prestar Servicio</a>
                                                @can('exequial.beneficiarios.update')
                                                    <a class="btn btn-warning ml-1 py-2 btn-open-modal-updateBene" data-index={{ $loop->index }} style="color: white" data-bs-toggle="modal"
                                                        data-bs-target="#modalUpdateBeneficiario"><i
                                                            class="bi bi-pencil-square"></i></a>
                                                @endcan
                                                @can('exequial.beneficiarios.destroy')
                                                    <a style="color: white" class="btn btn-danger py-2 ml-1 botonEliminarBene"><i
                                                            class="bi bi-x-square"></i></a>
                                                @endcan
                                            </td>
                                        @endif
                                    </tr>
                                @endif
                            @endforeach
                        </table>
                    </div>

                    {{-- Boton Add Beneficiario --}}
                    @if ($asociado['stade'])
                        @can('exequial.beneficiarios.store')
                            <div class="d-flex">
                                <button type="button" class="btn btn-success m-3" data-bs-toggle="modal"
                                    data-bs-target="#addBeneficiario" style="color: black">
                                    Agregar Beneficiario
                                </button>
                            </div>
                        @endcan
                    @endif
                </div>
                @if (session('PrestarServicio'))
                    <div class="alert alert-success p-1">
                        {{ session('PrestarServicio') }}
                    </div>
                @endif
                <div id="SuccessAddBeneficiario"></div>
            </div>
            <!-- tabla de beneficiarios SERVICIO PRESTADO -->
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
                                    @if ($beneficiario['type'] != 'A')
                                        <tr>
                                            <td>{{ $beneficiario['documentId'] }}
                                            </td>
                                            <td>{{ $beneficiario['names'] }}</td>
                                            <td>{{ $beneficiario['relationship'] }}</td>
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
            @if ($totalRegistros == 0)
                <script>
                    $("#cardServiciosPrestados").hide()
                </script>
            @endif
        </div>

        {{-- GENERAR PDF --}}
        <div class="row">
            <div class="col-md-auto">
                <div class="form-group">
                    <!-- <a href="" id="btnpdf">Generar PDF</a> -->
                    <a href="{{ route('asociados.generarpdf', ['id' => $asociado['documentId']]) }}" id="btnpdf">Generar PDF</a>
                </div>
            </div>
        </div>
    </div>

</div>


<!-- MODAL -->
<!-- ADD Beneficiario -->
<div class="modal fade" id="addBeneficiario" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="addBeneficiario" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="needs-validation" id="FormularioAddBeneficiario" method="post" novalidate>
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Agregar Beneficiario</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 pr-1">
                            <div class="form-group">
                                <label>Cédula <span class="required-asterisk">*</span></label>
                                <input type="hidden" value="{{ $asociado['documentId'] }}" name="cedulaAsociado">
                                <input type="number" class="form-control" placeholder="Cedula" id="cedula" name="cedula"
                                    required min="1" step="1">
                            </div>
                        </div>
                        <div class="col-md-4 pr-1">
                            <div class="form-group">
                                <label>Parentesco</label>
                                <select class="form-control selectParentesco" name="parentesco"
                                    aria-label="Default select example" id="selectParentesco" required></select>
                            </div>
                        </div>
                        <div class="col-md-4 pl-1">
                            <div class="form-group">
                                <label>Fecha de Nacimiento <span class="required-asterisk">*</span></label>
                                <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 pr-1">
                            <div class="form-group">
                                <label>Apellidos <span class="required-asterisk">*</span></label>
                                <input type="text" class="form-control uppercase-input" placeholder="Apellidos"
                                    id="apellidos" name="apellidos" required>
                            </div>
                        </div>
                        <div class="col-md-6 pl-1">
                            <div class="form-group">
                                <label>Nombres <span class="required-asterisk">*</span></label>
                                <input type="text" class="form-control uppercase-input" placeholder="Nombres"
                                    id="nombres" name="nombres" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <p class="text-danger col-md-12" id="msjAjax"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="botonEnviar" class="btn btn-success" style="color: black">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Actualizar Beneficiario --}}
<div class="modal fade" id="modalUpdateBeneficiario" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="modalUpdateBeneficiario" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="FormUpdateBeneficiario" method="post">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Actualizar Beneficiario</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 pr-1">
                            <div class="form-group">
                                <label>Cédula</label>
                                <input type="number" class="form-control" id="updateCedBenficiario" name="cedula"
                                    min="1" step="1">
                            </div>
                        </div>
                        <div class="col-md-4 pr-1">
                            <div class="form-group">
                                <label>Parentesco</label>
                                <select class="form-control selectParentesco" name="parentesco"
                                    aria-label="Default select example" id="UpdateSelectParentesco"></select>
                            </div>
                        </div>
                        <div class="col-md-4 pl-1">
                            <div class="form-group">
                                <label>Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="UpdatefechaNacimiento"
                                    name="fechaNacimiento">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 pr-1">
                            <div class="form-group">
                                <label>Apellidos y Nombres</label>
                                <input type="text" class="form-control uppercase-input" id="UpdateApellidosyNombres"
                                    name="names">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <p class="text-danger col-md-12" id="msjAjaxUpdateBene"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnUpdateBene" class="btn btn-warning">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Eliminar Beneficiario -->
<div class="modal fade" id="eliminarBeneficiario" tabindex="-1" aria-labelledby="elimiarBeneficiario"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que desea eliminar el registro?
                <div class="row">
                    <p class="text-danger col-md-12" id="msjApiEliminar"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    aria-label="Close">Cancelar</button>
                <button type="button" id="confirmarElimacionBene" class="btn btn-danger">Si</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Confirmacion Update Titular - Beneficiario --}}
<div class="modal fade" id="ConfirmarEnvioUpdate" tabindex="-1" aria-labelledby="ConfirmarEnvioUpdate"
    aria-hidden="true">
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">No</button>
            </div>
        </div>
    </div>
</div>

{{-- Prestar Servicio --}}
<div class="modal fade" id="ModalFormServicio" tabindex="-1" aria-labelledby="ModalFormServicio" aria-hidden="true"data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Prestar Servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('exequial.prestarServicio.store') }}" id="FormularioPrestarServicio" novalidate>
                <!-- <form id="FormularioPrestarServicio" novalidate> -->
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="cedulaTitular" name="cedulaTitular" value="{{ $asociado['documentId'] }}">
                    <input type="hidden" id="cedulaFallecido" name="cedulaFallecido">
                    <input type="hidden" id="parentescoServicio" name="parentesco">
                    <input type="hidden" id="nameFallecido" name="nameBeneficiary">
                    <input type="hidden" value="{{ $asociado['name'] }}" name="nameTitular">
                    <input type="hidden" name="fecNacFallecido" id="fecNacFallecido">
                    <input type="hidden" name="dateInit" id="dateInit">
                    <input type="hidden" name="codePlan" id="pscodePlan">
                    <input type="hidden" name="discount" id="psdiscount">
                    <input type="hidden" name="observation" id="psobservation">
                    <input type="hidden" name="pastor" id="ispastor">
                    <div class="row">
                        <div class="col-md-5 pr-1">
                            <div class="form-group">
                                <label>Lugar Fallecimiento <span class="required-asterisk">*</span></label>
                                <input type="text" class="form-control uppercase-input" name="lugarFallecimiento"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-4 px-1">
                            <div class="form-group">
                                <label>Fecha Fallecimiento <span class="required-asterisk">*</span></label>
                                <input type="date" class="form-control" name="fechaFallecimiento" required>
                            </div>
                        </div>
                        <div class="col-md-3 pl-1">
                            <div class="form-group">
                                <label>Hora <span class="required-asterisk">*</span></label>
                                <input type="time" class="form-control" name="horaFallecimiento" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 pr-1">
                            <div class="form-group">
                                <label>Contacto 1 <span class="required-asterisk">*</span></label>
                                <input type="text" class="form-control uppercase-input" name="contacto" required>
                            </div>
                        </div>
                        <div class="col-md-6 pl-1">
                            <div class="form-group">
                                <label>Teléfono de Contacto <span class="required-asterisk">*</span></label>
                                <input type="text" class="form-control" name="telefonoContacto" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 pr-1">
                            <div class="form-group">
                                <label>Contacto 2</label>
                                <input type="text" class="form-control uppercase-input" name="contacto2">
                            </div>
                        </div>
                        <div class="col-md-6 pl-1">
                            <div class="form-group">
                                <label>Teléfono de Contacto</label>
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
                                <input type="text" class="form-control uppercase-input" name="factura">
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        aria-label="Close">Cancelar</button>
                    <button type="submit" class="btn btn-success" style="color: black;">Servicio Prestado</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        var beneficiariosjson = @json($beneficiarios); 
        asociadojson = @json($asociado);
        document.querySelectorAll('.uppercase-input').forEach(input => {
            input.addEventListener('input', function () {
                this.value = this.value.toUpperCase();
            });
        });
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $('#btnpdf').on('click', function () {
            $('#overlay').css('visibility', 'visible');
            setTimeout(function () {
                $('#overlay').css('visibility', 'hidden');
            }, 7000);
        });
        /* Mensaje de Add Titular */
        if (localStorage.getItem('successMessageTit')) {
            var successMessage = localStorage.getItem('successMessageTit');
            $('#messageTit').html('<div class="alert alert-success p-1 pl-3" style="color:black;">' + successMessage + '</div>');
            localStorage.removeItem('successMessageTit');
        }

        /* Optimizar fechas */
        const formatDate = dateString => {
            const [year, month, day] = dateString.split('T')[0].split('-');
            return `${year}-${month}-${day}`;
        };
        if ('{{ $asociado['dateInit'] }}' != '') {
            $('#fechaInicioTitular').html(formatDate('{{ $asociado['dateInit'] }}'))
        }
        $('.dateBeneficiarios').each(function () {
            const originalDate = $(this).text();
            const formattedDate = formatDate(originalDate);
            $(this).html(formattedDate);
        });

        /* Añadir Beneficiario */
        var message = localStorage.getItem('successMessage');
        if (message) {
            $("#SuccessAddBeneficiario").html('<div class="alert alert-success p-1 pl-3" style=color:black;>' + message + '</div>');
            localStorage.removeItem('successMessage');
        }
        $('#FormularioAddBeneficiario').submit(function (event) {
            event.preventDefault();
            var form = document.getElementById('FormularioAddBeneficiario');
            if (form.checkValidity()) {
                var formData = $(this).serialize();
                $('#overlay').css('visibility', 'visible');
                $.ajax({
                    url: "{{ route('exequial.beneficiarios.store') }}",
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (response) {
                        //console.log(response);
                        localStorage.setItem('successMessage', "Registro Añadido Exitosamente");
                        $('#overlay').css('visibility', 'hidden');
                        location.reload();
                    },
                    error: function (xhr) {
                        var errorMessage = JSON.parse(xhr.responseText);
                        $('#msjAjax').text(errorMessage.error.message);
                        $('#overlay').css('visibility', 'hidden');
                    }
                });
            } else {
                $("#FormularioAddBeneficiario").addClass('was-validated');
                event.preventDefault();
                event.stopPropagation();
            }
        });

        /* Parentescos Formulario addBeneficiario - updateBenficiario */
        $.ajax({
            url: "{{ route('exequial.parentescosall') }}",
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                //console.log(response);
                $('.selectParentesco').each(function () {
                    var select = $(this);
                    response.forEach(function (data) {
                        select.append($('<option>', {
                            value: data.code,
                            text: data.name
                        }));
                    });
                });
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
            }
        });

        /* Actualizar Titular */
        $("#btnUpdateTitular").hide();
        $('#btnActualizarTitular').on('click', function () {
            $("#btnUpdateTitular").show();
            //input de texto
            $('.inputTitularAct').each(function (index) {
                var currentText = $(this).text();
                var input = $('<input>', {
                    type: 'text',
                    value: currentText,
                    class: 'form-control border-warning',
                    id: 'inputTitularAct' + index
                });
                $(this).replaceWith(input);
            });
            //select de los planes
            $("#planTitular").css("display", "none");
            $("#selectTitularUpdate").css("display", "block");
            var textoDiv = $("#planTitular").text().trim();
            $("#selectTitularUpdate").val(function () {
                return $('option', this).filter(function () {
                    return $(this).text() === textoDiv;
                }).val();
            });
            //input de fecha
            var currentText = $('#fechaInicioTitular').text();
            //console.log(currentText)
            var input = $('<input>', {
                type: 'date',
                value: currentText,
                class: 'form-control border-warning',
                id: 'inputFechaInicioTitular'
            });
            $('#fechaInicioTitular').replaceWith(input);
        });
        $('#formUpdateTitular').submit(function (event) {
            event.preventDefault();
            $('#ConfirmarEnvioUpdate').modal('show');
            $('#btnConfirmarEnvio').on('click', function () {
                var data = {
                    documentid: '{{ $asociado['documentId'] }}',
                    dateInit: $('#inputFechaInicioTitular').val(),
                    codePlan: $('#selectTitularUpdate').val(),
                    discount: parseInt($('#inputTitularAct1').val()),
                    observation: $('#inputTitularAct0').val(),
                };
                // console.log("fecha de ini "+$('#inputFechaInicioTitular').val())
                // console.log("descuento "+$('#inputTitularAct1').val())
                $('#overlay').css('visibility', 'visible');
                $.ajax({
                    url: "{{ route('exequial.asociados.update', ['asociado' => ':id']) }}"
                        .replace(':id', {{ $asociado['documentId'] }}),
                    type: 'PATCH',
                    data: JSON.stringify(data),
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (response) {
                        console.log("success edit comment " + response);
                        localStorage.setItem('successMessage', "Se Actualizó Titular Correctamente");
                        location.reload();
                        $('#overlay').css('visibility', 'hidden');
                    },
                    error: function (xhr, status, error) {
                        var errorMessage = JSON.parse(xhr.responseText);
                        console.log('error' + errorMessage.error.message)
                        $('#msjApiEliminar').text(errorMessage.error.message);
                        $('#overlay').css('visibility', 'hidden');
                    }
                });
            });
        });

        /* MODAL Actualizar Beneficiario */
        document.querySelectorAll('.btn-open-modal-updateBene').forEach(click => {
            // $("#SuccessAddBeneficiario").html('<div></div>');
            click.addEventListener('click', function () {
                const i = this.getAttribute('data-index');
                $('#updateCedBenficiario').val(beneficiariosjson[i].documentId);
                $('#UpdateSelectParentesco').val(beneficiariosjson[i].codeRelationship);
                $('#UpdatefechaNacimiento').val(beneficiariosjson[i].dateBirthday.split('T')[0]);
                $('#UpdateApellidosyNombres').val(beneficiariosjson[i].names);
            })
        });

        /* Actualizar Beneficiario */
        $('#FormUpdateBeneficiario').submit(function (event) {
            event.preventDefault();
            $('#ConfirmarEnvioUpdate').modal('show');
            $('#btnConfirmarEnvio').on('click', function () {                
                $('#overlay').css('visibility', 'visible');
                data = $('#FormUpdateBeneficiario').serialize();
                $.ajax({
                    url: "{{ route('exequial.beneficiarios.update', ['beneficiario' => ':id']) }}"
                        .replace(':id', $('updateCedBenficiario').val()),
                    type: 'PUT',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (response) {
                        //console.log(response);
                        localStorage.setItem('successMessage',
                            "Se Actualizó Beneficiario Exitosamente");
                        setTimeout(function () {
                            $('#overlay').css('visibility', 'hidden');
                        }, 7000);
                        location.reload();
                    },
                    error: function (e) {
                        console.log("en el error")
                        console.log(e)
                    }
                });
            });
        });


        /* Eliminar Titular */
        //cambiar el estado a false,

        /* Eliminar Beneficiario */
        $('.botonEliminarBene').on('click', function () {
            $("#SuccessAddBeneficiario").html('<div></div>');
            var button = $(this);
            $('#eliminarBeneficiario').modal('show');
            $('#msjApiEliminar').text(" ");
            $('#confirmarElimacionBene').on('click', function () {
                var fila = button.closest('tr');
                //var documentId = fila.find('input[name="documentId"]').val();
                rowIndex = fila.index();
                documentId = beneficiariosjson[rowIndex - 1].id;
                $('#overlay').css('visibility', 'visible');
                $.ajax({
                    url: "{{ route('exequial.beneficiarios.destroy', ['beneficiario' => ':id']) }}".replace(':id', documentId),
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (response) {
                        if (response == 200) {
                            localStorage.setItem('successMessage', "Registro Eliminado Exitosamente");
                            location.reload();
                            $('#overlay').css('visibility', 'hidden');
                        }
                    },
                    error: function (xhr, status, error) {
                        var errorMessage = JSON.parse(xhr.responseText);
                        $('#msjApiEliminar').text(errorMessage.error.message);
                        $('#overlay').css('visibility', 'hidden');
                    }
                });
            });
        });

        /* Prestar Servicio Form */
        $('.botonPrestarServicio').on('click', function () {
            $("#SuccessAddBeneficiario").html('<div></div>');
            i = $('.botonPrestarServicio').index($(this));
            if (i===0) {
                $('#cedulaFallecido').val(asociadojson.documentId);
                $('#nameFallecido').val(asociadojson.name);
                $('#dateInit').val(asociadojson.dateInit);
                $('#pscodePlan').val(asociadojson.codePlan)
                $('#psdiscount').val(asociadojson.discount)
                $('#pscodePlan').val(asociadojson.codePlan)
                $('#psobservation').val(asociadojson.observation)
                $('#ispastor').val('true')
            }else{
                //datos beneficiario
                var fila = $(this).closest('tr');
                var cedula = fila.find('td:eq(0)').text();
                var name = fila.find('td:eq(1)').text();
                var parentesco = fila.find('td:eq(2)').text();
                var fecNacimiento = fila.find('td:eq(3)').text();
                $('#cedulaFallecido').val(cedula);
                $('#parentescoServicio').val(parentesco);
                $("#nameFallecido").val(name);
                $("#fecNacFallecido").val(fecNacimiento);
                $('#dateInit').val(beneficiariosjson[fila.index()-1].dateEntry);
                $('#ispastor').val('false');                
            }
        });
        $('#FormularioPrestarServicio').submit(function (event) {
            console.log($(this).serialize())
            console.log("is pastorrrr? "+$('#ispastor').val())
            var form = this;
            if (!form.checkValidity()) {
                $("#FormularioPrestarServicio").addClass('was-validated');
                event.preventDefault();
                event.stopPropagation();
            }
            setTimeout(function () {
                            $('#overlay').css('visibility', 'hidden');
                        }, 7000);
        });
    });
</script>
@endsection