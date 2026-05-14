<x-base-layout>
    <div class="task-index-wrapper">
        {{-- 1. Encabezado de Navegación --}}
        <header class="index-header">
            <div class="header-titles">
                <span class="system-tag">Módulo de Recaudo / Gestión Contable</span>
                <h1 class="main-title">Nueva Imputación</h1>
                <p class="main-subtitle">Vincule movimientos bancarios con terceros y conceptos contables.</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('recaudo.imputaciones.index') }}" class="btn-ghost-corporate">
                    <i class="fas fa-chevron-left me-2"></i> Volver al listado
                </a>
            </div>
        </header>

        {{-- 2. Formulario Principal --}}
        <div class="form-card-corp shadow-lg">
            
            {{-- Manejo de Errores General --}}
            @if ($errors->any())
                <div class="alert-error-modern">
                    <div class="alert-icon"><i class="fas fa-exclamation-circle text-danger fs-2"></i></div>
                    <div class="alert-content">
                        <strong>Atención:</strong> Por favor verifique los campos marcados en rojo.
                        <ul class="mb-0 fs-10">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('recaudo.imputaciones.store') }}" method="POST" autocomplete="off" id="form-recaudo">
                @csrf

                <div class="row g-7">
                    {{-- Lado Izquierdo: Vinculación Bancaria y Terceros --}}
                    <div class="col-lg-7">
                        <div class="section-divider mb-5">
                            <span class="fs-9 fw-bold text-primary text-uppercase tracking-wider"><i class="fas fa-link me-2"></i>1. Datos de Vinculación</span>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group-corp">
                                    <label class="form-label-audit">ID Transacción (Extracto)</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-university"></i>
                                        <input type="number" name="id_transaccion" class="form-control-corp @error('id_transaccion') is-invalid @enderror" 
                                               required value="{{ old('id_transaccion') }}" placeholder="ID del banco">
                                    </div>
                                    @error('id_transaccion') <span class="invalid-feedback-force">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-corp">
                                    <label class="form-label-audit">Tercero (Cédula/NIT)</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-id-card"></i>
                                        <input type="text" name="id_tercero_origen" class="form-control-corp @error('id_tercero_origen') is-invalid @enderror" 
                                               required value="{{ old('id_tercero_origen') }}" placeholder="Documento del tercero">
                                    </div>
                                    @error('id_tercero_origen') <span class="invalid-feedback-force">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-corp">
                                    <label class="form-label-audit">Código de Distrito</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-map-marked-alt"></i>
                                        <input type="text" name="id_distrito" class="form-control-corp @error('id_distrito') is-invalid @enderror" 
                                               required value="{{ old('id_distrito') }}" placeholder="Ej: 01">
                                    </div>
                                    @error('id_distrito') <span class="invalid-feedback-force">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-corp">
                                    <label class="form-label-audit">Número de Recibo</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-file-invoice"></i>
                                        <input type="number" name="id_recibo" class="form-control-corp @error('id_recibo') is-invalid @enderror" 
                                               required value="{{ old('id_recibo') }}" placeholder="Número físico">
                                    </div>
                                    @error('id_recibo') <span class="invalid-feedback-force">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group-corp mt-6">
                            <label class="form-label-audit">Concepto Contable (Descripción)</label>
                            <textarea name="concepto_contable" class="form-control-corp @error('concepto_contable') is-invalid @enderror" rows="4" 
                                      required placeholder="Especifique detalladamente el motivo del recaudo...">{{ old('concepto_contable') }}</textarea>
                            @error('concepto_contable') <span class="invalid-feedback-force">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Lado Derecho: Cifras y Control --}}
                    <div class="col-lg-5">
                        <div class="sticky-top" style="top: 20px;">
                            <div class="section-divider mb-5">
                                <span class="fs-9 fw-bold text-success text-uppercase tracking-wider"><i class="fas fa-coins me-2"></i>2. Cifras y Estado</span>
                            </div>

                            {{-- Card de Valor con puntos de mil --}}
                            <div class="card-highlight-corp mb-2 @error('valor_imputado') border-danger-force @enderror">
                                <div class="form-group-corp p-5">
                                    <label class="form-label-audit text-white opacity-75">Valor Total a Imputar</label>
                                    <div class="d-flex align-items-center">
                                        <span class="fs-2 text-white fw-bold me-2">$</span>
                                        <input type="text" id="valor_visual" class="form-control-transparent" 
                                               placeholder="0" value="{{ old('valor_imputado') ? number_format(old('valor_imputado'), 0, ',', '.') : '' }}">
                                        {{-- Valor real oculto para Laravel --}}
                                        <input type="hidden" name="valor_imputado" id="valor_real" value="{{ old('valor_imputado') }}">
                                    </div>
                                </div>
                            </div>
                            @error('valor_imputado') <span class="invalid-feedback-force mb-4">{{ $message }}</span> @enderror

                            <div class="form-group-corp mb-5">
                                <label class="form-label-audit">Estado de Conciliación</label>
                                <select name="estado_conciliacion" class="form-select-corp @error('estado_conciliacion') is-invalid @enderror" required>
                                    <option value="Pendiente" {{ old('estado_conciliacion') == 'Pendiente' ? 'selected' : '' }}>PENDIENTE</option>
                                    <option value="Conciliado_Manual" {{ old('estado_conciliacion') == 'Conciliado_Manual' ? 'selected' : '' }}>CONCILIADO MANUAL</option>
                                    <option value="Conciliado_Auto" {{ old('estado_conciliacion') == 'Conciliado_Auto' ? 'selected' : '' }}>CONCILIADO AUTO</option>
                                    <option value="Anulado" {{ old('estado_conciliacion') == 'Anulado' ? 'selected' : '' }}>ANULADO</option>
                                </select>
                                @error('estado_conciliacion') <span class="invalid-feedback-force">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group-corp mb-8">
                                <label class="form-label-audit">Soporte ECM (URL Documento)</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-paperclip"></i>
                                    <input type="url" name="link_ecm" class="form-control-corp @error('link_ecm') is-invalid @enderror" 
                                           value="{{ old('link_ecm') }}" placeholder="https://ecm.siasoft.com/...">
                                </div>
                                @error('link_ecm') <span class="invalid-feedback-force">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-actions-corp pt-4">
                                <button type="submit" class="btn-corporate-black w-100 mb-3 py-4">
                                    <i class="fas fa-save me-2"></i> Registrar Imputación
                                </button>
                                <button type="reset" class="btn btn-sm btn-light w-100 fs-9 fw-bold">LIMPIAR CAMPOS</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        .form-card-corp {
            background: white; border-radius: 16px; padding: 40px; border: 1px solid #e2e8f0;
        }

        .section-divider { border-bottom: 2px solid #f1f5f9; padding-bottom: 8px; }

        .form-label-audit {
            font-size: 0.7rem; font-weight: 800; color: #64748b; text-transform: uppercase;
            letter-spacing: 0.5px; margin-bottom: 8px; display: block;
        }

        .input-with-icon { position: relative; }
        .input-with-icon i {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: #94a3b8; font-size: 0.9rem;
        }
        .input-with-icon .form-control-corp { padding-left: 40px; }

        .form-control-corp {
            border: 1px solid #e2e8f0; background: #f8fafc; border-radius: 10px;
            padding: 12px 16px; font-size: 0.9rem; font-weight: 600; transition: all 0.2s;
            width: 100%;
        }

        /* Forzar bordes rojos */
        .is-invalid {
            border-color: #ef4444 !important;
            background-color: #fff5f5 !important;
        }
        
        .border-danger-force { border: 2px solid #ef4444 !important; }
        
        .invalid-feedback-force {
            display: block; width: 100%; margin-top: 5px; font-size: 0.75rem;
            color: #ef4444; font-weight: 700;
        }

        .card-highlight-corp {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 12px; box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .form-control-transparent {
            background: transparent; border: none; color: #10b981;
            font-size: 2.2rem; font-weight: 800; width: 100%; padding: 0;
        }
        .form-control-transparent:focus { outline: none; }

        .form-select-corp {
            width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0;
            background: #f8fafc; font-weight: 700; font-size: 0.85rem;
        }

        .alert-error-modern {
            display: flex; align-items: flex-start; gap: 15px; background: #fef2f2;
            border: 1px solid #fecaca; border-radius: 12px; padding: 15px; margin-bottom: 30px;
        }

        .btn-corporate-black {
            background: #0f172a; color: white; border-radius: 12px;
            font-weight: 700; border: none; transition: all 0.3s;
        }
        .btn-corporate-black:hover { background: #000; transform: translateY(-2px); }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const vVisual = document.getElementById('valor_visual');
            const vReal = document.getElementById('valor_real');

            // Formateador de miles
            vVisual.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, "");
                if (value === "") {
                    vReal.value = "";
                    e.target.value = "";
                    return;
                }
                vReal.value = value;
                e.target.value = new Intl.NumberFormat('es-CO').format(value);
            });
        });
    </script>
</x-base-layout>