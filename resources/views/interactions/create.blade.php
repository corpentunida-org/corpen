<x-base-layout>
    <div class="container">
        <h1>Nueva Interacción</h1>

        <form action="{{ route('interactions.store') }}" 
              method="POST" 
              enctype="multipart/form-data">
            
            @csrf

            @include('interactions.form')

        </form>
    </div>
</x-base-layout>