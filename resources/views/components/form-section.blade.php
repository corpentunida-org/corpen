@props(['submit'])

<div {{ $attributes->merge(['class' => 'card']) }}>
    <div class="card-body">
        <x-section-title>
            <x-slot name="title">{{ $title }}</x-slot>
            <x-slot name="description">{{ $description }}</x-slot>
        </x-section-title>

        <div class="my-5 md:mt-0 md:col-span-2">
            <form wire:submit="{{ $submit }}">
                <div class="mb-3">
                    {{ $form }}
                </div>

                @if (isset($actions))
                    <div>
                        {{ $actions }}
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>