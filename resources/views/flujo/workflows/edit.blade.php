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

                        @php
                            $hasMembers = $workflow->participantes->count() > 0;
                        @endphp

                        <div class="team-empty" style="display: {{ $hasMembers ? 'none' : 'block' }};">
                            <i class="fas fa-user-plus"></i>
                            <p>Este proyecto aún no tiene miembros en el equipo</p>
                            
                            <div class="search-container">
                                <i class="fas fa-search"></i>
                                <input type="text" id="user-search" placeholder="Buscar usuario por nombre o email...">
                                <div class="search-results" id="search-results"></div>
                            </div>
                        </div>

                        <div class="add-member-section" style="display: {{ $hasMembers ? 'block' : 'none' }};">
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

                        <div class="team-list" id="team-list" style="display: {{ $hasMembers ? 'block' : 'none' }};">
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

                        <div class="team-actions" style="display: flex; justify-content: flex-end; margin-top: 1rem;">
                            <button type="button" id="btn-save-team" class="btn-save-team">
                                <i class="fas fa-save"></i> Actualizar Equipo
                            </button>
                        </div>

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
            
            // ==========================================
            // CONFIGURACIÓN INICIAL Y TABS
            // ==========================================
            const tabs = document.querySelectorAll('.config-tab');
            const contents = document.querySelectorAll('.config-content');
            const area = document.getElementById('configuracion');
            const jsonContainer = document.getElementById('json-container');

            // Switch de Pestañas
            function switchTab(target) {
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));
                document.querySelector(`[data-tab="${target}"]`).classList.add('active');
                document.getElementById(`${target}-tab`).classList.add('active');
            }
            tabs.forEach(tab => tab.addEventListener('click', () => switchTab(tab.dataset.tab)));

            // Notificaciones Toast (Global para usarlo en todos lados)
            window.showToast = function(msg, type) {
                const toast = document.getElementById('toast');
                const icon = document.getElementById('toast-icon');
                document.getElementById('toast-message').textContent = msg;
                
                toast.style.borderLeftColor = type === 'success' ? '#10b981' : (type === 'danger' ? '#ef4444' : '#64748b');
                icon.className = type === 'success' ? 'fas fa-check-circle' : 'fas fa-info-circle';
                icon.style.color = type === 'success' ? '#10b981' : '#ef4444';
                
                toast.classList.add('show');
                setTimeout(() => toast.classList.remove('show'), 3500);
            }

            // ==========================================
            // LÓGICA DEL EQUIPO (AQUÍ ESTÁ LA MAGIA)
            // ==========================================
            
            const allUsers = @json($users); 
            let selectedIds = @json($workflow->participantes->pluck('id')).map(Number);

            // Referencias al HTML
            const dom = {
                emptyState: document.querySelector('.team-empty'),
                listState: document.querySelector('.team-list'),
                addSection: document.querySelector('.add-member-section'),
                countLabel: document.getElementById('team-count'),
                // Buscadores
                searchEmpty: document.getElementById('user-search'),
                searchList: document.getElementById('user-search-2'),
                resultsEmpty: document.getElementById('search-results'),
                resultsList: document.getElementById('search-results-2')
            };

            // 1. Agregar Usuario
            window.addUserToTeam = function(userId) {
                userId = Number(userId);
                if (!selectedIds.includes(userId)) {
                    selectedIds.push(userId);
                    refreshTeamUI();
                    showToast('✅ Miembro agregado', 'success');
                } else {
                    showToast('⚠️ Ya está en el equipo', 'warning');
                }
                clearSearch();
            };

            // 2. Eliminar Usuario
            window.removeMember = function(userId) {
                userId = Number(userId);
                selectedIds = selectedIds.filter(id => id !== userId);
                refreshTeamUI();
                showToast('🗑️ Miembro eliminado', 'secondary');
            };

            // 3. ACTUALIZAR VISTA (LA CLAVE DEL BOTÓN)
            function refreshTeamUI() {
                // Actualizar texto contador
                if(dom.countLabel) dom.countLabel.textContent = `${selectedIds.length} miembro(s)`;

                if (selectedIds.length === 0) {
                    // Si está vacío: Mostramos icono grande, ocultamos lista
                    if(dom.emptyState) dom.emptyState.style.display = 'block';
                    if(dom.listState) dom.listState.style.display = 'none';
                    if(dom.addSection) dom.addSection.style.display = 'none';
                    // NOTA: EL BOTÓN DE GUARDAR YA NO SE OCULTA AQUÍ
                } else {
                    // Si hay gente: Ocultamos icono grande, mostramos lista
                    if(dom.emptyState) dom.emptyState.style.display = 'none';
                    if(dom.listState) {
                        dom.listState.style.display = 'block';
                        renderListHTML();
                    }
                    if(dom.addSection) dom.addSection.style.display = 'block';
                }
            }

            // Generar HTML de la lista
            function renderListHTML() {
                const html = selectedIds.map(id => {
                    const user = allUsers.find(u => u.id === id);
                    if (!user) return '';
                    return `
                        <div class="team-member">
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
                dom.listState.innerHTML = html;
            }

            // Limpiar inputs
            function clearSearch() {
                if(dom.searchEmpty) dom.searchEmpty.value = '';
                if(dom.searchList) dom.searchList.value = '';
                if(dom.resultsEmpty) dom.resultsEmpty.classList.remove('active');
                if(dom.resultsList) dom.resultsList.classList.remove('active');
            }

            // Configuración de Buscadores
            function setupSearch(inputId, resultsId) {
                const input = document.getElementById(inputId);
                const resultsBox = document.getElementById(resultsId);
                if(!input) return;

                input.addEventListener('input', function(e) {
                    const query = e.target.value.toLowerCase();
                    if (query.length < 2) {
                        resultsBox.classList.remove('active');
                        return;
                    }
                    // Filtro: Coincide texto Y NO está seleccionado
                    const matches = allUsers.filter(u => {
                        const alreadyInTeam = selectedIds.includes(u.id);
                        const matchesText = (u.name && u.name.toLowerCase().includes(query)) || 
                                            (u.email && u.email.toLowerCase().includes(query));
                        return !alreadyInTeam && matchesText;
                    }).slice(0, 5);

                    if (matches.length > 0) {
                        resultsBox.innerHTML = matches.map(u => {
                            const initials = u.name.substring(0,2).toUpperCase();
                            return `
                                <div class="search-result-item" onclick="addUserToTeam(${u.id})">
                                    <div class="search-result-avatar">${initials}</div>
                                    <div class="search-result-info">
                                        <div class="search-result-name">${u.name}</div>
                                        <div class="search-result-email">${u.email}</div>
                                    </div>
                                </div>
                            `;
                        }).join('');
                        resultsBox.classList.add('active');
                    } else {
                        resultsBox.innerHTML = '<div class="search-result-item">No encontrado</div>';
                        resultsBox.classList.add('active');
                    }
                });

                document.addEventListener('click', (e) => {
                    if (!e.target.closest('.search-container')) resultsBox.classList.remove('active');
                });
            }

            setupSearch('user-search', 'search-results');
            setupSearch('user-search-2', 'search-results-2');
            refreshTeamUI(); // Iniciar vista

            // Botón Toggle pequeño
            const toggleBtn = document.getElementById('toggle-add-member');
            if(toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    const box = document.getElementById('add-member-search');
                    const isHidden = box.style.display === 'none';
                    box.style.display = isHidden ? 'block' : 'none';
                    this.innerHTML = isHidden ? '<i class="fas fa-minus"></i> Cancelar' : '<i class="fas fa-plus"></i> Agregar';
                    if(isHidden) document.getElementById('user-search-2').focus();
                });
            }

            // Guardar Equipo (AJAX)
            const saveTeamBtn = document.getElementById('btn-save-team');
            if (saveTeamBtn) {
                saveTeamBtn.addEventListener('click', function() {
                    saveTeamBtn.classList.add('loading');
                    saveTeamBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Guardando...';
                    
                    fetch(`{{ route('flujo.workflows.updateTeam', $workflow) }}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ participantes: selectedIds })
                    })
                    .then(r => r.json())
                    .then(d => showToast(d.message, d.success ? 'success' : 'danger'))
                    .catch(e => showToast('Error de conexión', 'danger'))
                    .finally(() => {
                        saveTeamBtn.classList.remove('loading');
                        saveTeamBtn.innerHTML = '<i class="fas fa-save"></i> Actualizar Equipo';
                    });
                });
            }
        });
    </script>
</x-base-layout>