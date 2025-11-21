<x-base-layout>
    <x-warning />
    <x-success />
    <x-error />
    <div class="col-12">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="page-header-right ms-auto">

                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 d-flex justify-content-end align-items-center">
                        <form action="{{ route('cinco.retiros.show', ['calculoretiro' => 'ID']) }}" method="GET"
                            class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                            <label for="search-input" class="mb-0 me-2">Buscar:</label>
                            <input type="text" name="id" class="form-control form-control-sm" id="valueCedula"
                                placeholder="cédula" aria-controls="customerList">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card stretch stretch-full ">
            <div class="card-header">
                <h5 class="card-title">Retiro por año</h5>
                <a href="#CardAddCalculo" id="btnAddCalculo" class="btn btn-success">
                    <i class="feather-plus me-2"></i>
                    <span>Crear Nuevo</span>
                </a>
            </div>
            <div class="card-body custom-card-action p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr class="border-b">
                                <th>AÑO</th>
                                <th>VALOR</th>
                                <th>PLUS</th>
                                <th>EXTRA PLUS</th>
                                <th class="text-end">ACCIÓN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tabla as $r)
                                <tr>
                                    <td><span class="badge bg-gray-200 text-dark">{{ $r->anio }}</span></td>
                                    <td>${{ number_format($r->valor) }}</td>
                                    <td><span
                                            class="badge bg-soft-primary text-primary">${{ number_format($r->plus) }}</span>
                                    </td>
                                    <td>0</td>
                                    <td class="hstack justify-content-end gap-4 text-end">
                                        <div class="dropdown open">
                                            <a href="javascript:void(0)" class="avatar-text avatar-md"
                                                data-bs-toggle="dropdown" data-bs-offset="0,21">
                                                <i class="feather feather-more-horizontal"></i>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item">
                                                        <i class="feather feather-edit-3 me-3"></i>
                                                        <span>Editar</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <button type="submit" class="dropdown-item btnEliminar">
                                                        <i class="feather feather-trash-2 me-3"></i>
                                                        <span>Eliminar</span>
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Opciones de liquidación</h5>
                <a href="{{ route('cinco.condicionRetiros.create') }}" id="btnAddCalculo" class="btn btn-warning">
                    <i class="bi bi-pencil-square me-2"></i>
                    <span>Editar Opciones</span>
                </a>
            </div>
            <div class="card-body custom-card-action p-0">
                <div class="table-responsive">
                    <table class="table m-2">
                        <tbody>
                            @foreach ($retopciones as $tipo => $opciones)
                                <tr>
                                    <th colspan="5">
                                        {{ $tipo == 1 ? 'BENEFICIOS' : ($tipo == 2 ? 'BASE RETENCION' : 'SALDOS A FAVOR') }}
                                    </th>
                                </tr>                                
                                @foreach ($opciones as $opcion)
                                    <tr>
                                        <td>{{ $opcion->nombre }}</td>
                                    </tr>
                                @endforeach
                                <tr><td></td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="CardAddCalculo" class="col-lg-12" style="display: none;">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="fw-bold mb-0">
                    <span class="d-block mb-2">Agregar Cálculo para liquidación </span>
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('cinco.condicionRetiros.store') }}" id="formAddCondicion"
                    novalidate>
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="col-lg-3 mb-4">
                            <label class="form-label">Año<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="anio" required>
                        </div>
                        <div class="col-lg-3 mb-4">
                            <label class="form-label">Valor Base<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="valor" required>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-4">
                            <label class="form-label">Plus<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="valorplus">
                        </div>
                        <div class="col-lg-3 mb-4">
                            <label class="form-label">Extra Plus<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="observacionesbene">
                        </div>
                    </div>
                    <div class="d-flex flex-row-reverse gap-2 mt-2">
                        <button class="btn btn-success mt-4" data-bs-toggle="tooltip" type="submit"
                            data-bs-original-title="crear">
                            <i class="feather-plus me-2"></i>
                            <span>Agregar</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#btnAddCalculo').on('click', function(e) {
                console.log('Button clicked');
                $('#CardAddCalculo').show();
                $('#CardAddCalculo').slideDown('fast', function() {
                    $('html, body').animate({
                        scrollTop: $('#CardAddCalculo').offset().top
                    }, 500);
                });
            });
            $('#formAddCondicion').submit(function(event) {
                var form = this;
                if (!form.checkValidity()) {
                    $(form).addClass('was-validated');
                    event.preventDefault();
                    event.stopPropagation();
                }
            });
        });
    </script>
</x-base-layout>
