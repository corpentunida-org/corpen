@csrf

@php
    // Determinar si estamos en modo edición
    $isEdit = isset($usuario) && !empty($usuario->id);
@endphp

<div class="mb-4 px-4 d-flex align-items-center justify-content-between">
    <h5 class="fw-bold mb-0">Agentes Que dan Soporte</h5>
    <a href="{{ route('maestras.terceros.create') }}" class="btn btn-success btnCrear">
        <i class="feather-plus me-2"></i>
        <span>Crear Tercero (Si no existe, crearlo antes)</span>
    </a>
</div>

<div class="row">
    {{-- ID (solo lectura en edición) --}}
    @if($isEdit)
        <div class="col-md-3 mb-3">
            <label for="id" class="form-label text-capitalize">ID</label>
            <input type="text"
                   class="form-control"
                   id="id"
                   name="id"
                   value="{{ substr(md5($usuario->id . 'clave-secreta'), 0, 6) }}"
                   readonly>
        </div>
    @endif

    {{-- Campo: Agente --}}
    <div class="col-md-6 mb-3">
        <label for="cod_ter" class="form-label text-capitalize">Agente</label>

        @if(!$isEdit)
            {{-- Create: Select editable (Select2) --}}
            <select class="form-select select2 @error('cod_ter') is-invalid @enderror"
                    id="cod_ter"
                    name="cod_ter"
                    required>
                <option value=""></option>
                @foreach($terceros as $tercero)
                    <option value="{{ $tercero->cod_ter }}"
                        {{ old('cod_ter', $usuario->cod_ter ?? '') == $tercero->cod_ter ? 'selected' : '' }}>
                        {{ $tercero->cod_ter }} - {{ $tercero->nom_ter }}
                    </option>
                @endforeach
            </select>
        @else
            {{-- Edit: Mostrar texto y hidden --}}
            <div class="form-control" aria-readonly="true">
                {{ $usuario->cod_ter }} - {{ optional($terceros->firstWhere('cod_ter', $usuario->cod_ter))->nom_ter }}
            </div>
            <input type="hidden" name="cod_ter" value="{{ old('cod_ter', $usuario->cod_ter) }}">
        @endif

        @error('cod_ter')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Campo: Usuario --}}
    <div class="col-md-6 mb-3">
        <label for="usuario" class="form-label text-capitalize">Usuario</label>
        <input type="text"
               class="form-control @error('usuario') is-invalid @enderror"
               id="usuario"
               name="usuario"
               value="{{ old('usuario', $usuario->usuario ?? '') }}"
               required>
        @error('usuario')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Campo: Estado --}}
    <div class="col-md-6 mb-3">
        <label for="estado" class="form-label text-capitalize">Estado</label>
        <select class="form-select @error('estado') is-invalid @enderror"
                id="estado"
                name="estado"
                required>
            <option value="">Selecciona un estado</option>
            <option value="Activo" {{ old('estado', $usuario->estado ?? '') == 'Activo' ? 'selected' : '' }}>Activo</option>
            <option value="Inactivo" {{ old('estado', $usuario->estado ?? '') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
        </select>
        @error('estado')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Fechas de creación y actualización (solo lectura en edición) --}}
    @if($isEdit)
        <div class="col-md-6 mb-3">
            <label for="created_at" class="form-label text-capitalize">Creado el</label>
            <input type="text"
                   class="form-control"
                   id="created_at"
                   value="{{ $usuario->created_at?->format('Y-m-d H:i') ?? 'N/A' }}"
                   readonly>
        </div>

        <div class="col-md-6 mb-3">
            <label for="updated_at" class="form-label text-capitalize">Actualizado el</label>
            <input type="text"
                   class="form-control"
                   id="updated_at"
                   value="{{ $usuario->updated_at?->format('Y-m-d H:i') ?? 'N/A' }}"
                   readonly>
        </div>
    @endif
</div>

<!-- =================== SELECT2 =================== -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializar Select2 solo si no estamos en edición
    if ($('#cod_ter').length && !@json($isEdit)) {
        $('#cod_ter').select2({
            placeholder: 'Buscar un tercero...',
            allowClear: true,
            width: '100%',
            minimumInputLength: 1,
            language: {
                noResults: () => "No se encontraron resultados",
                searching: () => "Buscando..."
            }
        });
    }
});
</script>

<style>
/* Estilo profesional para Select2 */
.select2-container .select2-selection--single {
    height: 38px !important;
    padding: 6px 10px !important;
    display: flex !important;
    align-items: center !important;
    border: 1px solid #ced4da !important;
    border-radius: .375rem !important;
    background-color: #fff !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
    right: 10px !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #212529 !important;
    font-size: 0.95rem !important;
    line-height: 1.4 !important;
    padding-left: 2px !important;
}

.select2-container--default .select2-selection--single:hover {
    border-color: #86b7fe !important;
    box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25) !important;
}

.select2-dropdown {
    border-radius: .375rem !important;
    border-color: #ced4da !important;
}
</style>
