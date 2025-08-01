<x-base-layout>
    <div class="card">
        <div class="card-body">
            <h5 class="fw-bold mb-4">Crear nuevo cargo</h5>

            <form action="{{ route('archivo.cargo.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('archivo.cargo.form')
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-save me-2"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-base-layout>
