{{-- Este panel está en TODAS las páginas (layouts/base), así que la lista de usuarios se pide por
     AJAX la primera vez que se abre: pintarla aquí agregaba más de 1 MB de HTML a cada pantalla,
     aunque nadie abriera el chat. --}}
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
                <div id="user-list" class="d-grid gap-1"></div>

                <template id="user-row-template">
                    <button type="button"
                            class="btn btn-link text-decoration-none p-3 rounded-3 user-row transition-all border-0 text-start w-100">
                        <div class="d-flex align-items-center w-100">
                            <div class="avatar-circle me-3 shadow-sm"></div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 text-dark fw-bold fs-8"></h6>
                                <span class="text-muted fs-9 text-uppercase">Hacer clic para chatear</span>
                            </div>
                            <i class="bi bi-chevron-right text-light-emphasis fs-9"></i>
                        </div>
                    </button>
                </template>

                <div id="user-list-status" class="text-center py-5 d-none">
                    <p class="text-muted fs-8 mb-0"></p>
                </div>

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
    const userList = document.getElementById('user-list');
    const rowTemplate = document.getElementById('user-row-template');
    const listStatus = document.getElementById('user-list-status');
    const noResults = document.getElementById('no-users-found');
    const loader = document.getElementById('global-loader-chat');
    const getUserRows = () => userList.querySelectorAll('.user-row');
    let usuariosCargados = false;

    function mostrarEstadoLista(mensaje) {
        listStatus.querySelector('p').textContent = mensaje || '';
        listStatus.classList.toggle('d-none', !mensaje);
    }

    function pintarUsuarios(usuarios) {
        const filas = usuarios.map(usuario => {
            const row = rowTemplate.content.firstElementChild.cloneNode(true);
            row.dataset.userId = usuario.id;
            row.dataset.userName = usuario.name.toLowerCase();
            row.querySelector('.avatar-circle').textContent = usuario.name.charAt(0).toUpperCase();
            row.querySelector('h6').textContent = usuario.name;
            return row;
        });
        userList.replaceChildren(...filas);
    }

    // Solo se consulta una vez por página; si falla, se reintenta al volver a abrir el panel.
    function cargarUsuarios() {
        if (usuariosCargados) return;
        usuariosCargados = true;
        mostrarEstadoLista('Cargando usuarios...');

        fetch("{{ route('interactions.conversations.usuariosDisponibles') }}", {
            headers: { "Accept": "application/json" }
        })
        .then(response => {
            if (!response.ok) throw new Error(response.status);
            return response.json();
        })
        .then(usuarios => {
            pintarUsuarios(usuarios);
            mostrarEstadoLista(usuarios.length ? '' : 'No hay usuarios disponibles.');
        })
        .catch(error => {
            console.error("Error:", error);
            usuariosCargados = false;
            mostrarEstadoLista('No se pudieron cargar los usuarios. Cierra y vuelve a abrir el panel.');
        });
    }

    // 1. Lógica del Buscador (Filtrado instantáneo)
    searchInput?.addEventListener('input', function() {
        const term = this.value.toLowerCase().trim();
        let count = 0;

        getUserRows().forEach(row => {
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
    offcanvas?.addEventListener('show.bs.offcanvas', cargarUsuarios);
    offcanvas?.addEventListener('shown.bs.offcanvas', () => {
        searchInput.value = '';
        getUserRows().forEach(r => r.classList.remove('d-none'));
        noResults.classList.add('d-none');
        searchInput.focus();
    });

    // 3. Al hacer clic en un usuario (AJAX) — delegado porque las filas llegan después
    userList?.addEventListener('click', function(event) {
        const row = event.target.closest('.user-row');
        const userId = row?.getAttribute('data-user-id');
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
</script>