<x-base-layout>
    @php
        $modoEdicion = isset($interaction) && $interaction->id;
    @endphp

    <div class="card">
        <form id="interaction-form"
            action="{{ route('interactions.store') }}"
            method="POST" enctype="multipart/form-data">
            @csrf            
            @include('interactions.form')
        </form>
    </div>
</x-base-layout>
