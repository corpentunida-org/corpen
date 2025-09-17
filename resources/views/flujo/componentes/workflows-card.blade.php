<div class="card-monday">
    <style>
        .card-monday {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            transition: 0.3s ease;
            margin-bottom: 20px;
        }
        .card-monday:hover { transform: translateY(-2px); }
        .card-title { font-size: 18px; font-weight: 600; color: #0073EA; margin-bottom: 15px; display: flex; align-items: center; }
        .card-title i { margin-right: 8px; }
        .toolbar { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .search-box { display: flex; align-items: center; background: #F0F3F7; border-radius: 8px; padding: 5px 10px; }
        .search-box input { border: none; outline: none; background: transparent; padding: 5px; }
        .search-box button { border: none; background: transparent; cursor: pointer; color: #0073EA; }
        .btn-monday { background: #0073EA; color: #fff; border-radius: 8px; padding: 8px 14px; text-decoration: none; transition: 0.3s; }
        .btn-monday:hover { background: #005bb5; }
        .table-monday { width: 100%; border-collapse: collapse; }
        .table-monday th, .table-monday td { padding: 12px; border-bottom: 1px solid #E0E0E0; text-align: left; }
        .table-monday tbody tr:hover { background: #F9FAFB; }
        .badge { display: inline-block; padding: 6px 10px; border-radius: 8px; color: #fff; font-size: 12px; margin-right: 5px; text-decoration: none; }
        .badge.blue { background: #0073EA; } .badge.green { background: #00C875; } .badge.red { background: #E2445C; }
    </style>

    <h2 class="card-title"><i class="fas fa-sitemap"></i> Gestión de Procesos</h2>
    <div class="toolbar">
        <form action="{{ route('flujo.workflows.index') }}" method="GET" class="search-box">
            <input type="text" name="search" placeholder="Buscar proceso..." value="{{ request('search') }}">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
        <a href="{{ route('flujo.workflows.create') }}" class="btn-monday"><i class="fas fa-plus"></i> Nuevo</a>
    </div>

    <table class="table-monday">
        <thead>
            <tr><th>ID</th><th>Nombre</th><th>Creador</th><th>Acciones</th></tr>
        </thead>
        <tbody>
            @foreach($workflows as $workflow)
            <tr>
                <td>{{ $workflow->id }}</td>
                <td>{{ $workflow->nombre }}</td>
                <td>{{ $workflow->creator->name ?? 'N/A' }}</td>
                <td>
                    <a href="{{ route('flujo.workflows.show', $workflow->id) }}" class="badge blue"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('flujo.workflows.edit', $workflow->id) }}" class="badge green"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('flujo.workflows.destroy', $workflow->id) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Eliminar?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="badge red"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div>{{ $workflows->links() }}</div>
</div>
