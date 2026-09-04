<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Historial de contratos — {{ $empleado->nombre_completo }}</title>
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
            .bloque { page-break-inside: avoid; }
            @page { size: A4; margin: 0; }
        }

        * { box-sizing: border-box; }

        /* --- Encabezado: logo a la izquierda, con chips a la derecha --- */
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
            min-width: 90px;
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
        .bloque.bloque-contrato { background: var(--azul); border: none; }
        .bloque.bloque-contrato h2 { color: #fff; border-bottom-color: rgba(255,255,255,.25); }
        .bloque.bloque-contrato .subtitulo { color: rgba(255,255,255,.8); font-size: 12px; margin: 0; }
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
            justify-content: space-between;
            gap: 8px;
        }
        .bloque h2 i { color: var(--naranja); font-size: 12px; }
        .bloque h2 .evento-fecha { font-size: 10.5px; font-weight: 600; color: var(--gris-texto); text-transform: none; letter-spacing: 0; }

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

        .lista-diferencias { list-style: none; margin: 0; padding: 0; }
        .lista-diferencias li {
            background: #ffffff;
            border-left: 3px solid var(--naranja);
            border-radius: 0 6px 6px 0;
            padding: 8px 12px;
            font-size: 12.5px;
            color: #333;
            margin-bottom: 8px;
        }
        .lista-diferencias li:last-child { margin-bottom: 0; }

        .pie {
            margin-top: 30px;
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
            Historial completo de contratos — {{ $empleado->nombre_completo }}
        </div>
        <button class="btn-print" onclick="window.print()">
            <i class="fas fa-print me-2"></i> Imprimir / Guardar como PDF
        </button>
    </div>

    <div class="hoja-papel">
        @php
            $logo = asset('assets/images/logo/CORPENTUNIDA_LOGO_PRINCIPAL.png');
            $totalEventos = $historial->sum(fn ($b) => $b['eventos']->count());
        @endphp

        <div class="encabezado">
            <div class="encabezado-top">
                <div class="encabezado-marca">
                    <img src="{{ $logo }}" alt="Corpentunida">
                    <div>
                        <h1>HISTORIAL DE CONTRATOS</h1>
                        <p><strong>{{ $empleado->nombre_completo }}</strong>
                            @if ($empleado->tercero)
                                — C.C. {{ $empleado->tercero->cod_ter }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="encabezado-meta">
                    <div class="meta-chip">
                        <span class="etiqueta-clave">Contratos</span>
                        <span class="valor-clave">{{ $historial->count() }}</span>
                    </div>
                    <div class="meta-chip meta-naranja">
                        <span class="etiqueta-clave">Eventos</span>
                        <span class="valor-clave">{{ $totalEventos }}</span>
                    </div>
                </div>
            </div>
            <div class="barra-colores"></div>
        </div>

        @forelse ($historial as $bloqueContrato)
            @php $contrato = $bloqueContrato['contrato']; @endphp

            <div class="bloque bloque-contrato">
                <h2><i class="fas fa-file-lines" style="color:#fff"></i> {{ $contrato->tipoContrato->nombre }}</h2>
                <p class="subtitulo">
                    {{ $contrato->fecha_inicio?->format('d/m/Y') ?? 'Sin fecha de inicio' }} — {{ $contrato->estado }}
                    @if ($contrato->cargo) · {{ $contrato->cargo->nombre }} @endif
                </p>
            </div>

            @forelse ($bloqueContrato['eventos'] as $item)
                @php
                    $evento = $item['modificacion'];
                    $snap = $evento->snapshot;
                    $esElMasReciente = $contrato->modificaciones->first()?->id === $evento->id;
                @endphp

                <div class="bloque">
                    <h2>
                        <span><i class="fas fa-clock-rotate-left"></i> {{ $evento->causal }}</span>
                        <span class="evento-fecha">{{ $evento->created_at->format('d/m/Y H:i') }}</span>
                    </h2>

                    @if ($esElMasReciente)
                        <div class="aviso aviso-vigente" style="margin-bottom: 14px;">
                            <i class="fas fa-check-circle"></i>
                            <div>Este es el estado <strong>vigente</strong> del contrato.</div>
                        </div>
                    @endif

                    @if ($evento->causal === 'Creación' && $snap)
                        <table class="datos">
                            <tr><td class="etiqueta">Tipo de contrato</td><td>{{ $snap['tipo_contrato'] ?? '—' }}</td></tr>
                            <tr><td class="etiqueta">Cargo</td><td>{{ $snap['cargo'] ?? '—' }}</td></tr>
                            <tr><td class="etiqueta">Salario</td><td>{{ isset($snap['salario_contrato']) ? '$' . number_format((float) $snap['salario_contrato'], 0, ',', '.') : '—' }}</td></tr>
                            <tr><td class="etiqueta">Fecha de vencimiento</td><td>{{ !empty($snap['fecha_vencimiento']) ? \Illuminate\Support\Carbon::parse($snap['fecha_vencimiento'])->format('d/m/Y') : 'Indefinido' }}</td></tr>
                        </table>
                    @elseif (!empty($item['diferencias']))
                        <ul class="lista-diferencias">
                            @foreach ($item['diferencias'] as $diferencia)
                                <li>{{ $diferencia }}</li>
                            @endforeach
                        </ul>
                    @elseif (!$snap)
                        <div class="aviso aviso-historico" style="margin-bottom: 0;">
                            <i class="fas fa-triangle-exclamation"></i>
                            <div>No hay una foto guardada para este evento en particular — se muestran los datos actuales del contrato como mejor referencia disponible.</div>
                        </div>
                    @endif

                    @if ($evento->observacion)
                        <div class="observacion-box">{{ $evento->observacion }}</div>
                    @endif
                </div>
            @empty
                <div class="bloque text-muted small">Este contrato no tiene eventos registrados.</div>
            @endforelse
        @empty
            <div class="bloque text-muted small">Este colaborador no tiene contratos registrados.</div>
        @endforelse

        <div class="pie">
            <span>Documento generado el {{ now()->format('d/m/Y H:i') }} — Gestión Humana (SGRH)</span>
            <img src="{{ $logo }}" alt="Corpentunida">
        </div>
    </div>

</body>
</html>
