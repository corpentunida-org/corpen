<x-base-layout>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Editar Tipo: {{ $tipo->nombre }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('maestras.tipos.update', $tipo) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    {{-- Campos del formulario --}}
                    <div class="mb-3 col-md-6">
                        <label for="codigo" class="form-label">Código (*)</label>
                        <input type="text" class="form-control @error('codigo') is-invalid @enderror" id="codigo" name="codigo" value="{{ old('codigo', $tipo->codigo) }}">
                        @error('codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="nombre" class="form-label">Nombre (*)</label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $tipo->nombre) }}">
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-12">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $tipo->descripcion) }}</textarea>
                    </div>

                    <div class="mb-3 col-md-4">
                        <label for="grupo" class="form-label">Grupo</label>
                        <input type="text" class="form-control" id="grupo" name="grupo" value="{{ old('grupo', $tipo->grupo) }}">
                    </div>

                    <div class="mb-3 col-md-4">
                        <label for="categoria" class="form-label">Categoría</label>
                        <input type="text" class="form-control" id="categoria" name="categoria" value="{{ old('categoria', $tipo->categoria) }}">
                    </div>
                    
                    <div class="mb-3 col-md-4">
                        <label for="orden" class="form-label">Orden</label>
                        <input type="number" class="form-control" id="orden" name="orden" value="{{ old('orden', $tipo->orden) }}">
                    </div>

                    <div class="mb-3 col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="activo" name="activo" {{ old('activo', $tipo->activo) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">Activo</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Actualizar Tipo</button>
                    <a href="{{ route('maestras.tipos.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-base-layout>