<x-base-layout>
    <div class="dashboard-wrapper">
        <main class="content-body">
            {{-- Encabezado Ejecutivo Adaptable --}}
            <div class="welcome-bar">
                <div class="welcome-text">
                    <span class="system-tag">Sistema Central de Operaciones</span>
                    <h1 class="main-title">Consola de Gestión Operativa</h1>
                    <p class="main-subtitle">Supervisión integral de procesos, cumplimiento de hitos y trazabilidad técnica.</p>
                </div>
                <div class="welcome-actions">
                    <a href="{{ route('flujo.workflows.create') }}" class="btn-minimal-black">
                        <i class="fas fa-plus"></i> <span>Iniciar Nuevo Proyecto</span>
                    </a>
                </div>
            </div>

            {{-- KPIs CORPORATIVOS (Responsive Grid) --}}
            @php
                $countWorkflows = \App\Models\Flujo\Workflow::count();
                $countTasks = \App\Models\Flujo\Task::count();
                $countComments = \App\Models\Flujo\TaskComment::count();
                $countHistory = \App\Models\Flujo\TaskHistory::count();
            @endphp

            <div class="stats-row">
                <div class="stat-group">
                    <span class="stat-label">Proyectos</span>
                    <span class="stat-number">{{ $countWorkflows }}</span>
                </div>
                <div class="stat-group">
                    <span class="stat-label">Tareas</span>
                    <span class="stat-number">{{ $countTasks }}</span>
                </div>
                <div class="stat-group">
                    <span class="stat-label">Feedback</span>
                    <span class="stat-number">{{ $countComments }}</span>
                </div>
                <div class="stat-group">
                    <span class="stat-label">Auditoría</span>
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
                                <span class="title-sub d-none-mobile">Gestión de workflows y flujos de valor</span>
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
                                <span class="title-sub d-none-mobile">Seguimiento operativo y cumplimiento</span>
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
                                <span class="title-sub d-none-mobile">Feedback colaborativo y anotaciones</span>
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
                                <span class="title-sub d-none-mobile">Historial íntegro de modificaciones</span>
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
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;800&display=swap');

        :root {
            --executive-bg: #ffffff;
            --executive-text: #0f172a;
            --executive-muted: #64748b;
            --executive-border: #f1f5f9;
            --executive-accent: #4f46e5;
            --radius-xl: 24px;
            --radius-lg: 16px;
        }

        body { 
            background-color: #fbfcfd; 
            font-family: 'Inter', sans-serif; 
            color: var(--executive-text);
            -webkit-font-smoothing: antialiased;
            margin: 0;
        }

        .content-body { max-width: 1100px; margin: 40px auto; padding: 0 24px; }

        /* HEADER */
        .welcome-bar { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px; gap: 20px; }
        .system-tag { font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--executive-accent); letter-spacing: 0.1em; margin-bottom: 8px; display: block; }
        .main-title { font-family: 'Outfit'; font-size: 2.2rem; font-weight: 800; letter-spacing: -0.03em; margin: 0; line-height: 1.1; }
        .main-subtitle { color: var(--executive-muted); font-size: 1rem; margin-top: 8px; max-width: 600px; }

        .btn-minimal-black {
            background: #000; color: #fff; padding: 14px 24px; border-radius: 12px;
            font-size: 0.85rem; font-weight: 700; text-decoration: none;
            display: inline-flex; align-items: center; gap: 10px; transition: 0.3s;
            white-space: nowrap;
        }
        .btn-minimal-black:hover { background: #222; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }

        /* KPIs */
        .stats-row { 
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; 
            margin-bottom: 40px; padding-bottom: 30px; border-bottom: 1px solid var(--executive-border);
        }
        .stat-group { display: flex; flex-direction: column; gap: 6px; }
        .stat-label { font-size: 11px; font-weight: 700; color: var(--executive-muted); text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-number { font-family: 'Outfit'; font-size: 2rem; font-weight: 800; color: var(--executive-text); line-height: 1; }

        /* STACK CARDS */
        .modern-stack { display: flex; flex-direction: column; gap: 16px; }
        .stack-card { 
            background: #fff; border: 1px solid var(--executive-border); 
            border-radius: var(--radius-lg); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
            overflow: hidden;
        }
        .stack-card.active { border-color: #cbd5e1; box-shadow: 0 15px 30px -5px rgba(0,0,0,0.04); }

        .stack-trigger {
            width: 100%; display: flex; justify-content: space-between; align-items: center;
            padding: 24px 30px; background: transparent; cursor: pointer; border: none; outline: none;
        }

        .stack-title { display: flex; align-items: center; gap: 20px; text-align: left; }
        .icon-box { 
            width: 46px; height: 46px; border-radius: 12px; 
            display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
            flex-shrink: 0;
        }
        .icon-indigo { background: #eef2ff; color: #4f46e5; }
        .icon-emerald { background: #ecfdf5; color: #10b981; }
        .icon-blue { background: #eff6ff; color: #3b82f6; }
        .icon-amber { background: #fffbeb; color: #f59e0b; }

        .title-main { display: block; font-size: 1.1rem; font-weight: 700; color: var(--executive-text); letter-spacing: -0.01em; }
        .title-sub { font-size: 0.85rem; color: var(--executive-muted); font-weight: 500; margin-top: 2px; }

        .arrow-indicator { font-size: 0.9rem; color: var(--executive-muted); transition: 0.4s; }
        .stack-card.active .arrow-indicator { transform: rotate(180deg); color: var(--executive-text); }

        .stack-body { max-height: 0; overflow: hidden; transition: max-height 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
        .stack-card.active .stack-body { max-height: 5000px; }
        .stack-inner { padding: 0 30px 30px 30px; }

        /* --- MOBILE OPTIMIZATION --- */
        @media (max-width: 900px) {
            .content-body { padding: 0 16px; margin: 20px auto; }
            .welcome-bar { flex-direction: column; align-items: flex-start; margin-bottom: 30px; }
            .main-title { font-size: 1.7rem; }
            .btn-minimal-black { width: 100%; justify-content: center; }
            
            .stats-row { grid-template-columns: repeat(2, 1fr); gap: 16px; padding-bottom: 20px; }
            .stat-number { font-size: 1.6rem; }
            
            .stack-trigger { padding: 20px; }
            .stack-inner { padding: 0 15px 20px 15px; }
            .icon-box { width: 40px; height: 40px; font-size: 1rem; }
            .title-main { font-size: 0.95rem; }
            .d-none-mobile { display: none !important; }
        }
    </style>

    <script>
        function toggleStack(btn) {
            const card = btn.closest('.stack-card');
            const isActive = card.classList.contains('active');
            
            // UX Corporativa: Cerrar otros para enfocar
            document.querySelectorAll('.stack-card').forEach(c => c.classList.remove('active'));
            
            if (!isActive) {
                card.classList.add('active');
                // Scroll suave al abrir en móvil
                if(window.innerWidth < 900) {
                    setTimeout(() => {
                        card.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 300);
                }
            }
        }
    </script>
</x-base-layout>