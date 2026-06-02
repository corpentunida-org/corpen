<x-base-layout>
    <div class="task-index-wrapper">
        <header class="index-header">
            <div class="header-titles">
                <span class="system-tag"><i class="fas fa-pen me-1"></i> Edición Manual</span>
                <h1 class="main-title">Editar Registro Geográfico</h1>
            </div>
            <div class="header-actions">
                <a href="{{ route('demograficos.maestro.index') }}" class="btn-ghost-corporate">
                    <i class="fas fa-arrow-left"></i> <span>Volver</span>
                </a>
            </div>
        </header>

        <div class="show-card-corp shadow-sm">
            <div class="show-body p-4">
                {{-- Ajustar ruta pasándole el ID real: route('demograficos.maestro.update', $pais->codigo_iso) --}}
                <form action="#" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small uppercase">Código ISO (Solo lectura)</label>
                            <input type="text" name="codigo_iso" class="form-control bg-light" value="CO" readonly>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold text-muted small uppercase">Nombre del País</label>
                            <input type="text" name="nombre" class="form-control" value="Colombia" required>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn-corporate-black px-4 py-2">
                            <i class="fas fa-sync-alt me-2"></i> Actualizar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @includeIf('asociados.partials.styles')
</x-base-layout>