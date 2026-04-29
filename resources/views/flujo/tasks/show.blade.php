<x-base-layout>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        :root {
            --primary: #2563eb;
            --primary-light: #eff6ff;
            --dark: #0f172a;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-900: #0f172a;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
        }

        /* CONFIGURACIÓN GLOBAL */
        body { background-color: #f4f7fa; font-family: 'Inter', sans-serif; color: var(--slate-900); -webkit-font-smoothing: antialiased; }
        
        /* 1. AJUSTE DE ANCHO PRO (Aprovechamiento de espacio) */
        .task-show-wrapper { max-width: 1600px; margin: 30px auto; padding: 0 30px; }

        /* HEADER UI PREMIUM */
        .show-header { background: white; padding: 25px 35px; border-radius: 24px; border: 1px solid var(--slate-200); margin-bottom: 25px; box-shadow: var(--shadow-sm); }
        .breadcrumb-simple { font-size: 11px; font-weight: 800; color: var(--slate-500); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px; display: block; }
        .main-title { font-size: 2.2rem; font-weight: 800; letter-spacing: -0.04em; color: var(--slate-900); margin: 0; line-height: 1.1; }
        
        .badge-status-pill { padding: 6px 16px; border-radius: 50px; font-size: 0.75rem; font-weight: 800; border: 1px solid rgba(0,0,0,0.05); display: inline-flex; align-items: center; gap: 8px; text-transform: uppercase; }
        .st-pendiente { background: #fffbeb; color: #b45309; }
        .st-en_proceso { background: #eff6ff; color: #1e40af; }
        .st-completado { background: #f0fdf4; color: #166534; }

        /* BANNER DE WORKFLOW TIPO DASHBOARD */
        .workflow-highlight-banner {
            background: white; border-radius: 20px; border: 1px solid var(--slate-200); border-left: 8px solid var(--primary);
            padding: 20px 30px; margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between; box-shadow: var(--shadow-md);
        }
        .wf-icon-box { width: 52px; height: 52px; background: var(--primary-light); color: var(--primary); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .wf-overtitle { font-size: 10px; font-weight: 800; color: var(--slate-500); letter-spacing: 1px; text-transform: uppercase; }
        .wf-main-name { font-size: 1.3rem; font-weight: 800; color: var(--dark); margin: 0; }

        /* 2. LAYOUT GRID SOLUCIONADO (minmax evita que se espiche la derecha) */
        .show-grid { 
            display: grid; 
            grid-template-columns: minmax(0, 1fr) 380px; 
            gap: 30px; 
            align-items: start;
        }
        @media (max-width: 1200px) { .show-grid { grid-template-columns: minmax(0, 1fr); } }

        /* TARJETAS GLASSMISM MEJORADO */
        .glass-card { background: white; border-radius: 24px; padding: 30px; border: 1px solid var(--slate-200); box-shadow: var(--shadow-sm); width: 100%; transition: transform 0.2s; }
        .section-header { margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid var(--slate-100); display: flex; align-items: center; justify-content: space-between; }
        .section-title { font-size: 11px; font-weight: 800; display: flex; align-items: center; gap: 10px; color: var(--slate-900); text-transform: uppercase; letter-spacing: 1px; margin: 0; }
        .section-title i { color: var(--primary); font-size: 1.2rem; }

        /* BARRA DE PROGRESO MODERNA */
        .progress-box-modern { background: #ffffff; padding: 20px; border-radius: 18px; border: 1px solid var(--slate-200); }
        .label-status-top { font-size: 10px; font-weight: 800; color: var(--slate-500); letter-spacing: 0.1em; }
        .label-count-sub { font-size: 14px; font-weight: 700; color: var(--slate-600); }
        .pct-display-large { font-size: 32px; font-weight: 900; line-height: 1; }
        .progress-track-modern { height: 12px; background: var(--slate-100); border-radius: 50px; overflow: hidden; position: relative; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); }
        .progress-fill-modern { height: 100%; border-radius: 20px; transition: width 1.2s cubic-bezier(0.4, 0, 0.2, 1); position: relative; }
        .progress-shine-effect { position: absolute; top: 0; right: 0; bottom: 0; left: 0; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent); animation: shine-ui 3s infinite; }

        @keyframes shine-ui { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }

        /* 3. CONTENEDOR DE TABLA CON SCROLL PARA EVITAR DESBORDAMIENTOS */
        .table-responsive { width: 100%; overflow-x: auto; border-radius: 12px; }
        
        /* TABLA EXCEL MODO LECTURA UX */
        .excel-read-mode { width: 100%; min-width: 800px; }
        .excel-read-mode thead th { background: var(--slate-50); padding: 16px 14px; font-size: 10px; font-weight: 800; color: var(--slate-500); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid var(--slate-200); }
        .excel-read-mode tbody td { padding: 14px; vertical-align: middle; border-bottom: 1px solid var(--slate-100); font-size: 13.5px; }
        .row-done-ui { background-color: #f0fdf480 !important; }
        .status-badge-excel { padding: 4px 10px; border-radius: 8px; font-size: 10px; font-weight: 800; background: var(--slate-100); color: var(--slate-600); }
        .status-badge-excel.completa { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .status-badge-excel.en-proceso { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }

        /* FEED DE ACTIVIDAD / TIMELINE */
        .comment-timeline { display: flex; flex-direction: column; gap: 8px; }
        .comment-bubble-wrapper { display: flex; gap: 20px; margin-bottom: 15px; }
        .avatar-box { width: 42px; height: 42px; background: var(--dark); color: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800; shadow: var(--shadow-sm); }
        .timeline-line { width: 2px; flex-grow: 1; background: var(--slate-100); margin-top: 8px; }
        .comment-body-card { flex: 1; background: white; border: 1px solid var(--slate-200); padding: 20px; border-radius: 0 20px 20px 20px; shadow: var(--shadow-sm); }

        /* ATTACHMENT CARD */
        .attachment-card { margin-top: 12px; border: 1px solid var(--slate-200); border-radius: 15px; overflow: hidden; background: white; }
        .attachment-image-preview { height: 140px; background-size: cover; background-position: center; position: relative; }
        .preview-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; opacity: 0; transition: 0.2s; text-decoration: none; }
        .attachment-image-preview:hover .preview-overlay { opacity: 1; }
        .attachment-info { padding: 12px; display: flex; align-items: center; gap: 12px; }
        .file-type-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .is-img { background: #eff6ff; color: #3b82f6; }
        .is-pdf { background: #fff1f2; color: #e11d48; }
        .file-details { flex: 1; display: flex; flex-direction: column; }
        .file-name { font-size: 0.8rem; font-weight: 700; color: var(--slate-900); }
        .file-meta { font-size: 0.7rem; color: var(--slate-500); }
        .btn-open-file { color: var(--slate-400); font-size: 1.1rem; transition: 0.2s; }
        .btn-open-file:hover { color: var(--primary); }

        /* 4. BLINDAJE DE LA BARRA LATERAL (Para que no se encoja) */
        .side-column { display: flex; flex-direction: column; gap: 25px; width: 380px; min-width: 380px; }
        
        .highlight-border { border-top: 6px solid var(--primary) !important; }
        .data-row { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; }
        .icon-box-side { width: 38px; height: 38px; background: var(--slate-50); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--slate-400); font-size: 1rem; border: 1px solid var(--slate-200); }
        .data-label { display: block; font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--slate-500); letter-spacing: 0.5px; }
        .data-value { font-size: 0.95rem; font-weight: 700; color: var(--slate-900); }

        .audit-node { position: relative; padding-left: 25px; padding-bottom: 25px; border-left: 2px solid var(--slate-100); margin-left: 10px; }
        .node-circle { position: absolute; left: -7px; top: 0; width: 12px; height: 12px; background: var(--primary); border: 3px solid white; border-radius: 50%; box-shadow: 0 0 0 2px var(--slate-100); }

        /* BOTONES CORPORATIVOS */
        .btn-ux-primary { background: var(--dark); color: white; padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; border: none; }
        .btn-ux-primary:hover { background: var(--primary); transform: translateY(-2px); box-shadow: 0 10px 20px -10px var(--primary); }

        /* IMPRESIÓN */
        @media print {
            .no-print { display: none !important; }
            .task-show-wrapper { max-width: 100%; padding: 0; margin: 0; }
            .glass-card { border: 1px solid #eee; box-shadow: none; page-break-inside: avoid; }
        }
    </style>

    {{-- LÓGICA PHP PRINCIPAL CENTRALIZADA --}}
    @php
        $rawDesc = trim($task->descripcion);
        $isJson = str_starts_with($rawDesc, '[');
        
        $totalItems = 0;
        $itemsDone = 0;
        $htmlLegacy = '';
        $sheetData = [];

        if ($isJson) {
            $sheetData = json_decode($rawDesc, true) ?? [];
            $totalItems = count($sheetData);
            foreach($sheetData as $row) {
                if (isset($row['checked']) && $row['checked']) $itemsDone++;
            }
        } else {
            $descLines = $rawDesc ? explode("\n", $rawDesc) : [];
            foreach($descLines as $line) {
                $line = trim($line);
                if (preg_match('/^\[([xX\s])\]\s*-?\s*(.*)$/', $line, $matches)) {
                    $totalItems++;
                    $isChecked = strtolower(trim($matches[1])) === 'x';
                    if ($isChecked) $itemsDone++;
                    
                    $text = $matches[2];
                    if ($isChecked) {
                        // Usando tus colores exactos de producción
                        $htmlLegacy .= '<div style="display:flex; align-items:flex-start; gap:8px; margin-bottom:8px; padding: 6px 12px; background: #f0fdf4; border-radius: 8px; border: 1px solid #bbf7d0;"><i class="fas fa-check-square" style="color:var(--success); margin-top:3px; font-size: 1.1em;"></i> <span style="text-decoration:line-through; color:var(--slate-500); font-weight: 500;">' . e($text) . '</span></div>';
                    } else {
                        $htmlLegacy .= '<div style="display:flex; align-items:flex-start; gap:8px; margin-bottom:8px; padding: 6px 12px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;"><i class="far fa-square" style="color:var(--slate-400); margin-top:3px; font-size: 1.1em;"></i> <span style="color:var(--slate-900); font-weight: 500;">' . e($text) . '</span></div>';
                    }
                } elseif ($line !== '') {
                    $htmlLegacy .= '<p style="margin-bottom:8px; padding: 0 4px;">' . nl2br(e($line)) . '</p>';
                } else {
                    $htmlLegacy .= '<div style="height: 8px;"></div>';
                }
            }
        }

        // Cálculos para Gráficos y UI
        $pct = $totalItems > 0 ? round(($itemsDone / $totalItems) * 100) : 0;
        $colorProgreso = $pct == 100 ? '#10b981' : ($pct > 50 ? '#3b82f6' : '#f59e0b');
        $pendingTasks = max(0, $totalItems - $itemsDone); // Seguridad
        $commentsCount = $task->comments->count();
        $historyCount = $task->histories->count();
    @endphp

    <div class="task-show-wrapper print-area">
        {{-- Header Minimalista y Amigable (Bootstrap Nativo) --}}
        <header class="bg-white rounded-4 shadow-sm border p-4 mb-4">
            
            {{-- Fila Superior: Navegación y Acciones Globales --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3">
                <div class="d-flex flex-wrap gap-2 no-print">
                    @if($task->workflow)
                        <a href="{{ route('flujo.workflows.show', $task->workflow->id) }}" 
                           class="btn btn-sm btn-light text-secondary rounded-pill fw-semibold px-3 border" title="Volver al Workflow">
                            <i class="fas fa-arrow-left me-1"></i> Proyecto
                        </a>
                    @endif
                    <a href="{{ route('flujo.tasks.index') }}" 
                        class="btn btn-sm btn-light text-secondary rounded-pill fw-semibold px-3 border">
                        <i class="fas fa-tasks me-1"></i> Tareas
                    </a>
                    
                    {{-- MEJORA UX: Salto rápido a la vista de edición --}}
                    <a href="{{ route('flujo.tasks.edit', $task->id) }}" 
                        class="btn btn-sm btn-light text-primary rounded-pill fw-semibold px-3 border border-primary-subtle" title="Editar esta tarea">
                        <i class="fas fa-pen me-1"></i> Editar
                    </a>
                </div>
                
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill fw-semibold d-none d-sm-inline-block">
                        <span class="text-primary">Operaciones</span> / #{{ $task->id }}
                    </span>
                </div>
            </div>

            {{-- Fila Inferior: Título, Meta-datos y Botones de Acción --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-4">
                
                {{-- Bloque Izquierdo: Título y Estado --}}
                <div>
                    <h1 class="h2 fw-bolder text-dark mb-1">{{ $task->titulo }}</h1>
                    
                    {{-- MEJORA UX: Feedback de última actividad --}}
                    <p class="text-muted small mb-3">
                        <i class="fas fa-history me-1"></i> Última actualización: {{ $task->updated_at->diffForHumans() }}
                    </p>
                    
                    <div class="d-flex flex-wrap align-items-center gap-2 text-secondary small fw-medium">
                        {{-- Badge de Estado --}}
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill d-flex align-items-center gap-2 shadow-sm">
                            @if(in_array(strtolower($task->estado), ['completado', 'finalizado', 'revisado']))
                                <i class="fas fa-circle text-success" style="font-size: 8px;"></i>
                            @elseif(strtolower($task->estado) == 'en_proceso')
                                <i class="fas fa-circle text-primary" style="font-size: 8px;"></i>
                            @else
                                <i class="fas fa-circle text-warning" style="font-size: 8px;"></i>
                            @endif
                            {{ str_replace('_', ' ', ucfirst($task->estado)) }}
                        </span>

                        {{-- Badge de Prioridad --}}
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill d-flex align-items-center gap-2 shadow-sm">
                            @if(strtolower($task->prioridad) == 'alta')
                                <i class="fas fa-flag text-danger"></i>
                            @elseif(strtolower($task->prioridad) == 'media')
                                <i class="fas fa-flag text-warning"></i>
                            @else
                                <i class="fas fa-flag text-secondary"></i>
                            @endif
                            Prioridad {{ ucfirst($task->prioridad) }}
                        </span>

                        <span class="d-flex align-items-center gap-1 ms-1">
                            <i class="far fa-calendar-alt text-muted"></i> Creado el {{ $task->created_at->format('d M, Y') }}
                        </span>
                    </div>
                </div>

                {{-- Bloque Derecho: Botones de Acción --}}
                <div class="d-flex flex-wrap gap-2 no-print">
                    <button onclick="window.print()" class="btn btn-outline-secondary px-4 fw-semibold rounded-pill d-flex align-items-center gap-2 shadow-sm">
                        <i class="fas fa-file-pdf text-danger"></i> Exportar PDF
                    </button>
                    
                    <a href="{{ route('flujo.tasks.edit', $task) }}" class="btn btn-dark px-4 fw-semibold rounded-pill d-flex align-items-center gap-2 shadow-sm">
                        <i class="fas fa-cog"></i> Gestionar
                    </a>
                </div>
            </div>
            
        </header>

        {{-- INTEGRACIÓN: Banner Resaltado de Contexto (Workflow/Proyecto) --}}
        <div class="workflow-highlight-banner">
            <div class="wf-content">
                <div class="wf-icon-box">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <div class="wf-text-group">
                    <span class="wf-overtitle">Proyecto / Workflow Vinculado</span>
                    <h2 class="wf-main-name">{{ $task->workflow->nombre ?? 'Sin Workflow Asignado' }}</h2>
                </div>
            </div>
            @if($task->workflow)
                <a href="{{ route('flujo.workflows.show', $task->workflow->id) }}" class="btn-wf-link no-print">
                    Ir al Proyecto <i class="fas fa-chevron-right"></i>
                </a>
            @endif
        </div>

        <div class="show-grid">
            {{-- Columna Principal --}}
            <main class="main-column">

                {{-- NUEVO DASHBOARD GERENCIAL ACTUALIZADO --}}
                <section class="glass-card mb-4 dashboard-section shadow-sm">
                    <div class="section-header border-bottom pb-3 mb-4 d-flex justify-content-between align-items-center">
                        <h2 class="section-title"><i class="fas fa-chart-pie text-primary me-2"></i> Rendimiento Gerencial</h2>
                        <span class="badge bg-light text-secondary border rounded-pill px-3 py-1 shadow-sm" style="font-size: 10px; font-weight: 800; letter-spacing: 0.5px;">
                            VISTA ANALÍTICA
                        </span>
                    </div>
                    
                    <div class="row align-items-center pt-2">
                        {{-- Gráfico 1: Avance Operativo --}}
                        <div class="col-md-6 mb-4 mb-md-0 position-relative border-end-md" style="border-color: var(--slate-100) !important;">
                            <div class="text-center mb-3">
                                <span class="d-block fw-bold" style="font-size: 12px; color: var(--slate-500); text-transform: uppercase; letter-spacing: 1px;">Avance de Tareas</span>
                            </div>
                            
                            <div class="chart-container mx-auto" style="position: relative; height: 200px; width: 100%; display: flex; justify-content: center;">
                                @if($totalItems > 0)
                                    <canvas id="taskProgressChart"></canvas>
                                    {{-- Texto superpuesto en el centro de la dona usando CSS --}}
                                    <div class="position-absolute top-50 start-50 translate-middle text-center" style="margin-top: 5px; pointer-events: none;">
                                        <span class="d-block text-dark" style="font-size: 28px; font-weight: 900; line-height: 1;">{{ $pct }}%</span>
                                        <span class="text-muted" style="font-size: 9px; font-weight: 800; letter-spacing: 1px;">COMPLETADO</span>
                                    </div>
                                @else
                                    {{-- Estado vacío elegante si no hay tareas --}}
                                    <div class="d-flex flex-column justify-content-center align-items-center h-100 w-100 opacity-50 bg-light rounded-4 border border-dashed p-4">
                                        <i class="fas fa-chart-pie fa-2x text-muted mb-2"></i>
                                        <span class="small fw-bold text-muted">Sin datos de avance</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Gráfico 2: Volumen de Interacciones --}}
                        <div class="col-md-6 position-relative ps-md-4">
                            <div class="text-center mb-3">
                                <span class="d-block fw-bold" style="font-size: 12px; color: var(--slate-500); text-transform: uppercase; letter-spacing: 1px;">Volumen de Actividad</span>
                            </div>
                            <div class="chart-container" style="position: relative; height: 200px; width: 100%;">
                                <canvas id="activityChart"></canvas>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Sección: Descripción con Checklists Visuales y Barra de Progreso --}}
                <section class="glass-card mb-4">
                    <div class="section-header d-flex justify-content-between align-items-center">
                        <h2 class="section-title"><i class="fas fa-file-alt text-primary me-2"></i> Detalles simples y claros de las subtareas y su progreso de la tarea principal: {{ $task->titulo }}</h2>
                        {{-- CONTADOR RÁPIDO EN EL HEADER --}}
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge rounded-pill px-3 fw-bold shadow-sm" style="background: #eff6ff; color: #3b82f6; border: 1px solid #dbeafe; font-size: 13px;">
                                <i class="fas fa-tasks me-1"></i> {{ $itemsDone }} de {{ $totalItems }} tareas
                            </span>
                        </div>
                    </div>

                    <div class="description-content p-2">
                        @if ($totalItems > 0)
                            {{-- BARRA DE PROGRESO UX MEJORADA --}}
                            <div class="progress-box-modern mb-4 shadow-sm border">
                                <div class="d-flex justify-content-between align-items-end mb-2">
                                    <div>
                                        <span class="label-status-top">ESTADO DE CUMPLIMIENTO</span>
                                        <div class="label-count-sub">{{ $itemsDone }} actividades completadas de un total de {{ $totalItems }}</div>
                                    </div>
                                    <div class="text-end">
                                        <span class="pct-display-large" style="color: {{ $colorProgreso }};">{{ $pct }}%</span>
                                    </div>
                                </div>
                                <div class="progress-track-modern">
                                    <div class="progress-fill-modern" style="width: {{ $pct }}%; background-color: {{ $colorProgreso }};">
                                        <div class="progress-shine-effect"></div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($isJson)
                            {{-- TABLA ESTILO EXCEL MODO LECTURA --}}
                            <div class="table-responsive rounded-3 border shadow-sm bg-white">
                                <table class="table table-hover mb-0 excel-read-mode">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 50px;">✓</th>
                                            <th style="width: 120px;">FECHA</th>
                                            <th style="width: 150px;">RESPONSABLE</th>
                                            <th style="width: 110px;">ESTADO</th>
                                            <th class="text-center" style="width: 70px;">HRS</th>
                                            <th>DESCRIPCIÓN DE ACTIVIDAD</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sheetData as $row)
                                            <tr class="{{ ($row['checked'] ?? false) ? 'row-done-ui' : '' }}">
                                                <td class="text-center">
                                                    <i class="{{ ($row['checked'] ?? false) ? 'fas fa-check-circle text-success' : 'far fa-circle text-muted' }}" style="font-size: 1.1rem;"></i>
                                                </td>
                                                <td class="small fw-bold text-secondary">{{ $row['date'] ?? '--' }}</td>
                                                <td class="small text-dark fw-semibold">{{ $row['responsable'] ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="status-badge-excel {{ str_replace(' ', '-', ($row['status'] ?? 'pendiente')) }}">
                                                        {{ ucfirst($row['status'] ?? 'pendiente') }}
                                                    </span>
                                                </td>
                                                <td class="text-center fw-bold text-primary">{{ $row['tiempo'] ?? '0' }}</td>
                                                <td class="small text-muted" style="border-left: 4px solid {{ $row['color'] ?? '#eee' }}; padding-left: 12px;">
                                                    {{ $row['descripcion'] ?? '' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            {{-- VISTA LEGACY SI NO ES JSON --}}
                            <div class="legacy-view-box p-3 border rounded-3 bg-light">
                                {!! $htmlLegacy !== '' ? $htmlLegacy : '<p class="text-muted mb-0 small">No hay especificaciones registradas.</p>' !!}
                            </div>
                        @endif
                    </div>
                </section>

                {{-- Sección: Feed de Actividad y Comentarios (LÓGICA ORIGINAL INTACTA) --}}
                <section class="glass-card page-break-before">
                    <div class="section-header space-between">
                        <h2 class="section-title"><i class="fas fa-comments"></i> Línea de Tiempo y Feedback</h2>
                        <span class="comment-count">{{ $commentsCount }} Mensajes</span>
                    </div>
                    
                    <div class="comment-timeline">
                        @forelse($task->comments->sortByDesc('created_at') as $comment)
                            <div class="comment-bubble-wrapper avoid-break">
                                <div class="comment-avatar-container">
                                    <div class="avatar-box">{{ substr($comment->user->name ?? 'S', 0, 1) }}</div>
                                    <div class="timeline-line"></div>
                                </div>
                                <div class="comment-body">
                                    <div class="comment-header">
                                        <span class="user-name">{{ $comment->user->name ?? 'Sistema' }}</span>
                                        <span class="time-ago"><i class="far fa-clock"></i> {{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    
                                    <div class="comment-text-box" style="padding: 0; background: transparent; border: none;">
                                        {{-- INICIO PARSEO DEL FORMATO REPORTE DE DESARROLLO --}}
                                        @php
                                            $textoRaw = trim($comment->comentario);
                                            $esDevTemplate = str_starts_with($textoRaw, '[') && str_ends_with($textoRaw, ']');
                                            
                                            if ($esDevTemplate) {
                                                $textoLimpio = trim(substr($textoRaw, 1, -1));
                                                $textoLimpio = e($textoLimpio);
                                                // Reemplazar etiquetas por títulos corporativos
                                                $textoFormateado = preg_replace('/([A-ZÁÉÍÓÚÑ \/]+):/', '<span style="display: block; margin-top: 14px; font-size: 10px; font-weight: 800; color: var(--primary); letter-spacing: 0.5px;">$1</span>', $textoLimpio);
                                                $htmlFinal = '<div style="background: #ffffff; border: 1px solid var(--slate-200); border-left: 4px solid var(--primary); padding: 5px 20px 20px; border-radius: 12px; margin-top: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">' . nl2br($textoFormateado) . '</div>';
                                            } else {
                                                // Comentario Normal
                                                $htmlFinal = '<div style="background: var(--slate-50); border-radius: 0 15px 15px 15px; padding: 15px; border: 1px solid var(--slate-100);"><p style="margin:0; font-size: 0.9rem; line-height: 1.5; color: #334155;">' . nl2br(e($textoRaw)) . '</p></div>';
                                            }
                                        @endphp
                                        
                                        {!! $htmlFinal !!}
                                        {{-- FIN PARSEO --}}
                                    </div>

                                    @if($comment->soporte)
                                        @php
                                            $extension = pathinfo($comment->soporte, PATHINFO_EXTENSION);
                                            $esImagen = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                            $urlS3 = Storage::disk('s3')->temporaryUrl($comment->soporte, now()->addMinutes(30));
                                        @endphp
                                        <div class="attachment-card no-print">
                                            @if($esImagen)
                                                <div class="attachment-image-preview" style="background-image: url('{{ $urlS3 }}')">
                                                    <a href="{{ $urlS3 }}" target="_blank" class="preview-overlay"><i class="fas fa-search-plus"></i></a>
                                                </div>
                                            @endif
                                            <div class="attachment-info">
                                                <div class="file-type-icon {{ $esImagen ? 'is-img' : 'is-pdf' }}">
                                                    <i class="fas {{ $esImagen ? 'fa-image' : 'fa-file-pdf' }}"></i>
                                                </div>
                                                <div class="file-details">
                                                    <span class="file-name">Documento de Soporte</span>
                                                    <span class="file-meta">Formato: .{{ strtoupper($extension) }}</span>
                                                </div>
                                                <a href="{{ $urlS3 }}" target="_blank" class="btn-open-file" title="Ver Archivo">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="empty-feed" style="text-align: center; padding: 30px; color: var(--slate-400);">
                                <i class="fas fa-comment-slash fa-2x"></i>
                                <p style="margin-top: 10px; font-size: 13px;">Aún no hay feedback registrado.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </main>

            {{-- Columna Lateral (ORIGINAL INTACTA) --}}
            <aside class="side-column">
                <section class="glass-card mb-4 highlight-border">
                    <h2 class="section-title"><i class="fas fa-fingerprint"></i> Estatus Operativo</h2>
                    <div class="status-panel">
                        <div class="status-item">
                            <span class="label" style="font-size: 10px; font-weight: 800; color: var(--slate-500); text-transform: uppercase;">Prioridad</span>
                            <div class="priority-box p-{{ strtolower($task->prioridad) }}" style="display: flex; align-items: center; gap: 8px; margin-top: 5px;">
                                @if(strtolower($task->prioridad) == 'alta')
                                    <span class="p-dot" style="width: 10px; height: 10px; border-radius: 50%; background: var(--danger);"></span>
                                @elseif(strtolower($task->prioridad) == 'media')
                                    <span class="p-dot" style="width: 10px; height: 10px; border-radius: 50%; background: var(--warning);"></span>
                                @else
                                    <span class="p-dot" style="width: 10px; height: 10px; border-radius: 50%; background: var(--slate-400);"></span>
                                @endif
                                <span class="p-label fw-bold">{{ ucfirst($task->prioridad) }}</span>
                            </div>
                        </div>
                        
                        <div class="data-grid-side" style="margin-top: 20px;">
                            <div class="data-row">
                                <i class="fas fa-user-tie icon-val"></i>
                                <div>
                                    <span class="data-label">Responsable</span>
                                    <span class="data-value">{{ $task->user->name ?? 'Sin asignar' }}</span>
                                </div>
                            </div>
                            <div class="data-row">
                                <i class="fas fa-project-diagram icon-val"></i>
                                <div>
                                    <span class="data-label">Workflow</span>
                                    <span class="data-value">{{ $task->workflow->nombre ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="data-row {{ $task->fecha_limite && $task->fecha_limite->isPast() ? 'alert-danger' : '' }}">
                                <i class="fas fa-calendar-check icon-val"></i>
                                <div>
                                    <span class="data-label">Vencimiento</span>
                                    <span class="data-value">{{ $task->fecha_limite ? $task->fecha_limite->format('d M, Y') : 'Indefinido' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="glass-card">
                    <h2 class="section-title"><i class="fas fa-stream"></i> Auditoría de Estados</h2>
                    <div class="audit-timeline">
                        @forelse($task->histories->sortByDesc('created_at')->take(5) as $history)
                            <div class="audit-node">
                                <div class="node-circle"></div>
                                <div class="node-content">
                                    <span class="node-date" style="font-size: 11px; color: var(--slate-500);">{{ $history->created_at->diffForHumans() }}</span>
                                    <p class="node-text" style="font-size: 13px; margin-top: 2px;"><strong>{{ explode(' ', $history->user->name ?? 'User')[0] }}</strong> cambió a <span class="node-status text-primary fw-bold">{{ $history->estado_nuevo }}</span></p>
                                </div>
                            </div>
                        @empty
                            <p class="empty-txt-small" style="font-size: 12px; color: var(--slate-400);">Sin historial de cambios.</p>
                        @endforelse
                    </div>
                </section>
                
                <div class="system-footprint" style="margin-top: 20px; text-align: center; font-size: 11px; color: var(--slate-400);">
                    <p class="mb-1"><i class="fas fa-code-branch"></i> ID: UUID-{{ substr($task->id, 0, 8) }}</p>
                    <p><i class="fas fa-history"></i> Actualizado: {{ $task->updated_at->format('d/m H:i') }}</p>
                </div>
            </aside>
        </div>
    </div>

    {{-- LIBRERÍA DE GRÁFICOS (CHART.JS) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- SCRIPT ACTUALIZADO PARA LOS GRÁFICOS GERENCIALES --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Variables unificadas desde el bloque PHP principal
            const totalItems = {{ $totalItems ?? 0 }};
            const itemsDone = {{ $itemsDone ?? 0 }};
            const pendingTasks = Math.max(0, totalItems - itemsDone);
            const commentsCount = {{ $commentsCount ?? 0 }};
            const historyCount = {{ $historyCount ?? 0 }};

            // 1. GRÁFICO DE AVANCE DE TAREAS (Doughnut) - Sin plugin complejo, usando overlay HTML
            const canvasProgress = document.getElementById('taskProgressChart');
            if(canvasProgress && totalItems > 0) {
                new Chart(canvasProgress.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Completadas', 'Pendientes'],
                        datasets: [{
                            data: [itemsDone, pendingTasks],
                            backgroundColor: ['#10b981', '#f1f5f9'],
                            hoverBackgroundColor: ['#059669', '#e2e8f0'],
                            borderWidth: 0,
                            cutout: '80%' // Dona delgada y elegante
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11, family: 'Inter', weight: 'bold' } } },
                            tooltip: { callbacks: { label: function(context) { return ' ' + context.label + ': ' + context.raw + ' Tareas'; } } }
                        }
                    }
                });
            }

            // 2. GRÁFICO DE VOLUMEN DE ACTIVIDAD (Bar)
            const canvasActivity = document.getElementById('activityChart');
            if(canvasActivity) {
                new Chart(canvasActivity.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: ['Feed Mensajes', 'Cambios Edo.'],
                        datasets: [{
                            label: 'Interacciones',
                            data: [commentsCount, historyCount],
                            backgroundColor: ['#3b82f6', '#f59e0b'],
                            borderRadius: 6,
                            barPercentage: 0.5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: { label: function(context) { return ' Cantidad: ' + context.raw; } } }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0, font: {family: 'Inter'} }, grid: { borderDash: [4, 4], color: '#e2e8f0' } },
                            x: { grid: { display: false }, ticks: { font: {family: 'Inter', size: 11, weight: 'bold'} } }
                        }
                    }
                });
            }
        });
    </script>

</x-base-layout>