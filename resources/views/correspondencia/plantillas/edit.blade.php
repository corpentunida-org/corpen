<x-base-layout>
    <div class="app-container py-4">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 900px; border-radius: 20px;">
            <div class="card-body p-5">
                <h3 class="fw-bold mb-4">Editar Plantilla</h3>
                
                <form action="{{ route('correspondencia.plantillas.update', $plantilla) }}" method="POST">
                    @csrf @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nombre de la Plantilla</label>
                        <input type="text" name="nombre_plantilla" class="form-control @error('nombre_plantilla') is-invalid @enderror" value="{{ old('nombre_plantilla', $plantilla->nombre_plantilla) }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Código HTML</label>
                        <textarea name="html_base" class="form-control font-monospace" rows="15" required style="font-size: 0.9rem; background-color: #f8f9fa;">{{ old('html_base', $plantilla->html_base) }}</textarea>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('correspondencia.plantillas.index') }}" class="btn btn-light px-4 border">Cancelar</a>
                        <button type="submit" class="btn btn-warning px-5 fw-bold shadow">Actualizar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>