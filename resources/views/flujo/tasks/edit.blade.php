<x-base-layout>
    {{-- Carga de estilos de librería --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

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

        /* --- MEJORA UX: Dropzone modernizada --- */
        .soporte-dropzone {
            border: 2px dashed #cbd5e1;
            background: #f8fafc;
            padding: 25px 15px;
            border-radius: var(--radius-md);
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: var(--text-muted);
            position: relative;
        }

        .soporte-dropzone:hover, .soporte-dropzone.dragover {
            border-color: var(--accent-blue);
            background: #eff6ff;
            color: var(--brand-primary);
        }
        /* --------------------------------------- */

        .hidden-input {
            display: none;
        }

        /* --- MEJORA UX: Timeline para los comentarios --- */
        .comment-thread {
            position: relative;
            padding-left: 30px; 
        }

        .comment-thread::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 12px; 
            width: 2px;
            background: #e2e8f0;
            border-radius: 2px;
        }

        .comment-bubble {
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 16px;
            margin-bottom: 20px;
            position: relative;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .comment-bubble:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.05);
            border-color: #cbd5e1;
        }

        .comment-bubble .avatar-circle {
            position: absolute;
            left: -42px; 
            top: 15px;
            width: 28px;
            height: 28px;
            background: var(--brand-primary);
            color: white;
            border: 3px solid var(--bg-neutral); 
            box-shadow: 0 0 0 1px #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
        }
        /* ----------------------------------------------- */

        .comment-head {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
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
            justify-content: center;
            gap: 10px;
        }

        .btn-save-corporate:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            background: #000;
        }

        .w-full {
            width: 100%;
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
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }

        @media (max-width: 992px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-row-compact { flex-direction: column; gap: 0; }
        }

        /* Estilos minimalistas para la lista de tareas */
        .task-container { display: none; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .task-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
        .btn-toggle { background: none; border: none; color: #64748b; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: color 0.2s; }
        .btn-toggle:hover { color: #0f172a; }
        .task-row { display: flex; align-items: center; padding: 6px 0; border-bottom: 1px solid transparent; transition: all 0.2s; }
        .task-row:hover { border-bottom-color: #f1f5f9; }
        .task-checkbox { width: 16px; height: 16px; margin-right: 12px; cursor: pointer; accent-color: #3b82f6; }
        .task-input { flex: 1; border: none; outline: none; background: transparent; font-size: 14px; color: #334155; transition: all 0.2s; padding: 4px 0; }
        .task-input:focus { border-bottom: 1px dashed #cbd5e1; }
        .task-input.completed { text-decoration: line-through; color: #94a3b8; }
        .task-delete { opacity: 0; background: none; border: none; color: #ef4444; cursor: pointer; font-size: 14px; padding: 0 8px; transition: opacity 0.2s; }
        .task-row:hover .task-delete { opacity: 1; }
        .btn-add-task { background: none; border: none; color: #94a3b8; font-size: 14px; cursor: pointer; padding: 8px 0 0 28px; display: flex; align-items: center; gap: 6px; width: 100%; text-align: left; }
        .btn-add-task:hover { color: #3b82f6; }

        /* Estilos para la previsualización al pasar el mouse (Hover Preview) */
        #hover-preview-card {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            background: white;
            padding: 10px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            width: 500px;
            max-width: 90vw;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
        }
        #hover-preview-card img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            max-height: 70vh;
            object-fit: contain;
        }
        #hover-preview-card iframe {
            width: 100%;
            height: 60vh;
            border: none;
            border-radius: 8px;
            background: #f8fafc;
        }
    </style>

    <div class="task-edit-wrapper">
        
        {{-- Header Minimalista y Amigable (Bootstrap Nativo) --}}
        <header class="bg-white rounded-4 shadow-sm border p-4 mb-4">
            
            {{-- Fila Superior: Navegación y Acciones Globales --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3">
                <div class="d-flex flex-wrap gap-2">
                    @if($task->workflow)
                        <a href="{{ route('flujo.workflows.show', $task->workflow->id) }}" 
                           class="btn btn-sm btn-light text-secondary rounded-pill fw-semibold px-3 border" title="Volver al Workflow">
                            <i class="fas fa-arrow-left me-1"></i> Proyecto
                        </a>
                    @endif
                    <a href="{{ route('flujo.tasks.index') }}" 
                       class="btn btn-sm btn-light text-secondary rounded-pill fw-semibold px-3 border">
                        <i class="fas fa-tasks me-1"></i> Tareas
                    </a>
                    
                    {{-- MEJORA UX 2: Salto rápido a la vista de detalles --}}
                    <a href="{{ route('flujo.tasks.show', $task->id) }}" 
                       class="btn btn-sm btn-light text-primary rounded-pill fw-semibold px-3 border border-primary-subtle" title="Ver cómo se ve esta tarea">
                        <i class="fas fa-eye me-1"></i> Visor
                    </a>
                </div>
                
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill fw-semibold d-none d-sm-inline-block">
                        <span class="text-primary">Edición</span> / #{{ $task->id }}
                    </span>
                    
                    {{-- MEJORA UX 1: Botón de Guardado Superior (Vinculado al ID del formulario abajo) --}}
                    <button type="submit" form="task-update-form" class="btn btn-sm btn-dark rounded-pill fw-semibold px-4 shadow-sm d-flex align-items-center gap-2">
                        <i class="fas fa-save"></i> <span class="d-none d-md-inline">Guardar Cambios</span>
                    </button>
                </div>
            </div>

            {{-- Fila Inferior: Título, Meta-datos y Badges --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
                
                <div>
                    <h1 class="h2 fw-bolder text-dark mb-1">Edición de Tarea</h1>
                    {{-- MEJORA UX 3: Feedback de última actividad --}}
                    <p class="text-muted small mb-3 mb-md-0">
                        <i class="fas fa-history me-1"></i> Última actualización: {{ $task->updated_at->diffForHumans() }}
                    </p>
                </div>
                
                <div class="d-flex flex-wrap align-items-center gap-2 text-secondary small fw-medium">
                    
                    {{-- Badge de Estado con "Puntito" de color --}}
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill d-flex align-items-center gap-2 shadow-sm">
                        @if(in_array(strtolower($task->estado), ['completado', 'finalizado', 'revisado']))
                            <i class="fas fa-circle text-success" style="font-size: 8px;"></i>
                        @elseif(strtolower($task->estado) == 'en_proceso')
                            <i class="fas fa-circle text-primary" style="font-size: 8px;"></i>
                        @else
                            <i class="fas fa-circle text-warning" style="font-size: 8px;"></i>
                        @endif
                        {{ str_replace('_', ' ', ucfirst($task->estado)) }}
                    </span>

                    {{-- Badge de Prioridad --}}
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill d-flex align-items-center gap-2 shadow-sm">
                        @if(strtolower($task->prioridad) == 'alta')
                            <i class="fas fa-flag text-danger"></i>
                        @elseif(strtolower($task->prioridad) == 'media')
                            <i class="fas fa-flag text-warning"></i>
                        @else
                            <i class="fas fa-flag text-secondary"></i>
                        @endif
                        Prioridad {{ ucfirst($task->prioridad) }}
                    </span>
                    
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

                    <div class="mb-3">
                        <label for="titulo" class="form-label text-secondary fw-semibold">Título de la Tarea</label>
                        <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $task->titulo) }}"
                            class="form-control @error('titulo') is-invalid @enderror" required>
                        @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex align-items-center mb-3 p-2 bg-light border rounded">
                        <div class="form-check form-switch mb-0">
                            {{-- Agregamos el atributo 'checked' aquí --}}
                            <input class="form-check-input" type="checkbox" role="switch" id="devModeToggle" style="cursor: pointer;" checked>
                            <label class="form-check-label text-secondary fw-semibold ms-2" for="devModeToggle" style="cursor: pointer;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1 mb-1" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3.854 2.146a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 3.293l1.146-1.147a.5.5 0 0 1 .708 0zm0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 7.293l1.146-1.147a.5.5 0 0 1 .708 0zm0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0z"/>
                                </svg>
                                Habilitar Checklist
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label text-secondary fw-semibold">Descripción Detallada</label>
                        
                        <textarea name="descripcion" id="descripcion" rows="6" 
                            class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $task->descripcion) }}</textarea>

                        <div id="devModeContainer" class="border rounded p-3 bg-white shadow-sm" style="display: none;">
                            
                            {{-- NUEVO: Barra de progreso gerencial --}}
                            <div id="progress-wrapper" style="display: none; margin-bottom: 15px;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Progreso de la Tarea</span>
                                    <span id="progress-text" style="font-size: 12px; font-weight: 800; color: var(--accent-blue);">0%</span>
                                </div>
                                <div style="width: 100%; height: 8px; background-color: #e2e8f0; border-radius: 10px; overflow: hidden;">
                                    <div id="progress-bar" style="width: 0%; height: 100%; background-color: var(--accent-blue); transition: width 0.4s ease, background-color 0.4s ease;"></div>
                                </div>
                            </div>
                            
                            <div id="taskList"></div>
                            
                            <button type="button" id="addTaskBtn" class="btn btn-sm btn-outline-secondary mt-3 d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                </svg>
                                Agregar Sub-tarea
                            </button>
                        </div>
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
                    <label class="section-label-header"><i class="fas fa-history"></i> Actualizaciones y Evidencias</label>

                    <form action="{{ route('flujo.comments.store') }}" method="POST" enctype="multipart/form-data" class="comment-post-box">
                        @csrf
                        
                        <div class="d-flex align-items-center mb-3 p-2 bg-light border rounded">
                            <div class="form-check form-switch mb-0">
                                {{-- Agregamos el atributo 'checked' aquí --}}
                                <input class="form-check-input" type="checkbox" role="switch" id="templateToggle" onchange="alternarPlantilla(this)" style="cursor: pointer;" checked>
                                <label class="form-check-label text-secondary fw-semibold ms-2" for="templateToggle" style="cursor: pointer; font-size: 0.9rem;">
                                    Usar Formato de Reporte
                                </label>
                            </div>
                        </div>

                        <div class="form-group m-0" id="normalComentarioContainer">
                            <label for="comentario">Comentario</label>
                            <input type="hidden" name="task_id" value="{{ $task->id }}">
                            <textarea name="comentario" id="comentario" class="form-input mb-3" rows="7" placeholder="Escriba una actualización o descripción de la evidencia..." required></textarea>
                        </div>

                        <div id="templateContainer" class="border rounded p-3 bg-white shadow-sm mb-3" style="display: none;">
                            <div class="row g-2">
                                <div class="col-6"><label class="small text-muted mb-0" style="font-size: 10px;">FECHA:</label><input type="date" class="form-control form-control-sm dev-template-input" data-label="FECHA"></div>
                                <div class="col-6"><label class="small text-muted mb-0" style="font-size: 10px;">RESPONSABLE:</label><input type="text" class="form-control form-control-sm dev-template-input" data-label="RESPONSABLE" value="Brayam Castillo"></div>
                                <div class="col-12"><label class="small text-muted mb-0" style="font-size: 10px;">TIEMPO INVERTIDO:</label><input type="text" class="form-control form-control-sm dev-template-input" data-label="TIEMPO INVERTIDO" placeholder="Ej. 2 horas"></div>
                                <div class="col-12"><label class="small text-muted mb-0" style="font-size: 10px;">OBJETIVO TAREA:</label><input type="text" class="form-control form-control-sm dev-template-input" data-label="OBJETIVO DE LA TAREA"></div>
                                <div class="col-12"><label class="small text-muted mb-0" style="font-size: 10px;">DESARROLLO:</label><textarea class="form-control form-control-sm dev-template-input" data-label="DESARROLLO REALIZADO" rows="2"></textarea></div>
                                <div class="col-12"><label class="small text-muted mb-0" style="font-size: 10px;">CAMBIOS:</label><textarea class="form-control form-control-sm dev-template-input" data-label="CAMBIOS IMPLEMENTADOS" rows="2"></textarea></div>
                                <div class="col-12"><label class="small text-muted mb-0" style="font-size: 10px;">PROBLEMAS:</label><textarea class="form-control form-control-sm dev-template-input" data-label="PROBLEMAS IDENTIFICADOS" rows="2"></textarea></div>
                                <div class="col-12"><label class="small text-muted mb-0" style="font-size: 10px;">SOLUCIÓN:</label><textarea class="form-control form-control-sm dev-template-input" data-label="SOLUCIÓN APLICADA" rows="2"></textarea></div>
                                <div class="col-12"><label class="small text-muted mb-0" style="font-size: 10px;">PENDIENTES:</label><textarea class="form-control form-control-sm dev-template-input" data-label="PENDIENTES / PRÓXIMOS PASOS" rows="2"></textarea></div>
                                <div class="col-12"><label class="small text-muted mb-0" style="font-size: 10px;">COMMIT GITHUB:</label><input type="text" class="form-control form-control-sm dev-template-input" data-label="COMMIT GITHUB" placeholder="Ej. fix/bug"></div>
                            </div>
                        </div>

                        {{-- NUEVA DROPZONE: Icono, Drag & Drop y Preview integrados --}}
                        <div class="soporte-dropzone" id="main-dropzone" onclick="document.getElementById('soporte').click()">
                            <input type="file" name="soporte" id="soporte" class="hidden-input" accept=".pdf,image/*">
                            
                            <i class="fas fa-cloud-upload-alt fa-2x mb-2" id="dropzone-icon" style="color: var(--accent-blue);"></i>
                            
                            <div id="file-name-preview">
                                <span style="font-weight: 600; color: var(--brand-primary); display: block;">Haz clic o arrastra tu evidencia aquí</span>
                                <span style="font-size: 11px;">(También puedes pegar una imagen con <b>Ctrl+V</b>)</span>
                            </div>

                            <img id="image-preview-box" src="" style="display:none; max-width: 100%; max-height: 200px; margin: 15px auto 0; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        </div>

                        <button type="submit" class="btn-save-corporate w-full mt-3">
                            <i class="fas fa-paper-plane"></i> Publicar Evidencia
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
                        
                        {{-- INICIO DE LA MEJORA VISUAL PARA LA PLANTILLA --}}
                        @php
                            $textoRaw = trim($comment->comentario);
                            $esDevTemplate = str_starts_with($textoRaw, '[') && str_ends_with($textoRaw, ']');
                            
                            if ($esDevTemplate) {
                                $textoLimpio = trim(substr($textoRaw, 1, -1));
                                $textoLimpio = e($textoLimpio);
                                $textoFormateado = preg_replace('/([A-ZÁÉÍÓÚÑ \/]+):/', '<span style="display: block; margin-top: 14px; font-size: 10px; font-weight: 800; color: var(--accent-blue); letter-spacing: 0.5px;">$1</span>', $textoLimpio);
                                $htmlFinal = '<div style="background: #ffffff; border: 1px solid var(--border-color); border-left: 3px solid var(--accent-blue); padding: 5px 15px 15px; border-radius: 8px; margin-top: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">' . nl2br($textoFormateado) . '</div>';
                            } else {
                                $htmlFinal = nl2br(e($textoRaw));
                            }
                        @endphp

                        <div class="comment-body">{!! $htmlFinal !!}</div>
                        {{-- FIN DE LA MEJORA --}}

                        @if ($comment->soporte)
                            <div class="comment-attachment">
                                @php
                                    $urlS3 = Storage::disk('s3')->temporaryUrl(
                                        $comment->soporte,
                                        now()->addMinutes(30),
                                    );
                                    
                                    $extension = strtolower(pathinfo($comment->soporte, PATHINFO_EXTENSION));
                                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                                @endphp
                                
                                <a href="{{ $urlS3 }}" target="_blank" class="attachment-link preview-trigger" 
                                   data-url="{{ $urlS3 }}" 
                                   data-type="{{ $isImage ? 'image' : 'pdf' }}">
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

    {{-- Script para Selects e Inicialización de Archivos Unificada --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const commonConfig = {
                create: false,
                sortField: { field: "text", direction: "asc" },
                plugins: ['dropdown_input'],
                onInitialize: function() { this.wrapper.style.opacity = "1"; }
            };

            new TomSelect("#user_id", { ...commonConfig, placeholder: 'Buscar responsable...' });
            new TomSelect("#workflow_id", { ...commonConfig, placeholder: 'Buscar proyecto...' });

            // =================================================================
            // LÓGICA DE DRAG & DROP, PORTAPAPELES Y PREVISUALIZACIÓN UNIFICADA
            // =================================================================
            const dropzone = document.getElementById('main-dropzone');
            const fileInput = document.getElementById('soporte');
            const previewText = document.getElementById('file-name-preview');
            const imagePreviewBox = document.getElementById('image-preview-box');
            const dropzoneIcon = document.getElementById('dropzone-icon');

            // Función central que procesa visualmente el archivo
            function processFile(file) {
                if (!file) return;

                // Actualizar texto
                previewText.innerHTML = `<strong><i class="fas fa-check-circle me-1"></i> ${file.name || 'Imagen adjuntada'}</strong><br><span style="font-size:11px; color:#64748b;">Haz clic de nuevo para cambiar</span>`;
                dropzone.style.borderColor = 'var(--accent-blue)';
                dropzone.style.background = '#f5f3ff';
                dropzone.style.color = 'var(--accent-blue)';

                // Si es imagen, generar previsualización
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreviewBox.src = e.target.result;
                        imagePreviewBox.style.display = 'block';
                        dropzoneIcon.style.display = 'none'; // Ocultamos la nube para dar espacio a la imagen
                    }
                    reader.readAsDataURL(file);
                } else {
                    // Si no es imagen (PDF), aseguramos ocultar la previsualización de imagen
                    imagePreviewBox.style.display = 'none';
                    dropzoneIcon.style.display = 'inline-block';
                    dropzoneIcon.className = 'fas fa-file-pdf fa-2x mb-2 text-danger'; // Cambiamos a icono PDF
                }
            }

            // 1. Al dar clic tradicional y seleccionar archivo
            fileInput.addEventListener('change', function(e) {
                processFile(e.target.files[0]);
            });

            // 2. Al pegar (Ctrl+V)
            document.addEventListener('paste', function(e) {
                let pastedFiles = e.clipboardData.files;
                if (pastedFiles.length === 0) return; // Si es texto plano, se ignora

                const dt = new DataTransfer();
                dt.items.add(pastedFiles[0]);
                fileInput.files = dt.files; // Asignamos el archivo al input real

                processFile(pastedFiles[0]); // Lo mostramos en la vista
            });

            // 3. Al arrastrar y soltar (Drag & Drop)
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, preventDefaults, false);
            });
            function preventDefaults(e) { e.preventDefault(); e.stopPropagation(); }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => dropzone.classList.add('dragover'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => dropzone.classList.remove('dragover'), false);
            });

            dropzone.addEventListener('drop', function(e) {
                let droppedFiles = e.dataTransfer.files;
                if(droppedFiles.length) {
                    fileInput.files = droppedFiles;
                    processFile(droppedFiles[0]);
                }
            }, false);
        });
    </script>

    {{-- modo desarrollo para convertir el textarea (Descripción) en una checklist visual --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('devModeToggle');
            const textarea = document.getElementById('descripcion');
            const container = document.getElementById('devModeContainer');
            const taskList = document.getElementById('taskList');
            const addTaskBtn = document.getElementById('addTaskBtn');
            
            let isListMode = false;

            // Alternar entre vistas
            toggleBtn.addEventListener('change', function() {
                isListMode = this.checked;
                if (isListMode) {
                    textarea.style.display = 'none';
                    container.style.display = 'block';
                    renderTasks();
                } else {
                    textarea.style.display = 'block';
                    container.style.display = 'none';
                }
            });

            // Renderizar tareas leyendo el textarea
            function renderTasks() {
                taskList.innerHTML = '';
                const lines = textarea.value.split('\n');

                lines.forEach((line) => {
                    if (line.trim() === '') return;
                    const isChecked = line.trim().toLowerCase().startsWith('[x]');
                    let textValue = line.replace(/^\[[ xX]\]\s*/, '');
                    createTaskRow(textValue, isChecked);
                });

                if (taskList.children.length === 0) createTaskRow('', false);
            }

            // Crear fila con diseño corporativo (Bootstrap)
            function createTaskRow(text, isChecked, focusOnCreate = false) {
                const row = document.createElement('div');
                row.className = 'd-flex align-items-center mb-2';

                // Checkbox
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.checked = isChecked;
                checkbox.className = 'form-check-input mt-0 me-3'; 
                checkbox.style.cursor = 'pointer';

                // Input de texto
                const input = document.createElement('input');
                input.type = 'text';
                input.value = text;
                input.className = 'form-control form-control-sm';
                
                // Estilos iniciales si está checkeado
                if(isChecked) {
                    input.style.textDecoration = 'line-through';
                    input.classList.add('text-muted', 'bg-light');
                }

                // Botón de eliminar usando la clase nativa btn-close de Bootstrap
                const deleteBtn = document.createElement('button');
                deleteBtn.type = 'button';
                deleteBtn.className = 'btn-close ms-2'; 
                deleteBtn.setAttribute('aria-label', 'Eliminar tarea');
                deleteBtn.style.fontSize = '0.75rem'; 

                // Eventos
                checkbox.addEventListener('change', function() {
                    if(this.checked) {
                        input.style.textDecoration = 'line-through';
                        input.classList.add('text-muted', 'bg-light');
                    } else {
                        input.style.textDecoration = 'none';
                        input.classList.remove('text-muted', 'bg-light');
                    }
                    syncTextarea();
                });

                input.addEventListener('input', syncTextarea);
                
                // UX: Enter crea nueva tarea
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        createTaskRow('', false, true);
                        syncTextarea();
                    }
                });

                deleteBtn.addEventListener('click', function() {
                    row.remove();
                    syncTextarea();
                });

                // Ensamblar la fila
                row.appendChild(checkbox);
                row.appendChild(input);
                row.appendChild(deleteBtn);
                taskList.appendChild(row);

                if(focusOnCreate) input.focus();
            }

            // Botón Agregar
            addTaskBtn.addEventListener('click', function() {
                createTaskRow('', false, true);
                syncTextarea();
            });

            // Sincronizar hacia el textarea
            function syncTextarea() {
                const rows = taskList.querySelectorAll('.d-flex');
                let newText = [];
                let totalTasks = 0;
                let completedTasks = 0;

                rows.forEach(row => {
                    const checkbox = row.querySelector('.form-check-input');
                    const input = row.querySelector('input[type="text"]');
                    
                    if (input && input.value.trim() !== '') {
                        totalTasks++;
                        if (checkbox.checked) completedTasks++;
                        
                        const mark = checkbox.checked ? '[x]' : '[ ]';
                        newText.push(`${mark} ${input.value}`);
                    }
                });

                textarea.value = newText.join('\n');

                // NUEVO: Lógica de la barra de progreso
                const progressWrapper = document.getElementById('progress-wrapper');
                const progressBar = document.getElementById('progress-bar');
                const progressText = document.getElementById('progress-text');
                
                if (totalTasks > 0) {
                    progressWrapper.style.display = 'block';
                    let percentage = Math.round((completedTasks / totalTasks) * 100);
                    progressBar.style.width = percentage + '%';
                    progressText.innerText = percentage + '%';
                    
                    // Semáforo de colores en la barra
                    if (percentage === 100) {
                        progressBar.style.backgroundColor = '#10b981'; // Verde completado
                        progressText.style.color = '#10b981';
                    } else if (percentage > 50) {
                        progressBar.style.backgroundColor = '#3b82f6'; // Azul avanzado
                        progressText.style.color = '#3b82f6';
                    } else {
                        progressBar.style.backgroundColor = '#f59e0b'; // Naranja iniciando
                        progressText.style.color = '#f59e0b';
                    }
                } else {
                    progressWrapper.style.display = 'none';
                }
            }
        });
    </script>

    {{-- Lógica para la Plantilla del Comentario (Feedback) --}}
    <script>
        // Función activada por el onchange del switch del comentario
        function alternarPlantilla(checkbox) {
            var normalContainer = document.getElementById('normalComentarioContainer');
            var templateContainer = document.getElementById('templateContainer');
            var textarea = document.getElementById('comentario');
            
            if (checkbox.checked) {
                // Muestra la plantilla
                normalContainer.style.display = 'none';
                templateContainer.style.display = 'block';
                textarea.removeAttribute('required');
                actualizarPlantilla(); 
            } else {
                // Muestra el textarea normal
                normalContainer.style.display = 'block';
                templateContainer.style.display = 'none';
                textarea.setAttribute('required', 'required');
                textarea.value = ''; 
            }
        }

        // Lee los campos de la plantilla y los inyecta en el textarea oculto
        function actualizarPlantilla() {
            var inputs = document.querySelectorAll('.dev-template-input');
            var textarea = document.getElementById('comentario');
            var texto = "[\n";
            
            inputs.forEach(function(input) {
                var label = input.getAttribute('data-label');
                var val = input.value.trim();
                if (input.tagName === 'TEXTAREA') {
                    texto += label + ":\n" + val + "\n\n";
                } else {
                    texto += label + ": " + val + "\n";
                }
            });
            
            texto += "]";
            if (textarea) textarea.value = texto.trim();
        }

        // Inicializar fecha y eventos al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            // Asignar fecha actual al input correspondiente
            var dateInput = document.querySelector('.dev-template-input[data-label="FECHA"]');
            if(dateInput) dateInput.value = new Date().toISOString().split('T')[0];

            // Escuchar cambios en los inputs de la plantilla
            var inputs = document.querySelectorAll('.dev-template-input');
            inputs.forEach(function(input) {
                input.addEventListener('input', actualizarPlantilla);
            });
        });
    </script>

    {{-- NUEVO: Lógica visual del Hover Preview para archivos adjuntos y lo otro --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // ==========================================
            // 1. LÓGICA DE LOS INTERRUPTORES (TOGGLES)
            // ==========================================
            
            // Toggle de Formato de Reporte
            let templateToggle = document.getElementById('templateToggle');
            if(templateToggle && templateToggle.checked) {
                // Simula que el usuario acaba de hacer click para forzar la lógica nativa
                templateToggle.dispatchEvent(new Event('change'));
            }
            
            // Toggle de Habilitar Checklist
            let devToggle = document.getElementById('devModeToggle');
            if(devToggle && devToggle.checked) {
                // Simula que el usuario acaba de hacer click
                devToggle.dispatchEvent(new Event('change'));
            }

            // ==========================================
            // 2. LÓGICA DEL HOVER PREVIEW (ARCHIVOS)
            // ==========================================
            let previewModal = document.createElement('div');
            previewModal.id = 'hover-preview-card';
            previewModal.style.position = 'absolute';
            previewModal.style.zIndex = '9999';
            previewModal.style.display = 'none';
            previewModal.style.opacity = '0';
            previewModal.style.transition = 'opacity 0.2s ease';
            previewModal.style.pointerEvents = 'none'; 
            document.body.appendChild(previewModal);

            let hoverTimer; 

            const triggers = document.querySelectorAll('.preview-trigger');

            triggers.forEach(trigger => {
                trigger.addEventListener('mouseenter', function(e) {
                    const url = this.getAttribute('data-url');
                    const type = this.getAttribute('data-type');

                    hoverTimer = setTimeout(() => {
                        if (type === 'image') {
                            previewModal.innerHTML = `<img src="${url}" style="max-width:300px; border-radius:10px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);" alt="Vista previa">`;
                        } else {
                            previewModal.innerHTML = `<iframe src="${url}#toolbar=0&navpanes=0&scrollbar=0" style="width:300px; height:400px; border:none; border-radius:10px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);"></iframe>`;
                        }
                        
                        previewModal.style.display = 'block';
                        setTimeout(() => previewModal.style.opacity = '1', 10); 
                    }, 400); 
                });

                trigger.addEventListener('mousemove', function(e) {
                    previewModal.style.left = (e.pageX + 15) + 'px';
                    previewModal.style.top = (e.pageY + 15) + 'px';
                });

                trigger.addEventListener('mouseleave', function() {
                    clearTimeout(hoverTimer); 
                    previewModal.style.opacity = '0';
                    setTimeout(() => {
                        previewModal.style.display = 'none';
                        previewModal.innerHTML = ''; 
                    }, 200); 
                });
            });

        });
    </script>

</x-base-layout>