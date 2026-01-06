<x-base-layout>
    <div class="task-creation-wrapper">
        {{-- Encabezado de Acción --}}
        <header class="form-header">
            <div class="header-content">
                <a href="{{ route('flujo.tasks.index') }}" class="btn-back-minimal">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="main-title">Nueva Unidad de Trabajo</h1>
                    <p class="main-subtitle">Asigne parámetros, responsables y plazos para el cumplimiento del hito.</p>
                </div>
            </div>
        </header>

        <main class="form-container">
            @if ($errors->any())
                <div class="alert-minimal-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Existen campos que requieren su revisión antes de continuar.</span>
                </div>
            @endif

            <form action="{{ route('flujo.tasks.store') }}" method="POST" class="modern-form">
                @csrf
                
                <div class="form-grid">
                    {{-- Columna Principal: Información --}}
                    <div class="form-column">
                        <div class="card-minimal">
                            <div class="form-group">
                                <label for="titulo">Título de la Tarea</label>
                                <input type="text" name="titulo" id="titulo" 
                                       placeholder="Ej: Análisis de requerimientos técnicos"
                                       value="{{ old('titulo') }}" 
                                       class="form-input @error('titulo') invalid @enderror">
                                @error('titulo') <span class="error-text">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="descripcion">Descripción Detallada</label>
                                <textarea name="descripcion" id="descripcion" rows="5" 
                                          placeholder="Describa los alcances y criterios de aceptación..."
                                          class="form-input">{{ old('descripcion') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Columna Lateral: Configuración y Asignación --}}
                    <div class="form-column">
                        <div class="card-minimal">
                            <div class="form-group">
                                <label for="workflow_id"><i class="fas fa-project-diagram"></i> Workflow Asociado</label>
                                <select name="workflow_id" id="workflow_id" class="form-select">
                                    <option value="">Seleccione el flujo de origen</option>
                                    @foreach($workflows as $workflow)
                                        <option value="{{ $workflow->id }}" {{ old('workflow_id') == $workflow->id ? 'selected' : '' }}>
                                            {{ $workflow->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="user_id"><i class="far fa-user"></i> Responsable Ejecutivo</label>
                                <select name="user_id" id="user_id" class="form-select">
                                    <option value="">Asignar a un miembro</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-row-compact">
                                <div class="form-group flex-1">
                                    <label for="estado">Estado Inicial</label>
                                    <select name="estado" id="estado" class="form-select">
                                        <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="en_proceso" {{ old('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                        <option value="revisado" {{ old('estado') == 'revisado' ? 'selected' : '' }}>Revisado</option>
                                        <option value="completado" {{ old('estado') == 'completado' ? 'selected' : '' }}>Completado</option>
                                    </select>
                                </div>
                                <div class="form-group flex-1">
                                    <label for="prioridad">Prioridad</label>
                                    <select name="prioridad" id="prioridad" class="form-select">
                                        <option value="baja" {{ old('prioridad') == 'baja' ? 'selected' : '' }}>Baja</option>
                                        <option value="media" {{ old('prioridad') == 'media' ? 'selected' : '' }} selected>Media</option>
                                        <option value="alta" {{ old('prioridad') == 'alta' ? 'selected' : '' }}>Alta</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="fecha_limite"><i class="far fa-calendar-alt"></i> Fecha Límite de Entrega</label>
                                <input type="date" name="fecha_limite" id="fecha_limite" 
                                       value="{{ old('fecha_limite') }}" 
                                       class="form-input">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer de Acciones --}}
                <div class="form-actions-footer">
                    <a href="{{ route('flujo.tablero') }}" class="btn-cancel-soft">Descartar</a>
                    <button type="submit" class="btn-save-corporate">
                        Confirmar y Crear Tarea
                    </button>
                </div>
            </form>
        </main>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        :root {
            --bg-neutral: #fafafa;
            --brand-primary: #0f172a; /* Negro Corporativo */
            --accent-blue: #2563eb;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #f1f5f9;
            --white: #ffffff;
        }

        .task-creation-wrapper {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
            font-family: 'Inter', sans-serif;
        }

        /* Header */
        .form-header { margin-bottom: 40px; }
        .header-content { display: flex; align-items: center; gap: 20px; }
        .btn-back-minimal {
            width: 40px; height: 40px; border-radius: 10px;
            border: 1px solid var(--border-color); background: var(--white);
            display: flex; align-items: center; justify-content: center;
            color: var(--text-muted); text-decoration: none; transition: 0.2s;
        }
        .btn-back-minimal:hover { background: var(--brand-primary); color: var(--white); }
        .main-title { font-size: 1.75rem; font-weight: 800; letter-spacing: -0.03em; margin: 0; }
        .main-subtitle { font-size: 0.95rem; color: var(--text-muted); margin-top: 4px; }

        /* Form Layout */
        .form-grid { display: grid; grid-template-columns: 1.6fr 1fr; gap: 24px; }
        @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }

        .card-minimal {
            background: var(--white); border-radius: 16px; padding: 24px;
            border: 1px solid var(--border-color); box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        /* Inputs */
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block; font-size: 0.75rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.05em;
            color: var(--text-muted); margin-bottom: 8px;
        }
        .form-input, .form-select {
            width: 100%; padding: 12px 16px; border-radius: 10px;
            border: 1px solid var(--border-color); background: #f8fafc;
            font-size: 0.9rem; color: var(--text-main); outline: none;
            transition: all 0.2s ease;
        }
        .form-input:focus, .form-select:focus {
            border-color: var(--accent-blue); background: var(--white);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.05);
        }
        .form-row-compact { display: flex; gap: 12px; }
        .flex-1 { flex: 1; }

        /* Alerts */
        .alert-minimal-error {
            background: #fef2f2; color: #b91c1c; padding: 16px;
            border-radius: 12px; margin-bottom: 24px; display: flex;
            align-items: center; gap: 12px; font-size: 0.9rem; font-weight: 500;
        }

        /* Footer Actions */
        .form-actions-footer {
            margin-top: 40px; padding-top: 24px;
            border-top: 1px solid var(--border-color);
            display: flex; justify-content: flex-end; gap: 16px; align-items: center;
        }
        .btn-cancel-soft {
            text-decoration: none; color: var(--text-muted);
            font-size: 0.9rem; font-weight: 600; padding: 10px 20px;
        }
        .btn-save-corporate {
            background: var(--brand-primary); color: var(--white);
            border: none; padding: 14px 28px; border-radius: 12px;
            font-size: 0.9rem; font-weight: 700; cursor: pointer;
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-save-corporate:hover {
            background: #000; transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }

        .error-text { color: #dc2626; font-size: 0.75rem; margin-top: 4px; font-weight: 500; }
    </style>
</x-base-layout>