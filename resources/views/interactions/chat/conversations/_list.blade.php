<div class="d-flex justify-content-between align-items-center mb-8 px-2">
    <div class="min-w-0">
        <h4 class="fw-boldest m-0 text-gray-900 fs-5 tracking-tight text-truncate" title="{{ $activeWorkspace->name }}">
            {{ $activeWorkspace->name }}
        </h4>
        <div class="d-flex align-items-center mt-1">
            <span class="text-muted fs-9 fw-bold text-uppercase ls-1">Salas de Chat</span>
            <span class="mx-2 text-gray-300">|</span>
            <span class="text-muted fs-9 fw-bold">{{ $activeWorkspace->conversations->count() }} canales</span>
        </div>
    </div>
    
    <button type="button" class="btn btn-icon btn-sm btn-clean-add-success shadow-sm flex-shrink-0" 
            data-bs-toggle="modal" data-bs-target="#modalContextChat"
            title="Crear nueva sala">
        <i class="bi bi-plus-lg fs-5"></i>
    </button>
</div>

<div class="chat-items-nav custom-scroll-list" id="chat-list">
    @forelse($activeWorkspace->conversations->sortByDesc('updated_at') as $chat)
        @php
            $isActive = (isset($activeConversation) && $activeConversation->id == $chat->id);
            $isInternal = ($chat->visibility == 'internal');
            
            // --- LÓGICA DE NOMBRES E ICONOS DINÁMICOS ---
            if($chat->type == 'private') {
                // Modo Messenger: Buscamos al otro usuario
                $otroParticipante = $chat->participants->where('user_id', '!=', auth()->id())->first();
                $displayName = $otroParticipante->user->name ?? 'Chat Privado';
                $displayInitial = strtoupper(substr($displayName, 0, 1));
                
                $typeIcon = 'bi-person-fill'; 
                $typeColor = '#BE123C'; // Rosa/Rojo Pastel para DMs
                $typeBg = '#FFE4E6';
                $subtext = 'MENSAJE DIRECTO';
            } elseif($chat->type == 'contextual') {
                // Modo Proyecto/Proceso
                $displayName = basename(str_replace('\\', '/', $chat->chatable_type));
                $displayId = "#".$chat->chatable_id;
                
                $typeIcon = 'bi-hash';
                $typeColor = '#7E22CE'; // Morado Pastel
                $typeBg = '#F3E8FF';
                $subtext = strtoupper($chat->type);
            } else {
                // Modo General
                $displayName = $chat->name ?? 'Sala General';
                $displayId = "";
                
                $typeIcon = 'bi-chat-dots';
                $typeColor = '#3E97FF'; // Azul Pastel
                $typeBg = '#E0F2FE';
                $subtext = strtoupper($chat->type);
            }
        @endphp

        <a href="{{ route('interactions.chat.index', ['workspace_id' => $activeWorkspace->id, 'chat_id' => $chat->id]) }}"
           class="chat-item {{ $isActive ? 'is-active' : '' }} mb-2">
            
            <div class="d-flex align-items-center w-100">
                <div class="chat-icon-wrapper" style="background-color: {{ $isActive ? '#fff' : $typeBg }}; color: {{ $typeColor }};">
                    @if($chat->type == 'private')
                        <span class="fw-bold fs-6">{{ $displayInitial }}</span>
                    @else
                        <i class="bi {{ $typeIcon }} fs-4"></i>
                    @endif
                </div>

                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="chat-name {{ $isActive ? 'text-white' : 'text-gray-800' }}">
                            @if($chat->type == 'private')
                                {{ $displayName }}
                            @elseif($chat->type == 'contextual')
                                {{ $displayName }} <span class="{{ $isActive ? 'text-white opacity-50' : 'opacity-50' }}">{{ $displayId }}</span>
                            @else
                                {{ $displayName }}
                            @endif
                        </span>
                        
                        <span class="chat-status-dot {{ $isInternal ? 'is-internal' : 'is-public' }}"></span>
                    </div>

                    <div class="d-flex align-items-center mt-1">
                        <span class="chat-meta {{ $isActive ? 'text-white-50' : 'text-muted' }}">
                            {{ $subtext }}
                        </span>
                        <span class="mx-1 chat-meta {{ $isActive ? 'text-white-50' : 'text-gray-300' }}">·</span>
                        <span class="chat-meta {{ $isInternal ? 'text-danger-pastel' : 'text-success-pastel' }} {{ $isActive ? 'text-white opacity-75' : '' }}">
                            {{ $isInternal ? 'PRIVADO' : 'PÚBLICO' }}
                        </span>
                    </div>
                </div>
            </div>
        </a>
    @empty
        <div class="text-center py-20 opacity-40">
            <i class="bi bi-chat-square-dots fs-3x d-block mb-4"></i>
            <span class="fs-7 fw-bold">No hay conversaciones</span>
        </div>
    @endforelse
</div>

<style>
    /* Se mantienen exactamente tus mismos estilos */
    .btn-clean-add-success {
        background: #DCFCE7;
        color: #15803D;
        border-radius: 10px;
        width: 34px;
        height: 34px;
        transition: all 0.2s ease;
        border: none;
    }
    .btn-clean-add-success:hover {
        background: #15803D;
        color: #ffffff;
        transform: translateY(-2px);
    }

    .chat-items-nav {
        display: flex;
        flex-direction: column;
        padding-right: 4px;
    }

    .chat-item {
        display: flex;
        align-items: center;
        padding: 12px 14px;
        text-decoration: none;
        border-radius: 14px;
        border: 1px solid transparent;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    
    .chat-item:hover {
        background-color: #f1f5f9;
        transform: translateX(4px);
    }

    .chat-item.is-active {
        background-color: #3E97FF !important;
        border-color: #3E97FF;
        box-shadow: 0 10px 15px -3px rgba(62, 151, 255, 0.2);
    }

    .chat-icon-wrapper {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 14px;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .chat-name {
        display: block;
        font-size: 0.9rem;
        font-weight: 700;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .chat-meta {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.025em;
    }

    .chat-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        margin-left: 10px;
        flex-shrink: 0;
    }
    .is-internal { background-color: #ef4444; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1); }
    .is-public { background-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); }
    
    .chat-item.is-active .chat-status-dot {
        background-color: #fff !important;
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
    }

    .text-danger-pastel { color: #f87171; }
    .text-success-pastel { color: #34d399; }

    .custom-scroll-list::-webkit-scrollbar { width: 4px; }
    .custom-scroll-list::-webkit-scrollbar-track { background: transparent; }
    .custom-scroll-list::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>