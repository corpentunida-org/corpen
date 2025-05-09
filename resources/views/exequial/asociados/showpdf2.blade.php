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
        {{-- <img src="{{ asset('assets/images/CORPENTUNIDA_LOGO PRINCIPAL  (2).png') }}" alt="logo"> --}}
        <table style="border: none; width: 100%;" >
            <tr style="border: none">
                <td style="border: none; width:100px;">
                    <img src="{{ $image_path }}" alt="logoCorpen" style="width: 300px;">
                    {{-- <img src="{{ asset('assets/images/CORPENTUNIDA_LOGO PRINCIPAL  (2).png') }}" alt="logo" style="width: 300px;"> --}}
                </td>
                <td style="border: none"><h2 style="text-align: left;">CERTIFICADO DE AFILIACIÓN A PREVISIÓN EXEQUIAL</h2></td>
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
                <th colspan="4" style="text-align: center; background-color: rgba(0, 128, 0, 0.5);">INFORMACIÓN DEL CONTRATANTE</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th colspan="2">APELLIDOS Y NOMBRES</th>
                <th>DOCUMENTO DE IDENTIDAD</th>
                <th>CONTRATO</th>
            </tr>
            <tr>
                <td colspan="2">{{ $asociado['name'] }}</td>
                <td>{{ $asociado['documentId'] }}</td>
                <th>{{ $asociado['agreement'] }}</th>
            </tr>    
            <tr>
                <th>FECHA DE INICIO</th>
                <th colspan="2">FECHA DE NACIMIENTO</th>
                <th>EDAD</th>                
            </tr>
            <tr>
                <td>{{ $asociado['dateInit'] }}</td>
                <td colspan="2">{{ $pastor['birthdate'] }}</td>
                @php
                    $fecNac = new DateTime(
                        $pastor['birthdate'],
                    );
                    $fechaActual = new DateTime();
                    $diferencia = $fecNac->diff($fechaActual);
                    $edad = $diferencia->y;
                @endphp
                    <td>{{ $edad }}</td>
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
            @if ($beneficiario['type'] == 'A')
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
            @endif
            @endforeach
        </tbody>
    </table>
    <p class="centerText"><strong>app.corpentunida.org.co</strong> todos los derechos reservados.</p>
</body>

</html>