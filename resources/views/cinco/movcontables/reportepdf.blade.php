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
            font-size: 10px;
        }

        .header div {
            text-align: right;
            font-size: 10px;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            /*border: 1px solid black;*/
            padding: 8px;
            text-align: right;
            font-size: 10px;
        }

        .bg-grey {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .columna-1 {
            width: 10%;
            /* 30% del ancho total */
        }

        .columna-2 {
            width: 15%;
            /* 20% del ancho total */
        }

        .separador {
            height: 20px;
        }

        .centerText {
            font-size: 10px;
            text-align: center;
        }
    </style>

    <div class="header">
        {{-- <img src="{{ asset('assets/images/CORPENTUNIDA_LOGO PRINCIPAL  (2).png') }}" alt="logo"> --}}
        <table style="border: none; width: 100%;">
            <tr style="border: none">
                <td style="border: none; width:100px;">
                    <img src="{{ $image_path }}" alt="logoCorpen" style="width: 300px;">
                    {{-- <img src="{{ asset('assets/images/CORPENTUNIDA_LOGO PRINCIPAL  (2).png') }}" alt="logo" style="width: 300px;"> --}}
                </td>
                <td style="border: none">
                    <h2 style="text-align: left;">REPORTE PDF MOVIMIENTOS CONTABLES CINCO</h2>
                </td>
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


    <div>
        <p><strong>7514540 </strong> CAMACHO ISAZA ANGELMIRO</p>
    </div>

    @foreach ($cuentasAgrupadas as $c)
        <table class="table">
            <thead>
                <tr>
                    <td colspan="6" style="text-align: center;" class="bg-grey">{{ $c->cuenta }} _
                        {{ $c->cuentaContable->Descripcion }}</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th class="columna-1">Comprobante</th>
                    <th class="columna-1">Num Comprobante</th>
                    <th class="columna-2">Fecha</th>
                    <th>Observación</th>
                    <th class="columna-2">Débitos</th>
                    <th class="columna-2">Créditos</th>
                </tr>
                @php
                    $contadorcreditos = 0;
                    $contadordebitos = 0;
                @endphp
                @foreach ($movimientos as $mov)
                    @if ($mov->Cuenta == $c->cuenta)
                        <tr>
                            <td class="columna-1">{{ $mov->CodComprob }}</td>
                            <td class="columna-1">{{ $mov->NumComprob }}</td>
                            <td class="columna-2">{{ $mov->Fecha }}</td>
                            <td>{{ $mov->Observacion }}</td>
                            <td class="columna-2">{{ number_format($mov->VrDebitos) }}</td>
                            <td class="columna-2">{{ number_format($mov->VrCreditos) }}</td>
                        </tr>
                    @php
                        $contadorcreditos += $mov->VrCreditos;
                        $contadordebitos += $mov->VrDebitos;
                    @endphp
                    @endif
                @endforeach
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><strong>Débitos: </strong>$ {{ number_format($contadordebitos) }}</td>
                    <td><strong class="fw-bold">Créditos: </strong>$ {{ number_format($contadorcreditos) }}</td>
                <tr>
            </tbody>
        </table>
        <div class="separador"></div>
    @endforeach


    <p class="centerText"><strong>app.corpentunida.org.co</strong> todos los derechos reservados.</p>
</body>

</html>
