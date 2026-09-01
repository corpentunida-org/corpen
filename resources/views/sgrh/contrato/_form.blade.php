{{-- Espera: $contrato (null en creación), $tiposContrato, $cargos, y en creación además
     $empleadoSeleccionado/$empleados/$cargoIdPrefill/$tipoContratoIdPrefill --}}
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-user me-1 text-primary"></i>Colaborador
        </label>
        @if ($contrato)
            <input type="text" class="form-control" value="{{ $contrato->empleado->nombre_completo }}" disabled>
            <input type="hidden" name="empleado_id" value="{{ $contrato->empleado_id }}">
        @elseif ($empleadoSeleccionado)
            <input type="text" class="form-control" value="{{ $empleadoSeleccionado->nombre_completo }}" disabled>
            <input type="hidden" name="empleado_id" value="{{ $empleadoSeleccionado->id }}">
        @else
            <select name="empleado_id" class="form-select @error('empleado_id') is-invalid @enderror" required>
                <option value="">Selecciona un colaborador</option>
                @foreach ($empleados as $empleado)
                    <option value="{{ $empleado->id }}" @selected((string) old('empleado_id') === (string) $empleado->id)>
                        {{ $empleado->nombre_completo ?: 'Tercero no encontrado' }}
                    </option>
                @endforeach
            </select>
            @error('empleado_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endif
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-file-text me-1 text-primary"></i>Tipo de contrato
        </label>
        <select name="tipo_contrato_id" class="form-select @error('tipo_contrato_id') is-invalid @enderror" required>
            <option value="">Selecciona un tipo</option>
            @foreach ($tiposContrato as $tipo)
                <option value="{{ $tipo->id }}"
                        @selected((string) old('tipo_contrato_id', $contrato->tipo_contrato_id ?? $tipoContratoIdPrefill ?? '') === (string) $tipo->id)>
                    {{ $tipo->nombre }}
                </option>
            @endforeach
        </select>
        @error('tipo_contrato_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-briefcase me-1 text-primary"></i>Cargo
        </label>
        <select name="cargo_id" class="form-select @error('cargo_id') is-invalid @enderror">
            <option value="">Sin definir</option>
            @foreach ($cargos as $cargo)
                <option value="{{ $cargo->id }}"
                        @selected((string) old('cargo_id', $contrato->cargo_id ?? $cargoIdPrefill ?? '') === (string) $cargo->id)>
                    {{ $cargo->nombre }}{{ $cargo->area ? " ({$cargo->area->nombre})" : '' }}
                </option>
            @endforeach
        </select>
        @error('cargo_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-activity me-1 text-primary"></i>Estado
        </label>
        <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
            @foreach (['Activo', 'Vencido', 'Liquidado', 'Renovado'] as $opcion)
                <option value="{{ $opcion }}" @selected(old('estado', $contrato->estado ?? 'Activo') === $opcion)>{{ $opcion }}</option>
            @endforeach
        </select>
        @error('estado')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-calendar me-1 text-primary"></i>Fecha de inicio
        </label>
        <input type="date" name="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror"
               value="{{ old('fecha_inicio', optional($contrato?->fecha_inicio)->format('Y-m-d') ?? $fechaInicioPrefill ?? now()->format('Y-m-d')) }}" required>
        @error('fecha_inicio')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-calendar me-1 text-primary"></i>Fecha de vencimiento
        </label>
        <input type="date" name="fecha_vencimiento" class="form-control @error('fecha_vencimiento') is-invalid @enderror"
               value="{{ old('fecha_vencimiento', optional($contrato?->fecha_vencimiento)->format('Y-m-d') ?? $fechaVencimientoPrefill ?? null) }}">
        <div class="form-text">Vacío = contrato indefinido.</div>
        @error('fecha_vencimiento')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-calendar me-1 text-primary"></i>Fecha de cierre real
        </label>
        <input type="date" name="fecha_terminacion_real" class="form-control @error('fecha_terminacion_real') is-invalid @enderror"
               value="{{ old('fecha_terminacion_real', optional($contrato?->fecha_terminacion_real)->format('Y-m-d')) }}">
        <div class="form-text">Solo si el cierre real difirió de la fecha pactada (ej. terminación anticipada).</div>
        @error('fecha_terminacion_real')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-dollar-sign me-1 text-primary"></i>Salario del contrato
        </label>
        <input type="number" step="0.01" min="0" name="salario_contrato" class="form-control @error('salario_contrato') is-invalid @enderror"
               value="{{ old('salario_contrato', $contrato->salario_contrato ?? '') }}">
        @error('salario_contrato')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-paperclip me-1 text-primary"></i>PDF firmado del contrato
        </label>
        @if ($contrato?->documento_path)
            <div class="mb-2">
                <a href="{{ route('sgrh.contrato.documento.ver', $contrato) }}" target="_blank" class="small">
                    <i class="bi bi-file-earmark-pdf"></i> Ver documento actual
                </a>
            </div>
        @endif
        <input type="file" name="documento" accept="application/pdf" class="form-control @error('documento') is-invalid @enderror">
        <div class="form-text">Solo PDF, máx. 5 MB. @if ($contrato?->documento_path) Subir uno nuevo reemplaza el actual. @endif</div>
        @error('documento')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-message-square me-1 text-primary"></i>Observaciones
        </label>
        <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror" rows="3">{{ old('observaciones', $contrato->observaciones ?? '') }}</textarea>
        @error('observaciones')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
