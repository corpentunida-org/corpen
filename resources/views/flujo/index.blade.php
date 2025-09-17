<x-base-layout>
    <div class="dashboard-container">
        {{-- Checkbox oculto para controlar el sidebar --}}
        <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-checkbox" hidden>

        {{-- ================= SIDEBAR MEJORADO ================= --}}
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-project-diagram"></i>
                    <span>FlujoCorp</span>
                </div>
                {{-- Botón para colapsar/expandir (usando un label para el checkbox) --}}
                <label for="sidebar-toggle" class="collapse-btn">
                    <i class="fas fa-angle-left"></i>
                </label>
            </div>
            <div class="sidebar-content">
                <nav class="menu">
                    <a href="{{ route('flujo.tablero') }}" class="active"><i class="fas fa-home"></i> Inicio</a>
                    <a href="#"><i class="fas fa-check-circle"></i> Mis Tareas</a>
                    <a href="#"><i class="fas fa-inbox"></i> Notificaciones</a>
                </nav>

                <div class="workspaces">
                    <div class="workspace-header">
                        <span>Espacios de Trabajo</span>
                        <button class="add-workspace">+</button>
                    </div>
                    <div class="workspace-item">
                        <a href="#"><i class="fas fa-folder"></i> Proyectos Cliente A</a>
                    </div>
                    {{-- Un ejemplo con sub-elementos expandibles con CSS --}}
                    <div class="workspace-item">
                        {{-- Otro checkbox oculto para este elemento específico --}}
                        <input type="checkbox" id="workspace-dev-toggle" class="workspace-toggle-checkbox" hidden>
                        <label for="workspace-dev-toggle" class="workspace-item-label">
                            <a href="#" onclick="return false;"><i class="fas fa-folder"></i> Desarrollo Interno</a>
                            <i class="fas fa-chevron-right workspace-chevron"></i>
                        </label>
                        <div class="board-list">
                            <a href="#">- Proyecto Phoenix</a>
                            <a href="#">- Roadmap Q4</a>
                        </div>
                    </div>
                    <div class="workspace-item">
                        <a href="#"><i class="fas fa-folder"></i> Marketing</a>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Contenido principal --}}
        <main class="main-content">
            <!-- ... tu main content actual ... -->
            <header class="board-header">
                <div class="board-title">
                    <h1>Proyecto Phoenix</h1>
                    <i class="far fa-star"></i>
                </div>
                <div class="board-actions">
                    <div class="view-switcher">
                        {{-- Para las vistas, necesitarías enlaces que carguen diferentes partes o un JS muy básico --}}
                        <a href="?view=table" class="active"><i class="fas fa-table"></i> Tabla Principal</a>
                        <a href="?view=kanban"><i class="fas fa-columns"></i> Kanban</a>
                        <a href="?view=gantt"><i class="fas fa-chart-gantt"></i> Gantt</a>
                    </div>
                    <input type="text" placeholder="Buscar en tablero..." class="search-board">
                    <a href="#" class="btn-monday"><i class="fas fa-plus"></i> Nuevo Elemento</a>
                    <div class="user-avatar">BC</div>
                </div>
            </header>

            <div class="board-view">
                {{-- Esto requeriría lógica PHP en el backend para mostrar el componente correcto según ?view --}}
                @php
                    $currentView = request('view', 'table'); // Default a 'table'
                @endphp

                @if ($currentView == 'table')
                    <div class="cards-grid">
                         @include('flujo.componentes.workflows-card')
                         @include('flujo.componentes.tasks-card')
                         @include('flujo.componentes.comments-card')
                         @include('flujo.componentes.histories-card')
                    </div>
                @elseif ($currentView == 'kanban')
                    <div>Contenido de la vista Kanban...</div>
                @elseif ($currentView == 'gantt')
                    <div>Contenido de la vista Gantt...</div>
                @endif
            </div>

        </main>
    </div>

    {{-- ================= ESTILOS MONDAY MEJORADOS ================= --}}
    <style>
        /* ====== VARIABLES DE COLOR Y ESTILO ====== */
        :root {
            --primary-color: #5D5FEF;
            --primary-hover: #3F40C4;
            --sidebar-bg: #1B1F3B;
            --sidebar-text: #C7CADB;
            --body-bg: #F5F6F8;
            --card-bg: #FFFFFF;
            --text-dark: #1B1F3B;
            --border-color: #E0E0E0;
            --border-radius-md: 12px;
            --border-radius-lg: 16px;
            --shadow-soft: 0 4px 14px 0 rgba(0, 0, 0, 0.05);
        }

        /* ====== RESET ====== */
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
        a { text-decoration:none; color:inherit; }
        button { background:none; border:none; cursor:pointer; font-family:'Poppins', sans-serif; }

        /* ====== DASHBOARD LAYOUT ====== */
        .dashboard-container { display:flex; min-height:100vh; background: var(--body-bg); }

        /* ====== SIDEBAR ====== */
        .sidebar { 
            width:250px; 
            background:var(--sidebar-bg); 
            display:flex; 
            flex-direction:column; 
            border-right: 1px solid #2a2e4c; 
            transition: width 0.3s ease; 
            position: relative; /* Necesario para el botón de colapsar */
            flex-shrink: 0; /* Evita que el sidebar se encoja antes de tiempo */
        }
        .sidebar-header { padding: 20px; border-bottom: 1px solid #2a2e4c; display: flex; justify-content: space-between; align-items: center;}
        .sidebar-header .logo { font-size:20px; font-weight:700; color:#fff; display:flex; align-items:center; gap:10px; }
        .sidebar-header .logo span { transition: opacity 0.3s ease; } /* Animación para el texto */
        .sidebar-content { padding: 15px; overflow-y: auto; transition: opacity 0.3s ease; }
        .menu a { display:flex; align-items:center; gap:12px; padding:10px 14px; border-radius:var(--border-radius-md); color:var(--sidebar-text); margin-bottom:5px; transition:0.2s ease-in-out; font-weight: 500;}
        .menu a.active, .menu a:hover { background:var(--primary-color); color:#fff; }
        .menu a i { width:20px; text-align:center; font-size: 16px; }

        /* WORKSPACES */
        .workspaces { margin-top: 25px; }
        .workspace-header { display:flex; justify-content:space-between; align-items:center; padding:0 10px; margin-bottom:10px; color: var(--sidebar-text); font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; }
        .add-workspace { color: var(--sidebar-text); font-size: 18px; }
        .workspace-item { margin-bottom: 5px; }
        .workspace-item > a, .workspace-item-label { /* Aplica estilos tanto al <a> directo como al <label> */
            font-weight: 600; color: #fff; display:flex; align-items: center; gap:10px; padding:10px; border-radius:8px; font-size: 14px;
            cursor: pointer;
        }
        .workspace-item > a:hover, .workspace-item-label:hover { background: #2a2e4c; }
        .board-list { 
            padding-left: 25px; margin-top: 5px; 
            max-height: 0; /* Oculto por defecto */
            overflow: hidden;
            transition: max-height 0.3s ease-out, opacity 0.3s ease-out;
            opacity: 0;
        }
        .board-list a { display:block; color: var(--sidebar-text); padding: 6px 0; font-size: 13px; opacity: 0.8; }
        .board-list a:hover { color: #fff; opacity: 1; }

        /* ======= ESTILOS PARA EL SIDEBAR COLAPSADO (CSS PUREO) ======= */
        .sidebar-toggle-checkbox:checked ~ .sidebar {
            width: 80px; /* Ancho cuando está colapsado */
        }

        .sidebar-toggle-checkbox:checked ~ .sidebar .sidebar-header .logo span,
        .sidebar-toggle-checkbox:checked ~ .sidebar .sidebar-content {
            opacity: 0; /* Oculta el texto y contenido con opacidad */
            pointer-events: none; /* Deshabilita clics en elementos ocultos */
        }
        .sidebar-toggle-checkbox:checked ~ .sidebar .collapse-btn i {
             transform: rotate(180deg); /* Gira la flecha */
        }

        .collapse-btn {
            color: white;
            font-size: 18px;
            display: flex; /* Asegura que el icono no se salga */
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
        }

        /* ======= ESTILOS PARA WORKSPACE EXPANDIBLE (CSS PURO) ======= */
        .workspace-item-label {
            position: relative; /* Para posicionar el chevron */
        }
        .workspace-chevron {
            margin-left: auto; /* Empuja el chevron a la derecha */
            transition: transform 0.3s ease;
            font-size: 12px;
        }
        .workspace-toggle-checkbox:checked + .workspace-item-label .workspace-chevron {
            transform: rotate(90deg); /* Gira el chevron cuando está expandido */
        }
        .workspace-toggle-checkbox:checked + .workspace-item-label + .board-list {
            max-height: 200px; /* Suficiente alto para mostrar los elementos */
            opacity: 1;
        }


        /* ====== MAIN CONTENT ====== */
        .main-content { flex:1; padding:25px 30px; }

        /* ====== BOARD HEADER ====== */
        .board-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; }
        .board-title { display:flex; align-items:center; gap:10px; }
        .board-title h1 { font-size:24px; font-weight:700; color:var(--text-dark); }
        .board-title i { color: #888; cursor: pointer; transition: 0.2s; }
        .board-title i:hover { color: #FDAB3D; transform: scale(1.2); }
        .board-actions { display:flex; align-items:center; gap:15px; }
        .search-board { padding:8px 12px; border-radius:var(--border-radius-md); border:1px solid var(--border-color); width:200px; transition:0.3s; }
        .search-board:focus { border-color:var(--primary-color); outline:none; box-shadow:0 0 6px rgba(93,95,239,0.3); }
        .btn-monday { background:var(--primary-color); color:#fff; border-radius:var(--border-radius-md); padding:8px 16px; display:flex; align-items:center; gap:6px; font-weight:600; transition:0.3s; }
        .btn-monday:hover { background:var(--primary-hover); }
        .user-avatar { width:36px; height:36px; border-radius:50%; background:var(--primary-color); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:600; cursor:pointer; }
        
        /* VIEW SWITCHER */
        .view-switcher { display:flex; background: #E9E9F0; padding: 4px; border-radius: 10px; }
        .view-switcher a { /* Ahora son enlaces, no botones */
            padding: 6px 12px; border-radius: 8px; font-weight: 500; display:flex; align-items:center; gap:6px; color: #555; 
            transition: background 0.2s, color 0.2s;
        }
        .view-switcher a.active { background: var(--card-bg); color: var(--primary-color); box-shadow: 0 2px 4px rgba(0,0,0,0.08); }


        /* ====== GRID DE CARDS ====== */
        .cards-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(400px, 1fr)); gap:25px; }

        /* ====== CARD MONDAY (CON SOMBRA MÁS SUTIL) ====== */
        .card-monday {
            background:var(--card-bg); border-radius:var(--border-radius-lg); padding:20px;
            box-shadow: var(--shadow-soft);
            transition:0.3s ease; border-left:6px solid var(--primary-color);
        }
        .card-monday:hover { transform:translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
        .card-title{ font-size:18px; font-weight:600; color:var(--primary-color); margin-bottom:15px; display:flex; align-items:center; }
        .card-title i{ margin-right:8px; }

        /* ====== TABLAS Y BADGES (SIN CAMBIOS, YA ESTÁN BIEN) ====== */
        .table-monday { width:100%; border-collapse:collapse; font-size:0.9em; }
        .table-monday th, .table-monday td { padding:12px; text-align:left; border-bottom:1px solid var(--border-color); }
        .table-monday th { background:#F3F4FB; font-weight:600; color:var(--primary-color); }
        .table-monday tbody tr:hover { background:#F9F9FE; }

        .badge { display:inline-block; padding:5px 10px; border-radius:12px; color:#fff; font-size:12px; text-transform:capitalize; font-weight:500; }
        .badge.blue{background:#5D5FEF;} .badge.green{background:#00C875;} .badge.red{background:#E2445C;} .badge.orange{background:#FDAB3D;} .badge.purple{background:#A259FF;}
    </style>
</x-base-layout>