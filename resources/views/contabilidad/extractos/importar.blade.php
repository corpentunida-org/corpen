<x-base-layout>
    <div class="app-container py-8" style="background-color: #f4f7f9; min-height: 100vh;">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                
                {{-- Encabezado Minimalista --}}
                <div class="d-flex align-items-center mb-5">
                    <a href="{{ route('contabilidad.extractos.index') }}" class="btn btn-icon btn-outline-secondary btn-sm me-4 rounded-circle transition-all bg-white shadow-sm" style="width: 40px; height: 40px;">
                        <i class="fas fa-arrow-left fs-5 text-gray-600"></i>
                    </a>
                    <div>
                        <h2 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;">Importar Extracto Bancario</h2>
                        <p class="text-muted m-0 fs-6 mt-1">Sincroniza tus movimientos subiendo el archivo generado por tu banco.</p>
                    </div>
                </div>

                {{-- Tarjeta Principal --}}
                <div class="card border-0 shadow-sm bg-white" style="border-radius: 16px;">
                    <div class="card-body p-sm-5 p-4">
                        <form id="form-importacion" action="{{ route('contabilidad.extractos.procesar-importacion') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            {{-- PASO 1: Cuenta Bancaria --}}
                            <div class="mb-5">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="step-indicator me-3 bg-light-primary text-primary fw-bolder rounded-circle d-flex justify-content-center align-items-center" style="width: 32px; height: 32px; font-size: 14px;">1</span>
                                    <h4 class="fw-bold text-gray-800 m-0 fs-5">Selecciona la Cuenta</h4>
                                </div>
                                <div class="ms-sm-5 ps-sm-2">
                                    <select name="id_con_cuentas_bancaria" id="id_con_cuentas_bancaria" class="form-select form-select-solid border-gray-200 bg-light fs-6 text-dark py-3 cursor-pointer" style="border-radius: 10px;" required>
                                        <option value="" disabled selected>Elige una cuenta bancaria activa...</option>
                                        @foreach($cuentas as $cuenta)
                                            <option value="{{ $cuenta->id }}">{{ $cuenta->banco }} - {{ $cuenta->numero_cuenta }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <hr class="text-gray-200 my-5">

                            {{-- PASO 2: Archivo --}}
                            <div class="mb-5">
                                <div class="d-flex align-items-center mb-3 justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <span class="step-indicator me-3 bg-light-primary text-primary fw-bolder rounded-circle d-flex justify-content-center align-items-center" style="width: 32px; height: 32px; font-size: 14px;">2</span>
                                        <h4 class="fw-bold text-gray-800 m-0 fs-5">Carga el Documento</h4>
                                    </div>
                                    <a href="{{ route('contabilidad.extractos.plantilla') }}" class="text-primary text-decoration-none fs-7 fw-bold hover-underline">
                                        <i class="fas fa-download me-1"></i> Bajar Plantilla Base
                                    </a>
                                </div>
                                
                                <div class="ms-sm-5 ps-sm-2">
                                    {{-- Zona de Arrastre --}}
                                    <div id="drop-zone" class="drop-zone border-dashed border-2 rounded-3 text-center p-5 position-relative bg-light transition-all" style="border-color: #cbd5e1;">
                                        
                                        <input type="file" id="archivo_extracto" name="archivo_extracto" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" style="z-index: 10;" required>
                                        
                                        {{-- Estado Inicial (Vacío) --}}
                                        <div id="upload-prompt" class="d-flex flex-column align-items-center transition-all">
                                            <div class="upload-icon-wrapper mb-3 bg-white shadow-sm rounded-circle d-flex justify-content-center align-items-center" style="width: 64px; height: 64px;">
                                                <i class="fas fa-cloud-upload-alt fs-2 text-primary"></i>
                                            </div>
                                            <h5 class="fw-bold text-gray-800 mb-1">Haz clic para buscar o arrastra el archivo</h5>
                                            <p class="text-muted fs-7 mb-3">Formatos: .XLSX, .XLS, .CSV (Máx. 5MB)</p>
                                            <div class="badge bg-light text-gray-600 border border-gray-300 fs-8 px-3 py-2 fw-normal">
                                                Columnas req: <strong class="text-dark">Fecha, Cédula, Valor, Oficina</strong>
                                            </div>
                                        </div>

                                        {{-- Estado de Archivo Seleccionado (Oculto inicialmente) --}}
                                        <div id="file-preview" class="d-none w-100 text-start position-relative" style="z-index: 20;">
                                            <div class="d-flex align-items-center p-3 bg-white border border-gray-200 rounded-3 shadow-sm">
                                                <div class="me-4 ms-2">
                                                    <i id="preview-icon" class="fas fa-file-excel fs-1 text-success"></i>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <h6 id="preview-filename" class="fw-bold text-dark mb-1 text-truncate" style="max-width: 90%;">nombre_archivo.xlsx</h6>
                                                    <span id="preview-filesize" class="text-muted fs-8">0 KB</span>
                                                </div>
                                                <button type="button" id="btn-remove-file" class="btn btn-icon btn-sm btn-light-danger rounded-circle ms-2 transition-all" title="Eliminar archivo">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                    <div id="error-message" class="text-danger fs-8 mt-2 d-none fw-bold"><i class="fas fa-exclamation-circle me-1"></i> <span id="error-text"></span></div>
                                </div>
                            </div>

                            {{-- Botonera de Acción --}}
                            <div class="d-flex justify-content-end align-items-center mt-5 pt-3 border-top border-gray-100">
                                <a href="{{ route('contabilidad.extractos.index') }}" class="btn btn-link text-muted text-decoration-none fw-bold fs-6 me-4 transition-all hover-dark">
                                    Cancelar
                                </a>
                                <button type="submit" id="btn-submit" class="btn btn-primary fs-6 px-6 py-3 fw-bold shadow-sm" style="border-radius: 8px;" disabled>
                                    <i class="fas fa-eye me-2"></i> Procesar y Previsualizar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                {{-- Tips de Seguridad corporativos --}}
                <div class="text-center mt-4 text-muted fs-8 d-flex justify-content-center align-items-center gap-4">
                    <span><i class="fas fa-lock me-1"></i> Conexión Cifrada</span>
                    <span><i class="fas fa-shield-alt me-1"></i> Hash Antiduplicados Activo</span>
                </div>

            </div>
        </div>
    </div>

    {{-- ESTILOS PERSONALIZADOS UX/UI --}}
    <style>
        .form-select-solid {
            background-color: #f8f9fa;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        .form-select-solid:focus {
            background-color: #fff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        .drop-zone {
            transition: all 0.3s ease;
        }
        .drop-zone:hover, .drop-zone.dragover {
            background-color: #eff6ff !important;
            border-color: #3b82f6 !important;
        }
        .drop-zone.has-file {
            background-color: #f8fafc !important;
            border-style: solid;
            border-color: #e2e8f0 !important;
            padding: 1.5rem !important;
        }
        .upload-icon-wrapper {
            transition: transform 0.3s ease;
        }
        .drop-zone:hover .upload-icon-wrapper {
            transform: translateY(-5px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        }
        .hover-dark:hover { color: #1e293b !important; }
        .hover-underline:hover { text-decoration: underline !important; }
    </style>

    {{-- JAVASCRIPT EXTENDIDO PARA UX Y VALIDACIÓN --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('archivo_extracto');
            const dropZone = document.getElementById('drop-zone');
            const uploadPrompt = document.getElementById('upload-prompt');
            const filePreview = document.getElementById('file-preview');
            const previewFilename = document.getElementById('preview-filename');
            const previewFilesize = document.getElementById('preview-filesize');
            const btnRemoveFile = document.getElementById('btn-remove-file');
            const selectCuenta = document.getElementById('id_con_cuentas_bancaria');
            const btnSubmit = document.getElementById('btn-submit');
            const errorMsgContainer = document.getElementById('error-message');
            const errorText = document.getElementById('error-text');

            const maxFileSize = 5 * 1024 * 1024; // 5 MB en bytes
            const validExtensions = ['csv', 'xls', 'xlsx'];

            // Función para habilitar/deshabilitar el botón de submit
            function checkFormValidity() {
                if (fileInput.files.length > 0 && selectCuenta.value !== "") {
                    btnSubmit.disabled = false;
                } else {
                    btnSubmit.disabled = true;
                }
            }

            // Formateador de bytes a KB/MB
            function formatBytes(bytes, decimals = 2) {
                if (!+bytes) return '0 Bytes';
                const k = 1024;
                const dm = decimals < 0 ? 0 : decimals;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
            }

            // Mostrar error
            function showError(msg) {
                errorText.textContent = msg;
                errorMsgContainer.classList.remove('d-none');
                resetDropZone();
            }

            // Ocultar error
            function clearError() {
                errorMsgContainer.classList.add('d-none');
            }

            // Lógica cuando se selecciona un archivo (vía click o drag)
            function handleFile(file) {
                clearError();
                
                // Extraer extensión
                const fileName = file.name;
                const fileExt = fileName.split('.').pop().toLowerCase();

                // Validar extensión
                if (!validExtensions.includes(fileExt)) {
                    showError('Formato no válido. Solo se permiten archivos .csv, .xls o .xlsx');
                    return;
                }

                // Validar peso
                if (file.size > maxFileSize) {
                    showError('El archivo es demasiado pesado. El máximo permitido es 5 MB.');
                    return;
                }

                // Si todo es correcto, actualizar UI
                uploadPrompt.classList.add('d-none');
                filePreview.classList.remove('d-none');
                dropZone.classList.add('has-file');
                fileInput.classList.add('d-none'); // Esconder el input para que no se le de click por error

                previewFilename.textContent = fileName;
                previewFilesize.textContent = formatBytes(file.size);
                
                // Cambiar ícono según formato
                const icon = document.getElementById('preview-icon');
                if(fileExt === 'csv') {
                    icon.className = 'fas fa-file-csv fs-1 text-info';
                } else {
                    icon.className = 'fas fa-file-excel fs-1 text-success';
                }

                checkFormValidity();
            }

            // Resetear el Dropzone a su estado inicial
            function resetDropZone() {
                fileInput.value = '';
                uploadPrompt.classList.remove('d-none');
                filePreview.classList.add('d-none');
                dropZone.classList.remove('has-file');
                fileInput.classList.remove('d-none');
                checkFormValidity();
            }

            // --- EVENT LISTENERS ---

            // Cambio en el input select
            selectCuenta.addEventListener('change', checkFormValidity);

            // Cambio en el input de archivo
            fileInput.addEventListener('change', function (e) {
                if (this.files && this.files.length > 0) {
                    handleFile(this.files[0]);
                }
            });

            // Botón eliminar archivo
            btnRemoveFile.addEventListener('click', function(e) {
                e.preventDefault(); // Evitar submit
                resetDropZone();
            });

            // Drag & Drop visuales
            fileInput.addEventListener('dragenter', () => dropZone.classList.add('dragover'));
            fileInput.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
            fileInput.addEventListener('drop', () => dropZone.classList.remove('dragover'));
        });
    </script>
</x-base-layout>