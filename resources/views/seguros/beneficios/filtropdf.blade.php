<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pólizas</title>
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
            padding: 2px;
            text-align: left;
            font-size: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .separador {
            height: 20px;
        }

        .centerText {
            font-size: 7px;
            text-align: center;
        }
    </style>


    <table style="border: none; width: 100%;">
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
                <th colspan="7" style="text-align: center;">PÓLIZAS</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>N°</th>
                <th>Cedula Asegurado</th>
                <th>Nombre Asegurado</th>
                <th>Parentesco</th>
                <th>Edad</th>
                <th>Plan</th>
                <th>Valor a Pagar</th>
            </tr>
            @php
                $i = 1;
            @endphp
            @foreach ($registros as $r)
                @php
                    $fechaNacimiento = $r['tercero']['fechaNacimiento'] ?? null;
                    $edad = $fechaNacimiento ? \Carbon\Carbon::parse($fechaNacimiento)->age : ' ';
                @endphp
                <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $r['seg_asegurado_id'] ?? '' }}</td>
                    <td>{{ $r['tercero']['nombre'] ?? '' }}</td>
                    <td>{{ $r['asegurado']['parentesco'] ?? '' }}</td>
                    <td>{{ $edad }}</td>
                    <td>$ {{ is_numeric($r['valor_asegurado']) ? number_format((float) $r['valor_asegurado']) : '' }} </td>
                    <td>$ {{ is_numeric($r['primapagar']) ? number_format((float) $r['primapagar']) : '' }} </td>
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