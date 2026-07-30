<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta {{ $movimiento->codigo_acta }}</title>
    <style>
        /* --- CONFIGURACIÓN DE PÁGINA --- */
        @page {
            margin: 4cm 2.5cm 3cm 2.5cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #1f2937; /* Gris muy oscuro, más suave que el negro puro */
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }

        /* --- FONDO IMAGEN --- */
        #fondo-plantilla {
            position: fixed;
            top: -4cm;
            left: -2.5cm;
            width: 21cm;
            height: 29.7cm;
            z-index: -2000;
        }
        #fondo-plantilla img { width: 100%; height: 100%; }

        /* --- MARCA DE AGUA TEXTO --- */
        .marca-agua {
            position: fixed;
            top: 40%;
            left: 10%;
            width: 80%;
            text-align: center;
            transform: rotate(-45deg);
            font-size: 60px;
            color: #d1d5db;
            opacity: 0.3;
            z-index: -1000;
        }

        /* --- ENCABEZADO --- */
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #374151;
            padding-bottom: 12px;
            margin-top: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 15px;
            text-transform: uppercase;
            font-weight: bold;
            color: #111827;
        }
        .header h2 {
            margin: 6px 0;
            font-size: 13px;
            color: #4b5563;
            font-weight: bold;
        }
        .header-meta {
            font-size: 11px;
            color: #6b7280;
            margin-top: 5px;
        }
        .badge-total-header {
            background-color: #e5e7eb;
            padding: 3px 8px;
            border-radius: 4px;
            color: #111827;
            font-weight: bold;
            display: inline-block;
            margin-top: 4px;
        }

        /* --- CONTENEDORES DE SECCIÓN --- */
        .section-box {
            border: 1px solid #d1d5db;
            margin-bottom: 18px;
            padding: 12px;
            border-radius: 6px;
            background-color: #ffffff;
        }
        .section-title {
            font-weight: bold;
            background-color: #f3f4f6;
            margin: -12px -12px 12px -12px;
            padding: 8px 12px;
            border-bottom: 1px solid #d1d5db;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
            font-size: 11px;
            text-transform: uppercase;
            color: #374151;
        }

        .badge-count {
            background-color: #374151;
            color: #ffffff;
            font-size: 9px;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: 8px;
            vertical-align: middle;
        }

        /* --- TABLAS DE INFORMACIÓN (Estilo Formulario) --- */
        .info-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
        .info-table td { padding: 0 4px; vertical-align: bottom; }
        .info-label { font-size: 10px; color: #6b7280; font-weight: bold; text-transform: uppercase; }
        .info-value { border-bottom: 1px solid #9ca3af; color: #111827; padding-bottom: 2px; font-weight: bold; }

        /* --- TABLA DE ITEMS (Estilo Compacto / Pro Excel) --- */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
        }
        .items-table th {
            background-color: #e5e7eb;
            color: #374151;
            border: 1px solid #d1d5db;
            padding: 6px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
        }
        .items-table td {
            border: 1px solid #d1d5db;
            padding: 5px 6px;
            vertical-align: middle;
            color: #1f2937;
        }
        /* Zebra Striping */
        .items-table tbody tr:nth-child(even) { background-color: #f9fafb; }

        /* Etiquetas internas tabla */
        .cat-tag {
            display: inline-block;
            background-color: #e5e7eb;
            color: #4b5563;
            font-size: 8px;
            padding: 2px 5px;
            border-radius: 4px;
            margin-top: 3px;
            font-weight: normal;
        }
        .text-muted { color: #6b7280; font-size: 8.5px; margin-top: 2px; }

        /* --- SECCIÓN OBSERVACIONES Y LEGAL --- */
        .observaciones-box {
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 8px;
            min-height: 50px;
            margin-top: 5px;
            background-color: #f9fafb;
            color: #4b5563;
            font-style: italic;
        }
        .nota-legal {
            font-size: 9.5px;
            text-align: justify;
            margin-top: 12px;
            line-height: 1.4;
            color: #6b7280;
            background-color: #f3f4f6;
            padding: 8px;
            border-radius: 4px;
            border-left: 3px solid #9ca3af;
        }

        /* --- CONTENEDOR PARA EVITAR SALTO DE PÁGINA --- */
        .evitar-salto-pagina { page-break-inside: avoid; margin-top: 15px; }

        /* --- FIRMAS --- */
        .firmas-container { width: 100%; margin-top: 30px; border-collapse: collapse; }
        .firmas-container td { width: 50%; padding: 40px 15px 5px 15px; text-align: center; vertical-align: bottom; }
        .firma-box { text-align: left; display: inline-block; width: 90%; }
        .linea-firma { border-bottom: 1px solid #4b5563; width: 100%; margin-bottom: 8px; height: 10px; }
        .firma-texto { font-size: 10px; line-height: 1.5; color: #374151; }
        .firma-texto strong { color: #111827; }
    </style>
</head>
<body>

    <!-- FONDO IMAGEN -->
    @if(isset($fondoImg) && $fondoImg)
    <div id="fondo-plantilla">
        <img src="{{ $fondoImg }}" alt="Fondo">
    </div>
    @endif

    <!-- MARCA DE AGUA -->
    <div class="marca-agua">{{ strtoupper($movimiento->tipoRegistro->nombre ?? 'ACTA') }}</div>

    <main>
        <!-- ENCABEZADO -->
        <div class="header">
            <h1>FORMULARIO DE PRÉSTAMO O ASIGNACIÓN DE EQUIPOS INFORMÁTICOS</h1>
            <h2>Acta No. {{ $movimiento->codigo_acta }}</h2>
            <div class="header-meta">
                <strong>Fecha y Hora:</strong> {{ $movimiento->created_at->format('d/m/Y H:i') }}
            </div>
            <div class="badge-total-header">
                Total Equipos Asignados: {{ $movimiento->detalles->count() }}
            </div>
        </div>

        <!-- SECCIÓN: DATOS USUARIO SOLICITANTE -->
        <div class="section-box">
            <div class="section-title">DATOS DEL USUARIO SOLICITANTE</div>
            <table class="info-table">
                <tr>
                    <td style="width: 15%;" class="info-label">NOMBRE(S):</td>
                    <td style="width: 35%;" class="info-value">{{ $movimiento->responsable->name ?? '' }}</td>
                    <td style="width: 15%;" class="info-label">IDENTIFICACIÓN:</td>
                    <td style="width: 35%;" class="info-value">{{ $movimiento->responsable->nid ?? '' }}</td>
                </tr>
                <tr>
                    <td class="info-label">ÁREA:</td>
                    <td class="info-value">{{ $movimiento->responsable->cargoRelation->gdoArea->nombre ?? '' }}</td>
                    <td class="info-label">CELULAR:</td>
                    <td class="info-value">{{ $movimiento->responsable->perfilEmpleado->celular_personal ?? $movimiento->responsable->perfilEmpleado->telefono ?? '' }}</td>
                </tr>
                <tr>
                    <td class="info-label">CARGO:</td>
                    <td class="info-value">{{ $movimiento->responsable->cargoRelation->nombre_cargo ?? '' }}</td>
                    <td class="info-label">ESTADO:</td>
                    <td class="info-value">{{ $movimiento->detalles->first()->estado_individual ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <!-- SECCIÓN: DESCRIPCIÓN DE EQUIPOS -->
        <div class="section-box">
            <div class="section-title">
                DESCRIPCIÓN DE EQUIPOS
                <span class="badge-count">{{ $movimiento->detalles->count() }} Ítems</span>
            </div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: center;">#</th>
                        <th style="width: 35%;">ACTIVO / CATEGORÍA</th>
                        <th style="width: 18%;">MARCA</th>
                        <th style="width: 24%;">SERIAL / PLACA</th>
                        <th style="width: 18%;">ESTADO</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movimiento->detalles as $index => $detalle)
                    <tr>
                        <td style="text-align: center; font-weight: bold; color: #6b7280;">{{ $index + 1 }}</td>

                        <td>
                            <div style="font-weight: bold; font-size: 10px;">
                                {{ $detalle->activo->nombre ?? 'N/A' }}
                            </div>
                            <span class="cat-tag">
                                {{ $detalle->activo->subgrupo->nombre ?? 'Sin categoría' }}
                            </span>
                        </td>

                        <td>{{ $detalle->activo->referencia->marca->nombre ?? 'N/A' }}</td>

                        <td>
                            <div style="font-weight: bold;">
                                {{ $detalle->activo->serial ?? 'N/A' }}
                            </div>
                            <div class="text-muted">
                                PLACA: {{ $detalle->activo->codigo_activo ?? 'N/A' }}
                            </div>
                        </td>

                        <td>{{ $detalle->estado_individual ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- ========================================================================= -->
        <!-- CONTENEDOR AGRUPADO: ACEPTACIÓN + FIRMAS (Para evitar el salto de página) -->
        <!-- ========================================================================= -->
        <div class="evitar-salto-pagina">
            <br>
            <!-- SECCIÓN: ACEPTACIÓN Y OBSERVACIONES -->
            <div class="section-box" style="margin-bottom: 5px; border-color: #e5e7eb; box-shadow: none;">
                <div class="section-title" style="background-color: #f9fafb;">ACEPTACIÓN ENTREGA DE EQUIPO</div>

                <div style="font-size: 10px; font-weight: bold; color: #374151;">OBSERVACIONES:</div>
                <div class="observaciones-box">
                    {{ $movimiento->observacion_general ?? 'Sin observaciones registradas al momento de la entrega.' }}
                </div>

                <div class="nota-legal">
                    <strong>Nota Legal:</strong> Los bienes muebles descritos son de propiedad de la empresa. En caso de pérdida, hurto o daño comprobado por mal uso, la empresa podrá hacer efectivo el cobro del valor total o parcial de los equipos a quien recibe el bien a título de préstamo o asignación. En caso de cambio de equipo o terminación de contrato, el colaborador deberá tramitar su respectivo paz y salvo.
                </div>
            </div>
            <br><br><br>
            <!-- SECCIÓN: FIRMAS -->
            <table class="firmas-container">
                <tr>
                    <!-- Firma 1 -->
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
                    <!-- Firma 2 -->
                    <td>
                        <div class="firma-box">
                            <div class="linea-firma"></div>
                            <div class="firma-texto">
                                <strong>FIRMA QUIEN ENTREGA:</strong><br>
                                {{ $movimiento->creador->name ?? '_______________________' }}<br>
                                <strong>CC:</strong> {{ $movimiento->creador->nid ?? '_______________________' }}<br>
                                <strong>CARGO:</strong> {{ $movimiento->creador->cargoRelation->nombre_cargo ?? '____________________' }}
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <!-- Firma 3 -->
                    <td style="padding-top: 50px;">
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
                    <!-- Firma 4 -->
                    <td style="padding-top: 50px;">
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

        </div>
        <!-- ========================================================================= -->
        <!-- FIN DEL CONTENEDOR AGRUPADO -->
        <!-- ========================================================================= -->

    </main>
</body>
</html>
