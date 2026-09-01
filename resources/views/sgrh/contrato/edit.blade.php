<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">Editar contrato</h2>
            <p class="text-muted mb-0">{{ $contrato->empleado->nombre_completo }}</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('sgrh.empleado.edit', $contrato->empleado_id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Datos del contrato</h5>

            <form method="POST" action="{{ route('sgrh.contrato.update', $contrato) }}" enctype="multipart/form-data" id="formEditarContrato">
                @csrf
                @method('PUT')
                @include('sgrh.contrato._form', ['contrato' => $contrato])
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary px-4" id="btnGuardarContrato">
                        <i class="bi bi-check-circle"></i> Guardar cambios
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
                const form = document.getElementById('formEditarContrato');
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
