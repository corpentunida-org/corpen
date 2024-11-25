<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<style>
    *{
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        text-align: justify;
    }
    body{
        /* background-image: url("{{ asset('assets/img/fondoPdf.jpg') }}"); */
        {{--background-image: url("{{ $image_path }}"); --}}
        background-image: url("{{ $image_path }}");
        background-size: cover;
        background-repeat: no-repeat;
    }
    h3{
        text-align: center;
        font-family: Arial, Helvetica, sans-serif;
    }
    .container{
        margin-top: 100px;
    }
    .m-0{
        margin: 0;
    }
    .border-bottom{
        border-bottom: 1px solid black;
    }

</style>
<body class="pt-5">
    <div class="container p-4">
        <p id="fechaHoy"></p>
        <p>Hermano:</p>
        <p class="m-0">{{$registro->NOMBRE}}</p>
        <p>{{$registro->CEDULA}}</p>
        <p>DISTRITO {{$registro->DISTRITO}}</p>
        <h3 class="mt-5">ESTADO DE CUENTA {{ $registro->LINEA_DE_CREDITO }} (En Mora)</h3>
        <h3 class="mb-5">FECHA DE CORTE (2024)</h3>


        <p>Estimado Pastor, en esta oportunidad nos permitimos informarle que en nuestro sistema registra mora
            en su obligación para con la Asociación, por tal motivo a continuación relacionamos los valores
            pendientes de pago:</p>
        <ul>
            <li>FECHA INICIO DE CRÉDITO: {{$registro->fecha_inicio_credito}}</li>
            <li>VALOR CRÉDITO: {{$registro->valor_credito}}</li>
            <li>CAPITAL ACTUAL: {{$registro->capital_actual}}</li>
            <li>CUOTAS VENCIDAS: {{$registro->coutas_vencidas}}</li>
            <li>CAPITAL VENCIDO: {{$registro->capital_vencido}}</li>
            <li>INTERÉS Y SEGURO VENCIDO: {{$registro->interes_seguro_vencido}}</li>
        </ul>

        <p>Último mes cancelado {{$registro->mes_cancelado}}, para quedar al día debe cancelar el valor de {{$registro->saldo_mora}}.</p>

        <p><strong>Nota:</strong></p>
        <p>
            Recuerde que si su crédito llega a tener doce (12) cuotas en mora,
            este será llevado como anticipo del auxilio de retiro y tendrá una
            sanción de tres (3) años.
        </p>
        <p>
            Si requiere mayor información con gusto le atenderemos, Quedamos
            atentos a cualquier inquietud.
        </p>
        <p>
            “Por favor enviar soporte de la consignación, especificando nombre
            del pastor, cédula y teléfono al número de WhatsApp donde se está
            notificando esta información”
        </p>

        <p class="my-5">Atentamente,</p>
        <div class="w-25 pt-4 border-bottom border-dark"></div>

        <p class="m-0 text-uppercase"><strong>{{ Auth::user()->name }}</strong></p>
        <p><strong>Correo: </strong>{{ Auth::user()->email }}</p>

    </div>
    


    <script>
        var hoy = new Date().toISOString().split('T')[0];
        $("#fechaHoy").text("Bogotá D.C "+hoy)

        console.log(@json($registro))
    </script>
</body>

</html>