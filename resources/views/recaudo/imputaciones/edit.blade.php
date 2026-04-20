<x-base-layout>
    <div class="task-index-wrapper">
        <header class="index-header">
            <div class="header-titles">
                <span class="system-tag">Módulo de Recaudo</span>
                <h1 class="main-title">Editar Imputación #{{ $recImputacionContable->id_recibo }}</h1>
                <p class="main-subtitle">Actualiza los valores, estados o adjunta el soporte documental.</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('recaudo.imputaciones.index') }}" class="btn-ghost-corporate">
                    <i class="fas fa-arrow-left"></i> <span>Volver al listado</span>
                </a>
            </div>
        </header>

        <div class="form-card-corp">
            <form action="{{ route('recaudo.imputaciones.update', $recImputacionContable->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Campos de Solo Lectura (Informativos) --}}
                <div class="grid-3 mb-4 readonly-section">
                    <div class="readonly-item">
                        <span class="label">Transacción Bancaria</span>
                        <strong>Tx: {{ $recImputacionContable->id_transaccion }}</strong>
                    </div>
                    <div class="readonly-item">
                        <span class="label">Tercero Asociado</span>
                        <strong>{{ $recImputacionContable->id_tercero_origen }}</strong>
                    </div>
                    <div class="readonly-item">
                        <span class="label">Distrito</span>
                        <strong>{{ $recImputacionContable->id_distrito }}</strong>
                    </div>
                </div>

                <hr class="divider-corp mb-4">

                <div class="grid-2">
                    <div class="form-group-corp">
                        <label>Concepto Contable</label>
                        <textarea name="concepto_contable" class="form-control-corp" rows="3" required>{{ old('concepto_contable', $recImputacionContable->concepto_contable) }}</textarea>
                    </div>

                    <div class="form-group-corp">
                        <label>Valor Imputado ($)</label>
                        <input type="number" name="valor_imputado" class="form-control-corp" required value="{{ old('valor_imputado', $recImputacionContable->valor_imputado) }}">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group-corp">
                        <label>Estado de Conciliación</label>
                        <select name="estado_conciliacion" class="form-control-corp" required>
                            <option value="Pendiente" {{ $recImputacionContable->estado_conciliacion == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="Conciliado_Auto" {{ $recImputacionContable->estado_conciliacion == 'Conciliado_Auto' ? 'selected' : '' }}>Conciliado Auto</option>
                            <option value="Conciliado_Manual" {{ $recImputacionContable->estado_conciliacion == 'Conciliado_Manual' ? 'selected' : '' }}>Conciliado Manual</option>
                            <option value="Anulado" {{ $recImputacionContable->estado_conciliacion == 'Anulado' ? 'selected' : '' }}>Anulado</option>
                        </select>
                    </div>

                    <div class="form-group-corp">
                        <label>Enlace ECM (Soporte Documental)</label>
                        <input type="url" name="link_ecm" class="form-control-corp" value="{{ old('link_ecm', $recImputacionContable->link_ecm) }}">
                    </div>
                </div>

                <div class="form-actions mt-4 text-right">
                    <button type="submit" class="btn-corporate-black">
                        <i class="fas fa-sync-alt"></i> Actualizar Imputación
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    @include('recaudo.imputaciones.partials.styles')
</x-base-layout>