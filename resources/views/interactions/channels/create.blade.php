<x-base-layout>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="fw-bold text-primary mb-4">
                <i class="feather-plus me-2"></i> Crear Canal de Interacción
            </h5>

            <form action="{{ route('interactions.channels.store') }}" method="POST">
                @csrf
                @include('interactions.channels._form', ['buttonText' => 'Guardar'])
            </form>
        </div>
    </div>
</x-base-layout>
