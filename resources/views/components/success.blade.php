<div>
    @if (session('success'))
    <div class="alert alert-dismissible p-4 mt-3 alert-soft-success-message" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <p class="mb-0">
            {{ session('success') }}
        </p>
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-dismissible p-4 mt-3 alert-soft-danger-message" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <p class="mb-0">
            {{ session('error') }}
        </p>
    </div>
    @endif
</div>
