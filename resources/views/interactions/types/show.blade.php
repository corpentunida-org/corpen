<x-base-layout>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="fw-bold mb-4 text-primary">
                <i class="feather-eye me-2"></i> Detalle del Tipo de Interacción
            </h5>

            <div class="mb-3">
                <label class="fw-semibold d-block">ID:</label>
                <span>#{{ $type->id }}</span>
            </div>

            <div class="mb-3">
                <label class="fw-semibold d-block">Nombre:</label>
                <span>{{ $type->name }}</span>
            </div>

            <a href="{{ route('interactions.types.index') }}" class="btn btn-light border mt-3">
                <i class="feather-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>
</x-base-layout>
