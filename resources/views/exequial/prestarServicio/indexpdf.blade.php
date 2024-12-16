<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Prestar Servicio</title>
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
            font-size: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .separador {
            height: 20px;
        }
        .centerText{
            font-size: 7px;
            text-align: center;
        }
    </style>


    <table style="border: none; width: 100%;" >
        <tr style="border: none">
            <td style="border: none; width:50px;">
                <img src="{{ $image_path }}" alt="logoCorpen" style="width: 300px;">
                {{-- <img src="{{ asset('assets/img/corpentunida-logo-azul-oscuro-2021x300.png') }}" alt="logo"> --}}
            </td>
            <td style="border: none">
                <div style="text-align: right; font-size: 10px;">
                    <p><strong>FECHA: </strong>{{ date('Y-m-d') }}</p>
                    <p><strong>HORA: </strong>{{ date('H:i:s') }}</p>
                    <p><strong>USUARIO: </strong>{{ Auth::user()->name }}</p>
                </div>
            </td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th colspan="15" style="text-align: center; background-color: rgba(0, 128, 0, 0.5);">INFORME FALLECIDOS</th>
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
                <th>Cedula Fallecido</th>
                <th>Nombre Fallecido</th>
                <th>Lugar Fallecimiento</th>
                <th>Parentesco</th>
                <th>Traslado</th>
                <th>Contacto 1</th>
                <th>Telefono</th>
                <th>Factura</th>
                <th>Valor</th>
            </tr>
            @php
                $i = 1;
            @endphp
            @foreach ($registros as $reg)
            <tr>
                <td>{{$i}}</td>
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
                <td>{{ $reg->nombreTitular }}</td>
                <td>{{ $reg->cedulaFallecido }}</td>
                <td>{{ $reg->nombreFallecido }}</td>
                <td>{{ $reg->lugarFallecimiento }}</td>
                <td>{{ $reg->parentesco }}</td>
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
            @php
                $i++;
            @endphp
            @endforeach
        </tbody>
    </table>
    <p class="centerText"><strong>app.corpentunida.org.co</strong> todos los derechos reservados.</p>
    <div class="separador"></div>
</body>
</html>