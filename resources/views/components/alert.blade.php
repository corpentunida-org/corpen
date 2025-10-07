<div {{ $attributes->merge(['class' => 'alert alert-' . ($type ?? 'info') . ' alert-dismissible fade show shadow-sm rounded-3 mx-4 mt-3 small']) }} role="alert">
    <i class="bi bi-info-circle me-2"></i> {{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>