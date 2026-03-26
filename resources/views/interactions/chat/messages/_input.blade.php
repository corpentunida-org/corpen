<div class="p-4 p-lg-5 border-top mt-auto bg-white">
    
    <div id="reply-preview-container" class="mb-3 d-none animate-slide-up">
        <div class="d-flex align-items-center justify-content-between p-3 rounded-3" 
             style="background-color: #f0f7ff; border-left: 4px solid #3E97FF;">
            <div class="overflow-hidden me-3">
                <div class="d-flex align-items-center mb-1">
                    <i class="bi bi-reply-fill text-primary me-2 fs-6"></i>
                    <span class="fw-boldest text-primary text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.05em;">
                        Respondiendo a <span id="reply-user-name"></span>
                    </span>
                </div>
                <div class="text-gray-600 fs-8 text-truncate" id="reply-text-preview"></div>
            </div>
            <button type="button" class="btn btn-icon btn-sm btn-active-light-danger rounded-circle" onclick="cancelReply()">
                <i class="bi bi-x-lg fs-7"></i>
            </button>
        </div>
    </div>

    <div id="attachment-preview-container" class="mb-3 d-none animate-slide-up">
        <div class="d-flex align-items-center justify-content-between p-2 px-4 rounded-pill shadow-sm"
             style="background-color: #f0fdf4; border: 1px solid #dcfce7;">
            <div class="d-flex align-items-center overflow-hidden">
                <i class="bi bi-paperclip fs-5 text-success me-2"></i>
                <span id="preview-filename" class="fw-bold text-success fs-8 text-truncate" style="max-width: 200px;"></span>
            </div>
            <button type="button" class="btn btn-sm btn-icon btn-link text-success ms-2" onclick="removeAttachment()">
                <i class="bi bi-x-circle-fill fs-5"></i>
            </button>
        </div>
    </div>

    <form id="chat-form" action="{{ route('interactions.messages.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="conversation_id" value="{{ $activeConversation->id }}">
        <input type="hidden" name="parent_id" id="parent-id-input" value="">
        <input type="file" name="attachment" id="file-attachment" class="d-none" onchange="handleFileSelect(this)">

        <div class="input-container-pill">
            <button type="button" class="btn btn-icon btn-hover-scale flex-shrink-0" 
                    style="background: #f1f5f9; color: #64748b; border-radius: 50%; width: 40px; height: 40px;"
                    onclick="document.getElementById('file-attachment').click()">
                <i class="bi bi-plus-lg fs-4"></i>
            </button>

            <div class="flex-grow-1">
                <textarea class="form-control form-control-flush custom-textarea" 
                          name="body" 
                          rows="1" 
                          placeholder="Escribe un mensaje..." 
                          oninput="autoResize(this)"></textarea>
            </div>

            <button class="btn btn-send-minimal flex-shrink-0" 
                    type="submit" 
                    id="btn-send-chat">
                <i class="bi bi-send-fill fs-4"></i>
            </button>
        </div>
    </form>
</div>

<style>
    /* Estenedor tipo Cápsula */
    .input-container-pill {
        display: flex;
        align-items: flex-end;
        gap: 12px;
        background-color: #f8fafc;
        border: 1px solid #f1f5f9;
        padding: 8px 12px;
        border-radius: 28px;
        transition: all 0.3s ease;
    }

    .input-container-pill:focus-within {
        background-color: #ffffff;
        border-color: #3E97FF;
        box-shadow: 0 10px 25px -5px rgba(62, 151, 255, 0.1);
    }

    /* Input limpio */
    .custom-textarea {
        background: transparent !important;
        border: none !important;
        padding: 10px 4px !important;
        font-size: 0.95rem !important;
        color: #1e293b !important;
        resize: none !important;
        min-height: 24px;
        max-height: 180px;
        outline: none !important;
        box-shadow: none !important;
    }

    /* Botón Enviar Estilo Floating */
    .btn-send-minimal {
        width: 40px;
        height: 40px;
        background-color: #3E97FF;
        color: white;
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(62, 151, 255, 0.3);
        transition: all 0.2s ease;
    }

    .btn-send-minimal:hover {
        transform: scale(1.1) translateY(-2px);
        background-color: #2884ef;
        box-shadow: 0 6px 15px rgba(62, 151, 255, 0.4);
    }

    /* Animación de entrada para previews */
    .animate-slide-up {
        animation: slideUp 0.3s ease-out forwards;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
        .custom-textarea { font-size: 16px !important; }
        .input-container-pill { padding: 6px 10px; gap: 8px; }
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

    // Lógica autoResize mantenida
    function autoResize(el) {
        el.style.height = '40px'; 
        el.style.height = (el.scrollHeight) + 'px';
    }

    // Lógica setReply mantenida
    function setReply(id, user, text) {
        replyInput.value = id;
        replyUser.innerText = user;
        replyText.innerText = text;
        replyContainer.classList.remove('d-none');
        chatTextarea.placeholder = "Escribe tu respuesta...";
        chatTextarea.focus();
    }

    // Lógica cancelReply mantenida
    function cancelReply() {
        replyInput.value = "";
        replyContainer.classList.add('d-none');
        chatTextarea.placeholder = "Escribe un mensaje...";
        autoResize(chatTextarea);
    }

    // Lógica de archivos mantenida al 100%
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

    // Enter para enviar (Desktop) mantenida
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

    // Pegar imágenes mantenida
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
</script>
@endpush