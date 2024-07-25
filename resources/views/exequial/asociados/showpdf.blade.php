<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
        }

        /* .header {
            display: flex;
            align-items: center;
            justify-content: space-around;
        } */

        /* .header h2 {
            width: 60%;
            text-align: center;
            font-size: 30px;
        } */
        .header div{
            text-align: right;
            font-size: 10px;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }

        th {
            background-color: #f2f2f2;
        }

        .separador {
            height: 20px;
        }

        .centerText{
            font-size: 10px;
            text-align: center;
        }
    </style>

    <div class="header">
        <!-- <img src="{{ asset('assets/img/corpentunida-logo-azul-oscuro-2021x300.png') }}" alt="logo"> -->
        <table style="border: none; width: 100%;" >
            <tr style="border: none">
                <td style="border: none; width:50px;"><img src="{{ $image_path }}" alt="logoCorpen" style="80%"></td>
                <td style="border: none"><h2 style="text-align: left;">CERTIFICADO DE AFILIACIÓN A PREVENCIÓN EXEQUIAL</h2></td>
                <td style="border: none">
                    <div>
                        <p><strong>FECHA: </strong>{{ date('Y-m-d') }}</p>
                        <p><strong>HORA: </strong>{{ date('H:i:s') }}</p>
                        <p><strong>USUARIO: </strong>{{ Auth::user()->name }}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th colspan="5" style="text-align: center; background-color: rgba(0, 128, 0, 0.5);">INFORMACIÓN DEL CONTRATANTE</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th colspan="2">APELLIDOS Y NOMBRES</th>
                <th>DOCUMENTO DE IDENTIDAD</th>
                <th>FECHA DE INICIO</th>
                <th>CONTRATO</th>
            </tr>
            <tr>
                <td colspan="2">{{ $asociado['name'] }}</td>
                <td>{{ $asociado['documentId'] }}</td>
                <td>{{ $asociado['dateInit'] }}</td>
                <th>{{ $asociado['agreement'] }}</th>
            </tr>
            <tr>
                <th colspan="2">PLAN</th>
                <th colspan="3">OBSERVACIONES</th>
            </tr>
            <tr>
                <td colspan="2">{{ $asociado['codePlan'] }}</td>
                <td colspan="3">{{ $asociado['observation'] }}</td>
            </tr>
            <tr>
                <th>DISTRITO</th>
                <th>FECHA DE NACIMIENTO</th>
                <th colspan="3">CORREO</th>
            </tr>
            <tr>
                <td>{{ $pastor['district'] }}</td>
                <td>{{ $pastor['birthdate'] }}</td>
                <td colspan="3">{{ $pastor['email'] }}</td>
            </tr>
        </tbody>
    </table>
    <div class="separador"></div>
    <table class="table">
        <thead>
            <tr>
                <th colspan="5" style="text-align: center; background-color: rgba(0, 128, 0, 0.5);">INFORMACIÓN
                    BENEFICIARIOS</th>
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
                            <td>{{ $beneficiario['documentId'] }}</td>
                            <td>{{ $beneficiario['names'] }}</td>
                            <td>{{ $beneficiario['relationship'] }}</td>
                            <td>{{ $beneficiario['dateBirthday'] }}</td>
                            @php
                                $fecNac = new DateTime(
                                    $beneficiario['dateBirthday'],
                                );
                                $fechaActual = new DateTime();
                                $diferencia = $fecNac->diff($fechaActual);
                                $edad = $diferencia->y;
                            @endphp
                            <td>{{ $edad }}</td>
                        </tr>
            @endforeach
        </tbody>
    </table>
    <p class="centerText"><strong>app.corpentunida.org.co</strong> todos los derechos reservados.</p>
</body>

</html>