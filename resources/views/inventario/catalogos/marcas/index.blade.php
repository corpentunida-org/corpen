<x-base-layout>
    <style>
        /* Variables y tipografía general */
        :root {
            --primary: #0f172a;
            --brand: #2563eb;
            --brand-hover: #1d4ed8;
            --surface: #ffffff;
            --background: #f8fafc;
            --border: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --success-bg: #dcfce7;
            --success-text: #166534;
            --error-bg: #fee2e2;
            --error-text: #991b1b;
            --radius: 10px;
        }

        .cat-wrapper { max-width: 800px; margin: 40px auto; font-family: 'Inter', system-ui, sans-serif; color: var(--text-main); }
        .cat-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .cat-title { font-size: 1.75rem; font-weight: 800; color: var(--primary); margin: 0; }

        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 500;}
        .alert-success { background: var(--success-bg); color: var(--success-text); border: 1px solid #bbf7d0;}
        .alert-error { background: var(--error-bg); color: var(--error-text); border: 1px solid #fecaca;}
        .alert-error ul { margin: 5px 0 0 20px; padding: 0; }

        .cat-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }

        .cat-form { display: flex; flex-direction: column; gap: 16px; margin-bottom: 30px; padding-bottom: 24px; border-bottom: 1px solid var(--border); }
        .form-row { display: flex; gap: 16px; } /* Nuevo para poner inputs lado a lado */
        .form-row > div { flex: 1; }
        
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-label { font-size: 0.875rem; font-weight: 600; color: var(--text-main); }
        .cat-input, .cat-textarea { width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; transition: border-color 0.2s, box-shadow 0.2s; }
        .cat-input:focus, .cat-textarea:focus { outline: none; border-color: var(--brand); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        .cat-textarea { min-height: 80px; resize: vertical; }
        
        .search-container { position: relative; margin-bottom: 20px; }
        .search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; }

        .cat-btn { background: var(--primary); color: #fff; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: background 0.2s; align-self: flex-start; }
        .cat-btn:hover { background: #000; }
        .cat-btn-edit { background: #f1f5f9; color: var(--brand); border: 1px solid #cbd5e1; padding: 6px 14px; border-radius: 6px; cursor: pointer; font-size: 0.875rem; font-weight: 500; transition: all 0.2s; }
        .cat-btn-edit:hover { background: var(--brand); color: #fff; border-color: var(--brand); }
        
        .cat-item { display: flex; justify-content: space-between; align-items: center; padding: 16px 0; border-bottom: 1px solid var(--border); }
        .cat-item:last-child { border-bottom: none; padding-bottom: 0; }
        .cat-name { font-weight: 600; font-size: 1.05rem; display: flex; align-items: center; gap: 10px;}
        .cat-badge { background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 500; border: 1px solid #e2e8f0; }
        .cat-desc { color: var(--text-muted); font-size: 0.875rem; margin-top: 6px; line-height: 1.4; }
        .cat-meta { color: #94a3b8; font-size: 0.75rem; font-family: monospace; }
        
        .empty-state { text-align: center; padding: 40px 0; color: var(--text-muted); }
        .pagination-wrapper { margin-top: 20px; }

        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); opacity: 0; transition: opacity 0.3s ease; }
        .modal.show { display: flex; align-items: center; justify-content: center; opacity: 1; }
        .modal-content { background-color: var(--surface); padding: 30px; border-radius: var(--radius); width: 90%; max-width: 500px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); transform: translateY(-20px); transition: transform 0.3s ease; }
        .modal.show .modal-content { transform: translateY(0); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .modal-header h2 { margin: 0; font-size: 1.5rem; }
        .close { color: var(--text-muted); font-size: 24px; cursor: pointer; line-height: 1; transition: color 0.2s; }
        .close:hover { color: var(--error-text); }
    </style>

    <div class="cat-wrapper">
        <div class="cat-head">
            <h1 class="cat-title">Gestión de Marcas</h1>
        </div>

        @if(session('success'))
            <div class="alert alert-success">✅ {{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                ⚠️ <strong>Por favor corrige los siguientes errores:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="cat-card">
            {{-- Formulario Rápido --}}
            <form action="{{ route('inventario.marcas.store') }}" method="POST" class="cat-form">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nueva Marca <span style="color: red;">*</span></label>
                        <input type="text" name="nombre" class="cat-input" placeholder="Ej. Samsung, Nike..." value="{{ old('nombre') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Modelo</label>
                        <input type="text" name="modelo" class="cat-input" placeholder="Ej. Galaxy S23, Air Max..." value="{{ old('modelo') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="cat-textarea" placeholder="Breve descripción (opcional)">{{ old('descripcion') }}</textarea>
                </div>
                <button type="submit" class="cat-btn">+ Agregar</button>
            </form>

            {{-- Buscador en Tiempo Real --}}
            <div class="search-container">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" class="cat-input" placeholder="Buscar por marca, modelo o descripción..." style="padding-left: 35px;">
            </div>

            {{-- Lista de Marcas --}}
            <div id="marcasList">
                @forelse($marcas as $marca)
                    <div class="cat-item">
                        <div>
                            <div class="cat-name">
                                {{ $marca->nombre }}
                                @if($marca->modelo)
                                    <span class="cat-badge">Mod: {{ $marca->modelo }}</span>
                                @endif
                            </div>
                            @if($marca->descripcion)
                                <div class="cat-desc">{{ $marca->descripcion }}</div>
                            @endif
                        </div>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <span class="cat-meta">ID: {{ str_pad($marca->id, 4, '0', STR_PAD_LEFT) }}</span>
                            <button type="button" class="cat-btn-edit" onclick="openEditModal({{ $marca->id }}, '{{ addslashes($marca->nombre) }}', '{{ addslashes($marca->modelo ?? '') }}', '{{ addslashes($marca->descripcion ?? '') }}')">
                                Editar
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <p>No hay registros todavía.</p>
                    </div>
                @endforelse
            </div>

            {{-- Paginación Original --}}
            @if($marcas->hasPages())
                <div id="paginationWrapper" class="pagination-wrapper">
                    {{ $marcas->links() }}
                </div>
            @endif
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Editar Registro</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            
            <form id="editForm" action="#" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group" style="margin-bottom: 16px;">
                    <label for="editNombre" class="form-label">Nombre de la Marca <span style="color: red;">*</span></label>
                    <input type="text" id="editNombre" name="nombre" class="cat-input" required>
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label for="editModelo" class="form-label">Modelo</label>
                    <input type="text" id="editModelo" name="modelo" class="cat-input">
                </div>
                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="editDescripcion" class="form-label">Descripción</label>
                    <textarea id="editDescripcion" name="descripcion" class="cat-textarea"></textarea>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="cat-btn-edit" style="background: transparent; border: none; color: #64748b;" onclick="closeEditModal()">Cancelar</button>
                    <button type="submit" class="cat-btn" style="background: var(--brand);">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // --- LÓGICA DEL MODAL ---
        const modal = document.getElementById('editModal');

        // ¡Ojo! Añadimos el parámetro 'modelo' a la función
        function openEditModal(id, nombre, modelo, descripcion) {
            document.getElementById('editNombre').value = nombre;
            document.getElementById('editModelo').value = modelo;
            document.getElementById('editDescripcion').value = descripcion;
            
            document.getElementById('editForm').action = '/inventario/marcas/' + id;
            
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function closeEditModal() {
            modal.classList.remove('show');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape" && modal.classList.contains('show')) {
                closeEditModal();
            }
        });

        window.onclick = function(event) {
            if (event.target == modal) {
                closeEditModal();
            }
        }

        // --- LÓGICA DEL BUSCADOR EN TIEMPO REAL ---
        const searchInput = document.getElementById('searchInput');
        const marcasList = document.getElementById('marcasList');
        const paginationWrapper = document.getElementById('paginationWrapper');
        let searchTimeout = null;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout); 
            const query = this.value.trim();

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('inventario.marcas.index') }}?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    renderMarcas(data.items);
                    if(paginationWrapper) {
                        paginationWrapper.style.display = query.length > 0 ? 'none' : 'block';
                    }
                })
                .catch(error => console.error('Error en la búsqueda:', error));
            }, 300);
        });

        function renderMarcas(items) {
            marcasList.innerHTML = ''; 

            if (items.length === 0) {
                marcasList.innerHTML = '<div class="empty-state"><p>No se encontraron resultados para tu búsqueda.</p></div>';
                return;
            }

            let html = '';
            items.forEach(marca => {
                const desc = marca.descripcion ? `<div class="cat-desc">${marca.descripcion}</div>` : '';
                const badgeModelo = marca.modelo ? `<span class="cat-badge">Mod: ${marca.modelo}</span>` : '';
                const paddedId = String(marca.id).padStart(4, '0');
                
                const safeNombre = (marca.nombre || '').replace(/'/g, "\\'");
                const safeModelo = (marca.modelo || '').replace(/'/g, "\\'");
                const safeDesc = (marca.descripcion || '').replace(/'/g, "\\'");

                html += `
                <div class="cat-item">
                    <div>
                        <div class="cat-name">${marca.nombre} ${badgeModelo}</div>
                        ${desc}
                    </div>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <span class="cat-meta">ID: ${paddedId}</span>
                        <button type="button" class="cat-btn-edit" onclick="openEditModal(${marca.id}, '${safeNombre}', '${safeModelo}', '${safeDesc}')">
                            Editar
                        </button>
                    </div>
                </div>`;
            });

            marcasList.innerHTML = html;
        }
    </script>
</x-base-layout>