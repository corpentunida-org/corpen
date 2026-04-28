<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Proyecto #{{ $workflow->id }}</title>
    <style>
        /* --- 1. CONFIGURACIÓN DE PÁGINA (APA 7) --- */
        @page {
            margin: 2.54cm; /* Margen estándar de 1 pulgada */
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
        }

        /* --- 2. UTILIDAD DE SALTO DE PÁGINA --- */
        .page-break {
            page-break-before: always;
        }
        .avoid-break {
            page-break-inside: avoid;
        }

        /* --- 3. ENCABEZADO Y PIE (Running Head) --- */
        header {
            position: fixed;
            top: -1.5cm;
            left: 0; right: 0;
            height: 30px;
            font-size: 10pt;
            color: #555;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        footer {
            position: fixed;
            bottom: -1.5cm;
            left: 0; right: 0;
            height: 30px;
            text-align: center;
            font-size: 10pt;
            border-top: 1px solid #000;
            padding-top: 5px;
        }

        .page-number:after { content: counter(page); }

        /* --- 4. TIPOGRAFÍA APA --- */
        h1 {
            font-size: 16pt; font-weight: bold; text-align: center;
            margin-bottom: 20px; color: #000;
        }
        h2 {
            font-size: 14pt; font-weight: bold; text-align: left;
            margin-bottom: 15px; color: #000;
        }
        h3 {
            font-size: 12pt; font-weight: bold; text-align: left;
            margin-top: 15px; margin-bottom: 10px; color: #000;
        }
        h4 {
            font-size: 12pt; font-weight: bold; font-style: italic; text-align: left;
            margin-top: 10px; margin-bottom: 5px; color: #000;
        }
        p {
            text-align: justify; text-indent: 0.5in; margin-bottom: 15px;
        }

        /* --- 5. PORTADA --- */
        .title-page {
            text-align: center;
            margin-top: 150px; /* Centrado visual vertical */
        }
        .title-page-header { font-weight: bold; font-size: 14pt; margin-bottom: 10px; }
        .title-page-sub { margin-bottom: 40px; }
        .title-info { margin-top: 50px; line-height: 2; }

        /* --- 6. TABLAS Y FIGURAS APA --- */
        table {
            width: 100%; border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 10pt; margin-bottom: 0;
            table-layout: fixed; /* OBLIGA A RESPETAR LOS ANCHOS Y MÁRGENES */
            word-wrap: break-word;
        }
        
        .table-caption, .figure-caption {
            font-family: 'Times New Roman', serif; font-size: 12pt;
            text-align: left; margin-bottom: 5px; margin-top: 15px;
        }
        .table-caption strong, .figure-caption strong { display: block; } 
        .table-caption em, .figure-caption em { font-style: italic; } 

        th {
            border-top: 1px solid #000; border-bottom: 1px solid #000;
            text-align: left; padding: 8px; font-weight: bold;
        }
        td {
            padding: 8px; vertical-align: top; border: none;
            overflow-wrap: break-word;
        }
        .table-end { border-bottom: 1px solid #000; }

        .figure-container { text-align: center; margin-bottom: 20px; }
        .figure-container img { max-width: 80%; height: auto; border: 1px solid #ddd; padding: 10px; }

        /* --- 7. UTILIDADES VISUALES Y PARSEADORES --- */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-muted { color: #555; font-size: 9pt; font-style: italic; }
        
        .progress-wrapper {
            border: 1px solid #000; height: 10px; width: 100%;
            background: #fff; margin-top: 5px;
        }
        .progress-fill { height: 100%; background: #444; }
        
        .status-bold { font-weight: bold; text-transform: uppercase; font-size: 8pt; }
        .color-success { color: #15803d; }
        .color-warning { color: #b45309; }
        .color-danger { color: #b91c1c; }

        /* Estilos APA para Bloques de Código/Reportes */
        .apa-blockquote {
            margin-left: 0.5in; margin-right: 0;
            text-align: left; font-size: 11pt; line-height: 1.5;
            margin-bottom: 15px; padding-left: 10px; border-left: 2px solid #ccc;
        }
        .checklist-item {
            margin-left: 0.5in; text-align: left; margin-bottom: 5px; font-size: 11pt;
            word-wrap: break-word;
        }

        /* --- 8. EXCEPCIÓN MINIMALISTA (Para Auditoría Rápida) --- */
        .minimal-table {
            width: 100%; border-collapse: collapse; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            table-layout: fixed; /* Forzar ajuste al margen */
        }
        .minimal-table th {
            border-top: none; border-bottom: 2px solid #ddd; padding: 12px 10px;
            color: #666; font-size: 9pt; text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold;
        }
        .minimal-table td {
            padding: 12px 10px; border-bottom: 1px solid #eee; vertical-align: top; color: #333; font-size: 10pt;
            word-wrap: break-word; overflow-wrap: break-word;
        }
        .minimal-table tr:last-child td {
            border-bottom: 2px solid #ddd;
        }
        .badge {
            display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 8pt; font-weight: bold; text-transform: uppercase;
        }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef08a; color: #854d0e; }
        
        /* Estilos estructurales para el nuevo checklist flotante */
        .chk-row {
            margin-bottom: 6px; 
            overflow: hidden; /* Limpia los floats */
            page-break-inside: avoid; /* Evita que se corte a la mitad una misma oración */
        }
        .chk-box {
            float: left; width: 10px; height: 10px; border-radius: 2px; margin-top: 3px;
        }
        .chk-text {
            margin-left: 18px; font-size: 9pt; line-height: 1.3;
        }
    </style>
</head>
<body>

    {{-- === ENCABEZADO FIJO (Todas las páginas) === --}}
    <header>
        <div style="float: left; width: 80%;">
            INFORME EJECUTIVO: {{ strtoupper(Str::limit($workflow->nombre, 50)) }}
        </div>
        <div style="float: right; width: 20%; text-align: right;">
            Pág. <span class="page-number"></span>
        </div>
    </header>

    {{-- === PIE DE PÁGINA FIJO === --}}
    <footer>
        Confidencial | Fecha de Emisión: {{ date('d/m/Y') }}
    </footer>

    {{-- ========================================================= --}}
    {{-- HOJA 1: PORTADA (Title Page) --}}
    {{-- ========================================================= --}}
    <div class="title-page">
        <div class="title-page-header">INFORME DE ESTADO Y AUDITORÍA DEL PROYECTO</div>
        <div class="title-page-sub">{{ $workflow->nombre }}</div>

        <div class="title-info">
            <strong>Referencia del Sistema:</strong> #{{ $workflow->id }}<br>
            <strong>Prioridad:</strong> {{ ucfirst($workflow->prioridad) }}<br>
            <br>
            <strong>Preparado por:</strong><br>
            Sistema de Gestión de Flujos<br>
            <br>
            <strong>Líder del Proyecto:</strong><br>
            {{ $workflow->asignado->name ?? 'No Asignado' }}<br>
            <br>
            {{-- EQUIPO ASIGNADO EN TEXTO PLANO (APA) --}}
            <strong>Equipo Asignado:</strong><br>
            @if ($workflow->participantes->count() > 0)
                @foreach ($workflow->participantes as $member)
                    {{ $member->name }}<br>
                @endforeach
            @else
                Sin equipo definido<br>
            @endif
            <br>
            <strong>Fecha de Corte:</strong><br>
            {{ \Carbon\Carbon::now()->format('d \d\e F \d\e Y') }}
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- HOJA 2: RESUMEN GENERAL DE AUDITORÍA RÁPIDA (DISEÑO MINIMALISTA DE PRIMERA) --}}
    {{-- ========================================================= --}}
    <div class="page-break"></div>

    <h2>1. Resumen General de Auditoría Rápida</h2>
    
    {{-- WIDGET DE PROGRESO GLOBAL (Compatible con PDF) --}}
    <div style="background-color: #f8f9fa; border: 1px solid #eaeaea; border-radius: 8px; padding: 15px; margin-bottom: 25px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; page-break-inside: avoid;">
        <table style="width: 100%; border: none; margin: 0; padding: 0; table-layout: fixed;">
            <tr>
                <td style="width: 25%; font-weight: bold; font-size: 11pt; color: #333; border: none; padding: 0; vertical-align: middle;">
                    PROGRESO GLOBAL:
                </td>
                <td style="width: 60%; border: none; padding: 0 15px; vertical-align: middle;">
                    <div style="width: 100%; background-color: #e5e7eb; height: 12px; border-radius: 6px;">
                        <div style="width: {{ $progress }}%; background-color: #3b82f6; height: 12px; border-radius: 6px;"></div>
                    </div>
                </td>
                <td style="width: 15%; text-align: right; font-weight: bold; font-size: 14pt; color: #3b82f6; border: none; padding: 0; vertical-align: middle;">
                    {{ $progress }}%
                </td>
            </tr>
        </table>
    </div>

    <p style="text-indent: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10.5pt; color: #555;">
        El siguiente cuadro consolida de manera minimalista las tareas del flujo de trabajo, presentando el progreso individual de cada actividad, el desglose limpio de sus subtareas, el responsable a cargo y sus comentarios recientes.
    </p>

    <table class="minimal-table">
        <thead>
            <tr>
                {{-- NUEVA DISTRIBUCIÓN: Más espacio para la actividad (45%) y se une Responsable/Estado en una sola (20%) --}}
                <th width="45%">Actividad, Progreso y Subtareas</th>
                <th width="20%">Asignación y Estado</th>
                <th width="35%">Últimos Comentarios</th>
            </tr>
        </thead>
        <tbody>
            @forelse($workflow->tasks as $task)
                @php 
                    $isDone = in_array(strtolower($task->estado), ['finalizado', 'completado']); 
                    
                    // Lógica para extraer subtareas y calcular el progreso individual
                    $subtareas = [];
                    $completadas = 0;
                    if ($task->descripcion) {
                        $descLines = explode("\n", $task->descripcion);
                        foreach($descLines as $line) {
                            $line = trim($line);
                            if (preg_match('/^\[([xX\s])\]\s*-?\s*(.*)$/', $line, $matches)) {
                                $isChecked = strtolower(trim($matches[1])) === 'x';
                                $subtareas[] = ['done' => $isChecked, 'text' => $matches[2]];
                                if ($isChecked) $completadas++;
                            }
                        }
                    }
                    $totalSubtareas = count($subtareas);
                    
                    if ($totalSubtareas > 0) {
                        $porcentajeTarea = round(($completadas / $totalSubtareas) * 100);
                    } else {
                        $porcentajeTarea = $isDone ? 100 : 0;
                    }
                @endphp
                <tr>
                    <td>
                        <strong style="font-size: 11pt; color: #111; display: block; margin-bottom: 6px;">{{ $task->titulo }}</strong>
                        
                        {{-- BARRA DE PROGRESO INDIVIDUAL DE LA TAREA --}}
                        @if($totalSubtareas > 0 || $isDone)
                            <table style="width: 100%; border: none; margin: 0 0 10px 0; padding: 0; table-layout: fixed;">
                                <tr>
                                    <td style="width: 80%; padding: 0; border: none; vertical-align: middle;">
                                        <div style="width: 100%; background-color: #e5e7eb; height: 6px; border-radius: 3px;">
                                            <div style="width: {{ $porcentajeTarea }}%; background-color: {{ $porcentajeTarea == 100 ? '#10b981' : '#3b82f6' }}; height: 6px; border-radius: 3px;"></div>
                                        </div>
                                    </td>
                                    <td style="width: 20%; padding: 0 0 0 8px; border: none; font-size: 8pt; color: #666; font-weight: bold; text-align: right; vertical-align: middle;">
                                        {{ $porcentajeTarea }}%
                                    </td>
                                </tr>
                            </table>
                        @endif

                        {{-- CHECKLIST SIN TABLAS ANIDADAS (Soluciona el error del margen inferior en el PDF) --}}
                        @if($totalSubtareas > 0)
                            <div style="margin-top: 5px;">
                                @foreach($subtareas as $subt)
                                    <div class="chk-row">
                                        {{-- El cuadrado simulando el checkbox --}}
                                        <div class="chk-box" style="border: 1px solid {{ $subt['done'] ? '#15803d' : '#9ca3af' }}; background-color: {{ $subt['done'] ? '#15803d' : '#fff' }};"></div>
                                        
                                        {{-- El texto de la subtarea --}}
                                        <div class="chk-text" style="{{ $subt['done'] ? 'color: #15803d; font-weight: bold;' : 'color: #555;' }}">
                                            {{ $subt['text'] }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div style="font-size: 9pt; color: #888; margin-top: 6px; word-wrap: break-word;">
                                {{ strip_tags($task->descripcion) }}
                            </div>
                        @endif
                    </td>
                    <td>
                        <strong style="color: #333;">{{ $task->user->name ?? ($task->asignado->name ?? 'N/A') }}</strong><br>
                        <div style="font-size: 8pt; color: #888; margin-bottom: 8px;">Cr: {{ $task->created_at->format('d/m/Y') }}</div>
                        
                        <span class="badge {{ $isDone ? 'badge-success' : 'badge-warning' }}">
                            {{ ucfirst($task->estado) }}
                        </span>
                    </td>
                    <td style="font-size: 9pt;">
                        @if($task->comments->count() > 0)
                            @foreach($task->comments->take(3) as $comment)
                                <div style="margin-bottom: 6px; padding-bottom: 6px; border-bottom: 1px dotted #eaeaea; word-wrap: break-word;">
                                    <strong style="color:#222;">{{ $comment->user->name ?? 'Sis' }}:</strong> 
                                    <span style="color:#555;">{{ Str::limit(strip_tags($comment->comentario), 90, '...') }}</span>
                                </div>
                            @endforeach
                            @if($task->comments->count() > 3)
                                <div style="font-size: 8pt; color: #888; font-style: italic;">
                                    +{{ $task->comments->count() - 3 }} comentario(s) adicional(es).
                                </div>
                            @endif
                        @else
                            <span style="color: #aaa; font-style: italic;">Sin comentarios recientes.</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center" style="padding: 20px;">No hay tareas registradas para generar el resumen.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- ========================================================= --}}
    {{-- HOJA 3: RESUMEN EJECUTIVO Y KPIS --}}
    {{-- ========================================================= --}}
    <div class="page-break"></div>

    <h2>2. Resumen Ejecutivo</h2>
    <p>
        {{ $workflow->descripcion ?? 'El presente informe consolida el estado actual del proyecto, destacando el porcentaje de avance global, la carga de trabajo pendiente y la salud del cronograma. Asimismo, incluye un desglose detallado de las actividades realizadas y el registro de auditoría para garantizar la trazabilidad de la gestión.' }}
    </p>

    {{-- TABLA 1: KPIs --}}
    <div>
        <div class="table-caption">
            <strong>Tabla 1</strong>
            <em>Indicadores Clave de Desempeño (KPIs)</em>
        </div>
        <table>
            <thead>
                <tr>
                    <th width="35%">Indicador</th>
                    <th width="20%" class="text-center">Valor</th>
                    <th width="45%">Interpretación</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Avance Global</td>
                    <td class="text-center"><strong>{{ $progress }}%</strong></td>
                    <td>
                        <div class="progress-wrapper">
                            <div class="progress-fill" style="width: {{ $progress }}%;"></div>
                        </div>
                        <span class="text-muted">{{ $completedTasks }} de {{ $totalTasks }} tareas.</span>
                    </td>
                </tr>
                <tr>
                    <td>Carga Pendiente</td>
                    <td class="text-center"><strong>{{ $pendingTasks }}</strong></td>
                    <td>Tareas activas pendientes de cierre.</td>
                </tr>
                <tr>
                    <td>Tiempo de Ejecución</td>
                    <td class="text-center"><strong>{{ $daysActive }} días</strong></td>
                    <td>Tiempo transcurrido desde el inicio.</td>
                </tr>
                <tr>
                    <td>Nivel de Interacción</td>
                    <td class="text-center"><strong>{{ $totalComments }}</strong></td>
                    <td>Total de comentarios registrados.</td>
                </tr>
                <tr class="table-end">
                    <td>Salud del Cronograma</td>
                    <td class="text-center" style="color: {{ $kpiDate['color'] }}; font-weight: bold;">
                        {{ $kpiDate['value'] }}
                    </td>
                    <td>
                        <strong>{{ $kpiDate['label'] }}</strong>. {{ $kpiDate['note'] }}
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="text-muted" style="margin-top: 5px;">
            <em>Nota.</em> Los datos reflejan la información ingresada en el sistema hasta la fecha de generación de este reporte.
        </div>
    </div>

    {{-- FIGURAS APA (Gráficos Reales en Base64 enviados desde el Controlador) --}}
    <div class="avoid-break" style="margin-top: 30px;">
        
        @if(!empty($chart1_base64))
            <div class="figure-caption">
                <strong>Figura 1</strong>
                <em>Distribución Porcentual del Avance de Tareas</em>
            </div>
            <div class="figure-container">
                <img src="{{ $chart1_base64 }}" alt="Gráfico de Progreso">
            </div>
        @endif
        
        @if(!empty($chart2_base64))
            <div class="figure-caption">
                <strong>Figura 2</strong>
                <em>Volumen Analítico de Actividad y Auditoría</em>
            </div>
            <div class="figure-container">
                <img src="{{ $chart2_base64 }}" alt="Gráfico de Actividad">
            </div>
        @endif

    </div>

    {{-- ========================================================= --}}
    {{-- HOJA 4: DETALLE DE ACTIVIDADES (TABLA Y ESPECIFICACIONES) --}}
    {{-- ========================================================= --}}
    <div class="page-break"></div>

    <h2>3. Detalle de Actividades</h2>
    <p>
        A continuación, se relacionan las tareas asociadas al flujo de trabajo, identificando a los responsables y el estado actual de cada entregable, seguido de las especificaciones técnicas y reportes correspondientes.
    </p>

    <div>
        <div class="table-caption">
            <strong>Tabla 2</strong>
            <em>Relación de Tareas y Estado</em>
        </div>
        <table>
            <thead>
                <tr>
                    <th width="45%">Actividad / Tarea</th>
                    <th width="25%">Responsable</th>
                    <th width="15%">Estado</th>
                    <th width="15%" class="text-right">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workflow->tasks as $task)
                    <tr>
                        <td>{{ $task->titulo }}</td>
                        <td>{{ $task->user->name ?? ($task->asignado->name ?? 'N/A') }}</td>
                        <td>
                            @php $isDone = in_array(strtolower($task->estado), ['finalizado', 'completado']); @endphp
                            <span class="status-bold {{ $isDone ? 'color-success' : 'color-warning' }}">
                                {{ ucfirst($task->estado) }}
                            </span>
                        </td>
                        <td class="text-right">{{ $task->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">No hay actividades registradas.</td></tr>
                @endforelse
                <tr class="table-end"><td colspan="4"></td></tr>
            </tbody>
        </table>
    </div>

    {{-- INTEGRACIÓN DE CHECKLISTS Y REPORTES (Estilo APA) --}}
    @if($workflow->tasks->count() > 0)
        <div style="margin-top: 30px;">
            <h3>3.1 Especificaciones y Evidencias por Tarea</h3>
            <p>El siguiente apartado desglosa los criterios operativos (checklists) y los reportes de desarrollo asociados a cada unidad de trabajo.</p>

            @foreach($workflow->tasks as $task)
                <div class="avoid-break" style="margin-bottom: 25px;">
                    <h4>Tarea #{{ $task->id }}: {{ $task->titulo }}</h4>
                    
                    {{-- Parseo de Checklist --}}
                    @php
                        if ($task->descripcion) {
                            $descLines = explode("\n", $task->descripcion);
                            echo '<div style="margin-top: 10px;">';
                            foreach($descLines as $line) {
                                $line = trim($line);
                                if (preg_match('/^\[([xX\s])\]\s*-?\s*(.*)$/', $line, $matches)) {
                                    $isChecked = strtolower(trim($matches[1])) === 'x';
                                    $text = $matches[2];
                                    if ($isChecked) {
                                        echo '<div class="checklist-item"><strong>[X]</strong> ' . e($text) . '</div>';
                                    } else {
                                        echo '<div class="checklist-item">[&nbsp;&nbsp;&nbsp;] ' . e($text) . '</div>';
                                    }
                                } elseif ($line !== '') {
                                    echo '<div style="margin-left: 0.5in; margin-bottom:5px;">' . nl2br(e($line)) . '</div>';
                                }
                            }
                            echo '</div>';
                        }
                    @endphp

                    {{-- Parseo de Reportes de Desarrollo (Blockquote APA) --}}
                    @if($task->comments->count() > 0)
                        <div style="margin-top: 15px; margin-bottom: 5px; margin-left: 0.5in;"><em>Registro de Evidencias:</em></div>
                        @foreach($task->comments->sortByDesc('created_at') as $comment)
                            @php
                                $textoRaw = trim($comment->comentario);
                                $esDevTemplate = str_starts_with($textoRaw, '[') && str_ends_with($textoRaw, ']');
                                
                                echo '<div class="apa-blockquote">';
                                echo '<strong>' . ($comment->user->name ?? 'Sistema') . ' (' . $comment->created_at->format('d/m/Y') . '):</strong><br>';
                                
                                if ($esDevTemplate) {
                                    $textoLimpio = trim(substr($textoRaw, 1, -1));
                                    $textoLimpio = e($textoLimpio);
                                    $textoFormateado = preg_replace('/([A-ZÁÉÍÓÚÑ \/]+):/', '<strong>$1:</strong>', $textoLimpio);
                                    echo $textoFormateado;
                                } else {
                                    echo nl2br(e($textoRaw));
                                }
                                echo '</div>';
                            @endphp
                        @endforeach
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- ========================================================= --}}
    {{-- HOJA 5: HISTORIAL DE AUDITORÍA --}}
    {{-- ========================================================= --}}
    <div class="page-break"></div>

    <h2>4. Historial de Auditoría</h2>
    <p>
        Registro cronológico de los eventos, cambios de estado y comentarios realizados por los usuarios durante el ciclo de vida del proyecto.
    </p>

    <div style="margin-top: 10px;">
        <div class="table-caption">
            <strong>Tabla 3</strong>
            <em>Registro de Eventos y Cambios</em>
        </div>
        <table>
            <thead>
                <tr>
                    <th width="20%">Usuario</th>
                    <th width="15%">Tipo</th>
                    <th width="50%">Detalle del Evento</th>
                    <th width="15%" class="text-right">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auditEvents as $event)
                    <tr>
                        <td><strong>{{ $event->user->name ?? 'Sistema' }}</strong></td>
                        <td>
                            @if($event->tipo_evento == 'comentario')
                                Comentario
                            @else
                                Estado
                            @endif
                        </td>
                        <td>
                            @if($event->tipo_evento == 'comentario')
                                En tarea: <em>{{ $event->task->titulo ?? '?' }}</em>
                            @else
                                Cambio a: <strong>{{ $event->estado_nuevo }}</strong> ({{ $event->task->titulo ?? '?' }})
                            @endif
                            
                            @if(!empty($event->comentario))
                                <br>
                                <span class="text-muted">"{{ Str::limit($event->comentario, 100) }}"</span>
                            @endif
                        </td>
                        <td class="text-right">
                            {{ $event->created_at->format('d/m/Y') }}<br>
                            <span style="font-size: 8pt;">{{ $event->created_at->format('H:i') }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">Sin registros de auditoría.</td></tr>
                @endforelse
                <tr class="table-end"><td colspan="4"></td></tr>
            </tbody>
        </table>
    </div>

</body>
</html>