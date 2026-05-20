<x-base-layout>
    @php
        $comprobante = null;
        $estadoConfig = ['color' => 'secondary', 'icon' => 'fa-question', 'text' => 'Desconocido'];

        // Si existe el extracto, procesamos la lógica de la ficha
        if(isset($extracto)) {
            $comprobante = \App\Models\Cartera\CarComprobantePago::with(['user', 'obligacion', 'tercero', 'interaccion'])
                ->whereJsonContains('id_transaccion_bancaria', $extracto->id_transaccion)
                ->orWhereJsonContains('id_transaccion_bancaria', (string) $extracto->id_transaccion)
                ->first();

            $estadoConfig = match($extracto->estado_conciliacion) {
                'Conciliado_Auto'   => ['color' => 'success', 'icon' => 'fa-robot', 'text' => 'Conciliado Auto'],
                'Conciliado_Manual' => ['color' => 'primary', 'icon' => 'fa-user-check', 'text' => 'Conciliado Manual'],
                'Pendiente'         => ['color' => 'warning', 'icon' => 'fa-clock', 'text' => 'Pendiente'],
                'Anulado'           => ['color' => 'danger',  'icon' => 'fa-times-circle', 'text' => 'Anulado'],
                default             => ['color' => 'secondary', 'icon' => 'fa-question', 'text' => 'Desconocido']
            };
        }
    @endphp

    <div class="task-index-wrapper p-4" style="background-color: #fafbfc; min-height: 100vh;">
        
        {{-- 1. Encabezado de Navegación --}}
        <header class="index-header mb-5">
            <div class="d-flex justify-content-between align-items-center">
                <div class="header-titles">
                    <span class="badge bg-light-primary text-primary fw-bold text-uppercase tracking-wider mb-2 fs-7">Módulo de Recaudo / Gestión Contable</span>
                    <h1 class="main-title fw-bolder text-gray-900 mb-1">Nueva Imputación de Recaudo</h1>
                    <p class="text-muted fs-6">Vincule movimientos bancarios con terceros y conceptos contables.</p>
                </div>
                <div class="header-actions">
                    <a href="{{ route('recaudo.imputaciones.index') }}" class="btn btn-light shadow-sm fw-bolder hover-elevate-up fs-6">
                        <i class="fas fa-chevron-left me-2"></i> Volver al listado
                    </a>
                </div>
            </div>
        </header>

        {{-- Manejo de Errores General --}}
        @if ($errors->any())
            <div class="alert-error-modern mb-5">
                <div class="alert-icon"><i class="fas fa-exclamation-circle text-danger fs-2"></i></div>
                <div class="alert-content">
                    <strong class="text-danger fs-6">Atención:</strong> Por favor verifique los campos marcados en rojo.
                    <ul class="mb-0 fs-7 text-danger mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- LAYOUT DIVIDIDO: Izquierda Formulario | Derecha Ficha --}}
        <div class="row g-6">
            
            {{-- ==========================================
                 COLUMNA IZQUIERDA: FORMULARIO PRINCIPAL
                 ========================================== --}}
            <div class="col-xl-7">
                <div class="form-card-corp shadow-sm">
                    <form action="{{ route('recaudo.imputaciones.store') }}" method="POST" autocomplete="off" id="form-recaudo">
                        @csrf

                        <div class="row g-7">
                            {{-- Lado Izquierdo Interno: Vinculación Bancaria y Terceros --}}
                            <div class="col-md-12">
                                <div class="section-divider mb-5">
                                    <span class="fs-6 fw-bolder text-primary text-uppercase tracking-wider"><i class="fas fa-link me-2"></i>1. Datos de Vinculación</span>
                                </div>

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-group-corp">
                                            <label class="form-label-audit">ID Transacción (Extracto)</label>
                                            <div class="input-with-icon">
                                                <i class="fas fa-university fs-5"></i>
                                                <input type="number" name="id_transaccion" class="form-control-corp bg-light-primary border-primary border-opacity-25 @error('id_transaccion') is-invalid @enderror" 
                                                       required value="{{ old('id_transaccion', $extracto->id_transaccion ?? request('id_transaccion')) }}" 
                                                       placeholder="ID del banco" {{ request('id_transaccion') ? 'readonly' : '' }}>
                                            </div>
                                            @error('id_transaccion') <span class="invalid-feedback-force">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-corp">
                                            <label class="form-label-audit">Tercero (Cédula/NIT)</label>
                                            <div class="input-with-icon">
                                                <i class="fas fa-id-card fs-5"></i>
                                                <input type="text" name="id_tercero_origen" class="form-control-corp @error('id_tercero_origen') is-invalid @enderror" 
                                                       required value="{{ old('id_tercero_origen') }}" placeholder="Documento">
                                            </div>
                                            @error('id_tercero_origen') <span class="invalid-feedback-force">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-corp">
                                            <label class="form-label-audit">Código de Distrito</label>
                                            <div class="input-with-icon">
                                                <i class="fas fa-map-marked-alt fs-5"></i>
                                                <input type="text" name="id_distrito" class="form-control-corp @error('id_distrito') is-invalid @enderror" 
                                                       required value="{{ old('id_distrito') }}" placeholder="Ej: 01">
                                            </div>
                                            @error('id_distrito') <span class="invalid-feedback-force">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-corp">
                                            <label class="form-label-audit">Número de Recibo Físico</label>
                                            <div class="input-with-icon">
                                                <i class="fas fa-file-invoice fs-5"></i>
                                                <input type="number" name="id_recibo" class="form-control-corp @error('id_recibo') is-invalid @enderror" 
                                                       required value="{{ old('id_recibo') }}" placeholder="Consecutivo físico">
                                            </div>
                                            @error('id_recibo') <span class="invalid-feedback-force">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group-corp mt-6">
                                    <label class="form-label-audit">Concepto Contable (Descripción)</label>
                                    <textarea name="concepto_contable" class="form-control-corp @error('concepto_contable') is-invalid @enderror" rows="3" 
                                              required placeholder="Especifique detalladamente el motivo del recaudo...">{{ old('concepto_contable') }}</textarea>
                                    @error('concepto_contable') <span class="invalid-feedback-force">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Lado Derecho Interno: Cifras y Control --}}
                            <div class="col-md-12">
                                <div class="section-divider mb-5 mt-4">
                                    <span class="fs-6 fw-bolder text-success text-uppercase tracking-wider"><i class="fas fa-coins me-2"></i>2. Cifras y Estado</span>
                                </div>

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="card-highlight-corp h-100 @error('valor_imputado') border-danger-force @enderror">
                                            <div class="form-group-corp p-4 h-100 d-flex flex-column justify-content-center">
                                                <label class="form-label-audit text-white opacity-75 fs-7">Valor Total a Imputar</label>
                                                <div class="d-flex align-items-center mt-2">
                                                    <span class="fs-1 text-white fw-bolder me-2">$</span>
                                                    <input type="text" id="valor_visual" class="form-control-transparent" 
                                                           placeholder="0" value="{{ old('valor_imputado') ? number_format(old('valor_imputado'), 0, ',', '.') : '' }}">
                                                    <input type="hidden" name="valor_imputado" id="valor_real" value="{{ old('valor_imputado') }}">
                                                </div>
                                            </div>
                                        </div>
                                        @error('valor_imputado') <span class="invalid-feedback-force mt-2">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-corp mb-4">
                                            <label class="form-label-audit">Estado de Conciliación</label>
                                            <select name="estado_conciliacion" class="form-select-corp @error('estado_conciliacion') is-invalid @enderror" required>
                                                <option value="Pendiente" {{ old('estado_conciliacion') == 'Pendiente' ? 'selected' : '' }}>PENDIENTE</option>
                                                <option value="Conciliado_Manual" {{ old('estado_conciliacion') == 'Conciliado_Manual' ? 'selected' : '' }}>CONCILIADO MANUAL</option>
                                                <option value="Conciliado_Auto" {{ old('estado_conciliacion') == 'Conciliado_Auto' ? 'selected' : '' }}>CONCILIADO AUTO</option>
                                            </select>
                                            @error('estado_conciliacion') <span class="invalid-feedback-force">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="form-group-corp">
                                            <label class="form-label-audit">Soporte ECM (URL Documento)</label>
                                            <div class="input-with-icon">
                                                <i class="fas fa-paperclip fs-5"></i>
                                                <input type="url" name="link_ecm" class="form-control-corp @error('link_ecm') is-invalid @enderror" 
                                                       value="{{ old('link_ecm') }}" placeholder="https://ecm.siasoft.com/...">
                                            </div>
                                            @error('link_ecm') <span class="invalid-feedback-force">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions-corp pt-6 mt-4 border-top border-gray-100 d-flex gap-3">
                                    <button type="reset" class="btn btn-light fs-6 fw-bold px-6 py-3">LIMPIAR</button>
                                    <button type="submit" class="btn-corporate-black flex-grow-1 py-3 fs-5">
                                        <i class="fas fa-save me-2"></i> Registrar Imputación
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ==========================================
                 COLUMNA DERECHA: FICHA PROFESIONAL DEL EXTRACTO
                 ========================================== --}}
            <div class="col-xl-5">
                <div class="sticky-top" style="top: 20px;">
                    @if(isset($extracto) || request('id_transaccion'))
                        
                        {{-- Encabezado Lateral --}}
                        <div class="d-flex align-items-center justify-content-between mb-4 px-2">
                            <div>
                                <h3 class="fw-bolder m-0 text-gray-900 fs-3 d-flex align-items-center">
                                    <i class="fas fa-file-invoice text-primary me-2"></i> Ficha Técnica
                                </h3>
                                <p class="text-muted m-0 fs-6 fw-semibold mt-1">ID Sistema: <span class="text-dark font-monospace fw-bolder">#{{ $extracto->id_transaccion ?? request('id_transaccion') }}</span></p>
                            </div>
                            @if(isset($extracto))
                                <span class="badge bg-light-{{ $estadoConfig['color'] }} text-{{ $estadoConfig['color'] }} border border-{{ $estadoConfig['color'] }} border-opacity-25 fs-7 px-4 py-2 rounded-pill fw-bolder">
                                    <i class="fas {{ $estadoConfig['icon'] }} me-1"></i> {{ $estadoConfig['text'] }}
                                </span>
                            @endif
                        </div>

                        {{-- SECCIÓN 1: ORIGEN BANCARIO --}}
                        <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: 12px;">
                            <div class="card-header bg-transparent border-bottom border-gray-100 py-4 px-5 min-h-auto">
                                <h5 class="fw-bolder m-0 text-gray-800 fs-4 d-flex align-items-center">
                                    <div class="symbol symbol-30px me-3">
                                        <div class="symbol-label bg-light-info"><i class="fas fa-university text-info fs-5"></i></div>
                                    </div>
                                    Datos de Origen Bancario
                                </h5>
                            </div>
                            <div class="card-body p-6">
                                @if(isset($extracto))
                                    {{-- Monto Principal --}}
                                    <div class="mb-5 p-4 bg-light-success rounded-3 border border-success border-opacity-25 d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="text-success fw-bolder text-uppercase ls-1 fs-8 mb-1 d-block">Importe Recibido</span>
                                            <span class="text-gray-900 fw-bolder fs-2 lh-1">$ {{ number_format($extracto->valor_ingreso, 2, ',', '.') }}</span>
                                        </div>
                                        <i class="fas fa-arrow-down text-success opacity-50" style="font-size: 2.5rem;"></i>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-6">
                                            <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Entidad Financiera</span>
                                            <span class="text-gray-800 fw-bolder fs-6 text-truncate d-block" title="{{ $extracto->cuentaBancaria->banco ?? 'N/A' }}">{{ $extracto->cuentaBancaria->banco ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-8 mb-1">N° Cuenta</span>
                                            <span class="text-gray-800 fw-bolder font-monospace fs-6">{{ $extracto->cuentaBancaria->numero_cuenta ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-8 mb-1">F. Acreditación</span>
                                            <span class="text-gray-800 fw-bolder fs-6">{{ $extracto->fecha_movimiento->format('d M, Y H:i') }}</span>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Ref. Cédula</span>
                                            <span class="text-primary fw-bolder font-monospace fs-6">{{ $extracto->referencia_cedula ?? '---' }}</span>
                                        </div>
                                        <div class="col-12 mt-4">
                                            <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Hash Criptográfico</span>
                                            <div class="d-flex align-items-center bg-light p-3 rounded border border-gray-200">
                                                <code class="text-dark fs-6 font-monospace text-break flex-grow-1">{{ $extracto->hash_transaccion }}</code>
                                                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary ms-2 h-30px w-30px" onclick="copyToClipboard('{{ $extracto->hash_transaccion }}')" title="Copiar">
                                                    <i class="fas fa-copy fs-5"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center p-5">
                                        <span class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;"></span>
                                        <p class="text-dark fw-bold fs-5 m-0">Cargando datos del extracto...</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- SECCIÓN 2: VINCULACIÓN CARTERA --}}
                        @if(isset($extracto))
                            <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px;">
                                <div class="card-header bg-transparent border-bottom border-gray-100 py-4 px-5 min-h-auto d-flex justify-content-between align-items-center">
                                    <h5 class="fw-bolder m-0 text-gray-800 fs-4 d-flex align-items-center">
                                        <div class="symbol symbol-30px me-3">
                                            <div class="symbol-label bg-light-primary"><i class="fas fa-project-diagram text-primary fs-5"></i></div>
                                        </div>
                                        Integración Cartera
                                    </h5>
                                    @if($comprobante)
                                        <span class="badge badge-light-success fs-7 fw-bolder px-3 py-2"><i class="fas fa-link text-success me-2 fs-7"></i> VINCULADO</span>
                                    @else
                                        <span class="badge badge-light-danger fs-7 fw-bolder px-3 py-2"><i class="fas fa-unlink text-danger me-2 fs-7"></i> SIN VINCULAR</span>
                                    @endif
                                </div>
                                
                                <div class="card-body p-6">
                                    @if($comprobante)
                                        <div class="row g-5">
                                            <div class="col-6">
                                                <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Tercero Identificado</span>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-circle text-gray-400 fs-3 me-2"></i>
                                                    <span class="text-gray-900 fw-bolder fs-5">{{ $comprobante->cod_ter_MaeTerceros }}</span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Línea / Obligación</span>
                                                <span class="text-primary fw-bolder fs-6 text-truncate d-block" title="{{ $comprobante->obligacion->nombre ?? 'N/A' }}">{{ $comprobante->obligacion->nombre ?? 'N/A' }}</span>
                                            </div>
                                            
                                            <div class="col-4">
                                                <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Cuota N°</span>
                                                <span class="badge badge-light-dark fs-6 fw-bolder px-3 py-1">{{ $comprobante->numero_cuota ?? 'N/A' }}</span>
                                            </div>
                                            <div class="col-4">
                                                <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-8 mb-1">PR</span>
                                                <span class="text-gray-800 fw-bolder fs-5">{{ $comprobante->pr ?? '0' }}</span>
                                            </div>
                                            <div class="col-4">
                                                <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-8 mb-1">CCO</span>
                                                <span class="text-gray-800 fw-bolder fs-5">{{ $comprobante->cco ?? '0' }}</span>
                                            </div>

                                            <div class="col-12 mt-4 pt-4 border-top border-gray-100">
                                                <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-8 mb-2">Gestor y Observaciones</span>
                                                <div class="fs-6 text-gray-800 fw-bolder mb-2"><i class="fas fa-user-tie text-muted me-2 fs-5"></i> {{ $comprobante->user->name ?? 'Sistema' }}</div>
                                                <div class="bg-light p-3 rounded-3 text-gray-700 fs-6 fst-italic">
                                                    "{{ $comprobante->observacion ?? 'Sin observaciones registradas.' }}"
                                                </div>
                                            </div>

                                            <div class="col-12 mt-5">
                                                @if($comprobante->url_archivo && $comprobante->url_archivo !== '#')
                                                    <button type="button" onclick="abrirModalSoporte('{{ $comprobante->url_archivo }}', 'Soporte de {{ $comprobante->cod_ter_MaeTerceros }}')" class="btn btn-primary w-100 fw-bolder py-3 fs-5 d-flex align-items-center justify-content-center hover-elevate-up shadow-sm">
                                                        <i class="fas fa-file-pdf fs-3 me-2"></i> Ver Soporte Original
                                                    </button>
                                                @else
                                                    <div class="alert alert-warning border-warning border-dashed d-flex align-items-center p-3 mb-0">
                                                        <i class="fas fa-exclamation-triangle fs-3 text-warning me-3"></i>
                                                        <span class="text-dark fs-6 fw-bolder">Este comprobante no posee soporte documental adjunto en el sistema.</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <div class="bg-light-warning p-4 rounded-circle d-inline-block mb-4">
                                                <i class="fas fa-inbox text-warning fs-1"></i>
                                            </div>
                                            <h6 class="text-dark fw-bolder mb-2 fs-4">No Procesado Aún</h6>
                                            <p class="text-muted fs-6 px-4 mb-0">Este extracto no tiene un comprobante de cartera vinculado.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                    @else
                        {{-- Placeholder Vacío --}}
                        <div class="card border border-dashed border-gray-300 bg-transparent h-100 d-flex flex-column align-items-center justify-content-center p-8 text-center" style="min-height: 400px; border-radius: 12px;">
                            <i class="fas fa-search-dollar fs-4x text-gray-300 mb-4"></i>
                            <span class="fs-4 fw-bolder text-gray-600 mb-2">Sin Extracto de Origen</span>
                            <span class="fs-6 text-muted px-4">Ingrese un ID de Transacción válido en el formulario para cargar la ficha técnica automáticamente.</span>
                        </div>
                    @endif
                </div>
            </div>
            
        </div>
    </div>

    {{-- MODAL VISOR DE SOPORTES S3 --}}
    <div class="modal fade" id="modalVisorSoporte" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light-primary border-bottom border-primary border-opacity-10 py-4 px-6">
                    <h5 class="modal-title fw-bolder text-gray-800 d-flex align-items-center gap-3 fs-3">
                        <i class="fas fa-file-pdf text-primary fs-1"></i> 
                        <span id="visorSoporteTitulo">Soporte Documental</span>
                    </h5>
                    <div class="d-flex align-items-center gap-3">
                        <a href="#" id="btnDescargarSoporte" target="_blank" class="btn btn-sm btn-light-primary fw-bolder px-4 py-2" data-bs-toggle="tooltip" title="Abrir en pestaña nueva">
                            <i class="fas fa-external-link-alt me-2"></i> Abrir PDF Externo
                        </a>
                        <button type="button" class="btn-close fs-4" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-0 bg-dark position-relative" style="height: 75vh;">
                    <iframe id="visorSoporteFrame" src="" class="w-100 h-100 border-0 bg-white"></iframe>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-card-corp {
            background: white; border-radius: 16px; padding: 40px; border: 1px solid #e2e8f0;
        }

        .section-divider { border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; }

        .form-label-audit {
            font-size: 0.9rem; font-weight: 800; color: #475569; text-transform: uppercase;
            letter-spacing: 0.5px; margin-bottom: 8px; display: block;
        }

        .input-with-icon { position: relative; }
        .input-with-icon i {
            position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
            color: #64748b; font-size: 1.1rem;
        }
        .input-with-icon .form-control-corp { padding-left: 50px; }

        .form-control-corp {
            border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 10px;
            padding: 16px 20px; font-size: 1rem; font-weight: 600; transition: all 0.2s;
            width: 100%; color: #1e293b;
        }
        .form-control-corp:focus {
            border-color: #3b82f6; background: #ffffff; outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .is-invalid { border-color: #ef4444 !important; background-color: #fff5f5 !important; }
        .border-danger-force { border: 2px solid #ef4444 !important; }
        .invalid-feedback-force { display: block; width: 100%; margin-top: 8px; font-size: 0.9rem; color: #ef4444; font-weight: 700; }

        .card-highlight-corp {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 12px; box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .form-control-transparent {
            background: transparent; border: none; color: #10b981;
            font-size: 3rem; font-weight: 800; width: 100%; padding: 0;
        }
        .form-control-transparent:focus { outline: none; }
        .form-control-transparent::placeholder { color: rgba(16, 185, 129, 0.3); }

        .form-select-corp {
            width: 100%; padding: 16px 20px; border-radius: 10px; border: 1px solid #cbd5e1;
            background: #f8fafc; font-weight: 700; font-size: 1rem; color: #1e293b;
        }

        .alert-error-modern {
            display: flex; align-items: flex-start; gap: 15px; background: #fef2f2;
            border: 1px solid #fecaca; border-radius: 12px; padding: 20px;
        }

        .btn-corporate-black {
            background: #0f172a; color: white; border-radius: 10px;
            font-weight: 800; border: none; transition: all 0.3s;
        }
        .btn-corporate-black:hover { background: #000; transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,0,0,0.15); }
        
        /* Utilidades Ficha Derecha */
        .ls-1 { letter-spacing: 0.05em; }
        .border-dashed { border-style: dashed !important; border-width: 2px !important; }
        .text-break { word-wrap: break-word; overflow-wrap: break-word; }
        .hover-elevate-up { transition: transform 0.2s ease; }
        .hover-elevate-up:hover { transform: translateY(-3px); }
    </style>

    <script>
        let modalVisor;

        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar Formateador de Valor
            const vVisual = document.getElementById('valor_visual');
            const vReal = document.getElementById('valor_real');

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

            // Inicializar Modal de PDF
            const modalEl = document.getElementById('modalVisorSoporte');
            if (modalEl) {
                modalVisor = new bootstrap.Modal(modalEl);
                modalEl.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('visorSoporteFrame').src = '';
                });
            }

            // Inicializar Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });

        // Abrir Modal de S3
        window.abrirModalSoporte = function(url, titulo) {
            if(!url || url === '#') {
                alert('El documento no está disponible.');
                return;
            }
            document.getElementById('visorSoporteTitulo').innerText = titulo;
            document.getElementById('btnDescargarSoporte').href = url;
            document.getElementById('visorSoporteFrame').src = url;
            modalVisor.show();
        };

        // Copiar Hash
        window.copyToClipboard = function(text) {
            navigator.clipboard.writeText(text).then(() => {
                if(typeof toastr !== 'undefined') {
                    toastr.success('Hash copiado al portapapeles', '¡Copiado!');
                } else {
                    alert('Hash copiado: ' + text);
                }
            });
        }
    </script>
</x-base-layout>