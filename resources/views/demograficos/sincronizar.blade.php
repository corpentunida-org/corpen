<x-base-layout>
    <div class="task-index-wrapper">
        
        <header class="index-header">
            <div class="header-titles">
                <span class="system-tag"><i class="fas fa-globe-americas me-1"></i> Módulo de Demografía</span>
                <h1 class="main-title">Sincronización Masiva Geográfica</h1>
                <p class="main-subtitle">Exportación e importación transaccional de Países, Regiones, Ciudades y Direcciones mediante Excel.</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('demograficos.dashboard') }}" class="btn-ghost-corporate me-2">
                    <i class="fas fa-chart-line"></i> <span>Ver Tablero</span>
                </a>
                <a href="{{ route('demograficos.maestro.index') }}" class="btn-corporate-black">
                    <i class="fas fa-list"></i> <span>Ver Maestro Manual</span>
                </a>
            </div>
        </header>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="dash-widget border-bottom-info">
                    <div class="widget-icon bg-info-light text-info"><i class="fas fa-file-download"></i></div>
                    <div class="widget-details w-100">
                        <span class="widget-title">Paso 1: Respaldar / Obtener Plantilla</span>
                        <div class="d-flex justify-content-between align-items-end mt-1">
                            <span class="text-muted small">Descarga la estructura actual de la base de datos en formato .xlsx.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="dash-widget border-bottom-success">
                    <div class="widget-icon bg-success-light text-success"><i class="fas fa-file-upload"></i></div>
                    <div class="widget-details w-100">
                        <span class="widget-title">Paso 2: Actualizar Base de Datos</span>
                        <div class="d-flex justify-content-between align-items-end mt-1">
                            <span class="text-muted small">Carga el Excel modificado para crear o sobreescribir registros masivamente.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success shadow-sm border-0 d-flex align-items-center mb-4">
                <i class="fas fa-check-circle fs-4 me-3"></i> 
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning shadow-sm border-0 d-flex align-items-center mb-4">
                <i class="fas fa-exclamation-triangle fs-4 me-3"></i> 
                <div>{{ session('warning') }}</div>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger shadow-sm border-0 mb-4">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-times-circle fs-4 me-2"></i> <strong>Se encontraron errores:</strong>
                </div>
                <ul class="mb-0 ps-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">
            
            <div class="col-md-5">
                <div class="show-card-corp shadow-sm h-100">
                    <div class="show-body p-4 text-center d-flex flex-column justify-content-center h-100">
                        <div class="mb-4">
                            <i class="fas fa-file-excel text-success" style="font-size: 4rem; opacity: 0.8;"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-3">Descargar Base Actual</h4>
                        <p class="text-muted mb-4" style="font-size: 0.9rem;">
                            Obtén un archivo con 5 pestañas: Países, Regiones, Subregiones, Ciudades y Direcciones. Modifica este documento respetando los nombres de las columnas para evitar errores de sincronización.
                        </p>
                        <div class="mt-auto">
                            <a href="{{ route('demograficos.sincronizar.descargar') }}" class="btn-corporate-black w-100 d-inline-flex justify-content-center align-items-center py-2" style="font-size: 1rem;">
                                <i class="fas fa-download me-2"></i> Generar y Descargar (.xlsx)
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="show-card-corp shadow-sm h-100 border-start border-4 border-primary">
                    <div class="show-body p-4 h-100 d-flex flex-column">
                        
                        {{-- ESTADO B: ARCHIVO EN SESIÓN (PENDIENTE DE CONFIRMACIÓN) --}}
                        @if(session()->has('excel_demografia_path'))
                            <div class="text-center my-auto">
                                <div class="mb-3">
                                    <span class="badge bg-success-light text-success p-3 rounded-circle">
                                        <i class="fas fa-check-double fa-2x"></i>
                                    </span>
                                </div>
                                <h4 class="fw-bold text-dark">Plantilla en cola de procesamiento</h4>
                                <p class="text-muted mb-4">El archivo fue almacenado de manera segura. Al confirmar, el sistema recorrerá todas las hojas y actualizará la base de datos geográfica.</p>
                                
                                <form action="{{ route('demograficos.sincronizar.confirmar') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-warning w-100 py-3 fw-bold text-dark shadow-sm mb-3" style="font-size: 1.1rem; letter-spacing: 0.5px;">
                                        <i class="fas fa-bolt me-2"></i> Ejecutar Sincronización Masiva
                                    </button>
                                    <a href="{{ route('demograficos.sincronizar.index') }}" class="text-danger small fw-bold text-decoration-none">
                                        <i class="fas fa-times me-1"></i> Descartar archivo y volver a subir
                                    </a>
                                </form>
                            </div>

                        {{-- ESTADO A: FORMULARIO DE SUBIDA --}}
                        @else
                            <div class="mb-4">
                                <h4 class="fw-bold text-dark"><i class="fas fa-upload text-primary me-2"></i> Cargar Plantilla Modificada</h4>
                                <p class="text-muted small">Arrastra el archivo o haz clic para buscar. Asegúrate de mantener la estructura original de cabeceras.</p>
                            </div>

                            <form action="{{ route('demograficos.sincronizar.subir') }}" method="POST" enctype="multipart/form-data" class="mt-auto">
                                @csrf
                                <div class="card bg-light border-dashed p-4 text-center mb-4" style="border: 2px dashed #cbd5e1; border-radius: 8px;">
                                    <i class="fas fa-cloud-upload-alt text-muted mb-2" style="font-size: 2rem;"></i>
                                    <label for="archivo_excel" class="fw-bold text-primary" style="cursor: pointer;">Explorar archivos</label>
                                    <input type="file" name="archivo_excel" id="archivo_excel" class="form-control d-none" accept=".xlsx, .xls, .csv" required onchange="document.getElementById('file-name').textContent = this.files[0].name;">
                                    <div id="file-name" class="text-muted small mt-2">Ningún archivo seleccionado (.xlsx max 10MB)</div>
                                </div>
                                
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn-corporate-black px-5 py-2">
                                        Validar Archivo <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </form>
                        @endif

                    </div>
                </div>
            </div>

        </div>
    </div>
    
    @includeIf('asociados.partials.styles') 
    
    <style>
        /* Pequeños ajustes adicionales si no están en tus estilos base */
        .border-dashed { border-style: dashed !important; }
        .bg-success-light { background-color: #d1fae5 !important; }
        .bg-info-light { background-color: #e0f2fe !important; }
    </style>

</x-base-layout>