@csrf

<!-- Detalles del Soporte -->
<div class="mb-3">
    <label for="detalles_soporte" class="form-label">Detalles del Soporte</label>
    <textarea class="form-control @error('detalles_soporte') is-invalid @enderror" 
              id="detalles_soporte" name="detalles_soporte" rows="3" required>{{ old('detalles_soporte', $soporte->detalles_soporte ?? '') }}</textarea>
    @error('detalles_soporte')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<!-- Tipo de Soporte -->
<div class="mb-3">
    <label for="id_scp_tipo" class="form-label">Tipo de Soporte</label>
    <select class="form-select @error('id_scp_tipo') is-invalid @enderror" id="id_scp_tipo" name="id_scp_tipo" required>
        <option value="">Selecciona un tipo</option>
        @foreach ($tipos as $tipo)
            <option value="{{ $tipo->id }}" 
                {{ old('id_scp_tipo', $soporte->id_scp_tipo ?? '') == $tipo->id ? 'selected' : '' }}>
                {{ $tipo->nombre }}
            </option>
        @endforeach
    </select>
    @error('id_scp_tipo')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<!-- Prioridad -->
<div class="mb-3">
    <label for="id_scp_prioridad" class="form-label">Prioridad</label>
    <select class="form-select @error('id_scp_prioridad') is-invalid @enderror" id="id_scp_prioridad" name="id_scp_prioridad" required>
        <option value="">Selecciona una prioridad</option>
        @foreach ($prioridades as $prioridad)
            <option value="{{ $prioridad->id }}" 
                {{ old('id_scp_prioridad', $soporte->id_scp_prioridad ?? '') == $prioridad->id ? 'selected' : '' }}>
                {{ $prioridad->nombre }}
            </option>
        @endforeach
    </select>
    @error('id_scp_prioridad')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<!-- Tercero Asignacion -->
<div class="mb-3">
    <label for="cod_ter_maeTercero" class="form-label">Asignacion Inicial</label>
    <select class="form-select @error('cod_ter_maeTercero') is-invalid @enderror" id="cod_ter_maeTercero" name="cod_ter_maeTercero" required>
        <option value="">Selecciona un tercero</option>
        @foreach ($terceros as $tercero)
            <option value="{{ $tercero->cod_ter }}" 
                {{ old('cod_ter_maeTercero', $soporte->cod_ter_maeTercero ?? '') == $tercero->cod_ter ? 'selected' : '' }}>
                {{ $tercero->cod_ter }} - {{ $tercero->nom_ter }}
            </option>
        @endforeach
    </select>
    @error('cod_ter_maeTercero')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<!-- Usuario Creador -->
<div class="mb-3">
    <label class="form-label">Creado por</label>
    <input type="text" class="form-control" 
           value="{{ auth()->user()->name }}" readonly>
    <input type="hidden" name="id_users" value="{{ auth()->id() }}">
</div>


<!-- Cargo -->
<div class="mb-3">
    <label for="id_gdo_cargo" class="form-label">Cargo / Área</label>
    <select class="form-select @error('id_gdo_cargo') is-invalid @enderror" id="id_gdo_cargo" name="id_gdo_cargo" required>
        <option value="">Selecciona un cargo</option>
        @foreach ($cargos as $cargo)
            <option value="{{ $cargo->id }}" 
                {{ old('id_gdo_cargo', $soporte->id_gdo_cargo ?? '') == $cargo->id ? 'selected' : '' }}>
                {{ $cargo->nombre_cargo }}
            </option>
        @endforeach
    </select>
    @error('id_gdo_cargo')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<!-- Línea de Crédito -->
<div class="mb-3">
    <label for="id_cre_lineas_creditos" class="form-label">Línea Crédito</label>
    <select class="form-select @error('id_cre_lineas_creditos') is-invalid @enderror" id="id_cre_lineas_creditos" name="id_cre_lineas_creditos" required>
        <option value="">Selecciona una línea</option>
        @foreach ($lineas as $linea)
            <option value="{{ $linea->id }}" 
                {{ old('id_cre_lineas_creditos', $soporte->id_cre_lineas_creditos ?? '') == $linea->id ? 'selected' : '' }}>
                {{ $linea->nombre }}
            </option>
        @endforeach
    </select>
    @error('id_cre_lineas_creditos')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<button type="submit" class="btn btn-primary">
    {{ isset($soporte) ? 'Actualizar Soporte' : 'Guardar Soporte' }}
</button>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#cod_ter_maeTercero').select2({
            placeholder: "Busca por cédula o nombre",
            width: '100%',
            matcher: function(params, data) {
                if ($.trim(params.term) === '') return data;
                if ((data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1)) {
                    return data;
                }
                return null;
            }
        });
    });
</script>
@endpush
