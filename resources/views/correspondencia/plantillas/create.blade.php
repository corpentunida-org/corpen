<x-base-layout>
    <div class="app-container py-4">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 900px; border-radius: 20px;">
            <div class="card-body p-5">
                <h3 class="fw-bold mb-4">Diseñar Nueva Plantilla</h3>
                
                <form action="{{ route('correspondencia.plantillas.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nombre de la Plantilla</label>
                        <input type="text" name="nombre_plantilla" class="form-control form-control-lg @error('nombre_plantilla') is-invalid @enderror" placeholder="Ej: Oficio Externo Nivel Central" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Estructura HTML Base</label>
                        <div class="alert alert-info border-0 small mb-2">
                            <i class="fas fa-info-circle me-2"></i> Use marcadores como <code>@{{ nombre_usuario }}</code> o <code>@{{ fecha }}</code> para que el sistema los reemplace al generar documentos.
                        </div>
                        <textarea name="html_base" class="form-control font-monospace" rows="12" placeholder="<html>...</html>" required style="font-size: 0.9rem; background-color: #f8f9fa; border-radius: 12px;"></textarea>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('correspondencia.plantillas.index') }}" class="btn btn-light px-4 border">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow">Guardar Plantilla</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>