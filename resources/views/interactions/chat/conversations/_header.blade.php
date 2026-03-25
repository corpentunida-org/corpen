<div class="pb-3 border-bottom mb-3 d-flex justify-content-between align-items-center">
    <div>
        <h3 class="fw-bolder mb-1">
            @if($activeConversation->type == 'contextual')
                Chat Contextual: {{ basename(str_replace('\\', '/', $activeConversation->chatable_type)) }} #{{ $activeConversation->chatable_id }}
            @else
                {{ $activeConversation->name ?? 'Chat Grupal' }}
            @endif
        </h3>
        <span class="badge {{ $activeConversation->visibility == 'internal' ? 'bg-danger' : 'bg-success' }}">
            {{ $activeConversation->visibility == 'internal' ? 'Solo Interno' : 'Externo Permitido' }}
        </span>
    </div>
    
    <div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalInviteUsers">
            <i class="fa fa-user-plus"></i> Invitar
        </button>
        <button class="btn btn-sm btn-icon btn-light"><i class="fa fa-info-circle"></i></button>
    </div>
</div>