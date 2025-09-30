@csrf
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="nombre" class="form-label text-capitalize">Nombre</label>
        <input type="text" class="form-control @error('nombre') is-invalid @enderror"
               id="nombre" name="nombre"
               value="{{ old('nombre', $scpCategoria->nombre ?? '') }}" required>
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 mb-3">
        <label for="descripcion" class="form-label text-capitalize">Descripción</label>
        <textarea class="form-control @error('descripcion') is-invalid @enderror"
                  id="descripcion" name="descripcion">{{ old('descripcion', $scpCategoria->descripcion ?? '') }}</textarea>
        @error('descripcion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
