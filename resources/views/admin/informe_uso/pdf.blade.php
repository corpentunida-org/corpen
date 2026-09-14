<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Informe de Uso</title>
</head>

<body>
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 6px;
            text-align: left;
            font-size: 9px;
        }

        th {
            background-color: #f2f2f2;
        }

        h3 {
            font-size: 12px;
            margin: 0 0 6px 0;
        }

        .centerText {
            font-size: 7px;
            text-align: center;
        }
    </style>

    <table style="border: none; width: 100%;">
        <tr style="border: none">
            <td style="border: none; width:50px;">
                <img src="{{ public_path('assets/images/CORPENTUNIDA_LOGO PRINCIPAL  (2).png') }}" alt="logoCorpen" style="width: 250px;">
            </td>
            <td style="border: none">
                <div style="text-align: right; font-size: 10px;">
                    <p><strong>FECHA: </strong>{{ date('Y-m-d') }}</p>
                    <p><strong>PERIODO: </strong>{{ $desde }} a {{ $hasta }}</p>
                    <p><strong>USUARIO: </strong>{{ Auth::user()->name }}</p>
                </div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th colspan="2" style="text-align: center; background-color: rgba(0, 128, 0, 0.5);">USO POR ÁREA</th>
            </tr>
            <tr>
                <th>Área</th>
                <th>Total acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($porArea as $f)
                <tr>
                    <td>{{ $f['area'] }}</td>
                    <td>{{ $f['total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table>
        <thead>
            <tr>
                <th colspan="2" style="text-align: center; background-color: rgba(0, 128, 0, 0.5);">USO POR USUARIO</th>
            </tr>
            <tr>
                <th>Usuario</th>
                <th>Total acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($porUsuario as $f)
                <tr>
                    <td>{{ $f['nombre'] }}</td>
                    <td>{{ $f['total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table>
        <thead>
            <tr>
                <th colspan="4" style="text-align: center; background-color: rgba(0, 128, 0, 0.5);">TIEMPO ACTIVO POR USUARIO (toda la app, basado en sesiones)</th>
            </tr>
            <tr>
                <th>Usuario</th>
                <th>Sesiones</th>
                <th>Tiempo activo total</th>
                <th>Promedio por sesión</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tiempoActivo as $f)
                <tr>
                    <td>{{ $f['nombre'] }}</td>
                    <td>{{ $f['sesiones'] }}</td>
                    <td>{{ \App\Http\Controllers\Admin\InformeUsoController::formatoDuracion($f['segundos_total']) }}</td>
                    <td>{{ \App\Http\Controllers\Admin\InformeUsoController::formatoDuracion($f['segundos_promedio']) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Sin datos en este periodo.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table>
        <thead>
            <tr>
                <th colspan="4" style="text-align: center; background-color: rgba(0, 128, 0, 0.5);">TIEMPO EN GESTIONES (Interacciones, histórico real)</th>
            </tr>
            <tr>
                <th>Agente</th>
                <th>Gestiones</th>
                <th>Tiempo total</th>
                <th>Promedio por gestión</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tiempoGestiones as $f)
                <tr>
                    <td>{{ $f['nombre'] }}</td>
                    <td>{{ $f['gestiones'] }}</td>
                    <td>{{ \App\Http\Controllers\Admin\InformeUsoController::formatoDuracion($f['segundos_total']) }}</td>
                    <td>{{ \App\Http\Controllers\Admin\InformeUsoController::formatoDuracion($f['segundos_promedio']) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Sin gestiones en este periodo.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="centerText"><strong>app.corpentunida.org.co</strong> todos los derechos reservados.</p>
</body>
</html>
