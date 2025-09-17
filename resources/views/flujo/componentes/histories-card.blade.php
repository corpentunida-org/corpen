<div class="card-monday">
    <style>
        .card-monday{background:#fff;border-radius:12px;padding:20px;box-shadow:0 4px 10px rgba(0,0,0,0.05);transition:0.3s;margin-bottom:20px;}
        .card-monday:hover{transform:translateY(-2px);}
        .card-title{font-size:18px;font-weight:600;color:#0073EA;margin-bottom:15px;display:flex;align-items:center;}
        .card-title i{margin-right:8px;}
        .table-monday{width:100%;border-collapse:collapse;}
        .table-monday th,.table-monday td{padding:12px;border-bottom:1px solid #E0E0E0;text-align:left;}
        .table-monday tbody tr:hover{background:#F9FAFB;}
        .badge{display:inline-block;padding:6px 10px;border-radius:8px;color:#fff;font-size:12px;text-decoration:none;}
        .badge.blue{background:#0073EA;}.badge.green{background:#00C875;}.badge.red{background:#E2445C;}
    </style>

    <h2 class="card-title"><i class="fas fa-history"></i> Auditoría de Tareas</h2>
    <table class="table-monday">
        <thead>
            <tr><th>ID</th><th>Tarea</th><th>Nuevo Estado</th><th>Registrado Por</th><th>Fecha y Hora</th></tr>
        </thead>
        <tbody>
            @foreach($histories as $history)
            <tr>
                <td>{{ $history->id }}</td>
                <td>{{ $history->task->titulo ?? 'N/A' }}</td>
                <td><span class="badge {{ $history->estado_nuevo == 'completado' ? 'green' : ($history->estado_nuevo == 'pendiente' ? 'blue' : 'red') }}">{{ $history->estado_nuevo ?? '-' }}</span></td>
                <td>{{ $history->user->name ?? 'N/A' }}</td>
                <td>{{ $history->fecha_cambio ? \Carbon\Carbon::parse($history->fecha_cambio)->format('d/m/Y H:i') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div>{{ $histories->links() }}</div>
</div>
