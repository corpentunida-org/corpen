<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Contrato — {{ $modificacion->snapshot['empleado_nombre'] ?? $contrato->empleado->nombre_completo }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">

    <style>
        :root {
            --azul: #101a4d;
            --naranja: #f6a821;
            --gris-texto: #4a5568;
            --gris-linea: #e7e9f0;
        }

        @media screen {
            body {
                background-color: #eef0f5;
                margin: 0;
                padding: 20px;
                font-family: 'Helvetica', Arial, sans-serif;
            }
            .barra-herramientas {
                max-width: 21cm;
                margin: 0 auto 20px auto;
                background: #ffffff;
                padding: 15px 25px;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.06);
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 10px;
            }
            .btn-print {
                background-color: var(--azul);
                color: white;
                border: none;
                padding: 12px 25px;
                border-radius: 30px;
                font-weight: bold;
                cursor: pointer;
                font-size: 14px;
            }
            .btn-print:hover { background-color: #1c2c73; }
            .hoja-papel {
                width: 21cm;
                min-height: 29.7cm;
                background: #ffffff;
                margin: 0 auto;
                box-shadow: 0 0 24px rgba(0,0,0,0.1);
                padding: 2cm 2.2cm;
                box-sizing: border-box;
            }
        }

        @media print {
            body { background: white; margin: 0; padding: 0; font-family: 'Helvetica', Arial, sans-serif; }
            .barra-herramientas { display: none !important; }
            .hoja-papel { width: 21cm; padding: 1.6cm 2cm; box-shadow: none !important; margin: 0 !important; box-sizing: border-box; }
            @page { size: A4; margin: 0; }
        }

        * { box-sizing: border-box; }

        /* --- Encabezado: logo a la izquierda, Causa/Fecha como parte del propio encabezado --- */
        .encabezado { margin-bottom: 22px; }
        .encabezado-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 14px;
        }
        .encabezado-marca { display: flex; align-items: center; gap: 16px; }
        .encabezado-marca img { height: 42px; }
        .encabezado-marca h1 {
            font-size: 15px;
            margin: 0 0 4px 0;
            letter-spacing: 1.2px;
            color: var(--azul);
            font-weight: 800;
            text-align: left;
        }
        .encabezado-marca p { margin: 0; color: var(--gris-texto); font-size: 12.5px; text-align: left; }
        .encabezado-marca p strong { color: #1a1a1a; }

        .encabezado-meta { display: flex; gap: 10px; flex-shrink: 0; }
        .meta-chip {
            text-align: center;
            border-radius: 8px;
            padding: 8px 16px;
            min-width: 110px;
            background: #eef1fa;
            border: 1px solid #dde2f3;
        }
        .meta-chip.meta-naranja { background: #fdf3e2; border-color: #f3ddac; }
        .meta-chip .etiqueta-clave {
            display: block;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--gris-texto);
            margin-bottom: 3px;
        }
        .meta-chip .valor-clave {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--azul);
            line-height: 1.25;
        }
        .meta-chip.meta-naranja .valor-clave { color: #8a5c00; }

        .barra-colores {
            height: 5px;
            border-radius: 3px;
            background: linear-gradient(90deg, var(--azul) 0%, var(--azul) 65%, var(--naranja) 65%, var(--naranja) 100%);
        }

        /* --- Avisos de estado --- */
        .aviso {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            border-radius: 8px;
            padding: 11px 16px;
            font-size: 12px;
            margin-bottom: 22px;
            line-height: 1.5;
        }
        .aviso i { font-size: 15px; margin-top: 1px; }
        .aviso-historico { background: #fdf6e6; border: 1px solid #f2d38a; color: #8a6a00; }
        .aviso-vigente { background: #eaf7ee; border: 1px solid #a3dab2; color: #1e6b34; }

        /* --- Bloques de contenido --- */
        .bloque {
            margin-bottom: 20px;
            background: #fafbfd;
            border: 1px solid var(--gris-linea);
            border-radius: 10px;
            padding: 16px 20px;
        }
        .bloque h2 {
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--azul);
            font-weight: 800;
            margin: 0 0 12px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--gris-linea);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .bloque h2 i { color: var(--naranja); font-size: 12px; }

        table.datos { width: 100%; border-collapse: collapse; font-size: 13px; }
        table.datos tr:not(:last-child) td { border-bottom: 1px solid var(--gris-linea); }
        table.datos td { padding: 7px 10px 7px 0; vertical-align: top; color: #24272e; }
        table.datos td.etiqueta { font-weight: 700; color: var(--gris-texto); width: 220px; }

        .observacion-box {
            background: #ffffff;
            border: 1px dashed var(--gris-linea);
            border-radius: 6px;
            padding: 12px 15px;
            font-size: 12.5px;
            color: #333;
            margin-top: 12px;
        }

        .pie {
            margin-top: 40px;
            padding-top: 12px;
            border-top: 1px solid var(--gris-linea);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10.5px;
            color: #9aa0ac;
        }
        .pie img { height: 16px; opacity: .6; }
    </style>
</head>
<body>

    <div class="barra-herramientas">
        <div style="color: #444; font-size: 13.5px;">
            <i class="fas fa-file-contract text-primary me-2"></i>
            Vista imprimible del contrato — evento del {{ $modificacion->created_at->format('d/m/Y H:i') }}
        </div>
        <button class="btn-print" onclick="window.print()">
            <i class="fas fa-print me-2"></i> Imprimir / Guardar como PDF
        </button>
    </div>

    <div class="hoja-papel">
        @php
            $snapshot = $modificacion->snapshot;
            $esElMasReciente = $contrato->modificaciones->first()?->id === $modificacion->id;
            $logo = asset('assets/images/logo/CORPENTUNIDA_LOGO_PRINCIPAL.png');
            $datos = $snapshot ?: [
                'tipo_contrato' => $contrato->tipoContrato->nombre,
                'cargo' => $contrato->cargo->nombre,
                'area' => $contrato->cargo->area->nombre ?? null,
                'fecha_creacion_contrato' => optional($contrato->fecha_creacion_contrato)->format('Y-m-d'),
                'fecha_inicio' => optional($contrato->fecha_inicio)->format('Y-m-d'),
                'fecha_vencimiento' => optional($contrato->fecha_vencimiento)->format('Y-m-d'),
                'estado' => $contrato->estado,
                'salario_contrato' => $contrato->salario_contrato,
                'documento_url' => $contrato->documento_url,
            ];
        @endphp

        <div class="encabezado">
            <div class="encabezado-top">
                <div class="encabezado-marca">
                    <img src="{{ $logo }}" alt="Corpentunida">
                    <div>
                        <h1>CONDICIONES CONTRACTUALES</h1>
                        <p><strong>{{ $snapshot['empleado_nombre'] ?? $contrato->empleado->nombre_completo }}</strong>
                            @if (!empty($snapshot['empleado_documento']))
                                — C.C. {{ $snapshot['empleado_documento'] }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="encabezado-meta">
                    <div class="meta-chip">
                        <span class="etiqueta-clave">Fecha del evento</span>
                        <span class="valor-clave">{{ $modificacion->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="meta-chip meta-naranja">
                        <span class="etiqueta-clave">Causa</span>
                        <span class="valor-clave">
                            {{ $modificacion->causal === 'Otra' && $modificacion->observacion ? $modificacion->observacion : $modificacion->causal }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="barra-colores"></div>
        </div>

        @if ($esElMasReciente)
            <div class="aviso aviso-vigente">
                <i class="fas fa-check-circle"></i>
                <div>Este es el estado <strong>vigente</strong> del contrato — corresponde al evento más reciente de su historial.</div>
            </div>
        @else
            <div class="aviso aviso-historico">
                <i class="fas fa-history"></i>
                <div>
                    Este documento muestra las condiciones del contrato tal como quedaron vigentes a partir del
                    {{ $modificacion->created_at->format('d/m/Y') }}, no el estado actual — puede haber sido modificado
                    posteriormente. Consulta el historial completo para ver la versión vigente.
                </div>
            </div>
        @endif

        @if (empty($snapshot))
            <div class="aviso aviso-historico">
                <i class="fas fa-triangle-exclamation"></i>
                <div>No hay una foto guardada para este evento en particular — se muestran los datos actuales del contrato como mejor referencia disponible.</div>
            </div>
        @endif

        <div class="bloque">
            <h2><i class="fas fa-file-lines"></i> Datos del contrato</h2>
            <table class="datos">
                <tr>
                    <td class="etiqueta">Tipo de contrato</td>
                    <td>{{ $datos['tipo_contrato'] ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="etiqueta">Cargo</td>
                    <td>{{ $datos['cargo'] ?? '—' }}{{ !empty($datos['area']) ? " ({$datos['area']})" : '' }}</td>
                </tr>
                <tr>
                    <td class="etiqueta">Fecha de creación del contrato</td>
                    <td>{{ !empty($datos['fecha_creacion_contrato']) ? \Illuminate\Support\Carbon::parse($datos['fecha_creacion_contrato'])->format('d/m/Y') : '—' }}</td>
                </tr>
                <tr>
                    <td class="etiqueta">Fecha de inicio</td>
                    <td>{{ !empty($datos['fecha_inicio']) ? \Illuminate\Support\Carbon::parse($datos['fecha_inicio'])->format('d/m/Y') : 'Sin definir' }}</td>
                </tr>
                <tr>
                    <td class="etiqueta">Fecha de vencimiento</td>
                    <td>{{ !empty($datos['fecha_vencimiento']) ? \Illuminate\Support\Carbon::parse($datos['fecha_vencimiento'])->format('d/m/Y') : 'Indefinido' }}</td>
                </tr>
                <tr>
                    <td class="etiqueta">Salario</td>
                    <td>{{ isset($datos['salario_contrato']) ? '$' . number_format((float) $datos['salario_contrato'], 0, ',', '.') : '—' }}</td>
                </tr>
                <tr>
                    <td class="etiqueta">Estado</td>
                    <td>{{ $datos['estado'] ?? '—' }}</td>
                </tr>
                @if (!empty($datos['documento_url']))
                    <tr>
                        <td class="etiqueta">Documento firmado</td>
                        <td><a href="{{ $datos['documento_url'] }}" target="_blank" rel="noopener">{{ $datos['documento_url'] }}</a></td>
                    </tr>
                @endif
            </table>
        </div>

        <div class="bloque">
            <h2><i class="fas fa-clock-rotate-left"></i> Evento de este registro</h2>
            <table class="datos">
                <tr>
                    <td class="etiqueta">Tipo de evento</td>
                    <td>{{ $modificacion->causal }}</td>
                </tr>
                <tr>
                    <td class="etiqueta">Fecha</td>
                    <td>{{ $modificacion->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td class="etiqueta">Registrado por</td>
                    <td>{{ $modificacion->usuario->name ?? '—' }}</td>
                </tr>
            </table>
            @if ($modificacion->observacion)
                <div class="observacion-box">{{ $modificacion->observacion }}</div>
            @endif
        </div>

        <div class="pie">
            <span>Documento generado el {{ now()->format('d/m/Y H:i') }} — Gestión Humana (SGRH)</span>
            <img src="{{ $logo }}" alt="Corpentunida">
        </div>
    </div>

</body>
</html>
