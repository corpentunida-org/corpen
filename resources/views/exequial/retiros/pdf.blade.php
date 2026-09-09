<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Retirados</title>
</head>

<body>
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
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
            font-size: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .separador {
            height: 20px;
        }

        .centerText {
            font-size: 7px;
            text-align: center;
        }
    </style>

    <table style="border: none; width: 100%;">
        <tr style="border: none">
            <td style="border: none; width:50px;">
                <img src="{{ $image_path }}" alt="logoCorpen" style="width: 300px;">
            </td>
            <td style="border: none">
                <div style="text-align: right; font-size: 10px;">
                    <p><strong>FECHA: </strong>{{ date('Y-m-d') }}</p>
                    <p><strong>HORA: </strong>{{ date('H:i:s') }}</p>
                    <p><strong>USUARIO: </strong>{{ Auth::user()->name }}</p>
                </div>
            </td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th colspan="7" style="text-align: center; background-color: rgba(0, 128, 0, 0.5);">INFORME DE RETIRADOS</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>Cédula</th>
                <th>Nombre</th>
                <th>Fecha Afiliación</th>
                <th>Fecha Retiro</th>
                <th>Observaciones</th>
                <th>Estado</th>
                <th>Reportado</th>
            </tr>
            @foreach ($retiros as $r)
                <tr>
                    <td>{{ $r->cod_cli }}</td>
                    <td>{{ $r->nombre }}</td>
                    <td>{{ optional($r->fecha_afiliacion)->format('d/m/Y') }}</td>
                    <td>{{ $r->fecha_retiro->format('d/m/Y') }}</td>
                    <td>{{ $r->observaciones }}</td>
                    <td>{{ $r->fecha_reafiliacion ? 'Reafiliado ' . $r->fecha_reafiliacion->format('d/m/Y') : 'Vigente' }}</td>
                    <td>{{ $r->reportado_aliado ? 'Sí' : 'No' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="centerText"><strong>app.corpentunida.org.co</strong> todos los derechos reservados.</p>
    <div class="separador"></div>
</body>
</html>
