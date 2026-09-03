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

    @if ($desactualizado ?? false)
        <div class="alert d-flex align-items-center justify-content-between gap-3 mb-4" style="background-color: #ffe4e6; border: 1px solid #fbb6c2; color: #e11d48; border-radius: 12px;">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-exclamation-triangle-fill fs-3"></i>
                <div>
                    <strong>Información de usuario requiere actualizar</strong>
                    <span class="d-block small">Última actualización: {{ $tercero->fec_act ? \Illuminate\Support\Carbon::parse($tercero->fec_act)->format('d/m/Y') : 'nunca registrada' }}</span>
                </div>
            </div>
            @can('sgrh.tercero.edit')
                <a href="{{ route('sgrh.tercero.edit', $tercero->cod_ter) }}" class="btn btn-sm flex-shrink-0"
                   style="background-color: #e11d48; color: #fff; font-weight: bold;">
                    <i class="bi bi-pencil-square"></i> ACTUALIZAR / EDITAR TERCERO
                </a>
            @endcan
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @include('sgrh.empleado._tercero-campos', ['editable' => false])
        </div>
    </div>
</x-base-layout>
