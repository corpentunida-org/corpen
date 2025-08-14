{{-- resources/views/archivo/gdotipodocumento/_form.blade.php --}}

{{-- Campo para el Nombre del Tipo de Documento --}}
<div class="form-floating mb-3">
    <input type="text"
           name="nombre"
           id="nombre"
           class="form-control @error('nombre') is-invalid @enderror"
           placeholder="Nombre del tipo de documento"
           value="{{ old('nombre', $tipoDocumento->nombre ?? '') }}"
           required>
    <label for="nombre">Nombre del tipo de documento</label>

    @error('nombre')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Campo para seleccionar la Categoría del Documento --}}
<div class="form-floating">
    <select name="categoria_documento_id"
            id="categoria_documento_id"
            class="form-select @error('categoria_documento_id') is-invalid @enderror"
            required>
        <option value="" disabled {{ old('categoria_documento_id', $tipoDocumento->categoria_documento_id) ? '' : 'selected' }}>Seleccione una categoría</option>
        
        {{-- Iterar sobre las categorías pasadas desde el controlador --}}
        @foreach($categorias as $categoria)
            <option value="{{ $categoria->id }}" 
                    {{-- Marcar como seleccionada la opción que corresponda --}}
                    {{ old('categoria_documento_id', $tipoDocumento->categoria_documento_id) == $categoria->id ? 'selected' : '' }}>
                {{ $categoria->nombre }}
            </option>
        @endforeach
    </select>
    <label for="categoria_documento_id">Categoría del Documento</label>

    @error('categoria_documento_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>