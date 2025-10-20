<x-base-layout>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="fw-bold mb-4 text-primary"><i class="feather-plus-circle me-2"></i> Crear Tipo de Interacción</h5>

            <form action="{{ route('interactions.types.store') }}" method="POST">
                @include('interactions.types._form')
                <button type="submit" class="btn btn-success mt-3">
                    <i class="feather-check me-1"></i> Guardar
                </button>
                <a href="{{ route('interactions.types.index') }}" class="btn btn-light border mt-3">Cancelar</a>
            </form>
        </div>
    </div>
</x-base-layout>
