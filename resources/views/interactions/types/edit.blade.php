<x-base-layout>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="fw-bold mb-4 text-primary"><i class="feather-edit-3 me-2"></i> Editar Tipo de Interacción</h5>

            <form action="{{ route('interactions.types.update', $type->id) }}" method="POST">
                @method('PUT')
                @include('interactions.types._form')
                <button type="submit" class="btn btn-primary mt-3">
                    <i class="feather-save me-1"></i> Actualizar
                </button>
                <a href="{{ route('interactions.types.index') }}" class="btn btn-light border mt-3">Cancelar</a>
            </form>
        </div>
    </div>
</x-base-layout>
