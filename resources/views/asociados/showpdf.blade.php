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
        <h2>Solicitud de afiliación a prevención exequial</h2>
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
                <th colspan="3">APELLIDOS Y NOMBRES</th>
                <th>DOCUMENTO DE IDENTIDAD</th>
                <th>FECHA DE INICIO</th>
            </tr>
            <tr>
                <td colspan="3">{{ $asociado['name'] }}</td>
                <td>{{ $asociado['documentId'] }}</td>
                <td>{{ $asociado['dateInit'] }}</td>
            </tr>
            <tr>
                <th>PLAN</th>
                <th>OBSERVACIONES</th>
            </tr>
            <tr>
                <td>{{ $asociado['codePlan'] }}</td>
                <td colspan="4">{{ $asociado['observation'] }}</td>
            </tr>
        </tbody>
    </table>
    <div class="separador"></div>
    <table>
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
    
</body>

</html>