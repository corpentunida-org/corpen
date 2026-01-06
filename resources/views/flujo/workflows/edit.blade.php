<x-base-layout>
    <style>
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
        }

        .form-container {
            max-width: 1150px;
            margin: 2rem auto;
            background: var(--bg-card);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }

        .form-header h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-main);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 3rem;
        }

        @media (max-width: 1000px) {
            .form-grid { grid-template-columns: 1fr; }
        }

        .form-group { margin-bottom: 1.5rem; }

        .form-group label {
            display: block;
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 0.6rem;
            color: var(--text-main);
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background-color: #fcfcfd;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        /* Tabs de Configuración */
        .config-tabs { 
            display: flex; 
            gap: 10px;
            margin-bottom: 1.5rem; 
            background: #f1f5f9;
            padding: 6px;
            border-radius: 14px;
            width: fit-content;
        }
        
        .config-tab {
            padding: 0.6rem 1.2rem;
            background: transparent;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .config-tab.active { 
            background: white; 
            color: var(--primary); 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .config-content { display: none; }
        .config-content.active { display: block; animation: fadeIn 0.3s ease; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Tarjetas de Plantilla */
        .template-card {
            cursor: pointer;
            border: 2px solid var(--border-color);
            padding: 1.25rem;
            border-radius: 16px;
            background: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-align: left;
        }

        .template-card:hover {
            border-color: var(--primary);
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.08);
        }

        .template-card i {
            font-size: 1.8rem;
            margin-bottom: 0.75rem;
            display: block;
        }

        /* Editor JSON */
        .json-editor-container { 
            border-radius: 14px; 
            overflow: hidden; 
            border: 2px solid var(--border-color);
            transition: all 0.5s;
        }
        
        .json-editor { 
            font-family: 'Fira Code', monospace; 
            background: var(--json-bg); 
            color: #a5f3fc; 
            border: none; 
            padding: 1.25rem; 
            font-size: 0.85rem; 
            width: 100%; 
            line-height: 1.6;
        }
        
        .json-editor-actions { background: #0f172a; padding: 0.75rem 1rem; display: flex; gap: 0.75rem; }
        
        .btn-small { 
            background: rgba(255, 255, 255, 0.1); 
            color: white; 
            border: none; 
            padding: 0.4rem 0.8rem; 
            border-radius: 8px; 
            font-size: 0.75rem; 
            cursor: pointer;
            font-weight: 600;
        }

        .btn-small:hover { background: rgba(255, 255, 255, 0.2); }

        /* Efectos Visuales */
        .flash-success { animation: flash-green 1s ease-out; }
        @keyframes flash-green {
            0% { border-color: var(--border-color); }
            50% { border-color: var(--success); box-shadow: 0 0 20px rgba(16, 185, 129, 0.3); }
            100% { border-color: var(--border-color); }
        }

        .checkbox-card {
            background: #f8fafc;
            padding: 1.25rem;
            border-radius: 14px;
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
            border: 1px solid transparent;
            transition: 0.2s;
        }
        
        .checkbox-card:hover { border-color: var(--primary); background: #f1f5f9; }

        .form-actions { 
            margin-top: 3rem; 
            padding-top: 2rem; 
            border-top: 1px solid var(--border-color); 
            display: flex; 
            justify-content: flex-end; 
            gap: 1rem; 
        }

        .btn { padding: 0.8rem 1.8rem; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.6rem; border: none; transition: 0.2s; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-hover); transform: translateY(-2px); }
        
        .notification { 
            position: fixed; bottom: 2rem; right: 2rem; padding: 1rem 1.5rem; 
            border-radius: 14px; background: #1e293b; color: white; z-index: 9999; 
            transform: translateY(150px); transition: 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .notification.show { transform: translateY(0); }
    </style>

    <div class="form-container">
        <header class="form-header">
            <h1><i class="fas fa-edit" style="color: var(--primary)"></i> Editar Proyecto</h1>
            <a href="{{ route('flujo.workflows.index') }}" style="text-decoration:none; color: var(--secondary); font-weight: 600;">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </header>

        <form action="{{ route('flujo.workflows.update', $workflow) }}" method="POST" id="workflow-form">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-column">
                    <div class="form-group">
                        <label for="nombre">Nombre del Proyecto <span style="color:var(--danger)">*</span></label>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $workflow->nombre) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="4">{{ old('descripcion', $workflow->descripcion) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Configuración de Comportamiento</label>
                        <div class="config-tabs">
                            <button type="button" class="config-tab active" data-tab="easy">
                                <i class="fas fa-wand-magic-sparkles"></i> Modo Fácil
                            </button>
                            <button type="button" class="config-tab" data-tab="json">
                                <i class="fas fa-code"></i> Modo Experto
                            </button>
                        </div>

                        <div class="config-content active" id="easy-tab">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="template-card" onclick="applyTemplate('notif')">
                                    <i class="fas fa-envelope-open-text" style="color: var(--primary)"></i>
                                    <strong>Notificaciones</strong>
                                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px;">Activa emails automáticos y alertas diarias.</p>
                                </div>
                                <div class="template-card" onclick="applyTemplate('high_prio')">
                                    <i class="fas fa-bolt-lightning" style="color: var(--warning)"></i>
                                    <strong>Prioridad Crítica</strong>
                                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px;">Modo de alta urgencia con aviso a jefes.</p>
                                </div>
                                <div class="template-card" onclick="applyTemplate('approval')">
                                    <i class="fas fa-user-check" style="color: var(--success)"></i>
                                    <strong>Aprobaciones</strong>
                                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px;">Añade cadena de mando obligatoria.</p>
                                </div>
                                <div class="template-card" onclick="applyTemplate('clear')">
                                    <i class="fas fa-eraser" style="color: var(--secondary)"></i>
                                    <strong>Limpiar</strong>
                                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px;">Borra todos los ajustes técnicos.</p>
                                </div>
                            </div>
                        </div>

                        <div class="config-content" id="json-tab">
                            <div class="json-editor-container" id="json-container">
                                <textarea id="configuracion" name="configuracion" rows="10" class="json-editor">{{ old('configuracion', $workflow->configuracion ? json_encode($workflow->configuracion, JSON_PRETTY_PRINT) : '') }}</textarea>
                                <div class="json-editor-actions">
                                    <button type="button" class="btn-small" onclick="formatJson()"><i class="fas fa-align-left"></i> Formatear</button>
                                    <button type="button" class="btn-small" onclick="validateJson()"><i class="fas fa-check"></i> Validar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-column">
                    <div class="form-group">
                        <label for="creado_por">Autor Original <span style="color:var(--danger)">*</span></label>
                        <select name="creado_por" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('creado_por', $workflow->creado_por) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="asignado_a">Responsable Asignado</label>
                        <select name="asignado_a">
                            <option value="">-- Sin asignar --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('asignado_a', $workflow->asignado_a) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Estado</label>
                            <select name="estado" required>
                                @foreach($estados as $val => $lab)
                                    <option value="{{ $val }}" {{ old('estado', $workflow->estado) == $val ? 'selected' : '' }}>{{ $lab }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Prioridad</label>
                            <select name="prioridad" required>
                                @foreach($prioridades as $val => $lab)
                                    <option value="{{ $val }}" {{ old('prioridad', $workflow->prioridad) == $val ? 'selected' : '' }}>{{ $lab }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio', $workflow->fecha_inicio?->format('Y-m-d')) }}">
                        </div>
                        <div class="form-group">
                            <label>Fecha Cierre</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin', $workflow->fecha_fin?->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.8rem; margin-top: 1rem;">
                        <label class="checkbox-card">
                            <input type="checkbox" name="activo" value="1" {{ old('activo', $workflow->activo) ? 'checked' : '' }} style="width: 20px; height: 20px;">
                            <div><strong>Activo</strong><br><small>Habilitado para ejecución</small></div>
                        </label>
                        <label class="checkbox-card">
                            <input type="checkbox" name="es_plantilla" value="1" {{ old('es_plantilla', $workflow->es_plantilla) ? 'checked' : '' }} style="width: 20px; height: 20px;">
                            <div><strong>Es Plantilla</strong><br><small>Disponible para clonación</small></div>
                        </label>
                    </div>

                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 16px; padding: 1.25rem; margin-top: 2rem;">
                        <h4 style="margin:0 0 0.5rem 0; color:#1e40af; font-size: 0.9rem;"><i class="fas fa-history"></i> Trazabilidad</h4>
                        <div style="font-size: 0.8rem; color: #1e40af;">
                            <p style="margin: 4px 0;"><strong>Creado:</strong> {{ $workflow->created_at->format('d/m/Y H:i') }}</p>
                            <p style="margin: 4px 0;"><strong>Modificado:</strong> {{ $workflow->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="history.back()">Descartar</button>
                <button type="submit" class="btn btn-primary" id="btn-submit">
                    <i class="fas fa-save"></i> Actualizar Proyecto
                </button>
            </div>
        </form>
    </div>

    <div id="toast" class="notification">
        <i id="toast-icon" class="fas fa-info-circle"></i>
        <span id="toast-message"></span>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.config-tab');
            const contents = document.querySelectorAll('.config-content');
            const area = document.getElementById('configuracion');
            const jsonContainer = document.getElementById('json-container');

            // --- Pestañas ---
            function switchTab(target) {
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));
                document.querySelector(`[data-tab="${target}"]`).classList.add('active');
                document.getElementById(`${target}-tab`).classList.add('active');
            }

            tabs.forEach(tab => {
                tab.addEventListener('click', () => switchTab(tab.dataset.tab));
            });

            // --- Plantillas ---
            window.applyTemplate = function(type) {
                const templates = {
                    notif: { "email": true, "avisos": "diarios", "urgencia": "media" },
                    high_prio: { "modo_critico": true, "notificar_ceo": true, "timeout": "1h" },
                    approval: { "requiere_firma": true, "pasos": ["Jefe Area", "Gerente"] },
                    clear: {}
                };

                area.value = JSON.stringify(templates[type], null, 4);

                // Feedback visual
                setTimeout(() => {
                    switchTab('json');
                    jsonContainer.classList.add('flash-success');
                    setTimeout(() => jsonContainer.classList.remove('flash-success'), 1000);
                    showToast('✅ Ajustes aplicados correctamente', 'success');
                }, 200);
            };

            // --- Utilidades JSON ---
            window.formatJson = function() {
                try {
                    if(!area.value.trim()) return;
                    area.value = JSON.stringify(JSON.parse(area.value), null, 4);
                    showToast('✨ Código ordenado', 'success');
                } catch(e) { showToast('❌ El código tiene errores', 'danger'); }
            };

            window.validateJson = function() {
                try {
                    JSON.parse(area.value);
                    showToast('✅ JSON listo y válido', 'success');
                } catch(e) { showToast('❌ Error: ' + e.message, 'danger'); }
            };

            function showToast(msg, type) {
                const toast = document.getElementById('toast');
                const icon = document.getElementById('toast-icon');
                document.getElementById('toast-message').textContent = msg;
                
                icon.className = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
                icon.style.color = type === 'success' ? '#10b981' : '#ef4444';
                
                toast.classList.add('show');
                setTimeout(() => toast.classList.remove('show'), 3500);
            }

            // --- Validación Global ---
            document.getElementById('workflow-form').onsubmit = function() {
                const start = document.getElementById('fecha_inicio').value;
                const end = document.getElementById('fecha_fin').value;
                if(start && end && end < start) {
                    showToast('La fecha de cierre debe ser posterior al inicio', 'danger');
                    return false;
                }
                document.getElementById('btn-submit').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
                return true;
            };
        });
    </script>
</x-base-layout>