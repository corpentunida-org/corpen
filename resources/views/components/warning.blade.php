<div>
    <!-- The whole future lies in uncertainty: live immediately. - Seneca -->
    @if (session('warning'))
    <div class="alert alert-dismissible p-4 mt-3 alert-soft-warning-message" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <p class="mb-0">
            {{ session('warning') }}
        </p>
    </div>
    @endif
</div>
