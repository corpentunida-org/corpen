<div class="d-flex justify-content-between align-items-center mb-6">
    <div class="d-flex align-items-center me-2 overflow-hidden">
        <h4 class="fw-bolder m-0 text-gray-800 text-truncate" title="{{ $activeWorkspace->name }}">
            {{ $activeWorkspace->name }}
        </h4>
    </div>
    
    <button type="button" class="btn btn-sm btn-icon btn-light-success shadow-sm flex-shrink-0" 
            data-bs-toggle="modal" data-bs-target="#modalContextChat"
            title="Crear nueva sala">
        <i class="bi bi-plus-lg fs-4"></i>
    </button>
</div>

<div class="list-group list-group-flush border-top">
    @forelse($activeWorkspace->conversations->sortByDesc('updated_at') as $chat)
        @php
            $isActive = (isset($activeConversation) && $activeConversation->id == $chat->id);
        @endphp

        <a href="{{ route('interactions.chat.index', ['workspace_id' => $activeWorkspace->id, 'chat_id' => $chat->id]) }}"
           class="list-group-item list-group-item-action py-4 px-3 border-bottom {{ $isActive ? 'bg-light-primary border-start border-4 border-primary' : '' }}"
           style="transition: all 0.2s ease;">
            
            <div class="d-flex align-items-center">
                <div class="symbol symbol-40px me-3">
                    <div class="symbol-label {{ $isActive ? 'bg-primary text-white' : 'bg-light text-muted' }}">
                        @if($chat->type == 'contextual')
                            <i class="bi bi-paperclip fs-3 {{ $isActive ? 'text-white' : '' }}"></i>
                        @else
                            <i class="bi bi-chat-right-dots-fill fs-3 {{ $isActive ? 'text-white' : '' }}"></i>
                        @endif
                    </div>
                </div>

                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bolder fs-6 {{ $isActive ? 'text-primary' : 'text-gray-800' }} text-truncate">
                            @if($chat->type == 'contextual')
                                {{ basename(str_replace('\\', '/', $chat->chatable_type)) }} #{{ $chat->chatable_id }}
                            @else
                                {{ $chat->name ?? 'Sala General' }}
                            @endif
                        </span>
                        
                        <span class="bullet bullet-dot {{ $chat->visibility == 'internal' ? 'bg-danger' : 'bg-success' }} h-6px w-6px ms-2" 
                              title="{{ ucfirst($chat->visibility) }}"></span>
                    </div>

                    <div class="d-flex align-items-center">
                        <span class="fs-8 fw-bold {{ $isActive ? 'text-primary opacity-75' : 'text-muted' }} text-uppercase">
                            {{ $chat->type }} • 
                        </span>
                        <span class="fs-9 fw-bold ms-1 {{ $chat->visibility == 'internal' ? 'text-danger' : 'text-success' }}">
                            {{ strtoupper($chat->visibility) }}
                        </span>
                    </div>
                </div>
            </div>
        </a>
    @empty
        <div class="text-center py-10">
            <i class="bi bi-chat-dots-fill fs-2x text-gray-200 mb-3 d-block"></i>
            <span class="text-muted fs-7 fw-bold">No hay salas creadas aún.</span>
        </div>
    @endforelse
</div>

<style>
    /* Efectos de hover para que se sienta como una app real */
    .list-group-item-action:hover {
        background-color: #f8f9fa;
        transform: translateX(3px);
    }
    .list-group-item-action.active:hover {
        background-color: #f1faff;
        transform: none;
    }
</style>