<div class="px-4">
    <div class="mb-3">
        <label for="name" class="form-label fw-semibold">Nombre de la Próxima Acción</label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $nextAction->name ?? '') }}" placeholder="Ej. Llamada, Reunión, Envío de correo...">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="feather-save me-1"></i> Guardar
        </button>
        <a href="{{ route('interactions.next_actions.index') }}" class="btn btn-light border">
            <i class="feather-x me-1"></i> Cancelar
        </a>
    </div>
</div>
