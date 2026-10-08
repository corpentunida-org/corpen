@csrf
<div class="row">
    {{-- Columna Izquierda --}}
    <div class="col-md-6">
        {{-- Tercero (Cliente) --}}
        <div class="mb-3">
            <label for="mae_terceros_cod_ter" class="form-label">Cliente (Tercero)</label>
            <select class="form-select @error('mae_terceros_cod_ter') is-invalid @enderror" id="mae_terceros_cod_ter" name="mae_terceros_cod_ter" required>
                @if (isset($terceroActual) && $terceroActual)
                    <option value="{{ $terceroActual->cod_ter }}" selected>{{ $terceroActual->nom_ter }} ({{ $terceroActual->cod_ter }})</option>
                @else
                    <option value="" disabled selected>-- Busca un cliente por nombre o cédula --</option>
                @endif
            </select>
            @error('mae_terceros_cod_ter')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Línea de Crédito --}}
        <div class="mb-3">
            <label for="cre_lineas_creditos_id" class="form-label">Línea de Crédito</label>
            <select class="form-select @error('cre_lineas_creditos_id') is-invalid @enderror" id="cre_lineas_creditos_id" name="cre_lineas_creditos_id" required>
                <option value="" disabled {{ old('cre_lineas_creditos_id', $credito->cre_lineas_creditos_id ?? '') ? '' : 'selected' }}>-- Selecciona una línea de crédito --</option>
                @foreach ($lineasCredito as $linea)
                    <option value="{{ $linea->id }}" {{ old('cre_lineas_creditos_id', $credito->cre_lineas_creditos_id ?? '') == $linea->id ? 'selected' : '' }}>
                        {{ $linea->nombre }}
                    </option>
                @endforeach
            </select>
            @error('cre_lineas_creditos_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Valor del Crédito --}}
        <div class="mb-3">
            <label for="valor" class="form-label">Valor del Crédito</label>
            <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" class="form-control @error('valor') is-invalid @enderror" id="valor" name="valor" placeholder="Ej: 5000000" value="{{ old('valor', $credito->valor ?? '') }}" required step="0.01">
            </div>
            @error('valor')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        {{-- Número de Cuotas --}}
        <div class="mb-3">
            <label for="cuotas" class="form-label">Número de Cuotas</label>
            <input type="number" class="form-control @error('cuotas') is-invalid @enderror" id="cuotas" name="cuotas" placeholder="Ej: 24" value="{{ old('cuotas', $credito->cuotas ?? '') }}" required>
            @error('cuotas')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Columna Derecha --}}
    <div class="col-md-6">
        {{-- Fecha de Desembolso --}}
        <div class="mb-3">
            <label for="fecha_desembolso" class="form-label">Fecha de Desembolso</label>
            <input type="date" class="form-control @error('fecha_desembolso') is-invalid @enderror" id="fecha_desembolso" name="fecha_desembolso" value="{{ old('fecha_desembolso', optional($credito->fecha_desembolso ?? null)->format('Y-m-d')) }}" required>
            @error('fecha_desembolso')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Pagaré --}}
        <div class="mb-3">
            <label for="pagare" class="form-label">Número de Pagaré</label>
            <input type="text" class="form-control @error('pagare') is-invalid @enderror" id="pagare" name="pagare" placeholder="Ej: PG-00123" value="{{ old('pagare', $credito->pagare ?? '') }}" required>
            @error('pagare')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- PR (Campo adicional) --}}
        <div class="mb-3">
            <label for="pr" class="form-label">PR</label>
            <input type="text" class="form-control @error('pr') is-invalid @enderror" id="pr" name="pr" placeholder="Ingresa el valor de PR" value="{{ old('pr', $credito->pr ?? '') }}" required>
            @error('pr')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Estado --}}
        <div class="mb-3">
            <label for="cre_estados_id" class="form-label">Estado</label>
            <select class="form-select @error('cre_estados_id') is-invalid @enderror" id="cre_estados_id" name="cre_estados_id" required>
                <option value="" disabled {{ old('cre_estados_id', $credito->cre_estados_id ?? 16) ? '' : 'selected' }}>-- Selecciona un estado --</option>
                @foreach ($estados as $estado)
                    <option value="{{ $estado->id }}" {{ old('cre_estados_id', $credito->cre_estados_id ?? 16) == $estado->id ? 'selected' : '' }}>
                        {{ $estado->nombre }}
                    </option>
                @endforeach
            </select>
            @error('cre_estados_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="card-footer text-end mt-4">
    <a href="{{ route('creditos.credito.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">{{ $buttonText ?? 'Guardar Crédito' }}</button>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof $.fn.select2 !== 'function') {
            console.error('Select2 no está disponible.');
            return;
        }
        $('#mae_terceros_cod_ter').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Busca un cliente por nombre o cédula --',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route('creditos.credito.buscar-tercero') }}',
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term, page: params.page || 1 }),
                processResults: data => ({
                    results: data.results,
                    pagination: { more: data.pagination.more }
                })
            },
            language: {
                inputTooShort: () => 'Escribe al menos 2 letras...',
                noResults: () => 'Sin resultados.',
                searching: () => 'Buscando...'
            }
        });
    });
</script>
@endpush
