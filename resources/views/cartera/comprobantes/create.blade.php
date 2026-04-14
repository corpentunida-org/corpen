<x-base-layout>
    <div class="app-container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('cartera.comprobantes.index') }}" class="btn btn-icon btn-light btn-sm me-3 shadow-sm rounded-circle">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h3 class="fw-bolder m-0 text-dark fs-2">Registrar Comprobante</h3>
                        <p class="text-muted m-0 fs-6">Sube el documento del pago para su posterior conciliación</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px;">
                    <div class="card-body p-5">
                        {{-- IMPORTANTE: enctype="multipart/form-data" es obligatorio para subir archivos --}}
                        <form action="{{ route('cartera.comprobantes.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row g-4 mb-4">
                                {{-- Información del Tercero y Monto --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-bolder text-gray-800 fs-6">Código Tercero (Siasoft) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-light rounded-start-pill text-muted"><i class="fas fa-user-tag"></i></span>
                                        <input type="number" name="cod_ter_MaeTerceros" class="form-control rounded-end-pill border-light bg-light fs-6" placeholder="Ej: 5544" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bolder text-gray-800 fs-6">Monto Pagado *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-light rounded-start-pill text-muted">$</span>
                                        <input type="number" name="monto_pagado" class="form-control rounded-end-pill border-light bg-light fs-6 fw-bold text-success" placeholder="0" required>
                                    </div>
                                </div>

                                {{-- Fecha e Interacción --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-bolder text-gray-800 fs-6">Fecha de Pago *</label>
                                    <input type="number" name="fecha_pago" class="form-control rounded-pill border-light bg-light fs-6" placeholder="Ej: 20260414" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bolder text-gray-800 fs-6">ID Interacción CRM *</label>
                                    <input type="number" name="id_interaction" class="form-control rounded-pill border-light bg-light fs-6" placeholder="ID de la gestión en CRM" required>
                                </div>

                                {{-- Archivo y Referencias (Opcionales al subir) --}}
                                <div class="col-md-12 border-top border-light pt-4 mt-4">
                                    <h5 class="fw-bolder mb-4">Documento y Referencias</h5>
                                </div>
                                
                                <div class="col-md-12 mb-2">
                                    <label class="form-label fw-bolder text-gray-800 fs-6">Subir Soporte (PDF, JPG, PNG) *</label>
                                    <input type="file" name="archivo_soporte" class="form-control rounded border-light bg-light fs-6" accept=".pdf,.jpg,.jpeg,.png" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bolder text-gray-800 fs-6">Hash / Referencia Única <span class="text-muted fw-normal">(Opcional)</span></label>
                                    <input type="text" name="hash_transaccion" class="form-control rounded-pill border-light bg-light fs-6 font-monospace" placeholder="TRX-...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bolder text-gray-800 fs-6">ID Transacción Bancaria <span class="text-muted fw-normal">(Opcional)</span></label>
                                    <input type="number" name="id_transaccion_bancaria" class="form-control rounded-pill border-light bg-light fs-6" placeholder="ID del extracto vinculado">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3 mt-5 pt-3 border-top border-light">
                                <a href="{{ route('cartera.comprobantes.index') }}" class="btn btn-clean btn-active-light text-gray-700 rounded-pill fs-6 px-5 py-3 fw-bold">Cancelar</a>
                                <button type="submit" class="btn btn-primary rounded-pill fs-6 px-5 py-3 fw-bolder shadow-sm">
                                    <i class="fas fa-cloud-upload-alt me-2"></i> Subir y Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        body, .app-container { font-family: 'Inter', sans-serif !important; }
        .input-group-text { border-right: none; }
        .input-group .form-control { border-left: none; }
        .btn-clean { background-color: transparent; border: none; }
        .btn-clean:hover { background-color: #f3f6f9; }
        input[type="file"]::file-selector-button {
            background-color: #f1f1f4;
            border: none;
            border-right: 1px solid #e4e6ef;
            padding: 0.75rem 1rem;
            margin-right: 1rem;
            color: #3f4254;
            font-weight: 600;
            transition: all 0.2s;
        }
        input[type="file"]::file-selector-button:hover { background-color: #e4e6ef; }
    </style>
</x-base-layout>