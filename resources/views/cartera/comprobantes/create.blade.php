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
                        <form action="{{ route('cartera.comprobantes.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row g-4 mb-4">
                                {{-- FILA 1: Tercero y Obligación --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-bolder text-gray-800 fs-6">Código Tercero (Siasoft) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-light rounded-start-pill text-muted"><i class="fas fa-user-tag"></i></span>
                                        <input type="number" id="cod_tercero" name="cod_ter_MaeTerceros" class="form-control rounded-end-pill border-light bg-light fs-6 gen-hash" placeholder="Ej: 5544" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bolder text-gray-800 fs-6">Obligación / Línea de Crédito *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-light rounded-start-pill text-muted"><i class="fas fa-file-contract"></i></span>
                                        <select class="form-select rounded-end-pill border-light bg-light select2" id="id_obligacion" name="id_obligacion" required>
                                            <option value="">Seleccione obligación...</option>
                                            @if (isset($lineasCredito))
                                                @foreach ($lineasCredito as $id => $nombre)
                                                    <option value="{{ $id }}">{{ $nombre }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                {{-- FILA 2: Banco y Monto --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-bolder text-gray-800 fs-6">Banco *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-light rounded-start-pill text-muted"><i class="fas fa-university"></i></span>
                                        <select class="form-select rounded-end-pill border-light bg-light select2" id="id_banco" name="id_banco" required>
                                            <option value="">Seleccione banco...</option>
                                            @if (isset($idBanco))
                                                @foreach ($idBanco as $idb)
                                                    <option value="{{ $idb->id }}">{{ $idb->numero_cuenta }} - {{ $idb->banco }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bolder text-gray-800 fs-6">Monto Pagado *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-light rounded-start-pill text-muted">$</span>
                                        <input type="text" id="monto_pagado_display" class="form-control rounded-end-pill border-light bg-light fs-6 fw-bold text-success gen-hash" placeholder="0" required>
                                        <input type="hidden" id="monto_pagado" name="monto_pagado">
                                    </div>
                                </div>

                                {{-- FILA 3: Fecha, Cuota, PR, CCO (Integración de nuevos campos) --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-bolder text-gray-800 fs-6">Fecha de Pago *</label>
                                    <input type="date" id="fecha_pago" name="fecha_pago" class="form-control rounded-pill border-light bg-light fs-6 gen-hash" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bolder text-gray-800 fs-6">Número de Cuota</label>
                                    <input type="number" name="numero_cuota" class="form-control rounded-pill border-light bg-light fs-6" placeholder="Ej: 1">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bolder text-gray-800 fs-6">PR</label>
                                    <input type="number" name="pr" class="form-control rounded-pill border-light bg-light fs-6" placeholder="0">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bolder text-gray-800 fs-6">CCO</label>
                                    <input type="number" name="cco" class="form-control rounded-pill border-light bg-light fs-6" placeholder="0">
                                </div>
                                
                                {{-- CAMPO OCULTO: ID Interacción CRM --}}
                                <input type="hidden" name="id_interaction" value="">

                                {{-- Archivo y Referencias --}}
                                <div class="col-md-12 border-top border-light pt-4 mt-4">
                                    <h5 class="fw-bolder mb-4">Documento y Referencias</h5>
                                </div>
                                
                                <div class="col-md-12 mb-2" id="drop_zone">
                                    <label class="form-label fw-bolder text-gray-800 fs-6">Subir Soporte (PDF, JPG, PNG) * <span class="text-muted fw-normal fs-7">(Puedes pegar una imagen con Ctrl+V)</span></label>
                                    <input type="file" id="archivo_soporte" name="archivo_soporte" class="form-control rounded border-light bg-light fs-6" accept=".pdf,.jpg,.jpeg,.png" required>
                                    
                                    <div id="preview_container" class="mt-3 d-none">
                                        <p class="text-muted mb-1 fs-7">Previsualización:</p>
                                        <img id="image_preview" src="" alt="Previsualización" class="img-thumbnail shadow-sm" style="max-height: 150px; border-radius: 8px;">
                                        <div id="file_name_preview" class="alert alert-secondary py-2 mt-2 d-none"></div>
                                    </div>
                                </div>

                                {{-- Hash --}}
                                <div class="col-md-12">
                                    <label class="form-label fw-bolder text-gray-800 fs-6">Hash / Referencia Única</label>
                                    <input type="text" id="hash_transaccion" name="hash_transaccion" class="form-control rounded-pill border-light bg-light fs-6 font-monospace text-muted" placeholder="Autogenerado..." readonly>
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. FORMATO DE MILES PARA EL MONTO
            const displayInput = document.getElementById('monto_pagado_display');
            const hiddenInput = document.getElementById('monto_pagado');

            displayInput.addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, ''); 
                hiddenInput.value = value;
                
                if (value !== '') {
                    this.value = new Intl.NumberFormat('es-CO').format(value);
                } else {
                    this.value = '';
                }
            });

            // 2. GENERACIÓN AUTOMÁTICA DEL HASH (Banco + Fecha + Monto + Tercero)
            const inputsHash = document.querySelectorAll('.gen-hash');
            const inputBanco = document.getElementById('id_banco'); // NUEVO
            const inputHashTarget = document.getElementById('hash_transaccion');
            
            function actualizarHash() {
                const banco = inputBanco.value; 
                const fecha = document.getElementById('fecha_pago').value.replace(/-/g, ''); 
                const monto = hiddenInput.value;
                const tercero = document.getElementById('cod_tercero').value;

                // Ahora se requiere el banco para generar el Hash
                if(banco && fecha && monto && tercero) {
                    inputHashTarget.value = `${banco}-${fecha}-${monto}-${tercero}`;
                } else {
                    inputHashTarget.value = '';
                }
            }

            inputsHash.forEach(input => {
                input.addEventListener('input', actualizarHash);
                input.addEventListener('change', actualizarHash);
            });
            inputBanco.addEventListener('change', actualizarHash);

            // 3. PREVISUALIZACIÓN Y PEGAR IMAGEN (Ctrl+V)
            const fileInput = document.getElementById('archivo_soporte');
            const previewContainer = document.getElementById('preview_container');
            const imagePreview = document.getElementById('image_preview');
            const fileNamePreview = document.getElementById('file_name_preview');

            function handleFile(file) {
                if (!file) return;
                
                previewContainer.classList.remove('d-none');
                
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        imagePreview.src = e.target.result;
                        imagePreview.classList.remove('d-none');
                        fileNamePreview.classList.add('d-none');
                    }
                    reader.readAsDataURL(file);
                } 
                else if (file.type === 'application/pdf') {
                    imagePreview.classList.add('d-none');
                    fileNamePreview.classList.remove('d-none');
                    fileNamePreview.textContent = '📄 Archivo seleccionado: ' + file.name;
                }
            }

            fileInput.addEventListener('change', function(e) {
                handleFile(e.target.files[0]);
            });

            window.addEventListener('paste', e => {
                if(e.clipboardData.files.length > 0) {
                    const file = e.clipboardData.files[0];
                    if(file.type.startsWith('image/') || file.type === 'application/pdf') {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        fileInput.files = dataTransfer.files;
                        handleFile(file);
                    }
                }
            });
        });
    </script>
</x-base-layout>