<x-base-layout>
    <div class="app-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title h3 fw-bold m-0">Plantillas de Documentos</h1>
                <p class="text-muted">Formatos estandarizados para la generación de correspondencia.</p>
            </div>
            <a href="{{ route('correspondencia.plantillas.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4">
                <i class="fas fa-plus me-2"></i> Nueva Plantilla
            </a>
        </div>

        <div class="row g-4">
            @forelse($plantillas as $plantilla)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-shape bg-soft-primary text-primary rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: #eef2ff;">
                                <i class="fas fa-file-code fa-lg"></i>
                            </div>
                            <h5 class="fw-bold m-0 text-truncate">{{ $plantilla->nombre_plantilla }}</h5>
                        </div>
                        <p class="text-muted small">Estructura base HTML para documentos oficiales.</p>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <span class="text-muted small"><i class="far fa-calendar-alt me-1"></i> {{ $plantilla->created_at->format('d/m/Y') }}</span>
                            <div class="btn-group">
                                <a href="{{ route('correspondencia.plantillas.show', $plantilla) }}" class="btn btn-sm btn-light border" title="Vista Previa"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('correspondencia.plantillas.edit', $plantilla) }}" class="btn btn-sm btn-light border" title="Editar"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('correspondencia.plantillas.destroy', $plantilla) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta plantilla?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <img src="https://illustrations.popsy.co/gray/writing.svg" alt="No hay plantillas" style="width: 200px;" class="mb-3 opacity-50">
                <p class="text-muted">No se han creado plantillas de documentos todavía.</p>
            </div>
            @endforelse
        </div>
        
        <div class="mt-4">
            {{ $plantillas->links() }}
        </div>
    </div>
</x-base-layout>