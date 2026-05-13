<x-base-layout>
    <div class="bg-white min-vh-100 d-flex flex-column font-inter">
        
        {{-- Header de Seguridad (Minimalista) --}}
        <div class="border-bottom py-3 bg-light">
            <div class="container-xxl d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="spinner-grow spinner-grow-sm text-danger" role="status"></span>
                    <span class="fw-bolder text-dark tracking-widest fs-8 text-uppercase">Sistema en Mantenimiento</span>
                </div>
                <div class="d-flex gap-3 align-items-center">
                    <span class="text-muted fs-9 font-monospace"><i class="bi bi-hdd-network me-1"></i> AWS-RDS-MASTER</span>
                    <div class="vr bg-gray-300"></div>
                    <span class="text-muted fs-9 font-monospace"><i class="bi bi-shield-check me-1"></i> AES-256</span>
                </div>
            </div>
        </div>

        {{-- Contenido Principal (Layout a 2 columnas para pantallas anchas) --}}
        <div class="container-xxl flex-grow-1 d-flex align-items-center py-10 py-lg-15">
            <div class="row w-100 align-items-center">
                
                {{-- Columna Izquierda: Mensaje y Acciones --}}
                <div class="col-lg-5 pe-lg-10 mb-10 mb-lg-0 text-center text-lg-start">
                    <div class="mb-6">
                        <div class="d-inline-flex align-items-center justify-content-center bg-light-primary text-primary rounded-circle" style="width: 80px; height: 80px;">
                            <i class="bi bi-shield-lock-fill fs-1"></i>
                        </div>
                    </div>
                    
                    <h1 class="display-5 fw-bolder text-dark mb-4" style="letter-spacing: -1px;">
                        Mantenimiento<br>Estructural
                    </h1>
                    
                    <p class="fs-5 text-muted mb-8 fw-normal" style="line-height: 1.6;">
                        El módulo de contabilidad se encuentra temporalmente protegido. Estamos optimizando la base de datos y preparando la sincronización masiva de registros.
                    </p>
                    
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start">
                        <a href="{{ route('contabilidad.sincronizar.index') }}" class="btn btn-primary btn-lg px-8 fw-bold shadow-sm">
                            <i class="bi bi-terminal me-2"></i> Consola Maestra
                        </a>
                        <a href="/" class="btn btn-light btn-lg px-8 border border-gray-300 fw-bold text-dark hover-bg-gray-100">
                            Volver al Inicio
                        </a>
                    </div>
                </div>

                {{-- Columna Derecha: Tarjeta Técnica de Estado --}}
                <div class="col-lg-7">
                    <div class="card border border-gray-200 shadow-sm rounded-4 overflow-hidden bg-white">
                        <div class="card-body p-6 p-lg-10">
                            <div class="row g-8">
                                
                                {{-- Estado del Núcleo --}}
                                <div class="col-md-6 border-end-md border-gray-200 pe-md-8">
                                    <h6 class="fw-bolder text-uppercase text-muted mb-6 tracking-wider fs-9">
                                        <i class="bi bi-cpu me-2 text-dark"></i>Métricas del Núcleo
                                    </h6>
                                    
                                    <div class="d-flex flex-column gap-5">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="fs-7 fw-bold text-dark">Motor SQL</span>
                                                <span class="badge bg-light-primary text-primary fs-9">Optimización</span>
                                            </div>
                                            <div class="text-muted fs-9">Reindexando tablas contables</div>
                                        </div>
                                        
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="fs-7 fw-bold text-dark">Hash Sync</span>
                                                <span class="badge bg-light-warning text-warning fs-9">En Espera</span>
                                            </div>
                                            <div class="text-muted fs-9">Validación de duplicados pausada</div>
                                        </div>
                                        
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="fs-7 fw-bold text-dark">Memoria RAM</span>
                                                <span class="badge bg-light-success text-success fs-9">Estable</span>
                                            </div>
                                            <div class="text-muted fs-9">Asignación dinámica: 512MB</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Consola de Logs --}}
                                <div class="col-md-6 ps-md-4">
                                    <h6 class="fw-bolder text-uppercase text-muted mb-6 tracking-wider fs-9">
                                        <i class="bi bi-code-square me-2 text-dark"></i>Consola de Sistema
                                    </h6>
                                    <div class="bg-dark rounded-3 p-4 font-monospace shadow-sm" style="height: 180px; overflow-y: hidden;">
                                        <div class="text-success fs-9 mb-2">> Initializing master_sync.sh...</div>
                                        <div class="text-gray-400 fs-9 mb-2">> Checking AWS RDS connection... <span class="text-white">OK</span></div>
                                        <div class="text-gray-400 fs-9 mb-2">> Loading 100k records to buffer...</div>
                                        <div class="text-warning fs-9 mb-2">> Running Upsert procedure...</div>
                                        <div class="text-info fs-9 mt-4 animate-blink">> Waiting for admin action... _</div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Barra de Progreso --}}
                            <div class="mt-10 pt-6 border-top border-gray-200">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-dark fs-8 fw-bold text-uppercase tracking-wider">Progreso Global de Tareas</span>
                                    <span class="text-primary fs-6 fw-bolder">64%</span>
                                </div>
                                <div class="progress h-8px bg-light-primary rounded-pill overflow-hidden">
                                    <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated rounded-pill" style="width: 64%"></div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <style>
        /* Tipografía limpia */
        .font-inter { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        .tracking-widest { letter-spacing: 0.15em; }
        .tracking-wider { letter-spacing: 0.1em; }
        
        /* Ajustes de bordes para responsivo */
        @media (min-width: 768px) {
            .border-end-md { border-right: 1px solid #e9ecef !important; }
        }

        /* Animación del cursor en la terminal */
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }
        .animate-blink { animation: blink 1.2s infinite; }
        
        /* Transiciones suaves para botones */
        .hover-bg-gray-100:hover { background-color: #f8f9fa !important; }
    </style>
</x-base-layout>