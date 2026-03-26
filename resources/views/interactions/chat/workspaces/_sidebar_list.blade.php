@php
    // Inyectamos los usuarios para el buscador de Mensajes Directos y así evitar errores de variable no definida
    $usuariosDisponibles = \App\Models\User::orderBy('name', 'asc')->get();
@endphp

<div class="d-flex justify-content-between align-items-center mb-8 px-2">
    <h4 class="fw-boldest m-0 text-gray-900 fs-5 tracking-tight">Equipos</h4>
    
    <button type="button" class="btn btn-icon btn-sm btn-clean-add" 
            data-bs-toggle="modal" data-bs-target="#modalCreateWorkspace">
        <i class="bi bi-plus-lg"></i>
    </button>
</div>

<div class="messenger-search-container mb-6 px-2">
    <label class="fw-bold text-gray-500 fs-9 mb-2 d-block text-uppercase letter-spacing-wider">Mensajes Directos</label>
    <select class="form-select form-select-solid" id="select-user-dm" data-placeholder="Buscar persona...">
        <option value=""></option>
        @foreach($usuariosDisponibles as $user)
            @if($user->id !== auth()->id())
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endif
        @endforeach
    </select>
</div>

<form id="form-start-private-chat" action="{{ route('interactions.conversations.startPrivate') }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="target_user_id" id="target-user-id-input">
</form>

<div class="search-container mb-8">
    <div class="search-wrapper">
        <i class="bi bi-search search-icon"></i>
        <input type="text" class="search-input" id="search-workspaces" placeholder="Buscar..." />
    </div>
</div>

<div class="workspace-nav" id="workspace-list">
    @forelse($workspaces as $workspace)
        @php 
            $isActive = (isset($activeWorkspace) && $activeWorkspace->id == $workspace->id); 
            $initial = strtoupper(substr($workspace->name, 0, 1));
            
            // Colores Pastel Ultra-Suaves
            $pastelStyles = [
                'A' => ['bg' => '#E0F2FE', 'text' => '#0369A1'], // Azul
                'B' => ['bg' => '#DCFCE7', 'text' => '#15803D'], // Verde
                'C' => ['bg' => '#FEF9C3', 'text' => '#A16207'], // Amarillo
                'D' => ['bg' => '#F3E8FF', 'text' => '#7E22CE'], // Morado
                'E' => ['bg' => '#FFE4E6', 'text' => '#BE123C'], // Rosa
            ];
            $style = $pastelStyles[$initial] ?? ['bg' => '#F1F5F9', 'text' => '#475569'];
        @endphp

        <a href="{{ route('interactions.chat.index', ['workspace_id' => $workspace->id]) }}" 
           class="workspace-item {{ $isActive ? 'is-active' : '' }}">
            
            <div class="d-flex align-items-center w-100">
                <div class="workspace-avatar" style="background-color: {{ $style['bg'] }}; color: {{ $style['text'] }};">
                    {{ $initial }}
                </div>

                <div class="flex-grow-1 min-w-0">
                    <span class="workspace-name {{ $isActive ? 'fw-bold' : 'fw-medium' }}">
                        {{ $workspace->name }}
                    </span>
                    <span class="workspace-area">
                        {{ $workspace->area->nombre ?? 'General' }}
                    </span>
                </div>

                @if($workspace->conversations->count() > 0)
                    <span class="workspace-badge">
                        {{ $workspace->conversations->count() }}
                    </span>
                @endif
            </div>
        </a>
    @empty
        <div class="text-center py-10">
            <span class="text-muted fs-8">No hay equipos aún.</span>
        </div>
    @endforelse
</div>

<style>
    /* RESET & BASE */
    .tracking-tight { letter-spacing: -0.025em; }
    .letter-spacing-wider { letter-spacing: 0.05em; }

    /* ESTILO SELECT2 MESSENGER (Ajustado a tu estilo minimalista) */
    .select2-container--bootstrap5 .select2-selection--single.form-select-solid {
        background-color: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 10px;
        padding: 4px 10px;
        height: 40px;
    }

    /* BOTÓN CREAR MINIMALISTA */
    .btn-clean-add {
        background: #f1f5f9;
        color: #64748b;
        border-radius: 8px;
        width: 32px;
        height: 32px;
        transition: all 0.2s ease;
        border: none;
    }
    .btn-clean-add:hover {
        background: #e2e8f0;
        color: #0f172a;
        transform: scale(1.05);
    }

    /* FIX LUPA: Alineación Perfecta con Flexbox */
    .search-wrapper {
        display: flex;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 10px;
        padding: 8px 14px;
        transition: all 0.2s ease;
    }
    .search-wrapper:focus-within {
        background: #fff;
        border-color: #e2e8f0;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
    }
    .search-icon {
        color: #94a3b8;
        font-size: 0.9rem;
        margin-right: 10px;
        display: flex;
        align-items: center;
    }
    .search-input {
        border: none;
        background: transparent;
        outline: none;
        font-size: 0.85rem;
        color: #1e293b;
        width: 100%;
    }
    .search-input::placeholder { color: #cbd5e1; }

    /* ITEMS DE LA LISTA (UX MINIMALISTA) */
    .workspace-nav {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .workspace-item {
        display: flex;
        align-items: center;
        padding: 10px 12px;
        text-decoration: none;
        border-radius: 10px;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }
    .workspace-item:hover {
        background-color: #f8fafc;
    }
    .workspace-item.is-active {
        background-color: #ffffff;
        border-color: #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    /* AVATAR */
    .workspace-avatar {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        margin-right: 12px;
        flex-shrink: 0;
    }

    /* TEXTOS */
    .workspace-name {
        display: block;
        font-size: 0.85rem;
        color: #334155;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .workspace-area {
        display: block;
        font-size: 0.7rem;
        color: #94a3b8;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    /* BADGE */
    .workspace-badge {
        background: #f1f5f9;
        color: #64748b;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 20px;
        margin-left: 8px;
    }
    .workspace-item.is-active .workspace-badge {
        background: #e0f2fe;
        color: #0369A1;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- 1. Lógica Messenger (Select2) ---
        $('#select-user-dm').select2({
            minimumResultsForSearch: 1,
            templateResult: function(user) {
                if (!user.id) return user.text;
                const initial = user.text.charAt(0).toUpperCase();
                return $(`
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-25px symbol-circle me-3">
                            <span class="symbol-label bg-light-primary text-primary fs-9 fw-bold">${initial}</span>
                            <span class="bg-success position-absolute border border-white h-8px w-8px rounded-circle translate-middle start-100 top-100 ms-n1 mt-n1"></span>
                        </div>
                        <span class="fs-8 fw-bold">${user.text}</span>
                    </div>
                `);
            }
        });

        // Evento al seleccionar usuario en Messenger
        $('#select-user-dm').on('select2:select', function (e) {
            const userId = e.params.data.id;
            document.getElementById('target-user-id-input').value = userId;
            document.getElementById('form-start-private-chat').submit();
        });

        // --- 2. Filtro de Equipos (Tu lógica original) ---
        document.getElementById('search-workspaces')?.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            document.querySelectorAll('.workspace-item').forEach(item => {
                const text = item.innerText.toLowerCase();
                item.style.display = text.includes(filter) ? "flex" : "none";
            });
        });
    });
</script>