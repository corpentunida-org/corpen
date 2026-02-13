<x-base-layout>
    {{-- Carga de estilos de librería --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

    <div class="task-edit-wrapper">
        {{-- Encabezado de Acción --}}
        <header class="form-header">
            <div class="header-content">
                <a href="{{ route('flujo.tasks.index') }}" class="btn-back-minimal">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="header-text">
                    <h1 class="main-title">Edición de Tarea <span class="text-accent">#{{ $task->id }}</span></h1>
                    <p class="main-subtitle">Ajuste parámetros operativos y gestione el hilo de comunicación.</p>
                </div>
            </div>
        </header>

        {{-- Banner de Contexto de Proyecto --}}
        @if ($task->workflow)
            <a href="{{ route('flujo.workflows.show', $task->workflow->id) }}" class="workflow-banner-link"
                title="Click para ir al Proyecto">
                <div class="wf-banner-content">
                    <i class="fas fa-project-diagram"></i>
                    <div>
                        <span class="wf-label">Proyecto / Workflow de Origen</span>
                        <h2 class="wf-title">{{ $task->workflow->nombre }}</h2>
                    </div>
                </div>
                <div class="wf-status-tag">
                    <span class="pulse-indicator"></span>
                    Vinculado
                </div>
            </a>
        @endif

        <main class="form-grid">
            {{-- Columna Principal: Formulario --}}
            <div class="form-column">
                <form action="{{ route('flujo.tasks.update', $task) }}" method="POST" id="task-update-form" class="card-minimal">
                    @csrf
                    @method('PUT')

                    @if ($task->workflow)
                        <input type="hidden" name="redirect_to" value="{{ route('flujo.workflows.show', $task->workflow->id) }}">
                    @endif

                    <div class="form-group">
                        <label for="titulo">Título de la Tarea</label>
                        <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $task->titulo) }}"
                            class="form-input @error('titulo') invalid @enderror" required>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción Detallada</label>
                        <textarea name="descripcion" id="descripcion" rows="6" class="form-input">{{ old('descripcion', $task->descripcion) }}</textarea>
                    </div>

                    <div class="form-row-compact">
                        <div class="form-group flex-1">
                            <label for="workflow_id">Workflow Asociado</label>
                            <select name="workflow_id" id="workflow_id" autocomplete="off">
                                <option value="">Sin Workflow asignado</option>
                                @foreach ($workflows as $workflow)
                                    <option value="{{ $workflow->id }}"
                                        {{ old('workflow_id', $task->workflow_id) == $workflow->id ? 'selected' : '' }}>
                                        {{ $workflow->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group flex-1">
                            <label for="user_id">Responsable Ejecutivo</label>
                            <select name="user_id" id="user_id" autocomplete="off" required>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ old('user_id', $task->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row-compact">
                        <div class="form-group flex-1">
                            <label for="estado">Estado Operativo</label>
                            <select name="estado" id="estado" class="form-select">
                                <option value="pendiente"
                                    {{ old('estado', $task->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente
                                </option>
                                <option value="en_proceso"
                                    {{ old('estado', $task->estado) == 'en_proceso' ? 'selected' : '' }}>En Proceso
                                </option>
                                <option value="revisado"
                                    {{ old('estado', $task->estado) == 'revisado' ? 'selected' : '' }}>Revisado
                                </option>
                                <option value="completado"
                                    {{ old('estado', $task->estado) == 'completado' ? 'selected' : '' }}>Completado
                                </option>
                            </select>
                        </div>
                        <div class="form-group flex-1">
                            <label for="prioridad">Prioridad</label>
                            <select name="prioridad" id="prioridad" class="form-select">
                                <option value="baja"
                                    {{ old('prioridad', $task->prioridad) == 'baja' ? 'selected' : '' }}>Baja</option>
                                <option value="media"
                                    {{ old('prioridad', $task->prioridad) == 'media' ? 'selected' : '' }}>Media
                                </option>
                                <option value="alta"
                                    {{ old('prioridad', $task->prioridad) == 'alta' ? 'selected' : '' }}>Alta</option>
                            </select>
                        </div>
                        <div class="form-group flex-1">
                            <label for="fecha_limite">Fecha Límite</label>
                            <input type="date" name="fecha_limite" id="fecha_limite"
                                value="{{ old('fecha_limite', $task->fecha_limite ? $task->fecha_limite->format('Y-m-d') : '') }}"
                                class="form-input">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end header-actions">
                        <button type="submit" form="task-update-form" class="btn-save-corporate">
                            <i class="fas fa-cloud-upload-alt"></i> <span>Actualizar Registro</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Columna Lateral: Canal de Feedback --}}
            <div class="form-column">
                <div class="card-minimal feedback-section">
                    <label class="section-label-header"><i class="fas fa-comment-dots"></i> Canal de Feedback</label>

                    <form action="{{ route('flujo.comments.store') }}" method="POST" enctype="multipart/form-data"
                        class="comment-post-box">
                        @csrf
                        <div class="form-group m-0">
                            <label for="titulo">Comentario</label>
                            <input type="hidden" name="task_id" value="{{ $task->id }}">
                            <textarea name="comentario" class="form-input mb-3" rows="9" placeholder="Escriba una actualización..." required></textarea>
                        </div>
                        <div class="soporte-dropzone" onclick="document.getElementById('soporte').click()">
                            <input type="file" name="soporte" id="soporte" class="hidden-input"accept=".pdf,image/*">
                            <i class="fas fa-paperclip"></i>
                            <span id="file-name-preview">Adjuntar Soporte (PDF/Imagen)</span>
                        </div>

                        <button type="submit" class="btn-save-corporate w-full mt-3">
                            Publicar <i class="fas fa-paper-plane ml-2"></i>
                        </button>
                    </form>
                </div>
            </div>
        </main>
        <div class="card-minimal col-md-12 mt-4">
            <div class="comment-thread mt-4">
                @forelse($task->comments->sortByDesc('created_at') as $comment)
                    <div class="comment-bubble">
                        <div class="comment-head">
                            <div class="avatar-circle">{{ substr($comment->user->name ?? 'S', 0, 1) }}</div>
                            <span class="user-name">{{ $comment->user->name ?? 'Sistema' }}</span>
                            <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="comment-body">{{ $comment->comentario }}</div>

                        @if ($comment->soporte)
                            <div class="comment-attachment">
                                @php
                                    $urlS3 = Storage::disk('s3')->temporaryUrl(
                                        $comment->soporte,
                                        now()->addMinutes(30),
                                    );
                                @endphp
                                <a href="{{ $urlS3 }}" target="_blank" class="attachment-link">
                                    <i class="fas fa-file-download"></i> Ver Archivo
                                </a>
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-muted text-center text-sm italic">Sin comentarios aún.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Script de la librería --}}
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const commonConfig = {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                plugins: ['dropdown_input'],
                onInitialize: function() {
                    this.wrapper.style.opacity = "1";
                }
            };

            new TomSelect("#user_id", {
                ...commonConfig,
                placeholder: 'Buscar responsable...'
            });
            new TomSelect("#workflow_id", {
                ...commonConfig,
                placeholder: 'Buscar proyecto...'
            });

            // Preview de nombre de archivo
            document.getElementById('soporte').addEventListener('change', function(e) {
                const fileName = e.target.files[0] ? e.target.files[0].name :
                    "Adjuntar Soporte (PDF/Imagen)";
                document.getElementById('file-name-preview').innerText = fileName;
            });
        });
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;800&display=swap');

        :root {
            --bg-neutral: #f8fafc;
            --brand-primary: #0f172a;
            --accent-blue: #4f46e5;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --white: #ffffff;
            --radius-xl: 20px;
            --radius-md: 12px;
        }

        body {
            background-color: var(--bg-neutral);
            font-family: 'Inter', sans-serif;
        }

        .task-edit-wrapper {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 24px;
        }

        /* Header */
        .form-header {
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .btn-back-minimal {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            transition: 0.2s;
            text-decoration: none;
        }

        .btn-back-minimal:hover {
            background: var(--brand-primary);
            color: white;
        }

        .main-title {
            font-family: 'Outfit';
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--text-main);
            margin: 0;
        }

        .text-accent {
            color: var(--accent-blue);
        }

        .main-subtitle {
            font-size: 0.95rem;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Banner de Workflow */
        .workflow-banner-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--white);
            border: 1px solid var(--border-color);
            padding: 16px 24px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            text-decoration: none;
            transition: 0.2s;
            border-left: 4px solid var(--accent-blue);
        }

        .workflow-banner-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .wf-banner-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .wf-banner-content i {
            font-size: 1.5rem;
            color: var(--accent-blue);
        }

        .wf-label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--text-muted);
            display: block;
        }

        .wf-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
        }

        .wf-status-tag {
            background: #f0fdf4;
            color: #166534;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pulse-indicator {
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        /* Form Layout */
        .form-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 24px;
        }

        .card-minimal {
            background: var(--white);
            border-radius: var(--radius-xl);
            padding: 28px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 8px;
            letter-spacing: 0.05em;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            background: #fbfcfd;
            font-size: 14px;
            outline: none;
            transition: 0.2s;
        }

        .form-input:focus {
            border-color: var(--accent-blue);
            background: white;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.08);
        }

        .form-row-compact {
            display: flex;
            gap: 16px;
        }

        .flex-1 {
            flex: 1;
        }

        /* Feedback Section */
        .section-label-header {
            font-size: 12px;
            font-weight: 800;
            color: var(--brand-primary);
            margin-bottom: 20px;
            display: block;
            text-transform: uppercase;
        }

        .soporte-dropzone {
            border: 2px dashed var(--border-color);
            padding: 15px;
            border-radius: var(--radius-md);
            text-align: center;
            cursor: pointer;
            transition: 0.2s;
            color: var(--text-muted);
            font-size: 13px;
        }

        .soporte-dropzone:hover {
            border-color: var(--accent-blue);
            background: #f5f3ff;
            color: var(--accent-blue);
        }

        .hidden-input {
            display: none;
        }

        .comment-bubble {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 15px;
            margin-bottom: 12px;
        }

        .comment-head {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .avatar-circle {
            width: 24px;
            height: 24px;
            background: var(--accent-blue);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
        }

        .user-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            flex: 1;
        }

        .comment-date {
            font-size: 11px;
            color: var(--text-muted);
        }

        .comment-body {
            font-size: 13px;
            color: var(--text-main);
            line-height: 1.5;
        }

        .attachment-link {
            font-size: 11px;
            font-weight: 700;
            color: var(--accent-blue);
            text-decoration: none;
            margin-top: 8px;
            display: inline-block;
        }

        /* Botón Corporativo */
        .btn-save-corporate {
            background: var(--brand-primary);
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-save-corporate:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            background: #000;
        }

        .w-full {
            width: 100%;
            justify-content: center;
        }

        /* TomSelect Overrides */
        .ts-control {
            border-radius: var(--radius-md) !important;
            padding: 12px 16px !important;
            background: #fbfcfd !important;
            border: 1px solid var(--border-color) !important;
        }

        .ts-dropdown {
            border-radius: var(--radius-md) !important;
            margin-top: 5px !important;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
            }
        }

        @media (max-width: 992px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-row-compact {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</x-base-layout>
