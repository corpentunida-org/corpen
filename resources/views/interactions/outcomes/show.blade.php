<x-base-layout>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            {{-- 📌 Encabezado --}}
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="fw-bold mb-0 text-primary">
                    <i class="feather-eye me-2"></i> Detalle del Resultado
                </h5>
                <a href="{{ route('interactions.outcomes.index') }}" class="btn btn-secondary d-flex align-items-center gap-2">
                    <i class="feather-arrow-left"></i> <span>Volver</span>
                </a>
            </div>

            {{-- 📄 Detalle --}}
            <div class="px-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">ID</label>
                    <p class="form-control-plaintext">#{{ $outcome->id }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre</label>
                    <p class="form-control-plaintext">{{ $outcome->name }}</p>
                </div>

                <div class="mt-4">
                    <a href="{{ route('interactions.outcomes.edit', $outcome->id) }}" class="btn btn-warning me-2">
                        <i class="feather-edit-3"></i> Editar
                    </a>
                    <form action="{{ route('interactions.outcomes.destroy', $outcome->id) }}" method="POST" class="d-inline formEliminar">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="feather-trash-2"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.formEliminar').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡Esta acción no se puede deshacer!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => { if (result.isConfirmed) this.submit(); });
                });
            });
        });
    </script>
    @endpush
</x-base-layout>
