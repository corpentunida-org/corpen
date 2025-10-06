@csrf
{{-- {{ dd($usuario) }} --}}
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="cod_ter" class="form-label text-capitalize">Tercero</label>
        <select class="form-select @error('cod_ter') is-invalid @enderror" id="cod_ter" name="cod_ter" required>
            <option value="">Selecciona un Tercero</option>
            @foreach($terceros as $tercero)
                <option value="{{ $tercero->cod_ter }}">
                    {{ $tercero->nom_ter }}
                </option>
            @endforeach
        </select>
        @error('cod_ter')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="rol" class="form-label text-capitalize">Rol</label>
        <input type="text" class="form-control @error('rol') is-invalid @enderror"
               id="rol" name="rol" value="{{ old('rol', $usuario->rol ?? '') }}">
        @error('rol')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
