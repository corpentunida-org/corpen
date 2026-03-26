@php
    $isMe = $msg->user_id === auth()->id();
    $s3Url = $msg->attachment ? $msg->getFile($msg->attachment) : null;
    $hasParent = $msg->parent_id && $msg->parent;
@endphp

<div class="d-flex {{ $isMe ? 'justify-content-end' : 'justify-content-start' }} mb-6 msg-container" id="message-{{ $msg->id }}">
    <div class="d-flex flex-row align-items-end bubble-group {{ $isMe ? 'flex-row-reverse' : '' }}" style="max-width: 85%;">
        
        <div class="d-flex flex-column align-items-{{ $isMe ? 'end' : 'start' }} position-relative">
            
            <div class="d-flex align-items-center mb-1 px-1">
                @if(!$isMe)
                    <span class="fw-boldest text-gray-800 fs-8 me-2">{{ $msg->user->name ?? 'Usuario' }}</span>
                @endif
                <span class="text-muted fw-medium" style="font-size: 0.65rem;">{{ $msg->created_at->format('H:i') }}</span>
            </div>

            <div class="bubble-content {{ $isMe ? 'is-me' : 'is-other' }}">
                
                @if($hasParent)
                    <div class="reply-preview mb-3" onclick="scrollToMessage({{ $msg->parent_id }})">
                        <div class="reply-bar"></div>
                        <div class="reply-body">
                            <span class="reply-user">{{ $msg->parent->user->name ?? 'Usuario' }}</span>
                            <p class="reply-text">
                                @if($msg->parent->attachment) <i class="bi bi-image fs-9 me-1"></i> @endif
                                {{ Str::limit($msg->parent->body, 60) }}
                            </p>
                        </div>
                    </div>
                @endif
                
                @if($msg->attachment)
                    @php $extension = strtolower(pathinfo($msg->attachment, PATHINFO_EXTENSION)); @endphp
                    
                    <div class="attachment-wrapper mb-3">
                        @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                            <div class="img-container">
                                <img src="{{ $s3Url }}" class="img-fluid" onclick="window.open(this.src)" loading="lazy">
                            </div>
                        @else
                            <a href="{{ $s3Url }}" target="_blank" class="file-card">
                                <div class="file-icon">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <div class="file-info">
                                    <span class="file-name">Documento</span>
                                    <span class="file-ext">{{ strtoupper($extension) }}</span>
                                </div>
                                <i class="bi bi-download ms-auto text-muted fs-7"></i>
                            </a>
                        @endif
                    </div>
                @endif

                @if($msg->body)
                    <div class="message-text">
                        {!! nl2br(e($msg->body)) !!}
                    </div>
                @endif

                @if($isMe)
                    <div class="status-wrapper">
                        <i class="bi bi-check2-all"></i>
                    </div>
                @endif
            </div>
        </div>

        <button type="button" 
                class="btn-reply-action"
                onclick="setReply({{ $msg->id }}, '{{ $msg->user->name }}', '{{ Str::limit(e($msg->body), 40) }}')"
                title="Responder">
            <i class="bi bi-arrow-90deg-left"></i>
        </button>
    </div>
</div>

<style>
    /* VARIABLES PASTEL PARA CHAT */
    :root {
        --msg-me-bg: #E0F2FE; /* Azul Nube Pastel */
        --msg-me-text: #0369A1;
        --msg-other-bg: #ffffff;
        --msg-other-text: #334155;
        --msg-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
    }

    /* ESTILO BASE DE BURBUJA */
    .bubble-content {
        padding: 12px 16px;
        position: relative;
        box-shadow: var(--msg-shadow);
        transition: all 0.2s ease;
    }

    /* BURBUJA "YO" (Derecha) */
    .is-me {
        background-color: var(--msg-me-bg);
        color: var(--msg-me-text);
        border-radius: 20px 20px 4px 20px;
        border: 1px solid rgba(186, 230, 253, 0.5);
    }

    /* BURBUJA "OTRO" (Izquierda) */
    .is-other {
        background-color: var(--msg-other-bg);
        color: var(--msg-other-text);
        border-radius: 20px 20px 20px 4px;
        border: 1px solid #f1f5f9;
    }

    .message-text {
        font-size: 0.92rem;
        line-height: 1.5;
        font-weight: 450;
    }

    /* RESPUESTAS (REPLY PREVIEW) */
    .reply-preview {
        display: flex;
        background: rgba(0, 0, 0, 0.04);
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .reply-preview:hover { opacity: 0.8; }
    .reply-bar { width: 4px; background: currentColor; opacity: 0.3; }
    .reply-body { padding: 6px 10px; min-width: 0; }
    .reply-user { display: block; font-weight: 800; font-size: 0.7rem; text-transform: uppercase; }
    .reply-text { margin: 0; font-size: 0.75rem; color: inherit; opacity: 0.7; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    /* ADJUNTOS */
    .attachment-wrapper .img-container {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .attachment-wrapper img {
        display: block;
        max-height: 280px;
        width: 100%;
        object-fit: cover;
    }

    .file-card {
        display: flex;
        align-items: center;
        background: rgba(255,255,255,0.5);
        padding: 10px;
        border-radius: 12px;
        text-decoration: none !important;
        border: 1px solid rgba(0,0,0,0.03);
    }
    .file-icon {
        width: 35px;
        height: 35px;
        background: #fff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        color: #64748b;
    }
    .file-name { display: block; font-size: 0.75rem; font-weight: 700; color: #1e293b; }
    .file-ext { font-size: 0.65rem; color: #94a3b8; font-weight: 700; }

    /* STATUS CHECK */
    .status-wrapper {
        display: flex;
        justify-content: flex-end;
        margin-top: 4px;
        margin-bottom: -6px;
        opacity: 0.6;
        font-size: 0.85rem;
    }

    /* BOTÓN RESPONDER (MINIMAL) */
    .btn-reply-action {
        background: transparent;
        border: none;
        color: #94a3b8;
        padding: 8px;
        margin: 0 8px;
        border-radius: 50%;
        transition: all 0.2s ease;
        opacity: 0;
    }
    .msg-container:hover .btn-reply-action { opacity: 1; }
    .btn-reply-action:hover { background: #f1f5f9; color: #3E97FF; }

    /* MOBILE FIX */
    @media (max-width: 991px) {
        .btn-reply-action { opacity: 0.4; }
        .bubble-content { padding: 10px 14px; }
        .msg-container { margin-bottom: 1.25rem !important; }
    }

    /* NAVEGACIÓN A MENSAJE (Highlight) */
    .bg-light-warning-soft {
        animation: highlight-pulse 2s ease-out;
    }
    @keyframes highlight-pulse {
        0% { background-color: #fef9c3; transform: scale(1.02); }
        100% { background-color: transparent; transform: scale(1); }
    }
</style>