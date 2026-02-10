<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $comunicacionSalida->nro_oficio_salida }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; line-height: 1.6; color: #333; margin: 40px; }
        .header { text-align: center; margin-bottom: 50px; }
        .nro-oficio { font-weight: bold; text-align: right; margin-bottom: 30px; }
        .cuerpo { text-align: justify; margin-bottom: 50px; }
        .firma { margin-top: 100px; border-top: 1px solid #000; width: 250px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ENTIDAD DE PRUEBA S.A.</h2>
        <p>Sistema de Gestión de Correspondencia</p>
    </div>

    <div class="nro-oficio">
        OFICIO NRO: {{ $comunicacionSalida->nro_oficio_salida }}<br>
        Fecha: {{ $comunicacionSalida->fecha_generacion->format('d/m/Y') }}
    </div>

    <div class="cuerpo">
        {!! nl2br($comunicacionSalida->cuerpo_carta) !!}
    </div>

    <div class="firma">
        <strong>{{ $comunicacionSalida->usuario->name }}</strong><br>
        Responsable de Correspondencia
    </div>
</body>
</html>