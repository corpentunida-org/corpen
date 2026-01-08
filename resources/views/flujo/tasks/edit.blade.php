<x-base-layout>
    <div class="task-edit-wrapper">
        {{-- Encabezado Ejecutivo --}}
        <header class="form-header">
            <div class="header-content">
                <a href="{{ route('flujo.tasks.index') }}" class="btn-back-minimal" title="Cancelar y volver">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="header-titles">
                    <span class="system-tag"><i class="fas fa-shield-alt"></i> Gobernanza de Unidades</span>
                    <h1 class="main-title">Edición de Tarea <span class="text-accent">#{{ $task->id }}</span></h1>
                    <p class="main-subtitle">Ajuste parámetros operativos y gestione el hilo de comunicación.</p>
                </div>
            </div>
            <div class="header-actions">
                {{-- Botón que activa el envío del formulario --}}
                <button type="submit" form="task-update-form" class="btn-update-record">
                    <i class="fas fa-cloud-upload-alt"></i> <span>Actualizar Registro</span>
                </button>
            </div>
        </header>

        {{-- Banner de Contexto de Proyecto (Workflow) INTERACTIVO --}}
        @if($task->workflow)
            <a href="{{ route('flujo.workflows.show', $task->workflow->id) }}" class="workflow-context-edit-banner clickable-banner" title="Click para ir al Proyecto / Workflow">
                <div class="wf-edit-icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <div class="wf-edit-details">
                    <span class="wf-edit-label">Proyecto / Workflow de Origen <i class="fas fa-external-link-alt ml-1"></i></span>
                    <h2 class="wf-edit-title">{{ $task->workflow->nombre }}</h2>
                </div>
                <div class="wf-edit-status">
                    <span class="pulse-indicator"></span>
                    <span class="wf-status-text">Vinculado</span>
                </div>
            </a>
        @else
            <div class="workflow-context-edit-banner no-wf">
                <div class="wf-edit-icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <div class="wf-edit-details">
                    <span class="wf-edit-label">Proyecto / Workflow de Origen</span>
                    <h2 class="wf-edit-title">Unidad Independiente</h2>
                </div>
            </div>
        @endif

        <main class="edit-grid">
            {{-- Columna Izquierda: Formulario Principal --}}
            <section class="form-main-column">
                <form action="{{ route('flujo.tasks.update', $task) }}" method="POST" id="task-update-form" class="modern-card-edit">
                    @csrf
                    @method('PUT')

                    {{-- 
                        LÓGICA DE REDIRECCIÓN PARA EL CONTROLADOR: 
                        Se envía la ruta exacta del proyecto/workflow para que el update sepa a dónde regresar.
                    --}}
                    @if($task->workflow)
                        <input type="hidden" name="redirect_to" value="{{ route('flujo.workflows.show', $task->workflow->id) }}">
                    @endif

                    <div class="form-section-header">
                        <i class="fas fa-info-circle"></i> Información General
                    </div>

                    <div class="form-group floating-label">
                        <label for="titulo">Título de la Unidad de Trabajo</label>
                        <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $task->titulo) }}" class="form-input-corp" placeholder="Ej: Reporte de facturación mensual" required>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Especificaciones Técnicas / Hoja de Ruta</label>
                        <textarea name="descripcion" id="descripcion" rows="6" class="form-input-corp" placeholder="Detalle los pasos o requisitos técnicos...">{{ old('descripcion', $task->descripcion) }}</textarea>
                    </div>

                    <div class="form-section-header mt-4">
                        <i class="fas fa-user-cog"></i> Parámetros de Asignación
                    </div>

                    <div class="form-row">
                        <div class="form-group flex-1">
                            <label for="workflow_id">Workflow Destino</label>
                            <div class="select-wrapper">
                                <select name="workflow_id" id="workflow_id" class="form-select-corp">
                                    <option value="">Sin Workflow asignado</option>
                                    @foreach($workflows as $workflow)
                                        <option value="{{ $workflow->id }}" {{ (old('workflow_id', $task->workflow_id) == $workflow->id) ? 'selected' : '' }}>
                                            {{ $workflow->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group flex-1">
                            <label for="user_id">Responsable de Ejecución</label>
                            <div class="select-wrapper">
                                <select name="user_id" id="user_id" class="form-select-corp" required>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ (old('user_id', $task->user_id) == $user->id) ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group flex-1">
                            <label for="estado">Estado Operativo</label>
                            <div class="select-wrapper">
                                <select name="estado" id="estado" class="form-select-corp st-select-{{ old('estado', $task->estado) }}">
                                    <option value="pendiente" {{ old('estado', $task->estado) == 'pendiente' ? 'selected' : '' }}>🟡 Pendiente</option>
                                    <option value="en_proceso" {{ old('estado', $task->estado) == 'en_proceso' ? 'selected' : '' }}>🔵 En Proceso</option>
                                    <option value="revisado" {{ old('estado', $task->estado) == 'revisado' ? 'selected' : '' }}>🟣 Revisado</option>
                                    <option value="completado" {{ old('estado', $task->estado) == 'completado' ? 'selected' : '' }}>🟢 Completado</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group flex-1">
                            <label for="prioridad">Nivel de Prioridad</label>
                            <div class="select-wrapper">
                                <select name="prioridad" id="prioridad" class="form-select-corp">
                                    <option value="baja" {{ old('prioridad', $task->prioridad) == 'baja' ? 'selected' : '' }}>Baja</option>
                                    <option value="media" {{ old('prioridad', $task->prioridad) == 'media' ? 'selected' : '' }}>Media</option>
                                    <option value="alta" {{ old('prioridad', $task->prioridad) == 'alta' ? 'selected' : '' }}>Alta</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group flex-1">
                            <label for="fecha_limite">Plazo Límite</label>
                            <input type="date" name="fecha_limite" id="fecha_limite" 
                                   value="{{ old('fecha_limite', $task->fecha_limite ? $task->fecha_limite->format('Y-m-d') : '') }}" 
                                   class="form-input-corp">
                        </div>
                    </div>
                </form>
            </section>

            {{-- Columna Derecha: Canal de Feedback Mejorado --}}
            <section class="form-side-column">
                <div class="modern-card-edit feedback-section">
                    <h2 class="side-title"><i class="fas fa-comment-dots"></i> Canal de Feedback</h2>
                    
                    <form action="{{ route('flujo.comments.store') }}" method="POST" enctype="multipart/form-data" class="comment-post-box-corp">
                        @csrf
                        <input type="hidden" name="task_id" value="{{ $task->id }}">
                        <textarea name="comentario" placeholder="Escriba una observación técnica o actualización..." required></textarea>

                        <div class="soporte-dropzone">
                            <label for="soporte" class="soporte-label-corp">
                                <i class="fas fa-paperclip"></i> 
                                <span>Adjuntar Documentación</span>
                            </label>
                            <input type="file" name="soporte" id="soporte" class="soporte-input-hidden" accept=".pdf,image/*">
                            <div id="file-name-preview" class="file-name-display">Ningún archivo seleccionado</div>
                        </div>

                        <button type="submit" class="btn-post-comment">
                            Publicar Comentario <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>

                    <div class="comment-thread-corp">
                        @forelse($task->comments->sortByDesc('created_at') as $comment)
                            <div class="comment-bubble-corp">
                                <div class="comment-head">
                                    <div class="user-avatar-mini">{{ substr($comment->user->name ?? 'S', 0, 1) }}</div>
                                    <span class="user-name-mini">{{ $comment->user->name ?? 'Sistema' }}</span>
                                    <span class="date-mini">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="comment-body-corp">
                                    {{ $comment->comentario }}
                                </div>
                                
                                @if($comment->soporte)
                                    @php
                                        $extension = pathinfo($comment->soporte, PATHINFO_EXTENSION);
                                        $esImagen = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        $urlS3 = Storage::disk('s3')->temporaryUrl($comment->soporte, now()->addMinutes(30));
                                    @endphp
                                    <div class="comment-attachment-corp">
                                        <a href="{{ $urlS3 }}" target="_blank" class="attachment-pill">
                                            <i class="fas {{ $esImagen ? 'fa-image' : 'fa-file-pdf' }}"></i>
                                            <span>Ver Soporte .{{ strtoupper($extension) }}</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="empty-state-feedback">
                                <p>No hay hilos de conversación activos.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </aside>
        </main>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        :root {
            --brand-primary: #2563eb;
            --brand-dark: #0f172a;
            --bg-body: #f4f7fa;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #334155;
            --text-muted: #64748b;
        }

        body { background-color: var(--bg-body); font-family: 'Inter', sans-serif; color: var(--text-main); }
        .task-edit-wrapper { max-width: 1250px; margin: 30px auto; padding: 0 20px; }

        /* HEADER UI */
        .form-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .header-content { display: flex; align-items: center; gap: 20px; }
        .btn-back-minimal { width: 45px; height: 45px; border-radius: 14px; background: white; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; color: var(--text-muted); text-decoration: none; transition: 0.3s; }
        .btn-back-minimal:hover { background: var(--brand-dark); color: white; transform: scale(1.05); }
        
        .system-tag { font-size: 0.7rem; font-weight: 800; color: var(--brand-primary); text-transform: uppercase; letter-spacing: 0.05em; }
        .main-title { font-size: 1.85rem; font-weight: 800; color: var(--brand-dark); letter-spacing: -0.04em; margin: 5px 0; }
        .main-title .text-accent { color: var(--brand-primary); }
        .main-subtitle { font-size: 0.9rem; color: var(--text-muted); }

        .btn-update-record { background: var(--brand-dark); color: white; border: none; padding: 12px 25px; border-radius: 12px; font-weight: 700; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: 0.3s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .btn-update-record:hover { background: var(--brand-primary); transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3); }

        /* WORKFLOW BANNER INTERACTIVO */
        .workflow-context-edit-banner { 
            background: white; 
            border: 1px solid var(--border-color); 
            border-left: 6px solid var(--brand-primary); 
            border-radius: 16px; 
            padding: 15px 25px; 
            margin-bottom: 25px; 
            display: flex; 
            align-items: center; 
            gap: 20px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .clickable-banner:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.05);
            background: #fafafa;
        }
        .clickable-banner:hover .wf-edit-title { color: var(--brand-primary); }

        .wf-edit-icon { width: 45px; height: 45px; background: #eff6ff; color: var(--brand-primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
        .wf-edit-details { flex-grow: 1; }
        .wf-edit-label { font-size: 0.65rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; display: flex; align-items: center; gap: 5px; }
        .wf-edit-title { font-size: 1.2rem; font-weight: 800; color: var(--brand-dark); margin: 0; transition: color 0.2s; }
        
        .wf-edit-status { display: flex; align-items: center; gap: 10px; background: #f0fdf4; padding: 6px 14px; border-radius: 30px; }
        .wf-status-text { font-size: 0.7rem; font-weight: 800; color: #166534; text-transform: uppercase; }
        .pulse-indicator { width: 8px; height: 8px; background: #22c55e; border-radius: 50%; box-shadow: 0 0 0 rgba(34, 197, 94, 0.4); animation: pulse 2s infinite; }
        
        @keyframes pulse {
          0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
          70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
          100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }

        /* GRID & CARDS */
        .edit-grid { display: grid; grid-template-columns: 1.7fr 1fr; gap: 25px; }
        .modern-card-edit { background: white; border-radius: 20px; border: 1px solid var(--border-color); padding: 30px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .form-section-header { font-size: 0.85rem; font-weight: 800; text-transform: uppercase; color: var(--brand-primary); border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }

        /* FORM INPUTS */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.02em; }
        .form-input-corp, .form-select-corp { width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid var(--border-color); background: #f8fafc; font-size: 0.95rem; font-weight: 500; transition: all 0.3s; outline: none; }
        .form-input-corp:focus, .form-select-corp:focus { border-color: var(--brand-primary); background: white; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.08); }
        .form-row { display: flex; gap: 20px; }

        /* FEEDBACK AREA */
        .comment-post-box-corp { margin-bottom: 25px; }
        .comment-post-box-corp textarea { width: 100%; min-height: 110px; padding: 15px; border-radius: 14px; border: 1px solid var(--border-color); background: #f8fafc; font-size: 0.9rem; resize: vertical; margin-bottom: 15px; outline: none; transition: 0.3s; }
        .comment-post-box-corp textarea:focus { border-color: var(--brand-primary); background: white; }

        .soporte-dropzone { border: 2px dashed var(--border-color); padding: 15px; border-radius: 12px; text-align: center; margin-bottom: 15px; transition: 0.3s; background: #fdfdfd; cursor: pointer; }
        .soporte-dropzone:hover { border-color: var(--brand-primary); background: #eff6ff; }
        .soporte-label-corp { cursor: pointer; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); display: flex; flex-direction: column; gap: 5px; }
        .soporte-label-corp i { font-size: 1.2rem; color: var(--brand-primary); }
        .soporte-input-hidden { display: none; }
        .file-name-display { font-size: 0.7rem; color: var(--brand-primary); margin-top: 5px; font-weight: 600; }

        .btn-post-comment { width: 100%; background: var(--brand-dark); color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-post-comment:hover { background: #000; transform: translateY(-1px); }

        /* THREAD UX */
        .comment-thread-corp { display: flex; flex-direction: column; gap: 15px; }
        .comment-bubble-corp { background: #f8fafc; border: 1px solid var(--border-color); border-radius: 16px; padding: 15px; }
        .comment-head { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .user-avatar-mini { width: 25px; height: 25px; background: var(--brand-primary); color: white; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 800; }
        .user-name-mini { font-size: 0.8rem; font-weight: 700; color: var(--brand-dark); flex-grow: 1; }
        .date-mini { font-size: 0.7rem; color: var(--text-muted); }
        .comment-body-corp { font-size: 0.85rem; line-height: 1.5; color: var(--text-main); }
        .comment-attachment-corp { margin-top: 10px; }
        .attachment-pill { display: inline-flex; align-items: center; gap: 8px; background: white; border: 1px solid var(--border-color); padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; color: var(--brand-primary); text-decoration: none; transition: 0.2s; }
        .attachment-pill:hover { background: var(--brand-primary); color: white; }

        @media (max-width: 992px) {
            .edit-grid { grid-template-columns: 1fr; }
            .form-row { flex-direction: column; gap: 0; }
            .form-header { flex-direction: column; align-items: flex-start; gap: 20px; }
            .header-actions { width: 100%; }
            .btn-update-record { width: 100%; justify-content: center; }
        }
    </style>

    <script>
        document.getElementById('soporte').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : "Ningún archivo seleccionado";
            document.getElementById('file-name-preview').innerText = fileName;
        });
    </script>
</x-base-layout>