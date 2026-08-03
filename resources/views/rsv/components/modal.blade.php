@php
    $modalName = $name ?? $id ?? 'default-modal';
    $modalTitle = $title ?? '';
@endphp

<div class="modal fade" id="{{ $modalName }}" tabindex="-1" aria-labelledby="{{ $modalName }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 p-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="{{ $modalName }}Label">{{ $modalTitle }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="max-h-[75vh] overflow-y-auto pr-1">
                {!! $slot ?? '' !!}
            </div>
        </div>
    </div>
</div>
