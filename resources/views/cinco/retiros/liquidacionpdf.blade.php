<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    {{--    <style>
    * {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 10px;
    }

    .centerText {
        text-align: center;
    }

    /* DOMPDF NO RESPETA bien table-layout: fixed → lo ajusto */
    .tabla-certificado {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed; /* Si falla, lo cambiamos a auto */
        font-size: 12px;
    }

    /* Bordes */
    .tabla-certificado,
    .tabla-certificado th,
    .tabla-certificado td {
        border: 1px solid #000;
    }

    /* Encabezados */
    .tabla-certificado th {
        background-color: #c9daf8;
        text-align: center;
        padding: 4px;
        font-weight: bold;
    }

    /* Celdas normales */
    .tabla-certificado td {
        padding: 3px 5px;
        vertical-align: top;
        word-wrap: break-word;   /* → evita que colapse la tabla */
        overflow: hidden;        /* → ayuda a DOMPDF */
    }

    /* Ajustes importantes para DOMPDF */
    .tabla-certificado td:first-child {
        width: 70%;              /* Primera columna más ancha */
    }

    .tabla-certificado td:last-child {
        width: 30%;              /* Columna de valores */
        text-align: center;
    }

    /* Si hay tablas dentro de otras tablas */
    .tabla-certificado table {
        width: 100% !important;
        table-layout: fixed;
    }
</style>
 --}}
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
            /*table-layout: fixed;*/
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
    <table class="tabla-certificado" style="margin: 5px;">
        <tr>
            <th colspan="4">{{ $tercero->nom_ter }}</th>
            <th>C.C.</th>
            <td>{{ number_format($tercero->cod_ter) }}</td>
            <th>EDAD:</th>
            <td colspan="2">{{ $tercero->edad }}</td>
            <th>DTO:</th>
            <td>{{ preg_replace('/\D/', '', $tercero->distrito?->NOM_DIST) ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="2">FECHA ELABORACIÓN</td>
            <td>{{ \Carbon\Carbon::parse($retiro->fechaCreacion)->format('d/m/Y') }}</td>
            <td colspan="3" rowspan="2" style="text-align:center;">FECHA ULTIMO APORTE</td>
            <td colspan="2">CONSECUTIVO DOCUMENTO</td>
            <td colspan="3" class="codigo">{{ $retiro->consecutivoDocumento }}</td>
        </tr>
        <tr>
            <td colspan="2">FECHA INGRESO AL MINISTERIO</td>
            <td>{{ optional($tercero->fec_minis)->format('d/m/Y') ?? '' }}</td>
            <td colspan="2">FECHA DE AFILIACIÓN DEL ASOCIADO</td>
            <td colspan="3">
                {{ optional($tercero->fec_ing)->format('d/m/Y') ?? (optional($tercero->fecha_ipuc)->format('d/m/Y') ?? ' ') }}
            </td>
        </tr>
        <tr>
            <td colspan="2">FECHA PRIMER APORTE</td>
            <td>{{ optional($tercero->fec_aport)->format('d/m/Y') ?? ' ' }}</td>
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
            <td>{{ $retiro->fechaUltimoAporte }}</td>
            <td>{{ \Carbon\Carbon::parse($retiro->fechaRetiro)->format('d') }} </td>
            <td>{{ \Carbon\Carbon::parse($retiro->fechaRetiro)->format('m') }} </td>
            <td>{{ \Carbon\Carbon::parse($retiro->fechaRetiro)->format('Y') }} </td>
            <td>{{ \Carbon\Carbon::parse($retiro->fechaInicialLiquidacion)->format('d') }} </td>
            <td>{{ \Carbon\Carbon::parse($retiro->fechaInicialLiquidacion)->format('m') }} </td>
            <td>{{ \Carbon\Carbon::parse($retiro->fechaInicialLiquidacion)->format('Y') }} </td>
        </tr>
        <tr>
            <td>TIPO DE RETIRO:</td>
            <td colspan="4">{{ $retiro->tipoRetiroNom->nombre }} / {{ $retiro->observación }}</td>
            <td>{{ $tercero->fecha_ipuc->format('d/m/Y') }}</td>
            <td colspan="5">PARA INGRESO AL MINISTERIO DESDE EL 96 SE TIENE EN CUENTA LA FECHA DEL PRIMER APORTE</td>
        </tr>
    </table>



    <table style="width:100%; vertical-align: top;">
        <tr>
            <td style="width:33%; vertical-align: top;">
                <table class="tabla-certificado">
                    
                    <tr>
                        <th colspan="2">BENEFICIOS</th>
                    </tr>
                    

                    <tr>
                        <td style="background:#f4f4f4;font-weight:bold;">SUBTOTAL OTROS BENEFICIOS</td>
                        <td class="centrado" style="background:#f4f4f4;font-weight:bold;">$0</td>
                    </tr>

                    <tr>
                        <td style="background:#f4f4f4;font-weight:bold;">SUBTOTAL BENEFICIO POR ANTIGÜEDAD</td>
                        <td class="centrado" style="background:#f4f4f4;font-weight:bold;">$30,988,502</td>
                    </tr>

                    <tr>
                        <td style="background:#f4f4f4;font-weight:bold;">TOTAL BRUTO BENEFICIO POR ANTIGÜEDAD</td>
                        <td class="centrado" style="background:#f4f4f4;font-weight:bold;">$30,988,502</td>
                    </tr>
                </table>
            </td>

            <!-- BASE RETENCIÓN -->
            <td style="width:33%; vertical-align: top;">
                <table class="tabla-certificado">
                    <tr>
                        <th colspan="3">BASE RETENCIÓN</th>
                    </tr>

                    <tr>
                        <td>DECLARA RENTA</td>
                        <td class="centrado">NO</td>
                        <td class="centrado">$30,988,502</td>
                    </tr>

                    <tr>
                        <th colspan="3" class="centrado">DESCUENTOS</th>
                    </tr>

                    <tr>
                        <td>RETENCIÓN EN LA FUENTE</td>
                        <td class="centrado">0.035</td>
                        <td class="centrado">$1,084,598</td>
                    </tr>
                    <tr>
                        <td>SEGURO DE VIDA 2025</td>
                        <td></td>
                        <td class="centrado">$2,143,500</td>
                    </tr>
                    <tr>
                        <td>EXEQUIALES</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>RAPICREDITO</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>ANTICIPO SEGURO DE VIDA</td>
                        <td></td>
                        <td class="centrado">$2,442,900</td>
                    </tr>
                    <tr>
                        <td>ANTICIPO RAPICRÉDITO</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>CRÉDITO LIBRE INVERSIÓN MAYORES</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>LIBRERÍA</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>VEJEZ E INVALIDEZ</td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td colspan="2" style="background:#f4f4f4;font-weight:bold;">TOTAL DESCUENTOS</td>
                        <td class="centrado" style="background:#f4f4f4;font-weight:bold;">$5,670,998</td>
                    </tr>
                </table>
            </td>

            <!-- TOTALES -->
            <td style="width:33%; vertical-align: top;">
                <table class="tabla-certificado">
                    <tr>
                        <td>TOTAL BENEFICIOS</td>
                        <td class="centrado">$30,988,502</td>
                    </tr>
                    <tr>
                        <td>TOTAL SALDOS A FAVOR</td>
                        <td class="centrado">$0</td>
                    </tr>
                    <tr>
                        <td>TOTAL DESCUENTOS</td>
                        <td class="centrado">$5,670,998</td>
                    </tr>

                    <tr>
                        <td colspan="2"
                            style="background:#dfe8f3; font-weight:bold; text-align:center; padding:15px;">
                            TOTAL BENEFICIO POR ANTIGÜEDAD<br>CORPENTUNIDA
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="font-size:28px; font-weight:bold; text-align:center; padding:15px;">
                            $25,317,504
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br><br>

    <!-- SALDOS A FAVOR -->
    <table class="tabla-certificado">
        <tr>
            <th colspan="6" class="centrado">SALDOS A FAVOR</th>
        </tr>

        <tr>
            <td>SEGURO DE VIDA</td>
            <td>EXEQUIALES</td>
            <td>RAPICREDITO</td>
            <td>ANTICIPOS SEGURO DE VIDA</td>
            <td>CREDITOS 1</td>
            <td>CREDITOS 2</td>
        </tr>

        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td>CREDITOS 3</td>
            <td>OTROS</td>
            <td colspan="4" class="centrado">$0</td>
        </tr>
    </table>
    <p class="centerText"><strong>app.corpentunida.org.co</strong> todos los derechos reservados.</p>
</body>

</html>
