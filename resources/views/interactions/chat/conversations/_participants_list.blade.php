@php
    // Identificamos al usuario actual dentro de la lista
    $me = $participants->where('user_id', auth()->id())->first();
    $isAdmin = ($me && $me->role->slug == 'admin');
@endphp

<div class="mt-2">
    <div class="d-flex align-items-center justify-content-between mb-5">
        <h6 class="fw-bolder text-uppercase text-gray-500 m-0" style="font-size: 0.7rem; letter-spacing: 0.05rem;">
            Miembros de la sala <span class="badge badge-circle badge-light-secondary ms-1">{{ $participants->count() }}</span>
        </h6>
    </div>

    <div class="list-group list-group-flush">
        @foreach($participants as $participant)
            @php $isItMe = ($participant->user_id === auth()->id()); @endphp
            
            <div class="list-group-item px-0 py-3 border-0 d-flex align-items-center justify-content-between bg-transparent participant-item">
                
                <div class="d-flex align-items-center min-w-0">
                    <div class="symbol symbol-35px symbol-circle me-3 flex-shrink-0">
                        <div class="symbol-label fw-bold {{ $isItMe ? 'bg-light-success text-success' : 'bg-light-primary text-primary' }}">
                            {{ strtoupper(substr($participant->user->name, 0, 1)) }}
                        </div>
                    </div>

                    <div class="d-flex flex-column min-w-0">
                        <div class="d-flex align-items-center">
                            <span class="fs-7 fw-bolder text-gray-800 text-truncate {{ $isItMe ? 'pe-1' : '' }}" style="max-width: 150px;">
                                {{ $participant->user->name }}
                            </span>
                            @if($isItMe)
                                <span class="badge badge-light-success fw-bold" style="font-size: 0.6rem; padding: 0.15rem 0.3rem;">TÚ</span>
                            @endif
                        </div>
                        
                        <div class="d-flex align-items-center mt-1">
                            <span class="badge badge-light-{{ $participant->role->slug == 'admin' ? 'danger' : 'info' }} fw-bold" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;">
                                {{ $participant->role->name }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center ms-2">
                    @if($isAdmin && !$isItMe)
                        <form action="{{ route('interactions.conversations.removeParticipant', $participant->id) }}" 
                              method="POST" 
                              class="m-0"
                              onsubmit="return confirm('¿Estás seguro de que deseas quitar a {{ $participant->user->name }} de esta sala?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-icon btn-sm btn-light-danger btn-active-danger shadow-none rounded-circle w-30px h-30px" 
                                    title="Quitar de la sala">
                                <i class="bi bi-person-x-fill fs-6"></i>
                            </button>
                        </form>
                    @elseif($isItMe)
                        {{-- Icono informativo de seguridad para el usuario actual --}}
                        <i class="bi bi-shield-check text-success fs-4 opacity-50" title="Eres tú"></i>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    /* Mejora de UX: Hover sutil en la lista */
    .participant-item {
        transition: background-color 0.2s ease;
    }
    
    @media (hover: hover) {
        .participant-item:hover {
            background-color: rgba(0,0,0,0.02) !important;
        }
    }

    /* Ajuste para que el texto no se rompa en pantallas pequeñas */
    @media (max-width: 380px) {
        .participant-item .fs-7 {
            max-width: 100px !important;
        }
    }
</style>