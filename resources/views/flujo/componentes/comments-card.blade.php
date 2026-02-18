{{-- resources/views/flujo/componentes/comments-card.blade.php --}}
<style>
    .corporate-comments-card {
        background: #fff;
        padding: 20px;
        font-family: 'Inter', sans-serif;
        border-radius: 16px;
    }

    /* Tabla Desktop */
    .comment-table-minimal {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .comment-table-minimal th {
        text-align: left;
        padding: 12px 15px;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #64748b;
        border-bottom: 2px solid #f1f5f9;
        font-weight: 800;
    }

    .comment-table-minimal td {
        padding: 18px 15px;
        font-size: 0.85rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: top;
    }

    .comment-table-minimal tbody tr:hover {
        background: #fcfdfe;
    }

    /* Contenido del Mensaje */
    .comment-text-wrapper {
        max-width: 500px;
    }

    .trace-container {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .project-tag-mini {
        font-size: 0.65rem;
        font-weight: 700;
        color: #475569;
        background: #f1f5f9;
        padding: 3px 10px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid #e2e8f0;
    }

    .comment-task-ref {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.75rem;
        color: #4f46e5;
        text-decoration: none;
        font-weight: 700;
    }

    .comment-task-ref:hover {
        text-decoration: underline;
    }

    /* Usuario */
    .user-info-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar-init {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: #eef2ff;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 800;
        border: 1px solid #e0e7ff;
    }

    .timestamp-corp {
        font-size: 0.75rem;
        color: #94a3b8;
        white-space: nowrap;
        text-align: right;
    }

    .ref-tag {
        font-family: 'Outfit';
        font-size: 0.7rem;
        color: #64748b;
        background: #f8fafc;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 700;
        border: 1px solid #f1f5f9;
    }

    /* --- MÓVIL (Adaptive Timeline) --- */
    .mobile-comments-view {
        display: none;
        flex-direction: column;
        gap: 16px;
    }

    @media (max-width: 900px) {
        .comment-table-minimal {
            display: none;
        }

        .mobile-comments-view {
            display: flex;
        }

        .corporate-comments-card {
            padding: 15px 10px;
        }

        .comment-mobile-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px;
            position: relative;
            transition: 0.2s;
        }

        .m-comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .m-user-block {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .m-user-name {
            font-size: 0.85rem;
            font-weight: 700;
            color: #0f172a;
        }

        .m-time {
            font-size: 0.7rem;
            color: #94a3b8;
        }

        .m-comment-body {
            padding: 12px;
            background: #f8fafc;
            border-radius: 12px;
            margin-bottom: 12px;
            border-left: 3px solid #4f46e5;
        }

        .m-comment-text {
            font-size: 0.9rem;
            color: #334155;
            line-height: 1.5;
            font-style: italic;
        }

        .m-trace-footer {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .m-ref-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 4px;
        }
    }
</style>
<div class="corporate-comments-card">
    {{-- Vista Desktop (Tabla) --}}
    <table class="comment-table-minimal">
        <thead>
            <tr>
                <th width="100">Referencia</th>
                <th>Mensaje y Trazabilidad Operativa</th>
                <th>Remitente</th>
                <th width="180" class="text-right">Fecha de Registro</th>
            </tr>
        </thead>
        <tbody>
            @forelse($comments as $comment)
                <tr>
                    <td><span class="ref-tag">#COM-{{ $comment->id }}</span></td>
                    <td>
                        <div class="comment-text-wrapper">
                            <span class="comment-main-text">"{{ $comment->comentario }}"</span>
                            <div class="trace-container mt-1">
                                <span class="project-tag-mini" title="Proyecto / Workflow">
                                    <i class="fas fa-layer-group"></i>
                                    {{ $comment->task->workflow->nombre ?? 'Sistema General' }}
                                </span>
                                <i class="fas fa-chevron-right" style="font-size: 0.6rem; color: #cbd5e1;"></i>
                                <a href="{{ route('flujo.tasks.show', $comment->task_id) }}" class="comment-task-ref">
                                    <i class="fas fa-thumbtack"></i>
                                    {{ $comment->task->titulo ?? 'Unidad Desconocida' }}
                                </a>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="user-info-group">
                            <div class="user-avatar-init">{{ substr($comment->user->name ?? 'S', 0, 1) }}</div>
                            <div style="display: flex; flex-direction: column;">
                                <span
                                    style="font-weight: 700; color: #0f172a; font-size: 0.85rem;">{{ $comment->user->name ?? 'Sistema' }}</span>
                                <span
                                    style="font-size: 0.65rem; color: #64748b; text-transform: lowercase;">{{ $comment->user->email ?? '' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="timestamp-corp">
                        <div style="color: #0f172a; font-weight: 700;"><i class="far fa-clock"></i>
                            {{ $comment->created_at->diffForHumans() }}</div>
                        <div style="font-size: 0.65rem; margin-top: 4px; font-weight: 500;">
                            {{ $comment->created_at->format('d/m/Y H:i') }}</div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center; padding: 80px; color: #94a3b8;">
                        <i class="far fa-comment-dots"
                            style="display: block; font-size: 3rem; margin-bottom: 20px; opacity: 0.1;"></i>
                        No se han registrado comunicaciones en el sistema.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Vista Móvil (Timeline Cards) --}}
    <div class="mobile-comments-view">
        @forelse($comments as $comment)
            <div class="comment-mobile-card">
                <div class="m-comment-header">
                    <div class="m-user-block">
                        <div class="user-avatar-init" style="width: 28px; height: 28px; font-size: 0.65rem;">
                            {{ substr($comment->user->name ?? 'S', 0, 1) }}</div>
                        <div>
                            <div class="m-user-name">{{ $comment->user->name ?? 'Sistema' }}</div>
                            <div class="m-time">{{ $comment->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    <span class="ref-tag">#{{ $comment->id }}</span>
                </div>

                <div class="m-comment-body">
                    <div class="m-comment-text">"{{ $comment->comentario }}"</div>
                </div>

                <div class="m-trace-footer">
                    <span class="project-tag-mini">
                        <i class="fas fa-layer-group"></i>
                        {{ Str::limit($comment->task->workflow->nombre ?? 'Sistema', 25) }}
                    </span>
                    <div class="m-ref-row">
                        <a href="{{ route('flujo.tasks.show', $comment->task_id) }}" class="comment-task-ref">
                            <i class="fas fa-thumbtack"></i>
                            {{ Str::limit($comment->task->titulo ?? 'Unidad', 20) }}
                        </a>
                        <span class="m-time"
                            style="font-size: 0.6rem;">{{ $comment->created_at->format('d/m/y H:i') }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 40px; color: #94a3b8;">
                Sin comentarios registrados.
            </div>
        @endforelse
    </div>

    <div class="pagination-wrap">
        {{ $comments->links() }}
    </div>
</div>
