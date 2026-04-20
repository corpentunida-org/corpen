<x-base-layout>
    <div class="task-index-wrapper">
        <header class="index-header">
            <div class="header-titles">
                <span class="system-tag">Módulo de Recaudo</span>
                <h1 class="main-title">Nueva Imputación Contable</h1>
                <p class="main-subtitle">Registro manual de comprobantes y asignación contable.</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('recaudo.imputaciones.index') }}" class="btn-ghost-corporate">
                    <i class="fas fa-arrow-left"></i> <span>Volver al listado</span>
                </a>
            </div>
        </header>

        <div class="form-card-corp">
            
            {{-- BLOQUE DE ERRORES DE VALIDACIÓN --}}
            @if ($errors->any())
                <div style="background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; padding: 15px; border-radius: 8px; margin-bottom: 24px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <strong style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-exclamation-triangle"></i> Por favor corrige los siguientes errores:
                    </strong>
                    <ul style="margin-top: 10px; margin-bottom: 0; font-size: 0.9rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('recaudo.imputaciones.store') }}" method="POST">
                @csrf

                <div class="grid-3">
                    <div class="form-group-corp">
                        <label>ID Transacción (Extracto)</label>
                        <input type="number" name="id_transaccion" class="form-control-corp" required value="{{ old('id_transaccion') }}" placeholder="Ej. 10025">
                        @error('id_transaccion') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group-corp">
                        <label>Tercero Origen (Cédula/NIT)</label>
                        <input type="text" name="id_tercero_origen" class="form-control-corp" required value="{{ old('id_tercero_origen') }}" placeholder="Documento del tercero">
                        @error('id_tercero_origen') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group-corp">
                        <label>Código de Distrito</label>
                        <input type="text" name="id_distrito" class="form-control-corp" required value="{{ old('id_distrito') }}" placeholder="Ej. 01">
                        @error('id_distrito') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid-3">
                    <div class="form-group-corp">
                        <label>Número de Recibo</label>
                        <input type="number" name="id_recibo" class="form-control-corp" required value="{{ old('id_recibo') }}">
                        @error('id_recibo') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group-corp">
                        <label>Valor Imputado ($)</label>
                        <input type="number" name="valor_imputado" class="form-control-corp" required value="{{ old('valor_imputado') }}">
                        @error('valor_imputado') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group-corp">
                        <label>Estado de Conciliación</label>
                        <select name="estado_conciliacion" class="form-control-corp" required>
                            <option value="Pendiente" {{ old('estado_conciliacion') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="Conciliado_Auto" {{ old('estado_conciliacion') == 'Conciliado_Auto' ? 'selected' : '' }}>Conciliado Auto</option>
                            <option value="Conciliado_Manual" {{ old('estado_conciliacion') == 'Conciliado_Manual' ? 'selected' : '' }}>Conciliado Manual</option>
                            <option value="Anulado" {{ old('estado_conciliacion') == 'Anulado' ? 'selected' : '' }}>Anulado</option>
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group-corp">
                        <label>Concepto Contable</label>
                        <textarea name="concepto_contable" class="form-control-corp" rows="3" required placeholder="Descripción del recaudo...">{{ old('concepto_contable') }}</textarea>
                        @error('concepto_contable') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group-corp">
                        <label>Enlace ECM (Soporte Documental)</label>
                        <input type="url" name="link_ecm" class="form-control-corp" value="{{ old('link_ecm') }}" placeholder="https://ecm.empresa.com/doc/123">
                        <small class="text-muted mt-1 d-block"><i class="fas fa-info-circle"></i> Ideal para vincular el PDF indexado del recibo.</small>
                    </div>
                </div>

                <div class="form-actions mt-4 text-right">
                    <button type="submit" class="btn-corporate-black">
                        <i class="fas fa-save"></i> Guardar Imputación
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    @include('recaudo.imputaciones.partials.styles')
</x-base-layout>