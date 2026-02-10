@csrf

@php
    // Determinar si estamos en modo edición
    $isEdit = isset($usuario) && !empty($usuario->id);
@endphp
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>



<div class="mb-4 px-4 d-flex align-items-center justify-content-between">
    <h5 class="fw-bold mb-0">Agentes Que dan Soporte</h5>
    <div>
        <a href="{{ route('maestras.terceros.create') }}" class="btn btn-success btnCrear">
            <i class="feather-plus me-2"></i>
            <span>Crear Tercero</span>
        </a>
        <p class="fs-12 text-muted mb-0">Si no existe en terceros crear primero, para crearlo en agente soportes</p>
    </div>
</div>

<div class="row">
    {{-- ID (solo lectura en edición) --}}
    @if ($isEdit)
        <div class="col-md-3 mb-3">
            <label for="id" class="form-label text-capitalize">ID</label>
            <input type="text" class="form-control" id="id" name="id"
                value="{{ substr(md5($usuario->id . 'clave-secreta'), 0, 6) }}" readonly>
        </div>
    @endif

    {{-- Campo: Agente --}}
    <div class="col-md-8 mb-3">
        <label for="cod_ter" class="form-label">Agente</label>
        @if (!$isEdit)
            {{-- Create: Select editable (Select2) --}}
            <select class="form-select select-user" name="cod_ter">
                <option value="">Selecciona</option>
                @foreach ($terceros as $user)
                    <option value="{{ $user->nid }}"> {{ $user->name }} - {{ $user->email }}</option>
                @endforeach
            </select>
        @else
            {{-- Edit: Mostrar texto y hidden --}}
            <div class="form-control" aria-readonly="true">
                {{ $usuario->cod_ter }} - {{ optional($terceros->firstWhere('cod_ter', $usuario->cod_ter))->nom_ter }}
            </div>
            <input type="hidden" name="cod_ter" value="{{ old('cod_ter', $usuario->cod_ter) }}">
        @endif

        @error('cod_ter')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- Campo: Estado --}}
    <div class="col-md-4 mb-3">
        <label for="estado" class="form-label text-capitalize">Estado</label>
        <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado" required>
            <option value="">Selecciona un estado</option>
            <option value="Activo" {{ old('estado', $usuario->estado ?? '') == 'Activo' ? 'selected' : '' }}>Activo
            </option>
            <option value="Inactivo" {{ old('estado', $usuario->estado ?? '') == 'Inactivo' ? 'selected' : '' }}>
                Inactivo</option>
        </select>
        @error('estado')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Fechas de creación y actualización (solo lectura en edición) --}}
    @if ($isEdit)
        <div class="col-md-6 mb-3">
            <label for="created_at" class="form-label text-capitalize">Creado el</label>
            <input type="text" class="form-control" id="created_at"
                value="{{ $usuario->created_at?->format('Y-m-d H:i') ?? 'N/A' }}" readonly>
        </div>

        <div class="col-md-6 mb-3">
            <label for="updated_at" class="form-label text-capitalize">Actualizado el</label>
            <input type="text" class="form-control" id="updated_at"
                value="{{ $usuario->updated_at?->format('Y-m-d H:i') ?? 'N/A' }}" readonly>
        </div>
    @endif
</div>
<script>
    $(document).ready(function() {
        $('.select-user').select2({ width: '100%' });
    });
</script>

