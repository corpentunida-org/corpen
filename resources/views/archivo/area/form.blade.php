{{-- Contenedor de campos con disposición vertical y espaciado consistente --}}
<div class="d-flex flex-column gap-3">

    {{-- Campo Nombre con Floating Label --}}
    <div class="form-floating">
        <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" 
               placeholder="Nombre del área" value="{{ old('nombre', $area->nombre ?? '') }}" required>
        <label for="nombre">Nombre del área</label>
        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    
    {{-- Campo Descripción con Floating Label (usando textarea) --}}
    <div class="form-floating">
        <textarea name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" 
                  placeholder="Descripción" style="height: 100px">{{ old('descripcion', $area->descripcion ?? '') }}</textarea>
        <label for="descripcion">Descripción</label>
        @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Doble columna para Estado y Jefe de Área --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-floating">
                <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror">
                    <option value="activo" {{ old('estado', $area->estado ?? 'activo') == 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ old('estado', $area->estado ?? 'activo') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
                <label for="estado">Estado</label>
                @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <select name="GDO_cargo_id" id="GDO_cargo_id" class="form-select @error('GDO_cargo_id') is-invalid @enderror">
                    <option value="" disabled selected>Selecciona un cargo...</option>
                    @foreach($cargos as $cargo)
                        <option value="{{ $cargo->id }}" 
                            {{ old('GDO_cargo_id', $area->GDO_cargo_id ?? '') == $cargo->id ? 'selected' : '' }}>
                            {{ $cargo->nombre_cargo }}
                        </option>
                    @endforeach
                </select>
                <label for="GDO_cargo_id">Jefe del Área (Cargo)</label>
                @error('GDO_cargo_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
</div>