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

        .header {
            display: flex;
            align-items: center;
            justify-content: space-around;
        }

        .header h2 {
            width: 60%;
            text-align: center;
            font-size: 30px;
        }
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
        }

        th {
            background-color: #f2f2f2;
        }

        .separador {
            height: 20px;
        }
        #noborder{
            border: 0px #f2f2f2 solid;
        }

        .centerText{
            font-size: 10px;
            text-align: center;
        }
    </style>

    <div class="header">
        <img src="{{ asset('assets/img/corpentunida-logo-azul-oscuro-2021x300.png') }}" alt="logo">
        
                        <h2>Solicitud de afiliación a prevención exequial</h2>
                    <!-- <table class="table">
                        <tr>
                            <td>FECHA:</td>
                            <td>{{ date('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <td>HORA:</td>
                            <td>{{ date('H:i:s') }}</td>
                        </tr>
                        <tr>
                            <td>USUARIO:</td>
                            <td>{{ Auth::user()->name }}</td>
                        </tr>
                    </table> -->
                    <div>
                    <p><strong>FECHA: </strong>{{ date('Y-m-d') }}</p>
                        <p><strong>HORA: </strong>{{ date('H:i:s') }}</p>
                        <p><strong>USUARIO: </strong>{{ Auth::user()->name }}</p>
                    </div>
                </tr>
            </table>
        </div>
    </div>
    <div class="separador"></div>
    <table class="table">
        <thead>
            <tr>
                <th colspan="5" style="text-align: center; background-color: rgba(0, 128, 0, 0.5);">INFORMACION DEL CONTRATANTE</th>
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
                <td>{{ $asociado['agreement'] }}</td>
            </tr>
            <tr>
                <th>PLAN</th>
                <th colspan="4">OBSERVACIONES</th>
            </tr>
            <tr>
                <td>{{ $asociado['codePlan'] }}</td>
                <td colspan="4">{{ $asociado['observation'] }}</td>
            </tr>
            <tr>
                <th>DISTRITO</th>
                <th colspan="2">CONGREGACIÓN</th>
                <th>FECHA DE NACIMIENTO</th>
                <th>CORREO</th>
            </tr>
            <tr>
                <td>{{ $pastor['district'] }}</td>
                <td colspan="2">{{ $pastor['congregation'] }}</td>
                <td>{{ $pastor['birthdate'] }}</td>
                <td>{{ $pastor['email'] }}</td>
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
    <p class="centerText">app.corpentunida.org.co</p>
</body>

</html>