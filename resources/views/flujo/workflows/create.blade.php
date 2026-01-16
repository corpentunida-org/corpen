<x-base-layout>
    {{-- 1. Librería Tom Select (CSS) para búsqueda en selectores --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;800&family=Fira+Code&display=swap');

        :root {
            --primary: #4f46e5;
            --primary-soft: #eef2ff;
            --secondary: #64748b;
            --danger: #ef4444;
            --warning: #f59e0b;
            --success: #10b981;
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --json-bg: #0f172a;
            --radius-lg: 20px;
            --radius-md: 12px;
        }

        body { background-color: var(--bg-body); color: var(--text-main); font-family: 'Inter', sans-serif; }

        .app-container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }

        .form-container {
            background: var(--bg-card);
            padding: 40px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }

        /* Header Formulario */
        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .form-header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.02em;
        }

        .exit-link {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .exit-link:hover { color: var(--danger); }

        /* Grid de Formulario */
        .form-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 40px;
        }

        .form-group { margin-bottom: 24px; }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-main);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* --- MEJORA: ESTADOS REQUERIDOS --- */
        .required-label::after {
            content: " *";
            color: var(--danger);
            font-weight: bold;
        }

        .form-group input:required, 
        .form-group select:required, 
        .form-group textarea:required {
            border-left: 4px solid var(--danger);
        }

        /* Efecto al intentar enviar sin completar */
        .was-validated .form-group input:invalid,
        .was-validated .form-group select:invalid {
            border-color: var(--danger);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23ef4444'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23ef4444' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid var(--border-color);
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            color: var(--text-main);
            transition: all 0.25s ease;
            background: #fff;
        }

        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-soft);
        }

        /* Selector de Modo JSON */
        .config-mode-selector {
            display: inline-flex;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 12px;
            margin-bottom: 15px;
        }

        .mode-btn {
            border: none;
            padding: 8px 18px;
            border-radius: 9px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
            color: var(--text-muted);
            background: transparent;
        }

        .mode-btn.active { background: white; color: var(--primary); box-shadow: 0 2px 6px rgba(0,0,0,0.06); }

        /* Editor JSON Premium */
        .json-editor-container { 
            border-radius: var(--radius-md); 
            overflow: hidden; 
            border: 1px solid #334155;
            background: var(--json-bg); 
        }
        .json-editor { 
            font-family: 'Fira Code', monospace; 
            color: #34d399; 
            background: transparent; 
            border: none; 
            padding: 20px; 
            font-size: 0.85rem; 
            width: 100%; 
            resize: vertical; 
            min-height: 200px;
            line-height: 1.6;
        }
        .json-actions { 
            background: #0f172a; 
            padding: 10px 15px; 
            display: flex; 
            gap: 10px; 
            border-top: 1px solid #334155;
        }

        .btn-small {
            background: #334155;
            color: #e2e8f0;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-small:hover { background: #475569; color: white; }

        /* Plantillas */
        .template-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .template-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            background: white;
            border: 1.5px solid var(--border-color);
            padding: 12px;
            border-radius: var(--radius-md);
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            color: var(--text-main);
        }
        .template-btn:hover { border-color: var(--primary); background: var(--primary-soft); color: var(--primary); }

        /* Checkbox Cards */
        .checkbox-container { display: flex; flex-direction: column; gap: 12px; }
        .checkbox-card {
            background: #f8fafc;
            padding: 16px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            border: 1.5px solid transparent;
            transition: 0.2s;
        }
        .checkbox-card:hover { border-color: var(--primary); background: white; }
        .checkbox-card input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; }
        .checkbox-card strong { font-size: 0.9rem; display: block; }
        .checkbox-card small { color: var(--text-muted); font-size: 0.75rem; }

        /* Acciones */
        .form-actions {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        .btn { 
            padding: 12px 28px; 
            border-radius: var(--radius-md); 
            font-weight: 700; 
            font-size: 0.95rem;
            cursor: pointer; 
            text-decoration: none; 
            border: none; 
            display: inline-flex; 
            align-items: center; 
            gap: 10px; 
            transition: 0.2s;
        }
        .btn-primary { background: var(--text-main); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); background: #000; }
        .btn-secondary { background: #f1f5f9; color: var(--text-muted); }
        .btn-secondary:hover { background: #e2e8f0; color: var(--text-main); }

        /* Errores */
        .error-container {
            background: #fff1f2;
            border: 1px solid #fecaca;
            padding: 16px;
            border-radius: var(--radius-md);
            margin-bottom: 30px;
        }
        .error-list { margin: 0; color: #b91c1c; font-size: 0.85rem; list-style: none; padding: 0; display: flex; flex-direction: column; gap: 5px; }

        /* --- ESTILOS PERSONALIZADOS PARA TOM SELECT (Buscador) --- */
        .ts-wrapper { width: 100%; }
        /* Hacemos que el input del buscador se vea igual a tus inputs normales */
        .ts-control {
            border: 1.5px solid var(--border-color) !important;
            border-radius: var(--radius-md) !important;
            padding: 12px 16px !important;
            font-size: 0.95rem !important;
            color: var(--text-main) !important;
            background: #fff !important;
            box-shadow: none !important;
            min-height: auto !important;
        }
        /* Color cuando está seleccionado/enfocado */
        .ts-control.focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px var(--primary-soft) !important;
        }
        /* El menú desplegable */
        .ts-dropdown {
            border-radius: var(--radius-md) !important;
            border: 1px solid var(--border-color) !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
            margin-top: 5px !important;
            overflow: hidden;
        }
        .ts-dropdown .option { padding: 10px 16px; }
        .ts-dropdown .active { background-color: var(--primary-soft); color: var(--primary); font-weight: 600; }


        /* --- RESPONSIVE / MÓVIL --- */
        @media (max-width: 900px) {
            .app-container { margin: 15px auto; padding: 0 15px; }
            .form-container { padding: 25px 20px; }
            .form-header h1 { font-size: 1.4rem; }
            .form-grid { grid-template-columns: 1fr; gap: 0; }
            .form-actions { flex-direction: column-reverse; }
            .btn { width: 100%; justify-content: center; padding: 16px; }
            .template-grid { grid-template-columns: 1fr; }
            .input-row-mobile { display: flex; flex-direction: column; gap: 0; }
            .grid-2-mobile { grid-template-columns: 1fr !important; }
        }
    </style>

    <div class="app-container">
        <div class="form-container">
            <header class="form-header">
                <h1><i class="fas fa-project-diagram" style="color: var(--primary)"></i> Nuevo Workflow</h1>
                <a href="{{ route('flujo.workflows.index') }}" class="exit-link">
                    <i class="fas fa-times-circle"></i> <span>Cerrar</span>
                </a>
            </header>

            @if ($errors->any())
                <div class="error-container">
                    <ul class="error-list">
                        @foreach ($errors->all() as $error)
                            <li><i class="fas fa-exclamation-circle"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('flujo.workflows.store') }}" method="POST" id="workflow-create-form" class="needs-validation" novalidate>
                @csrf

                <div class="form-grid">
                    {{-- Columna Izquierda --}}
                    <div class="form-column">
                        <div class="form-group">
                            <label for="nombre" class="required-label">Nombre del Proyecto</label>
                            <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" placeholder="Ej: Implementación de ERP" required>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción General</label>
                            <textarea id="descripcion" name="descripcion" rows="4" placeholder="Describe el objetivo de este flujo de trabajo...">{{ old('descripcion') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Configuración Avanzada (JSON)</label>
                            
                            <div class="config-mode-selector">
                                <button type="button" class="mode-btn active" id="btn-easy" onclick="toggleConfigMode('easy')">Asistido</button>
                                <button type="button" class="mode-btn" id="btn-expert" onclick="toggleConfigMode('expert')">Experto</button>
                            </div>

                            <div id="panel-easy">
                                <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 12px;">Pre-configuraciones:</p>
                                <div class="template-grid">
                                    <button type="button" class="template-btn" onclick="applyTemplate('notif')"><i class="fas fa-bell"></i> Alertas Email</button>
                                    <button type="button" class="template-btn" onclick="applyTemplate('fast')"><i class="fas fa-rocket"></i> Aprobación Rápida</button>
                                    <button type="button" class="template-btn" onclick="applyTemplate('empty')"><i class="fas fa-eraser"></i> Limpiar</button>
                                </div>
                            </div>

                            <div id="panel-expert" style="display: none;">
                                <div class="json-editor-container">
                                    <textarea id="configuracion" name="configuracion" rows="10" class="json-editor" placeholder='{ "setting": true }'>{{ old('configuracion') }}</textarea>
                                    <div class="json-actions">
                                        <button type="button" class="btn-small" onclick="processJson('format')"><i class="fas fa-align-left"></i> Formatear</button>
                                        <button type="button" class="btn-small" onclick="processJson('validate')"><i class="fas fa-check-double"></i> Validar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Columna Derecha --}}
                    <div class="form-column">
                        
                        {{-- SECCIÓN DE SELECTORES CON BÚSQUEDA --}}
                        <div class="form-group">
                            <label for="creado_por" class="required-label">Dueño del Proyecto</label>
                            {{-- Se añade el placeholder para el buscador --}}
                            <select name="creado_por" id="creado_por" required placeholder="Escribe para buscar...">
                                <option value="">Seleccionar Dueño...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ (old('creado_por') == $user->id || auth()->id() == $user->id) ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="asignado_a">Asignado Principal (Ejecutor)</label>
                            {{-- Se añade el placeholder para el buscador --}}
                            <select name="asignado_a" id="asignado_a" placeholder="Escribe para buscar...">
                                <option value="">Sin asignar...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('asignado_a') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- FIN SECCIÓN SELECTORES --}}

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;" class="grid-2-mobile">
                            <div class="form-group">
                                <label class="required-label">Estado</label>
                                <select name="estado" required>
                                    @foreach($estados as $key => $val)
                                        <option value="{{ $key }}" {{ old('estado') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="required-label">Prioridad</label>
                                <select name="prioridad" required>
                                    @foreach($prioridades as $key => $val)
                                        <option value="{{ $key }}" {{ old('prioridad') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;" class="grid-2-mobile">
                            <div class="form-group">
                                <label>Fecha Inicio</label>
                                <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', date('Y-m-d')) }}">
                            </div>
                            <div class="form-group">
                                <label>Fecha Fin</label>
                                <input type="date" name="fecha_fin" value="{{ old('fecha_fin') }}">
                            </div>
                        </div>

                        <div class="checkbox-container">
                            <label class="checkbox-card">
                                <input type="checkbox" name="activo" value="1" {{ old('activo', '1') ? 'checked' : '' }}>
                                <div><strong>Proyecto Activo</strong><small>Visible para todos los usuarios asignados.</small></div>
                            </label>
                            <label class="checkbox-card">
                                <input type="checkbox" name="es_plantilla" value="1" {{ old('es_plantilla') ? 'checked' : '' }}>
                                <div><strong>Marcar como Plantilla</strong><small>Guardar estructura para futuros proyectos.</small></div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Descartar</a>
                    <button type="submit" class="btn btn-primary" id="btn-submit">
                        <i class="fas fa-save"></i> Crear Workflow
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 2. Script de la librería Tom Select --}}
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <script>
        // --- INICIALIZAR LOS BUSCADORES ---
        document.addEventListener("DOMContentLoaded", function() {
            // Aplicar búsqueda al selector de Dueño
            new TomSelect('#creado_por', {
                create: false, // No permite crear nuevos usuarios, solo buscar
                sortField: { field: "text", direction: "asc" },
                plugins: ['dropdown_input']
            });

            // Aplicar búsqueda al selector de Ejecutor
            new TomSelect('#asignado_a', {
                create: false,
                sortField: { field: "text", direction: "asc" },
                plugins: ['dropdown_input']
            });
        });

        // --- VALIDACIÓN VISUAL ---
        const form = document.getElementById('workflow-create-form');
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);

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
                notif: { "email_alerts": true, "push_notifications": true, "deadline_warnings": true },
                fast: { "auto_approve": true, "skip_review": true },
                empty: {}
            };
            area.value = JSON.stringify(templates[type], null, 4);
            toggleConfigMode('expert');
            area.style.border = "2px solid var(--success)";
            setTimeout(() => area.style.border = "none", 1000);
        }

        function processJson(action) {
            const area = document.getElementById('configuracion');
            try {
                if(!area.value.trim()) return;
                const obj = JSON.parse(area.value);
                if(action === 'format') area.value = JSON.stringify(obj, null, 4);
                if(action === 'validate') alert("✅ JSON Estructurado Correctamente.");
            } catch(e) { 
                alert("❌ Error en el JSON: " + e.message); 
            }
        }

        form.onsubmit = function() {
            if (form.checkValidity()) {
                document.getElementById('btn-submit').innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Guardando...';
                document.getElementById('btn-submit').disabled = true;
                return true;
            }
        };
    </script>
</x-base-layout>