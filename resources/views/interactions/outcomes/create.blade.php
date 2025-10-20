<x-base-layout>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            {{-- 📌 Encabezado --}}
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="fw-bold mb-0 text-primary">
                    <i class="feather-plus me-2"></i> Crear Resultado
                </h5>
                <a href="{{ route('interactions.outcomes.index') }}" class="btn btn-secondary d-flex align-items-center gap-2">
                    <i class="feather-arrow-left"></i> <span>Volver</span>
                </a>
            </div>

            {{-- 📋 Formulario --}}
            <form action="{{ route('interactions.outcomes.store') }}" method="POST">
                @csrf
                @include('interactions.outcomes._form', ['outcome' => null])
            </form>
        </div>
    </div>
</x-base-layout>
