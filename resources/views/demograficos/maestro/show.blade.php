<x-base-layout>
    <div class="task-index-wrapper">
        <header class="index-header">
            <div class="header-titles">
                <span class="system-tag"><i class="fas fa-eye me-1"></i> Detalles del Registro</span>
                <h1 class="main-title">Ficha Geográfica Completa</h1>
            </div>
            <div class="header-actions">
                <a href="{{ route('demograficos.maestro.edit', $pais->codigo_iso) }}" class="btn-ghost-corporate me-2">
                    <i class="fas fa-pen"></i> <span>Editar País</span>
                </a>
                <a href="{{ route('demograficos.maestro.index') }}" class="btn-corporate-black">
                    <i class="fas fa-arrow-left"></i> <span>Volver al Directorio</span>
                </a>
            </div>
        </header>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="show-card-corp shadow-sm h-100">
                    <div class="show-body p-4 text-center d-flex flex-column justify-content-center">
                        <div class="avatar-circle mx-auto mb-3 bg-primary text-white" style="width: 100px; height: 100px; font-size: 2.5rem;">
                            {{ substr($pais->codigo_iso, 0, 2) }}
                        </div>
                        <h2 class="fw-bold text-dark mb-1">{{ $pais->nombre }}</h2>
                        <span class="badge bg-light text-dark border mx-auto mb-4" style="font-size: 0.9rem;">
                            Código ISO: {{ $pais->codigo_iso }}
                        </span>

                        <div class="row border-top pt-3 mt-auto">
                            <div class="col-6 border-end">
                                <h4 class="fw-bold text-primary mb-0">{{ $pais->regiones->count() }}</h4>
                                <small class="text-muted text-uppercase" style="font-size: 0.7rem;">Regiones</small>
                            </div>
                            <div class="col-6">
                                <h4 class="fw-bold text-primary mb-0">Activo</h4>
                                <small class="text-muted text-uppercase" style="font-size: 0.7rem;">Estado</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="show-card-corp shadow-sm h-100 d-flex flex-column">
                    
                    <div class="p-4 bg-light border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="fas fa-map-signs me-2 text-primary"></i> Directorio de Regiones
                                <span class="badge bg-primary ms-2 rounded-pill">{{ $pais->regiones->count() }}</span>
                            </h5>
                            <button class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i> Nueva Región
                            </button>
                        </div>
                        
                        <div class="input-group input-group-sm shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" id="buscadorRegiones" class="form-control border-start-0" placeholder="Buscar región por nombre o código...">
                        </div>
                    </div>

                    <div class="p-0 flex-grow-1" style="max-height: 500px; overflow-y: auto;">
                        <ul class="list-group list-group-flush" id="listaRegiones">
                            @forelse($pais->regiones as $region)
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3 region-item hover-bg-light" style="transition: background-color 0.2s;">
                                    
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 text-muted">
                                            <i class="fas fa-map-marker-alt" style="font-size: 1.5rem; opacity: 0.5;"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block region-nombre" style="font-size: 1.05rem;">{{ $region->nombre }}</span>
                                            <small class="text-muted">
                                                Cód: <span class="text-dark fw-bold">{{ $region->codigo_iso ?? 'N/A' }}</span> | ID Sistema: {{ $region->id_region }}
                                            </small>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2">
                                        {{-- Aquí puedes apuntar a la ruta real de edición de región, por ejemplo: route('regiones.edit', $region->id_region) --}}
                                        <a href="#" class="btn btn-sm btn-light border text-warning shadow-sm" data-bs-toggle="tooltip" title="Editar Región">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center py-5 text-muted border-0">
                                    <div class="mb-3">
                                        <i class="fas fa-folder-open" style="font-size: 3rem; opacity: 0.3;"></i>
                                    </div>
                                    <h6 class="fw-bold">No hay regiones asociadas</h6>
                                    <p class="small mb-0">Puedes agregarlas importando un Excel o creando una manualmente.</p>
                                </li>
                            @endforelse
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
    
    @includeIf('asociados.partials.styles')

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const buscador = document.getElementById('buscadorRegiones');
            const lista = document.getElementById('listaRegiones');
            const items = lista.getElementsByClassName('region-item');

            if(buscador) {
                buscador.addEventListener('keyup', function() {
                    const filtro = this.value.toLowerCase();

                    Array.from(items).forEach(function(item) {
                        // Busca tanto en el nombre como en el código
                        const texto = item.textContent.toLowerCase();
                        
                        if (texto.includes(filtro)) {
                            // Usamos setProperty para sobreescribir estilos de bootstrap si es necesario
                            item.style.setProperty('display', 'flex', 'important');
                        } else {
                            item.style.setProperty('display', 'none', 'important');
                        }
                    });
                });
            }

            // Inicializar Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

    <style>
        .hover-bg-light:hover {
            background-color: #f8f9fa !important;
        }
    </style>
</x-base-layout>