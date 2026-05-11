<x-base-layout>
    <div class="app-container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('contabilidad.extractos.index') }}" class="btn btn-icon btn-light btn-sm me-3 shadow-sm rounded-circle">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h3 class="fw-bolder m-0 text-dark fs-2">Importar Extracto</h3>
                        <p class="text-muted m-0 fs-6">Sube el archivo Excel o CSV generado por el banco</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px;">
                    <div class="card-body p-5">
                        <form action="{{ route('contabilidad.extractos.procesar-importacion') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label fw-bolder text-dark fs-5">Selecciona la Cuenta Bancaria</label>
                                <select name="id_con_cuentas_bancaria" class="form-select form-select-lg rounded-pill border-light bg-light fs-5" required>
                                    <option value="" disabled selected>Elige una cuenta activa...</option>
                                    @foreach($cuentas as $cuenta)
                                        <option value="{{ $cuenta->id }}">{{ $cuenta->banco }} - {{ $cuenta->numero_cuenta }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-5">
                                <label class="form-label fw-bolder text-dark fs-5">Archivo del Banco</label>
                                
                                {{-- Contenedor principal con un ID para el JavaScript --}}
                                <div id="drop-zone" class="border border-2 border-dashed border-primary rounded-3 text-center p-5 bg-light-primary position-relative" style="transition: all 0.3s;">
                                    
                                    {{-- INPUT DE ARCHIVO (Se agregó el ID 'archivo_extracto') --}}
                                    <input type="file" id="archivo_extracto" name="archivo_extracto" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" accept=".csv, .txt, .xls, .xlsx" style="z-index: 1;" required>
                                    
                                    {{-- Ícono y textos con IDs para actualizarlos dinámicamente --}}
                                    <i id="file-icon" class="fas fa-file-excel fa-3x text-primary mb-3" style="transition: all 0.3s;"></i>
                                    <h5 id="file-title" class="fw-bolder text-dark" style="transition: all 0.3s;">Haz clic o arrastra tu archivo Excel/CSV aquí</h5>
                                    <p id="file-subtitle" class="text-muted mb-1 fs-6">Formatos soportados: Excel (.xlsx, .xls) y CSV</p>
                                    
                                    <div class="mt-3 d-flex flex-column align-items-center position-relative" style="z-index: 2;">
                                        <span class="badge bg-light-info text-info fs-8 mb-3">
                                            Estructura: Fecha | Cédula | Valor | Oficina
                                        </span>
                                        
                                        <a href="{{ route('contabilidad.extractos.plantilla') }}" class="btn btn-sm btn-outline-primary rounded-pill px-4">
                                            <i class="fas fa-download me-2"></i> Descargar Plantilla
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3">
                                <a href="{{ route('contabilidad.extractos.index') }}" class="btn btn-light rounded-pill fs-5 px-5 py-3 fw-bold">Cancelar</a>
                                <button type="submit" class="btn btn-primary rounded-pill fs-5 px-5 py-3 fw-bolder shadow-sm">
                                    <i class="fas fa-eye me-2"></i> Previsualizar Archivo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT PARA LA MAGIA VISUAL --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('archivo_extracto');
            const dropZone = document.getElementById('drop-zone');
            const fileIcon = document.getElementById('file-icon');
            const fileTitle = document.getElementById('file-title');
            const fileSubtitle = document.getElementById('file-subtitle');

            // 1. Detectar cuando se selecciona un archivo
            fileInput.addEventListener('change', function (e) {
                if (this.files && this.files.length > 0) {
                    const fileName = this.files[0].name;
                    
                    // Cambiar textos
                    fileTitle.textContent = fileName;
                    fileSubtitle.textContent = '¡Archivo cargado exitosamente!';
                    
                    // Cambiar colores e ícono a éxito (Verde)
                    fileTitle.classList.replace('text-dark', 'text-success');
                    fileIcon.className = 'fas fa-check-circle fa-3x text-success mb-3';
                    dropZone.classList.replace('border-primary', 'border-success');
                    dropZone.classList.replace('bg-light-primary', 'bg-light-success');
                } else {
                    // Revertir a estado original si cancela
                    fileTitle.textContent = 'Haz clic o arrastra tu archivo Excel/CSV aquí';
                    fileSubtitle.textContent = 'Formatos soportados: Excel (.xlsx, .xls) y CSV';
                    
                    fileTitle.classList.replace('text-success', 'text-dark');
                    fileIcon.className = 'fas fa-file-excel fa-3x text-primary mb-3';
                    dropZone.classList.replace('border-success', 'border-primary');
                    dropZone.classList.replace('bg-light-success', 'bg-light-primary');
                }
            });

            // 2. Efecto visual al arrastrar un archivo por encima
            fileInput.addEventListener('dragenter', function() {
                dropZone.style.transform = 'scale(1.02)';
                dropZone.style.borderColor = '#0056b3'; // Un azul más oscuro
            });

            fileInput.addEventListener('dragleave', function() {
                dropZone.style.transform = 'scale(1)';
            });

            fileInput.addEventListener('drop', function() {
                dropZone.style.transform = 'scale(1)';
            });
        });
    </script>
</x-base-layout>