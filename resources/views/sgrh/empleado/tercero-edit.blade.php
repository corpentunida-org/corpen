<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">Datos del tercero</h2>
            <p class="text-muted mb-0">
                cod_ter: <strong>{{ $tercero->cod_ter }}</strong> ·
                Vista acotada para RR. HH. — solo identificación, datos personales, ubicación y contacto.
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('sgrh.empleado.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver a colaboradores
            </a>
        </div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('sgrh.tercero.update', $tercero->cod_ter) }}" class="card-body">
            @csrf
            @method('PUT')

            @include('sgrh.empleado._tercero-campos', ['editable' => true])

            <div class="d-flex flex-row-reverse gap-2 mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="feather-save me-2"></i> Guardar cambios
                </button>
                <a href="{{ route('sgrh.empleado.index') }}" class="btn btn-light">Cancelar</a>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif
            @if ($errors->any())
                toastr.error("{{ $errors->first() }}");
            @endif
        </script>
    @endpush
</x-base-layout>
