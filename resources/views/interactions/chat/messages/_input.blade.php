<div class="pt-3 border-top mt-auto">
    <form action="{{ route('interactions.messages.store') }}" method="POST">
        @csrf
        <input type="hidden" name="conversation_id" value="{{ $activeConversation->id }}">
        
        <div class="input-group">
            <textarea class="form-control" name="body" rows="2" placeholder="Escribe tu mensaje aquí..." required style="resize: none;"></textarea>
            <button class="btn btn-primary px-4" type="submit">
                <i class="fa fa-paper-plane me-1"></i> Enviar
            </button>
        </div>
    </form>
</div>