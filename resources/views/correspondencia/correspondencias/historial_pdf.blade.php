<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Historial Radicado #{{ $correspondencia->id_radicado }}</title>
    <style>
        @page {
            margin: 2.0cm 1.5cm 2.0cm 1.5cm;
            @bottom-right {
                content: "Página " counter(page) " de " counter(pages);
                font-family: 'Helvetica', Arial, sans-serif;
                font-size: 9px;
                color: #64748b;
            }
        }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            color: #1e293b;
            line-height: 1.5;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        /* --- ENCABEZADO PRINCIPAL --- */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .header-logo-title {
            text-align: left;
            vertical-align: middle;
        }

        .header-logo-title h2 {
            margin: 0;
            font-size: 20px;
            color: #0f172a;
            letter-spacing: -0.5px;
            font-weight: bold;
        }

        .header-logo-title p {
            margin: 4px 0 0 0;
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-report-info {
            text-align: right;
            font-size: 10px;
            color: #334155;
            line-height: 1.4;
            vertical-align: middle;
        }

        /* --- SECCIÓN HERO: RADICADO Y ASUNTO --- */
        .hero-radicado-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #f1f5f9;
            border-left: 5px solid #1e40af;
        }

        .hero-radicado-box td {
            padding: 15px 20px;
        }

        .hero-title {
            font-size: 24px;
            color: #1e40af;
            margin: 0;
            font-weight: bold;
        }

        .hero-asunto-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #64748b;
            margin-top: 8px;
            font-weight: bold;
        }

        .hero-asunto-text {
            font-size: 15px;
            color: #334155;
            margin: 2px 0 0 0;
            font-weight: normal;
            line-height: 1.3;
        }

        /* --- SECCIONES / TITULOS --- */
        .section-title {
            color: #0f172a;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 4px;
        }

        /* --- TABLAS DE DATOS GENERALES --- */
        .table-datos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .table-datos th, .table-datos td {
            border: 1px solid #e2e8f0;
            padding: 7px 10px;
            text-align: left;
            vertical-align: top;
        }

        .table-datos th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: bold;
            width: 20%;
        }

        .table-datos td {
            width: 30%;
        }

        /* --- TABLA RUTA UNIFICADA COMPACTA --- */
        .table-timeline {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table-timeline th {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            padding: 8px 10px;
            border: 1px solid #1e293b;
            text-align: left;
        }

        .table-timeline td {
            border: 1px solid #e2e8f0;
            padding: 10px;
            vertical-align: top;
        }

        .table-timeline tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* --- COMPONENTES INTERNOS DE LA LÍNEA DE TIEMPO --- */
        .log-meta {
            font-size: 10px;
            color: #64748b;
            line-height: 1.3;
        }
        
        .log-user {
            font-weight: bold;
            color: #334155;
            font-size: 11px;
        }

        .log-stage {
            font-weight: bold;
            color: #1e40af;
            font-size: 11px;
            margin-bottom: 5px;
        }

        .nota-comentario {
            color: #334155;
            font-size: 11px;
            word-wrap: break-word;
        }

        /* --- BADGES (INSIGNIAS MEJORADAS PARA ESTADOS) --- */
        .badge {
            display: inline-block;
            padding: 3px 7px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .badge-success { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .badge-info { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-size: 11px; padding: 5px 10px;}
        .badge-warning { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        
        /* --- ADJUNTOS / EVIDENCIAS --- */
        .attachments-box {
            margin-top: 8px;
            padding: 6px 10px;
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 4px;
        }

        .attachments-title {
            font-size: 9px;
            color: #475569;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .file-link {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            color: #2563eb;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    @php
        // Array global para recolectar imágenes para el anexo final
        $todasLasImagenes = [];
    @endphp

    <table class="header-table">
        <tr>
            <td class="header-logo-title">
                <h2>CORPENTUNIDA</h2>
                <p>Sistema de Gestión Documental y Correspondencia</p>
            </td>
            <td class="header-report-info">
                <strong>Reporte de Trazabilidad Histórica</strong><br>
                Fecha Impresión: {{ now()->format('d/m/Y h:i A') }}<br>
                Operador: {{ auth()->user()->name }}
            </td>
        </tr>
    </table>

    <table class="hero-radicado-box">
        <tr>
            <td>
                <div class="hero-title">Radicado #{{ $correspondencia->id_radicado }}</div>
                <div class="hero-asunto-label">Asunto Documental</div>
                <div class="hero-asunto-text"><strong>{{ $correspondencia->asunto }}</strong></div>
            </td>
        </tr>
    </table>

    <div class="section-title">Información General de Radicación</div>
    <table class="table-datos">
        <tr>
            <th>Fecha Solicitud:</th>
            <td>{{ $correspondencia->fecha_solicitud ? $correspondencia->fecha_solicitud->format('d/m/Y h:i A') : 'N/A' }}</td>
            <th>Medio de Recepción:</th>
            <td>{{ $correspondencia->medioRecepcion->nombre ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Remitente / Tercero:</th>
            <td>{{ $correspondencia->remitente->cod_ter ?? '' }} - {{ $correspondencia->remitente->nom_ter ?? 'N/A' }}</td>
            <th>Flujo / Serie TRD:</th>
            <td>{{ $correspondencia->flujo->nombre ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Estado Actual:</th>
            <td colspan="3">
                <span class="badge badge-info">
                    ESTADO: {{ $correspondencia->estado->nombre ?? 'Sin Estado' }}
                </span>
            </td>
        </tr>
    </table>

    <div class="section-title">Ruta del Documento y Trazabilidad de Gestión</div>
    <table class="table-timeline">
        <thead>
            <tr>
                <th style="width: 20%;">Fecha y Gestor</th>
                <th style="width: 30%;">Etapa y Estado del Paso</th>
                <th style="width: 50%;">Detalles, Notas y Evidencias</th>
            </tr>
        </thead>
        <tbody>
            
            {{-- 1. Fila de Apertura Inicial si existe observación previa --}}
            @if(!empty($correspondencia->observacion_previa))
                <tr>
                    <td>
                        <span class="log-user">Sistema / Radicación</span><br>
                        <span class="log-meta">{{ $correspondencia->fecha_solicitud ? $correspondencia->fecha_solicitud->format('d/m/Y h:i A') : 'N/A' }}</span>
                    </td>
                    <td>
                        <div class="log-stage">Apertura del Radicado</div>
                        <span class="badge badge-success">Iniciado</span>
                    </td>
                    <td class="nota-comentario">
                        <strong>Nota de ingreso:</strong><br>
                        {!! nl2br(e($correspondencia->observacion_previa)) !!}
                    </td>
                </tr>
            @endif

            {{-- 2. Recorrido Unificado de los Procesos (Historial) --}}
            @forelse($correspondencia->procesos as $log)
                @php
                    $comentarioTexto = $log->descripcion ?? $log->observaciones ?? $log->comentario ?? $log->detalle ?? $log->observacion ?? null;
                    $urlsArchivos = $log->getArchivosUrls() ?? [];
                    $tieneArchivos = count($urlsArchivos) > 0;
                @endphp
                <tr>
                    <td>
                        <span class="log-user">{{ $log->usuario->name ?? $log->user_id ?? 'Gestor Asignado' }}</span><br>
                        <span class="log-meta">{{ $log->created_at ? $log->created_at->format('d/m/Y h:i A') : 'N/A' }}</span>
                    </td>
                    <td>
                        <div class="log-stage">{{ $log->proceso->nombre ?? 'Etapa de Ruta' }}</div>
                        <span class="badge badge-success">Completado</span>
                        
                        @if($log->estado_paso)
                            <br>
                            <span class="badge badge-warning" style="margin-top: 4px;">
                                Estado: {{ $log->estado_paso }}
                            </span>
                        @endif
                    </td>
                    <td class="nota-comentario">
                        @if(!empty($comentarioTexto))
                            {!! nl2br(e($comentarioTexto)) !!}
                        @else
                            <span class="log-meta" style="font-style: italic;">Transición de etapa sin observaciones manuscritas.</span>
                        @endif

                        {{-- Adjuntos incorporados directamente en la celda de la ruta --}}
                        @if($tieneArchivos)
                            <div class="attachments-box">
                                <div class="attachments-title">Evidencias Anexas:</div>
                                @foreach($urlsArchivos as $archivoComentario)
                                    @php
                                        $extC = strtolower(pathinfo($archivoComentario['nombre'], PATHINFO_EXTENSION));
                                        if (in_array($extC, ['jpg', 'jpeg', 'png'])) {
                                            $todasLasImagenes[] = [
                                                'nombre' => 'Gestión (' . ($log->proceso->nombre ?? 'Paso') . '): ' . $archivoComentario['nombre'],
                                                'url' => $archivoComentario['url']
                                            ];
                                        }
                                    @endphp
                                    <div style="margin-bottom: 2px;">
                                        <span style="color: #3b82f6;">&#128194;</span>
                                        <a href="{{ $archivoComentario['url'] }}" target="_blank" class="file-link">
                                            {{ $archivoComentario['nombre'] }}
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                @if(empty($correspondencia->observacion_previa))
                    <tr>
                        <td colspan="3" style="text-align: center; color: #64748b; padding: 15px;">
                            No se registran movimientos ni acciones de gestión sobre este radicado.
                        </td>
                    </tr>
                @endif
            @endforelse

            {{-- 3. Fila de Cierre Final si existe --}}
            @if(!empty($correspondencia->final_descripcion))
                <tr>
                    <td>
                        <span class="log-user">Sistema Documental</span><br>
                        <span class="log-meta">{{ $correspondencia->updated_at ? $correspondencia->updated_at->format('d/m/Y h:i A') : '' }}</span>
                    </td>
                    <td>
                        <div class="log-stage">Cierre del Radicado</div>
                        <span class="badge badge-success">Finalizado</span>
                    </td>
                    <td class="nota-comentario" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 4px; padding: 6px;">
                        <strong>Conclusión de cierre:</strong><br>
                        {!! nl2br(e($correspondencia->final_descripcion)) !!}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="section-title">Documentos Custodiados en Radicación Inicial</div>
    <table class="table-datos" style="margin-top: 10px;">
        <tr>
            <th style="width: 25%;">Archivos Originales:</th>
            <td>
                @php
                    $archivosRaw = $correspondencia->documento_arc;
                    if (is_string($archivosRaw)) {
                        $archivosArr = json_decode($archivosRaw, true) ?? [];
                    } else {
                        $archivosArr = (array)$archivosRaw;
                    }
                    $archivosArr = array_filter($archivosArr);
                @endphp

                @if(count($archivosArr) > 0)
                    @foreach($archivosArr as $index => $archivo)
                        @php
                            $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                            if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                                $todasLasImagenes[] = [
                                    'nombre' => 'Apertura Radicado: ' . basename($archivo),
                                    'url' => $correspondencia->getFile($archivo)
                                ];
                            }
                        @endphp
                        <div style="margin-bottom: 5px; font-size: 11px;">
                            <span style="color: #64748b;">Anexo #{{ $index + 1 }}:</span>
                            <a href="{{ $correspondencia->getFile($archivo) }}" target="_blank" class="file-link" style="color: #1e40af;">
                                {{ basename($archivo) }}
                            </a>
                        </div>
                    @endforeach
                @else
                    <span style="color: #64748b; font-style: italic;">No se indexaron archivos digitales adicionales en el registro inicial.</span>
                @endif
            </td>
        </tr>
    </table>

    @if(count($todasLasImagenes) > 0)
        @foreach($todasLasImagenes as $index => $imgData)
            <div style="page-break-before: always;">
                <div class="section-title">Anexo Visual #{{ $index + 1 }}: {{ $imgData['nombre'] }}</div>
                <div style="text-align: center; margin-top: 20px;">
                    <img src="{{ $imgData['url'] }}" style="max-width: 100%; max-height: 780px; border: 1px solid #cbd5e1; padding: 4px; background: #ffffff;">
                </div>
            </div>
        @endforeach
    @endif

</body>
</html>