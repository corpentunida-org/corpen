<x-base-layout>
    <div class="card">
        <div class="card-header border-0 pb-0">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Centro de Interacciones y Chat</span>
            </h3>
        </div>
        
        <div class="card-body">
            <div class="d-flex flex-row" style="height: 70vh;">
                
                <div class="w-25 border-end pe-4" style="overflow-y: auto;">
                    @include('interactions.chat.workspaces._sidebar_list')
                </div>

                <div class="w-25 border-end px-4" style="overflow-y: auto;">
                    @if(isset($activeWorkspace))
                        @include('interactions.chat.conversations._list')
                    @else
                        <div class="text-muted text-center mt-5">
                            Selecciona un espacio de trabajo para ver sus salas.
                        </div>
                    @endif
                </div>

                <div class="w-50 ps-4 d-flex flex-column">
                    @if(isset($activeConversation))
                        @include('interactions.chat.conversations._header')
                        @include('interactions.chat.messages._window')
                        @include('interactions.chat.messages._input')
                    @else
                        <div class="text-muted text-center mt-auto mb-auto">
                            Selecciona una conversación para empezar a chatear.
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
            var chatContainer = document.getElementById('chat-messages-container');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }

            var messageInput = document.querySelector('textarea[name="body"]');
            if(messageInput) {
                messageInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault(); 
                        this.closest('form').submit(); 
                    }
                });
            }
        });
    </script>
    @endpush
</x-base-layout>