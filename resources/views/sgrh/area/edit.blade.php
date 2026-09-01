<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">Editar área</h2>
            <p class="text-muted mb-0">{{ $area->nombre }}</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('sgrh.area.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver al listado
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Datos del área</h5>

            <form method="POST" action="{{ route('sgrh.area.update', $area) }}" id="formEditarArea">
                @csrf
                @method('PUT')
                @include('sgrh.area._form', ['area' => $area, 'cargos' => $cargos])
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary px-4" id="btnGuardarArea">
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
                const form = document.getElementById('formEditarArea');
                const boton = document.getElementById('btnGuardarArea');
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
