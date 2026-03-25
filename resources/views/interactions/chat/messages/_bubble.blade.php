@php
    $isMe = $msg->user_id === auth()->id();
@endphp

<div class="d-flex flex-column mb-3 align-items-{{ $isMe ? 'end' : 'start' }}">
    <div class="d-flex align-items-center mb-1">
        @if(!$isMe)
            <span class="text-muted fw-bold fs-7 me-2">{{ $msg->user->name ?? 'Usuario' }}</span>
        @endif
        <span class="text-muted fs-8">{{ $msg->created_at->format('H:i') }}</span>
        @if($isMe)
            <span class="text-muted fw-bold fs-7 ms-2">Tú</span>
        @endif
    </div>
    
    <div class="p-3 rounded {{ $isMe ? 'bg-primary text-white' : 'bg-light text-dark' }}" style="max-width: 80%;">
        {{ $msg->body }}
    </div>
</div>