{{-- resources/views/flujo/componentes/comments-card.blade.php --}}
<div class="corporate-comments-card">
    <style>
        .corporate-comments-card { background: #fff; padding: 10px 5px; font-family: 'Inter', sans-serif; }
        
        /* Tabla Estilizada */
        .comment-table-minimal { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .comment-table-minimal th { 
            text-align: left; 
            padding: 12px 15px; 
            font-size: 0.65rem; 
            text-transform: uppercase; 
            letter-spacing: 0.05em; 
            color: #64748b; 
            border-bottom: 2px solid #f1f5f9;
        }
        .comment-table-minimal td { padding: 16px 15px; font-size: 0.85rem; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        .comment-table-minimal tbody tr:hover { background: #fcfdfe; }

        /* Estilo de Mensaje y Trazabilidad */
        .comment-text-wrapper { max-width: 450px; }
        .comment-main-text { display: block; color: #1e293b; line-height: 1.5; font-weight: 500; font-style: italic; }
        
        .trace-container { display: flex; align-items: center; gap: 8px; margin-top: 8px; flex-wrap: wrap; }
        
        .project-tag-mini {
            font-size: 0.65rem; font-weight: 700; color: #475569; background: #f1f5f9;
            padding: 2px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;
        }

        .comment-task-ref { 
            display: inline-flex; align-items: center; gap: 5px; 
            font-size: 0.75rem; color: #2563eb; text-decoration: none; font-weight: 600;
        }
        .comment-task-ref:hover { text-decoration: underline; }

        /* Usuario y Metadata */
        .user-info-group { display: flex; align-items: center; gap: 10px; }
        .user-avatar-init { 
            width: 32px; height: 32px; border-radius: 8px; 
            background: #f1f5f9; color: #475569; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 0.75rem; font-weight: 700; border: 1px solid #e2e8f0;
        }
        .timestamp-corp { font-size: 0.75rem; color: #94a3b8; white-space: nowrap; text-align: right; }
        
        .ref-tag { font-family: monospace; font-size: 0.7rem; color: #64748b; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-weight: 600; }
    </style>

    <table class="comment-table-minimal">
        <thead>
            <tr>
                <th width="90">Referencia</th>
                <th>Mensaje y Trazabilidad Operativa</th>
                <th>Remitente</th>
                <th width="160" class="text-right">Fecha de Registro</th>
            </tr>
        </thead>
        <tbody>
            @forelse($comments as $comment)
            <tr>
                {{-- ID de Comentario --}}
                <td><span class="ref-tag">#COM-{{ $comment->id }}</span></td>

                {{-- Comentario, Tarea y Proyecto --}}
                <td>
                    <div class="comment-text-wrapper">
                        <span class="comment-main-text">"{{ $comment->comentario }}"</span>
                        
                        <div class="trace-container">
                            {{-- Origen: Proyecto (Workflow) --}}
                            <span class="project-tag-mini" title="Proyecto / Workflow">
                                <i class="fas fa-layer-group"></i> 
                                {{ $comment->task->workflow->nombre ?? 'Sistema General' }}
                            </span>
                            
                            <i class="fas fa-chevron-right" style="font-size: 0.6rem; color: #cbd5e1;"></i>
                            
                            {{-- Destino: Tarea --}}
                            <a href="{{ route('flujo.tasks.show', $comment->task_id) }}" class="comment-task-ref">
                                <i class="fas fa-thumbtack"></i> 
                                {{ $comment->task->titulo ?? 'Unidad Desconocida' }}
                            </a>
                        </div>
                    </div>
                </td>

                {{-- Usuario --}}
                <td>
                    <div class="user-info-group">
                        <div class="user-avatar-init">{{ substr($comment->user->name ?? 'S', 0, 1) }}</div>
                        <div style="display: flex; flex-direction: column;">
                            <span style="font-weight: 600; color: #0f172a;">{{ $comment->user->name ?? 'Sistema' }}</span>
                            <span style="font-size: 0.65rem; color: #94a3b8; text-transform: lowercase;">{{ $comment->user->email ?? '' }}</span>
                        </div>
                    </div>
                </td>

                {{-- Fecha --}}
                <td class="timestamp-corp">
                    <div style="color: #1e293b; font-weight: 600;"><i class="far fa-clock"></i> {{ $comment->created_at->diffForHumans() }}</div>
                    <div style="font-size: 0.65rem; margin-top: 3px;">{{ $comment->created_at->format('d/m/Y H:i') }}</div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center; padding: 60px; color: #94a3b8; font-style: italic;">
                    <i class="fas fa-comments-slash" style="display: block; font-size: 2.5rem; margin-bottom: 15px; opacity: 0.1;"></i>
                    No se han registrado comunicaciones en el sistema de trazabilidad.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-wrap">
        {{ $comments->links() }}
    </div>
</div>