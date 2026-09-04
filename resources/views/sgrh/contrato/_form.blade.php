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
        <select name="tipo_contrato_id" id="select_tipo_contrato" class="form-select @error('tipo_contrato_id') is-invalid @enderror" required>
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
        <select name="cargo_id" class="form-select @error('cargo_id') is-invalid @enderror" required>
            <option value="">Selecciona un cargo</option>
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
        <select name="estado" id="select_estado" class="form-select @error('estado') is-invalid @enderror" required>
            @foreach (['Activo', 'Vencido', 'Liquidado', 'Renovado'] as $opcion)
                <option value="{{ $opcion }}" @selected(old('estado', $contrato->estado ?? 'Activo') === $opcion)>{{ $opcion }}</option>
            @endforeach
        </select>
        @error('estado')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-check mt-2 d-none" id="wrapper_retiro_definitivo">
            <input type="checkbox" name="retiro_definitivo" value="1" class="form-check-input" id="input_retiro_definitivo"
                   @checked(old('retiro_definitivo'))>
            <label class="form-check-label small text-danger fw-bold" for="input_retiro_definitivo">
                El colaborador se retira definitivamente (no solo queda sin contrato vigente)
            </label>
        </div>
    </div>

    <div class="col-md-3">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-edit-3 me-1 text-primary"></i>Fecha de creación del contrato
        </label>
        @if ($contrato)
            <input type="text" class="form-control" value="{{ $contrato->fecha_creacion_contrato?->format('Y-m-d') }}" disabled>
            <input type="hidden" name="fecha_creacion_contrato" value="{{ $contrato->fecha_creacion_contrato?->format('Y-m-d') }}">
            <div class="form-text">No se puede modificar una vez creado el contrato.</div>
        @else
            <input type="text" class="form-control" value="{{ now()->format('Y-m-d') }}" disabled>
            <input type="hidden" name="fecha_creacion_contrato" value="{{ now()->format('Y-m-d') }}">
            <div class="form-text">Siempre es la fecha de hoy — no se digita.</div>
        @endif
    </div>
    <div class="col-md-3">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-calendar me-1 text-primary"></i>Fecha de inicio
        </label>
        @if ($contrato)
            <input type="text" class="form-control" value="{{ $contrato->fecha_inicio?->format('Y-m-d') ?? 'Sin definir' }}" disabled>
            <input type="hidden" name="fecha_inicio" value="{{ $contrato->fecha_inicio?->format('Y-m-d') }}">
            <div class="form-text">No se puede modificar una vez creado el contrato.</div>
        @else
            <input type="date" name="fecha_inicio" id="input_fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror"
                   value="{{ old('fecha_inicio', $fechaInicioPrefill ?? now()->format('Y-m-d')) }}">
            <div class="form-text">Obligatoria salvo para contratos a término indefinido.</div>
            @error('fecha_inicio')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endif
    </div>
    <div class="col-md-3">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-calendar me-1 text-primary"></i>Fecha de vencimiento
        </label>
        <input type="date" name="fecha_vencimiento" id="input_fecha_vencimiento" class="form-control @error('fecha_vencimiento') is-invalid @enderror"
               value="{{ old('fecha_vencimiento', session('fechaVencimientoSugerida') ?? optional($contrato?->fecha_vencimiento)->format('Y-m-d') ?? $fechaVencimientoPrefill ?? null) }}">
        <div class="form-text">Para contratos activos: posterior a hoy y dentro del próximo año.</div>
        @if (session('fechaVencimientoSugerida'))
            <div class="form-text text-primary">Sugerida por la renovación — ajústala si es necesario.</div>
        @endif
        @error('fecha_vencimiento')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3">
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
               value="{{ old('salario_contrato', $contrato->salario_contrato ?? '') }}" required>
        @error('salario_contrato')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-paperclip me-1 text-primary"></i>PDF firmado del contrato (enlace)
        </label>
        <div class="input-group">
            <input type="url" name="documento_url" id="input_documento_url" class="form-control @error('documento_url') is-invalid @enderror"
                   value="{{ old('documento_url', $contrato->documento_url ?? '') }}" placeholder="https://...">
            <button type="button" id="btn_ver_documento" class="btn btn-outline-secondary" @disabled(empty($contrato->documento_url ?? old('documento_url')))>
                <i class="bi bi-box-arrow-up-right"></i> Ver
            </button>
            @error('documento_url')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-text">Enlace al documento en el gestor documental — no se sube el archivo aquí.</div>
    </div>

</div>

<script>
    // Fecha de inicio y fecha de vencimiento: obligatorias salvo para contratos a término
    // indefinido — se ajusta en vivo según el tipo seleccionado, sin necesidad de recargar el
    // formulario. (fecha_inicio solo existe editable aquí en creación; en edición ya está
    // deshabilitada y no aplica este toggle.)
    (function () {
        const selectTipo = document.getElementById('select_tipo_contrato');
        const inputVencimiento = document.getElementById('input_fecha_vencimiento');
        const inputInicio = document.getElementById('input_fecha_inicio');
        if (!selectTipo || !inputVencimiento) {
            return;
        }
        function actualizarRequerido() {
            const opcion = selectTipo.options[selectTipo.selectedIndex];
            const esIndefinido = opcion && opcion.text.trim() === 'Indefinido';
            if (esIndefinido) {
                inputVencimiento.removeAttribute('required');
                inputInicio?.removeAttribute('required');
            } else {
                inputVencimiento.setAttribute('required', 'required');
                inputInicio?.setAttribute('required', 'required');
            }
        }
        selectTipo.addEventListener('change', actualizarRequerido);
        actualizarRequerido();
    })();

    // El rango "posterior a hoy y dentro del próximo año" del vencimiento solo tiene sentido
    // para un contrato Activo — un contrato ya cerrado (Vencido/Liquidado/Renovado) conserva su
    // fecha histórica, que puede estar en el pasado.
    (function () {
        const selectEstado = document.getElementById('select_estado');
        const inputVencimiento = document.getElementById('input_fecha_vencimiento');
        if (!selectEstado || !inputVencimiento) {
            return;
        }
        function actualizarRango() {
            if (selectEstado.value === 'Activo') {
                inputVencimiento.min = '{{ now()->addDay()->format('Y-m-d') }}';
                inputVencimiento.max = '{{ (($contrato?->fecha_vencimiento?->isFuture() ? $contrato->fecha_vencimiento : now()))->copy()->addYear()->format('Y-m-d') }}';
            } else {
                inputVencimiento.removeAttribute('min');
                inputVencimiento.removeAttribute('max');
            }
        }
        selectEstado.addEventListener('change', actualizarRango);
        actualizarRango();
    })();

    // El checkbox de retiro definitivo solo aplica (y solo se muestra) cuando el estado pasa a
    // Liquidado — es la única forma de marcar a un colaborador como 'retirado', ver
    // ContratoController::update().
    (function () {
        const selectEstado = document.getElementById('select_estado');
        const wrapper = document.getElementById('wrapper_retiro_definitivo');
        const checkbox = document.getElementById('input_retiro_definitivo');
        if (!selectEstado || !wrapper || !checkbox) {
            return;
        }
        function actualizarVisibilidad() {
            const esLiquidado = selectEstado.value === 'Liquidado';
            wrapper.classList.toggle('d-none', !esLiquidado);
            if (!esLiquidado) {
                checkbox.checked = false;
            }
        }
        selectEstado.addEventListener('change', actualizarVisibilidad);
        actualizarVisibilidad();
    })();
</script>
