<div class="flex-grow-1 overflow-auto custom-scroll p-4 p-lg-7" 
     id="chat-messages-container" 
     style="display: flex; flex-direction: column; scroll-behavior: smooth;">

    @forelse($messages as $message)
        @include('interactions.chat.messages._bubble', ['msg' => $message])
        
    @empty
        <div class="d-flex flex-column flex-center my-auto opacity-50">
            <div class="symbol symbol-100px mb-5">
                <div class="symbol-label bg-light-neutral">
                    <i class="bi bi-chat-dots fs-3x text-gray-400"></i>
                </div>
            </div>
            <div class="fw-bolder fs-5 text-gray-600">¡Aún no hay mensajes!</div>
            <p class="text-gray-400 fs-7">Rompe el hielo y envía el primer mensaje en esta sala.</p>
        </div>
    @endforelse

</div>

<style>
    /* Ajuste de scroll para móviles: 
       Evita que el contenido se pegue demasiado al borde derecho 
    */
    #chat-messages-container {
        scrollbar-width: thin;
        scrollbar-color: rgba(0,0,0,0.1) transparent;
        /* Mejora el rendimiento del scroll en iOS */
        -webkit-overflow-scrolling: touch; 
    }

    /* Añadimos un pequeño espacio al final para que el último mensaje 
       no quede tapado por el input en algunos navegadores móviles 
    */
    #chat-messages-container::after {
        content: "";
        display: block;
        min-height: 10px;
        width: 100%;
    }
</style>