<x-base-layout>
    <div class="card mb-4">
        <div class="card-body">
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Editar Soporte</h5>
                <a href="{{ route('soportes.soportes.index') }}" class="btn btn-secondary">
                    <i class="feather-arrow-left me-2"></i>
                    <span>Volver al Listado</span>
                </a>
            </div>

            <form action="{{ route('soportes.soportes.update', $scpSoporte->id) }}" method="POST">
                @method('PUT')
                @include('soportes.soportes.form', ['soporte' => $scpSoporte])
            </form>
        </div>
    </div>
</x-base-layout>
