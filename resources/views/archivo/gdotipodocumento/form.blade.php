<div class="mb-3">
    <label for="nombre" class="form-label">Nombre</label>
    <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" 
           value="{{ old('nombre', $tipoDocumento->nombre) }}" 
           placeholder="Ingrese el nombre del tipo de documento" required>
    @error('nombre')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
