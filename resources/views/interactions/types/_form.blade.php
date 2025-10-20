@csrf
<div class="mb-3">
    <label for="name" class="form-label fw-semibold">Nombre</label>
    <input type="text" name="name" id="name" value="{{ old('name', $type->name ?? '') }}" class="form-control" required>
</div>
