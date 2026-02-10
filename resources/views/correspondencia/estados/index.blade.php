<x-base-layout>
    <div class="app-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold m-0">Gestión de Estados</h1>
                <p class="text-muted">Ciclo de vida de la correspondencia.</p>
            </div>
            <a href="{{ route('correspondencia.estados.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus me-2"></i> Nuevo Estado
            </a>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4">Nombre del Estado</th>
                            <th>Descripción</th>
                            <th class="text-center">Documentos</th>
                            <th class="text-end px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estados as $estado)
                        <tr>
                            <td class="px-4">
                                <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill fw-bold" style="background-color: #eef2ff;">
                                    {{ $estado->nombre }}
                                </span>
                            </td>
                            <td class="text-muted small">{{ $estado->descripcion ?? 'Sin descripción' }}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ $estado->correspondencias_count }}</span>
                            </td>
                            <td class="text-end px-4">
                                <div class="btn-group">
                                    <a href="{{ route('correspondencia.estados.show', $estado) }}" class="btn btn-sm btn-light border"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('correspondencia.estados.edit', $estado) }}" class="btn btn-sm btn-light border"><i class="fas fa-edit"></i></a>
                                    <!--<form action="{{ route('correspondencia.estados.destroy', $estado) }}" method="POST" class="d-inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-light border text-danger btn-delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form> -->
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No hay estados registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0">{{ $estados->links() }}</div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Confirmación de eliminación
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "No podrás revertir esto y fallará si tiene documentos asociados.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.closest('form').submit();
                    }
                });
            });
        });
    </script>
    @endpush
</x-base-layout>