<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Historial Radicado #{{ $correspondencia->id_radicado }}</title>
    <style>
        @page {
            margin: 2.5cm 2cm 2.5cm 2cm;
            @bottom-right {
                content: "Página " counter(page) " de " counter(pages);
                font-family: 'Helvetica', Arial, sans-serif;
                font-size: 10px;
                color: #777777;
            }
        }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            color: #2c3e50;
            line-height: 1.4;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        /* --- ENCABEZADO --- */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            border-bottom: 2px solid #4a90e2;
            padding-bottom: 10px;
        }

        .header-logo-title {
            text-align: left;
        }

        .header-logo-title h2 {
            margin: 0;
            font-size: 18px;
            color: #2c3e50;
            letter-spacing: -0.5px;
        }

        .header-logo-title p {
            margin: 2px 0 0 0;
            font-size: 10px;
            color: #7f8c8d;
            text-transform: uppercase;
            font-weight: bold;
        }

        .header-report-info {
            text-align: right;
            font-size: 11px;
            color: #34495e;
        }

        /* --- SECCIONES / TITULOS --- */
        .section-title {
            background-color: #f8f9fa;
            color: #1a252f;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 10px;
            margin-top: 20px;
            margin-bottom: 12px;
            border-left: 4px solid #4a90e2;
        }

        /* --- TABLAS DE DATOS --- */
        .table-datos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .table-datos th, .table-datos td {
            border: 1px solid #e2e8f0;
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }

        .table-datos th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
            width: 25%;
        }

        /* --- TABLA TRAZABILIDAD / HISTÓRICO --- */
        .table-timeline {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .table-timeline th {
            background-color: #2c3e50;
            color: #ffffff;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            padding: 8px 10px;
            border: 1px solid #2c3e50;
        }

        .table-timeline td {
            border: 1px solid #e2e8f0;
            padding: 8px 10px;
            font-size: 11.5px;
        }

        .table-timeline tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* --- COMPONENTES AUXILIARES --- */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-info { background-color: #e0f2fe; color: #0369a1; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
        
        .text-muted { color: #64748b; font-size: 11px; }
        .nota-comentario { font-style: italic; color: #334155; }
        
        .file-item {
            margin-bottom: 4px;
            color: #0369a1;
            text-decoration: none;
        }
    </style>
</head>
<body>

    @php
        // Array global para recolectar TODAS las imágenes (del radicado inicial y de los comentarios)
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
                Fecha de Impresión: {{ now()->format('d/m/Y h:i A') }}<br>
                Generado por: {{ auth()->user()->name }}
            </td>
        </tr>
    </table>

    <div class="section-title">Información General del Radicado</div>
    <table class="table-datos">
        <tr>
            <th>ID Radicado:</th>
            <td><strong>#{{ $correspondencia->id_radicado }}</strong></td>
            <th>Fecha Radicación:</th>
            <td>{{ $correspondencia->fecha_solicitud ? $correspondencia->fecha_solicitud->format('d/m/Y h:i A') : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Remitente / Tercero:</th>
            <td>{{ $correspondencia->remitente->cod_ter ?? '' }} - {{ $correspondencia->remitente->nom_ter ?? 'N/A' }}</td>
            <th>Medio de Recepción:</th>
            <td>{{ $correspondencia->medioRecepcion->nombre ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Asunto:</th>
            <td colspan="3"><strong>{{ $correspondencia->asunto }}</strong></td>
        </tr>
        <tr>
            <th>Flujo / Serie TRD:</th>
            <td>{{ $correspondencia->flujo->nombre ?? 'N/A' }}</td>
            <th>Estado Actual:</th>
            <td>
                <span class="badge badge-info">
                    {{ $correspondencia->estado->nombre ?? 'Sin Estado' }}
                </span>
            </td>
        </tr>
        <tr>
            <th>Observación Inicial:</th>
            <td colspan="3" class="text-muted">
                {{ $correspondencia->observacion_previa ?? 'Sin observaciones iniciales registradas.' }}
            </td>
        </tr>
    </table>

    <div class="section-title">Historial de Ruta y Gestión de Pasos</div>
    <table class="table-timeline">
        <thead>
            <tr>
                <th style="width: 25%;">Etapa / Paso</th>
                <th style="width: 25%;">Gestionado Por</th>
                <th style="width: 20%;">Fecha Ejecución</th>
                <th style="width: 30%;">Detalle / Estado final del Paso</th>
            </tr>
        </thead>
        <tbody>
            @forelse($correspondencia->procesos as $log)
                <tr>
                    <td>
                        <strong>{{ $log->proceso->nombre ?? 'Etapa No Definida' }}</strong>
                    </td>
                    <td>
                        {{ $log->usuario->name ?? $log->user_id ?? 'Sistema' }}
                    </td>
                    <td>
                        {{ $log->created_at ? $log->created_at->format('d/m/Y h:i A') : 'N/A' }}
                    </td>
                    <td>
                        <span class="badge badge-success">Completado</span>
                        @if($log->estado_paso)
                            <br><span class="text-muted">Estado: {{ $log->estado_paso }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;" class="text-muted">No se registran movimientos ni gestiones en la ruta de este radicado aún.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Comentarios, Notas y Adjuntos de Gestión</div>
    <table class="table-timeline">
        <thead>
            <tr>
                <th style="width: 25%;">Fecha y Gestor</th>
                <th style="width: 75%;">Comentario / Nota Registrada</th>
            </tr>
        </thead>
        <tbody>
            @php $tieneComentarios = false; @endphp

            {{-- 1. Incorporar la observación de apertura si existe --}}
            @if(!empty($correspondencia->observacion_previa))
                @php $tieneComentarios = true; @endphp
                <tr>
                    <td>
                        <strong>Apertura del Radicado</strong><br>
                        <span class="text-muted" style="font-size: 10px;">
                            {{ $correspondencia->fecha_solicitud ? $correspondencia->fecha_solicitud->format('d/m/Y h:i A') : '' }}
                        </span>
                    </td>
                    <td class="nota-comentario">
                        {!! nl2br(e($correspondencia->observacion_previa)) !!}
                    </td>
                </tr>
            @endif

            {{-- 2. Recorrer los comentarios de cada paso de la ruta y sus adjuntos --}}
            @foreach($correspondencia->procesos as $log)
                @php
                    $comentarioTexto = $log->descripcion ?? $log->observaciones ?? $log->comentario ?? $log->detalle ?? $log->observacion ?? null;
                    $urlsArchivos = $log->getArchivosUrls() ?? [];
                    $tieneArchivosEnPaso = count($urlsArchivos) > 0;
                @endphp

                {{-- Si tiene comentario O si tiene archivos, mostramos la fila --}}
                @if(!empty($comentarioTexto) || $tieneArchivosEnPaso)
                    @php $tieneComentarios = true; @endphp
                    <tr>
                        <td>
                            <strong>{{ $log->usuario->name ?? 'Gestor' }}</strong><br>
                            <span class="text-muted" style="font-size: 10px;">
                                {{ $log->created_at ? $log->created_at->format('d/m/Y h:i A') : '' }}
                            </span>
                        </td>
                        <td class="nota-comentario">
                            <strong>[{{ $log->proceso->nombre ?? 'Paso de Ruta' }}]:</strong><br>
                            
                            @if(!empty($comentarioTexto))
                                {!! nl2br(e($comentarioTexto)) !!}
                            @else
                                <span class="text-muted" style="font-size: 10.5px;">(Solo se anexaron archivos)</span>
                            @endif

                            {{-- Imprimir Archivos Adjuntos del Comentario --}}
                            @if($tieneArchivosEnPaso)
                                <div style="margin-top: 8px; padding-top: 6px; border-top: 1px dashed #cbd5e1;">
                                    <strong style="font-size: 10px; color: #475569; text-transform: uppercase;">Evidencias de esta gestión:</strong><br>
                                    @foreach($urlsArchivos as $archivoComentario)
                                        @php
                                            // Recolectar para los Anexos Visuales si es imagen
                                            $extC = strtolower(pathinfo($archivoComentario['nombre'], PATHINFO_EXTENSION));
                                            if (in_array($extC, ['jpg', 'jpeg', 'png'])) {
                                                $todasLasImagenes[] = [
                                                    'nombre' => 'Gestión: ' . $archivoComentario['nombre'],
                                                    'url' => $archivoComentario['url']
                                                ];
                                            }
                                        @endphp
                                        <div style="margin-bottom: 2px; font-size: 11px;">
                                            <span style="color: #e11d48;">&bull;</span> 
                                            <a href="{{ $archivoComentario['url'] }}" target="_blank" style="color: #0369a1; font-family: monospace; text-decoration: underline;">
                                                {{ $archivoComentario['nombre'] }}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach

            {{-- 3. Incorporar observaciones de cierre final --}}
            @if(!empty($correspondencia->final_descripcion))
                @php $tieneComentarios = true; @endphp
                <tr>
                    <td>
                        <strong>Cierre de Radicado</strong><br>
                        <span class="text-muted" style="font-size: 10px;">
                            {{ $correspondencia->updated_at ? $correspondencia->updated_at->format('d/m/Y h:i A') : '' }}
                        </span>
                    </td>
                    <td class="nota-comentario" style="color: #065f46; background-color: #f0fdf4;">
                        {!! nl2br(e($correspondencia->final_descripcion)) !!}
                    </td>
                </tr>
            @endif

            @if(!$tieneComentarios)
                <tr>
                    <td colspan="2" style="text-align: center;" class="text-muted">No se han anexado comentarios ni archivos en el desarrollo de la ruta.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="section-title">Documentos y Archivos Custodiados (Radicación Inicial)</div>
    <table class="table-datos">
        <tr>
            <th>Listado de Evidencias / Archivos:</th>
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
                            // Recolectar para los Anexos Visuales si es imagen
                            $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                            if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                                $todasLasImagenes[] = [
                                    'nombre' => 'Apertura: ' . basename($archivo),
                                    'url' => $correspondencia->getFile($archivo)
                                ];
                            }
                        @endphp

                        <div class="file-item" style="margin-bottom: 6px; font-size: 11.5px;">
                            <span style="color: #e11d48;">&bull;</span> 
                            <strong>Archivo Adjunto #{{ $index + 1 }}:</strong> 
                            
                            <a href="{{ $correspondencia->getFile($archivo) }}" target="_blank" style="color: #0369a1; font-family: monospace; text-decoration: underline;">
                                {{ basename($archivo) }}
                            </a>
                        </div>
                    @endforeach
                @else
                    <span class="text-muted">No se cargaron archivos digitales adjuntos en la apertura de este radicado.</span>
                @endif
            </td>
        </tr>
    </table>

    @if(count($todasLasImagenes) > 0)
        @foreach($todasLasImagenes as $index => $imgData)
            <div style="page-break-before: always;">
                <div class="section-title">Anexo Visual #{{ $index + 1 }}: {{ $imgData['nombre'] }}</div>
                <div style="text-align: center; margin-top: 20px;">
                    <img src="{{ $imgData['url'] }}" style="max-width: 100%; max-height: 850px; border: 1px solid #cbd5e1; padding: 5px;">
                </div>
            </div>
        @endforeach
    @endif

</body>
</html>