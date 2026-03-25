<div class="p-3 p-lg-4 border-top mt-auto bg-body">
    
    <div id="reply-preview-container" class="mb-2 d-none">
        <div class="d-flex align-items-center justify-content-between p-3 bg-light-primary border-start border-4 border-primary rounded shadow-none">
            <div class="overflow-hidden me-3">
                <div class="d-flex align-items-center mb-1">
                    <i class="bi bi-reply-fill text-primary me-2"></i>
                    <span class="fw-bolder text-primary fs-9 text-uppercase">Respondiendo a <span id="reply-user-name"></span></span>
                </div>
                <div class="text-gray-600 fs-9 text-truncate" id="reply-text-preview" style="max-width: 100%;"></div>
            </div>
            <button type="button" class="btn btn-sm btn-icon btn-active-color-danger" onclick="cancelReply()" title="Cancelar respuesta">
                <i class="bi bi-x-lg fs-6"></i>
            </button>
        </div>
    </div>

    <div id="attachment-preview-container" class="mb-2 d-none">
        <div class="d-flex align-items-center justify-content-between p-2 px-4 bg-light-info border border-info border-dashed rounded-pill shadow-sm">
            <div class="d-flex align-items-center overflow-hidden">
                <i class="bi bi-paperclip fs-5 text-info me-2"></i>
                <span id="preview-filename" class="fw-bold text-info fs-9 text-truncate" style="max-width: 200px;"></span>
            </div>
            <button type="button" class="btn btn-sm btn-icon btn-active-light-danger ms-2" onclick="removeAttachment()">
                <i class="bi bi-x-circle-fill fs-5"></i>
            </button>
        </div>
    </div>

    <form id="chat-form" action="{{ route('interactions.messages.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="conversation_id" value="{{ $activeConversation->id }}">
        <input type="hidden" name="parent_id" id="parent-id-input" value="">
        <input type="file" name="attachment" id="file-attachment" class="d-none" onchange="handleFileSelect(this)">

        <div class="d-flex align-items-end gap-2">
            <button type="button" class="btn btn-icon btn-active-light-primary w-38px h-38px flex-shrink-0" 
                    onclick="document.getElementById('file-attachment').click()" 
                    title="Adjuntar archivo">
                <i class="bi bi-plus-circle-fill fs-3"></i>
            </button>

            <div class="flex-grow-1 position-relative">
                <textarea class="form-control form-control-flush bg-light border-0 px-4 py-2 custom-textarea shadow-none" 
                          name="body" 
                          rows="1" 
                          placeholder="Escribe un mensaje..." 
                          style="resize: none; border-radius: 20px; min-height: 38px; max-height: 150px; font-size: 0.9rem;"
                          oninput="autoResize(this)"></textarea>
            </div>

            <button class="btn btn-primary btn-icon rounded-circle w-38px h-38px flex-shrink-0 shadow-sm hover-elevate-up" 
                    type="submit" 
                    id="btn-send-chat">
                <i class="bi bi-send-fill fs-4"></i>
            </button>
        </div>
    </form>
</div>

<style>
    /* Estilo para que el textarea se sienta más como una App */
    .custom-textarea:focus {
        background-color: #ffffff !important;
        border: 1px solid #e1e3ea !important;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.03) !important;
    }
    
    @media (max-width: 768px) {
        .custom-textarea {
            font-size: 16px !important; /* Evita que iOS haga zoom automático al enfocar */
        }
    }

    /* Animación para la aparición de la respuesta */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

@push('scripts')
<script>
    const chatTextarea = document.querySelector('textarea[name="body"]');
    const attachmentInput = document.getElementById('file-attachment');
    const previewContainer = document.getElementById('attachment-preview-container');
    const filenameDisplay = document.getElementById('preview-filename');
    
    const replyContainer = document.getElementById('reply-preview-container');
    const replyInput = document.getElementById('parent-id-input');
    const replyUser = document.getElementById('reply-user-name');
    const replyText = document.getElementById('reply-text-preview');

    // Función para auto-ajustar altura del textarea
    function autoResize(el) {
        el.style.height = '38px'; // Base reducida
        el.style.height = (el.scrollHeight) + 'px';
    }

    /**
     * LÓGICA DE RESPUESTA
     */
    function setReply(id, user, text) {
        replyInput.value = id;
        replyUser.innerText = user;
        replyText.innerText = text;
        replyContainer.classList.remove('d-none');
        chatTextarea.placeholder = "Escribe tu respuesta...";
        chatTextarea.focus();
        
        replyContainer.style.animation = "fadeIn 0.3s ease";
    }

    function cancelReply() {
        replyInput.value = "";
        replyContainer.classList.add('d-none');
        chatTextarea.placeholder = "Escribe un mensaje...";
        autoResize(chatTextarea);
    }

    /**
     * LÓGICA DE ADJUNTOS
     */
    chatTextarea.addEventListener('paste', function(e) {
        const items = (e.clipboardData || e.originalEvent.clipboardData).items;
        for (let index in items) {
            const item = items[index];
            if (item.kind === 'file') {
                const blob = item.getAsFile();
                const file = new File([blob], "img_pasted_" + Date.now() + ".png", { type: blob.type });
                
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                attachmentInput.files = dataTransfer.files;
                
                showPreview(file.name);
            }
        }
    });

    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            showPreview(input.files[0].name);
        }
    }

    function showPreview(name) {
        filenameDisplay.innerText = name;
        previewContainer.classList.remove('d-none');
        chatTextarea.placeholder = "Añade un comentario...";
        chatTextarea.focus();
    }

    function removeAttachment() {
        attachmentInput.value = "";
        previewContainer.classList.add('d-none');
        chatTextarea.placeholder = "Escribe un mensaje...";
    }

    /**
     * ENVÍO
     */
    chatTextarea.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            if (window.innerWidth > 991) {
                e.preventDefault();
                if(this.value.trim() !== "" || attachmentInput.files.length > 0) {
                    this.closest('form').submit();
                }
            }
        }
    });

    function scrollToMessage(id) {
        const element = document.getElementById('message-' + id);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            element.classList.add('bg-light-warning-soft'); 
            setTimeout(() => {
                element.classList.remove('bg-light-warning-soft');
            }, 2000);
        }
    }
</script>
@endpush