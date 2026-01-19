<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Auditoría - Corpentunida</title>
    <style>
        /* --- Configuración General --- */
        @page { margin: 100px 25px 60px 25px; }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 10px; 
            color: #334155; 
            line-height: 1.4; 
        }

        /* --- Header Fijo --- */
        header { 
            position: fixed; 
            top: -70px; 
            left: 0; 
            right: 0; 
            height: 60px; 
            border-bottom: 2px solid #0f172a; 
            padding-bottom: 5px; 
        }
        
        .header-table { width: 100%; border-collapse: collapse; }
        .logo-text { font-size: 20px; font-weight: 900; color: #0f172a; text-transform: uppercase; letter-spacing: 1px; }
        .sub-logo { font-size: 9px; color: #475569; text-transform: uppercase; letter-spacing: 2px; }
        .header-meta { text-align: right; font-size: 9px; color: #475569; }

        /* --- Footer Fijo --- */
        footer { 
            position: fixed; 
            bottom: -40px; 
            left: 0; 
            right: 0; 
            height: 30px; 
            border-top: 1px solid #e2e8f0; 
            padding-top: 10px; 
            text-align: center; 
            font-size: 8px; 
            color: #94a3b8; 
        }

        /* --- Títulos --- */
        .doc-title { 
            text-align: center; 
            font-size: 16px; 
            font-weight: bold; 
            margin-bottom: 15px; 
            text-transform: uppercase; 
            color: #0f172a;
            letter-spacing: 0.5px;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 25px;
            margin-bottom: 8px;
            border-bottom: 1px solid #4f46e5;
            padding-bottom: 4px;
            display: inline-block;
        }

        /* --- Tabla Resumen Mensual (Matricial) --- */
        .matrix-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 8px;
        }
        /* CORRECCIÓN AQUÍ: Usamos HEX directo para asegurar que el fondo se vea */
        .matrix-table th {
            background-color: #0f172a; /* Azul Oscuro Corporativo */
            color: #ffffff;            /* Texto Blanco */
            padding: 5px 2px;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #0f172a;
        }
        .matrix-table th.th-user { text-align: left; padding-left: 8px; width: 120px; }
        
        .matrix-table td {
            border-bottom: 1px solid #e2e8f0;
            padding: 6px 2px;
            text-align: center;
            color: #475569;
        }
        /* Zebra Striping con HEX directo */
        .matrix-table tr:nth-child(even) { background-color: #f8fafc; }
        
        .matrix-table .val-zero { color: #cbd5e1; }
        .matrix-table .val-data { color: #0f172a; font-weight: bold; }
        .matrix-table .col-total { background-color: #f1f5f9; font-weight: bold; color: #0f172a; border-left: 1px solid #e2e8f0; }

        /* --- Tabla de Datos Detallada --- */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .data-table th { 
            background-color: #f1f5f9; 
            color: #0f172a; 
            font-weight: bold; 
            text-transform: uppercase; 
            font-size: 9px; 
            padding: 8px; 
            text-align: left; 
            border-bottom: 1px solid #cbd5e1;
        }
        .data-table td { padding: 8px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .data-table tr:nth-child(even) { background-color: #fafafa; }

        /* Estilos de Celdas */
        .col-date { width: 75px; color: #475569; font-size: 9px; }
        .col-user { width: 100px; font-weight: bold; color: #0f172a; }
        .col-type { width: 60px; text-align: center; }
        
        /* Badges */
        .pill { padding: 2px 5px; border-radius: 3px; font-size: 7px; font-weight: bold; text-transform: uppercase; display: inline-block; }
        .pill-comment { background-color: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }
        .pill-history { background-color: #fffbeb; color: #b45309; border: 1px solid #fcd34d; }
        
        .filter-badge { background-color: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; padding: 2px 6px; border-radius: 4px; font-size: 9px; display: inline-block; margin-right: 4px; }

        /* Detalles */
        .project-name { font-size: 8px; text-transform: uppercase; color: #94a3b8; display: block; margin-bottom: 2px; }
        .task-title { font-size: 10px; font-weight: bold; color: #475569; display: block; margin-bottom: 4px; }
        .text-comment { font-style: italic; color: #475569; font-size: 9px; }
        .box-change { background-color: white; border: 1px solid #cbd5e1; padding: 3px 6px; border-radius: 4px; font-size: 9px; display: inline-block; }
        
        /* Caja Resumen */
        .summary-box { background-color: #f8fafc; padding: 10px; border: 1px solid #e2e8f0; border-radius: 4px; margin-bottom: 20px; }
        .summary-label { font-weight: bold; color: #0f172a; font-size: 9px; }
        .summary-val { color: #475569; font-size: 9px; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <header>
        <table class="header-table">
            <tr>
                <td style="vertical-align: bottom;">
                    <div class="logo-text">CORPENTUNIDA</div>
                    <div class="sub-logo">Sistema de Gestión & Trazabilidad</div>
                </td>
                <td class="header-meta">
                    <strong>Generado por:</strong> {{ $user }}<br>
                    <strong>Emisión:</strong> {{ $date }}<br>
                    <strong>Ref:</strong> AUD-{{ now()->format('Ymd-Hi') }}
                </td>
            </tr>
        </table>
    </header>

    {{-- FOOTER --}}
    <footer>
        Documento confidencial - Corpentunida S.A.S. | Página <span class="pagenum"></span>
    </footer>

    <main>
        <div class="doc-title">Informe de Auditoría Global {{ $year }}</div>

        {{-- RESUMEN DE FILTROS --}}
        <div class="summary-box">
            <table width="100%">
                <tr>
                    <td width="15%" class="summary-label">Parámetros:</td>
                    <td>
                        @if(count($filters))
                            @foreach($filters as $filter)
                                <span class="filter-badge">{{ $filter }}</span>
                            @endforeach
                        @else
                            <span style="font-style:italic; font-size:9px; color:#94a3b8;">Reporte general sin filtros.</span>
                        @endif
                    </td>
                    <td width="20%" style="text-align:right; font-size:9px;">
                        <strong>Total Registros:</strong> {{ count($events) }}
                    </td>
                </tr>
            </table>
        </div>

        {{-- SECCIÓN 1: MATRIZ DE ACTIVIDAD MENSUAL --}}
        @if(count($monthlyStats) > 0)
            <div class="section-title">ACTIVIDAD MENSUAL POR USUARIO ({{ $year }})</div>
            <table class="matrix-table">
                <thead>
                    <tr>
                        <th class="th-user">Usuario / Líder</th>
                        <th>Ene</th><th>Feb</th><th>Mar</th><th>Abr</th><th>May</th><th>Jun</th>
                        <th>Jul</th><th>Ago</th><th>Sep</th><th>Oct</th><th>Nov</th><th>Dic</th>
                        <th class="col-total">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyStats as $stat)
                        <tr>
                            <td style="text-align: left; padding-left: 8px; font-weight:bold; color:#0f172a;">
                                {{ Str::limit($stat['name'], 22) }}
                            </td>
                            @foreach($stat['months'] as $count)
                                <td class="{{ $count > 0 ? 'val-data' : 'val-zero' }}">
                                    {{ $count > 0 ? $count : '-' }}
                                </td>
                            @endforeach
                            <td class="col-total">{{ $stat['total'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- SECCIÓN 2: DETALLE DE OPERACIONES --}}
        <div class="section-title" style="margin-top: 15px;">DETALLE DE TRANSACCIONES</div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th class="col-date">Fecha / Hora</th>
                    <th class="col-user">Responsable</th>
                    <th class="col-type">Tipo</th>
                    <th>Detalle de la Operación</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                    <tr>
                        <td class="col-date">
                            {{ $event->created_at->format('Y-m-d') }}<br>
                            <span style="color: #94a3b8;">{{ $event->created_at->format('H:i:s') }}</span>
                        </td>
                        <td class="col-user">
                            {{ $event->user->name ?? 'Sistema' }}
                        </td>
                        <td class="col-type">
                            @if($event->tipo_evento === 'comentario')
                                <span class="pill pill-comment">Feedback</span>
                            @else
                                <span class="pill pill-history">Estado</span>
                            @endif
                        </td>
                        <td>
                            <span class="project-name">
                                PRJ: {{ Str::upper(Str::limit($event->task->workflow->nombre ?? 'N/A', 60)) }}
                            </span>
                            <span class="task-title">
                                Tarea: {{ $event->task->titulo ?? 'Item Eliminado' }}
                            </span>

                            @if($event->tipo_evento === 'comentario')
                                <div class="text-comment">"{{ $event->comentario }}"</div>
                                @if($event->soporte)
                                    <div style="font-size: 8px; color: #4f46e5; margin-top: 2px;">
                                        [Evidencia Adjunta en Sistema]
                                    </div>
                                @endif
                            @elseif($event->tipo_evento === 'historial')
                                <div class="box-change">
                                    {{ str_replace('_', ' ', $event->estado_anterior) }} 
                                    <span style="color:#94a3b8; padding:0 3px;">➜</span> 
                                    <strong>{{ str_replace('_', ' ', $event->estado_nuevo) }}</strong>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 30px; color: #94a3b8;">
                            No se encontraron registros detallados para los filtros seleccionados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div style="margin-top: 30px; font-size: 8px; color: #94a3b8; text-align: center; border-top:1px solid #e2e8f0; padding-top:5px;">
            Fin del reporte generado el {{ now()->format('Y-m-d H:i:s') }}
        </div>
    </main>

    {{-- Script para numeración de páginas --}}
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Pág. {PAGE_NUM} / {PAGE_COUNT}";
            $size = 8;
            $font = $fontMetrics->getFont("Helvetica");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size, array(0.5, 0.5, 0.5));
        }
    </script>
</body>
</html>