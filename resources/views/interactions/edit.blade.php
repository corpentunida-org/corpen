<x-base-layout>
    <div class="card">
       <form action="{{ route('interactions.update', $interaction->id) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @csrf  
            @include('interactions.form')
        </form>
    </div>
</x-base-layout>
