<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Reservas - Corpentunida</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; text-transform: uppercase; }
        .date { font-size: 10px; color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f2f2f2; border: 1px solid #ddd; padding: 8px; text-align: left; }
        td { border: 1px solid #ddd; padding: 8px; }
        
        .status-badge { padding: 3px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; }
        .summary-box { background: #f9f9f9; padding: 15px; border: 1px solid #eee; margin-bottom: 20px; }
        
        .text-danger { color: #dc3545; }
        .fw-bold { font-weight: bold; }
        footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Informe Ejecutivo de Reservas</div>
        <div class="date">Generado el: {{ $fechaInforme }}</div>
    </div>

    <h3>1. Resumen por Estados</h3>
    <div class="summary-box">
        <table>
            <thead>
                <tr>
                    <th>Estado de Reserva</th>
                    <th style="text-align: center;">Total Solicitudes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resumenEstados as $resumen)
                    <tr>
                        <td>
                            @if($resumen->res_status_id == 1) Solicitadas
                            @elseif($resumen->res_status_id == 2) Confirmadas
                            @elseif($resumen->res_status_id == 4) <span class="text-danger">Canceladas</span>
                            @elseif($resumen->res_status_id == 5) Con Soporte
                            @else {{ $resumen->res_status->name ?? 'Otro' }} @endif
                        </td>
                        <td style="text-align: center;">{{ $resumen->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <h3>2. Listado Detallado de Reservas</h3>
    <table>
        <thead>
            <tr>
                <th>Inmueble</th>
                <th>Asociado</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservas as $reserva)
                <tr>
                    <td>{{ $reserva->res_inmueble->name ?? 'N/A' }}</td>
                    <td>{{ $reserva->user->name ?? 'N/A' }}</td>
                    <td>{{ $reserva->fecha_inicio->format('d/m/Y') }}</td>
                    <td>{{ $reserva->fecha_fin->format('d/m/Y') }}</td>
                    <td>
                        @if($reserva->res_status_id == 4) <strong>CANCELADA</strong>
                        @elseif($reserva->res_status_id == 2) Confirmada
                        @else Pendiente @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <footer>
        Sistema de Gestión Documental y Reservas - Corpentunida &copy; {{ date('Y') }}
    </footer>
</body>
</html>