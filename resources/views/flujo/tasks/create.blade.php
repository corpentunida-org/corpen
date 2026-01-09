<x-base-layout>
    <div class="task-creation-wrapper">
        {{-- Encabezado de Acción --}}
        <header class="form-header">
            <div class="header-content">
                <a href="{{ route('flujo.tasks.index') }}" class="btn-back-minimal">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="header-text">
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

                            <div class="form-group mb-0">
                                <label for="descripcion">Descripción Detallada</label>
                                <textarea name="descripcion" id="descripcion" rows="6" 
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
                                        <option value="{{ $workflow->id }}"
                                            {{ (old('workflow_id', request('workflow_id')) == $workflow->id) ? 'selected' : '' }}>
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

                            <div class="form-group mb-0">
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

        body { background-color: var(--bg-neutral); }

        .task-creation-wrapper {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 24px;
            font-family: 'Inter', sans-serif;
        }

        /* Header */
        .form-header { margin-bottom: 35px; }
        .header-content { display: flex; align-items: flex-start; gap: 20px; }
        .btn-back-minimal {
            width: 44px; height: 44px; border-radius: var(--radius-md);
            border: 1px solid var(--border-color); background: var(--white);
            display: flex; align-items: center; justify-content: center;
            color: var(--text-muted); text-decoration: none; transition: 0.2s;
            flex-shrink: 0;
        }
        .btn-back-minimal:hover { background: var(--brand-primary); color: var(--white); border-color: var(--brand-primary); }
        
        .main-title { font-family: 'Outfit'; font-size: 1.85rem; font-weight: 800; letter-spacing: -0.03em; margin: 0; color: var(--text-main); }
        .main-subtitle { font-size: 0.95rem; color: var(--text-muted); margin-top: 6px; line-height: 1.5; }

        /* Form Layout */
        .form-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 24px; align-items: start; }

        .card-minimal {
            background: var(--white); border-radius: var(--radius-xl); padding: 28px;
            border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            height: 100%;
        }

        /* Inputs */
        .form-group { margin-bottom: 24px; }
        .form-group.mb-0 { margin-bottom: 0; }
        
        .form-group label {
            display: block; font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.08em;
            color: var(--text-muted); margin-bottom: 10px;
        }
        
        .form-input, .form-select {
            width: 100%; padding: 12px 16px; border-radius: var(--radius-md);
            border: 1px solid var(--border-color); background: #fbfcfd;
            font-size: 14px; color: var(--text-main); outline: none;
            transition: all 0.25s ease;
            box-sizing: border-box;
        }
        
        .form-input:focus, .form-select:focus {
            border-color: var(--accent-blue); background: var(--white);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.08);
        }

        .form-row-compact { display: flex; gap: 16px; }
        .flex-1 { flex: 1; }

        /* Alerts */
        .alert-minimal-error {
            background: #fff1f2; color: #be123c; padding: 16px 20px;
            border-radius: var(--radius-md); margin-bottom: 28px; display: flex;
            align-items: center; gap: 12px; font-size: 14px; font-weight: 600;
            border: 1px solid #ffe4e6;
        }

        /* Footer Actions */
        .form-actions-footer {
            margin-top: 40px; padding-top: 28px;
            border-top: 1px solid var(--border-color);
            display: flex; justify-content: flex-end; gap: 20px; align-items: center;
        }
        .btn-cancel-soft {
            text-decoration: none; color: var(--text-muted);
            font-size: 14px; font-weight: 600; padding: 12px 24px;
            transition: 0.2s;
        }
        .btn-cancel-soft:hover { color: var(--brand-primary); }

        .btn-save-corporate {
            background: var(--brand-primary); color: var(--white);
            border: none; padding: 16px 32px; border-radius: var(--radius-md);
            font-size: 14px; font-weight: 700; cursor: pointer;
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-save-corporate:hover {
            background: #000; transform: translateY(-2px);
            box-shadow: 0 12px 20px -5px rgba(0,0,0,0.15);
        }

        .error-text { color: #e11d48; font-size: 12px; margin-top: 6px; font-weight: 500; display: block; }
        .invalid { border-color: #fda4af !important; background: #fff1f2 !important; }

        /* --- MOBILE OPTIMIZATION --- */
        @media (max-width: 768px) {
            .task-creation-wrapper { margin: 20px auto; padding: 0 16px; }
            .form-grid { grid-template-columns: 1fr; gap: 16px; }
            .card-minimal { padding: 20px; }
            .form-header { margin-bottom: 25px; }
            .main-title { font-size: 1.5rem; }
            .form-row-compact { flex-direction: column; gap: 0; }
            .form-actions-footer { flex-direction: column-reverse; gap: 12px; }
            .btn-save-corporate, .btn-cancel-soft { width: 100%; text-align: center; }
            .btn-save-corporate { padding: 18px; }
        }
    </style>
</x-base-layout>