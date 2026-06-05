<x-base-layout>
    <div class="task-index-wrapper">
        <header class="index-header">
            <div class="header-titles">
                <span class="system-tag"><i class="fas fa-file-excel me-1"></i> Sincronización de Datos</span>
                <h1 class="main-title">Gestión Masiva de Asociados</h1>
                <p class="main-subtitle">Exporta la base actual, edítala en Excel y cárgala para realizar actualizaciones o inserciones simultáneas (Upsert).</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('asociados.maestro.index') }}" class="btn-ghost-corporate me-2">
                    <i class="fas fa-arrow-left"></i> <span>Volver al Directorio</span>
                </a>
            </div>
        </header>

        {{-- Alertas del Sistema --}}
        @if(session('error'))
            <div class="alert alert-danger shadow-sm border-0 border-start border-danger border-4 mb-4">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-warning shadow-sm border-0 border-start border-warning border-4 mb-4">
                <h6 class="fw-bold mb-2"><i class="fas fa-times-circle me-2"></i>Errores detectados en el archivo:</h6>
                <ul class="mb-0 small" style="max-height: 120px; overflow-y: auto;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4 mb-4">
            {{-- Tarjeta Paso 1: Descargar --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <div class="card-body p-4 d-flex flex-column text-center">
                        <div class="mb-3">
                            <i class="fas fa-file-download text-primary" style="font-size: 3rem; opacity: 0.8;"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Paso 1: Exportar Plantilla</h4>
                        <p class="text-muted small mb-4 px-3">
                            Descarga la estructura oficial con todos los registros actuales.
                        </p>
                        <div class="mt-auto">
                            <a href="{{ route('asociados.sincronizar.descargar') }}" class="btn-corporate-black w-100 justify-content-center">
                                <i class="fas fa-download me-2"></i> <span>Descargar Base Actual (.xlsx)</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tarjeta Paso 2: Subir --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-3 h-100" style="background-color: #f8fafc;">
                    <div class="card-body p-4 flex-column text-center">
                        <div class="mb-3">
                            <i class="fas fa-cloud-upload-alt text-success" style="font-size: 3rem; opacity: 0.8;"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Paso 2: Subir y Previsualizar</h4>
                        <p class="text-muted small mb-4 px-3">
                            Carga el archivo modificado. El sistema procesará el lote y te mostrará una mesa de validación inteligente.
                        </p>
                        
                        <form action="{{ route('asociados.sincronizar.subir') }}" method="POST" enctype="multipart/form-data" class="mt-auto text-start">
                            @csrf
                            <div class="input-group mb-3 shadow-sm rounded-3 overflow-hidden">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-file-excel"></i></span>
                                <input type="file" name="excel_file" class="form-control border-start-0" accept=".xlsx,.xls,.csv" required style="font-size: 0.85rem;">
                            </div>
                            <button type="submit" class="btn w-100 text-white shadow" style="background-color: var(--bs-success); font-weight: 600;">
                                <i class="fas fa-cogs me-2"></i> Procesar y Validar Lote
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('asociados.partials.styles')
</x-base-layout>