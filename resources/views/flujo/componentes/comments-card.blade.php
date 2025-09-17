<div class="card-monday">
    <style>
        .card-monday{background:#fff;border-radius:12px;padding:20px;box-shadow:0 4px 10px rgba(0,0,0,0.05);transition:0.3s;margin-bottom:20px;}
        .card-monday:hover{transform:translateY(-2px);}
        .card-title{font-size:18px;font-weight:600;color:#0073EA;margin-bottom:15px;display:flex;align-items:center;}
        .card-title i{margin-right:8px;}
        .table-monday{width:100%;border-collapse:collapse;}
        .table-monday th,.table-monday td{padding:12px;border-bottom:1px solid #E0E0E0;text-align:left;}
        .table-monday tbody tr:hover{background:#F9FAFB;}
    </style>

    <h2 class="card-title"><i class="fas fa-comments"></i> Novedades y Comentarios</h2>
    <table class="table-monday">
        <thead>
            <tr><th>ID</th><th>ID T</th><th>Tarea</th><th>Comentario</th><th>Usuario</th><th>Fecha</th></tr>
        </thead>
        <tbody>
            @foreach($comments as $comment)
            <tr>
                <td>{{ $comment->id }}</td>
                <td>{{ $comment->task_id }}</td>
                <td>{{ $comment->task->titulo ?? 'N/A' }}</td>
                <td>{{ $comment->comentario }}</td>
                <td>{{ $comment->user->name ?? 'N/A' }}</td>
                <td>{{ $comment->created_at ? $comment->created_at->format('d/m/Y H:i') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div>{{ $comments->links() }}</div>
</div>
