@php
    $relaciones = [
        'cc_supervisor' => 'CC. Supervisor',
        'cc_primer_presb' => 'CC. Primer Presbítero',
        'cc_segundo_presb' => 'CC. Segundo Presbítero',
        'cc_tercer_presb' => 'CC. Tercer Presbítero',
        'cc_secre_presb' => 'CC. Secretario Presbiterio',
        'cc_teso_presb' => 'CC. Tesorero Presbiterio',
        'cc_fiscal' => 'CC. Fiscal',
        'cc_asesor_corpen' => 'CC. Asesor Corpen',
    ];
    $relacionObj = [
        'cc_supervisor' => 'supervisor',
        'cc_primer_presb' => 'primerPresbitero',
        'cc_segundo_presb' => 'segundoPresbitero',
        'cc_tercer_presb' => 'tercerPresbitero',
        'cc_secre_presb' => 'secretarioPresbiterio',
        'cc_teso_presb' => 'tesoreroPresbiterio',
        'cc_fiscal' => 'fiscal',
        'cc_asesor_corpen' => 'asesorCorpen',
    ];
@endphp
<x-base-layout>
    @section('titlepage', 'Editar Distrito')

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>¡Atención!</strong> Por favor, corrige los errores marcados en el formulario.
        </div>
    @endif

    <div class="col-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Editar Distrito — {{ $distrito->NOM_DIST }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('maestras.distrito.update', $distrito->COD_DIST) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Código</label>
                            <input type="text" class="form-control" value="{{ $distrito->COD_DIST }}" readonly>
                            <input type="hidden" name="cod_dist" value="{{ $distrito->COD_DIST }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase @error('nom_dist') is-invalid @enderror"
                                name="nom_dist" value="{{ old('nom_dist', $distrito->NOM_DIST) }}" required>
                            @error('nom_dist')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Detalle</label>
                            <input type="text" class="form-control @error('detalle') is-invalid @enderror"
                                name="detalle" value="{{ old('detalle', $distrito->DETALLE) }}">
                            @error('detalle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Compuesto</label>
                            <input type="text" class="form-control @error('compuest') is-invalid @enderror"
                                name="compuest" value="{{ old('compuest', $distrito->COMPUEST) }}">
                            @error('compuest')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3">Junta del Presbiterio</h6>

                    <div class="row">
                        @foreach ($relaciones as $campo => $etiqueta)
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ $etiqueta }}</label>
                                <input type="number" class="form-control cedula-cargo @error($campo) is-invalid @enderror"
                                    name="{{ $campo }}" id="{{ $campo }}" data-nombre-target="{{ $campo }}_nombre"
                                    value="{{ old($campo, $distrito->$campo) }}" min="1">
                                <input type="text" class="form-control mt-1" id="{{ $campo }}_nombre"
                                    value="{{ $distrito->{$relacionObj[$campo]}?->nom_ter }}" disabled>
                                @error($campo)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex flex-row-reverse gap-2 mt-4">
                        <button class="btn btn-warning" type="submit">
                            <i class="feather-save me-2"></i>
                            <span>Actualizar Distrito</span>
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
