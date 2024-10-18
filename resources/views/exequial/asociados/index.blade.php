<style>
    #overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.7); /* Semi-transparent background */
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999; /* Make sure it's on top of other content */
        visibility: hidden;
    }
    .required-asterisk{
        color: red;
    }
    .uppercase-input {
        text-transform: uppercase;
    }
</style>

@extends('layouts.appTemplate')
@section('titlepage', 'Exequiales')
@section('titleView', 'Titular - Beneficiarios')
@section('titlenav', 'EXEQUIALES')
@section('contentpage')

<div class="card-body">
    @if(session('messageTit'))
        <div class="alert alert-danger p-2" role="alert">
            {{ session('messageTit') }}.
            <a data-bs-toggle="modal" data-bs-target="#addTitular" class="alert-link"
                style="text-decoration: underline;">¿Desea agregar?... Clik aqui </a>
        </div>
    @endif

    <div class="container mt-3 w-50">
        <div id="overlay">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <form action="{{ route('exequial.beneficiarios.show', ['beneficiario' => 'ID']) }}" method="GET">                                             
                    <label>Buscar titular por número de cédula</label>
                    <input type="text" name="id" placeholder="Cédula" class="form-control p-3" id="valueCedula" required>
                </form>
                <div class="invalid-feedback">Ingrese un dato</div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 d-flex justify-content-center align-items-center">
                <button type="button" class="btn btn-primary w-100" id="btn-buscador">Buscar</button>
            </div>
            <div class="col-md-6 d-flex justify-content-center align-items-center">
            
            {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalVerCedula"> --}}
            
            @can('exequial.asociados.store')
            <button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
                data-bs-target="#addTitular" style="color: black" id="btn-add-titular">
                Agregar Titular
            </button>
            @endcan
            </div>
        </div> 
    </div>
</div>

{{-- Modal Filtro Cedula --}}
<div class="modal fade" id="ModalVerCedula" tabindex="-1" aria-labelledby="ModalVerCedula" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalInsert">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal No se añade Titular --}}
<div class="modal fade" id="ModalNoAñadido" tabindex="-1" aria-labelledby="ModalNoAñadido" aria-hidden="true">
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
                                <select class="form-control" name="parentesco"
                                    aria-label="Default select example" id="selectParentesco"></select>
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
                                <select class="form-control" name="parentesco"
                                    aria-label="Default select example" id="selectDistrito"></select>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success">Guardar</button>
                </div>
            </div>
        </div>
    </div>

{{-- Agregar Datos del titular API --}}
<div class="modal fade" id="addTitular" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addTitular" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="needs-validation" action="" id="FormularioAddTitular" method="post"
                novalidate>
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Agregar Titular</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 pr-1">
                            <div class="form-group">
                                <label for="cedula">Cédula <span class="required-asterisk">*</span></label>
                                <input type="number" class="form-control" placeholder="Cedula" id="cedula" name="documentId" required min="1" step="1">
                                <div class="invalid-feedback">Ingrese una cédula válida.</div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Nombres</label>
                                <input type="text" class="form-control uppercase-input" placeholder="Nombres y Apellidos" id="name">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Plan</label>
                                <select class="form-control" name="plan" id="selectPlanes" required></select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Valor Plan</label>
                                <div class="form-control" id="valorPlan"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Descuento</label>
                                <div class="input-group">
                                    <input class="form-control" type="text" name="descuento">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Obsvervaciones <span class="required-asterisk">*</span></label>
                                <input class="form-control uppercase-input" type="text" name="observaciones" required>
                                <div class="invalid-feedback">
                                    Digite una observación.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <p class="text-danger col-md-12" id="msjAjax"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="botonAddTitular" class="btn btn-success" style=color:black;>Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        /* Get Planes */
        $.ajax({
            url: "{{ route('exequial.plansall') }}",
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var select = $('#selectPlanes');
                for (var i = response.length - 1; i >= 0; i--) {
                    var data = response[i];
                    select.append($('<option>', {
                        value: data.code,
                        text: data.name,
                        'data-value': data.value
                    }));
                }
                $('#valorPlan').html(response[3].value);
                $('#selectPlanes').change(function() {
                    var valorSeleccionado = $(this).find(':selected').data('value');;
                    $('#valorPlan').html(valorSeleccionado);
                });
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
            
        $('#valueCedula').on('change', function() {
            var newValue = $(this).val();
            $('#cedula').val(newValue);
        });

        $('#cedula').on('change', function() {
            var newValue = $(this).val();
            $('#cedula').val(newValue);
            $.ajax({
                url: "{{ route('exequial.terceros.show', ['tercero' => ':id']) }}".replace(':id', newValue),
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log(response)
                    $('#name').val(response.name);
                },
                error: function(xhr, status, error) {
                    $('#name').val(" ");
                }
            });
        });

        $('#btn-buscador').click(function() {                      
            var inputField = $('#valueCedula');          
            if (inputField.val()) {
                inputField.addClass('was-validated').removeClass('is-invalid');
                $('#overlay').css('visibility', 'visible');    
                window.location.href = "/beneficiarios/ID?id=" + inputField.val();
                setTimeout(function() {
                    $('#overlay').css('visibility', 'hidden');
                }, 10000);
            } else {
                inputField.removeClass('was-validated').addClass('is-invalid');                    
            }
        });  
        $('#btn-add-titular').click(function() {     
            $('#msjAjax').html('<p> </p>')
        });
        /* Añadir Titular */
        $('#FormularioAddTitular').submit(function(event) {
            event.preventDefault();
            var form = document.getElementById('FormularioAddTitular');
            if (form.checkValidity()) {
                $('#overlay').css('visibility', 'visible');
                var formData = $(this).serialize();
                //console.log(formData)
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                docid = $('input[name="documentId"]').val()
                $.ajax({
                    url: "{{ route('exequial.asociados.store') }}",
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        localStorage.setItem('successMessageTit', "Registro Añadido Exitosamente");
                        setTimeout(function() {
                            $('#overlay').css('visibility', 'hidden');
                        }, 7000);
                        window.location.href = "/beneficiarios/ID?id=" + docid;
                        // location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        $('#overlay').css('visibility', 'hidden');
                        $('#FormularioAddTitular')[0].reset();
                        $('#valorPlan').text("0")
                        var response = JSON.parse(xhr.responseText);
                        $('#msjAjax').html('<p> </p>')
                        if(response.code==2){
                            $('#msjAjax').html('<p>'+response.error+'</p><p><a href="/beneficiarios/ID?id='+docid+'">VER TITULAR...'+docid+'</a></p>')
                        }
                        else{
                            $('#msjAjax').html('<p>'+response.error+'</p>')                                
                        }                            
                    }
                });
            } else {
                $("#FormularioAddTitular").addClass('was-validated');
                event.preventDefault();
                event.stopPropagation();
            }
        });
    });
</script>

@endsection
