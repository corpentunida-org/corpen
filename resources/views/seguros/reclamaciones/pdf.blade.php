<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Reclamaciones</title>
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
                <th colspan="11" style="text-align: center;">INFORME RECLAMACIONES</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>N°</th>
                <th>Asegurado Cédula</th>
                <th>Asegurado</th>
                <th>Edad</th>
                <th>Titular Cédula</th>
                <th>Titular</th>
                <th>Parentesco</th>
                <th>Cobertura</th>
                <th>Valor Asegurado</th>
                <th>Fecha Actualización</th>
                <th>Estado</th>
            </tr>
            @php
                $i = 1;
            @endphp
            @foreach ($registros as $r)
                <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $r->cedulaAsegurado ?? '' }}</td>
                    <td>{{ $r->asegurado->tercero->nombre ?? ''}}</td>
                    <td>
                    @php                        
                        $edad = \Carbon\Carbon::parse($r->asegurado->tercero->fechaNacimiento)->age;
                    @endphp
                    {{$edad}}
                    </td>
                    <td>{{ $r->asegurado->terceroAF->cedula ?? ''}}</td>
                    <td>{{ $r->asegurado->terceroAF->nombre ?? ''}}</td>
                    <td>{{ $r->asegurado->parentesco ?? ''}}</td>
                    <td>{{ $r->cobertura->nombre }}</td>
                    <td>$ {{ number_format($r->valor_asegurado) }} </td>
                    <td>{{ $r->updated_at }}</td>
                    <td>{{ $r->estadoReclamacion->nombre }}</td>
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
