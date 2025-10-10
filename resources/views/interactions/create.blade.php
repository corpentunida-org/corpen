<x-base-layout>
    <div class="container mx-auto px-4 py-10">
        <form action="{{ route('interactions.store') }}" 
              method="POST" 
              enctype="multipart/form-data"
              class="bg-white p-6 rounded-xl shadow-md border border-gray-200 transition duration-300 hover:shadow-lg">
            
            @csrf

            @include('interactions.form')
        </form>
    </div>
</x-base-layout>
