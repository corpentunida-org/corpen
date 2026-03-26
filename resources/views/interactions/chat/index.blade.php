<x-base-layout>
    <style>
        :root {
            --app-bg: #f8fafc;
            --sidebar-width: 280px;
            --glass-border: #f1f5f9;
            --pastel-blue: #E0F2FE;
            --text-main: #1e293b;
        }

        /* Contenedor Principal estilo "App Shell" */
        .chat-app-container {
            display: flex;
            flex-direction: column;
            height: calc(100dvh - 120px); 
            overflow: hidden;
            background-color: white;
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.02), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
        }

        /* Layout de 3 Columnas */
        .chat-layout {
            display: flex;
            height: 100%;
            width: 100%;
            overflow: hidden;
        }

        /* Columna 1: Equipos (Soft Slate) */
        .col-workspaces-nav {
            width: 20%;
            min-width: 220px;
            background-color: #f8fafc;
            border-right: 1px solid var(--glass-border);
            transition: all 0.3s ease;
        }

        /* Columna 2: Conversaciones (Pure White) */
        .col-conversations-list {
            width: 25%;
            min-width: 280px;
            background-color: #ffffff;
            border-right: 1px solid var(--glass-border);
            transition: all 0.3s ease;
        }

        /* Columna 3: Ventana de Chat (Cloud Blue) */
        .col-main-chat {
            flex-grow: 1;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Ajustes para Scroll */
        .scroll-clean {
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: #e2e8f0 transparent;
        }
        .scroll-clean::-webkit-scrollbar { width: 4px; }
        .scroll-clean::-webkit-scrollbar-track { background: transparent; }
        .scroll-clean::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

        /* Mobile Logic (Breakpoints) */
        @media (max-width: 991.98px) {
            .chat-app-container {
                height: calc(100dvh - 80px); 
                margin: -1rem;
                border-radius: 0;
                border: none;
            }

            .col-mobile-hidden { display: none !important; }
            .col-mobile-visible { 
                display: flex !important; 
                width: 100% !important; 
                min-width: 100% !important;
            }
            
            /* Animación simple de entrada */
            .col-mobile-visible {
                animation: slideIn 0.2s ease-out;
            }
        }

        @keyframes slideIn {
            from { transform: translateX(15px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>

    <div class="chat-app-container">
        <div class="chat-layout">
            
            <div class="col-workspaces-nav p-6 scroll-clean 
                {{ isset($activeWorkspace) || isset($activeConversation) ? 'col-mobile-hidden' : 'col-mobile-visible' }}">
                @include('interactions.chat.workspaces._sidebar_list')
            </div>

            <div class="col-conversations-list p-6 scroll-clean
                {{ isset($activeConversation) ? 'col-mobile-hidden' : (isset($activeWorkspace) ? 'col-mobile-visible' : 'd-none d-lg-block') }}">
                
                @if(isset($activeWorkspace))
                    <div class="d-lg-none mb-6">
                        <a href="{{ route('interactions.chat.index') }}" class="btn btn-sm btn-light-primary rounded-pill">
                            <i class="bi bi-arrow-left me-2"></i> Equipos
                        </a>
                    </div>
                    @include('interactions.chat.conversations._list')
                @endif
            </div>

            <div class="col-main-chat
                {{ isset($activeConversation) ? 'col-mobile-visible' : 'd-none d-lg-flex' }}">
                
                @if(isset($activeConversation))
                    <div class="px-6 py-4 border-bottom bg-white flex-shrink-0 shadow-sm" style="z-index: 5;">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('interactions.chat.index', ['workspace_id' => $activeWorkspace->id]) }}" 
                               class="btn btn-icon btn-sm btn-light-primary me-4 d-lg-none rounded-circle">
                                <i class="bi bi-chevron-left fs-4"></i>
                            </a>
                            <div class="flex-grow-1">
                                @include('interactions.chat.conversations._header')
                            </div>
                        </div>
                    </div>

                    <div class="flex-grow-1 scroll-clean" id="chat-messages-scroll-container">
                        @include('interactions.chat.messages._window')
                    </div>

                    <div class="flex-shrink-0">
                        @include('interactions.chat.messages._input')
                    </div>
                @else
                    <div class="d-flex flex-column flex-center h-100 bg-light-subtle opacity-50">
                        <div class="bg-white p-10 rounded-circle mb-5 shadow-sm">
                            <i class="bi bi-chat-right-heart text-primary fs-5x"></i>
                        </div>
                        <h4 class="fw-boldest text-gray-800">Tu espacio de trabajo</h4>
                        <p class="text-muted">Selecciona un equipo y comienza a colaborar.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    @include('interactions.chat.workspaces._modal_create')
    @if(isset($activeWorkspace)) @include('interactions.chat.conversations._modal_context') @endif
    @if(isset($activeConversation)) @include('interactions.chat.conversations._modal_invite') @endif

    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const container = document.getElementById('chat-messages-scroll-container');
            
            // Función para scrollear al fondo
            const scrollToBottom = () => {
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            };

            scrollToBottom();

            // Fix para iOS Keyboard
            const input = document.querySelector('textarea[name="body"]');
            if (input) {
                input.addEventListener('focus', () => {
                    setTimeout(scrollToBottom, 300);
                });
            }
            
            // Observer para nuevos mensajes si usas livewire/pusher
            const observer = new MutationObserver(scrollToBottom);
            if (container) {
                observer.observe(container, { childList: true });
            }
        });
    </script>
    @endpush
</x-base-layout>