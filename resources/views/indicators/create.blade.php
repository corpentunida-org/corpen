<x-base-layout>
<x-error />
    @section('titlepage', 'Configuración de Indicadores')
    <div class="col-12">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="page-header-right ms-auto">
                            <div class="page-header-right-items">
                                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                                    <a href="{{ route('indicators.indicadores.index') }}"
                                        class="btn btn-light-primary bg-soft-primary">
                                        <i class="feather-list me-2"></i>
                                        <span>Volver a Indicadores</span>
                                    </a>
                                </div>
                            </div>
                            <div class="d-md-none d-flex align-items-center">
                                <a href="javascript:void(0)" class="page-header-right-open-toggle">
                                    <i class="feather-align-right fs-20"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 d-flex justify-content-end align-items-center">

                    </div>
                </div>
                <hr>
                <div class="row mt-4 justify-content-center">
                    <div class="col-sm-12 col-md-8">
                        <h5>Crear nuevo parámetro para indicadores</h5>
                        <form id="formCreateIndicador" action="{{ route('indicators.indicadores.store') }}"
                            method="POST" novalidate>
                            @csrf
                            <div class="row">
                                <div class="col-6 mb-4">
                                    <label for="" class="form-label">Nombre del indicador<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="" required
                                        placeholder="Ej: Porcentaje de cumplimiento de metas" class="form-control">
                                </div>
                                <div class="col-6 mb-4">
                                    <label for="" class="form-label">Formula del indicador<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="calculation" id="" required
                                        placeholder="Ej: (cumplimiento / metas) * 100" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-2 mb-4">
                                    <label for="" class="form-label">Meta del indicador<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="goal" id="" required
                                        placeholder="≥80%" class="form-control">
                                </div>
                                <div class="col-3 mb-4">
                                    <label for="" class="form-label">Frecuencia de medición<span
                                            class="text-danger">*</span></label>
                                    <select name="frecuencia" class="form-control select2">
                                        <option value="" disabled selected>Seleccione una frecuencia</option>
                                        <option value="Mensual">Mensual</option>
                                        <option value="Semestral">Semestral</option>
                                        <option value="Trimestral">Trimestral</option>
                                        <option value="Anual">Anual</option>
                                    </select>
                                </div>
                                <div class="col-3 mb-4">
                                    <label for="" class="form-label">Area<span class="text-danger">*</span></label>
                                    <select name="area" class="form-control select2">
                                        <option value="" disabled selected>Seleccione un área</option>
                                        @foreach ($areas as $area)
                                            <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4 mb-4">
                                    <label for="" class="form-label">Responsable<span class="text-danger">*</span></label>
                                    <select name="responsible" class="form-control select2">
                                        <option value="" disabled selected>Seleccione un responsable</option>
                                        @foreach ($responsables as $r)
                                            <option value="{{ $r->id }}">{{ $r->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="" class="form-label">Consulta SQL del indicador<span
                                        class="text-danger">*</span></label>
                                <textarea name="consultasql" id="" required
                                    placeholder="Ej: select sum(cumplimiento) / count(*) * 100 from metas where year = 2026" rows="4"
                                    class="form-control"></textarea>
                            </div>
                            <div class="w-100 d-flex justify-content-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="feather-plus me-2"></i>
                                    Crear Indicador
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                allowClear: true,
                width: '100%'
            });
        });
    </script>
</x-base-layout>
