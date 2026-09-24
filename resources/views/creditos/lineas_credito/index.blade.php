<x-base-layout>
    @section('titlepage', 'Líneas de Crédito')
    <x-success />
    <x-error />

    <div class="col-12">
        <div class="d-md-flex align-items-center justify-content-between mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-dots mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('creditos.credito.index') }}" class="text-muted text-decoration-none">Créditos</a></li>
                        <li class="breadcrumb-item active">Líneas de Crédito</li>
                    </ol>
                </nav>
                <h2 class="fw-black tracking-tight text-dark mb-1">Líneas de Crédito</h2>
                <p class="text-secondary opacity-75">Los productos de crédito disponibles (tasa, plazos, edades, garantía).</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('lineas_credito.create') }}" class="btn btn-dark btn-lg rounded-pill px-4 shadow-sm d-flex align-items-center gap-2">
                    <i class="feather-plus-circle"></i> <span>Nueva Línea</span>
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="ps-4 py-3 border-0 text-uppercase fs-xs fw-bold text-muted" style="letter-spacing: 1px;">Nombre</th>
                            <th class="border-0 text-uppercase fs-xs fw-bold text-muted">Tipo</th>
                            <th class="border-0 text-uppercase fs-xs fw-bold text-muted">Garantía</th>
                            <th class="text-center border-0 text-uppercase fs-xs fw-bold text-muted">Tasa</th>
                            <th class="text-center border-0 text-uppercase fs-xs fw-bold text-muted">Plazo</th>
                            <th class="text-end pe-4 border-0 text-uppercase fs-xs fw-bold text-muted">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lineasCredito as $linea)
                            <tr>
                                <td class="ps-4 py-4">
                                    <a href="{{ route('lineas_credito.show', $linea) }}" class="d-flex align-items-center text-decoration-none link-name-container">
                                        <div class="avatar-letter bg-soft-primary text-primary me-3">
                                            {{ substr($linea->nombre, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark name-text">{{ $linea->nombre }}</h6>
                                            <small class="text-muted fs-xs italic">Cuenta: {{ $linea->cuenta }}</small>
                                        </div>
                                    </a>
                                </td>
                                <td>{{ $linea->tipoCredito->nombre ?? '—' }}</td>
                                <td>{{ $linea->garantia->nombre ?? '—' }}</td>
                                <td class="text-center">{{ $linea->tasa_interes !== null ? rtrim(rtrim(number_format($linea->tasa_interes, 2, '.', ''), '0'), '.').'%' : '—' }}</td>
                                <td class="text-center">{{ $linea->plazo_minimo }}–{{ $linea->plazo_maximo }} meses</td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('lineas_credito.edit', $linea) }}" class="btn btn-icon-modern" data-bs-toggle="tooltip" title="Editar">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                        <form action="{{ route('lineas_credito.destroy', $linea) }}" method="POST" class="formEliminar d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-icon-modern text-danger" data-bs-toggle="tooltip" title="Eliminar">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <h5 class="fw-bold text-muted">No hay líneas de crédito registradas</h5>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($lineasCredito->hasPages())
                <div class="card-footer bg-white border-0 py-4 px-4">
                    {{ $lineasCredito->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f7f6; }
        .fw-black { font-weight: 800; }
        .fs-xs { font-size: 0.7rem; }
        .tracking-tight { letter-spacing: -0.02em; }
        .bg-soft-primary { background-color: #f0f7ff; }
        .avatar-letter { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-weight: 700; text-transform: uppercase; }
        .link-name-container:hover .name-text { color: #0d6efd !important; text-decoration: underline; }
        .btn-icon-modern { width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; border: 1px solid #eee; background: white; color: #555; transition: all 0.2s; text-decoration: none; }
        .btn-icon-modern:hover { background: #000; color: #fff; }
        .btn-icon-modern.text-danger:hover { background: #ff4d4d; color: #fff; border-color: #ff4d4d; }
        .breadcrumb-dots .breadcrumb-item + .breadcrumb-item::before { content: "•"; color: #ccc; padding: 0 1rem; }
    </style>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.formEliminar').forEach(function (form) {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        Swal.fire({
                            title: '¿Eliminar esta línea de crédito?',
                            text: 'No se puede deshacer. Si ya tiene créditos asociados, no se podrá eliminar.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar',
                        }).then((result) => { if (result.isConfirmed) form.submit(); });
                    });
                });
            });
        </script>
    @endpush
</x-base-layout>
