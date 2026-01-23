<x-base-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500&display=swap');

        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --secondary: #64748b;
            --danger: #ef4444;
            --warning: #f59e0b;
            --success: #10b981;
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --json-bg: #1e293b;
            --ring-color: rgba(79, 70, 229, 0.2);
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .form-container {
            max-width: 1150px;
            margin: 3rem auto;
            background: var(--bg-card);
            padding: 3rem;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        /* Línea decorativa superior */
        .form-container::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--primary), var(--success));
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 2rem;
            margin-bottom: 2.5rem;
        }

        .form-header h1 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-main);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
            letter-spacing: -0.04em;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 3.5rem;
        }

        @media (max-width: 1000px) {
            .form-grid { grid-template-columns: 1fr; gap: 2rem; }
        }

        .form-group { margin-bottom: 1.8rem; position: relative; }

        .form-group label {
            display: block;
            font-weight: 700;
            font-size: 0.85rem;
            margin-bottom: 0.7rem;
            color: var(--text-main);
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        /* Mejora de Inputs */
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.85rem 1.25rem;
            border: 2px solid var(--border-color);
            border-radius: 14px;
            background-color: #fcfcfd;
            font-size: 1rem;
            color: var(--text-main);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            background-color: #fff;
            box-shadow: 0 0 0 4px var(--ring-color);
            transform: translateY(-1px);
        }

        /* Tabs de Configuración */
        .config-tabs { 
            display: flex; 
            gap: 8px;
            margin-bottom: 1.8rem; 
            background: #f1f5f9;
            padding: 6px;
            border-radius: 16px;
            width: fit-content;
        }
        
        .config-tab {
            padding: 0.7rem 1.4rem;
            background: transparent;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }
        
        .config-tab.active { 
            background: white; 
            color: var(--primary); 
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .config-content { display: none; }
        .config-content.active { display: block; animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1); }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Tarjetas de Plantilla */
        .template-card {
            cursor: pointer;
            border: 2px solid var(--border-color);
            padding: 1.5rem;
            border-radius: 18px;
            background: white;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-align: left;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .template-card:hover {
            border-color: var(--primary);
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .template-card i {
            font-size: 2rem;
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }
        .template-card:hover i { transform: rotate(-5deg) scale(1.1); }

        /* Editor JSON */
        .json-editor-container { 
            border-radius: 18px; 
            overflow: hidden; 
            border: 2px solid var(--border-color);
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .json-editor { 
            font-family: 'Fira Code', monospace; 
            background: var(--json-bg); 
            color: #a5f3fc; 
            border: none; 
            padding: 1.5rem; 
            font-size: 0.9rem; 
            width: 100%; 
            line-height: 1.7;
            resize: vertical;
        }
        
        .json-editor-actions { 
            background: #0f172a; 
            padding: 1rem 1.5rem; 
            display: flex; 
            gap: 1rem;
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        
        .btn-small { 
            background: rgba(255, 255, 255, 0.08); 
            color: white; 
            border: 1px solid rgba(255,255,255,0.1);
            padding: 0.5rem 1rem; 
            border-radius: 10px; 
            font-size: 0.8rem; 
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-small:hover { 
            background: var(--primary); 
            border-color: var(--primary);
        }

        /* Checkbox Cards UX */
        .checkbox-card {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s ease;
        }
        
        .checkbox-card:hover { 
            border-color: var(--primary); 
            background: #f1f5f9;
            transform: scale(1.01);
        }

        .checkbox-card input[type="checkbox"] {
            accent-color: var(--primary);
            width: 22px;
            height: 22px;
            margin: 0;
        }

        /* Footer y Acciones */
        .form-actions { 
            margin-top: 4rem; 
            padding-top: 2rem; 
            border-top: 2px solid var(--border-color); 
            display: flex; 
            justify-content: flex-end; 
            gap: 1.5rem; 
        }

        .btn { 
            padding: 0.9rem 2.2rem; 
            border-radius: 14px; 
            font-weight: 700; 
            cursor: pointer; 
            display: flex; 
            align-items: center; 
            gap: 0.75rem; 
            border: none; 
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            font-size: 0.95rem;
        }

        .btn-primary { 
            background: var(--primary); 
            color: white; 
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        }

        .btn-primary:hover { 
            background: var(--primary-hover); 
            transform: translateY(-3px); 
            box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.4);
        }

        .btn-secondary {
            background: white;
            border: 2px solid var(--border-color);
            color: var(--text-muted);
        }

        .btn-secondary:hover {
            background: #f1f5f9;
            color: var(--text-main);
            border-color: var(--secondary);
        }
        
        /* Notificaciones */
        .notification { 
            position: fixed; top: 2rem; right: 2rem; 
            padding: 1.2rem 2rem; 
            border-radius: 18px; 
            background: #0f172a; 
            color: white; 
            z-index: 9999; 
            display: flex;
            align-items: center;
            gap: 1rem;
            transform: translateX(150%); 
            transition: 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55); 
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);
            border-left: 5px solid var(--primary);
        }
        .notification.show { transform: translateX(0); }

        /* Helpers */
        .date-error { border-color: var(--danger) !important; color: var(--danger); }
        .text-accent { color: var(--primary); }
        .mt-4 { margin-top: 1rem; }

        .btn-ghost-modern {
            padding: 10px 15px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f1f5f9;
            transition: 0.3s;
        }
        .btn-ghost-modern:hover {
            background: var(--primary-light);
            color: var(--primary) !important;
        }
        
        /* Estilos para la sección de equipo */
        .team-section {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #bbf7d0;
            border-radius: 20px;
            padding: 1.5rem;
            margin-top: 2.5rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        
        .team-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .team-header h4 {
            margin: 0;
            color: #166534;
            font-size: 1rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .team-empty {
            text-align: center;
            padding: 2rem 1rem;
            color: #166534;
        }
        
        .team-empty i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .team-empty p {
            margin: 0 0 1.5rem 0;
            font-size: 0.95rem;
            opacity: 0.8;
        }
        
        .search-container {
            position: relative;
            margin-bottom: 1rem;
        }
        
        .search-container input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 2px solid #bbf7d0;
            border-radius: 12px;
            background: white;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        
        .search-container input:focus {
            outline: none;
            border-color: #166534;
            box-shadow: 0 0 0 3px rgba(22, 101, 52, 0.1);
        }
        
        .search-container i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #166534;
            opacity: 0.6;
        }
        
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #bbf7d0;
            border-top: none;
            border-radius: 0 0 12px 12px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 100;
            display: none;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .search-results.active {
            display: block;
        }
        
        .search-result-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: all 0.2s;
            border-bottom: 1px solid #f0fdf4;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .search-result-item:hover {
            background: #f0fdf4;
            transform: translateX(5px);
        }
        
        .search-result-item:last-child {
            border-bottom: none;
        }
        
        .search-result-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #166534, #22c55e);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
            flex-shrink: 0;
        }
        
        .search-result-info {
            flex-grow: 1;
        }
        
        .search-result-name {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.9rem;
        }
        
        .search-result-email {
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        
        .team-list {
            max-height: 250px;
            overflow-y: auto;
            padding: 0.5rem 0;
        }
        
        .team-member {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border-radius: 12px;
            margin-bottom: 0.5rem;
            background: white;
            transition: all 0.2s ease;
        }
        
        .team-member:hover {
            background: #f0fdf4;
            transform: translateX(5px);
        }
        
        .team-member input[type="checkbox"] {
            margin-right: 0.75rem;
            accent-color: #166534;
        }
        
        .team-member .user-info {
            flex-grow: 1;
        }
        
        .team-member .user-name {
            font-weight: 600;
            color: #1e293b;
        }
        
        .team-member .user-email {
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        
        .team-member .remove-btn {
            background: #dcfce7;
            color: #166534;
            border: none;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            margin-left: 0.5rem;
        }
        
        .team-member .remove-btn:hover {
            background: #ef4444;
            color: white;
        }
        
        .team-actions {
            margin-top: 1rem;
            display: flex;
            justify-content: flex-end;
        }
        
        .btn-save-team {
            background: #166534;
            color: white;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }
        
        .btn-save-team:hover {
            background: #14532d;
            transform: translateY(-2px);
        }
        
        .btn-save-team.loading {
            opacity: 0.7;
            pointer-events: none;
        }
        
        .btn-save-team.loading i {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .add-member-section {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 2px dashed #bbf7d0;
        }
        
        .add-member-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        
        .add-member-header h5 {
            margin: 0;
            font-size: 0.9rem;
            color: #166534;
            font-weight: 700;
        }
        
        .toggle-add-member {
            background: #166534;
            color: white;
            border: none;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .toggle-add-member:hover {
            background: #14532d;
        }
    </style>

    <div class="form-container">
        <header class="form-header">
            <h1><i class="fas fa-project-diagram" style="color: var(--primary)"></i> Editar Proyecto</h1>
            <a href="{{ route('flujo.workflows.show', $workflow) }}" class="btn-ghost-modern" style="text-decoration:none; color: var(--secondary); font-weight: 700;">
                <i class="fas fa-eye"></i> Ver Expediente
            </a>
        </header>

        <form action="{{ route('flujo.workflows.update', $workflow) }}" method="POST" id="workflow-form">
            @csrf
            @method('PUT')

            {{-- CAMPO DE REDIRECCIÓN PERSONALIZADA AL SHOW DEL PROYECTO --}}
            <input type="hidden" name="redirect_to" value="{{ route('flujo.workflows.show', $workflow) }}">

            <div class="form-grid">
                <div class="form-column">
                    <div class="form-group">
                        <label for="nombre">Nombre Identificador <span style="color:var(--danger)">*</span></label>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $workflow->nombre) }}" placeholder="Ej: Pipeline de Ventas v2" required>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción Operativa</label>
                        <textarea id="descripcion" name="descripcion" rows="4" placeholder="Breve resumen de los objetivos de este flujo...">{{ old('descripcion', $workflow->descripcion) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Configuración Técnica / Metadata</label>
                        <div class="config-tabs">
                            <button type="button" class="config-tab active" data-tab="easy">
                                <i class="fas fa-magic"></i> Wizard
                            </button>
                            <button type="button" class="config-tab" data-tab="json">
                                <i class="fas fa-brackets-curly"></i> Código JSON
                            </button>
                        </div>

                        <div class="config-content active" id="easy-tab">
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.25rem;">
                                <div class="template-card" onclick="applyTemplate('notif')">
                                    <i class="fas fa-bell" style="color: var(--primary)"></i>
                                    <div>
                                        <strong>Sistema Alertas</strong>
                                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 6px; line-height: 1.4;">Habilita canales de correo y notificaciones push.</p>
                                    </div>
                                </div>
                                <div class="template-card" onclick="applyTemplate('high_prio')">
                                    <i class="fas fa-fire-flame-curved" style="color: var(--warning)"></i>
                                    <div>
                                        <strong>Misión Crítica</strong>
                                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 6px; line-height: 1.4;">Escalamiento automático y tiempos de respuesta cortos.</p>
                                    </div>
                                </div>
                                <div class="template-card" onclick="applyTemplate('approval')">
                                    <i class="fas fa-stamp" style="color: var(--success)"></i>
                                    <div>
                                        <strong>Flujo Aprobación</strong>
                                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 6px; line-height: 1.4;">Requiere validación de superiores antes de cerrar.</p>
                                    </div>
                                </div>
                                <div class="template-card" onclick="applyTemplate('clear')">
                                    <i class="fas fa-broom" style="color: var(--secondary)"></i>
                                    <div>
                                        <strong>Restablecer</strong>
                                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 6px; line-height: 1.4;">Limpiar todos los objetos de configuración JSON.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="config-content" id="json-tab">
                            <div class="json-editor-container" id="json-container">
                                <textarea id="configuracion" name="configuracion" rows="12" class="json-editor" spellcheck="false">{{ old('configuracion', $workflow->configuracion ? json_encode($workflow->configuracion, JSON_PRETTY_PRINT) : '') }}</textarea>
                                <div class="json-editor-actions">
                                    <button type="button" class="btn-small" onclick="formatJson()"><i class="fas fa-wand-sparkles"></i> Auto-formato</button>
                                    <button type="button" class="btn-small" onclick="validateJson()"><i class="fas fa-shield-check"></i> Validar Sintaxis</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-column">
                    <div class="form-group">
                        <label for="creado_por"><i class="fas fa-user-pen"></i> Autor Original</label>
                        <select name="creado_por" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('creado_por', $workflow->creado_por) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="asignado_a"><i class="fas fa-user-shield"></i> Responsable Asignado</label>
                        <select name="asignado_a">
                            <option value="">-- Sin asignar --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('asignado_a', $workflow->asignado_a) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                        <div class="form-group">
                            <label>Estado Global</label>
                            <select name="estado" required>
                                @foreach($estados as $val => $lab)
                                    <option value="{{ $val }}" {{ old('estado', $workflow->estado) == $val ? 'selected' : '' }}>{{ $lab }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nivel Prioridad</label>
                            <select name="prioridad" required>
                                @foreach($prioridades as $val => $lab)
                                    <option value="{{ $val }}" {{ old('prioridad', $workflow->prioridad) == $val ? 'selected' : '' }}>{{ $lab }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                        <div class="form-group">
                            <label>Fecha Lanzamiento</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio', $workflow->fecha_inicio?->format('Y-m-d')) }}">
                        </div>
                        <div class="form-group">
                            <label>Fecha Compromiso</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin', $workflow->fecha_fin?->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem;">
                        <label class="checkbox-card">
                            <input type="checkbox" name="activo" value="1" {{ old('activo', $workflow->activo) ? 'checked' : '' }}>
                            <div>
                                <strong style="font-size: 1rem;">Flujo Activo</strong><br>
                                <small style="color: var(--text-muted);">Permitir el procesamiento de tareas en este flujo.</small>
                            </div>
                        </label>
                        <label class="checkbox-card">
                            <input type="checkbox" name="es_plantilla" value="1" {{ old('es_plantilla', $workflow->es_plantilla) ? 'checked' : '' }}>
                            <div>
                                <strong style="font-size: 1rem;">Definir como Plantilla</strong><br>
                                <small style="color: var(--text-muted);">Hacer que este diseño sea clonable para futuros flujos.</small>
                            </div>
                        </label>
                    </div>

                    <!-- SECCIÓN DE EQUIPO MEJORADA -->
                    <div class="team-section">
                        <div class="team-header">
                            <h4><i class="fas fa-users"></i> Equipo del Proyecto</h4>
                            <span style="font-size: 0.85rem; color: #166534; font-weight: 600;" id="team-count">
                                {{ $workflow->participantes->count() }} miembro(s)
                            </span>
                        </div>
                        
                        @if($workflow->participantes->count() == 0)
                            <!-- SI NO HAY MIEMBROS, MOSTRAR BÚSQUEDA -->
                            <div class="team-empty">
                                <i class="fas fa-user-plus"></i>
                                <p>Este proyecto aún no tiene miembros en el equipo</p>
                                
                                <div class="search-container">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="user-search" placeholder="Buscar usuario por nombre o email...">
                                    <div class="search-results" id="search-results"></div>
                                </div>
                            </div>
                        @else
                            <!-- SI HAY MIEMBROS, MOSTRAR LISTA ACTUAL -->
                            <div class="add-member-section">
                                <div class="add-member-header">
                                    <h5><i class="fas fa-user-plus"></i> Agregar Nuevo Miembro</h5>
                                    <button type="button" class="toggle-add-member" id="toggle-add-member">
                                        <i class="fas fa-plus"></i> Agregar
                                    </button>
                                </div>
                                <div class="search-container" id="add-member-search" style="display: none;">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="user-search-2" placeholder="Buscar usuario por nombre o email...">
                                    <div class="search-results" id="search-results-2"></div>
                                </div>
                            </div>
                            
                            <div class="team-list" id="team-list">
                                @foreach($workflow->participantes as $participant)
                                    <div class="team-member" data-user-id="{{ $participant->id }}">
                                        <div class="user-info">
                                            <div class="user-name">{{ $participant->name }}</div>
                                            <div class="user-email">{{ $participant->email }}</div>
                                        </div>
                                        <button type="button" class="remove-btn" onclick="removeMember({{ $participant->id }})">
                                            <i class="fas fa-times"></i> Quitar
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        @if($workflow->participantes->count() > 0)
                            <div class="team-actions">
                                <button type="button" id="btn-save-team" class="btn-save-team">
                                    <i class="fas fa-save"></i> Actualizar Equipo
                                </button>
                            </div>
                        @endif
                    </div>

                    <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #bfdbfe; border-radius: 20px; padding: 1.5rem; margin-top: 2.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                        <h4 style="margin:0 0 0.75rem 0; color:#1e40af; font-size: 1rem; font-weight: 800; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-fingerprint"></i> Auditoría
                        </h4>
                        <div style="font-size: 0.85rem; color: #1e40af; display: flex; flex-direction: column; gap: 0.5rem;">
                            <p style="margin: 0; display: flex; justify-content: space-between;"><strong>Registro:</strong> <span>{{ $workflow->created_at->format('d/m/Y H:i') }}</span></p>
                            <p style="margin: 0; display: flex; justify-content: space-between;"><strong>Último Cambio:</strong> <span>{{ $workflow->updated_at->diffForHumans() }}</span></p>
                            <p style="margin: 0; display: flex; justify-content: space-between;"><strong>Versión ID:</strong> <span>{{ strtoupper(substr(md5($workflow->id), 0, 8)) }}</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('flujo.workflows.show', $workflow) }}'">
                    <i class="fas fa-times"></i> Cancelar cambios
                </button>
                <button type="submit" class="btn btn-primary" id="btn-submit">
                    <i class="fas fa-save"></i> Guardar y Sincronizar
                </button>
            </div>
        </form>
    </div>

    <div id="toast" class="notification">
        <i id="toast-icon" class="fas fa-info-circle"></i>
        <span id="toast-message" style="font-weight: 600;"></span>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.config-tab');
            const contents = document.querySelectorAll('.config-content');
            const area = document.getElementById('configuracion');
            const jsonContainer = document.getElementById('json-container');

            // --- Switch de Pestañas ---
            function switchTab(target) {
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));
                document.querySelector(`[data-tab="${target}"]`).classList.add('active');
                document.getElementById(`${target}-tab`).classList.add('active');
            }

            tabs.forEach(tab => {
                tab.addEventListener('click', () => switchTab(tab.dataset.tab));
            });

            // --- Plantillas Predeterminadas ---
            window.applyTemplate = function(type) {
                const templates = {
                    notif: { "canales": ["email", "browser"], "frecuencia": "real-time", "logs": true },
                    high_prio: { "escalamiento": "inmediato", "notificar_admin": true, "sla": "2h" },
                    approval: { "steps": 2, "required_roles": ["manager", "director"], "blocking": true },
                    clear: {}
                };

                // Pequeño delay para UX
                showToast('🚀 Cargando configuración...', 'success');
                
                setTimeout(() => {
                    area.value = JSON.stringify(templates[type], null, 4);
                    switchTab('json');
                    jsonContainer.classList.add('flash-success');
                    setTimeout(() => jsonContainer.classList.remove('flash-success'), 1000);
                    showToast('✅ Módulo técnico actualizado', 'success');
                }, 300);
            };

            // --- Herramientas JSON ---
            window.formatJson = function() {
                try {
                    if(!area.value.trim()){
                        showToast('ℹ️ El editor está vacío', 'secondary');
                        return;
                    }
                    area.value = JSON.stringify(JSON.parse(area.value), null, 4);
                    showToast('✨ Código indentado correctamente', 'success');
                } catch(e) { 
                    showToast('❌ Error de sintaxis en el JSON', 'danger'); 
                }
            };

            window.validateJson = function() {
                try {
                    JSON.parse(area.value);
                    showToast('💎 Estructura JSON perfecta', 'success');
                } catch(e) { 
                    showToast('❌ JSON Inválido: ' + e.message, 'danger'); 
                }
            };

            // --- Toast Notification System ---
            function showToast(msg, type) {
                const toast = document.getElementById('toast');
                const icon = document.getElementById('toast-icon');
                document.getElementById('toast-message').textContent = msg;
                
                toast.style.borderLeftColor = type === 'success' ? '#10b981' : (type === 'danger' ? '#ef4444' : '#64748b');
                icon.className = type === 'success' ? 'fas fa-check-circle' : 'fas fa-info-circle';
                icon.style.color = type === 'success' ? '#10b981' : '#ef4444';
                
                toast.classList.add('show');
                setTimeout(() => toast.classList.remove('show'), 3500);
            }

            // --- Validaciones de Fecha en Tiempo Real ---
            const startDate = document.getElementById('fecha_inicio');
            const endDate = document.getElementById('fecha_fin');

            function checkDates() {
                if (startDate.value && endDate.value && endDate.value < startDate.value) {
                    endDate.classList.add('date-error');
                } else {
                    endDate.classList.remove('date-error');
                }
            }

            startDate.addEventListener('change', checkDates);
            endDate.addEventListener('change', checkDates);

            // --- Envió de Formulario ---
            document.getElementById('workflow-form').onsubmit = function() {
                const start = startDate.value;
                const end = endDate.value;
                
                if(start && end && end < start) {
                    showToast('⚠️ Error: El cierre es anterior al inicio', 'danger');
                    endDate.focus();
                    return false;
                }
                
                const btn = document.getElementById('btn-submit');
                btn.style.opacity = '0.7';
                btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Procesando cambios...';
                return true;
            };
            
            // --- Funcionalidad para el equipo ---
            const users = @json($users);
            const currentTeamMembers = @json($workflow->participantes->pluck('id'));
            let selectedUsers = [...currentTeamMembers];
            
            // Función para buscar usuarios
            function searchUsers(query, excludeIds = []) {
                if (!query || query.length < 2) return [];
                
                const filtered = users.filter(user => {
                    const isExcluded = excludeIds.includes(user.id);
                    const matchesSearch = user.name.toLowerCase().includes(query.toLowerCase()) || 
                                        user.email.toLowerCase().includes(query.toLowerCase());
                    return !isExcluded && matchesSearch;
                });
                
                return filtered.slice(0, 5); // Limitar a 5 resultados
            }
            
            // Función para mostrar resultados de búsqueda
            function showSearchResults(results, containerId) {
                const container = document.getElementById(containerId);
                
                if (results.length === 0) {
                    container.innerHTML = '<div class="search-result-item">No se encontraron usuarios</div>';
                } else {
                    container.innerHTML = results.map(user => {
                        const initials = user.name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
                        return `
                            <div class="search-result-item" onclick="addUserToTeam(${user.id})">
                                <div class="search-result-avatar">${initials}</div>
                                <div class="search-result-info">
                                    <div class="search-result-name">${user.name}</div>
                                    <div class="search-result-email">${user.email}</div>
                                </div>
                            </div>
                        `;
                    }).join('');
                }
                
                container.classList.add('active');
            }
            
            // Configurar búsqueda simplificada
            function setupSimpleSearch(inputId, resultsId) {
                const searchInput = document.getElementById(inputId);
                const resultsContainer = document.getElementById(resultsId);
                
                if (!searchInput) return;
                
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim();
                    
                    if (query.length < 2) {
                        resultsContainer.classList.remove('active');
                        return;
                    }
                    
                    // Búsqueda inmediata sin debounce
                    const results = searchUsers(query, selectedUsers);
                    showSearchResults(results, resultsId);
                });
                
                // Cerrar resultados al hacer clic fuera
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.search-container')) {
                        resultsContainer.classList.remove('active');
                    }
                });
            }
            
            // Agregar usuario al equipo
            window.addUserToTeam = function(userId) {
                if (!selectedUsers.includes(userId)) {
                    selectedUsers.push(userId);
                    updateTeamDisplay();
                    showToast('✅ Usuario agregado al equipo', 'success');
                }
                
                // Limpiar búsqueda
                document.getElementById('user-search').value = '';
                document.getElementById('user-search-2').value = '';
                document.getElementById('search-results').classList.remove('active');
                document.getElementById('search-results-2').classList.remove('active');
            };
            
            // Quitar usuario del equipo
            window.removeMember = function(userId) {
                selectedUsers = selectedUsers.filter(id => id !== userId);
                updateTeamDisplay();
                showToast('🗑️ Usuario eliminado del equipo', 'success');
            };
            
            // Actualizar la visualización del equipo
            function updateTeamDisplay() {
                const teamList = document.getElementById('team-list');
                const teamCount = document.getElementById('team-count');
                
                if (selectedUsers.length === 0) {
                    // Si no hay miembros, recargar la página para mostrar la vista vacía
                    window.location.reload();
                    return;
                }
                
                // Actualizar contador
                teamCount.textContent = `${selectedUsers.length} miembro(s)`;
                
                // Actualizar lista de miembros
                const membersHtml = selectedUsers.map(userId => {
                    const user = users.find(u => u.id === userId);
                    if (!user) return '';
                    
                    return `
                        <div class="team-member" data-user-id="${user.id}">
                            <div class="user-info">
                                <div class="user-name">${user.name}</div>
                                <div class="user-email">${user.email}</div>
                            </div>
                            <button type="button" class="remove-btn" onclick="removeMember(${user.id})">
                                <i class="fas fa-times"></i> Quitar
                            </button>
                        </div>
                    `;
                }).join('');
                
                teamList.innerHTML = membersHtml;
            }
            
            // Configurar búsquedas
            setupSimpleSearch('user-search', 'search-results');
            setupSimpleSearch('user-search-2', 'search-results-2');
            
            // Toggle para mostrar/ocultar búsqueda de agregar miembros
            const toggleAddMember = document.getElementById('toggle-add-member');
            if (toggleAddMember) {
                toggleAddMember.addEventListener('click', function() {
                    const searchContainer = document.getElementById('add-member-search');
                    if (searchContainer.style.display === 'none') {
                        searchContainer.style.display = 'block';
                        this.innerHTML = '<i class="fas fa-minus"></i> Cancelar';
                        // Enfocar el campo de búsqueda
                        setTimeout(() => {
                            document.getElementById('user-search-2').focus();
                        }, 100);
                    } else {
                        searchContainer.style.display = 'none';
                        this.innerHTML = '<i class="fas fa-plus"></i> Agregar';
                        document.getElementById('user-search-2').value = '';
                        document.getElementById('search-results-2').classList.remove('active');
                    }
                });
            }
            
            // Evento para guardar el equipo
            const saveTeamBtn = document.getElementById('btn-save-team');
            if (saveTeamBtn) {
                saveTeamBtn.addEventListener('click', function() {
                    // Mostrar estado de carga
                    saveTeamBtn.classList.add('loading');
                    saveTeamBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Actualizando...';
                    
                    // Enviar petición AJAX
                    fetch(`{{ route('flujo.workflows.updateTeam', $workflow) }}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            participantes: selectedUsers
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast(data.message, 'success');
                        } else {
                            showToast(data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        showToast('Error de conexión al actualizar el equipo', 'danger');
                        console.error('Error:', error);
                    })
                    .finally(() => {
                        // Restaurar estado del botón
                        saveTeamBtn.classList.remove('loading');
                        saveTeamBtn.innerHTML = '<i class="fas fa-save"></i> Actualizar Equipo';
                    });
                });
            }
        });
    </script>
</x-base-layout>