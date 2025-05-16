<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Corinthia:wght@400;700&display=swap" rel="stylesheet">
</head>
<style>
    @page {
        margin: 0;
    }

    * {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        text-align: justify;
    }

    .corinthia-regular {
        font-family: "Corinthia", cursive;
        font-weight: 400;
        font-style: normal;
        font-size: 25px;
    }

    body {
        margin: 0;
        padding: 0;
        background-image: url("{{ $image_path }}");
        background-size: cover;
        background-repeat: no-repeat;
    }

    .text-center {
        text-align: center;
        font-family: Arial, Helvetica, sans-serif;
    }

    .container {
        margin-top: 100px;
    }

    .m-0 {
        margin: 0;
    }

    .mb-5 {
        margin-bottom: 50px;
    }

    .border-bottom {
        border-bottom: 1px solid black;
    }

    .p-7 {
        padding: 80px;
    }

    table {
        width: 100%;
        margin-left: 30px;
        margin-bottom: 30px;
    }

    .table-td {
        width: 30%;
    }

    .pt-5 {
        padding-top: 50px;
    }
</style>

<body class="pt-5">
    <div class="container p-7">
        <p id="fechaHoy" class="mb-5">{{ \Carbon\Carbon::now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
        </p>
        <p>Hermano:</p>
        <p class="m-0"><strong>{{ $registro->NOMBRE }}</strong></p>
        <p class="m-0"><strong>{{ $registro->CEDULA }}</strong></p>
        <p class="m-0 mb-5">DISTRITO {{ $registro->DISTRITO }}</p>
        <p class="text-center"><strong class="mt-5">ESTADO DE CUENTA {{ strtoupper($registro->LINEA_DE_CREDITO) }} (En
                Mora)</strong></p>
        <p class="text-center"><strong class="mb-5">FECHA DE CORTE (2025)</strong></p>


        <p>Estimado Pastor, en esta oportunidad nos permitimos informarle que en nuestro sistema registra mora
            en su obligación para con la Asociación, por tal motivo a continuación relacionamos los valores
            pendientes de pago:</p>
        <table>
            <tr>
                <td class="table-td">
                    <li>FECHA INICIO DE CRÉDITO:
                </td>
                <td><strong> {{ $registro->FECHA_INCIO_DE_CREDITO }}</strong></td>
            </tr>
            <tr>
                <td>
                    <li>VALOR CRÉDITO:
                </td>
                <td><strong> $ {{ number_format($registro->VALOR_CREDITO) }}</strong></td>
            </tr>
            <tr>
                <td>
                    <li>CAPITAL ACTUAL:
                </td>
                <td><strong> $ {{ number_format($registro->CAPITAL_ACTUAL) }}</strong></td>
            </tr>
            <tr>
                <td>
                    <li>CUOTAS VENCIDAS:
                </td>
                <td><strong> {{ $registro->CUOTAS_VENCIDAS }} </strong></td>
            </tr>
            <tr>
                <td>
                    <li>CAPITAL VENCIDO:
                </td>
                <td><strong> $ {{ number_format($registro->CAPITAL_VENCIDO) }}</strong></td>
            </tr>
            <tr>
                <td>
                    <li>INTERÉS Y SEGURO VENCIDO:
                </td>
                <td><strong> $ {{ number_format($registro->INTERES_SEGURO_VENCIDO) }}</strong></td>
            </tr>
        </table>

        <p>Último mes cancelado <strong>
                {{ $registro->MES_CANCELADO 
                    ? \Carbon\Carbon::createFromFormat('d/m/Y', $registro->MES_CANCELADO)->locale('es')->isoFormat('MMMM - YYYY') 
                    : 'NINGUNO' }}
            </strong>, para quedar al día debe cancelar el valor de <strong> $
                {{ number_format($registro->CANCELAR_VALOR_DE) }} </strong></p>


        <p strong class="mt-5"><strong>Nota:</strong></p>
        <p>
            Recuerde que si su crédito llega a tener doce (12) cuotas en mora,
            este será llevado como anticipo del auxilio de retiro y tendrá una
            sanción de tres (3) años.
        </p>
        <p>
            Si requiere mayor información con gusto le atenderemos, Quedamos
            atentos a cualquier inquietud.
        </p>
        <p class="text-center">
            “ Por favor enviar soporte de la consignación, especificando nombre
            del pastor, cédula y teléfono al número de WhatsApp donde se está
            notificando esta información. ”
        </p>

        <p class="my-5">Atentamente,</p>
        <strong class="pt-4 corinthia-regular">{{ ucwords(strtolower(Auth::user()->name)) }}</strong>
        <div class="w-25 border-bottom border-dark p-0 m-0"></div>

        <p class="m-0 text-uppercase pt-4"><strong>{{ Auth::user()->cargo }}</strong></p>
        <p class="m-0"><strong>Correo: </strong>{{ Auth::user()->email }}</p>
        <p class="m-0"><strong>Teléfono: </strong>{{ Auth::user()->telefono }}</p>

    </div>

</body>
</html>