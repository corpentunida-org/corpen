<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Auditoría - Corpentunida</title>
    <style>
        /* --- CONFIGURACIÓN DE PÁGINA (Estilo APA/Formal) --- */
        @page {
            margin: 2.54cm 2.54cm;
            font-family: 'Times New Roman', serif;
        }

        body {
            color: #111827;
            line-height: 1.4;
            font-size: 10pt;
        }

        /* --- UTILIDADES --- */
        .page-break { page-break-after: always; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .justify { text-align: justify; }
        
        /* --- HEADER Y FOOTER --- */
        header {
            position: fixed;
            top: -1.5cm;
            left: 0;
            right: 0;
            height: 1cm;
            color: #6b7280;
            font-size: 8pt;
            border-bottom: 1px solid #e5e7eb;
            text-align: right;
            line-height: 0.8cm;
        }

        footer {
            position: fixed;
            bottom: -1.5cm;
            left: 0;
            right: 0;
            height: 1cm;
            color: #9ca3af;
            font-size: 8pt;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
        }

        /* --- ESTILOS DE PORTADA --- */
        .cover-container {
            top: 15%;
            position: relative;
            text-align: center;
        }
        .company-name {
            font-size: 22pt;
            font-weight: bold;
            letter-spacing: 2px;
            color: #1e3a8a; /* Azul Corporativo */
            margin-bottom: 5px;
        }
        .system-name {
            font-size: 9pt;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 60px;
            color: #4b5563;
        }
        .report-title {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 40px;
            color: #111827;
            border-top: 2px solid #111827;
            border-bottom: 2px solid #111827;
            padding: 20px 0;
            display: inline-block;
            width: 80%;
        }
        .report-meta {
            margin-top: 80px;
            font-size: 11pt;
            line-height: 1.8;
        }
        .confidential-box {
            margin-top: 120px;
            border: 1px solid #dc2626;
            color: #dc2626;
            padding: 8px 20px;
            font-size: 9pt;
            display: inline-block;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* --- TÍTULOS DE SECCIÓN --- */
        h1 {
            font-size: 14pt;
            color: #1e3a8a;
            border-bottom: 1px solid #1e3a8a;
            padding-bottom: 5px;
            margin-top: 0;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        h2 {
            font-size: 12pt;
            color: #374151;
            margin-top: 20px;
            margin-bottom: 10px;
            background-color: #f3f4f6;
            padding: 5px 10px;
            border-left: 4px solid #1e3a8a;
        }

        /* --- TABLAS --- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt;
            font-family: sans-serif;
        }
        th {
            background-color: #1e3a8a;
            color: white;
            padding: 6px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #1e3a8a;
            font-size: 8pt;
            text-transform: uppercase;
        }
        td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            vertical-align: top;
        }
        tr:nth-child(even) { background-color: #f9fafb; }

        /* Estilos Matriz Heatmap */
        .matrix-table th { text-align: center; font-size: 7pt; }
        .matrix-table td { text-align: center; font-size: 8pt; color: #6b7280; }
        .matrix-user { text-align: left !important; font-weight: bold; color: #111827 !important; }
        .col-total { background-color: #e0e7ff; font-weight: bold; color: #1e3a8a !important; }
        .has-data { font-weight: bold; color: #ffffff !important; background-color: #3b82f6; }

        /* Estilos Matriz TIC (KPIs) */
        .kpi-table th { background-color: #374151; border-color: #374151; }
        .kpi-table .kpi-meta { text-align: center; font-size: 8pt; color: #4b5563; }
        .kpi-val-ok { color: #047857; font-weight: bold; }
        .kpi-val-warn { color: #b45309; font-weight: bold; }
        .kpi-val-bad { color: #b91c1c; font-weight: bold; }

        /* Badges y Detalles */
        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 6pt;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }
        .b-comment { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .b-history { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .change-arrow { font-family: sans-serif; color: #9ca3af; padding: 0 4px; }
        
        /* Caja de Anexos */
        .annex-box {
            border: 1px dashed #9ca3af;
            padding: 20px;
            background-color: #f9fafb;
            color: #4b5563;
            margin-top: 20px;
            font-size: 10pt;
        }
    </style>
</head>
<body>

    {{-- HEADER FIJO --}}
    <header>
        Auditoría de Cumplimiento & Governance | Ref: AUD-{{ now()->format('Ymd') }}-{{ rand(1000,9999) }}
    </header>

    {{-- FOOTER FIJO --}}
    <footer>
        <table style="width: 100%; border: none; margin: 0;">
            <tr style="background: transparent;">
                <td style="border: none; text-align: left; width: 33%;">Generado por: {{ $user }}</td>
                <td style="border: none; text-align: center; width: 33%;">Confidencial - Uso Interno</td>
                <td style="border: none; text-align: right; width: 33%;">
                    <script type="text/php">
                        if (isset($pdf)) {
                            $font = $fontMetrics->getFont("Helvetica", "normal");
                            $pdf->page_text(520, 820, "Pág {PAGE_NUM} de {PAGE_COUNT}", $font, 8, array(0.5, 0.5, 0.5));
                        }
                    </script>
                </td>
            </tr>
        </table>
    </footer>

    {{-- PÁGINA 1: PORTADA --}}
    <div class="cover-container">
        <div class="company-name">CORPENTUNIDA</div>
        <div class="system-name">Sistema Central de Workflows</div>

        <div class="report-title">
            INFORME DE AUDITORÍA Y<br>TRAZABILIDAD DE PROCESOS
        </div>

        <div class="report-meta">
            <table style="width: 70%; margin: 0 auto; border: none; font-size: 11pt;">
                <tr style="background: transparent;">
                    <td style="border: none; text-align: right; width: 45%; padding-right: 15px; font-weight: bold;">Periodo Auditado:</td>
                    <td style="border: none; text-align: left; width: 55%;">Año Fiscal {{ $year }}</td>
                </tr>
                <tr style="background: transparent;">
                    <td style="border: none; text-align: right; padding-right: 15px; font-weight: bold;">Fecha de Emisión:</td>
                    <td style="border: none; text-align: left;">{{ $date }}</td>
                </tr>
                <tr style="background: transparent;">
                    <td style="border: none; text-align: right; padding-right: 15px; font-weight: bold;">Generado Por:</td>
                    <td style="border: none; text-align: left;">{{ $user }}</td>
                </tr>
                <tr style="background: transparent;">
                    <td style="border: none; text-align: right; padding-right: 15px; font-weight: bold;">Origen de Datos:</td>
                    <td style="border: none; text-align: left;">Base de Datos Central (SQL)</td>
                </tr>
            </table>

            @if(count($filters) > 0)
                <div style="margin-top: 30px; font-size: 10pt; color: #4b5563;">
                    <strong>Filtros de Segmentación Aplicados:</strong><br>
                    @foreach($filters as $f)
                        <span style="background: #e5e7eb; padding: 2px 6px; border-radius: 4px; font-size: 9pt;">{{ $f }}</span>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="confidential-box">
            Documento Estrictamente Confidencial
        </div>
    </div>

    <div class="page-break"></div>

    {{-- PÁGINA 2: RESUMEN EJECUTIVO --}}
    <h1>1. Resumen Ejecutivo</h1>
    <p class="justify" style="margin-bottom: 20px;">
        El presente documento certifica la integridad y trazabilidad de las operaciones registradas en el 
        Sistema Central de Operaciones de Corpentunida.
    </p>

    <h2>1.1 Matriz de Densidad Operativa (Mapa de Calor)</h2>
    @if(count($monthlyStats) > 0)
        <table class="matrix-table">
            <thead>
                <tr>
                    <th class="matrix-user" width="22%">Usuario</th>
                    <th>Ene</th><th>Feb</th><th>Mar</th><th>Abr</th><th>May</th><th>Jun</th>
                    <th>Jul</th><th>Ago</th><th>Sep</th><th>Oct</th><th>Nov</th><th>Dic</th>
                    <th class="col-total">TOT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyStats as $stat)
                    <tr>
                        <td class="matrix-user">{{ Str::limit($stat['name'], 18) }}</td>
                        @foreach($stat['months'] as $count)
                            <td class="{{ $count > 0 ? 'has-data' : '' }}">
                                {{ $count > 0 ? $count : '.' }}
                            </td>
                        @endforeach
                        <td class="col-total">{{ $stat['total'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="annex-box text-center">
            No existen registros suficientes en el periodo seleccionado.
        </div>
    @endif

    {{-- NUEVA SECCIÓN: TABLERO DE CONTROL (KPIs) --}}
    <h2>1.2 Tablero de Control - Indicadores TIC</h2>
    <table class="kpi-table">
        <thead>
            <tr>
                <th width="35%">Indicador de Gestión</th>
                <th width="15%" style="text-align: center;">Meta</th>
                <th width="15%" style="text-align: center;">Frecuencia</th>
                <th width="35%" style="text-align: center;">Estado Actual (Snapshot)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Tasa de Incidentes Resueltos</strong><br><span style="font-size: 7pt; color:#6b7280;">(Resueltos / Total Tareas)</span></td>
                <td class="kpi-meta">≥ 90%</td>
                <td class="kpi-meta">Mensual</td>
                <td style="text-align: center;" class="{{ $kpiResolucion >= 90 ? 'kpi-val-ok' : 'kpi-val-warn' }}">
                    {{ $kpiResolucion }}%
                </td>
            </tr>
            <tr>
                <td><strong>Tiempo Respuesta (Alta)</strong><br><span style="font-size: 7pt; color:#6b7280;">Promedio Horas Resolución</span></td>
                <td class="kpi-meta">≤ 6h</td>
                <td class="kpi-meta">Mensual</td>
                <td style="text-align: center;" class="{{ $kpiTiempoAlta <= 6 ? 'kpi-val-ok' : 'kpi-val-bad' }}">
                    {{ $kpiTiempoAlta }} horas
                </td>
            </tr>
            <tr>
                <td><strong>Digitalización de Procesos</strong><br><span style="font-size: 7pt; color:#6b7280;">(Proyectos Digitales / Totales)</span></td>
                <td class="kpi-meta">≥ 75%</td>
                <td class="kpi-meta">Anual</td>
                <td style="text-align: center;" class="{{ $kpiDigitalizacion >= 75 ? 'kpi-val-ok' : 'kpi-val-warn' }}">
                    {{ $kpiDigitalizacion }}%
                </td>
            </tr>
            <tr>
                <td><strong>Cumplimiento Estratégico</strong><br><span style="font-size: 7pt; color:#6b7280;">(Ejecutados / Planificados)</span></td>
                <td class="kpi-meta">≥ 85%</td>
                <td class="kpi-meta">Anual</td>
                <td style="text-align: center;" class="{{ $kpiCumplimiento >= 85 ? 'kpi-val-ok' : 'kpi-val-warn' }}">
                    {{ $kpiCumplimiento }}%
                </td>
            </tr>
            <tr>
                <td><strong>Colaboración Digital</strong><br><span style="font-size: 7pt; color:#6b7280;">(Usuarios Activos / Total)</span></td>
                <td class="kpi-meta">≥ 80%</td>
                <td class="kpi-meta">Mensual</td>
                <td style="text-align: center;" class="{{ $kpiColaboracion >= 80 ? 'kpi-val-ok' : 'kpi-val-warn' }}">
                    {{ $kpiColaboracion }}%
                </td>
            </tr>
        </tbody>
    </table>

    <h2>1.3 Estadísticas Globales del Informe</h2>
    <table style="width: 100%;">
        <tr>
            <th width="70%">Métrica General</th>
            <th width="30%" style="text-align: right;">Resultado</th>
        </tr>
        <tr>
            <td>Total de Eventos Registrados (Logs)</td>
            <td style="text-align: right; font-weight: bold;">{{ number_format(count($events)) }}</td>
        </tr>
        <tr>
            <td>Usuarios Activos Intervinientes</td>
            <td style="text-align: right;">{{ count($monthlyStats) }}</td>
        </tr>
        <tr>
            <td>Integridad de Datos</td>
            <td style="text-align: right; color: green;">Verificada (100%)</td>
        </tr>
    </table>

    <div class="page-break"></div>

    {{-- PÁGINA 3: DETALLE --}}
    <h1>2. Bitácora Detallada de Operaciones</h1>
    <p class="justify" style="margin-bottom: 15px; font-size: 9pt; color: #4b5563;">
        A continuación se listan cronológicamente los eventos capturados por el sistema de auditoría.
    </p>

    <table>
        <thead>
            <tr>
                <th width="15%">Fecha / Hora</th>
                <th width="20%">Responsable</th>
                <th width="10%">Tipo</th>
                <th width="55%">Detalle de la Operación y Trazabilidad</th>
            </tr>
        </thead>
        <tbody>
            @forelse($events as $event)
                <tr>
                    <td>
                        {{ $event->created_at->format('Y-m-d') }}<br>
                        <span style="color: #6b7280; font-size: 8pt;">{{ $event->created_at->format('H:i:s') }}</span>
                    </td>
                    <td>
                        <strong>{{ $event->user->name ?? 'Sistema Automático' }}</strong>
                        <br>
                        <span style="font-size: 7pt; color: #6b7280;">ID: {{ $event->user->id ?? 'SYS' }}</span>
                    </td>
                    <td style="text-align: center;">
                        @if($event->tipo_evento === 'comentario')
                            <span class="badge b-comment">Feedback</span>
                        @else
                            <span class="badge b-history">Estado</span>
                        @endif
                    </td>
                    <td>
                        <div style="font-size: 8pt; color: #4b5563; text-transform: uppercase; margin-bottom: 2px;">
                            <strong>Proyecto:</strong> {{ Str::limit($event->task->workflow->nombre ?? 'N/A', 45) }}
                        </div>
                        <div style="font-weight: bold; margin-bottom: 4px; font-size: 9pt;">
                            Tarea: {{ Str::limit($event->task->titulo ?? 'Elemento eliminado', 60) }}
                        </div>

                        @if($event->tipo_evento === 'comentario')
                            <div style="font-style: italic; background: #f9fafb; padding: 4px; border-radius: 4px; border-left: 2px solid #3b82f6;">
                                "{{ $event->comentario }}"
                            </div>
                        @else
                            <div style="font-size: 8pt; background: #fffbeb; padding: 2px 5px; border-radius: 4px; display: inline-block;">
                                {{ str_replace('_', ' ', $event->estado_anterior) }}
                                <span class="change-arrow">➜</span>
                                <strong>{{ str_replace('_', ' ', $event->estado_nuevo) }}</strong>
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 30px; color: #9ca3af;">
                        No se encontraron registros.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    {{-- PÁGINA 4: ANEXOS Y CONTINUIDAD --}}
    <h1>3. Anexos y Continuidad</h1>
    
    <p class="justify">
        Este informe constituye una extracción oficial de la base de datos. Para garantizar la completitud de la auditoría, 
        se reserva este espacio para la inclusión de evidencias físicas, soportes digitales impresos o notas aclaratorias 
        posteriores a la generación del reporte.
    </p>

    <h2>3.1 Control de Soportes Adjuntos</h2>
    <p style="font-size: 9pt; color: #4b5563;">
        Utilice la siguiente tabla para relacionar documentos externos entregados junto con este reporte (facturas, actas firmadas, contratos, etc.).
    </p>

    <table style="margin-top: 20px;">
        <thead>
            <tr>
                <th width="10%">Ref #</th>
                <th width="50%">Descripción del Documento / Soporte</th>
                <th width="20%">Formato</th>
                <th width="20%">Folios / Páginas</th>
            </tr>
        </thead>
        <tbody>
            <tr><td style="height: 30px;">1</td><td></td><td></td><td></td></tr>
            <tr><td style="height: 30px;">2</td><td></td><td></td><td></td></tr>
            <tr><td style="height: 30px;">3</td><td></td><td></td><td></td></tr>
            <tr><td style="height: 30px;">4</td><td></td><td></td><td></td></tr>
            <tr><td style="height: 30px;">5</td><td></td><td></td><td></td></tr>
        </tbody>
    </table>

    <h2>3.2 Notas del Auditor / Observaciones Finales</h2>
    <div class="annex-box" style="height: 150px; border: 1px solid #d1d5db; background: white;">
    </div>

    <div style="margin-top: 50px; text-align: center; font-size: 8pt; color: #6b7280;">
        <p>*** INFORME ***</p>
        <p>Generado por el usuario <strong>{{ $user }}</strong> el {{ $date }} a través de la plataforma Corpentunida.</p>
    </div>

</body>
</html>