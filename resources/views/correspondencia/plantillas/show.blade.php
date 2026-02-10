<x-base-layout>
    <div class="app-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ route('correspondencia.plantillas.index') }}" class="text-decoration-none small fw-bold">
                    <i class="fas fa-arrow-left me-1"></i> Volver a Plantillas
                </a>
                <h2 class="fw-bold m-0 mt-2">{{ $plantilla->nombre_plantilla }}</h2>
            </div>
            <div class="gap-2 d-flex">
                <a href="{{ route('correspondencia.plantillas.edit', $plantilla) }}" class="btn btn-outline-warning">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
                <button onclick="document.getElementById('preview-frame').contentWindow.print()" class="btn btn-dark">
                    <i class="fas fa-print me-1"></i> Probar Impresión
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm p-2" style="border-radius: 15px; background: #525659;">
                    <div class="text-center py-2 text-white-50 small">
                        <i class="fas fa-desktop me-2"></i> Vista Previa del Renderizado
                    </div>
                    {{-- Usamos un iframe para aislar el CSS de la plantilla del CSS del sistema --}}
                    <div class="bg-white rounded-3 shadow-inner" style="min-height: 800px; overflow: hidden;">
                        <iframe id="preview-frame" srcdoc="{{ $plantilla->html_base }}" style="width: 100%; height: 800px; border: none;"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>