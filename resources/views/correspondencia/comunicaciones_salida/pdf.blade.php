<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Oficio Salida - {{ $comunicacionSalida->nro_oficio_salida }}</title>
    <style>
        @page {
            /* Márgenes basados en el documento de Corpentunida */
            margin: 3.5cm 2.5cm 4cm 2.5cm; 
        }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            color: #222;
            line-height: 1.5;
            font-size: 14px;
        }

        /* --- ENCABEZADO --- */
        header {
            position: fixed;
            top: -2.5cm;
            left: 0;
            right: 0;
            height: 2cm;
        }

        .logo-text {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            letter-spacing: 1px;
            text-transform: lowercase; /* Para que diga "corpentunida" */
        }

        /* --- PIE DE PÁGINA --- */
        footer {
            position: fixed;
            bottom: -3cm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
        }

        /* --- ESTRUCTURA DEL DOCUMENTO --- */
        .fecha {
            margin-bottom: 30px;
        }

        .destinatario {
            margin-bottom: 25px;
            line-height: 1.3;
        }

        .asunto {
            font-weight: bold;
            margin-bottom: 25px;
        }

        .cuerpo {
            text-align: justify;
            margin-bottom: 40px;
        }

        /* --- FIRMA --- */
        .firma-container {
            margin-top: 50px;
            page-break-inside: avoid;
        }

        .firma-despedida {
            margin-bottom: 40px;
        }

        .firma-nombre {
            font-weight: bold;
            text-transform: uppercase;
        }

        .marca-agua {
            position: fixed;
            top: 40%;
            left: 15%;
            font-size: 70px;
            color: rgba(200, 200, 200, 0.15);
            transform: rotate(-45deg);
            z-index: -1000;
            white-space: nowrap;
            letter-spacing: 5px;
        }
    </style>
</head>
<body>

    <header>
        <div class="logo-text">corpentunida</div>
    </header>

    <footer>
        <strong>NIT. 860.509.451-5</strong><br>
        <strong>Asociación Gremial de Ministros</strong><br>
        <strong>de la Iglesia Pentecostal Unida de Colombia</strong><br>
        Tv 29 38 22 La soledad / Bogotá / Colombia<br>
        www.corpentunida.org.co / www.librerialuzyverdad.co<br>
        PBX 60 1 208 71 71
    </footer>

    <div class="marca-agua">
        {{ strtoupper($comunicacionSalida->estado_envio) }}
    </div>

    <main>
        
        <div class="fecha">
            Bogotá, {{ $comunicacionSalida->fecha_generacion ? $comunicacionSalida->fecha_generacion->format('d/n/Y') : now()->format('d/n/Y') }}
        </div>

        <div class="destinatario">
            Asociado<br>
            
            <strong>{{ $comunicacionSalida->correspondencia->remitente->nom_ter ?? 'Nombre no encontrado en base de datos' }}</strong><br>
            
            {{ $comunicacionSalida->correspondencia->remitente->distrito ?? 'Distrito / Ciudad' }}
        </div>

        <div class="asunto">
            Asunto: {{ $comunicacionSalida->correspondencia->asunto ?? 'Respuesta a solicitud' }}
        </div>

        <div class="cuerpo">
            <p>Cordial saludo en el nombre del señor Jesucristo para usted y su familia.</p>
            {!! nl2br(e($comunicacionSalida->cuerpo_carta)) !!}
        </div>

        <div class="firma-container">
            <div class="firma-despedida">Con amor en Cristo, cordialmente Junta Directiva,</div>
            
            <div class="firma-nombre">
                {{ $comunicacionSalida->usuario->name ?? 'USUARIO DEL SISTEMA' }}
            </div>
            <div>
                Secretario Corpentunida
            </div>
        </div>

    </main>
</body>
</html>