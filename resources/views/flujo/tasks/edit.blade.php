<x-base-layout>
    <div class="task-edit-wrapper">
        {{-- Encabezado Ejecutivo --}}
        <header class="form-header">
            <div class="header-content">
                <a href="{{ route('flujo.tasks.index') }}" class="btn-back-minimal">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <span class="system-tag">Gobernanza de Unidades</span>
                    <h1 class="main-title">Edición de Tarea #{{ $task->id }}</h1>
                    <p class="main-subtitle">Modifique los parámetros operativos o gestione el feedback colaborativo.</p>
                </div>
            </div>
            <div class="header-actions">
                <button type="submit" form="task-update-form" class="btn-corporate-black">
                    <i class="fas fa-save"></i> Actualizar Registro
                </button>
            </div>
        </header>

        <main class="edit-grid">
            {{-- Columna Izquierda: Formulario Principal --}}
            <section class="form-main-column">
                <form action="{{ route('flujo.tasks.update', $task) }}" method="POST" id="task-update-form" class="modern-card">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="titulo">Título de la Unidad</label>
                        <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $task->titulo) }}" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Especificaciones Técnicas / Descripción</label>
                        <textarea name="descripcion" id="descripcion" rows="6" class="form-input">{{ old('descripcion', $task->descripcion) }}</textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group flex-1">
                            <label for="workflow_id">Workflow de Origen</label>
                            <select name="workflow_id" id="workflow_id" class="form-select">
                                <option value="">Sin Workflow asignado</option>
                                @foreach($workflows as $workflow)
                                    <option value="{{ $workflow->id }}" {{ (old('workflow_id', $task->workflow_id) == $workflow->id) ? 'selected' : '' }}>
                                        {{ $workflow->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group flex-1">
                            <label for="user_id">Responsable Designado</label>
                            <select name="user_id" id="user_id" class="form-select" required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ (old('user_id', $task->user_id) == $user->id) ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group flex-1">
                            <label for="estado">Estado Operativo</label>
                            <select name="estado" id="estado" class="form-select">
                                <option value="pendiente" {{ old('estado', $task->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="en_proceso" {{ old('estado', $task->estado) == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                <option value="revisado" {{ old('estado', $task->estado) == 'revisado' ? 'selected' : '' }}>Revisado</option>
                                <option value="completado" {{ old('estado', $task->estado) == 'completado' ? 'selected' : '' }}>Completado</option>
                            </select>
                        </div>
                        <div class="form-group flex-1">
                            <label for="prioridad">Nivel de Prioridad</label>
                            <select name="prioridad" id="prioridad" class="form-select">
                                <option value="baja" {{ old('prioridad', $task->prioridad) == 'baja' ? 'selected' : '' }}>Baja</option>
                                <option value="media" {{ old('prioridad', $task->prioridad) == 'media' ? 'selected' : '' }}>Media</option>
                                <option value="alta" {{ old('prioridad', $task->prioridad) == 'alta' ? 'selected' : '' }}>Alta</option>
                            </select>
                        </div>
                        <div class="form-group flex-1">
                            <label for="fecha_limite">Plazo de Entrega</label>
                            <input type="date" name="fecha_limite" id="fecha_limite" 
                                   value="{{ old('fecha_limite', $task->fecha_limite ? $task->fecha_limite->format('Y-m-d') : '') }}" 
                                   class="form-input">
                        </div>
                    </div>
                </form>
            </section>

            {{-- Columna Derecha: Comentarios y Feedback con Lógica de Archivos --}}
            <section class="form-side-column">
                <div class="modern-card feedback-section">
                    <h2 class="side-title"><i class="fas fa-comments"></i> Canal de Feedback</h2>
                    
                    {{-- Formulario de Comentario - IMPORTANTE: enctype para subir archivos --}}
                    <form action="{{ route('flujo.comments.store') }}" method="POST" enctype="multipart/form-data" class="comment-post-box">
                        @csrf
                        <input type="hidden" name="task_id" value="{{ $task->id }}">
                        <textarea name="comentario" placeholder="Escriba una anotación o feedback..." required></textarea>

                        <div class="soporte-container">
                            <label for="soporte" class="soporte-label">
                                <i class="fas fa-paperclip"></i> Adjuntar Soporte (PDF o Imagen)
                            </label>
                            <input type="file" name="soporte" id="soporte" class="soporte-input" accept=".pdf,image/*">
                            <span class="soporte-help">Máx 10MB (Formatos: PDF, JPG, PNG)</span>
                        </div>

                        <button type="submit" class="btn-send-comment">Postear Mensaje</button>
                    </form>

                    <div class="comment-thread">
                        @forelse($task->comments->sortByDesc('created_at') as $comment)
                            <div class="comment-bubble">
                                <div class="comment-header">
                                    <span class="comment-user">{{ $comment->user->name ?? 'Sistema' }}</span>
                                    <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="comment-body">
                                    {{ $comment->comentario }}
                                </div>
                                
                                {{-- LÓGICA DE SOPORTE INTEGRADA --}}
                                @if($comment->soporte)
                                    @php
                                        $extension = pathinfo($comment->soporte, PATHINFO_EXTENSION);
                                        $esImagen = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        // Generar URL temporal de S3 válida por 30 minutos
                                        $urlS3 = Storage::disk('s3')->temporaryUrl($comment->soporte, now()->addMinutes(30));
                                    @endphp

                                    <div class="comment-attachment">
                                        @if($esImagen)
                                            {{-- Miniatura si es imagen --}}
                                            <a href="{{ $urlS3 }}" target="_blank" class="attachment-preview">
                                                <img src="{{ $urlS3 }}" alt="Vista previa">
                                            </a>
                                        @endif
                                        
                                        <a href="{{ $urlS3 }}" target="_blank" class="attachment-link">
                                            <i class="fas {{ $esImagen ? 'fa-image' : 'fa-file-pdf' }}"></i> 
                                            Ver Soporte ({{ strtoupper($extension) }})
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="empty-feedback text-center py-4">
                                <i class="far fa-comment-dots fa-2x mb-2" style="opacity: 0.1;"></i>
                                <p class="text-muted small">Sin registros de comunicación aún.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </main>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        :root {
            --corp-dark: #0f172a;
            --corp-blue: #2563eb;
            --corp-border: #f1f5f9;
            --corp-bg: #fafafa;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        .task-edit-wrapper { max-width: 1200px; margin: 40px auto; padding: 0 24px; font-family: 'Inter', sans-serif; }

        /* Header */
        .form-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
        .header-content { display: flex; align-items: center; gap: 20px; }
        .btn-back-minimal { 
            width: 42px; height: 42px; border-radius: 12px; background: #fff; border: 1px solid var(--corp-border);
            display: flex; align-items: center; justify-content: center; color: var(--text-muted); text-decoration: none; transition: 0.2s;
        }
        .btn-back-minimal:hover { background: var(--corp-dark); color: #fff; }
        .system-tag { font-size: 0.65rem; font-weight: 800; color: var(--corp-blue); text-transform: uppercase; letter-spacing: 0.1em; display: block; margin-bottom: 4px; }
        .main-title { font-size: 1.8rem; font-weight: 800; letter-spacing: -0.03em; margin: 0; }
        .main-subtitle { font-size: 0.9rem; color: var(--text-muted); }

        .btn-corporate-black {
            background: var(--corp-dark); color: #fff; border: none; padding: 12px 24px; border-radius: 10px;
            font-weight: 600; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: 0.2s;
        }
        .btn-corporate-black:hover { background: #000; transform: translateY(-1px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }

        /* Layout */
        .edit-grid { display: grid; grid-template-columns: 1.6fr 1fr; gap: 24px; align-items: start; }
        @media (max-width: 992px) { .edit-grid { grid-template-columns: 1fr; } }

        .modern-card { background: #fff; border-radius: 16px; border: 1px solid var(--corp-border); padding: 28px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }

        /* Form Elements */
        .form-group { margin-bottom: 24px; }
        .form-group label { display: block; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 10px; }
        .form-input, .form-select {
            width: 100%; padding: 12px 16px; border-radius: 10px; border: 1px solid var(--corp-border);
            background: #f8fafc; font-size: 0.9rem; outline: none; transition: 0.2s;
        }
        .form-input:focus, .form-select:focus { border-color: var(--corp-blue); background: #fff; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.05); }
        .form-row { display: flex; gap: 20px; flex-wrap: wrap; }
        .flex-1 { flex: 1; min-width: 150px; }

        /* Feedback Section */
        .comment-post-box { margin-bottom: 30px; }
        .comment-post-box textarea {
            width: 100%; min-height: 100px; padding: 16px; border-radius: 12px; border: 1px solid var(--corp-border);
            background: #f8fafc; font-size: 0.85rem; outline: none; resize: vertical; margin-bottom: 12px;
        }

        /* Soporte Styles */
        .soporte-container { margin-bottom: 15px; background: #f1f5f9; padding: 12px; border-radius: 10px; border: 1px dashed #cbd5e1; }
        .soporte-label { display: block; font-size: 0.7rem; font-weight: 700; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; }
        .soporte-input { font-size: 0.8rem; color: var(--text-main); width: 100%; }
        .soporte-help { display: block; font-size: 0.65rem; color: #94a3b8; margin-top: 5px; }

        .btn-send-comment {
            background: var(--corp-dark); color: #fff; border: none; padding: 12px 16px;
            border-radius: 8px; font-weight: 600; font-size: 0.8rem; cursor: pointer; transition: 0.2s; width: 100%;
        }
        .btn-send-comment:hover { background: #000; transform: translateY(-1px); }

        /* Comment Bubbles */
        .comment-thread { display: flex; flex-direction: column; gap: 16px; margin-top: 20px;}
        .comment-bubble { padding: 16px; border-radius: 12px; background: #f8fafc; border: 1px solid var(--corp-border); }
        .comment-header { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .comment-user { font-size: 0.8rem; font-weight: 700; color: var(--corp-dark); }
        .comment-date { font-size: 0.7rem; color: var(--text-muted); }
        .comment-body { font-size: 0.85rem; line-height: 1.5; color: var(--text-main); }
        
        /* Attachment Display */
        .comment-attachment { margin-top: 12px; padding-top: 10px; border-top: 1px solid #e2e8f0; }
        .attachment-preview { display: block; width: 100%; max-height: 120px; border-radius: 8px; overflow: hidden; margin-bottom: 8px; border: 1px solid #e2e8f0;}
        .attachment-preview img { width: 100%; height: 100%; object-fit: cover; transition: 0.3s; }
        .attachment-preview:hover img { transform: scale(1.05); }
        
        .attachment-link { font-size: 0.75rem; font-weight: 600; color: var(--corp-blue); text-decoration: none; display: inline-flex; align-items: center; gap: 5px; }
        .attachment-link:hover { text-decoration: underline; }
    </style>
</x-base-layout>