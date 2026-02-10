<x-base-layout>
    <div class="app-container py-4 text-center">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 500px; border-radius: 20px;">
            <div class="card-body p-5">
                <div class="icon-box bg-soft-primary text-primary mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border-radius: 50%; background: #f0f4ff;">
                    <i class="fas fa-tag fa-2x"></i>
                </div>
                <h2 class="fw-bold">{{ $estado->nombre }}</h2>
                <p class="text-muted mb-4">{{ $estado->descripcion ?? 'Sin descripción disponible.' }}</p>
                
                <div class="bg-light rounded-3 p-3 mb-4 border">
                    <span class="small text-uppercase fw-bold text-muted d-block">Documentos vinculados actualmente</span>
                    <span class="h3 fw-bold">{{ $estado->correspondencias->count() }}</span>
                </div>

                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('correspondencia.estados.edit', $estado) }}" class="btn btn-warning text-white px-4 shadow-sm">Editar</a>
                    <a href="{{ route('correspondencia.estados.index') }}" class="btn btn-light border px-4">Volver</a>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>