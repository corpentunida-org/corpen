<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Flujos de Trabajo</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }

        /* Encabezado Dashboard */
        .dashboard-header {
            background-color: #0f172a;
            color: #ffffff;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .dashboard-title {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }

        /* Tarjetas de Contenedor (Cards) */
        .card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .card-header {
            padding: 10px 15px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-weight: bold;
            font-size: 13px;
            color: #1e293b;
        }

        .card-body {
            padding: 15px;
        }

        /* Recuadros de Tareas Internas */
        .recuadro-tarea {
            background: #fdfdfd;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 15px;
        }

        .recuadro-tarea:last-child {
            margin-bottom: 0;
        }

        .tarea-titulo {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 8px;
            border-left: 3px solid #3b82f6;
            padding-left: 6px;
        }

        /* Badges de Estados */
        .badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            display: inline-block;
            text-transform: uppercase;
            text-align: center;
        }

        .bg-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        /* Verde - Completado */
        .bg-warning {
            background-color: #ffedd5;
            color: #ea580c;
        }

        /* Naranja - En Proceso */
        .bg-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Rojo - Pendiente */
        .bg-dark {
            background-color: #e2e8f0;
            color: #334155;
        }

        /* Tablas */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .table-dashboard th {
            background: #334155;
            color: white;
            font-size: 11px;
            padding: 8px;
            text-align: left;
        }

        .table-dashboard td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-subtasks th {
            background: #f1f5f9;
            color: #475569;
            font-size: 10px;
            padding: 5px;
            border-bottom: 1px solid #cbd5e1;
            text-align: left;
        }

        .table-subtasks td {
            padding: 6px 5px;
            font-size: 11px;
            border-bottom: 1px solid #f1f5f9;
        }

        .seccion-titulo {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            margin: 20px 0 10px 0;
            padding-bottom: 4px;
            border-bottom: 2px solid #cbd5e1;
        }

        .eventos-titulo {
            font-size: 11px;
            font-weight: bold;
            color: #475569;
            margin: 10px 0 5px 0;
            text-transform: uppercase;
        }

        .evento-item {
            font-size: 11px;
            color: #555;
            padding: 3px 0;
            border-bottom: 1px dashed #f1f5f9;
        }
    </style>
</head>

<body>

    <div class="dashboard-header">
        <table style="width: 100%; margin: 0;">
            <tr>
                <td>
                    <div class="dashboard-title">Dashboard de Control de Proyectos y Auditoría</div>
                </td>
                <td style="text-align: right; font-size: 11px; color: #94a3b8;">Fecha de Reporte: {{ $date }}
                </td>
            </tr>
        </table>
    </div>

    <div class="seccion-titulo">📋 Listado General de Control (Todos los Proyectos)</div>
    <table class="table-dashboard">
        <thead>
            <tr>
                <th style="width: 15%;">ID Proyecto</th>
                <th>Nombre del Workflow</th>
                <th style="width: 25%; text-align: center;">Estado General</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($todosLosWorkflows as $proyecto)
                <tr>
                    <td><strong>#{{ $proyecto->id }}</strong></td>
                    <td>{{ $proyecto->nombre }}</td>
                    <td style="text-align: center;">
                        @if (strtolower($proyecto->estado) === 'completado')
                            <span class="badge bg-success">Completado</span>
                        @elseif(strtolower($proyecto->estado) === 'activo' || strtolower($proyecto->estado) === 'en proceso')
                            <span class="badge bg-warning">En Proceso</span>
                        @else
                            <span class="badge bg-danger">{{ $proyecto->estado ?? 'Pendiente' }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="page-break-after: always;"></div>

    <div class="seccion-titulo">✅ Detalle de Proyectos Completados</div>
    @if ($workflowsCompletos->isEmpty())
        <p style="color: #64748b; font-style: italic;">No se registran flujos finalizados en este periodo.</p>
    @else
        <table class="table-dashboard">
            <thead>
                <tr>
                    <th style="width: 15%;">ID</th>
                    <th>Nombre del Workflow</th>
                    <th style="width: 25%; text-align: center;">Indicador</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($workflowsCompletos as $wf)
                    <tr>
                        <td><strong>#{{ $wf->id }}</strong></td>
                        <td>{{ $wf->nombre }}</td>
                        <td style="text-align: center;"><span class="badge bg-success">Finalizado con Éxito</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div style="page-break-after: always;"></div>

    <div class="seccion-titulo">❌ Detalle de Pendientes en Proyectos Incompletos</div>

    @if ($workflowsIncompletos->isEmpty())
        <p style="color: #64748b; font-style: italic;">¡Excelente! Todos los flujos de trabajo se encuentran
            completados.</p>
    @else
        @foreach ($workflowsIncompletos as $wf)
            <div class="card">
                <div class="card-header">
                    <table style="width: 100%; margin: 0;">
                        <tr>
                            <td>Workflow #{{ $wf->id }}: <span
                                    style="font-weight: 500;">{{ $wf->nombre }}</span></td>
                            <td style="text-align: right;"><span class="badge bg-danger">Incompleto</span></td>
                        </tr>
                    </table>
                </div>

                <div class="card-body">
                    @if ($wf->tasks->isEmpty())
                        <p style="margin: 0; color: #94a3b8; font-style: italic;">Este proyecto no tiene tareas con
                            arrays de subtareas válidos en su descripción.</p>
                    @else
                        @foreach ($wf->tasks as $task)
                            <div class="recuadro-tarea">
                                <div class="tarea-titulo">
                                    Tarea: {{ $task->titulo }} <span
                                        style="font-weight: normal; color: #64748b; font-size: 10px;">(ID:
                                        #{{ $task->id }})</span>
                                </div>

                                <table class="table-subtasks">
                                    <thead>
                                        <tr>
                                            <th>Descripción de Subtarea</th>
                                            <th style="width: 20%;">Responsable</th>
                                            <th style="width: 15%;">Tiempo</th>
                                            <th style="width: 18%; text-align: center;">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($task->subtareas_estructuradas as $subtarea)
                                            @php
                                                $status = strtolower($subtarea['status'] ?? 'pendiente');
                                                if (
                                                    $status === 'completa' ||
                                                    $status === 'completado' ||
                                                    $status === 'completo'
                                                ) {
                                                    $badgeColor = 'bg-success';
                                                    $textoStatus = 'Completado';
                                                } elseif (
                                                    $status === 'en proceso' ||
                                                    $status === 'proceso' ||
                                                    $status === 'progreso'
                                                ) {
                                                    $badgeColor = 'bg-warning';
                                                    $textoStatus = 'En Proceso';
                                                } else {
                                                    $badgeColor = 'bg-danger';
                                                    $textoStatus = 'Pendiente';
                                                }
                                            @endphp
                                            <tr>
                                                <td><strong>{{ $subtarea['descripcion'] ?? 'Sin descripción' }}</strong>
                                                </td>
                                                <td>{{ !empty($subtarea['responsable']) ? $subtarea['responsable'] : 'No asignado' }}
                                                </td>
                                                <td>{{ !empty($subtarea['tiempo']) ? $subtarea['tiempo'] : 'N/D' }}
                                                </td>
                                                <td style="text-align: center;">
                                                    <span class="badge {{ $badgeColor }}" style="min-width: 65px;">
                                                        {{ $textoStatus }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="eventos-titulo">⏱️ Historial de Eventos sobre esta Tarea</div>

                                @if (empty($task->events) || count($task->events) === 0)
                                    <div
                                        style="font-size: 11px; color: #94a3b8; font-style: italic; padding-left: 5px;">
                                        No se han registrado eventos o acciones recientes sobre esta tarea.
                                    </div>
                                @else
                                    @foreach ($task->events as $event)
                                        <div class="evento-item">
                                            •
                                            <strong>[{{ \Carbon\Carbon::parse($event->created_at)->format('d/m/Y H:i') }}]</strong>
                                            {{ $event->descripcion ?? ($event->accion ?? 'Acción ejecutada') }}
                                            @if (!empty($event->user))
                                                (Por: <em>{{ $event->user->name }}</em>)
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</body>

</html>
