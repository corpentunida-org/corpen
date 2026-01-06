<x-base-layout>
    <div class="task-show-wrapper">
        {{-- Header Ejecutivo con Acciones --}}
        <header class="show-header">
            <div class="header-left">
                <a href="{{ route('flujo.tasks.index') }}" class="btn-back-circle" title="Volver al listado">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <div>
                    <span class="system-tag">Expediente de Unidad de Trabajo</span>
                    <h1 class="main-title">{{ $task->titulo }}</h1>
                    <p class="main-subtitle">Registro integral de ejecución, trazabilidad y feedback colaborativo.</p>
                </div>
            </div>
            <div class="header-right">
                <a href="{{ route('flujo.tasks.edit', $task) }}" class="btn-corporate-edit">
                    <i class="fas fa-pen"></i> Gestionar Unidad
                </a>
            </div>
        </header>

        <div class="show-grid">
            {{-- Columna Principal: Información y Discusión --}}
            <main class="main-column">
                {{-- Bloque: Especificaciones --}}
                <section class="card-corp mb-4">
                    <h2 class="card-corp-title"><i class="fas fa-align-left"></i> Especificaciones Técnicas</h2>
                    <div class="content-area">
                        <p class="description-text">{{ $task->descripcion ?: 'No se han registrado especificaciones detalladas para esta unidad.' }}</p>
                    </div>
                </section>

                {{-- Bloque: Canal de Comunicación (Relación: Comments) --}}
                <section class="card-corp">
                    <h2 class="card-corp-title"><i class="fas fa-comments"></i> Canal de Feedback</h2>
                    <div class="comment-thread">
                        @forelse($task->comments->sortByDesc('created_at') as $comment)
                            <div class="comment-item">
                                <div class="comment-avatar">{{ substr($comment->user->name ?? 'S', 0, 1) }}</div>
                                <div class="comment-content">
                                    <div class="comment-meta">
                                        <span class="user-name">{{ $comment->user->name ?? 'Sistema' }}</span>
                                        <span class="date-stamp">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="comment-text">{{ $comment->comentario }}</p>

                                    {{-- LÓGICA DE SOPORTES AGREGADA --}}
                                    @if($comment->soporte)
                                        @php
                                            $extension = pathinfo($comment->soporte, PATHINFO_EXTENSION);
                                            $esImagen = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                            $urlS3 = Storage::disk('s3')->temporaryUrl($comment->soporte, now()->addMinutes(30));
                                        @endphp
                                        <div class="comment-attachment-wrapper mt-3">
                                            @if($esImagen)
                                                <a href="{{ $urlS3 }}" target="_blank" class="attachment-preview-link">
                                                    <img src="{{ $urlS3 }}" alt="Soporte" class="img-fluid rounded border">
                                                </a>
                                            @endif
                                            <div class="attachment-action-bar">
                                                <span class="file-info-label">
                                                    <i class="fas {{ $esImagen ? 'fa-image' : 'fa-file-pdf' }}"></i> 
                                                    Documento de Soporte (.{{ strtoupper($extension) }})
                                                </span>
                                                <a href="{{ $urlS3 }}" target="_blank" class="btn-download-soporte">
                                                    <i class="fas fa-external-link-alt"></i> Abrir Soporte
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="empty-state-simple">
                                <p>No existen registros de comunicación en esta unidad.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </main>

            {{-- Columna Lateral: Atributos y Auditoría --}}
            <aside class="side-column">
                {{-- Bloque: Atributos Operativos --}}
                <section class="card-corp mb-4">
                    <h2 class="card-corp-title"><i class="fas fa-info-circle"></i> Atributos Operativos</h2>
                    <div class="attribute-list">
                        <div class="attr-item">
                            <label>Estado Actual</label>
                            <span class="badge-status-corp st-{{ strtolower($task->estado) }}">
                                {{ str_replace('_', ' ', ucfirst($task->estado)) }}
                            </span>
                        </div>
                        <div class="attr-item">
                            <label>Prioridad Asignada</label>
                            <p class="prio-text prio-{{ strtolower($task->prioridad) }}">
                                <i class="fas fa-circle"></i> {{ ucfirst($task->prioridad) }}
                            </p>
                        </div>
                        <div class="attr-item">
                            <label>Responsable Ejecutivo</label>
                            <p class="val-text"><i class="far fa-user-circle"></i> {{ $task->user->name ?? 'Sin asignar' }}</p>
                        </div>
                        <div class="attr-item">
                            <label>Workflow de Origen</label>
                            <p class="val-text"><i class="fas fa-project-diagram"></i> {{ $task->workflow->nombre ?? 'N/A' }}</p>
                        </div>
                        <div class="attr-item">
                            <label>Fecha Límite</label>
                            <p class="val-text"><i class="far fa-calendar-times"></i> {{ $task->fecha_limite ? $task->fecha_limite->format('d M, Y') : 'Sin definir' }}</p>
                        </div>
                    </div>
                </section>

                {{-- Bloque: Historial de Auditoría (Relación: Histories) --}}
                <section class="card-corp">
                    <h2 class="card-corp-title"><i class="fas fa-history"></i> Log de Auditoría</h2>
                    <div class="audit-log">
                        @forelse($task->histories->sortByDesc('created_at') as $history)
                            <div class="log-item">
                                <span class="log-time">{{ $history->created_at->format('d/m H:i') }}</span>
                                <p class="log-desc">
                                    <strong>{{ $history->user->name ?? 'User' }}</strong> transicionó de 
                                    <span class="state-label">{{ $history->estado_anterior }}</span> a 
                                    <span class="state-label">{{ $history->estado_nuevo }}</span>
                                </p>
                            </div>
                        @empty
                            <p class="empty-log">Sin cambios de estado registrados.</p>
                        @endforelse
                    </div>
                </section>

                {{-- Metadatos del Registro --}}
                <div class="meta-footer">
                    <p>Creado: {{ $task->created_at->format('d/m/Y H:i') }}</p>
                    <p>Última actualización: {{ $task->updated_at->format('d/m/Y H:i') }}</p>
                    <p>ID Referencia: UUID-{{ $task->id }}</p>
                </div>
            </aside>
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        :root {
            --corp-dark: #0f172a;
            --corp-blue: #2563eb;
            --corp-border: #f1f5f9;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg-page: #fafafa;
        }

        body { background-color: var(--bg-page); font-family: 'Inter', sans-serif; color: var(--text-main); }
        .task-show-wrapper { max-width: 1200px; margin: 40px auto; padding: 0 24px; }

        /* HEADER */
        .show-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .header-left { display: flex; align-items: center; gap: 20px; }
        .btn-back-circle { 
            width: 40px; height: 40px; border-radius: 50%; background: #fff; border: 1px solid var(--corp-border);
            display: flex; align-items: center; justify-content: center; color: var(--text-muted); text-decoration: none; transition: 0.2s;
        }
        .btn-back-circle:hover { background: var(--corp-dark); color: #fff; }
        .system-tag { font-size: 0.65rem; font-weight: 800; color: var(--corp-blue); text-transform: uppercase; letter-spacing: 0.1em; display: block; margin-bottom: 4px; }
        .main-title { font-size: 2.25rem; font-weight: 800; letter-spacing: -0.04em; margin: 0; color: var(--corp-dark); }
        .main-subtitle { font-size: 0.95rem; color: var(--text-muted); margin-top: 4px; }

        .btn-corporate-edit {
            background: var(--corp-dark); color: #fff; padding: 12px 24px; border-radius: 10px;
            font-weight: 600; font-size: 0.85rem; text-decoration: none; display: flex; align-items: center; gap: 10px; transition: 0.2s;
        }
        .btn-corporate-edit:hover { background: #000; transform: translateY(-1px); }

        /* GRID LAYOUT */
        .show-grid { display: grid; grid-template-columns: 1.8fr 1fr; gap: 30px; align-items: start; }
        @media (max-width: 992px) { .show-grid { grid-template-columns: 1fr; } }

        .card-corp { background: #fff; border-radius: 16px; border: 1px solid var(--corp-border); padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .card-corp-title { font-size: 0.95rem; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; color: var(--corp-dark); border-bottom: 1px solid var(--corp-border); padding-bottom: 12px; }
        .mb-4 { margin-bottom: 24px; }

        /* CONTENT AREA */
        .description-text { font-size: 0.95rem; line-height: 1.6; color: var(--text-main); }

        /* COMMENTS & ATTACHMENTS */
        .comment-thread { display: flex; flex-direction: column; gap: 20px; }
        .comment-item { display: flex; gap: 15px; }
        .comment-avatar { width: 32px; height: 32px; background: #e0e7ff; color: #4338ca; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800; flex-shrink: 0; }
        .comment-meta { display: flex; justify-content: space-between; margin-bottom: 4px; }
        .user-name { font-size: 0.85rem; font-weight: 700; }
        .date-stamp { font-size: 0.7rem; color: var(--text-muted); }
        .comment-text { font-size: 0.9rem; line-height: 1.5; color: var(--text-main); }

        /* SOPORTE STYLES */
        .comment-attachment-wrapper { background: #f8fafc; border: 1px solid var(--corp-border); border-radius: 12px; padding: 12px; }
        .attachment-preview-link { display: block; max-width: 200px; margin-bottom: 10px; }
        .attachment-action-bar { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        .file-info-label { font-size: 0.75rem; color: var(--text-muted); font-weight: 600; display: flex; align-items: center; gap: 6px; }
        .btn-download-soporte { font-size: 0.75rem; color: var(--corp-blue); text-decoration: none; font-weight: 700; border: 1px solid var(--corp-blue); padding: 4px 10px; border-radius: 6px; transition: 0.2s; }
        .btn-download-soporte:hover { background: var(--corp-blue); color: #fff; }

        /* ATTRIBUTES */
        .attribute-list { display: flex; flex-direction: column; gap: 16px; }
        .attr-item label { display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px; letter-spacing: 0.05em; }
        .val-text { font-size: 0.9rem; font-weight: 500; display: flex; align-items: center; gap: 8px; }

        .badge-status-corp { padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; display: inline-block; }
        .st-pendiente { background: #fef3c7; color: #b45309; }
        .st-en_proceso { background: #e0f2fe; color: #0369a1; }
        .st-completado { background: #dcfce7; color: #15803d; }

        .prio-text { font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .prio-alta { color: #ef4444; }
        .prio-media { color: #f59e0b; }
        .prio-baja { color: #10b981; }

        /* AUDIT LOG */
        .audit-log { display: flex; flex-direction: column; gap: 12px; }
        .log-item { font-size: 0.8rem; border-left: 2px solid var(--corp-border); padding-left: 15px; }
        .log-time { font-size: 0.7rem; color: var(--text-muted); font-weight: 600; }
        .log-desc { margin-top: 2px; }
        .state-label { font-family: monospace; padding: 2px 4px; background: #f1f5f9; border-radius: 4px; font-size: 0.75rem; }

        .meta-footer { margin-top: 30px; padding: 20px; border-top: 1px dashed var(--corp-border); font-size: 0.75rem; color: var(--text-muted); line-height: 1.8; }
        .empty-state-simple { text-align: center; padding: 20px; color: var(--text-muted); font-size: 0.85rem; font-style: italic; }
    </style>
</x-base-layout>