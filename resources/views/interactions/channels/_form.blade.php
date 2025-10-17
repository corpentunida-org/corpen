<div class="mb-3">
    <label for="name" class="form-label fw-semibold">Nombre del Canal</label>
    <input type="text"
           name="name"
           id="name"
           class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $channel->name ?? '') }}"
           placeholder="Ej: WhatsApp, Email, Teléfono"
           required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-success d-flex align-items-center gap-2">
        <i class="feather-save"></i> <span>{{ $buttonText }}</span>
    </button>
    <a href="{{ route('interactions.channels.index') }}" class="btn btn-secondary d-flex align-items-center gap-2">
        <i class="feather-x-circle"></i> <span>Cancelar</span>
    </a>
</div>
