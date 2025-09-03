@props(['for'])

@error($for)
    <p {{ $attributes->merge(['class' => 'fs-12 d-block fw-normal text-danger']) }}>{{ $message }}</p>
@enderror
