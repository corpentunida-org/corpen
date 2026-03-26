<div class="flex-grow-1 overflow-auto custom-scroll p-5 p-lg-10 chat-messages-viewport" 
     id="chat-messages-container" 
     style="display: flex; flex-direction: column; scroll-behavior: smooth;">

    @forelse($messages as $message)
        @include('interactions.chat.messages._bubble', ['msg' => $message])
        
    @empty
        <div class="empty-chat-state my-auto">
            <div class="empty-icon-wrapper mb-6">
                <div class="pulse-ring"></div>
                <i class="bi bi-chat-left-dots"></i>
            </div>
            <h5 class="fw-boldest text-gray-800 fs-4 mb-2">Comienza la conversación</h5>
            <p class="text-muted fs-7 px-10">Este es el inicio de la historia de este canal. <br> Di algo amable para empezar.</p>
            
            <div class="d-flex justify-content-center gap-2 mt-5">
                <span class="badge badge-light-primary badge-sm">Privacidad cifrada</span>
                <span class="badge badge-light-success badge-sm">Equipo activo</span>
            </div>
        </div>
    @endforelse

</div>

<style>
    /* FONDO DE LA VENTANA (Textura sutil) */
    .chat-messages-viewport {
        background-color: #f8fafc; /* Gris azulado ultra claro */
        background-image: 
            radial-gradient(at 0% 0%, rgba(224, 242, 254, 0.5) 0px, transparent 50%),
            radial-gradient(at 100% 100%, rgba(243, 232, 255, 0.5) 0px, transparent 50%);
        position: relative;
    }

    /* ESTADO VACÍO ESTILIZADO */
    .empty-chat-state {
        text-align: center;
        max-width: 350px;
        align-self: center;
        animation: fadeIn 0.8s ease-out;
    }

    .empty-icon-wrapper {
        position: relative;
        width: 80px;
        height: 80px;
        background: white;
        border-radius: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 2.5rem;
        color: #3E97FF;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }

    /* Efecto de pulso suave para el icono */
    .pulse-ring {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 24px;
        border: 2px solid #3E97FF;
        opacity: 0;
        animation: pulse-animation 2s infinite;
    }

    @keyframes pulse-animation {
        0% { transform: scale(1); opacity: 0.5; }
        100% { transform: scale(1.4); opacity: 0; }
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* SCROLLBAR MINIMALISTA */
    #chat-messages-container::-webkit-scrollbar {
        width: 6px;
    }

    #chat-messages-container::-webkit-scrollbar-track {
        background: transparent;
    }

    #chat-messages-container::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 10px;
    }

    #chat-messages-container::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.1);
    }

    /* Espaciado de seguridad inferior */
    #chat-messages-container::after {
        content: "";
        display: block;
        min-height: 20px;
        width: 100%;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .chat-messages-viewport {
            padding: 1.5rem !important;
        }
    }
</style>