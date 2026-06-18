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
                        <h5>Editar indicador #{{ $indicador->id }}</h5>
                        <form id="formUpdateIndicador"
                            action="{{ route('indicators.indicadores.update', $indicador->id) }}" method="POST"
                            novalidate>

                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-6 mb-4">
                                    <label class="form-label">
                                        Nombre del indicador <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" required
                                        value="{{ old('name', $indicador->nombre) }}" class="form-control">
                                </div>

                                <div class="col-6 mb-4">
                                    <label class="form-label">
                                        Formula del indicador <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="calculation" required
                                        value="{{ old('calculation', $indicador->calculo) }}" class="form-control">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-2 mb-4">
                                    <label class="form-label">
                                        Meta del indicador <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="goal" required
                                        value="{{ old('goal', $indicador->meta) }}" class="form-control">
                                </div>

                                <div class="col-3 mb-4">
                                    <label class="form-label">
                                        Frecuencia de medición <span class="text-danger">*</span>
                                    </label>

                                    <select name="frecuencia" class="form-control select2">
                                        <option value="Mensual"
                                            {{ $indicador->frecuencia == 'Mensual' ? 'selected' : '' }}>
                                            Mensual
                                        </option>

                                        <option value="Semestral"
                                            {{ $indicador->frecuencia == 'Semestral' ? 'selected' : '' }}>
                                            Semestral
                                        </option>

                                        <option value="Trimestral"
                                            {{ $indicador->frecuencia == 'Trimestral' ? 'selected' : '' }}>
                                            Trimestral
                                        </option>

                                        <option value="Anual"
                                            {{ $indicador->frecuencia == 'Anual' ? 'selected' : '' }}>
                                            Anual
                                        </option>
                                    </select>
                                </div>

                                <div class="col-3 mb-4">
                                    <label class="form-label">
                                        Área <span class="text-danger">*</span>
                                    </label>

                                    <select name="area" class="form-control select2">
                                        @foreach ($areas as $area)
                                            <option value="{{ $area->id }}"
                                                {{ $indicador->area == $area->id ? 'selected' : '' }}>
                                                {{ $area->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-4 mb-4">
                                    <label class="form-label">
                                        Responsable <span class="text-danger">*</span>
                                    </label>                                    
                                    <select name="responsible" class="form-control select2">
                                        @foreach ($responsables as $r)
                                            <option value="{{ $r->id }}" {{ (string)$indicador->responsable === (string) $r->id ? 'selected' : '' }}>
                                                {{ $r->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">
                                    Consulta SQL del indicador <span class="text-danger">*</span>
                                </label>

                                <textarea name="consultasql" rows="6" required class="form-control">{{ old('consultasql', $indicador->consulta_bd) }}</textarea>
                            </div>

                            <div class="w-100 d-flex justify-content-end">
                                <button type="submit" class="btn btn-warning">
                                    <i class="feather-save me-2"></i>
                                    Actualizar Indicador
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
