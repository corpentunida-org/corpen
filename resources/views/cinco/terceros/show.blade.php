<x-base-layout>
    <x-warning />
    <x-success />
    @section('titlepage', 'Fechas Tercero')
    <div class="col-12">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <form method="GET" class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                            <label for="search-input" class="mb-0 me-2">Buscar:</label>
                            <input type="text" name="id" class="form-control form-control-sm" id="valueCedula"
                                placeholder="nombre titular" aria-controls="customerList">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                        </form>
                    </div>
                    <div class="col-sm-12 col-md-6 d-flex justify-content-end align-items-center">
                        <form method="GET" class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                            <label for="search-input" class="mb-0 me-2">Buscar:</label>
                            <input type="text" name="id" class="form-control form-control-sm" id="valueCedula"
                                placeholder="cédula titular" aria-controls="customerList">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body personal-info">
                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">{{ $tercero->Cod_Ter }}</span>
                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">Información Personal:</span>
                    </h5>
                    @if ($tercero->verificado)
                        <div class="badge bg-soft-success text-success">Verificado</div>
                    @else
                        <div class="badge bg-soft-danger text-danger">Sin Verificar</div>
                    @endif
                </div>
                <div class="row mb-4 align-items-center">
                    <div class="col-lg-4">
                        <label for="fullnameInput" class="fw-semibold">Nombre: </label>
                    </div>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <div class="input-group-text"><i class="feather-user"></i></div>
                            <input type="text" value="{{ $tercero->Nom_Ter }}" class="form-control" disabled>
                        </div>
                    </div>
                </div>
                <form action="{{ route('cinco.tercero.update', $tercero->Cod_Ter) }}" method="POST"
                    id="formEditfechasCinco" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="row mb-4 align-items-center">
                        <div class="col-lg-4">
                            <label class="fw-semibold">Fecha Ingreso Corpentunida: </label>
                        </div>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <div class="input-group-text"><i class="bi bi-calendar2"></i></div>
                                <input type="date" class="form-control" name="Fec_Ing"
                                    placeholder="Fecha Ingreso Corpentunida" value="{{ $tercero->Fec_Ing }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4 align-items-center">
                        <div class="col-lg-4">
                            <label class="fw-semibold">Fecha Primer Aporte: </label>
                        </div>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <div class="input-group-text"><i class="bi bi-calendar2"></i></div>
                                <input type="date" class="form-control" name="Fec_Aport"
                                    placeholder="Fecha Primer Aporte" value="{{ $tercero->Fec_Aport }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4 align-items-center">
                        <div class="col-lg-4">
                            <label class="fw-semibold">Fecha Ingreso Ministerio: </label>
                        </div>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <div class="input-group-text"><i class="bi bi-calendar2"></i></div>
                                <input type="date" class="form-control" name="Fec_Minis"
                                    value="{{ $tercero->Fec_Minis }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4 align-items-center">
                        <div class="col-lg-4">
                            <label for="aboutInput" class="fw-semibold">Observación: </label>
                        </div>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <div class="input-group-text"><i class="feather-type"></i></div>
                                <input type="text" class="form-control" value="{{ $tercero->observacion }}" placeholder="Observación de actualización" name="observacion" required>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-row-reverse gap-2 mt-2">
                        <button class="btn btn-warning mt-4" data-bs-toggle="tooltip" title="Timesheets"
                            type="submit">
                            <i class="feather-plus me-2"></i>
                            <span>Editar Fechas</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $('#formEditfechasCinco').submit(function(event) {
            var form = this;
            if (!form.checkValidity()) {
                $(form).addClass('was-validated');
                event.preventDefault();
                event.stopPropagation();
            } else {
                console.log($(this).serialize());
            }
        });
    </script>

</x-base-layout>
