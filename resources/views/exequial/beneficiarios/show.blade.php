@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
<div class="mb-4 d-flex align-items-center justify-content-between">
    <h5 class="fw-bold mb-0">
        <span class="d-block mb-2">Información:</span>
    </h5>
    <div class="d-flex gap-2">
        @if ($asociado['stade'])
            @can('exequial.beneficiarios.store')
                <a href="{{ route('exequial.beneficiarios.create', ['asociado' => $asociado['documentId']]) }}"
                    class="btn btn-success">
                    <i class="feather-plus me-2"></i>
                    <span>Agregar Beneficiario</span>
                </a>
            @endcan
        @endif
    </div>
</div>

<div class="table-responsive">
    <div id="proposalList_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-hover dataTable no-footer" id="proposalList"
                    aria-describedby="proposalList_info">
                    <thead>
                        <tr>
                            <th>Cédula</th>
                            <th>Nombre</th>
                            <th>Parentesco</th>
                            <th>Fecha Nacimiento</th>
                            <th>Edad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($beneficiarios as $beneficiario)
                            @if ($beneficiario['type'] == 'A')
                                <tr>
                                    <td>{{ $beneficiario['documentId'] }}</td>
                                    <td>{{ $beneficiario['names'] }}</td>
                                    <td>{{ $beneficiario['relationship'] }}</td>
                                    <td>{{ $beneficiario['dateBirthday'] }}</td>
                                    @php
                                        $fecNac = new DateTime($beneficiario['dateBirthday']);
                                        $fechaActual = new DateTime();
                                        $diferencia = $fecNac->diff($fechaActual);
                                        $edad = $diferencia->y;
                                    @endphp
                                    <td>{{ $edad }}</td>
                                    <td>
                                        
                                            <div class="dropdown">
                                                <a href="javascript:void(0)" class="avatar-text avatar-md"
                                                    data-bs-toggle="dropdown" data-bs-offset="0,21">
                                                    <i class="feather feather-more-horizontal"></i>
                                                </a>
                                                @if ($asociado['stade'])
                                                    <ul class="dropdown-menu">
                                                        @can('exequial.beneficiarios.update')
                                                            <li>
                                                                <form
                                                                    action="{{ route('exequial.beneficiarios.edit', ['beneficiario' => $beneficiario['documentId']]) }}"
                                                                    method="GET">
                                                                    <input type="hidden" name="asociadoid"
                                                                        value="{{ $asociado['documentId'] }}">
                                                                    <input type="hidden" name="name"
                                                                        value="{{ $beneficiario['names'] }}">
                                                                    <input type="hidden" name="relationship"
                                                                        value="{{ $beneficiario['relationship'] }}">
                                                                    <input type="hidden" name="dateBirthday"
                                                                        value="{{ $beneficiario['dateBirthday'] }}">
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="feather feather-edit-3 me-3"></i>
                                                                        <span>Editar</span>
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endcan
                                                        @can('exequial.prestarServicio.store')
                                                            <li class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item btn-prestarservicio" href="javascript:void(0)"
                                                                    data-bs-toggle="offcanvas" data-bs-target="#proposalSent"
                                                                    data-index={{ $loop->index }}>
                                                                    <i class="fa-regular fa-bell"></i>
                                                                    <span>Prestar Servicio</span>
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('exequial.beneficiarios.destroy')
                                                            <li class="dropdown-divider"></li>
                                                            <li>
                                                                <form
                                                                    action="{{ route('exequial.beneficiarios.destroy', ['beneficiario' => $beneficiario['id']]) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <input type="hidden" name="documentid"
                                                                        value="{{ $asociado['documentId'] }}">
                                                                    <input type="hidden" name="id" value="{{ $beneficiario['id'] }}">
                                                                    <input type="hidden" name="beneid"
                                                                        value="{{ $beneficiario['documentId'] }}">
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="feather feather-trash-2 me-3"></i>
                                                                        <span>Eliminar</span>
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endcan
                                                    </ul>
                                                @endif
                                            </div>                                        
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="card stretch stretch-full" id="cardServiciosPrestados">
    <div class="card-header">
        <h5 class="card-title">Services</h5>
        <a href="javascript:void(0);" class="btn btn-md btn-light-brand">
            <i class="feather-plus me-2"></i>
            <span>Servicios Prestados</span>
        </a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-hover dataTable no-footer" id="proposalList"
                    aria-describedby="proposalList_info">
                    <thead>
                        <tr>
                            <th class="text-start sorting" tabindex="0" aria-controls="proposalList" rowspan="1"
                                colspan="1" aria-label="Proposal: activate to sort column ascending"
                                style="width: 67.9844px;">
                                Cédula</th>
                            <th class="sorting" tabindex="0" aria-controls="proposalList" rowspan="1" colspan="1"
                                aria-label="Client: activate to sort column ascending" style="width: 247.75px;">
                                Apellidos Nombre</th>
                            <th class="sorting" tabindex="0" aria-controls="proposalList" rowspan="1" colspan="1"
                                aria-label="Amount: activate to sort column ascending" style="width: 94.8281px;">
                                Parentesco</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalRegistros = 0;
                        @endphp
                        @foreach ($beneficiarios as $beneficiario)
                            @if ($beneficiario['type'] != 'A')
                                <tr class="single-item odd">
                                    <td>{{ $beneficiario['documentId'] }}</td>
                                    <td>{{ $beneficiario['names'] }}</td>
                                    <td>{{ $beneficiario['relationship'] }}</td>
                                </tr>
                                @php
                                    $totalRegistros++;
                                @endphp
                            @endif
                        @endforeach
                    </tbody>
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

<div class="offcanvas offcanvas-end modal-dialog-scrollable" tabindex="-1" id="proposalSent">
    {{-- <div class="offcanvas-header ht-80 px-4 border-bottom border-gray-5"> --}}
        <div class="offcanvas-header d-flex justify-content-between border-bottom border-gray-5">
            <h2 class="fs-16 fw-bold pt-2">PRESTAR SERVICIO</h2>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('exequial.prestarServicio.store') }}" id="FormularioPrestarServicio"
            novalidate>
            @csrf
            <div
                class="py-3 px-4 d-flex justify-content-between align-items-center border-bottom border-bottom-dashed border-gray-5 bg-gray-100">
                <div>
                    <input class="fw-bold text-dark p-0 m-0 border border-0 bg-gray-100" id="nameBeneficiario"
                        style="width: 100%;" name="nameBeneficiary" readonly>
                    <input class="fs-12 fw-medium text-muted p-0 m-0 border border-0 bg-gray-100" id="idBeneficiario"
                        name="cedulaFallecido" readonly>
                </div>
            </div>
            <input type="hidden" name="pastor" id="ispastor" value="false">
            <input type="hidden" id="cedulaTitular" name="cedulaTitular" value="{{ $asociado['documentId'] }}">
            <input type="hidden" value="{{ $asociado['name'] }}" name="nameTitular">
            <input type="hidden" id="parentescoServicio" name="parentesco">
            <input type="hidden" name="fecNacFallecido" id="fecNacFallecido">
            <input type="hidden" name="dateInit" id="dateInit">

            <div class="offcanvas-body">
                <div class="form-group mb-4">
                    <label class="form-label">Lugar Fallecimiento<span class="text-danger">*</span></label>
                    <input type="text" class="form-control uppercase-input" name="lugarFallecimiento" required>
                </div>
                <div class="row">
                    <!-- <div class="form-group mb-4">
                    <label class="form-label">To: <span class="text-danger">*</span></label>
                    <input class="form-control" name="tomailcontent" value="wrapcode.info@gmail.com" placeholder="To..."
                        required>
                </div> -->
                    <div class="form-group col-lg-6 mb-4">
                        <label class="form-label">Fecha<span class="text-danger">*</span></label>
                        <input type="date" class="form-control datepicker-input" name="fechaFallecimiento" required>
                    </div>
                    <div class="form-group col-lg-6 mb-4">
                        <label class="form-label">Hora<span class="text-danger">*</span></label>
                        <input type="time" class="form-control" name="horaFallecimiento" required>
                    </div>
                </div>
                <div class="form-group mb-4">
                    <label class="form-label">Contacto 1<span class="text-danger">*</span></label>
                    <input type="text" class="form-control uppercase-input" name="contacto">
                </div>
                <div class="form-group mb-4">
                    <label class="form-label">Contacto 2</label>
                    <input type="text" class="form-control uppercase-input" name="contacto2">
                </div>
                <div class="row">
                    <div class="form-group col-lg-6 mb-4">
                        <label class="form-label">Telefono 1<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="telefonoContacto">
                    </div>
                    <div class="form-group col-lg-6 mb-4">
                        <label class="form-label">Telefono 2</label>
                        <input type="number" class="form-control" name="telefonoContacto2">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-6 mb-6">
                        <label class="form-label">Factura<span class="text-danger">*</span></label>
                        <input type="text" class="form-control uppercase-input" name="factura">
                    </div>
                    <div class="form-group col-lg-6 mb-6">
                        <label class="form-label">Valor<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="valor">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-6 mb-6">
                        <label class="form-label">Género<span class="text-danger">*</span></label>
                        <select class="form-control" name="genero">
                            <option value="F">Femenino</option>
                            <option value="M">Masculino</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-6 mb-6">
                        <label class="form-label">Traslado<span class="text-danger">*</span></label>
                        <select class="form-control" name="traslado">
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="px-4 gap-2 d-flex align-items-center ht-80 border border-end-0 border-gray-2">
                <a href="javascript:void(0);" class="btn btn-danger w-50" data-bs-dismiss="offcanvas">Cancel</a>
                <button type="submit" class="btn btn-success w-50">Prestar Servicio</button>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            var beneficiariosjson = @json($beneficiarios);
            $(document).on('click', '.btn-prestarservicio', function () {
                var i = $(this).data('index');
                $('#nameBeneficiario').val(beneficiariosjson[i].names);
                $('#idBeneficiario').val(beneficiariosjson[i].documentId);
                $('#parentescoServicio').val(beneficiariosjson[i].codeRelationship);
                $("#fecNacFallecido").val(beneficiariosjson[i].dateBirthday);
                $("#dateInit").val(beneficiariosjson[i].dateEntry);
            });
            $('#FormularioPrestarServicio').submit(function (event) {
                var form = this;
                if (!form.checkValidity()) {
                    $(form).addClass('was-validated');
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    console.log($(this).serialize());
                }
            });
        });
    </script>