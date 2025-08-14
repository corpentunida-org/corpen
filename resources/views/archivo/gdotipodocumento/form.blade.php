{{-- 
    Este formulario ahora usa el patrón "Floating Label" de Bootstrap.
    La etiqueta está dentro del campo y flota hacia arriba cuando se enfoca.
    Es extremadamente limpio y minimalista.
--}}
<div class="form-floating">
    <input type="text" 
           name="nombre" 
           id="nombre" 
           class="form-control @error('nombre') is-invalid @enderror" 
           placeholder="Nombre del tipo de documento" 
           value="{{ old('nombre', $tipoDocumento->nombre ?? '') }}" 
           required>
    <label for="nombre">Nombre del tipo de documento</label>
    
    {{-- La validación de errores se mantiene intacta y funciona perfectamente con este diseño --}}
    @error('nombre')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>