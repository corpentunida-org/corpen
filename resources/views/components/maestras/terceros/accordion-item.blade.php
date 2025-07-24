@props(['id', 'title', 'open' => false])

<div class="accordion-item border">
    <h2 class="accordion-header" id="heading-{{ $id }}">
        <button class="accordion-button {{ !$open ? 'collapsed' : '' }}" type="button"
            data-bs-toggle="collapse" data-bs-target="#collapse-{{ $id }}" aria-expanded="{{ $open ? 'true' : 'false' }}"
            aria-controls="collapse-{{ $id }}">
            {{ $title }}
        </button>
    </h2>
    <div id="collapse-{{ $id }}" class="accordion-collapse collapse {{ $open ? 'show' : '' }}"
        aria-labelledby="heading-{{ $id }}">
        <div class="accordion-body">
            {{ $slot }}
        </div>
    </div>
</div>
