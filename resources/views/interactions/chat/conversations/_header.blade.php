<div class="pb-3 border-bottom mb-3">
    <div class="d-flex align-items-center justify-content-between">
        
        <div class="d-flex align-items-center min-w-0">
            <div class="symbol symbol-35px symbol-lg-45px me-3 flex-shrink-0">
                <div class="symbol-label bg-light-primary text-primary">
                    @if($activeConversation->type == 'contextual')
                        <i class="bi bi-paperclip fs-3 fs-lg-2"></i>
                    @else
                        <i class="bi bi-chat-left-dots fs-3 fs-lg-2"></i>
                    @endif
                </div>
            </div>

            <div class="min-w-0">
                <h3 class="fw-bolder mb-0 fs-6 fs-lg-4 text-truncate">
                    @if($activeConversation->type == 'contextual')
                        {{ basename(str_replace('\\', '/', $activeConversation->chatable_type)) }} #{{ $activeConversation->chatable_id }}
                    @else
                        {{ $activeConversation->name ?? 'Chat Grupal' }}
                    @endif
                </h3>
                <div class="d-flex align-items-center flex-wrap">
                    <span class="badge {{ $activeConversation->visibility == 'internal' ? 'badge-light-danger' : 'badge-light-success' }} fw-bold fs-10 px-2 py-1 me-2 my-1">
                        <i class="bi {{ $activeConversation->visibility == 'internal' ? 'bi-lock-fill' : 'bi-globe' }} me-1 fs-10"></i>
                        <span class="d-none d-sm-inline">{{ strtoupper($activeConversation->visibility) }}</span>
                    </span>
                    <span class="text-muted fs-9 fw-bold my-1">{{ count($participants) }} <span class="d-none d-sm-inline">Miembros</span><span class="d-inline d-sm-none">M.</span></span>
                </div>
            </div>
        </div>

        <div class="d-flex gap-1 gap-lg-2 ms-2 flex-shrink-0">
            <button type="button" class="btn btn-sm btn-light-primary fw-bolder d-flex align-items-center px-3 px-lg-4" 
                    data-bs-toggle="modal" data-bs-target="#modalInviteUsers">
                <i class="bi bi-person-plus-fill fs-4 {{ isset($activeConversation) ? 'me-lg-1' : '' }}"></i>
                <span class="d-none d-md-inline">Invitar</span>
            </button>

            <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#panelInfoChat" 
                    aria-expanded="false"
                    title="Ver detalles de la sala">
                <i class="bi bi-info-circle-fill fs-3"></i>
            </button>
        </div>
    </div>

    <div class="collapse" id="panelInfoChat">
        <div class="card card-bordered bg-light-neutral mt-3 shadow-sm">
            <div class="card-body p-4 p-lg-5">
                @include('interactions.chat.conversations._participants_list')
            </div>
        </div>
    </div>
</div>