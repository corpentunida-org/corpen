<x-base-layout>
    <div class="card">
        <div class="card-body">
            <h5 class="fw-bold mb-4">Editar cargo</h5>

            <form action="{{ route('archivo.cargo.update', $cargo->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('archivo.cargo.form')
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="feather-edit me-2"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-base-layout>
