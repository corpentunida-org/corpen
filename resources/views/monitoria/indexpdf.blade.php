<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Monitoria</title>
</head>

<body>
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-around;
        }

        .header h2 {
            width: 50%;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .separador {
            height: 20px;
        }
    </style>


    <div class="header">
        <img src="{{ asset('assets/img/corpentunida-logo-azul-oscuro-2021x300.png') }}" alt="logo">
        <h2> </h2>
    </div>
    <div class="separador"></div>
    <table>
        <thead>
            <tr>
                <th colspan="14" style="text-align: center; background-color: rgba(0, 128, 0, 0.5);">INFORME FALLECIDOS</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>N°</th>
                <th>Fecha Fall.</th>
                <th>Dia</th>
                <th>Hora</th>
                <th>Cedula Titular</th>
                <th>Nombre Titular</th>
                <th>Nombre Fallecido</th>
                <th>Lugar Fallecimiento</th>
                <th>Parentesco</th>
                <th>Traslado</th>
                <th>Contacto 1</th>
                <th>Telefono</th>
                <th>Factura</th>
                <th>Valor</th>
            </tr>
            @foreach ($registros as $reg)
            <tr>
                <td>{{$reg->id}}</td>
                <td>{{ $reg->fechaFallecimiento }}</td>
                <td>
                    @php
                    Carbon\Carbon::setLocale('es');
                    $dia = Carbon\Carbon::parse($reg->fechaFallecimiento)->translatedFormat('l');
                    @endphp
                    {{ $dia }}
                </td>
                <td>{{ $reg->horaFallecimiento }}</td>
                <td>{{ $reg->cedulaTitular }}</td>
                <td>{{ $reg->asociado->apellido . " " . $reg->asociado->nombre}}</td>
                <td>{{ $reg->beneficiario->nombre }}</td>
                <td>{{ $reg->lugarFallecimiento }}</td>
                <td>{{ $reg->parentescoo->nomPar}}</td>
                @if( $reg->traslado)
                <td>SI</td>
                @else
                <td>NO</td>
                @endif
                <td>{{ $reg->contacto }}</td>
                <td>{{ $reg->telefonoContacto }}</td>
                <td>{{ $reg->factura }}</td>
                <td>{{ $reg->valor }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="separador"></div>

</body>

</html>