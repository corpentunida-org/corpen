<x-base-layout>
    <div class="container">
        <h1>Editar Interacción #{{ $interaction->id }}</h1>

        <form action="{{ route('interactions.update', $interaction->id) }}" 
              method="POST" 
              enctype="multipart/form-data">
            
            @csrf
            @method('PUT')

            @include('interactions.form')

        </form>
    </div>
</x-base-layout>