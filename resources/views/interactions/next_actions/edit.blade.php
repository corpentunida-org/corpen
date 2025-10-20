<x-base-layout>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="fw-bold mb-0 text-primary">
                    <i class="feather-edit-3 me-2"></i> Editar Próxima Acción
                </h5>
                <a href="{{ route('interactions.next_actions.index') }}" class="btn btn-secondary d-flex align-items-center gap-2">
                    <i class="feather-arrow-left"></i> <span>Volver</span>
                </a>
            </div>

            <form action="{{ route('interactions.next_actions.update', $nextAction->id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('interactions.next_actions._form', ['nextAction' => $nextAction])
            </form>
        </div>
    </div>
</x-base-layout>
