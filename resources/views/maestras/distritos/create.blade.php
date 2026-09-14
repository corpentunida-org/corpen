<x-base-layout>
    @section('titlepage', 'Registrar Nuevo Distrito')

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>¡Atención!</strong> Por favor, corrige los errores marcados en el formulario.
        </div>
    @endif

    <div class="col-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Formulario de Registro de Distrito</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('maestras.distrito.store') }}" method="POST" novalidate>
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Código <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('cod_dist') is-invalid @enderror"
                                name="cod_dist" value="{{ old('cod_dist') }}" required min="1">
                            @error('cod_dist')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase @error('nom_dist') is-invalid @enderror"
                                name="nom_dist" value="{{ old('nom_dist') }}" required>
                            @error('nom_dist')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Detalle</label>
                            <input type="text" class="form-control @error('detalle') is-invalid @enderror"
                                name="detalle" value="{{ old('detalle') }}">
                            @error('detalle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Compuesto</label>
                            <input type="text" class="form-control @error('compuest') is-invalid @enderror"
                                name="compuest" value="{{ old('compuest') }}">
                            @error('compuest')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3">Junta del Presbiterio</h6>

                    <div class="row">
                        @foreach ([
                            'cc_supervisor' => 'CC. Supervisor',
                            'cc_primer_presb' => 'CC. Primer Presbítero',
                            'cc_segundo_presb' => 'CC. Segundo Presbítero',
                            'cc_tercer_presb' => 'CC. Tercer Presbítero',
                            'cc_secre_presb' => 'CC. Secretario Presbiterio',
                            'cc_teso_presb' => 'CC. Tesorero Presbiterio',
                            'cc_fiscal' => 'CC. Fiscal',
                            'cc_asesor_corpen' => 'CC. Asesor Corpen',
                        ] as $campo => $etiqueta)
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ $etiqueta }}</label>
                                <input type="number" class="form-control cedula-cargo @error($campo) is-invalid @enderror"
                                    name="{{ $campo }}" id="{{ $campo }}" data-nombre-target="{{ $campo }}_nombre"
                                    value="{{ old($campo) }}" min="1">
                                <input type="text" class="form-control mt-1" id="{{ $campo }}_nombre" value="" disabled>
                                @error($campo)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex flex-row-reverse gap-2 mt-4">
                        <button class="btn btn-success" type="submit">
                            <i class="feather-plus me-2"></i>
                            <span>Guardar Distrito</span>
                        </button>
                        <a href="{{ route('maestras.distrito.index') }}" class="btn btn-light">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        @include('maestras.distritos._buscar-cargo-script')
    @endpush
</x-base-layout>
