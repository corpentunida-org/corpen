<x-base-layout>
    <style>
        /* FIX CRÍTICO: Usamos flexbox en el body/container para que 
           el chat ocupe el 100% del espacio disponible sin salirse.
        */
        .chat-wrapper {
            display: flex;
            flex-direction: column;
            /* 100dvh es "Dynamic Viewport Height", se ajusta si sale el teclado */
            height: calc(100dvh - 150px); 
            overflow: hidden;
        }

        @media (max-width: 991.98px) {
            .chat-wrapper {
                /* En móvil quitamos más espacio para el navbar de Metronic */
                height: calc(100dvh - 100px); 
                margin: -1rem; /* Expandimos al borde total en móvil */
            }
            .col-mobile-hidden { display: none !important; }
            .col-mobile-visible { 
                display: flex !important; 
                flex-direction: column;
                width: 100% !important; 
                height: 100% !important;
            }
        }

        /* Fuerza el scroll suave y con inercia */
        .scroll-y-auto {
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
        }

        /* Scroll fino para PC */
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #dbdfe9; border-radius: 10px; }
    </style>

    <div class="card shadow-none bg-transparent chat-wrapper">
        <div class="card-body p-0 d-flex flex-column h-100">
            <div class="d-flex flex-column flex-lg-row h-100 overflow-hidden">
                
                <div class="w-100 w-lg-20 border-end p-5 scroll-y-auto custom-scroll 
                    {{ isset($activeWorkspace) || isset($activeConversation) ? 'col-mobile-hidden' : 'col-mobile-visible' }}" 
                    style="background-color: #f9f9f9;">
                    @include('interactions.chat.workspaces._sidebar_list')
                </div>

                <div class="w-100 w-lg-25 border-end p-5 scroll-y-auto custom-scroll 
                    {{ isset($activeConversation) ? 'col-mobile-hidden' : (isset($activeWorkspace) ? 'col-mobile-visible' : 'd-none d-lg-block') }}">
                    
                    @if(isset($activeWorkspace))
                        <div class="d-lg-none mb-4">
                            <a href="{{ route('interactions.chat.index') }}" class="btn btn-sm btn-light py-2">
                                <i class="bi bi-arrow-left"></i> Volver a Equipos
                            </a>
                        </div>
                        @include('interactions.chat.conversations._list')
                    @endif
                </div>

                <div class="w-100 w-lg-55 d-flex flex-column h-100
                    {{ isset($activeConversation) ? 'col-mobile-visible' : 'd-none d-lg-flex' }} p-0 bg-white">
                    
                    @if(isset($activeConversation))
                        <div class="p-4 border-bottom flex-shrink-0">
                            <div class="d-flex align-items-center">
                                <a href="{{ route('interactions.chat.index', ['workspace_id' => $activeWorkspace->id]) }}" 
                                   class="btn btn-icon btn-sm btn-light me-3 d-lg-none">
                                    <i class="bi bi-chevron-left fs-2"></i>
                                </a>
                                <div class="flex-grow-1 min-w-0">
                                    @include('interactions.chat.conversations._header')
                                </div>
                            </div>
                        </div>

                        <div class="flex-grow-1 scroll-y-auto custom-scroll p-5" 
                             id="chat-messages-scroll-container"
                             style="background-color: #f4f7f6;">
                            @include('interactions.chat.messages._window')
                        </div>

                        <div class="p-3 border-top flex-shrink-0 bg-white">
                            @include('interactions.chat.messages._input')
                        </div>
                    @else
                        <div class="d-flex flex-column flex-center h-100 opacity-50">
                            <i class="bi bi-chat-square-text fs-5x mb-5"></i>
                            <h4 class="fw-bolder">Selecciona una conversación</h4>
                        </div>
                    @endif
                </div>

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
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
            
            // Fix para iOS: asegura que el input no se oculte al ganar foco
            const input = document.querySelector('textarea[name="body"]');
            if (input) {
                input.addEventListener('focus', () => {
                    setTimeout(() => {
                        container.scrollTop = container.scrollHeight;
                    }, 300);
                });
            }
        });
    </script>
    @endpush
</x-base-layout>