@csrf
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="nombre" class="form-label text-capitalize">Nombre</label>
        <input type="text" class="form-control @error('nombre') is-invalid @enderror"
               id="nombre" name="nombre"
               value="{{ old('nombre', $scpSubTipo->nombre ?? '') }}" required>
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="scp_tipo_id" class="form-label text-capitalize">Tipo</label>
        <select class="form-select @error('scp_tipo_id') is-invalid @enderror"
                id="scp_tipo_id" name="scp_tipo_id" required>
            <option value="">Selecciona un Tipo</option>
            @foreach($tipos as $tipo)
                <option value="{{ $tipo->id }}"
                    {{ old('scp_tipo_id', $scpSubTipo->scp_tipo_id ?? '') == $tipo->id ? 'selected' : '' }}>
                    {{ $tipo->nombre }}
                </option>
            @endforeach
        </select>
        @error('scp_tipo_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 mb-3">
        <label for="descripcion" class="form-label text-capitalize">Descripción</label>
        <textarea class="form-control @error('descripcion') is-invalid @enderror"
                  id="descripcion" name="descripcion">{{ old('descripcion', $scpSubTipo->descripcion ?? '') }}</textarea>
        @error('descripcion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

