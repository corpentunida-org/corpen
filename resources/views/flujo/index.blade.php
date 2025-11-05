<x-base-layout>
    <div class="dashboard-container">
        {{-- ================= BARRA DE NAVEGACIÓN SUPERIOR ================= --}}
        <header class="top-header">
            <div class="header-left">
                <div class="logo">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <nav class="main-nav">
                    <a href="{{ route('flujo.tablero') }}" class="active">Inicio</a>
                    <a href="#">Mis Tareas</a>
                    <a href="#">Notificaciones</a>
                </nav>
            </div>
            <div class="header-right">
                <div class="search-container">
                    <input type="text" placeholder="Buscar..." class="search-board">
                    <i class="fas fa-search"></i>
                </div>
                <button class="btn-primary">
                    <i class="fas fa-plus"></i> Nuevo
                </button>
                <div class="user-avatar">BC</div>
            </div>
        </header>

        {{-- Contenido principal --}}
        <main class="main-content">
            <div class="board-header">
                <div class="board-title">
                    <h1>Panel de Control</h1>
                    <i class="far fa-star"></i>
                </div>
                <div class="board-actions">
                    <div class="view-switcher">
                        <a href="?view=table" class="active"><i class="fas fa-table"></i></a>
                        <a href="?view=kanban"><i class="fas fa-columns"></i></a>
                        <a href="?view=gantt"><i class="fas fa-chart-gantt"></i></a>
                    </div>
                </div>
            </div>

            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: rgba(255, 182, 193, 0.3); color: #ff6b81;">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="stat-content">
                        <h3>24</h3>
                        <p>Tareas Activas</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: rgba(173, 216, 230, 0.3); color: #4dabf7;">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="stat-content">
                        <h3>5</h3>
                        <p>Proyectos</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: rgba(152, 251, 152, 0.3); color: #51cf66;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3>12</h3>
                        <p>Completadas</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: rgba(255, 218, 185, 0.3); color: #ff922b;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3>3</h3>
                        <p>Pendientes</p>
                    </div>
                </div>
            </div>

            <div class="board-view">
                @php
                    $currentView = request('view', 'table');
                @endphp

                @if ($currentView == 'table')
                    <div class="cards-grid">
                         @include('flujo.componentes.workflows-card')
                         @include('flujo.componentes.tasks-card')
                         @include('flujo.componentes.comments-card')
                         @include('flujo.componentes.histories-card')
                    </div>
                @elseif ($currentView == 'kanban')
                    <div class="kanban-container">
                        <div class="kanban-column">
                            <div class="column-header">
                                <h3>Por Hacer</h3>
                                <span class="task-count">4</span>
                            </div>
                            <div class="kanban-tasks">
                                <div class="kanban-task">
                                    <h4>Diseñar nueva interfaz</h4>
                                    <div class="task-meta">
                                        <span class="task-priority high">Alta</span>
                                        <div class="task-assignees">
                                            <div class="assignee-avatar">JD</div>
                                            <div class="assignee-avatar">MS</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="kanban-task">
                                    <h4>Revisar documentación</h4>
                                    <div class="task-meta">
                                        <span class="task-priority medium">Media</span>
                                        <div class="task-assignees">
                                            <div class="assignee-avatar">AK</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="kanban-column">
                            <div class="column-header">
                                <h3>En Progreso</h3>
                                <span class="task-count">3</span>
                            </div>
                            <div class="kanban-tasks">
                                <div class="kanban-task">
                                    <h4>Implementar API</h4>
                                    <div class="task-meta">
                                        <span class="task-priority high">Alta</span>
                                        <div class="task-assignees">
                                            <div class="assignee-avatar">RT</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="kanban-column">
                            <div class="column-header">
                                <h3>Completado</h3>
                                <span class="task-count">7</span>
                            </div>
                            <div class="kanban-tasks">
                                <div class="kanban-task">
                                    <h4>Configurar servidor</h4>
                                    <div class="task-meta">
                                        <span class="task-priority low">Baja</span>
                                        <div class="task-assignees">
                                            <div class="assignee-avatar">LM</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif ($currentView == 'gantt')
                    <div class="gantt-container">
                        <div class="gantt-header">
                            <div class="gantt-timeline">
                                <div class="timeline-month">Enero</div>
                                <div class="timeline-month">Febrero</div>
                                <div class="timeline-month">Marzo</div>
                                <div class="timeline-month">Abril</div>
                            </div>
                        </div>
                        <div class="gantt-tasks">
                            <div class="gantt-task">
                                <div class="task-info">Diseño UI/UX</div>
                                <div class="task-bar" style="width: 40%; left: 0%; background-color: rgba(255, 182, 193, 0.7);"></div>
                            </div>
                            <div class="gantt-task">
                                <div class="task-info">Desarrollo Backend</div>
                                <div class="task-bar" style="width: 60%; left: 20%; background-color: rgba(173, 216, 230, 0.7);"></div>
                            </div>
                            <div class="gantt-task">
                                <div class="task-info">Testing</div>
                                <div class="task-bar" style="width: 30%; left: 60%; background-color: rgba(152, 251, 152, 0.7);"></div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </main>
    </div>

    {{-- ================= ESTILOS MINIMALISTAS CON COLORES PASTELES ================= --}}
    <style>
        /* ====== VARIABLES DE COLOR Y ESTILO ====== */
        :root {
            /* Colores pasteles */
            --primary-color: #a6c1ee;
            --primary-hover: #8b9fd9;
            --secondary-color: #fbc2eb;
            --accent-color: #a6e3e9;
            --success-color: #c8e6c9;
            --warning-color: #ffe0b2;
            --danger-color: #ffcdd2;
            
            /* Tonos neutros */
            --body-bg: #fafbfc;
            --card-bg: #ffffff;
            --text-dark: #495057;
            --text-light: #6c757d;
            --border-color: #e9ecef;
            
            /* Espaciado y bordes */
            --border-radius-sm: 8px;
            --border-radius-md: 12px;
            --border-radius-lg: 16px;
            --shadow-soft: 0 4px 6px rgba(0, 0, 0, 0.05);
            --shadow-medium: 0 8px 15px rgba(0, 0, 0, 0.08);
            
            /* Transiciones */
            --transition-fast: 0.2s ease;
            --transition-medium: 0.3s ease;
        }

        /* ====== RESET ====== */
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
        }
        
        a { 
            text-decoration: none; 
            color: inherit; 
            transition: var(--transition-fast);
        }
        
        button { 
            background: none; 
            border: none; 
            cursor: pointer; 
            font-family: inherit;
            transition: var(--transition-fast);
        }

        /* ====== DASHBOARD LAYOUT ====== */
        .dashboard-container { 
            display: flex; 
            flex-direction: column; /* Cambiado a columna para apilar header y main */
            min-height: 100vh; 
            background: var(--body-bg); 
        }

        /* ====== TOP HEADER ====== */
        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 30px;
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow-soft);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 40px;
        }

        .logo {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .main-nav {
            display: flex;
            gap: 20px;
        }

        .main-nav a {
            font-weight: 500;
            color: var(--text-light);
            padding: 8px 12px;
            border-radius: var(--border-radius-sm);
            transition: var(--transition-fast);
        }

        .main-nav a.active,
        .main-nav a:hover {
            color: var(--primary-color);
            background: rgba(166, 193, 238, 0.1);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* ====== MAIN CONTENT ====== */
        .main-content { 
            flex: 1; 
            padding: 30px; 
            overflow-y: auto;
        }

        /* ====== BOARD HEADER ====== */
        .board-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 30px; 
        }
        
        .board-title { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
        }
        
        .board-title h1 { 
            font-size: 28px; 
            font-weight: 600; 
            color: var(--text-dark); 
        }
        
        .board-title i { 
            color: var(--warning-color); 
            cursor: pointer; 
            transition: var(--transition-fast);
            font-size: 20px;
        }
        
        .board-title i:hover { 
            color: #ff922b; 
            transform: scale(1.2); 
        }
        
        .board-actions { 
            display: flex; 
            align-items: center; 
            gap: 15px; 
        }
        
        .search-container {
            position: relative;
        }
        
        .search-board { 
            padding: 10px 15px 10px 40px; 
            border-radius: var(--border-radius-md); 
            border: 1px solid var(--border-color); 
            width: 220px; 
            transition: var(--transition-fast);
            background: white;
        }
        
        .search-board:focus { 
            border-color: var(--primary-color); 
            outline: none; 
            box-shadow: 0 0 0 3px rgba(166, 193, 238, 0.2);
            width: 280px;
        }
        
        .search-container i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }
        
        .btn-primary { 
            background: var(--primary-color); 
            color: white; 
            border-radius: var(--border-radius-md); 
            padding: 10px 16px; 
            display: flex; 
            align-items: center; 
            gap: 8px; 
            font-weight: 500; 
            transition: var(--transition-fast);
            box-shadow: var(--shadow-soft);
        }
        
        .btn-primary:hover { 
            background: var(--primary-hover); 
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }
        
        .user-avatar { 
            width: 40px; 
            height: 40px; 
            border-radius: 50%; 
            background: var(--secondary-color); 
            color: white; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-weight: 600; 
            cursor: pointer;
            transition: var(--transition-fast);
        }
        
        .user-avatar:hover {
            transform: scale(1.05);
        }
        
        /* VIEW SWITCHER */
        .view-switcher { 
            display: flex; 
            background: white; 
            padding: 4px; 
            border-radius: var(--border-radius-md);
            box-shadow: var(--shadow-soft);
        }
        
        .view-switcher a {
            padding: 8px 12px; 
            border-radius: var(--border-radius-sm); 
            font-weight: 500; 
            display: flex; 
            align-items: center; 
            gap: 6px; 
            color: var(--text-light); 
            transition: var(--transition-fast);
        }
        
        .view-switcher a.active { 
            background: var(--primary-color); 
            color: white; 
        }

        /* ====== STATS CONTAINER ====== */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: var(--border-radius-md);
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: var(--shadow-soft);
            transition: var(--transition-fast);
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-medium);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: var(--border-radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        .stat-content h3 {
            font-size: 24px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 4px;
        }
        
        .stat-content p {
            font-size: 14px;
            color: var(--text-light);
        }

        /* ====== GRID DE CARDS ====== */
        .cards-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); 
            gap: 25px; 
        }

        /* ====== CARD MINIMALISTA ====== */
        .card-monday {
            background: var(--card-bg); 
            border-radius: var(--border-radius-lg); 
            padding: 24px;
            box-shadow: var(--shadow-soft);
            transition: var(--transition-medium);
            border-top: 4px solid var(--primary-color);
        }
        
        .card-monday:hover { 
            transform: translateY(-5px); 
            box-shadow: var(--shadow-medium); 
        }
        
        .card-title{ 
            font-size: 18px; 
            font-weight: 600; 
            color: var(--text-dark); 
            margin-bottom: 20px; 
            display: flex; 
            align-items: center; 
        }
        
        .card-title i{ 
            margin-right: 10px; 
            color: var(--primary-color);
        }

        /* ====== TABLAS Y BADGES ====== */
        .table-monday { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 14px; 
        }
        
        .table-monday th, .table-monday td { 
            padding: 12px; 
            text-align: left; 
            border-bottom: 1px solid var(--border-color); 
        }
        
        .table-monday th { 
            background: #f8f9fa; 
            font-weight: 600; 
            color: var(--text-dark); 
        }
        
        .table-monday tbody tr:hover { 
            background: #f8f9fa; 
        }

        .badge { 
            display: inline-block; 
            padding: 4px 10px; 
            border-radius: 20px; 
            color: white; 
            font-size: 12px; 
            text-transform: capitalize; 
            font-weight: 500; 
        }
        
        .badge.blue{background: var(--primary-color);} 
        .badge.green{background: var(--success-color);} 
        .badge.red{background: var(--danger-color);} 
        .badge.orange{background: var(--warning-color);} 
        .badge.purple{background: var(--secondary-color);}
        
        /* ====== KANBAN VIEW ====== */
        .kanban-container {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding-bottom: 20px;
        }
        
        .kanban-column {
            flex: 1;
            min-width: 300px;
            background: white;
            border-radius: var(--border-radius-md);
            padding: 20px;
            box-shadow: var(--shadow-soft);
        }
        
        .column-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .column-header h3 {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
        }
        
        .task-count {
            background: var(--border-color);
            color: var(--text-light);
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 20px;
        }
        
        .kanban-tasks {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .kanban-task {
            background: var(--body-bg);
            border-radius: var(--border-radius-sm);
            padding: 15px;
            transition: var(--transition-fast);
            cursor: pointer;
        }
        
        .kanban-task:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-soft);
        }
        
        .kanban-task h4 {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 10px;
        }
        
        .task-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .task-priority {
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        
        .task-priority.high {
            background: var(--danger-color);
            color: white;
        }
        
        .task-priority.medium {
            background: var(--warning-color);
            color: white;
        }
        
        .task-priority.low {
            background: var(--success-color);
            color: white;
        }
        
        .task-assignees {
            display: flex;
            gap: -5px;
        }
        
        .assignee-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 600;
            border: 2px solid white;
        }
        
        /* ====== GANTT VIEW ====== */
        .gantt-container {
            background: white;
            border-radius: var(--border-radius-md);
            padding: 20px;
            box-shadow: var(--shadow-soft);
            overflow-x: auto;
        }
        
        .gantt-header {
            margin-bottom: 20px;
        }
        
        .gantt-timeline {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
        }
        
        .timeline-month {
            flex: 1;
            text-align: center;
            font-weight: 600;
            color: var(--text-light);
        }
        
        .gantt-tasks {
            position: relative;
        }
        
        .gantt-task {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            position: relative;
            height: 40px;
        }
        
        .task-info {
            width: 150px;
            font-weight: 500;
            color: var(--text-dark);
            padding-right: 20px;
        }
        
        .task-bar {
            height: 30px;
            border-radius: var(--border-radius-sm);
            position: absolute;
            left: 150px;
            right: 0;
        }
    </style>
</x-base-layout>