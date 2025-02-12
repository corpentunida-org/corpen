<x-base-layout>
    <style>
        .uppercase-input {
            text-transform: uppercase;
        }
    </style>
    @section('titlepage', 'Crear Convenio')
    <x-error />
    <div class="col-xxl-12 col-xl-12">
        <div class="card border-top-0">
            <div class="card-header p-0">
                <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#securityTab"
                            aria-selected="true">Copiar Convenio</a>
                    </li>
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" data-bs-target="#taskTab"
                            aria-selected="true">Crear Convenio</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 active show" id="securityTab" role="tabpanel">
                    <div class="col-lg-12 p-4">
                        <form method="POST" action="{{ route('seguros.convenio.store') }}" id="formAddPlan" novalidate>
                            @csrf
                            @method('POST')
                            <div class="mb-4">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <label class="form-label">Id Convenio<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control uppercase-input" name="name"
                                            required>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Nombre Convenio<span class="text-danger">*</span></label>
                                        <select class="form-control" name="convenio" required>
                                            
                                        </select>
                                    </div>
                                    <div class="col-lg-2">
                                        <label class="form-label">Año<span class="text-danger">*</span></label>
                                        <input type="number" class="form-control uppercase-input" name="name"required>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="planes-container">
                                <div class="mb-4 cobertura-row">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label class="form-label">Nombre Plan<span class="text-danger">*</span></label>
                                            <select class="form-control" name="cobertura_id[]" id="cobertura_id" required></select>
                                        </div>
                                        <div class="col-lg-3">
                                            <label class="form-label">Valor Asegurado<span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="valorAsegurado[]" required>
                                        </div>
                                        <div class="col-lg-3">
                                            <label class="form-label">Valor Prima<span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="valorPrima[]" required>
                                        </div>
                                        <div class="col-lg-3">
                                            <label class="form-label">Condicion<span class="text-danger">*</span></label>
                                            <select class="form-control"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <a href="javascript:void(0);" class="btn btn-primary wd-200" id="add-cobertura">
                                    <i class="feather-plus me-2"></i>
                                    <span>Agregar plan</span>
                                </a>
                            </div>
                            {{-- hidden --}}
                            <div class="d-flex flex-row-reverse gap-2 mt-2">
                                <button class="btn btn-success mt-4" data-bs-toggle="tooltip" title="Timesheets"
                                    type="submit">
                                    <i class="feather-plus me-2"></i>
                                    <span>Agregar Plan</span>
                                </button>
                            </div>
                        </form>
                        <script>
                            $(document).ready(function() {
                                $('#formAddPlan').submit(function(event) {
                                    var form = this;
                                    if (!form.checkValidity()) {
                                        $(form).addClass('was-validated');
                                        event.preventDefault();
                                        event.stopPropagation();
                                    } 
                                });
                                $('#add-cobertura').click(function() {
                                    const container = document.getElementById('planes-container');
                                    const coberturaRow = document.querySelector('.cobertura-row');

                                    const clonedRow = coberturaRow.cloneNode(true);

                                    const inputs = clonedRow.querySelectorAll('input');
                                    inputs.forEach(input => input.value = '');

                                    container.appendChild(clonedRow);
                                });
                            });
                        </script>
                    </div>
                </div>
                <div class="tab-pane fade p-4" id="taskTab" role="tabpanel">
                    <div class="col-lg-12 p-4">
                        <form method="POST" action="{{ route('seguros.convenio.store') }}" id="formAddConvenio" novalidate>
                            @csrf
                            @method('POST')
                                 
                                <div class="row mb-4">
                                    <div class="col-lg-3">
                                        <label class="form-label">Id Convenio<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control uppercase-input" name="nameConvenio"required>
                                    </div>
                                    <div class="col-lg-7">
                                        <label class="form-label">Nombre Convenio<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control uppercase-input" name="nameConvenio"required>
                                    </div>
                                    <div class="col-lg-2">
                                        <label class="form-label">Año<span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="anioConvenio"required>
                                    </div>                                    
                                </div>
                                
                                <div class="row mb-4">
                                    <div class="col-lg-4">
                                        <label class="form-label">Proveedor<span class="text-danger">*</span></label>
                                        <select class="form-control" name="convenio" id="convenio_id" required>
                                            <option value="1">Allianz Seguros de Vida S.A</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-4">
                                        <label class="form-label">Fecha Inicio<span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="fechaInicio"required>
                                    </div>                                    
                                    <div class="col-lg-4">
                                        <label class="form-label">Fecha Fin<span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="fechaInicio"required>
                                    </div>                                    
                                </div>
                            
                            
                            <div class="d-flex flex-row-reverse gap-2 mt-2">
                                <button class="btn btn-success mt-4" data-bs-toggle="tooltip" title="Timesheets"
                                    type="submit">
                                    <i class="feather-plus me-2"></i>
                                    <span>Agregar Convenio</span>
                                </button>
                            </div>
                        </form>
                        <script>
                            $(document).ready(function() {
                                $('#convenio_id').on('change', function() {
                                    var convenioId = $(this).val();

                                    $('#cobertura_id').empty();                                    

                                    if (convenioId) {
                                        var url = '{{ route("seguros.cobertura.show", ":cobertura") }}';
            
                                        $.ajax({
                                            url: url.replace(':cobertura', convenioId),                                            
                                            method: 'GET',
                                            success: function(data) {
                                                console.log(data);
                                                data.forEach(function(cobertura) {
                                                    $('#cobertura_id').append('<option value="' + cobertura
                                                        .id + '">' + cobertura.nombre + '</option>');
                                                });
                                            },
                                            error: function() {
                                                alert('Hubo un error al cargar las coberturas.');
                                            }
                                        });
                                    }
                                });

                                $('#formAddPlan').submit(function(event) {
                                    var form = this;
                                    if (!form.checkValidity()) {
                                        $(form).addClass('was-validated');
                                        event.preventDefault();
                                        event.stopPropagation();
                                    } else {
                                        var totalValorPrima = 0;
                                        $('input[name="valorPrima[]"]').each(function() {
                                            var valor = parseFloat($(this).val()) || 0;
                                            totalValorPrima += valor;
                                        });

                                        var totalPrima = parseFloat($('input[name="prima"]').val());

                                        if (isNaN(totalPrima) || totalPrima <= 0) {
                                            $('#prima').addClass('is-invalid');
                                            event.preventDefault();
                                            return;
                                        } else {
                                            $('#prima').removeClass('is-invalid');
                                            $('#primaError').text('');
                                        }
                                    }
                                });
                                $('#add-cobertura').click(function() {
                                    const container = document.getElementById('coberturas-container');
                                    const coberturaRow = document.querySelector('.cobertura-row');

                                    const clonedRow = coberturaRow.cloneNode(true);

                                    const inputs = clonedRow.querySelectorAll('input');
                                    inputs.forEach(input => input.value = '');

                                    container.appendChild(clonedRow);
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-base-layout>