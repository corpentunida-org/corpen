<div class="flex-grow-1 overflow-auto pe-2 mb-3" id="chat-messages-container" style="display: flex; flex-direction: column;">
    @forelse($messages as $message)
        @include('interactions.chat.messages._bubble', ['msg' => $message])
    @empty
        <div class="text-center text-muted my-auto">
            <i class="fa fa-comments fs-1 mb-2"></i><br>
            No hay mensajes en esta sala.<br>¡Sé el primero en escribir!
        </div>
    @endforelse
</div>