<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">Datos del tercero (solo lectura)</h2>
            <p class="text-muted mb-0">
                cod_ter: <strong>{{ $tercero->cod_ter }}</strong> ·
                Vista acotada para RR. HH. — solo identificación, datos personales, ubicación y contacto.
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            @can('sgrh.tercero.edit')
                <a href="{{ route('sgrh.tercero.edit', $tercero->cod_ter) }}" class="btn btn-primary">
                    <i class="bi bi-pencil-square"></i> Editar
                </a>
            @endcan
            <a href="{{ route('sgrh.empleado.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver a colaboradores
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @include('sgrh.empleado._tercero-campos', ['editable' => false])
        </div>
    </div>
</x-base-layout>
