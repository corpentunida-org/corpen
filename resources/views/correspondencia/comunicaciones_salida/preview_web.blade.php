<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Oficio de Salida - {{ $comunicacionSalida->nro_oficio_salida }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
    
    <style>
        /* --- ESTILOS PARA LA PANTALLA (MODO EDITOR) --- */
        @media screen {
            body {
                background-color: #f0f2f5;
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
                box-shadow: 0 4px 12px rgba(0,0,0,0.05);
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .btn-print {
                background-color: #4a90e2;
                color: white;
                border: none;
                padding: 12px 25px;
                border-radius: 30px;
                font-weight: bold;
                cursor: pointer;
                transition: all 0.3s ease;
                font-size: 14px;
            }

            .btn-print:hover {
                background-color: #357abd;
                transform: translateY(-1px);
                box-shadow: 0 5px 15px rgba(74, 144, 226, 0.4);
            }

            .hoja-papel {
                width: 21cm;
                min-height: 29.7cm;
                background: #ffffff;
                margin: 0 auto;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
                position: relative;
                padding: 4cm 2.5cm 3cm 2.5cm;
                box-sizing: border-box;
            }
        }

        /* --- ESTILOS PARA LA IMPRESIÓN / GENERACIÓN DE PDF NATIVO --- */
        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
                font-family: 'Helvetica', Arial, sans-serif;
            }
            .barra-herramientas {
                display: none !important;
            }
            .hoja-papel {
                width: 21cm;
                height: 29.7cm;
                padding: 4cm 2.5cm 3cm 2.5cm;
                box-shadow: none !important;
                margin: 0 !important;
                box-sizing: border-box;
            }
            @page {
                size: A4;
                margin: 0;
            }
        }

        /* --- CAPAS DE CAPTURA VISUAL --- */
        #fondo-plantilla {
            position: absolute;
            top: 0;
            left: 0;
            width: 21cm;
            height: 29.7cm;
            z-index: 1;
            pointer-events: none; /* Crucial para poder hacer click en los inputs */
        }
        #fondo-plantilla img { width: 100%; height: 100%; }

        .marca-agua {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 50px;
            color: rgba(200, 200, 200, 0.12);
            z-index: 2;
            pointer-events: none;
            font-weight: bold;
            white-space: nowrap;
        }
        
        .contenido-dinamico {
            position: relative;
            z-index: 10; /* Por encima del fondo y de la marca de agua */
        }

        /* --- BLOQUES DE CONTENIDO --- */
        .header-content { margin-bottom: 30px; }
        .fecha { font-weight: bold; margin-bottom: 25px; }
        .destinatario-block { margin-bottom: 20px; }
        .asunto { font-weight: bold; text-transform: uppercase; margin-bottom: 30px; }
        .cuerpo-carta { text-align: justify; min-height: 350px; margin-bottom: 20px; }

        /* --- CONFIGURACIÓN DE EDICIÓN FLUIDA --- */
        .input-editable {
            border: 1px dashed transparent;
            background: transparent;
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 13px;
            color: #1a1a1a;
            width: 100%;
            padding: 2px 5px;
            margin-left: -5px;
            transition: all 0.2s ease;
        }

        @media screen {
            .input-editable:hover {
                border-color: #4a90e2;
                background-color: #f4f8ff;
                border-radius: 4px;
            }
            .input-editable:focus {
                border-color: #4a90e2;
                background-color: #ffffff;
                box-shadow: 0 0 5px rgba(74,144,226,0.3);
                outline: none;
                border-radius: 4px;
            }
        }

        .font-bold { font-weight: bold; }
        .text-uppercase { text-transform: uppercase; }

        /* --- SECCIÓN DE FIRMA --- */
        .contenedor-firma {
            width: 300px;
            margin-top: 10px;
        }
        .espacio-rubrica { height: 65px; position: relative; }
        .img-firma { max-height: 75px; position: absolute; bottom: 5px; left: 0; }
        .linea { border-top: 1px solid #000; width: 100%; margin-bottom: 5px; }
        .nombre-firmante { font-weight: bold; text-transform: uppercase; font-size: 12px; display: block; }
        .cargo-firmante { font-size: 11px; color: #444; }
    </style>
</head>
<body>

    <div class="barra-herramientas">
        <div style="color: #444; font-size: 13.5px;">
            <i class="fas fa-edit text-primary me-2"></i>Puede modificar el <strong>Destinatario</strong> o el <strong>Asunto</strong> haciendo clic sobre ellos.
        </div>
        <button class="btn-print" onclick="window.print()">
            <i class="fas fa-print me-2"></i> Imprimir / Guardar como PDF
        </button>
    </div>

    <div class="hoja-papel">
        
        @if(isset($fondoImg) && $fondoImg)
        <div id="fondo-plantilla">
            <img src="{{ $fondoImg }}">
        </div>
        @endif

        <div class="marca-agua">{{ strtoupper($comunicacionSalida->estado_envio) }}</div>

        <div class="contenido-dinamico">
            <div class="header-content">
                <br><br><br>
                <div class="fecha">
                    Bogotá D.C., {{ \Carbon\Carbon::parse($comunicacionSalida->fecha_generacion)->translatedFormat('d \d\e F \d\e Y') }}
                </div>

                <div class="destinatario-block">
                    <input type="text" class="input-editable" value="Pastor:">
                    <input type="text" class="input-editable font-bold" value="{{ $comunicacionSalida->correspondencia->remitente->nom_ter ?? 'Araujo Hurtado Hernan Enrique' }}">
                    @php
                        $distritoRaw = $comunicacionSalida->correspondencia->remitente->distrito;
                    @endphp
                    <input type="text" class="input-editable" value="Asociado">
                </div>
                
                <br>
                
                <div class="asunto">
                    <table style="width: 100%; border-collapse: collapse; margin: 0; padding: 0;">
                        <tr>
                            <td style="width: 55px; font-weight: bold; vertical-align: middle; white-space: nowrap;">Asunto:</td>
                            <td style="padding-left: 5px; vertical-align: middle;">
                                <input type="text" class="input-editable font-bold text-uppercase" value="{{ $comunicacionSalida->correspondencia->asunto ?? 'SOLICITUD' }}">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="cuerpo-carta">
                <p>El Señor Jesucristo le bendiga abundantemente.</p>
                <div style="margin-top: 15px;">
                    {!! nl2br(e($comunicacionSalida->cuerpo_carta)) !!}
                </div>
                {{-- <br> --}}
                <p>Deseamos que las bendiciones del Señor le acompañe y direccionen en todos sus asuntos.</p>
                <p style="margin-bottom: 5px;">Con amor en Cristo, siempre su servidor,</p>
            </div>

            <div class="contenedor-firma">
                <div class="espacio-rubrica">
                    @if(isset($firmaImg) && $firmaImg)
                        <img src="{{ $firmaImg }}" class="img-firma">
                    @endif
                </div>

                <div class="linea"></div>
                <span class="nombre-firmante">{{ $comunicacionSalida->usuario->name ?? 'BRAYAM STIVED CASTILLO MORENO' }}</span>
                <span class="cargo-firmante">Secretario Corpentunida</span>
            </div>
        </div>
    </div>

</body>
</html>