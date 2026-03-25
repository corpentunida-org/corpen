<div class="d-flex justify-content-between align-items-center mb-6 px-2">
    <h4 class="fw-bolder m-0 text-gray-800 fs-5">Mis Equipos</h4>
    
    <button type="button" class="btn btn-icon btn-sm btn-light-primary shadow-sm rounded-circle" 
            data-bs-toggle="modal" data-bs-target="#modalCreateWorkspace"
            title="Crear nuevo equipo">
        <i class="bi bi-plus-lg fs-4"></i>
    </button>
</div>

<div class="position-relative mb-6 px-2">
    <span class="svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6">
        <i class="bi bi-search"></i>
    </span>
    <input type="text" class="form-control form-control-solid ps-10 fs-7" 
           id="search-workspaces" placeholder="Buscar equipo..." />
</div>

<div class="nav nav-pills flex-column custom-scroll" id="workspace-list">
    @forelse($workspaces as $workspace)
        @php 
            $isActive = (isset($activeWorkspace) && $activeWorkspace->id == $workspace->id); 
            // Obtenemos la inicial del nombre para el avatar
            $initial = strtoupper(substr($workspace->name, 0, 1));
        @endphp

        <li class="nav-item mb-2">
            <a href="{{ route('interactions.chat.index', ['workspace_id' => $workspace->id]) }}" 
               class="nav-link border border-transparent border-hover-dashed border-gray-300 p-3 {{ $isActive ? 'active bg-light-primary' : 'bg-transparent' }}"
               style="transition: all 0.2s ease;">
               
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-35px symbol-lg-40px me-3 flex-shrink-0">
                        <div class="symbol-label fw-bolder {{ $isActive ? 'bg-primary text-white' : 'bg-light-neutral text-primary border' }}">
                            {{ $initial }}
                        </div>
                    </div>

                    <div class="flex-grow-1 min-w-0">
                        <span class="d-block fw-bolder fs-7 {{ $isActive ? 'text-primary' : 'text-gray-800' }} text-truncate">
                            {{ $workspace->name }}
                        </span>
                        <div class="d-flex align-items-center">
                            <span class="text-muted fs-9 fw-bold text-uppercase">
                                {{ $workspace->area->nombre ?? 'General' }}
                            </span>
                            <span class="badge badge-circle badge-light-secondary ms-2 w-15px h-15px fs-10" title="Salas activas">
                                {{ $workspace->conversations->count() }}
                            </span>
                        </div>
                    </div>

                    @if($isActive)
                        <div class="d-none d-lg-block ms-2">
                            <i class="bi bi-chevron-right text-primary fs-8"></i>
                        </div>
                    @endif
                </div>
            </a>
        </li>
    @empty
        <div class="text-center py-10 opacity-50">
            <i class="bi bi-folder-x fs-2x mb-3 d-block"></i>
            <span class="fs-8 fw-bold">No hay equipos creados</span>
        </div>
    @endforelse
</div>

<style>
    /* Estilo para los items del menú */
    .nav-pills .nav-link {
        border-radius: 12px !important;
    }

    .nav-pills .nav-link:hover {
        background-color: rgba(0,0,0,0.02);
    }

    .nav-pills .nav-link.active {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05);
    }

    /* Ocultar el scrollbar pero mantener funcionalidad */
    #workspace-list {
        max-height: 50vh;
        overflow-y: auto;
    }
</style>

@push('scripts')
<script>
    // Filtro rápido de búsqueda en JS
    document.getElementById('search-workspaces')?.addEventListener('keyup', function() {
        let filter = this.value.toUpperCase();
        let items = document.querySelectorAll('#workspace-list .nav-item');
        
        items.forEach(item => {
            let text = item.textContent || item.innerText;
            item.style.display = text.toUpperCase().indexOf(filter) > -1 ? "" : "none";
        });
    });
</script>
@endpush