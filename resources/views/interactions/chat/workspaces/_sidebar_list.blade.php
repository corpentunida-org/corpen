<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bolder m-0">Mis Equipos</h4>
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateWorkspace">
        <i class="fa fa-plus"></i> Nuevo
    </button>
</div>

<ul class="nav nav-pills flex-column mb-auto">
    @foreach($workspaces as $workspace)
        <li class="nav-item mb-1">
            <a href="{{ route('interactions.chat.index', ['workspace_id' => $workspace->id]) }}" 
               class="nav-link {{ (isset($activeWorkspace) && $activeWorkspace->id == $workspace->id) ? 'active' : 'text-dark bg-light' }}">
                <i class="fa fa-folder-open me-2"></i> {{ $workspace->name }}
            </a>
        </li>
    @endforeach
</ul>