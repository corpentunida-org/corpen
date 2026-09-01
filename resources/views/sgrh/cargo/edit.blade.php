<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">Editar cargo</h2>
            <p class="text-muted mb-0">{{ $cargo->nombre }}</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('sgrh.cargo.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver al listado
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Datos del cargo</h5>

            <form method="POST" action="{{ route('sgrh.cargo.update', $cargo) }}" id="formEditarCargo">
                @csrf
                @method('PUT')
                @include('sgrh.cargo._form', ['cargo' => $cargo, 'areas' => $areas, 'jornadas' => $jornadas])
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary px-4" id="btnGuardarCargo">
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
                const form = document.getElementById('formEditarCargo');
                const boton = document.getElementById('btnGuardarCargo');
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
