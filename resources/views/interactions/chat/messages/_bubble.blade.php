@php
    $isMe = $msg->user_id === auth()->id();
    // URL temporal de S3
    $s3Url = $msg->attachment ? $msg->getFile($msg->attachment) : null;
    $hasParent = $msg->parent_id && $msg->parent;
@endphp

<div class="d-flex {{ $isMe ? 'justify-content-end' : 'justify-content-start' }} mb-4 msg-container" id="message-{{ $msg->id }}">
    <div class="d-flex flex-row align-items-center bubble-group {{ $isMe ? 'flex-row-reverse' : '' }}" style="max-width: 90%;">
        
        <div class="d-flex flex-column align-items-{{ $isMe ? 'end' : 'start' }} position-relative">
            
            <div class="d-flex align-items-center mb-1 px-2">
                @if(!$isMe)
                    <span class="text-dark fw-bolder fs-7 me-2">{{ $msg->user->name ?? 'Usuario' }}</span>
                @endif
                <span class="text-muted" style="font-size: 0.65rem;">{{ $msg->created_at->format('H:i') }}</span>
            </div>

            <div class="p-3 shadow-sm bubble-content" 
                 style="border-radius: 18px; 
                        {{ $isMe ? 'border-top-right-radius: 4px;' : 'border-top-left-radius: 4px;' }}
                        background-color: {{ $isMe ? '#dcf8c6' : '#ffffff' }}; 
                        color: #303030;
                        border: 1px solid {{ $isMe ? '#cde7b9' : '#e9e9e9' }};
                        min-width: 60px;">
                
                @if($hasParent)
                    <div class="p-2 mb-2 rounded bg-black bg-opacity-5 border-start border-4 border-primary" 
                         style="cursor: pointer; opacity: 0.8;" 
                         onclick="scrollToMessage({{ $msg->parent_id }})">
                        <div class="fw-bolder fs-9 text-primary">{{ $msg->parent->user->name ?? 'Usuario' }}</div>
                        <div class="text-muted fs-9 text-truncate" style="max-width: 200px;">
                            @if($msg->parent->attachment)
                                <i class="bi bi-camera-fill fs-9 me-1 text-muted"></i>
                            @endif
                            {{ $msg->parent->body }}
                        </div>
                    </div>
                @endif
                
                @if($msg->attachment)
                    @php $extension = strtolower(pathinfo($msg->attachment, PATHINFO_EXTENSION)); @endphp
                    
                    <div class="mb-2 overflow-hidden rounded shadow-sm bg-white bg-opacity-50">
                        @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                            <img src="{{ $s3Url }}" 
                                 class="img-fluid rounded" 
                                 style="max-height: 250px; width: 100%; object-fit: cover; cursor: pointer;" 
                                 onclick="window.open(this.src)"
                                 loading="lazy">
                        @else
                            <a href="{{ $s3Url }}" target="_blank" class="d-flex align-items-center text-decoration-none p-3 hover-elevate-up">
                                <div class="symbol symbol-40px me-3">
                                    <div class="symbol-label bg-light-primary text-primary">
                                        <i class="bi bi-file-earmark-arrow-down fs-2"></i>
                                    </div>
                                </div>
                                <div class="overflow-hidden">
                                    <div class="text-dark fw-bold fs-7 text-truncate" style="max-width: 150px;">Documento</div>
                                    <span class="text-muted fs-9 text-uppercase">{{ $extension }} • Seguro</span>
                                </div>
                            </a>
                        @endif
                    </div>
                @endif

                @if($msg->body)
                    <div class="fs-6 lh-sm text-break">
                        {!! nl2br(e($msg->body)) !!}
                    </div>
                @endif

                <div class="d-flex justify-content-end align-items-center mt-1" style="margin-bottom: -5px;">
                    @if($isMe)
                        <i class="bi bi-check2-all text-primary ms-1" style="font-size: 0.9rem;"></i>
                    @endif
                </div>
            </div>
        </div>

        <button type="button" 
                class="btn btn-icon btn-sm btn-clean btn-active-light-primary mx-2 rounded-circle reply-trigger"
                onclick="setReply({{ $msg->id }}, '{{ $msg->user->name }}', '{{ Str::limit(e($msg->body), 40) }}')"
                title="Responder">
            <i class="bi bi-arrow-90deg-left fs-4"></i>
        </button>
    </div>
</div>

<style>
    /* Desktop: Solo mostrar el botón al pasar el mouse */
    @media (min-width: 992px) {
        .reply-trigger {
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        .bubble-group:hover .reply-trigger {
            opacity: 1;
        }
    }

    /* Mobile: Hacer el botón sutil pero siempre accesible o visible al toque */
    @media (max-width: 991px) {
        .reply-trigger {
            opacity: 0.4; /* Sutil en móvil para no ensuciar, pero está ahí */
        }
        .bubble-group:active .reply-trigger {
            opacity: 1;
        }
        .msg-container {
            margin-bottom: 1rem !important;
        }
    }

    .bubble-content {
        transition: transform 0.1s ease;
    }

    .bubble-content:active {
        transform: scale(0.98); /* Feedback visual al tocar */
    }

    /* Resaltado cuando se navega a una respuesta */
    .bg-light-warning-soft {
        background-color: #fffde7 !important;
        box-shadow: 0 0 10px rgba(255, 193, 7, 0.2) !important;
        transition: background-color 0.8s ease;
    }
</style>