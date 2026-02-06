<x-base-layout>
    <style>
        .cat-wrapper { max-width: 800px; margin: 30px auto; font-family: 'Inter', sans-serif; color: #0f172a; }
        .cat-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .cat-title { font-size: 1.5rem; font-weight: 800; }
        
        .cat-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; }
        .cat-form { display: flex; flex-direction: column; gap: 10px; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #f1f5f9; }
        .cat-input { flex-grow: 1; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; }
        .cat-textarea { flex-grow: 1; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; min-height: 80px; resize: vertical; }
        .cat-btn { background: #000; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 700; }
        .cat-btn-edit { background: #3b82f6; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.875rem; }
        
        .cat-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
        .cat-name { font-weight: 600; }
        .cat-desc { color: #64748b; font-size: 0.875rem; margin-top: 4px; }
        .cat-actions { display: flex; gap: 8px; }
        
        /* Estilos para el modal */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #fefefe; margin: 10% auto; padding: 20px; border-radius: 12px; width: 80%; max-width: 500px; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { color: black; }
    </style>

    <div class="cat-wrapper">
        <div class="cat-head">
            <h1 class="cat-title">Gestión de Bodegas</h1>
        </div>

        <div class="cat-card">
            {{-- Formulario Rápido --}}
            <form action="{{ route('inventario.bodegas.store') }}" method="POST" class="cat-form">
                @csrf
                <input type="text" name="nombre" class="cat-input" placeholder="Nombre de la nueva bodega..." required>
                <textarea name="descripcion" class="cat-textarea" placeholder="Descripción de la bodega..." required></textarea>
                <button type="submit" class="cat-btn">Agregar</button>
            </form>

            {{-- Lista --}}
            <div>
                @foreach($bodegas as $bodega)
                <div class="cat-item">
                    <div>
                        <div class="cat-name">{{ $bodega->nombre }}</div>
                        @if($bodega->descripcion)
                        <div class="cat-desc">{{ $bodega->descripcion }}</div>
                        @endif
                    </div>
                    <div class="cat-actions">
                        <span style="color: #cbd5e1; margin-right: 8px;">ID: {{ $bodega->id }}</span>
                        <button class="cat-btn-edit" onclick="openEditModal({{ $bodega->id }}, '{{ $bodega->nombre }}', '{{ $bodega->descripcion ?? '' }}')">Editar</button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal de Edición -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Editar Bodega</h2>
            <form id="editForm" action="#" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="editId" name="id">
                <div style="margin-bottom: 15px;">
                    <label for="editNombre">Nombre:</label>
                    <input type="text" id="editNombre" name="nombre" class="cat-input" style="width: 100%; margin-top: 5px;" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="editDescripcion">Descripción:</label>
                    <textarea id="editDescripcion" name="descripcion" class="cat-textarea" style="width: 100%; margin-top: 5px;" required></textarea>
                </div>
                <button type="submit" class="cat-btn">Actualizar</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, nombre, descripcion) {
            document.getElementById('editId').value = id;
            document.getElementById('editNombre').value = nombre;
            document.getElementById('editDescripcion').value = descripcion;
            
            // Establecer la acción del formulario
            document.getElementById('editForm').action = '/inventario/bodegas/' + id;
            
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Cerrar el modal si el usuario hace clic fuera de él
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</x-base-layout>