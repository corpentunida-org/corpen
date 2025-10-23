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

        .centerText {
            font-size: 10px;
            text-align: center;
        }

        .tabla-certificado {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-family: Arial, sans-serif;
            font-size: 13px;
            text-align: left;
        }

        .tabla-certificado,
        .tabla-certificado th,
        .tabla-certificado td {
            border: 1px solid #000;
        }

        .tabla-certificado th {
            background-color: #c9daf8;
            /* Azul suave */
            text-align: center;
            padding: 6px;
        }

        .tabla-certificado td {
            padding: 5px 8px;
            vertical-align: middle;
        }

        .tabla-certificado .codigo {
            font-weight: bold;
            font-size: 16px;
            text-align: center;
        }

        .tabla-certificado .centrado {
            text-align: center;
        }
    </style>
    <div class="header">
        <table style="border: none; width: 100%;">
            <tr style="border: none">
                <td style="border: none; width:100px;">
                    <img src="{{ $image_path }}" alt="logoCorpen" style="width: 300px;">
                </td>
                <td style="border: none">
                    <div class="centerText">
                        <p style="font-size: 8">ESTADO DE CUENTA:</p>
                        <p><strong>LIQUIDACION BENEFICIO POR ANTIGUEDAD </strong></p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <table class="tabla-certificado">
        <tr>
            <th colspan="4">{{$tercero->nom_ter}}</th>
            <th>C.C.</th>
            <td>{{ number_format($tercero->cod_ter)}}</td>
            <th>EDAD:</th>
            <td colspan="2">{{ $tercero->edad }}</td>
            <th>DTO:</th>
            <td>{{ preg_replace('/\D/', '', $tercero->distrito?->NOM_DIST) ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="2">FECHA ELABORACIÓN</td>
            <td>{{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</td>
            <td colspan="3" rowspan="2" style="text-align:center;">FECHA ULTIMO APORTE</td>
            <td colspan="2">CONSECUTIVO DOCUMENTO</td>
            <td colspan="3" class="codigo">BPA-2025063</td>
        </tr>
        <tr>
            <td colspan="2">FECHA INGRESO AL MINISTERIO</td>
            <td>{{ optional($tercero->fec_minis)->format('d/m/Y') ?? '' }}</td>
            <td colspan="2">FECHA DE AFILIACIÓN DEL ASOCIADO</td>
            <td colspan="3">{{ optional($tercero->fec_ing)->format('d/m/Y') ?? ' ' }}</td>
        </tr>
        <tr>
            <td colspan="2">FECHA PRIMER APORTE</td>
            <td>{{ optional($tercero->fec_aport)->format('d/m/Y') ?? ' '}}</td>
            <td>DIA</td>
            <td>MES</td>
            <td>AÑO</td>
            <td rowspan="2" colspan="2">FECHA INICIAL LIQUIDACION</td>
            <td>DIA</td>
            <td>MES</td>
            <td>AÑO</td>
        </tr>
        <tr>
            <td colspan="2">FECHA RETIRO IPUC:</td>
            <td></td>
            <td>28</td>
            <td>8</td>
            <td>2023</td>
            <td>1</td><td>2</td><td>2011</td>            
        </tr>
        <tr>
            <td>TIPO DE RETIRO:</td>
            <td colspan="4">DESTITUCIÓN / Faltas al comportamiento Ético y Moral</td>
            <td></td>
            <td colspan="5">PARA INGRESO AL MINISTERIO DESDE EL 96 SE TIENE EN CUENTA LA FECHA DEL PRIMER APORTE</td>
        </tr>
    </table>




    <p class="centerText"><strong>app.corpentunida.org.co</strong> todos los derechos reservados.</p>
</body>

</html>
