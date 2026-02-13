<x-base-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @php
        // Lógica de colores y configuración visual
        $priorityConfig = match ($workflow->prioridad) {
            'crítica' => ['bg' => '#FFE4E6', 'text' => '#BE123C', 'icon_bg' => '#FDA4AF', 'icon' => 'fa-fire'],
            'alta' => ['bg' => '#FFEDD5', 'text' => '#C2410C', 'icon_bg' => '#FDBA74', 'icon' => 'fa-arrow-trend-up'],
            'media' => ['bg' => '#E0F2FE', 'text' => '#0369A1', 'icon_bg' => '#7DD3FC', 'icon' => 'fa-minus'],
            default => ['bg' => '#F1F5F9', 'text' => '#475569', 'icon_bg' => '#CBD5E1', 'icon' => 'fa-arrow-down'],
        };

        $progressColor =
            $progress == 100
                ? 'linear-gradient(135deg, #34d399 0%, #059669 100%)'
                : 'linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%)';
    @endphp

    <style>
        :root {
            --font-display: 'Nunito', sans-serif;
            --font-body: 'Inter', sans-serif;
            --primary: #6366f1;
            --bg-body: #f8faff;
            --card-bg: #ffffff;
            --text-main: #334155;
            --text-muted: #94a3b8;
            --radius-xl: 24px;
            --radius-lg: 16px;
            --shadow-soft: 0 10px 40px -10px rgba(0, 0, 0, 0.05);
            --shadow-hover: 0 20px 40px -10px rgba(99, 102, 241, 0.15);
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            font-family: var(--font-body);
        }

        /* Layout Principal */
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2.5rem;
            display: grid;
            gap: 2.5rem;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .card-friendly {
            background: var(--card-bg);
            border-radius: var(--radius-xl);
            padding: 2rem;
            border: none;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
        }

        /* Header Hero */
        .header-hero {
            position: relative;
            overflow: hidden;
            background: white;
            padding: 2.5rem;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-soft);
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 2rem;
            align-items: center;
        }

        .header-hero::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, #e0e7ff 0%, #fae8ff 100%);
            border-radius: 50%;
            opacity: 0.6;
            z-index: 0;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.03em;
            margin-bottom: 0.5rem;
        }

        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 99px;
            font-size: 0.85rem;
            font-weight: 700;
            font-family: var(--font-display);
        }

        /* Usuario Chip Principal */
        .user-chip {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #f1f5f9;
            padding: 6px 16px 6px 6px;
            border-radius: 99px;
            transition: transform 0.2s;
        }

        .user-chip:hover {
            transform: scale(1.02);
            background: #e2e8f0;
        }

        .avatar-img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.8rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* --- NUEVOS ESTILOS PARA EQUIPO MINIMALISTA --- */
        .team-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .user-chip-compact {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            padding: 4px 12px 4px 4px;
            border-radius: 99px;
            transition: all 0.2s ease;
            cursor: default;
        }

        .user-chip-compact:hover {
            background: white;
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .avatar-compact {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* --------------------------------------------- */

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .stat-box {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.2s;
            cursor: default;
        }

        .stat-box:hover {
            transform: translateY(-5px);
        }

        .stat-icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .custom-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            background: #f1f5f9;
            padding: 5px;
            border-radius: 16px;
            width: fit-content;
        }

        .tab-btn {
            padding: 10px 24px;
            border-radius: 12px;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: var(--font-display);
        }

        .tab-btn.active {
            background: white;
            color: var(--primary);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .task-item {
            background: white;
            border-radius: var(--radius-lg);
            padding: 1.2rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            border: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }

        .task-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 6px;
            background: #cbd5e1;
            transition: background 0.3s;
        }

        .task-item:hover {
            transform: translateX(5px);
            box-shadow: var(--shadow-soft);
        }

        .task-item:hover::before {
            background: var(--primary);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }

        .btn-soft {
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
            border: none;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.4);
        }

        .btn-secondary {
            background: #eff6ff;
            color: var(--primary);
        }

        .btn-secondary:hover {
            background: #dbeafe;
        }

        .btn-pdf {
            background: #fee2e2;
            color: #ef4444;
        }

        .btn-pdf:hover {
            background: #fecaca;
            transform: translateY(-2px);
        }

        .timeline-modern {
            position: relative;
            padding-left: 1.5rem;
            border-left: 2px dashed #e2e8f0;
            margin-left: 10px;
        }

        .timeline-event {
            position: relative;
            margin-bottom: 2rem;
            padding-left: 1rem;
        }

        .timeline-point {
            position: absolute;
            left: -1.95rem;
            top: 0;
            width: 14px;
            height: 14px;
            background: white;
            border: 3px solid var(--primary);
            border-radius: 50%;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: slideUp 0.4s ease forwards;
        }

        .hidden {
            display: none;
        }

        /* Grid Responsive */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        /* RESPONSIVE */
        @media (max-width: 992px) {
            .dashboard-container {
                padding: 1rem;
                gap: 1.5rem;
            }

            .header-hero {
                grid-template-columns: 1fr;
                padding: 1.5rem;
            }

            .hero-actions-group {
                flex-wrap: wrap;
                margin-top: 1rem;
                justify-content: flex-start !important;
                width: 100%;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .table-responsive-wrapper {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }
    </style>

    <div class="dashboard-container">

        {{-- HEADER PRINCIPAL --}}
        <div class="header-hero">
            <div class="hero-content">
                {{-- Breadcrumbs --}}
                <div style="font-size: 0.85rem; color: #94a3b8; margin-bottom: 1rem;">
                    <a href="{{ route('flujo.workflows.index') }}"
                        style="text-decoration: none; color: inherit; transition: 0.2s;"
                        onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='#94a3b8'">
                        <i class="fas fa-arrow-left"></i> Volver a Workflows
                    </a>
                </div>

                <div style="display: flex; gap: 15px; align-items: center; margin-bottom: 10px; flex-wrap: wrap;">
                    <span class="badge-pill"
                        style="background: {{ $priorityConfig['bg'] }}; color: {{ $priorityConfig['text'] }};">
                        <i class="fas {{ $priorityConfig['icon'] }}"></i> {{ ucfirst($workflow->prioridad) }}
                    </span>
                    <span style="color: #cbd5e1;">|</span>
                    <span style="font-size: 0.9rem; color: #64748b;">ID: #{{ $workflow->id }}</span>
                </div>

                <h1 class="hero-title">{{ $workflow->nombre }}</h1>

                {{-- Acciones --}}
                <div class="hero-actions-group"
                    style="display: flex; gap: 20px; align-items: center; margin-top: 1.5rem;">
                    <div class="user-chip">
                        <div class="avatar-img">{{ substr($workflow->asignado->name ?? '?', 0, 1) }}</div>
                        <div>
                            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">RESPONSABLE</div>
                            <div style="font-size: 0.9rem; font-weight: 700;">
                                {{ $workflow->asignado->name ?? 'Sin Asignar' }}</div>
                        </div>
                    </div>

                    <div style="margin-left: auto; display: flex; gap: 10px; flex-wrap: wrap;">
                        <a href="{{ route('flujo.workflows.pdf', $workflow->id) }}" class="btn-soft btn-pdf"
                            target="_blank">
                            <i class="fas fa-file-pdf"></i> Informe PDF
                        </a>
                        <a href="{{ route('flujo.workflows.edit', $workflow) }}" class="btn-soft btn-secondary">
                            <i class="fas fa-sliders"></i> Ajustes
                        </a>
                    </div>
                </div>
            </div>

            {{-- Widget de Progreso Circular --}}
            <div
                style="text-align: center; background: rgba(255,255,255,0.8); padding: 1.5rem; border-radius: 20px; backdrop-filter: blur(5px);">
                <div style="position: relative; width: 100px; height: 100px; margin: 0 auto;">
                    <svg viewBox="0 0 36 36" style="width: 100%; height: 100%; transform: rotate(-90deg);">
                        <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none" stroke="#e2e8f0" stroke-width="3" />
                        <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none" stroke="url(#gradient)" stroke-width="3"
                            stroke-dasharray="{{ $progress }}, 100" stroke-linecap="round" />
                        <defs>
                            <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#6366f1" />
                                <stop offset="100%" stop-color="#ec4899" />
                            </linearGradient>
                        </defs>
                    </svg>
                    <div
                        style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: 800; font-size: 1.2rem; color: #334155;">
                        {{ $progress }}%
                    </div>
                </div>
                <div style="margin-top: 10px; font-size: 0.85rem; color: #64748b; font-weight: 600;">Progreso General
                </div>
            </div>
        </div>

        {{-- KPIS --}}
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-icon-circle" style="background: #eff6ff; color: #3b82f6;"><i
                        class="fas fa-list-check"></i></div>
                <div>
                    <div style="font-size: 1.5rem; font-weight: 800;">{{ $totalTasks }}</div>
                    <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;">TAREAS TOTALES</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon-circle" style="background: #ecfdf5; color: #10b981;"><i class="fas fa-check"></i>
                </div>
                <div>
                    <div style="font-size: 1.5rem; font-weight: 800;">{{ $completedTasks }}</div>
                    <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;">COMPLETADAS</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon-circle" style="background: #fff7ed; color: #f97316;"><i
                        class="fas fa-history"></i></div>
                <div>
                    <div style="font-size: 1.5rem; font-weight: 800;">{{ $workflowHistories->count() }}</div>
                    <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;">MOVIMIENTOS</div>
                </div>
            </div>
        </div>

        {{-- CONTENIDO PRINCIPAL --}}
        <div class="content-grid">

            {{-- COLUMNA IZQUIERDA: TAREAS --}}
            <div>
                <div class="custom-tabs">
                    <button class="tab-btn active" onclick="openTab('tasks', this)">Mis Tareas</button>
                    <button class="tab-btn" onclick="openTab('audit', this)">Historial</button>
                </div>

                {{-- TAB: TAREAS --}}
                <div id="tasks" class="tab-content fade-in">
                    <div class="card-friendly" style="min-height: 400px;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h3 style="margin: 0;">Lista de Pendientes</h3>
                            <a href="{{ route('flujo.tasks.create', ['workflow_id' => $workflow->id]) }}"
                                class="btn-soft btn-primary">
                                <i class="fas fa-plus"></i> Nueva
                            </a>
                        </div>

                        <div style="position: relative; margin-bottom: 20px;">
                            <i class="fas fa-search"
                                style="position: absolute; left: 15px; top: 12px; color: #cbd5e1;"></i>
                            <input type="text" id="searchInput" onkeyup="filterTasks()" placeholder="Buscar tarea..."
                                style="width: 100%; padding: 10px 10px 10px 40px; border: 2px solid #f1f5f9; border-radius: 12px; outline: none; transition: 0.2s;"
                                onfocus="this.style.borderColor='var(--primary)'"
                                onblur="this.style.borderColor='#f1f5f9'">
                        </div>

                        <div class="tasks-container">
                            @forelse($workflow->tasks as $task)
                                <div class="task-item" data-title="{{ strtolower($task->titulo) }}">
                                    <div style="flex: 1; min-width: 0;">
                                        <div
                                            style="font-weight: 700; color: #1e293b; margin-bottom: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $task->titulo }}</div>
                                        <div
                                            style="display: flex; gap: 10px; font-size: 0.8rem; color: #64748b; align-items: center; flex-wrap: wrap;">
                                            <span style="display: flex; align-items: center;">
                                                <span class="status-dot"
                                                    style="background: {{ $task->estado == 'finalizado' ? '#10b981' : '#f59e0b' }};"></span>
                                                {{ ucfirst($task->estado) }}
                                            </span>
                                            <span>&bull;</span>
                                            <span><i class="bi bi-calendar-plus"></i>
                                                {{ $task->created_at->format('d M') }}</span>
                                            <span>&bull;</span>
                                            <span style="display: flex; align-items: center; gap: 5px;"
                                                title="Asignado a">
                                                <div
                                                    style="width: 20px; height: 20px; background: #e0e7ff; border-radius: 50%; color: #6366f1; font-size: 0.65rem; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                                    {{ substr($task->user->name ?? ($task->asignado->name ?? '?'), 0, 1) }}
                                                </div>
                                                {{ $task->user->name ?? ($task->asignado->name ?? 'Sin Asignar') }}
                                            </span><span>&bull;</span>
                                            <span><span><i class="bi bi-calendar2-check"></i></span>
                                                {{ $task->fecha_limite->format('d M') }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('flujo.tasks.edit', $task->id) }}"
                                        style="color: #94a3b8; padding: 10px; margin-left: 5px; transition: 0.2s;"
                                        onmouseover="this.style.color='var(--primary)'"
                                        onmouseout="this.style.color='#94a3b8'">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            @empty
                                <div style="text-align: center; padding: 3rem; color: #94a3b8;">
                                    <i class="fas fa-mug-hot"
                                        style="font-size: 2.5rem; color: #e2e8f0; margin-bottom: 1rem;"></i>
                                    <p>¡Todo limpio! No hay tareas pendientes.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- TAB: AUDIT --}}
                <div id="audit" class="tab-content" style="display: none;">
                    <div class="card-friendly">
                        <div class="table-responsive-wrapper">
                            <table style="width: 100%; border-collapse: collapse; min-width: 500px;">
                                <thead>
                                    <tr style="border-bottom: 2px solid #f1f5f9;">
                                        <th
                                            style="text-align: left; padding: 15px; color: #94a3b8; font-size: 0.85rem;">
                                            USUARIO</th>
                                        <th
                                            style="text-align: left; padding: 15px; color: #94a3b8; font-size: 0.85rem;">
                                            ACCIÓN</th>
                                        <th
                                            style="text-align: right; padding: 15px; color: #94a3b8; font-size: 0.85rem;">
                                            FECHA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($auditEvents->take(10) as $event)
                                        <tr style="border-bottom: 1px solid #f8fafc;">
                                            <td style="padding: 15px; font-weight: 600; vertical-align: top;">
                                                {{ $event->user->name ?? 'Sistema' }}
                                            </td>
                                            <td style="padding: 15px; color: #64748b; vertical-align: top;">
                                                <div style="margin-bottom: 6px;">
                                                    @if ($event->tipo_evento == 'comentario')
                                                        <span style="color: #6366f1; font-weight: 600;">
                                                            <i class="far fa-comment-dots"></i> Nuevo Comentario
                                                        </span>
                                                    @else
                                                        Cambió estado a <strong
                                                            style="color: #334155;">{{ $event->estado_nuevo }}</strong>
                                                    @endif
                                                </div>
                                                @if (!empty($event->comentario))
                                                    <div
                                                        style="background: #f8fafc; padding: 10px 14px; border-radius: 12px; border: 1px solid #e2e8f0; font-size: 0.85rem; color: #475569; line-height: 1.4; position: relative;">
                                                        <span
                                                            style="position: absolute; top: -5px; left: 10px; width: 8px; height: 8px; background: #f8fafc; border-top: 1px solid #e2e8f0; border-left: 1px solid #e2e8f0; transform: rotate(45deg);"></span>
                                                        {{ $event->comentario }}
                                                    </div>
                                                @endif
                                                <div class="comment-attachment mt-2">                                                    
                                                    <a href="{{ $event->getFile($event->soporte) }}" target="_blank"class="attachment-link">
                                                        <i class="fas fa-file-download"></i> Ver Archivo
                                                    </a>
                                                </div>
                                            </td>
                                            <td style="text-align: right; color: #94a3b8;vertical-align: top;">
                                                {{ $event->created_at->diffForHumans() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: WIDGETS --}}
            <div style="display: flex; flex-direction: column; gap: 2rem;">

                {{-- Gráfico --}}
                <div class="card-friendly">
                    <h4 style="margin-top: 0; font-family: var(--font-display);">Actividad</h4>
                    <div style="height: 180px;">
                        <canvas id="friendlyChart"></canvas>
                    </div>
                </div>

                {{-- WIDGET: EQUIPO MINIMALISTA (NUEVO) --}}
                <div class="card-friendly">
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem;">
                        <h4 style="margin: 0; font-family: var(--font-display);">Equipo Asignado</h4>
                        <span
                            style="background: #e0e7ff; color: #6366f1; padding: 2px 10px; border-radius: 99px; font-size: 0.75rem; font-weight: 700;">
                            {{ $workflow->participantes->count() }}
                        </span>
                    </div>

                    @if ($workflow->participantes->count() > 0)
                        <div class="team-grid">
                            @foreach ($workflow->participantes as $member)
                                <div class="user-chip-compact" title="{{ $member->email }}">
                                    <div class="avatar-compact">
                                        {{ substr($member->name, 0, 1) }}{{ substr(strrchr($member->name, ' '), 1, 1) }}
                                    </div>
                                    <div style="display: flex; flex-direction: column; line-height: 1;">
                                        <span style="font-size: 0.85rem; font-weight: 700; color: #334155;">
                                            {{ explode(' ', $member->name)[0] }}
                                        </span>
                                        <span style="font-size: 0.65rem; color: #94a3b8;">Miembro</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div
                            style="text-align: center; padding: 1rem; border: 2px dashed #e2e8f0; border-radius: 12px; color: #94a3b8;">
                            <i class="fas fa-user-slash" style="margin-bottom: 5px;"></i><br>
                            <span style="font-size: 0.85rem;">Sin equipo definido</span>
                        </div>
                    @endif

                    <div style="margin-top: 1rem; text-align: center;">
                        <a href="{{ route('flujo.workflows.edit', $workflow) }}"
                            style="font-size: 0.8rem; color: #6366f1; text-decoration: none; font-weight: 600;">
                            Gestión de Miembros &rarr;
                        </a>
                    </div>
                </div>

                {{-- Timeline Reciente --}}
                <div class="card-friendly">
                    <h4 style="margin-top: 0; font-family: var(--font-display); margin-bottom: 1.5rem;">Últimos Cambios
                    </h4>
                    <div class="timeline-modern">
                        @forelse($workflowHistories->take(4) as $history)
                            <div class="timeline-event">
                                <div class="timeline-point"></div>
                                <div style="font-weight: 600; font-size: 0.9rem;">
                                    {{ $history->user->name ?? 'Sistema' }}</div>
                                <div style="font-size: 0.85rem; color: #64748b; margin-top: 4px;">
                                    {{ Str::limit($history->task->titulo, 20) }} &rarr; <span
                                        style="color: #10b981;">{{ $history->estado_nuevo }}</span>
                                </div>
                                <div style="font-size: 0.75rem; color: #cbd5e1; margin-top: 4px;">
                                    {{ $history->created_at->format('h:i A') }}</div>
                            </div>
                        @empty
                            <p style="color: #cbd5e1; font-style: italic;">Sin actividad reciente</p>
                        @endforelse
                    </div>
                </div>

                {{-- JSON Card --}}
                <div class="card-friendly" style="background: #1e293b; color: white;">
                    <div style="display: flex; justify-content: space-between; cursor: pointer;"
                        onclick="document.getElementById('jsonBody').classList.toggle('hidden')">
                        <span style="font-family: monospace; color: #94a3b8;">DEV_CONFIG.JSON</span>
                        <i class="fas fa-code"></i>
                    </div>
                    <div id="jsonBody" class="hidden"
                        style="margin-top: 15px; font-family: monospace; font-size: 0.75rem; color: #a5b4fc; word-break: break-all;">
                        {{ json_encode($workflow->configuracion) }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // Lógica de Pestañas
        function openTab(tabName, btn) {
            document.querySelectorAll('.tab-content').forEach(x => x.style.display = 'none');
            document.querySelectorAll('.tab-btn').forEach(x => x.classList.remove('active'));
            document.getElementById(tabName).style.display = 'block';
            document.getElementById(tabName).classList.add('fade-in');
            btn.classList.add('active');
        }

        // Filtro Simple
        function filterTasks() {
            let val = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('.task-item').forEach(el => {
                el.style.display = el.getAttribute('data-title').includes(val) ? 'flex' : 'none';
            });
        }

        // Gráfico Amigable
        const ctx = document.getElementById('friendlyChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Comentarios', 'Estados'],
                datasets: [{
                    data: [{{ $auditStats['comments'] }}, {{ $auditStats['history'] }}],
                    backgroundColor: ['#6366f1', '#f472b6'],
                    borderWidth: 0,
                    borderRadius: 20,
                }]
            },
            options: {
                cutout: '85%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8
                        }
                    }
                }
            }
        });
    </script>
</x-base-layout>
