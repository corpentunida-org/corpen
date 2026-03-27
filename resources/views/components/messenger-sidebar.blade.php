@php
    $usuariosDisponibles = auth()->check() 
        ? \App\Models\User::where('id', '!=', auth()->id())->orderBy('name', 'asc')->get() 
        : [];
@endphp

<div class="offcanvas offcanvas-end shadow border-0" tabindex="-1" id="offcanvasMessenger" style="width: 100%; max-width: 400px;">
    
    <div class="offcanvas-header border-bottom py-3 px-4 bg-white">
        <div class="d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                <i class="bi bi-chat-square-text-fill text-primary fs-4"></i>
            </div>
            <div>
                <h5 class="offcanvas-title fw-bold text-dark m-0">Mensajes</h5>
                <small class="text-muted">Chat Directo</small>
            </div>
        </div>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body p-0 d-flex flex-column bg-light-subtle">
        
        <div class="p-4 bg-white border-bottom shadow-sm">
            <label class="form-label fw-bold text-uppercase fs-9 text-muted mb-3 tracking-wider">
                <i class="bi bi-person-plus me-1"></i> Nueva Conversación
            </label>
            
            <div class="input-group input-group-merge shadow-sm rounded-3 overflow-hidden border">
                <span class="input-group-text bg-light border-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" 
                       id="user-search-input" 
                       class="form-control border-0 ps-2 py-2 fs-8 shadow-none bg-light" 
                       placeholder="Escribe un nombre..."
                       autocomplete="off">
            </div>
        </div>

        <div class="flex-grow-1 overflow-auto custom-scroll p-3" id="user-list-container">
            <div class="d-grid gap-1">
                @forelse($usuariosDisponibles as $user)
                    <button type="button" 
                            class="btn btn-link text-decoration-none p-3 rounded-3 user-row transition-all border-0 text-start w-100" 
                            data-user-id="{{ $user->id }}"
                            data-user-name="{{ strtolower($user->name) }}">
                        
                        <div class="d-flex align-items-center">
                            @php $initial = strtoupper(substr($user->name, 0, 1)); @endphp
                            <div class="avatar-circle me-3 shadow-sm">
                                {{ $initial }}
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 text-dark fw-bold fs-8">{{ $user->name }}</h6>
                                <span class="text-muted fs-9 text-uppercase">Hacer clic para chatear</span>
                            </div>
                            <i class="bi bi-chevron-right text-light-emphasis fs-9"></i>
                        </div>
                    </button>
                @empty
                    <div class="text-center py-5">
                        <p class="text-muted fs-8">No hay usuarios disponibles.</p>
                    </div>
                @endforelse

                <div id="no-users-found" class="text-center py-5 d-none">
                    <i class="bi bi-emoji-frown fs-1 text-muted opacity-25"></i>
                    <p class="text-muted fs-8 mt-2">No encontramos a nadie con ese nombre.</p>
                </div>
            </div>
        </div>

        <div id="global-loader-chat" class="d-none position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-flex align-items-center justify-content-center" style="z-index: 2000;">
            <div class="text-center">
                <div class="spinner-border text-primary mb-2" role="status"></div>
                <div class="fs-8 fw-bold">Abriendo chat...</div>
            </div>
        </div>
    </div>
</div>

<style>
    .fs-9 { font-size: 0.65rem !important; }
    .fs-8 { font-size: 0.85rem !important; }
    .tracking-wider { letter-spacing: 0.08em; }

    /* Estilo de la fila de usuario */
    .user-row {
        background-color: #ffffff;
        border: 1px solid #f1f5f9 !important;
        transition: all 0.2s ease;
    }
    .user-row:hover {
        background-color: #f8fafc;
        transform: translateY(-2px);
        border-color: #3e97ff50 !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .user-row:active { transform: scale(0.98); }

    /* Avatar con inicial */
    .avatar-circle {
        width: 42px;
        height: 42px;
        background: linear-gradient(135deg, #3E97FF 0%, #2B78E4 100%);
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    /* Scroll Personalizado */
    .custom-scroll::-webkit-scrollbar { width: 5px; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const offcanvas = document.getElementById('offcanvasMessenger');
    const searchInput = document.getElementById('user-search-input');
    const userRows = document.querySelectorAll('.user-row');
    const noResults = document.getElementById('no-users-found');
    const loader = document.getElementById('global-loader-chat');

    // 1. Lógica del Buscador (Filtrado instantáneo)
    searchInput?.addEventListener('input', function() {
        const term = this.value.toLowerCase().trim();
        let count = 0;

        userRows.forEach(row => {
            const userName = row.getAttribute('data-user-name');
            if (userName.includes(term)) {
                row.classList.remove('d-none');
                count++;
            } else {
                row.classList.add('d-none');
            }
        });

        noResults.classList.toggle('d-none', count > 0);
    });

    // 2. Al abrir el Offcanvas: Limpiar búsqueda y poner foco
    offcanvas?.addEventListener('shown.bs.offcanvas', () => {
        searchInput.value = '';
        userRows.forEach(r => r.classList.remove('d-none'));
        noResults.classList.add('d-none');
        searchInput.focus();
    });

    // 3. Al hacer clic en un usuario (AJAX)
    userRows.forEach(row => {
        row.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            if(!userId) return;

            loader.classList.remove('d-none'); // Mostrar bloqueo de carga

            fetch("{{ route('interactions.conversations.startPrivate') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ target_user_id: userId })
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url; // Redirigir si el controlador lo pide
                } else {
                    return response.json();
                }
            })
            .then(data => {
                // Si llegamos aquí sin redirección, ocultar el loader para poder elegir otro
                loader.classList.add('d-none');
            })
            .catch(error => {
                console.error("Error:", error);
                loader.classList.add('d-none');
                alert("Ocurrió un error al intentar abrir el chat.");
            });
        });
    });
});
</script>