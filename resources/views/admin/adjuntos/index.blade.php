<x-base-layout>
    @section('titlepage', 'Limpiar Historial de Adjuntos')
    <x-success />
    <x-error />

    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-1"><i class="feather-hard-drive me-2"></i>Limpiar Historial de Adjuntos (Interacciones)</h5>
                <p class="text-muted mb-0 small">
                    Los soportes (evidencias) que se suben en Interacciones se guardan en S3 y hoy nunca se borran solos.
                    Aquí puedes liberar espacio eliminando los archivos de años ya cerrados — <strong>el registro del
                    seguimiento (nota, resultado, quién gestionó) siempre se conserva</strong>, solo se quita el archivo pesado.
                </p>
            </div>
            <div class="card-body">
                @if ($porAnio->isEmpty())
                    <p class="text-muted mb-0">No hay adjuntos registrados todavía.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Año</th>
                                    <th class="text-center">Adjuntos guardados</th>
                                    <th class="text-center">Tamaño conocido</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($porAnio as $fila)
                                    <tr>
                                        <td class="fw-bold">{{ $fila->anio }}</td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-light text-dark border px-3">{{ number_format($fila->total) }}</span>
                                        </td>
                                        <td class="text-center">
                                            {{ $fila->tamano_formateado }}
                                            @if ($fila->sin_tamano > 0)
                                                <br><small class="text-muted" data-bs-toggle="tooltip"
                                                    title="Estos se subieron antes de guardar el tamaño; se calcula solo al entrar a 'Ver archivos'.">
                                                    (faltan {{ number_format($fila->sin_tamano) }} por calcular)
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.adjuntos.ver', $fila->anio) }}" class="btn btn-sm btn-outline-secondary me-1">
                                                <i class="feather-eye me-1"></i>Ver archivos
                                            </a>
                                            @if ($fila->anio >= $anioActual)
                                                <span class="text-muted small" data-bs-toggle="tooltip" title="No se puede limpiar el año en curso, para no borrar evidencia todavía activa.">
                                                    <i class="feather-lock me-1"></i>Año en curso
                                                </span>
                                            @elseif ($puedeLimpiar)
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-limpiar-anio"
                                                        data-anio="{{ $fila->anio }}" data-total="{{ $fila->total }}">
                                                    <i class="feather-trash-2 me-1"></i>Limpiar {{ $fila->anio }}
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($puedeLimpiar)
        <form id="formLimpiarAnio" method="POST" class="d-none">
            @csrf
            <input type="hidden" name="confirmar_anio" id="confirmarAnioInput">
        </form>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    document.querySelectorAll('.btn-limpiar-anio').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            const anio = btn.dataset.anio;
                            const total = btn.dataset.total;
                            Swal.fire({
                                title: `¿Limpiar los adjuntos de ${anio}?`,
                                html: `Se eliminarán <strong>${total}</strong> archivos de S3 de forma permanente.
                                       Los seguimientos (notas, resultados) se conservan, solo se quita el archivo.<br><br>
                                       Escribe <strong>${anio}</strong> para confirmar:`,
                                input: 'text',
                                inputPlaceholder: anio,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#dc3545',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Sí, limpiar',
                                cancelButtonText: 'Cancelar',
                                preConfirm: (valor) => {
                                    if (valor !== anio) {
                                        Swal.showValidationMessage('El año no coincide.');
                                        return false;
                                    }
                                    return valor;
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    const form = document.getElementById('formLimpiarAnio');
                                    form.action = "{{ url('adjuntos-interacciones') }}/" + anio + "/limpiar";
                                    document.getElementById('confirmarAnioInput').value = result.value;
                                    form.submit();
                                }
                            });
                        });
                    });
                });
            </script>
        @endpush
    @endif
</x-base-layout>
