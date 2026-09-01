<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">Registrar contrato</h2>
            @if ($empleadoSeleccionado)
                <p class="text-muted mb-0">Colaborador: {{ $empleadoSeleccionado->nombre_completo }}</p>
            @endif
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ $empleadoSeleccionado ? route('sgrh.empleado.edit', $empleadoSeleccionado) : route('sgrh.contrato.index') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Datos del contrato</h5>

            <form method="POST" action="{{ route('sgrh.contrato.store') }}" enctype="multipart/form-data" id="formRegistrarContrato">
                @csrf
                @include('sgrh.contrato._form', ['contrato' => null])
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary px-4" id="btnGuardarContrato">
                        <i class="bi bi-check-circle"></i> Guardar contrato
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            @if ($errors->any())
                toastr.error("{{ $errors->first() }}");
            @endif

            (function () {
                const form = document.getElementById('formRegistrarContrato');
                const boton = document.getElementById('btnGuardarContrato');
                if (!form || !boton) {
                    return;
                }
                form.addEventListener('submit', function () {
                    if (!form.checkValidity()) {
                        return;
                    }
                    boton.disabled = true;
                    boton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Guardando...';
                });
            })();
        </script>
    @endpush
</x-base-layout>
