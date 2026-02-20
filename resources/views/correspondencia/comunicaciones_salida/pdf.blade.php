<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 4cm 2.5cm 3cm 2.5cm; 
        }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            color: #1a1a1a;
            line-height: 1.5;
            font-size: 13px;
            margin: 0;
            padding: 0;
        }

        /* --- FONDO --- */
        #fondo-plantilla {
            position: fixed;
            top: -4cm;
            left: -2.5cm;
            width: 21cm;
            height: 29.7cm;
            z-index: -2000;
        }
        #fondo-plantilla img { width: 100%; height: 100%; }

        /* --- BLOQUES DE CONTENIDO --- */
        .header-content { margin-bottom: 30px; }
        .fecha { font-weight: bold; margin-bottom: 25px; }
        .destinatario { margin-bottom: 20px; }
        .asunto { font-weight: bold; text-transform: uppercase; margin-bottom: 30px; }

        /* Contenedor flexible para el cuerpo para evitar que la firma quede muy arriba */
        .cuerpo-carta {
            text-align: justify;
            min-height: 350px; 
            margin-bottom: 20px;
        }

        /* --- SECCIÓN DE FIRMA --- */
        .contenedor-firma {
            page-break-inside: avoid; 
            width: 300px;
            margin-top: 10px;
        }

        .espacio-rubrica {
            height: 65px; 
            position: relative;
        }

        .img-firma {
            max-height: 75px; /* Un poco más alta para mayor visibilidad */
            position: absolute;
            bottom: 5px; /* Pisa ligeramente la línea para verse natural */
            left: 0;
        }

        .linea {
            border-top: 1px solid #000;
            width: 100%;
            margin-bottom: 5px;
        }

        .nombre-firmante {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            display: block;
        }

        .cargo-firmante {
            font-size: 11px;
            color: #444;
        }

        .marca-agua {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 50px;
            color: rgba(200, 200, 200, 0.08);
            z-index: -1000;
        }
    </style>
</head>
<body>

    @if(isset($fondoImg))
    <div id="fondo-plantilla">
        <img src="{{ $fondoImg }}">
    </div>
    @endif

    <div class="marca-agua">{{ strtoupper($comunicacionSalida->estado_envio) }}</div>

    <main>
        <div class="header-content">
            <div class="fecha">
                Bogotá D.C., {{ \Carbon\Carbon::parse($comunicacionSalida->fecha_generacion)->translatedFormat('d \d\e F \d\e Y') }}
            </div>

            <div class="destinatario">
                Asociado(a):<br>
                <strong>{{ $comunicacionSalida->correspondencia->remitente->nom_ter ?? 'NOMBRE DEL ASOCIADO' }}</strong><br>
                {{ $comunicacionSalida->correspondencia->remitente->distrito ?? 'Distrito / Ciudad' }}
            </div>

            <div class="asunto">
                Asunto: {{ $comunicacionSalida->correspondencia->asunto ?? 'SOLICITUD' }}
            </div>
        </div>

        <div class="cuerpo-carta">
            <p>Cordial saludo en el nombre del Señor Jesucristo para usted y los suyos.</p>
            
            <div style="margin-top: 15px;">
                {!! nl2br(e($comunicacionSalida->cuerpo_carta)) !!}
            </div>
        </div>

        <div class="contenedor-firma">
            <p style="margin-bottom: 5px;">Cordialmente,</p>
            
            <div class="espacio-rubrica">
                {{-- Priorizamos la imagen Base64 procesada por el controlador --}}
                @if(isset($firmaImg) && $firmaImg)
                    <img src="{{ $firmaImg }}" class="img-firma">
                @endif
            </div>

            <div class="linea"></div>
            <span class="nombre-firmante">{{ $comunicacionSalida->usuario->name ?? 'BRAYAM STIVED CASTILLO MORENO' }}</span>
            <span class="cargo-firmante">Secretario Corpentunida</span>
        </div>
    </main>
</body>
</html>