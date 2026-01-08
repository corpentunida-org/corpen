<x-base-layout>
    <div class="task-show-wrapper">
        {{-- Header con Trazabilidad --}}
        <header class="show-header">
            <div class="header-left">
                <a href="{{ route('flujo.tasks.index') }}" class="btn-back-circle" title="Volver al listado">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="title-container">
                    <nav class="breadcrumb-simple">
                        <span class="system-tag">Operaciones</span> / 
                        <span class="system-tag-id">#{{ $task->id }}</span>
                    </nav>
                    <h1 class="main-title">{{ $task->titulo }}</h1>
                    <div class="header-meta-info">
                        <span class="badge-status-pill st-{{ strtolower($task->estado) }}">
                            <i class="fas fa-sync-alt fa-spin-slow"></i> {{ str_replace('_', ' ', ucfirst($task->estado)) }}
                        </span>
                        <span class="divider">|</span>
                        <span class="meta-date"><i class="far fa-calendar"></i> Creado {{ $task->created_at->format('d M, Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="header-right">
                <a href="{{ route('flujo.tasks.edit', $task) }}" class="btn-corporate-primary">
                    <i class="fas fa-sliders-h"></i> <span>Gestionar Unidad</span>
                </a>
            </div>
        </header>

        {{-- INTEGRACIÓN: Banner Resaltado de Contexto (Workflow/Proyecto) --}}
        <div class="workflow-highlight-banner">
            <div class="wf-content">
                <div class="wf-icon-box">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <div class="wf-text-group">
                    <span class="wf-overtitle">Proyecto / Workflow Vinculado</span>
                    <h2 class="wf-main-name">{{ $task->workflow->nombre ?? 'Sin Workflow Asignado' }}</h2>
                </div>
            </div>
            @if($task->workflow)
                <a href="{{ route('flujo.workflows.show', $task->workflow->id) }}" class="btn-wf-link">
                    Ir al Proyecto <i class="fas fa-chevron-right"></i>
                </a>
            @endif
        </div>

        <div class="show-grid">
            {{-- Columna Principal --}}
            <main class="main-column">
                {{-- Sección: Descripción con mejor tipografía --}}
                <section class="glass-card mb-4">
                    <div class="section-header">
                        <h2 class="section-title"><i class="fas fa-file-alt"></i> Especificaciones</h2>
                    </div>
                    <div class="description-content">
                        {{ $task->descripcion ?: 'No hay descripción técnica registrada para esta unidad.' }}
                    </div>
                </section>

                {{-- Sección: Feed de Actividad y Comentarios --}}
                <section class="glass-card">
                    <div class="section-header space-between">
                        <h2 class="section-title"><i class="fas fa-comments"></i> Línea de Tiempo y Feedback</h2>
                        <span class="comment-count">{{ $task->comments->count() }} Mensajes</span>
                    </div>
                    
                    <div class="comment-timeline">
                        @forelse($task->comments->sortByDesc('created_at') as $comment)
                            <div class="comment-bubble-wrapper">
                                <div class="comment-avatar-container">
                                    <div class="avatar-box">{{ substr($comment->user->name ?? 'S', 0, 1) }}</div>
                                    <div class="timeline-line"></div>
                                </div>
                                <div class="comment-body">
                                    <div class="comment-header">
                                        <span class="user-name">{{ $comment->user->name ?? 'Sistema' }}</span>
                                        <span class="time-ago"><i class="far fa-clock"></i> {{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="comment-text-box">
                                        <p>{{ $comment->comentario }}</p>
                                    </div>

                                    @if($comment->soporte)
                                        @php
                                            $extension = pathinfo($comment->soporte, PATHINFO_EXTENSION);
                                            $esImagen = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                            $urlS3 = Storage::disk('s3')->temporaryUrl($comment->soporte, now()->addMinutes(30));
                                        @endphp
                                        <div class="attachment-card">
                                            @if($esImagen)
                                                <div class="attachment-image-preview" style="background-image: url('{{ $urlS3 }}')">
                                                    <a href="{{ $urlS3 }}" target="_blank" class="preview-overlay"><i class="fas fa-search-plus"></i></a>
                                                </div>
                                            @endif
                                            <div class="attachment-info">
                                                <div class="file-type-icon {{ $esImagen ? 'is-img' : 'is-pdf' }}">
                                                    <i class="fas {{ $esImagen ? 'fa-image' : 'fa-file-pdf' }}"></i>
                                                </div>
                                                <div class="file-details">
                                                    <span class="file-name">Documento de Soporte</span>
                                                    <span class="file-meta">Formato: .{{ strtoupper($extension) }}</span>
                                                </div>
                                                <a href="{{ $urlS3 }}" target="_blank" class="btn-open-file">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="empty-feed">
                                <i class="fas fa-comment-slash"></i>
                                <p>Aún no hay feedback registrado.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </main>

            {{-- Columna Lateral --}}
            <aside class="side-column">
                <section class="glass-card mb-4 highlight-border">
                    <h2 class="section-title"><i class="fas fa-fingerprint"></i> Estatus Operativo</h2>
                    <div class="status-panel">
                        <div class="status-item">
                            <span class="label">Prioridad</span>
                            <div class="priority-box p-{{ strtolower($task->prioridad) }}">
                                <span class="p-dot"></span>
                                <span class="p-label">{{ ucfirst($task->prioridad) }}</span>
                            </div>
                        </div>
                        
                        <div class="data-grid-side">
                            <div class="data-row">
                                <i class="fas fa-user-tie icon-val"></i>
                                <div>
                                    <span class="data-label">Responsable</span>
                                    <span class="data-value">{{ $task->user->name ?? 'Sin asignar' }}</span>
                                </div>
                            </div>
                            <div class="data-row">
                                <i class="fas fa-project-diagram icon-val"></i>
                                <div>
                                    <span class="data-label">Workflow</span>
                                    <span class="data-value">{{ $task->workflow->nombre ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="data-row {{ $task->fecha_limite && $task->fecha_limite->isPast() ? 'alert-danger' : '' }}">
                                <i class="fas fa-calendar-check icon-val"></i>
                                <div>
                                    <span class="data-label">Vencimiento</span>
                                    <span class="data-value">{{ $task->fecha_limite ? $task->fecha_limite->format('d M, Y') : 'Indefinido' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="glass-card">
                    <h2 class="section-title"><i class="fas fa-stream"></i> Auditoría de Estados</h2>
                    <div class="audit-timeline">
                        @forelse($task->histories->sortByDesc('created_at')->take(5) as $history)
                            <div class="audit-node">
                                <div class="node-circle"></div>
                                <div class="node-content">
                                    <span class="node-date">{{ $history->created_at->diffForHumans() }}</span>
                                    <p class="node-text"><strong>{{ explode(' ', $history->user->name ?? 'User')[0] }}</strong> cambió a <span class="node-status">{{ $history->estado_nuevo }}</span></p>
                                </div>
                            </div>
                        @empty
                            <p class="empty-txt-small">Sin historial de cambios.</p>
                        @endforelse
                    </div>
                </section>
                
                <div class="system-footprint">
                    <p><i class="fas fa-code-branch"></i> ID: UUID-{{ substr($task->id, 0, 8) }}</p>
                    <p><i class="fas fa-history"></i> Actualizado: {{ $task->updated_at->format('d/m H:i') }}</p>
                </div>
            </aside>
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        :root {
            --primary: #2563eb;
            --dark: #0f172a;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-500: #64748b;
            --slate-900: #0f172a;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        body { background-color: #f4f7fa; font-family: 'Inter', sans-serif; color: var(--slate-900); }
        .task-show-wrapper { max-width: 1200px; margin: 30px auto; padding: 0 20px; }

        /* HEADER UI */
        .show-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .header-left { display: flex; align-items: center; gap: 20px; }
        .btn-back-circle { width: 42px; height: 42px; border-radius: 12px; background: white; border: 1px solid var(--slate-200); display: flex; align-items: center; justify-content: center; color: var(--slate-500); transition: all 0.2s; text-decoration: none; }
        .btn-back-circle:hover { background: var(--dark); color: white; transform: translateX(-3px); }
        
        .breadcrumb-simple { font-size: 0.75rem; font-weight: 700; color: var(--slate-500); margin-bottom: 5px; }
        .system-tag { color: var(--primary); text-transform: uppercase; }
        .main-title { font-size: 1.85rem; font-weight: 800; letter-spacing: -0.03em; color: var(--slate-900); margin: 0; line-height: 1.1; }
        
        .header-meta-info { display: flex; align-items: center; gap: 12px; margin-top: 10px; }
        .badge-status-pill { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; border: 1px solid rgba(0,0,0,0.05); display: flex; align-items: center; gap: 6px; }
        .st-pendiente { background: #fffbeb; color: #b45309; }
        .st-en_proceso { background: #eff6ff; color: #1e40af; }
        .st-completado { background: #f0fdf4; color: #166534; }
        .meta-date { font-size: 0.8rem; color: var(--slate-500); font-weight: 500; }

        .btn-corporate-primary { background: var(--dark); color: white; padding: 12px 20px; border-radius: 12px; font-weight: 600; font-size: 0.85rem; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: 0.3s; }
        .btn-corporate-primary:hover { background: var(--primary); box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2); transform: translateY(-2px); }

        /* ESTILOS DEL BANNER DE WORKFLOW (NUEVO) */
        .workflow-highlight-banner {
            background: white;
            border-radius: 18px;
            border: 1px solid var(--slate-200);
            border-left: 6px solid var(--primary);
            padding: 18px 25px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .wf-content { display: flex; align-items: center; gap: 20px; }
        .wf-icon-box { width: 45px; height: 45px; background: #eff6ff; color: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
        .wf-text-group { display: flex; flex-direction: column; }
        .wf-overtitle { font-size: 0.65rem; font-weight: 800; color: var(--slate-500); text-transform: uppercase; letter-spacing: 0.05em; }
        .wf-main-name { font-size: 1.15rem; font-weight: 800; color: var(--dark); margin: 0; }
        .btn-wf-link { background: #f8fafc; border: 1px solid var(--slate-200); padding: 8px 16px; border-radius: 10px; font-size: 0.8rem; font-weight: 700; color: var(--slate-700); text-decoration: none; transition: 0.2s; }
        .btn-wf-link:hover { background: var(--primary); color: white; border-color: var(--primary); }

        /* LAYOUT */
        .show-grid { display: grid; grid-template-columns: 1.8fr 1fr; gap: 25px; }
        @media (max-width: 992px) { .show-grid { grid-template-columns: 1fr; } .workflow-highlight-banner { flex-direction: column; align-items: flex-start; gap: 15px; } .btn-wf-link { width: 100%; text-align: center; justify-content: center; } }

        .glass-card { background: white; border-radius: 20px; border: 1px solid var(--slate-200); padding: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .section-header { margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--slate-100); display: flex; align-items: center; }
        .section-header.space-between { justify-content: space-between; }
        .section-title { font-size: 0.95rem; font-weight: 700; display: flex; align-items: center; gap: 10px; color: var(--slate-900); margin: 0; }
        .section-title i { color: var(--primary); }

        /* COMMENTS TIMELINE */
        .comment-timeline { display: flex; flex-direction: column; gap: 5px; }
        .comment-bubble-wrapper { display: flex; gap: 15px; }
        .comment-avatar-container { display: flex; flex-direction: column; align-items: center; width: 35px; }
        .avatar-box { width: 35px; height: 35px; background: var(--slate-100); color: var(--primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 800; border: 1px solid var(--slate-200); }
        .timeline-line { width: 2px; flex-grow: 1; background: var(--slate-100); margin: 5px 0; }
        
        .comment-body { flex: 1; padding-bottom: 25px; }
        .comment-header { display: flex; justify-content: space-between; margin-bottom: 6px; }
        .user-name { font-size: 0.85rem; font-weight: 700; color: var(--slate-900); }
        .time-ago { font-size: 0.75rem; color: var(--slate-500); }
        .comment-text-box { background: var(--slate-50); border-radius: 0 15px 15px 15px; padding: 15px; border: 1px solid var(--slate-100); }
        .comment-text-box p { margin: 0; font-size: 0.9rem; line-height: 1.5; color: #334155; }

        /* ATTACHMENT CARD */
        .attachment-card { margin-top: 12px; border: 1px solid var(--slate-200); border-radius: 15px; overflow: hidden; background: white; }
        .attachment-image-preview { height: 140px; background-size: cover; background-position: center; position: relative; }
        .preview-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; opacity: 0; transition: 0.2s; text-decoration: none; }
        .attachment-image-preview:hover .preview-overlay { opacity: 1; }
        
        .attachment-info { padding: 12px; display: flex; align-items: center; gap: 12px; }
        .file-type-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .is-img { background: #eff6ff; color: #3b82f6; }
        .is-pdf { background: #fff1f2; color: #e11d48; }
        .file-details { flex: 1; display: flex; flex-direction: column; }
        .file-name { font-size: 0.8rem; font-weight: 700; color: var(--slate-900); }
        .file-meta { font-size: 0.7rem; color: var(--slate-500); }
        .btn-open-file { color: var(--slate-400); font-size: 1.1rem; transition: 0.2s; }
        .btn-open-file:hover { color: var(--primary); }

        /* SIDEBAR COMPONENTS */
        .priority-box { display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 10px; font-weight: 700; font-size: 0.8rem; width: 100%; }
        .p-dot { width: 8px; height: 8px; border-radius: 50%; }
        .p-alta { background: #fef2f2; color: #b91c1c; } .p-alta .p-dot { background: #ef4444; }
        .p-media { background: #fffbeb; color: #b45309; } .p-media .p-dot { background: #f59e0b; }
        .p-baja { background: #f0fdf4; color: #15803d; } .p-baja .p-dot { background: #10b981; }

        .data-grid-side { margin-top: 20px; display: flex; flex-direction: column; gap: 18px; }
        .data-row { display: flex; align-items: center; gap: 15px; }
        .icon-val { font-size: 1rem; color: var(--slate-400); width: 20px; text-align: center; }
        .data-label { display: block; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: var(--slate-500); letter-spacing: 0.05em; }
        .data-value { font-size: 0.9rem; font-weight: 600; color: var(--slate-900); }
        .alert-danger .data-value { color: var(--danger); }

        /* AUDIT TIMELINE */
        .audit-timeline { position: relative; padding-left: 10px; }
        .audit-node { position: relative; padding-left: 20px; padding-bottom: 20px; }
        .node-circle { position: absolute; left: -5px; top: 5px; width: 10px; height: 10px; background: var(--slate-200); border-radius: 50%; border: 2px solid white; z-index: 2; }
        .audit-node::before { content: ''; position: absolute; left: -1px; top: 15px; width: 2px; height: 100%; background: var(--slate-100); }
        .audit-node:last-child::before { display: none; }
        .node-date { font-size: 0.7rem; color: var(--slate-400); display: block; }
        .node-text { font-size: 0.8rem; margin: 2px 0 0; color: var(--slate-700); }
        .node-status { font-weight: 700; color: var(--primary); }

        .system-footprint { font-size: 0.7rem; color: var(--slate-400); text-align: center; display: flex; gap: 15px; justify-content: center; }
        .highlight-border { border-top: 4px solid var(--primary); }
        .fa-spin-slow { animation: fa-spin 3s infinite linear; }
    </style>
</x-base-layout>