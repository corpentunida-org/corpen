<x-base-layout>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --secondary: #64748b;
            --danger: #ef4444;
            --warning: #f59e0b;
            --success: #10b981;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #1e293b;
            --json-bg: #1e293b;
        }

        .form-container {
            max-width: 1100px;
            margin: 2rem auto;
            background: var(--bg-card);
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }

        .form-header h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 2.5rem;
        }

        @media (max-width: 900px) {
            .form-grid { grid-template-columns: 1fr; }
        }

        .form-group { margin-bottom: 1.5rem; }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-main);
            font-size: 0.9rem;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        /* Configuración Intuitiva */
        .config-mode-selector {
            display: flex;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 10px;
            margin-bottom: 1rem;
            width: fit-content;
        }

        .mode-btn {
            border: none;
            padding: 6px 16px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .mode-btn.active { background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: var(--primary); }

        .json-editor-container { border-radius: 8px; overflow: hidden; background: var(--json-bg); }
        .json-editor { font-family: 'Fira Code', monospace; color: #a5f3fc; background: transparent; border: none; padding: 1rem; font-size: 0.85rem; width: 100%; resize: vertical; }
        .json-actions { background: #0f172a; padding: 0.5rem 1rem; display: flex; gap: 0.5rem; }

        .template-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: white;
            border: 1px solid var(--border-color);
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: 0.2s;
        }
        .template-btn:hover { border-color: var(--primary); color: var(--primary); }

        .checkbox-card {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
            border: 1px solid transparent;
        }
        .checkbox-card:hover { border-color: var(--primary); }

        .form-actions {
            margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);
            display: flex; justify-content: flex-end; gap: 1rem;
        }

        .btn { padding: 0.8rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer; text-decoration: none; border: none; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-secondary { background: #e2e8f0; color: var(--text-main); }
    </style>

    <div class="form-container">
        <header class="form-header">
            <h1><i class="fas fa-magic" style="color: var(--primary)"></i> Crear Nuevo Workflow</h1>
            <a href="{{ route('flujo.workflows.index') }}" style="color: var(--secondary); text-decoration: none;">
                <i class="fas fa-times"></i> Salir
            </a>
        </header>

        @if ($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fecaca; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                <ul style="margin: 0; color: #991b1b; font-size: 0.9rem; list-style: none; padding: 0;">
                    @foreach ($errors->all() as $error)
                        <li><i class="fas fa-exclamation-triangle"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('flujo.workflows.store') }}" method="POST" id="workflow-create-form">
            @csrf

            <div class="form-grid">
                <div class="form-column">
                    <div class="form-group">
                        <label for="nombre">Nombre del Proyecto <span style="color:var(--danger)">*</span></label>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" placeholder="Ej: Auditoría Anual 2024" required>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="4" placeholder="¿De qué trata este proceso?">{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Configuración de Instrucciones (JSON)</label>
                        
                        <div class="config-mode-selector">
                            <button type="button" class="mode-btn active" id="btn-easy" onclick="toggleConfigMode('easy')">Modo Asistido</button>
                            <button type="button" class="mode-btn" id="btn-expert" onclick="toggleConfigMode('expert')">Modo Experto</button>
                        </div>

                        <div id="panel-easy" style="background: #f8fafc; border: 1px solid var(--border-color); padding: 1.5rem; border-radius: 12px;">
                            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">Carga una base de instrucciones con un clic:</p>
                            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                <button type="button" class="template-btn" onclick="applyTemplate('notif')"><i class="fas fa-bell"></i> Con Notificaciones</button>
                                <button type="button" class="template-btn" onclick="applyTemplate('fast')"><i class="fas fa-bolt"></i> Vía Rápida</button>
                                <button type="button" class="template-btn" onclick="applyTemplate('empty')"><i class="fas fa-eraser"></i> Limpiar</button>
                            </div>
                        </div>

                        <div id="panel-expert" style="display: none;">
                            <div class="json-editor-container">
                                <textarea id="configuracion" name="configuracion" rows="8" class="json-editor" placeholder='{"clave": "valor"}'>{{ old('configuracion') }}</textarea>
                                <div class="json-actions">
                                    <button type="button" class="btn-small" onclick="processJson('format')">Limpiar Código</button>
                                    <button type="button" class="btn-small" onclick="processJson('validate')">Validar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-column">
                    <div class="form-group">
                        <label for="creado_por">Autor <span style="color:var(--danger)">*</span></label>
                        <select name="creado_por" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('creado_por', auth()->id()) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="asignado_a">Asignado a (Responsable)</label>
                        <select name="asignado_a">
                            <option value="">-- Sin asignar --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('asignado_a') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Estado</label>
                            <select name="estado" required>
                                @foreach($estados as $val => $lab)
                                    <option value="{{ $val }}" {{ old('estado') == $val ? 'selected' : '' }}>{{ $lab }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Prioridad</label>
                            <select name="prioridad" required>
                                @foreach($prioridades as $val => $lab)
                                    <option value="{{ $val }}" {{ old('prioridad', 'media') == $val ? 'selected' : '' }}>{{ $lab }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio', date('Y-m-d')) }}">
                        </div>
                        <div class="form-group">
                            <label>Fecha Fin</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin') }}">
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem;">
                        <label class="checkbox-card">
                            <input type="checkbox" name="activo" value="1" {{ old('activo', '1') ? 'checked' : '' }}>
                            <div><strong>Activo</strong><br><small>Disponible inmediatamente</small></div>
                        </label>
                        <label class="checkbox-card">
                            <input type="checkbox" name="es_plantilla" value="1" {{ old('es_plantilla') ? 'checked' : '' }}>
                            <div><strong>Es Plantilla</strong><br><small>Base para futuros flujos</small></div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('flujo.workflows.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary" id="btn-submit">
                    <i class="fas fa-rocket"></i> Lanzar Workflow
                </button>
            </div>
        </form>
    </div>

    <script>
        function toggleConfigMode(mode) {
            const isEasy = mode === 'easy';
            document.getElementById('panel-easy').style.display = isEasy ? 'block' : 'none';
            document.getElementById('panel-expert').style.display = isEasy ? 'none' : 'block';
            document.getElementById('btn-easy').classList.toggle('active', isEasy);
            document.getElementById('btn-expert').classList.toggle('active', !isEasy);
        }

        function applyTemplate(type) {
            const area = document.getElementById('configuracion');
            const templates = {
                notif: { "notificar_por_email": true, "aviso_retraso": true, "frecuencia": "diaria" },
                fast: { "pasos_aprobacion": 1, "ignorar_feriados": true, "modo_turbo": true },
                empty: {}
            };
            area.value = JSON.stringify(templates[type], null, 4);
            toggleConfigMode('expert'); // Mostramos el código resultante
        }

        function processJson(action) {
            const area = document.getElementById('configuracion');
            try {
                if(!area.value.trim()) return;
                const obj = JSON.parse(area.value);
                if(action === 'format') area.value = JSON.stringify(obj, null, 4);
                if(action === 'validate') alert("¡Estructura perfecta!");
            } catch(e) { alert("Hay un error en tu código: " + e.message); }
        }

        document.getElementById('workflow-create-form').onsubmit = function() {
            const start = document.getElementById('fecha_inicio').value;
            const end = document.getElementById('fecha_fin').value;
            if(start && end && end < start) {
                alert("La fecha de cierre debe ser posterior al inicio.");
                return false;
            }
            document.getElementById('btn-submit').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            return true;
        };
    </script>
</x-base-layout>