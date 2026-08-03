<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Reserva #{{ $reserva->codigo_reserva ?? 'RSV-0000' }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; font-size: 14px; line-height: 1.5; margin: 0; padding: 20px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.05); background: #fff; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #4f46e5; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #4f46e5; margin: 0; font-size: 24px; }
        .details { margin-bottom: 30px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        .table th { background-color: #f9fafb; font-weight: bold; color: #374151; }
        .total { text-align: right; font-size: 18px; font-weight: bold; margin-top: 25px; color: #111827; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #eee; pt: 20px; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div>
                <h1>Comprobante de Reserva</h1>
                <p>Código: <strong>{{ $reserva->codigo_reserva ?? 'N/D' }}</strong></p>
            </div>
            <div style="text-align: right;">
                <p>Fecha de emisión: {{ date('d/m/Y') }}</p>
            </div>
        </div>

        <div class="details">
            <p><strong>Huésped Principal:</strong> {{ $reserva->user->name ?? 'Cliente General' }}</p>
            <p><strong>Fecha de Inicio:</strong> {{ $reserva->fecha_inicio ?? 'N/D' }}</p>
            <p><strong>Fecha de Fin:</strong> {{ $reserva->fecha_fin ?? 'N/D' }}</p>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Detalle</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Estancia en Inmueble</td>
                    <td>Reserva ID: {{ $reserva->id ?? 'N/D' }}</td>
                    <td>${{ number_format($reserva->monto_total ?? 0, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total">
            Total Pagado: ${{ number_format($reserva->monto_total ?? 0, 2) }}
        </div>

        <div class="footer">
            <p>Este documento es un comprobante digital generado automáticamente por el sistema de reservas RSV.</p>
        </div>
    </div>
</body>
</html>
