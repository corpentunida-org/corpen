<x-base-layout>
    <div class="task-index-wrapper">
        <header class="index-header mb-5">
            <div class="header-titles">
                <span class="system-tag text-uppercase fs-7 text-primary">Módulo de Recaudo</span>
                <h1 class="main-title">Editar Imputación #{{ $recImputacionContable->id_recibo }}</h1>
                <p class="main-subtitle text-muted">Ajuste los valores, clasificación y estado del registro contable.</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('recaudo.imputaciones.index') }}" class="btn-ghost-corporate">
                    <i class="fas fa-arrow-left me-2"></i> <span>Volver al listado</span>
                </a>
            </div>
        </header>

        <div class="form-card-corp shadow-sm">
            <form action="{{ route('recaudo.imputaciones.update', $recImputacionContable->id) }}" method="POST" autocomplete="off">
                @csrf
                @method('PUT')

                {{-- SECCIÓN INFORMATIVA (Solo Lectura) --}}
                <div class="readonly-section mb-5 p-4 bg-light rounded-3 border border-gray-200">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <span class="label text-muted d-block fs-8 text-uppercase fw-bold mb-1">Transacción</span>
                            <strong class="fs-6 text-dark"><i class="fas fa-university me-2 text-primary"></i>Tx: {{ $recImputacionContable->id_transaccion }}</strong>
                        </div>
                        <div class="col-md-3">
                            <span class="label text-muted d-block fs-8 text-uppercase fw-bold mb-1">Tercero</span>
                            <strong class="fs-6 text-dark"><i class="fas fa-user-circle me-2 text-primary"></i>{{ $recImputacionContable->tercero->nom_ter ?? $recImputacionContable->id_tercero_origen }}</strong>
                        </div>
                        <div class="col-md-3">
                            <span class="label text-muted d-block fs-8 text-uppercase fw-bold mb-1">Distrito</span>
                            <strong class="fs-6 text-dark"><i class="fas fa-map-marker-alt me-2 text-primary"></i>{{ $recImputacionContable->distrito->NOM_DIST ?? $recImputacionContable->id_distrito }}</strong>
                        </div>
                        <div class="col-md-3">
                            <span class="label text-muted d-block fs-8 text-uppercase fw-bold mb-1">Registrado por</span>
                            <strong class="fs-6 text-dark"><i class="fas fa-user-shield me-2 text-success"></i>{{ $recImputacionContable->user->name ?? 'Sistema' }}</strong>
                        </div>
                    </div>
                </div>

                <hr class="divider-corp mb-5">

                {{-- CAMPOS EDITABLES --}}
                <div class="row g-4">
                    {{-- Campo: Tipo --}}
                    <div class="col-md-6">
                        <div class="form-group-corp">
                            <label class="form-label-audit">Tipo de Imputación</label>
                            <select name="tipo" class="form-control-corp @error('tipo') is-invalid @enderror" required>
                                <option value="" disabled>Seleccione una opción...</option>
                                @foreach(['Pago Normal', 'Ajuste Contable', 'Saldo a Favor', 'Reintegro', 'Otro'] as $t)
                                    <option value="{{ $t }}" {{ old('tipo', $recImputacionContable->tipo) == $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                            @error('tipo') <span class="invalid-feedback-force">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Campo: Valor --}}
                    <div class="col-md-6">
                        <div class="form-group-corp">
                            <label class="form-label-audit">Valor Imputado ($)</label>
                            <input type="number" name="valor_imputado" class="form-control-corp @error('valor_imputado') is-invalid @enderror" 
                                   required value="{{ old('valor_imputado', $recImputacionContable->valor_imputado) }}">
                            @error('valor_imputado') <span class="invalid-feedback-force">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group-corp mt-4">
                    <label class="form-label-audit">Concepto Contable</label>
                    <textarea name="concepto_contable" class="form-control-corp @error('concepto_contable') is-invalid @enderror" rows="3" required>{{ old('concepto_contable', $recImputacionContable->concepto_contable) }}</textarea>
                    @error('concepto_contable') <span class="invalid-feedback-force">{{ $message }}</span> @enderror
                </div>

                <div class="row g-4 mt-1">
                    <div class="col-md-6">
                        <div class="form-group-corp">
                            <label class="form-label-audit">Estado de Conciliación</label>
                            <select name="estado_conciliacion" class="form-control-corp @error('estado_conciliacion') is-invalid @enderror" required>
                                @foreach(['Pendiente', 'Conciliado_Auto', 'Conciliado_Manual', 'Anulado'] as $est)
                                    <option value="{{ $est }}" {{ old('estado_conciliacion', $recImputacionContable->estado_conciliacion) == $est ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', $est) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('estado_conciliacion') <span class="invalid-feedback-force">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group-corp">
                            <label class="form-label-audit">Enlace ECM (Soporte)</label>
                            <input type="url" name="link_ecm" class="form-control-corp @error('link_ecm') is-invalid @enderror" 
                                   value="{{ old('link_ecm', $recImputacionContable->link_ecm) }}" placeholder="https://...">
                            @error('link_ecm') <span class="invalid-feedback-force">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="form-actions mt-5 d-flex gap-3 justify-content-end">
                    <a href="{{ route('recaudo.imputaciones.index') }}" class="btn btn-light px-6 py-3">CANCELAR</a>
                    <button type="submit" class="btn-corporate-black px-6 py-3">
                        <i class="fas fa-sync-alt me-2"></i> Actualizar Imputación
                    </button>
                </div>
            </form>
        </div>
    </div>
    @include('recaudo.imputaciones.partials.styles')
</x-base-layout>