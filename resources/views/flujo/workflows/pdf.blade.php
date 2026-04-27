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
            font-family: Arial, sans-serif; /* Arial permitido en tablas APA */
            font-size: 10pt; margin-bottom: 0;
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
            <strong>Fecha de Corte:</strong><br>
            {{ \Carbon\Carbon::now()->format('d \d\e F \d\e Y') }}
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- HOJA 2: RESUMEN Y KPIS --}}
    {{-- ========================================================= --}}
    <div class="page-break"></div>

    <h2>1. Resumen Ejecutivo</h2>
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
    {{-- HOJA 3: DETALLE DE ACTIVIDADES (TABLA Y ESPECIFICACIONES) --}}
    {{-- ========================================================= --}}
    <div class="page-break"></div>

    <h2>2. Detalle de Actividades</h2>
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
            <h3>2.1 Especificaciones y Evidencias por Tarea</h3>
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
                        @foreach($task->comments->sortByDesc('created_at')->take(2) as $comment)
                            @php
                                $textoRaw = trim($comment->comentario);
                                $esDevTemplate = str_starts_with($textoRaw, '[') && str_ends_with($textoRaw, ']');
                                
                                echo '<div class="apa-blockquote">';
                                echo '<strong>' . ($comment->user->name ?? 'Sistema') . ' (' . $comment->created_at->format('d/m/Y') . '):</strong><br>';
                                
                                if ($esDevTemplate) {
                                    $textoLimpio = trim(substr($textoRaw, 1, -1));
                                    $textoLimpio = e($textoLimpio);
                                    // Formato de negrita a los títulos del reporte
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
    {{-- HOJA 4: HISTORIAL DE AUDITORÍA --}}
    {{-- ========================================================= --}}
    <div class="page-break"></div>

    <h2>3. Historial de Auditoría</h2>
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