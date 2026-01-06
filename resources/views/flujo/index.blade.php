<x-base-layout>
    <div class="dashboard-wrapper">
        <main class="content-body">
            {{-- Encabezado Ejecutivo --}}
            <div class="welcome-bar">
                <div class="welcome-text">
                    <span class="system-tag">Sistema Central de Operaciones</span>
                    <h1 class="main-title">Consola de Gestión Operativa</h1>
                    <p class="main-subtitle">Supervisión integral de procesos, cumplimiento de hitos y trazabilidad técnica.</p>
                </div>
                <div class="welcome-actions">
                    <a href="{{ route('flujo.workflows.create') }}" class="btn-minimal-black">
                        <i class="fas fa-plus"></i> Iniciar Nuevo Proyecto
                    </a>
                </div>
            </div>

            {{-- KPIs CORPORATIVOS (Indicadores Reales de los 4 Modelos) --}}
            @php
                $countWorkflows = \App\Models\Flujo\Workflow::count();
                $countTasks = \App\Models\Flujo\Task::count();
                $countComments = \App\Models\Flujo\TaskComment::count();
                $countHistory = \App\Models\Flujo\TaskHistory::count();
            @endphp

            <div class="stats-row">
                <div class="stat-group">
                    <span class="stat-label">Portafolio de Proyectos</span>
                    <span class="stat-number">{{ $countWorkflows }}</span>
                </div>
                <div class="stat-group">
                    <span class="stat-label">Unidades de Trabajo</span>
                    <span class="stat-number">{{ $countTasks }}</span>
                </div>
                <div class="stat-group">
                    <span class="stat-label">Registro de Feedback</span>
                    <span class="stat-number">{{ $countComments }}</span>
                </div>
                <div class="stat-group">
                    <span class="stat-label">Eventos de Auditoría</span>
                    <span class="stat-number">{{ $countHistory }}</span>
                </div>
            </div>

            {{-- STACK DE GESTIÓN (Acordeón con UX Premium) --}}
            <div class="modern-stack">
                
                {{-- Módulo 1: Workflows --}}
                <div class="stack-card active">
                    <button class="stack-trigger" onclick="toggleStack(this)">
                        <div class="stack-title">
                            <div class="icon-box icon-indigo"><i class="fas fa-project-diagram"></i></div>
                            <div>
                                <span class="title-main">Estructura de Procesos</span>
                                <span class="title-sub">Gestión de workflows y flujos de valor institucionales</span>
                            </div>
                        </div>
                        <i class="fas fa-chevron-down arrow-indicator"></i>
                    </button>
                    <div class="stack-body">
                        <div class="stack-inner">
                            @include('flujo.componentes.workflows-card')
                        </div>
                    </div>
                </div>

                {{-- Módulo 2: Tareas --}}
                <div class="stack-card">
                    <button class="stack-trigger" onclick="toggleStack(this)">
                        <div class="stack-title">
                            <div class="icon-box icon-emerald"><i class="fas fa-tasks"></i></div>
                            <div>
                                <span class="title-main">Control de Actividades</span>
                                <span class="title-sub">Seguimiento operativo y cumplimiento de requerimientos</span>
                            </div>
                        </div>
                        <i class="fas fa-chevron-down arrow-indicator"></i>
                    </button>
                    <div class="stack-body">
                        <div class="stack-inner">
                            @include('flujo.componentes.tasks-card')
                        </div>
                    </div>
                </div>

                {{-- Módulo 3: Comentarios --}}
                <div class="stack-card">
                    <button class="stack-trigger" onclick="toggleStack(this)">
                        <div class="stack-title">
                            <div class="icon-box icon-blue"><i class="fas fa-comments"></i></div>
                            <div>
                                <span class="title-main">Canal de Comunicación</span>
                                <span class="title-sub">Feedback colaborativo y anotaciones de seguimiento técnico</span>
                            </div>
                        </div>
                        <i class="fas fa-chevron-down arrow-indicator"></i>
                    </button>
                    <div class="stack-body">
                        <div class="stack-inner">
                            @include('flujo.componentes.comments-card')
                        </div>
                    </div>
                </div>

                {{-- Módulo 4: Auditoría --}}
                <div class="stack-card">
                    <button class="stack-trigger" onclick="toggleStack(this)">
                        <div class="stack-title">
                            <div class="icon-box icon-amber"><i class="fas fa-history"></i></div>
                            <div>
                                <span class="title-main">Bitácora de Gobernanza</span>
                                <span class="title-sub">Historial íntegro de modificaciones y registro de cambios</span>
                            </div>
                        </div>
                        <i class="fas fa-chevron-down arrow-indicator"></i>
                    </button>
                    <div class="stack-body">
                        <div class="stack-inner">
                            @include('flujo.componentes.histories-card')
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        :root {
            --executive-bg: #ffffff;
            --executive-text: #0f172a;
            --executive-muted: #64748b;
            --executive-border: #f1f5f9;
            --executive-accent: #2563eb;
        }

        body { 
            background-color: #fafafa; 
            font-family: 'Inter', sans-serif; 
            color: var(--executive-text);
            -webkit-font-smoothing: antialiased;
        }

        .content-body { max-width: 1100px; margin: 50px auto; padding: 0 25px; }

        /* HEADER CORPORATIVO */
        .welcome-bar { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 50px; }
        .system-tag { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: var(--executive-accent); letter-spacing: 0.1em; margin-bottom: 8px; display: block; }
        .main-title { font-size: 2.25rem; font-weight: 800; letter-spacing: -0.04em; margin: 0; color: var(--executive-text); }
        .main-subtitle { color: var(--executive-muted); font-size: 1rem; margin-top: 6px; }

        .btn-minimal-black {
            background: #000; color: #fff; padding: 12px 24px; border-radius: 10px;
            font-size: 0.85rem; font-weight: 600; text-decoration: none;
            display: inline-flex; align-items: center; gap: 10px; transition: all 0.2s ease;
        }
        .btn-minimal-black:hover { background: #222; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }

        /* KPIs */
        .stats-row { 
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; 
            margin-bottom: 50px; padding-bottom: 30px; border-bottom: 1px solid var(--executive-border);
        }
        .stat-group { display: flex; flex-direction: column; gap: 4px; }
        .stat-label { font-size: 0.7rem; font-weight: 700; color: var(--executive-muted); text-transform: uppercase; letter-spacing: 0.08em; }
        .stat-number { font-size: 2.25rem; font-weight: 800; color: var(--executive-text); line-height: 1; }

        /* STACK CARDS */
        .modern-stack { display: flex; flex-direction: column; gap: 16px; }
        .stack-card { 
            background: #fff; border: 1px solid var(--executive-border); 
            border-radius: 16px; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
        }
        .stack-card.active { border-color: #cbd5e1; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.04); }

        .stack-trigger {
            width: 100%; display: flex; justify-content: space-between; align-items: center;
            padding: 24px 30px; background: transparent; cursor: pointer; border: none; outline: none;
        }

        .stack-title { display: flex; align-items: center; gap: 20px; text-align: left; }
        .icon-box { 
            width: 44px; height: 44px; border-radius: 12px; 
            display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
        }
        .icon-indigo { background: #e0e7ff; color: #4338ca; }
        .icon-emerald { background: #d1fae5; color: #065f46; }
        .icon-blue { background: #e0f2fe; color: #0369a1; }
        .icon-amber { background: #fef3c7; color: #92400e; }

        .title-main { display: block; font-size: 1.05rem; font-weight: 700; color: var(--executive-text); }
        .title-sub { font-size: 0.8rem; color: var(--executive-muted); font-weight: 500; }

        .arrow-indicator { font-size: 0.85rem; color: var(--executive-muted); transition: 0.4s; }
        .stack-card.active .arrow-indicator { transform: rotate(180deg); color: var(--executive-text); }

        .stack-body { max-height: 0; overflow: hidden; transition: max-height 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
        .stack-card.active .stack-body { max-height: 4000px; }
        .stack-inner { padding: 0 30px 30px 30px; }
    </style>

    <script>
        function toggleStack(btn) {
            const card = btn.closest('.stack-card');
            const isActive = card.classList.contains('active');
            
            // UX Corporativa: Una sola sección abierta para foco total
            document.querySelectorAll('.stack-card').forEach(c => c.classList.remove('active'));
            
            if (!isActive) {
                card.classList.add('active');
            }
        }
    </script>
</x-base-layout>