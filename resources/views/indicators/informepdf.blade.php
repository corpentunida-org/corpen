<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Indicadores TIC</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 16px;
            margin: 0;
        }

        .meta {
            width: 100%;
            margin-bottom: 15px;
        }

        .meta td {
            font-size: 9px;
            padding: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #f0f0f0;
            border: 1px solid #999;
            padding: 6px;
            text-align: left;
            font-size: 10px;
        }

        td {
            border: 1px solid #ccc;
            padding: 6px;
            vertical-align: top;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            font-size: 9px;
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>

    {{-- ENCABEZADO --}}
    <div class="header">
        <h1>Informe de Indicadores TIC</h1>
    </div>

    {{-- METADATA --}}
    <table class="meta">
        <tr>
            <td><strong>Usuario:</strong> {{ auth()->user()->name ?? 'N/A' }}</td>
            <td class="text-right"><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

    {{-- TABLA PRINCIPAL --}}
    <table>
        <thead>
            <tr>
                <th width="4%">ID</th>
                <th width="22%">Nombre</th>
                <th width="28%">Cálculo</th>
                <th width="8%" class="text-center">Meta</th>
                <th width="10%" class="text-center">Frecuencia</th>
                <th width="10%" class="text-center">Indicador</th>
            </tr>
        </thead>
        <tbody>
            @foreach($indicators as $ind)
                <tr>
                    <td class="text-center">{{ $ind->id }}</td>
                    <td>{{ $ind->nombre }}</td>
                    <td>{{ $ind->calculo }}</td>
                    <td class="text-center">{{ $ind->meta }}</td>
                    <td class="text-center">{{ $ind->frecuencia }}</td>
                    <td class="text-center">
                        {{ is_numeric($ind->indicador_calculado)
                            ? number_format($ind->indicador_calculado, 2) . ' %'
                            : ' '
                        }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        Documento generado automáticamente por el sistema
    </div>

</body>
</html>

