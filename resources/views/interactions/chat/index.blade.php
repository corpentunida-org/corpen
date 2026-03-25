<x-base-layout>
    <style>
        /* dvh (Dynamic Viewport Height) es mejor para móviles (evita que el teclado tape el input) */
        .chat-container {
            height: calc(100dvh - 200px);
            min-height: 500px;
        }

        @media (max-width: 991.98px) {
            .chat-container {
                height: calc(100dvh - 120px);
            }
            /* Control de visibilidad en móvil */
            .col-mobile-hidden { display: none !important; }
            .col-mobile-visible { display: flex !important; width: 100% !important; padding: 0 !important; }
        }

        /* SCROLL CON INERCIA PARA MÓVILES */
        .touch-scroll {
            overflow-y: auto;
            -webkit-overflow-scrolling: touch; /* Inercia en iOS */
            scroll-behavior: smooth;
        }

        /* Scrollbar fino para PC */
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #e0e0e0; border-radius: 10px; }
        
        .bg-chat-pattern {
            background-color: #f4f7f6;
            background-image: url('{{ asset("assets/media/misc/chat-bg.png") }}');
            background-repeat: repeat;
        }
    </style>

    <div class="card shadow-sm h-100">
        <div class="card-body p-0">
            <div class="d-flex flex-column flex-lg-row chat-container overflow-hidden">
                
                <div class="w-100 w-lg-20 border-end p-5 touch-scroll custom-scroll 
                    {{ isset($activeWorkspace) || isset($activeConversation) ? 'col-mobile-hidden' : 'col-mobile-visible' }}" 
                    style="background-color: #fcfcfc;">
                    @include('interactions.chat.workspaces._sidebar_list')
                </div>

                <div class="w-100 w-lg-25 border-end p-5 touch-scroll custom-scroll 
                    {{ isset($activeConversation) ? 'col-mobile-hidden' : (isset($activeWorkspace) ? 'col-mobile-visible' : 'd-none d-lg-block') }}">
                    
                    @if(isset($activeWorkspace))
                        <a href="{{ route('interactions.chat.index') }}" class="btn btn-sm btn-light mb-4 d-lg-none">
                            <i class="fa fa-arrow-left me-1"></i> Equipos
                        </a>
                        @include('interactions.chat.conversations._list')
                    @else
                        <div class="text-center mt-20 d-none d-lg-block opacity-50">
                            <i class="bi bi-people fs-3x mb-5"></i>
                            <p>Selecciona un equipo</p>
                        </div>
                    @endif
                </div>

                <div class="w-100 w-lg-55 d-flex flex-column 
                    {{ isset($activeConversation) ? 'col-mobile-visible' : 'd-none d-lg-flex' }} p-0">
                    
                    @if(isset($activeConversation))
                        <div class="p-4 p-lg-5 border-bottom bg-white flex-shrink-0">
                            <div class="d-flex align-items-center">
                                <a href="{{ route('interactions.chat.index', ['workspace_id' => $activeWorkspace->id]) }}" 
                                   class="btn btn-icon btn-sm btn-light me-3 d-lg-none">
                                    <i class="fa fa-arrow-left"></i>
                                </a>
                                <div class="flex-grow-1 min-w-0">
                                    @include('interactions.chat.conversations._header')
                                </div>
                            </div>
                        </div>

                        <div class="flex-grow-1 touch-scroll custom-scroll bg-chat-pattern" id="chat-messages-scroll-container">
                            @include('interactions.chat.messages._window')
                        </div>

                        <div class="flex-shrink-0 bg-white shadow-sm">
                            @include('interactions.chat.messages._input')
                        </div>
                    @else
                        <div class="text-center my-auto">
                            <i class="bi bi-chat-square-dots fs-4x text-light-primary mb-5"></i>
                            <h4 class="fw-bolder">Centro de Mensajes</h4>
                            <p class="text-muted">Selecciona una sala para comenzar a chatear.</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    @include('interactions.chat.workspaces._modal_create')
    @if(isset($activeWorkspace))
        @include('interactions.chat.conversations._modal_context')
    @endif
    @if(isset($activeConversation))
        @include('interactions.chat.conversations._modal_invite')
    @endif

    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const scrollToBottom = () => {
                const container = document.getElementById('chat-messages-scroll-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            };

            scrollToBottom();

            // Enfocar automáticamente en escritorio
            if (window.innerWidth > 992) {
                const input = document.querySelector('textarea[name="body"]');
                if (input) input.focus();
            }
        });
    </script>
    @endpush
</x-base-layout>