@php
    // --- LÓGICA DE TÍTULO DINÁMICO ---
    $tituloChat = $activeConversation->name ?? 'Chat Grupal';
    $subtitulo = strtoupper($activeConversation->type);
    $bgIcono = 'bg-light-primary';
    $textIcono = 'text-primary';
    $inicial = '';

    if($activeConversation->type == 'private') {
        // Buscamos al otro participante
        $otroUser = $activeConversation->participants->where('user_id', '!=', auth()->id())->first();
        $tituloChat = $otroUser->user->name ?? 'Conversación Privada';
        $subtitulo = 'MENSAJE DIRECTO';
        $bgIcono = 'bg-light-success';
        $textIcono = 'text-success';
        $inicial = strtoupper(substr($tituloChat, 0, 1));
    } elseif($activeConversation->type == 'contextual') {
        $tituloChat = basename(str_replace('\\', '/', $activeConversation->chatable_type)) . " #" . $activeConversation->chatable_id;
        $bgIcono = 'bg-light-info';
        $textIcono = 'text-info';
    }
@endphp

<div class="pb-3 border-bottom mb-3">
    <div class="d-flex align-items-center justify-content-between">
        
        <div class="d-flex align-items-center min-w-0">
            <div class="symbol symbol-35px symbol-lg-45px me-3 flex-shrink-0">
                <div class="symbol-label {{ $bgIcono }} {{ $textIcono }} fw-bold">
                    @if($activeConversation->type == 'private')
                        <span class="fs-3">{{ $inicial }}</span>
                    @elseif($activeConversation->type == 'contextual')
                        <i class="bi bi-paperclip fs-3 fs-lg-2 {{ $textIcono }}"></i>
                    @else
                        <i class="bi bi-chat-left-dots fs-3 fs-lg-2 {{ $textIcono }}"></i>
                    @endif
                </div>
                {{-- Punto verde de activo si es privado (opcional) --}}
                @if($activeConversation->type == 'private')
                    <div class="bg-success position-absolute border border-white h-8px w-8px rounded-circle translate-middle start-100 top-100 ms-n2 mt-n2"></div>
                @endif
            </div>

            <div class="min-w-0">
                <h3 class="fw-bolder mb-0 fs-6 fs-lg-4 text-truncate text-gray-900">
                    {{ $tituloChat }}
                </h3>
                
                <div class="d-flex align-items-center flex-wrap">
                    <span class="badge {{ $activeConversation->visibility == 'internal' ? 'badge-light-danger' : 'badge-light-success' }} fw-bold fs-10 px-2 py-1 me-2 my-1">
                        <i class="bi {{ $activeConversation->visibility == 'internal' ? 'bi-lock-fill' : 'bi-globe' }} me-1 fs-10"></i>
                        <span class="d-none d-sm-inline">{{ strtoupper($activeConversation->visibility) }}</span>
                    </span>
                    
                    <span class="text-muted fs-9 fw-bold my-1">
                        {{ count($participants) }} 
                        <span class="d-none d-sm-inline">Miembros</span>
                        <span class="d-inline d-sm-none">M.</span>
                    </span>

                    <span class="mx-2 text-gray-300">|</span>
                    <span class="text-gray-500 fs-10 fw-bold">{{ $subtitulo }}</span>
                </div>
            </div>
        </div>

        <div class="d-flex gap-1 gap-lg-2 ms-2 flex-shrink-0">
            {{-- El botón invitar solo suele usarse en grupos, pero lo dejamos como pediste --}}
            <button type="button" class="btn btn-sm btn-light-primary fw-bolder d-flex align-items-center px-3 px-lg-4 shadow-none" 
                    data-bs-toggle="modal" data-bs-target="#modalInviteUsers">
                <i class="bi bi-person-plus-fill fs-4 {{ isset($activeConversation) ? 'me-lg-1' : '' }}"></i>
                <span class="d-none d-md-inline">Invitar</span>
            </button>

            <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary shadow-none" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#panelInfoChat" 
                    aria-expanded="false"
                    title="Ver detalles de la sala">
                <i class="bi bi-info-circle-fill fs-3"></i>
            </button>
        </div>
    </div>

    <div class="collapse" id="panelInfoChat">
        <div class="card card-bordered bg-light-neutral mt-3 shadow-sm border-dashed">
            <div class="card-body p-4 p-lg-5">
                @include('interactions.chat.conversations._participants_list')
            </div>
        </div>
    </div>
</div>