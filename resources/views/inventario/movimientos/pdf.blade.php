<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta {{ $movimiento->codigo_acta }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }

        /* Encabezado */
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header h2 { margin: 5px 0 0; font-size: 14px; color: #555; }

        /* Contenedores con bordes para agrupar información */
        .section-box { border: 1px solid #000; margin-bottom: 15px; padding: 10px; border-radius: 4px; }
        .section-title { font-weight: bold; background-color: #f2f2f2; margin: -10px -10px 10px -10px; padding: 5px 10px; border-bottom: 1px solid #000; font-size: 12px; }

        /* Tablas de información */
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 6px 4px; vertical-align: bottom; }
        .linea-texto { display: inline-block; border-bottom: 1px solid #000; width: 100%; min-height: 15px; }

        /* Tabla de items */
        .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .items-table th, .items-table td { border: 1px solid #000; padding: 6px; text-align: left; }
        .items-table th { background-color: #f2f2f2; font-weight: bold; text-align: center; font-size: 10px;}
        .items-table td { font-size: 10px; }

        /* Textos Legales y Observaciones */
        .nota-legal { font-size: 10px; text-align: justify; margin-top: 10px; line-height: 1.4; }

        /* Firmas */
        .firmas-container { width: 100%; margin-top: 40px; border-collapse: collapse; }
        .firmas-container td { width: 50%; padding: 20px 10px 10px 10px; text-align: center; vertical-align: bottom; }
        .firma-box { text-align: left; display: inline-block; width: 90%; }
        .linea-firma { border-bottom: 1px solid #000; width: 100%; margin-bottom: 5px; height: 40px; }
        .firma-texto { font-size: 11px; line-height: 1.6; }
    </style>
</head>
<body>

    <div class="header">
        <h1>FORMULARIO DE PRÉSTAMO O ASIGNACIÓN DE EQUIPOS INFORMÁTICOS</h1>
        <h2>Acta No. {{ $movimiento->codigo_acta }}</h2>
        <p style="margin: 5px 0 0;"><strong>Fecha y Hora:</strong> {{ $movimiento->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <!-- SECCIÓN: DATOS USUARIO SOLICITANTE -->
    <div class="section-box">
        <div class="section-title">DATOS USUARIO SOLICITANTE</div>
        <table class="info-table">
            <tr>
                <td style="width: 15%;"><strong>NOMBRE(S):</strong></td>
                <td style="width: 35%; border-bottom: 1px solid #000;">{{ $movimiento->responsable->name ?? '' }}</td>
                <td style="width: 15%;"><strong>IDENTIFICACIÓN:</strong></td>
                <td style="width: 35%; border-bottom: 1px solid #000;">
                    <!-- Se llama al campo nid del modelo User -->
                    {{ $movimiento->responsable->nid ?? '' }}
                </td>
            </tr>
            <tr>
                <td><strong>ÁREA:</strong></td>
                <td style="border-bottom: 1px solid #000;">
                    <!-- Se llama a la relación cargoRelation (GdoCargo) -->
                    {{ $movimiento->responsable->cargoRelation->gdoArea->nombre ?? '' }}
                </td>
                <td><strong>CELULAR:</strong></td>
                <td style="border-bottom: 1px solid #000;">
                    <!-- Se llama a la relación perfilEmpleado (GdoEmpleado) -->
                    {{ $movimiento->responsable->perfilEmpleado->celular_personal ?? $movimiento->responsable->perfilEmpleado->telefono ?? '' }}
                </td>
            </tr>
            <tr>
                <td><strong>CARGO:</strong></td>
                <td style="border-bottom: 1px solid #000;">
                    <!-- Se llama a la relación cargoRelation (GdoCargo) -->
                    {{ $movimiento->responsable->cargoRelation->nombre_cargo ?? $movimiento->responsable->cargoRelation->nombre_cargo ?? '' }}
                </td>
                <td colspan="2">
                    <strong>PRÉSTAMO:</strong> [ &nbsp; ] &nbsp;&nbsp;&nbsp;&nbsp; <strong>ASIGNACIÓN:</strong> [ &nbsp; ]
                </td>
            </tr>
        </table>
    </div>

    <!-- SECCIÓN: DESCRIPCIÓN DE EQUIPOS -->
    <div class="section-box">
        <div class="section-title">DESCRIPCIÓN DE EQUIPOS</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 25%;">DESCRIPCIÓN / ACTIVO</th>
                    <th style="width: 15%;">MARCA</th>
                    <th style="width: 20%;">SERIAL / PLACA</th>
                    <th style="width: 15%;">ESTADO</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movimiento->detalles as $index => $detalle)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $detalle->activo->nombre ?? 'N/A' }}</td>
                    <td>{{ $detalle->activo->referencia->marca->nombre ?? 'N/A' }}</td>
                    <td>
                        {{ $detalle->activo->serial ?? 'N/A' }} <br>
                        <small>{{ $detalle->activo->codigo_activo ?? '' }}</small>
                    </td>
                    <td>{{ $detalle->estado_individual ?? 'N/A' }}</td>
                </tr>
                @endforeach
                <!-- Filas extra vacías opcionales para rellenar a mano si se desea -->
            </tbody>
        </table>
    </div>

    <!-- SECCIÓN: ACEPTACIÓN Y OBSERVACIONES -->
    <div class="section-box">
        <div class="section-title">ACEPTACIÓN ENTREGA DE EQUIPO</div>
        <p style="margin: 0 0 10px 0; font-size: 11px;">
            <strong>OBSERVACIONES:</strong> <br>
            <span class="linea-texto" style="margin-top: 5px;">{{ $movimiento->observacion_general ?? '' }}</span>
            <span class="linea-texto" style="margin-top: 15px;"></span>
        </p>

        <div class="nota-legal">
            <strong>Nota:</strong> Los bienes muebles son de propiedad de la empresa. En caso de pérdida o daño, la empresa podrá hacer efectivo el valor total o parcial del o los equipos a quien recibe el bien a título de préstamo o asignación. En caso de cambio de equipo o término de contrato debe solicitar paz y salvo.
        </div>
    </div>

    <!-- SECCIÓN: FIRMAS -->
    <table class="firmas-container">
        <tr>
            <!-- Firma 1: Quien Recibe (Responsable) -->
            <td>
                <div class="firma-box">
                    <div class="linea-firma"></div>
                    <div class="firma-texto">
                        <strong>FIRMA QUIEN RECIBE:</strong><br>
                        {{ $movimiento->responsable->name ?? '_______________________' }}<br>
                        <strong>CC:</strong> {{ $movimiento->responsable->nid ?? '_______________________' }}<br>
                        <strong>CARGO:</strong> {{ $movimiento->responsable->cargoRelation->nombre_cargo ?? '____________________' }}
                    </div>
                </div>
            </td>
            <!-- Firma 2: Quien Entrega (Creador) -->
            <td>
                <div class="firma-box">
                    <div class="linea-firma"></div>
                    <div class="firma-texto">
                        <strong>QUIEN ENTREGA:</strong><br>
                        {{ $movimiento->creador->name ?? '_______________________' }}<br>
                        <strong>CC:</strong> {{ $movimiento->creador->nid ?? '_______________________' }}<br>
                        <strong>CARGO:</strong> {{ $movimiento->creador->cargoRelation->nombre_cargo ?? '____________________' }}
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <!-- Firma 3: Revisado Por (Manual) -->
            <td style="padding-top: 30px;">
                <div class="firma-box">
                    <div class="linea-firma"></div>
                    <div class="firma-texto">
                        <strong>REVISADO POR:</strong><br>
                        _______________________<br>
                        <strong>CC:</strong> _______________________<br>
                        <strong>CARGO:</strong> ____________________
                    </div>
                </div>
            </td>
            <!-- Firma 4: Aprobado Por (Manual) -->
            <td style="padding-top: 30px;">
                <div class="firma-box">
                    <div class="linea-firma"></div>
                    <div class="firma-texto">
                        <strong>APROBADO POR:</strong><br>
                        _______________________<br>
                        <strong>CC:</strong> _______________________<br>
                        <strong>CARGO:</strong> ____________________
                    </div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
