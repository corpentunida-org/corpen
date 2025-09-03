{{-- <div class="md:col-span-1 flex justify-between">
    <div class="px-4 sm:px-0">
        <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>

        <p class="mt-1 text-sm text-gray-600">
            {{ $description }}
        </p>
    </div>

    <div class="px-4 sm:px-0">
        {{ $aside ?? '' }}
    </div>
</div> --}}

<div class="mb-3 d-flex align-items-center justify-content-between">
    <h5 class="fw-bold mb-0 me-4">
        <span class="d-block mb-2">{{ $title }}: </span>
        <span class="fs-12 fw-normal text-muted text-truncate-1-line">{{ $description }} </span>
    </h5>
</div>