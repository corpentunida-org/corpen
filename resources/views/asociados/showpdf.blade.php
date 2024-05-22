<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <style>
        *{
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
        .separador{
            height: 20px;
        }
    </style>


    <div class="header">
        <img src="{{ asset('assets/img/corpentunida-logo-azul-oscuro-2021x300.png') }}" alt="logo">
        <h2>Solicitud de afiliacion a prevención exequial</h2>
    </div>
    <div class="separador"></div>
    <table>
        <thead>
            <tr>
                <th colspan="5" style="text-align: center; background-color: rgba(0, 128, 0, 0.5);">INFORMACION DEL
                    CONTRATANTE</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>CUIDAD</th>
                <th>DEPARTAMENTO</th>
                <th>CONGREGACIÓN</th>
                <th>DISTRITO</th>
                <th>FECHA AFILIACIÓN</th>
            </tr>
            <tr>
                @if ($asociado->ciudad_id !== null && $asociado->ciudad_id !== 0)
                    <td>{{ $asociado->ciudade->nombre }}</td>
                @else
                    <td>{{ $asociado->ciudade }}</td>
                @endif

                <td> </td>
                <td> </td>

                @if ($asociado->distrito !== null && $asociado->distrito !== 0)
                    <td>{{ $asociado->distrito->id }}</td>
                @else
                    <td>{{ $asociado->distrito }}</td>
                @endif

                <td> </td>

            </tr>
            <tr>
                <th colspan="3">APELLIDOS Y NOMBRES</th>
                <th>DOCUMENTO DE IDENTIDAD</th>
                <th>FECHA DE NACIMIENTO</th>
            </tr>
            <tr>
                <td colspan="3">{{ $asociado->apellido . ' ' . $asociado->nombre }}</td>
                <td>{{ $asociado->cedula }}</td>
                <td>{{ $asociado->fechaNacimiento }}</td>
            </tr>
            <tr>
                <th>EDAD</th>
                <th>TELEFONO</th>
                <th>CELULAR</th>
                <th>CORREO ELECTRONICO</th>
            </tr>
            <tr>
                @php
                    $fecNac = new DateTime($asociado->fechaNacimiento);
                    $fechaActual = new DateTime();
                    $diferencia = $fecNac->diff($fechaActual);
                    $edad = $diferencia->y;
                @endphp                
                <td>{{ $edad }}</td>
                <td>{{ $asociado->celular }}</td>
                <td> </td>
                <td> </td>
            </tr>
        </tbody>
    </table>
    <div class="separador"></div>
    <table>
        <thead>
            <tr>
                <th colspan="5" style="text-align: center; background-color: rgba(0, 128, 0, 0.5);">INFORMACIÓN BENEFICIARIOS</th>
            </tr>
            <tr>
                <th>CEDULA</th>
                <th>APELLIDOS NOMBRES</th>
                <th>PARENTESCO</th>
                <th>FECHA NACIMIENTO</th>
                <th>EDAD</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($beneficiarios as $beneficiario)
                <tr>
                    <td>{{ $beneficiario->cedula }}</td>
                    <td>{{ $beneficiario->apellido . $beneficiario->nombre }}</td>
                    {{-- @if ($beneficiario->parentesco !== null && $beneficiario->parentesco !== 0)
                        <td>{{ $beneficiario->parentescoo->nomPar }}</td>
                        @else
                        <td>{{ $beneficiario->parentesco }}</td>
                        @endif --}}
                    @if ($beneficiario->parentescoo !== null)
                        <td>{{ $beneficiario->parentescoo->nomPar }}</td>
                    @else
                        <td>{{ $beneficiario->parentesco }}</td>
                    @endif
                    <td>{{ $beneficiario->fechaNacimiento }}</td>

                    @php
                        $fecNac = new DateTime($beneficiario->fechaNacimiento);
                        $fechaActual = new DateTime();
                        $diferencia = $fecNac->diff($fechaActual);
                        $edad = $diferencia->y;
                    @endphp

                    <td>{{ $edad }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
