<x-base-layout>
    @section('titlepage', 'Crear Nuevo Crédito')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Formulario de Nuevo Crédito</h5>
                    <div class="card-header-action">
                        {{-- Enlace para volver a la lista principal --}}
                        <a href="{{ route('creditos.credito.index') }}" class="btn btn-sm btn-secondary">
                            <i class="feather-arrow-left me-1"></i> Volver a la lista
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    {{-- Muestra un resumen de errores de validación si existen --}}
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <strong>Por favor, corrige los siguientes errores:</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('creditos.credito.store') }}" method="POST">
                        @csrf  {{-- ¡Muy importante! Token de seguridad de Laravel --}}

                        <div class="row">
                            {{-- Columna Izquierda --}}
                            <div class="col-md-6">
                                {{-- Tercero (Cliente) --}}
                                <div class="mb-3">
                                    <label for="mae_terceros_cod_ter" class="form-label">Cliente (Tercero)</label>
                                    <select class="form-select @error('mae_terceros_cod_ter') is-invalid @enderror" id="mae_terceros_cod_ter" name="mae_terceros_cod_ter" required>
                                        <option value="" disabled selected>-- Selecciona un cliente --</option>
                                        @foreach ($terceros as $tercero)
                                            <option value="{{ $tercero->cod_ter }}" {{ old('mae_terceros_cod_ter') == $tercero->cod_ter ? 'selected' : '' }}>
                                                {{ $tercero->nom_ter }} ({{ $tercero->cod_ter }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('mae_terceros_cod_ter')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Si tienes muchos clientes, considera usar una librería como Select2 para hacer este campo buscable.</small>
                                </div>

                                {{-- Línea de Crédito --}}
                                <div class="mb-3">
                                    <label for="cre_lineas_creditos_id" class="form-label">Línea de Crédito</label>
                                    <select class="form-select @error('cre_lineas_creditos_id') is-invalid @enderror" id="cre_lineas_creditos_id" name="cre_lineas_creditos_id" required>
                                        <option value="" disabled selected>-- Selecciona una línea de crédito --</option>
                                        @foreach ($lineasCredito as $linea)
                                            <option value="{{ $linea->id }}" {{ old('cre_lineas_creditos_id') == $linea->id ? 'selected' : '' }}>
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
                                        <input type="number" class="form-control @error('valor') is-invalid @enderror" id="valor" name="valor" placeholder="Ej: 5000000" value="{{ old('valor') }}" required step="0.01">
                                    </div>
                                    @error('valor')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Número de Cuotas --}}
                                <div class="mb-3">
                                    <label for="cuotas" class="form-label">Número de Cuotas</label>
                                    <input type="number" class="form-control @error('cuotas') is-invalid @enderror" id="cuotas" name="cuotas" placeholder="Ej: 24" value="{{ old('cuotas') }}" required>
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
                                    <input type="date" class="form-control @error('fecha_desembolso') is-invalid @enderror" id="fecha_desembolso" name="fecha_desembolso" value="{{ old('fecha_desembolso') }}">
                                    @error('fecha_desembolso')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Pagaré --}}
                                <div class="mb-3">
                                    <label for="pagare" class="form-label">Número de Pagaré</label>
                                    <input type="text" class="form-control @error('pagare') is-invalid @enderror" id="pagare" name="pagare" placeholder="Ej: PG-00123" value="{{ old('pagare') }}">
                                    @error('pagare')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                {{-- PR (Campo adicional) --}}
                                <div class="mb-3">
                                    <label for="pr" class="form-label">PR</label>
                                    <input type="text" class="form-control @error('pr') is-invalid @enderror" id="pr" name="pr" placeholder="Ingresa el valor de PR" value="{{ old('pr') }}">
                                    @error('pr')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Estado --}}
                                <div class="mb-3">
                                    <label for="cre_estados_id" class="form-label">Estado Inicial</label>
                                    <select class="form-select @error('cre_estados_id') is-invalid @enderror" id="cre_estados_id" name="cre_estados_id" required>
                                        <option value="" disabled selected>-- Selecciona un estado --</option>
                                        @foreach ($estados as $estado)
                                            {{-- Puedes preseleccionar un estado por defecto si lo deseas --}}
                                            <option value="{{ $estado->id }}" {{ old('cre_estados_id', 16) == $estado->id ? 'selected' : '' }}>
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

                        {{-- Pie del formulario con los botones de acción --}}
                        <div class="card-footer text-end mt-4">
                            <a href="{{ route('creditos.credito.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar Crédito</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>