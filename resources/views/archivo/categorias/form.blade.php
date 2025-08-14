{{-- Campo de Nombre --}}
<div class="mb-3">
    <label for="nombre" class="form-label fw-bold">Nombre de la Categoría</label>
    <input type="text"
           id="nombre"
           name="nombre"
           class="form-control @error('nombre') is-invalid @enderror"
           value="{{ old('nombre', $categoria->nombre) }}"
           placeholder="Ej: Contratos, Informes, Facturas..."
           required
           autofocus>
    {{-- Mensaje de error de validación --}}
    @error('nombre')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

<hr class="my-4">

{{-- Botones de Acción --}}
<div class="d-flex justify-content-end gap-2">
    {{-- Botón para volver al listado --}}
    <a href="{{ route('archivo.categorias.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>
        Cancelar
    </a>
    {{-- Botón de envío del formulario --}}
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg me-1"></i>
        {{-- La variable $btnText viene del @include en create/edit --}}
        {{ $btnText ?? 'Guardar' }}
    </button>
</div>