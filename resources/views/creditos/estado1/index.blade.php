<x-base-layout>
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Fábricas de Crédito</h5>
                <a href="{{ route('estado1.create') }}" class="btn btn-success btnCrear">
                    <i class="feather-plus me-2"></i>
                    <span>Crear Nueva</span>
                </a>
            </div>

            {{-- Tabla --}}
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle small" style="font-size: 0.875rem;">
                    <thead>
                        <tr class="border-top">
                            <th class="py-1 px-2">Nombre</th>
                            <th class="py-1 px-2">Cuenta</th>
                            <th class="py-1 px-2">Tipo</th>
                            <th class="py-1 px-2">Tasa Interés</th>
                            <th class="py-1 px-2 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($fabricas as $fabrica)
                            <tr class="py-1">
                                <td class="py-1 px-2">{{ $fabrica->nombre }}</td>
                                <td class="py-1 px-2">{{ $fabrica->cuenta }}</td>
                                <td class="py-1 px-2">{{ $fabrica->tipo }}</td>
                                <td class="py-1 px-2">{{ $fabrica->tasa_interes }}%</td>
                                <td class="hstack justify-content-end gap-3 text-end py-1 px-2">
                                    <div class="dropdown open">
                                        <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                            <i class="feather-more-horizontal"></i>
                                        </a>

                                        <ul class="dropdown-menu">
                                            @if ($fabrica->id)
                                                <li>
                                                    <a class="dropdown-item btnEditar" href="{{ route('estado1.edit', ['fabrica' => $fabrica->id]) }}">
                                                        <i class="feather-edit me-3"></i> Editar
                                                    </a>
                                                </li>

                                                <li>
                                                    <form action="{{ route('estado1.destroy', ['fabrica' => $fabrica->id]) }}" method="POST" class="formEliminar">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item btnEliminar">
                                                            <i class="feather-trash me-3"></i> Eliminar
                                                        </button>
                                                    </form>
                                                </li>
                                            @else
                                                <li>
                                                    <span class="dropdown-item text-danger">Registro incompleto</span>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay registros.</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $fabricas->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const confirmar = (title, text, icon, callback) => {
                    Swal.fire({
                        title,
                        text,
                        icon,
                        showCancelButton: true,
                        confirmButtonColor: '#198754',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí',
                        cancelButtonText: 'Cancelar'
                    }).then(result => {
                        if (result.isConfirmed) callback();
                    });
                };

                document.querySelectorAll('.btnCrear').forEach(btn => {
                    btn.addEventListener('click', e => {
                        e.preventDefault();
                        confirmar('¿Crear una nueva fábrica?', 'Serás redirigido al formulario.', 'info', () => {
                            window.location.href = btn.getAttribute('href');
                        });
                    });
                });

                document.querySelectorAll('.btnEditar').forEach(btn => {
                    btn.addEventListener('click', e => {
                        e.preventDefault();
                        confirmar('¿Editar esta fábrica?', 'Serás redirigido al formulario de edición.', 'question', () => {
                            window.location.href = btn.getAttribute('href');
                        });
                    });
                });

                document.querySelectorAll('.formEliminar').forEach(form => {
                    form.addEventListener('submit', e => {
                        e.preventDefault();
                        confirmar('¿Eliminar esta fábrica?', 'Esta acción no se puede deshacer.', 'warning', () => {
                            form.submit();
                        });
                    });
                });
            });
        </script>
    @endpush
</x-base-layout>
