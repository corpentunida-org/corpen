<x-base-layout>
    <style>
        .cat-wrapper { max-width: 800px; margin: 30px auto; font-family: 'Inter', sans-serif; color: #0f172a; }
        .cat-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .cat-title { font-size: 1.5rem; font-weight: 800; }
        
        .cat-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; }
        .cat-form { display: flex; gap: 10px; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #f1f5f9; }
        .cat-input { flex-grow: 1; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; }
        .cat-btn { background: #000; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 700; }
        
        .cat-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
        .cat-name { font-weight: 600; }
    </style>

    <div class="cat-wrapper">
        <div class="cat-head">
            <h1 class="cat-title">Gestión de Bodegas</h1>
        </div>

        <div class="cat-card">
            {{-- Formulario Rápido --}}
            <form action="{{ route('inventario.marcas.store') }}" method="POST" class="cat-form">
                @csrf
                <input type="text" name="nombre" class="cat-input" placeholder="Nombre de la nueva marca..." required>
                <button type="submit" class="cat-btn">Agregar</button>
            </form>

            {{-- Lista --}}
            <div>
                @foreach($marcas as $marca)
                <div class="cat-item">
                    <span class="cat-name">{{ $marca->nombre }}</span>
                    <span style="color: #cbd5e1;">ID: {{ $marca->id }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-base-layout>