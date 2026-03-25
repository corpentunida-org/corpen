<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bolder m-0 text-truncate" title="{{ $activeWorkspace->name }}">
        {{ $activeWorkspace->name }}
    </h4>
    <button type="button" class="btn btn-sm btn-light-success" data-bs-toggle="modal" data-bs-target="#modalContextChat">
        <i class="fa fa-plus"></i> Sala
    </button>
</div>

<div class="list-group">
    @foreach($activeWorkspace->conversations as $chat)
        <a href="{{ route('interactions.chat.index', ['workspace_id' => $activeWorkspace->id, 'chat_id' => $chat->id]) }}"
           class="list-group-item list-group-item-action {{ (isset($activeConversation) && $activeConversation->id == $chat->id) ? 'active' : '' }}">
            <div class="d-flex w-100 justify-content-between">
                <h6 class="mb-1 text-truncate">
                    @if($chat->type == 'contextual')
                        <i class="fa fa-paperclip me-1"></i> ID: #{{ $chat->chatable_id }}
                    @else
                        <i class="fa fa-users me-1"></i> {{ $chat->name ?? 'Grupo' }}
                    @endif
                </h6>
            </div>
            <small class="{{ (isset($activeConversation) && $activeConversation->id == $chat->id) ? 'text-white' : 'text-muted' }}">
                {{ ucfirst($chat->visibility) }}
            </small>
        </a>
    @endforeach
</div>