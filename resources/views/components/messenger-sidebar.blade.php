<div class="offcanvas offcanvas-end shadow-sm" tabindex="-1" id="offcanvasMessenger" aria-labelledby="offcanvasMessengerLabel" style="width: 380px; border-left: none;">
    
    <div class="offcanvas-header border-bottom bg-light-neutral py-3 px-4">
        <h5 class="offcanvas-title fw-boldest text-gray-900 d-flex align-items-center" id="offcanvasMessengerLabel">
            <i class="feather-message-square text-primary me-2 fs-4"></i>
            Mensajes
        </h5>
        <button type="button" class="btn-close text-reset shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-0 custom-scroll">
        
        <div class="p-3">
            @include('interactions.chat.workspaces._sidebar_list', ['workspaces' => $workspaces ?? []])
        </div>

    </div>
</div>

<style>
    /* Ajustes sutiles para el Offcanvas estilo Messenger */
    #offcanvasMessenger {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .bg-light-neutral {
        background-color: #f8fafc;
    }
    .custom-scroll::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
</style>