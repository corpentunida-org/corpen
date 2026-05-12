<x-base-layout>
    {{-- 1. MODAL DE CARGA (Overlay) --}}
    <div id="loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.95); z-index: 9999; flex-direction: column; justify-content: center; align-items: center; backdrop-filter: blur(5px);">
        <div class="spinner-border text-primary mb-4" style="width: 3rem; height: 3rem;" role="status"></div>
        <h2 class="fw-bold text-dark">Sincronizando...</h2>
        <p class="text-muted">Procesando datos con la base de datos de AWS. No cierres esta ventana.</p>
    </div>

    <div class="app-container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                
                {{-- Encabezado --}}
                <div class="mb-5 text-center">
                    <h3 class="fw-bold text-dark">Sincronización Excel</h3>
                    <p class="text-muted fs-7">Gestión bidireccional de la base de datos</p>
                </div>

                {{-- 2. ALERTAS DE FEEDBACK (Éxito y Error) --}}
                @if(session('success'))
                    <div class="alert alert-success border-0 bg-light-success text-success p-4 rounded-4 mb-4 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-3 fs-3"></i>
                            <div>
                                <span class="fw-bolder">¡Logrado!</span><br>
                                <span class="fs-7">{{ session('success') }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger border-0 bg-light-danger text-danger p-4 rounded-4 mb-4 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-3 fs-3"></i>
                            <div>
                                <span class="fw-bolder">Hubo un problema</span><br>
                                <span class="fs-7">{{ $errors->first() }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Tarjeta Principal --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-5">
                        <div class="row g-0">
                            
                            {{-- Sección Exportar --}}
                            <div class="col-12 mb-5">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h5 class="fw-bold m-0 text-dark">Exportar a Excel</h5>
                                        <p class="text-muted fs-8 m-0">Descarga la tabla <code>con_extractos</code></p>
                                    </div>
                                    <a href="{{ route('contabilidad.sincronizar.descargar') }}" class="btn btn-light-primary btn-sm px-4 fw-bold rounded-pill">
                                        <i class="fas fa-download me-2"></i>Descargar
                                    </a>
                                </div>
                            </div>

                            <hr class="text-gray-200">

                            {{-- Sección Importar --}}
                            <div class="col-12 mt-5">
                                <h5 class="fw-bold mb-3 text-dark">Importar desde Excel</h5>
                                <form id="sync-form" action="{{ route('contabilidad.sincronizar.subir') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="d-flex flex-column gap-3">
                                        <input class="form-control form-control-sm bg-light border-0 px-4 py-2 rounded-pill" type="file" name="archivo_excel" accept=".xlsx, .xls" required>
                                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold rounded-pill py-2 shadow-sm">
                                            <i class="fas fa-sync me-2"></i>Sincronizar ahora
                                        </button>
                                    </div>
                                    <p class="text-muted fs-9 mt-3 text-center">Formatos soportados: <strong>.xlsx</strong> y <strong>.xls</strong></p>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Botón Volver --}}
                <div class="text-center mt-5">
                    <a href="{{ route('contabilidad.extractos.index') }}" class="text-muted fs-8 text-decoration-none hover-primary">
                        <i class="fas fa-arrow-left me-2"></i>Regresar al panel de extractos
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- 3. JAVASCRIPT PARA EL MODAL --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const syncForm = document.getElementById('sync-form');
            const overlay = document.getElementById('loading-overlay');

            if (syncForm) {
                syncForm.addEventListener('submit', function() {
                    // Mostrar el modal de carga
                    overlay.style.display = 'flex';
                    
                    // Deshabilitar el botón para evitar doble click
                    const btn = syncForm.querySelector('button[type="submit"]');
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
                });
            }
        });
    </script>
</x-base-layout>