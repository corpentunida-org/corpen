<x-base-layout>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="fw-bold text-primary mb-4">
                <i class="feather-edit-3 me-2"></i> Editar Canal: {{ $channel->name }}
            </h5>

            <form action="{{ route('interactions.channels.update', $channel->id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('interactions.channels._form', ['buttonText' => 'Actualizar'])
            </form>
        </div>
    </div>
</x-base-layout>
