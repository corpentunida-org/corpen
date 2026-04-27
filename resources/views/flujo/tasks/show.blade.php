<x-base-layout>
    {{-- LÓGICA PHP PRINCIPAL EXTRAÍDA PARA ALIMENTAR LOS GRÁFICOS Y LA VISTA --}}
    @php
        $descLines = $task->descripcion ? explode("\n", $task->descripcion) : [];
        $htmlDesc = '';
        $totalTasks = 0;
        $completedTasks = 0;

        // Procesar descripción y contar tareas
        foreach($descLines as $line) {
            $line = trim($line);
            if (preg_match('/^\[([xX\s])\]\s*-?\s*(.*)$/', $line, $matches)) {
                $totalTasks++;
                $isChecked = strtolower(trim($matches[1])) === 'x';
                if ($isChecked) $completedTasks++;

                $text = $matches[2];
                if ($isChecked) {
                    $htmlDesc .= '<div style="display:flex; align-items:flex-start; gap:8px; margin-bottom:8px; padding: 6px 12px; background: #f0fdf4; border-radius: 8px; border: 1px solid #bbf7d0;"><i class="fas fa-check-square" style="color:var(--success); margin-top:3px; font-size: 1.1em;"></i> <span style="text-decoration:line-through; color:var(--slate-500); font-weight: 500;">' . e($text) . '</span></div>';
                } else {
                    $htmlDesc .= '<div style="display:flex; align-items:flex-start; gap:8px; margin-bottom:8px; padding: 6px 12px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;"><i class="far fa-square" style="color:var(--slate-400); margin-top:3px; font-size: 1.1em;"></i> <span style="color:var(--slate-900); font-weight: 500;">' . e($text) . '</span></div>';
                }
            } elseif ($line !== '') {
                $htmlDesc .= '<p style="margin-bottom:8px; padding: 0 4px;">' . nl2br(e($line)) . '</p>';
            } else {
                $htmlDesc .= '<div style="height: 8px;"></div>';
            }
        }

        $pendingTasks = $totalTasks - $completedTasks;
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

                {{-- NUEVO: DASHBOARD GERENCIAL (MÉTRICAS) --}}
                <section class="glass-card mb-4 dashboard-section">
                    <div class="section-header">
                        <h2 class="section-title"><i class="fas fa-chart-pie"></i> Dashboard de Métricas</h2>
                    </div>
                    <div class="metrics-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: center;">
                        <div class="chart-container" style="position: relative; height:200px; width:100%; display: flex; justify-content: center;">
                            <canvas id="taskProgressChart"></canvas>
                        </div>
                        <div class="chart-container" style="position: relative; height:200px; width:100%;">
                            <canvas id="activityChart"></canvas>
                        </div>
                    </div>
                </section>

                {{-- Sección: Descripción con Checklists Visuales y Barra de Progreso --}}
                <section class="glass-card mb-4">
                    <div class="section-header">
                        <h2 class="section-title"><i class="fas fa-file-alt"></i> Especificaciones y Avance</h2>
                    </div>
                    <div class="description-content">
                        @php
                            // Construcción dinámica de la BARRA DE PROGRESO si hay tareas
                            if ($totalTasks > 0) {
                                $percentage = round(($completedTasks / $totalTasks) * 100);
                                
                                // Lógica de colores Semáforo
                                $barColor = '#f59e0b'; // Naranja (iniciando)
                                if ($percentage == 100) {
                                    $barColor = '#10b981'; // Verde completado
                                } elseif ($percentage > 50) {
                                    $barColor = '#3b82f6'; // Azul avanzado
                                }

                                $progressBarHtml = '
                                <div style="margin-bottom: 25px; background: #ffffff; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                        <span style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fas fa-tasks"></i> Progreso Operativo</span>
                                        <span style="font-size: 16px; font-weight: 800; color: '.$barColor.';">'.$percentage.'%</span>
                                    </div>
                                    <div style="width: 100%; height: 10px; background-color: #f1f5f9; border-radius: 10px; overflow: hidden; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);">
                                        <div style="width: '.$percentage.'%; height: 100%; background-color: '.$barColor.'; border-radius: 10px; transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);"></div>
                                    </div>
                                </div>';

                                echo $progressBarHtml;
                            }

                            // Imprimir la descripción y los checks procesados (calculados arriba)
                            if ($htmlDesc !== '') {
                                echo $htmlDesc;
                            } else {
                                echo '<p class="text-muted" style="margin:0;">No hay especificaciones técnicas ni tareas registradas para esta unidad.</p>';
                            }
                        @endphp
                    </div>
                </section>

                {{-- Sección: Feed de Actividad y Comentarios --}}
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
                            <div class="empty-feed">
                                <i class="fas fa-comment-slash"></i>
                                <p>Aún no hay feedback registrado.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </main>

            {{-- Columna Lateral --}}
            <aside class="side-column">
                <section class="glass-card mb-4 highlight-border">
                    <h2 class="section-title"><i class="fas fa-fingerprint"></i> Estatus Operativo</h2>
                    <div class="status-panel">
                        <div class="status-item">
                            <span class="label">Prioridad</span>
                            <div class="priority-box p-{{ strtolower($task->prioridad) }}">
                                <span class="p-dot"></span>
                                <span class="p-label">{{ ucfirst($task->prioridad) }}</span>
                            </div>
                        </div>
                        
                        <div class="data-grid-side">
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
                                    <span class="node-date">{{ $history->created_at->diffForHumans() }}</span>
                                    <p class="node-text"><strong>{{ explode(' ', $history->user->name ?? 'User')[0] }}</strong> cambió a <span class="node-status">{{ $history->estado_nuevo }}</span></p>
                                </div>
                            </div>
                        @empty
                            <p class="empty-txt-small">Sin historial de cambios.</p>
                        @endforelse
                    </div>
                </section>
                
                <div class="system-footprint">
                    <p><i class="fas fa-code-branch"></i> ID: UUID-{{ substr($task->id, 0, 8) }}</p>
                    <p><i class="fas fa-history"></i> Actualizado: {{ $task->updated_at->format('d/m H:i') }}</p>
                </div>
            </aside>
        </div>
    </div>

    {{-- LIBRERÍA DE GRÁFICOS (CHART.JS) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- SCRIPT PARA INICIALIZAR LOS GRÁFICOS GERENCIALES --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Variables alimentadas por PHP
            const totalTasks = {{ $totalTasks }};
            const completedTasks = {{ $completedTasks }};
            const pendingTasks = {{ $pendingTasks }};
            const commentsCount = {{ $commentsCount }};
            const historyCount = {{ $historyCount }};

            // 1. GRÁFICO DE AVANCE DE SUBTAREAS (Doughnut)
            const ctxTask = document.getElementById('taskProgressChart').getContext('2d');
            if(totalTasks > 0) {
                new Chart(ctxTask, {
                    type: 'doughnut',
                    data: {
                        labels: ['Completadas', 'Pendientes'],
                        datasets: [{
                            data: [completedTasks, pendingTasks],
                            backgroundColor: ['#10b981', '#f1f5f9'],
                            hoverBackgroundColor: ['#059669', '#e2e8f0'],
                            borderWidth: 0,
                            cutout: '75%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11, family: 'Inter' } } },
                            tooltip: { callbacks: { label: function(context) { return ' ' + context.label + ': ' + context.raw + ' Tareas'; } } }
                        }
                    },
                    plugins: [{
                        id: 'textCenter',
                        beforeDraw: function(chart) {
                            var width = chart.width, height = chart.height, ctx = chart.ctx;
                            ctx.restore();
                            var fontSize = (height / 114).toFixed(2);
                            ctx.font = "bold " + fontSize + "em Inter";
                            ctx.textBaseline = "middle";
                            ctx.fillStyle = "#0f172a";
                            var text = Math.round((completedTasks/totalTasks)*100) + "%",
                                textX = Math.round((width - ctx.measureText(text).width) / 2),
                                textY = (height / 2) - 10;
                            ctx.fillText(text, textX, textY);
                            ctx.save();
                        }
                    }]
                });
            } else {
                // Mensaje si no hay checklist
                ctxTask.font = "13px Inter";
                ctxTask.fillStyle = "#64748b";
                ctxTask.textAlign = "center";
                ctxTask.fillText("Sin subtareas para medir", 150, 100);
            }

            // 2. GRÁFICO DE VOLUMEN DE ACTIVIDAD (Bar)
            const ctxAct = document.getElementById('activityChart').getContext('2d');
            new Chart(ctxAct, {
                type: 'bar',
                data: {
                    labels: ['Mensajes', 'Cambios Edo.'],
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
                        x: { grid: { display: false }, ticks: { font: {family: 'Inter', size: 11} } }
                    }
                }
            });
        });
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        :root {
            --primary: #2563eb;
            --dark: #0f172a;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-500: #64748b;
            --slate-900: #0f172a;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        body { background-color: #f4f7fa; font-family: 'Inter', sans-serif; color: var(--slate-900); }
        .task-show-wrapper { max-width: 1200px; margin: 30px auto; padding: 0 20px; }

        /* HEADER UI */
        .show-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .header-left { display: flex; align-items: center; gap: 20px; }
        .btn-back-circle { width: 42px; height: 42px; border-radius: 12px; background: white; border: 1px solid var(--slate-200); display: flex; align-items: center; justify-content: center; color: var(--slate-500); transition: all 0.2s; text-decoration: none; }
        .btn-back-circle:hover { background: var(--dark); color: white; transform: translateX(-3px); }
        
        .breadcrumb-simple { font-size: 0.75rem; font-weight: 700; color: var(--slate-500); margin-bottom: 5px; }
        .system-tag { color: var(--primary); text-transform: uppercase; }
        .main-title { font-size: 1.85rem; font-weight: 800; letter-spacing: -0.03em; color: var(--slate-900); margin: 0; line-height: 1.1; }
        
        .header-meta-info { display: flex; align-items: center; gap: 12px; margin-top: 10px; }
        .badge-status-pill { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; border: 1px solid rgba(0,0,0,0.05); display: flex; align-items: center; gap: 6px; }
        .st-pendiente { background: #fffbeb; color: #b45309; }
        .st-en_proceso { background: #eff6ff; color: #1e40af; }
        .st-completado { background: #f0fdf4; color: #166534; }
        .meta-date { font-size: 0.8rem; color: var(--slate-500); font-weight: 500; }

        .btn-corporate-primary { background: var(--dark); color: white; padding: 12px 20px; border-radius: 12px; font-weight: 600; font-size: 0.85rem; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: 0.3s; }
        .btn-corporate-primary:hover { background: var(--primary); box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2); transform: translateY(-2px); }

        .btn-corporate-secondary { background: white; color: var(--slate-700); padding: 12px 20px; border-radius: 12px; font-weight: 600; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; border: 1px solid var(--slate-200); cursor: pointer; }
        .btn-corporate-secondary:hover { background: #f8fafc; border-color: var(--slate-400); transform: translateY(-2px); }

        /* ESTILOS DEL BANNER DE WORKFLOW */
        .workflow-highlight-banner {
            background: white;
            border-radius: 18px;
            border: 1px solid var(--slate-200);
            border-left: 6px solid var(--primary);
            padding: 18px 25px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .wf-content { display: flex; align-items: center; gap: 20px; }
        .wf-icon-box { width: 45px; height: 45px; background: #eff6ff; color: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
        .wf-text-group { display: flex; flex-direction: column; }
        .wf-overtitle { font-size: 0.65rem; font-weight: 800; color: var(--slate-500); text-transform: uppercase; letter-spacing: 0.05em; }
        .wf-main-name { font-size: 1.15rem; font-weight: 800; color: var(--dark); margin: 0; }
        .btn-wf-link { background: #f8fafc; border: 1px solid var(--slate-200); padding: 8px 16px; border-radius: 10px; font-size: 0.8rem; font-weight: 700; color: var(--slate-700); text-decoration: none; transition: 0.2s; }
        .btn-wf-link:hover { background: var(--primary); color: white; border-color: var(--primary); }

        /* LAYOUT */
        .show-grid { display: grid; grid-template-columns: 1.8fr 1fr; gap: 25px; }
        @media (max-width: 992px) { .show-grid { grid-template-columns: 1fr; } .workflow-highlight-banner { flex-direction: column; align-items: flex-start; gap: 15px; } .btn-wf-link { width: 100%; text-align: center; justify-content: center; } .metrics-grid { grid-template-columns: 1fr !important; } }

        .glass-card { background: white; border-radius: 20px; border: 1px solid var(--slate-200); padding: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .section-header { margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--slate-100); display: flex; align-items: center; }
        .section-header.space-between { justify-content: space-between; }
        .section-title { font-size: 0.95rem; font-weight: 700; display: flex; align-items: center; gap: 10px; color: var(--slate-900); margin: 0; }
        .section-title i { color: var(--primary); }

        /* DESCRIPTION CHECKLIST FIXES */
        .description-content p { color: #334155; line-height: 1.6; }

        /* COMMENTS TIMELINE */
        .comment-timeline { display: flex; flex-direction: column; gap: 5px; }
        .comment-bubble-wrapper { display: flex; gap: 15px; }
        .comment-avatar-container { display: flex; flex-direction: column; align-items: center; width: 35px; }
        .avatar-box { width: 35px; height: 35px; background: var(--slate-100); color: var(--primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 800; border: 1px solid var(--slate-200); }
        .timeline-line { width: 2px; flex-grow: 1; background: var(--slate-100); margin: 5px 0; }
        
        .comment-body { flex: 1; padding-bottom: 25px; }
        .comment-header { display: flex; justify-content: space-between; margin-bottom: 6px; }
        .user-name { font-size: 0.85rem; font-weight: 700; color: var(--slate-900); }
        .time-ago { font-size: 0.75rem; color: var(--slate-500); }

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

        /* SIDEBAR COMPONENTS */
        .priority-box { display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 10px; font-weight: 700; font-size: 0.8rem; width: 100%; }
        .p-dot { width: 8px; height: 8px; border-radius: 50%; }
        .p-alta { background: #fef2f2; color: #b91c1c; } .p-alta .p-dot { background: #ef4444; }
        .p-media { background: #fffbeb; color: #b45309; } .p-media .p-dot { background: #f59e0b; }
        .p-baja { background: #f0fdf4; color: #15803d; } .p-baja .p-dot { background: #10b981; }

        .data-grid-side { margin-top: 20px; display: flex; flex-direction: column; gap: 18px; }
        .data-row { display: flex; align-items: center; gap: 15px; }
        .icon-val { font-size: 1rem; color: var(--slate-400); width: 20px; text-align: center; }
        .data-label { display: block; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: var(--slate-500); letter-spacing: 0.05em; }
        .data-value { font-size: 0.9rem; font-weight: 600; color: var(--slate-900); }
        .alert-danger .data-value { color: var(--danger); }

        /* AUDIT TIMELINE */
        .audit-timeline { position: relative; padding-left: 10px; }
        .audit-node { position: relative; padding-left: 20px; padding-bottom: 20px; }
        .node-circle { position: absolute; left: -5px; top: 5px; width: 10px; height: 10px; background: var(--slate-200); border-radius: 50%; border: 2px solid white; z-index: 2; }
        .audit-node::before { content: ''; position: absolute; left: -1px; top: 15px; width: 2px; height: 100%; background: var(--slate-100); }
        .audit-node:last-child::before { display: none; }
        .node-date { font-size: 0.7rem; color: var(--slate-400); display: block; }
        .node-text { font-size: 0.8rem; margin: 2px 0 0; color: var(--slate-700); }
        .node-status { font-weight: 700; color: var(--primary); }

        .system-footprint { font-size: 0.7rem; color: var(--slate-400); text-align: center; display: flex; gap: 15px; justify-content: center; }
        .highlight-border { border-top: 4px solid var(--primary); }
        .fa-spin-slow { animation: fa-spin 3s infinite linear; }

        /* ESTILOS DE IMPRESIÓN (PDF) */
        @media print {
            body { background: white !important; font-size: 12px; }
            .no-print, .header-right { display: none !important; }
            .task-show-wrapper { margin: 0; padding: 0; max-width: 100%; box-shadow: none; }
            .glass-card { border: 1px solid #ccc; box-shadow: none; page-break-inside: avoid; margin-bottom: 15px; }
            .show-grid { display: block; }
            .side-column { margin-top: 20px; }
            .page-break-before { page-break-before: always; }
            .avoid-break { page-break-inside: avoid; }
            .chart-container canvas { max-height: 180px; }
            .description-content, .comment-text-box { font-size: 11px; }
            .workflow-highlight-banner { background: #f8fafc; border-left: 4px solid #000; }
        }
    </style>
</x-base-layout>