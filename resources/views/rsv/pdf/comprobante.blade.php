<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Reserva · {{ $reserva->codigo_reserva ?? ('#' . $reserva->id) }}</title>
    <style>
        @page { margin: 24px 28px; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10.5px;
            color: #334155;
            line-height: 1.45;
            margin: 0;
            padding: 0;
        }
        .top-banner {
            background: #0f172a;
            color: #ffffff;
            padding: 16px 20px;
            border-radius: 6px;
            margin-bottom: 16px;
        }
        .top-banner table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        .top-banner td {
            border: none;
            padding: 0;
            color: #ffffff;
            vertical-align: middle;
        }
        .brand-tag {
            display: inline-block;
            font-size: 8.5px;
            font-weight: bold;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: #86efac;
            margin-bottom: 4px;
        }
        .doc-title {
            font-size: 15px;
            font-weight: bold;
            margin: 0 0 3px 0;
            color: #ffffff;
        }
        .doc-sub {
            font-size: 9.5px;
            color: #cbd5e1;
            margin: 0;
        }
        .total-box {
            text-align: right;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(134, 239, 172, 0.35);
            border-radius: 6px;
            padding: 8px 12px;
        }
        .total-box .lbl {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #94a3b8;
            display: block;
        }
        .total-box .amt {
            font-size: 14px;
            font-weight: bold;
            color: #86efac;
            font-variant-numeric: tabular-nums;
        }
        .sec-title {
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #3d7a5c;
            border-bottom: 1px solid #d5e7dc;
            padding-bottom: 4px;
            margin: 14px 0 8px 0;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.data-table th {
            background: #f8fafc;
            color: #475569;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            text-align: left;
        }
        table.data-table td {
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            font-size: 10px;
            color: #1e293b;
            vertical-align: top;
        }
        .meta-label {
            background: #fafbfd !important;
            color: #64748b !important;
            font-weight: bold;
            width: 18%;
            font-size: 9px !important;
            text-transform: uppercase;
        }
        .meta-val {
            width: 32%;
        }
        .num {
            text-align: right !important;
            font-variant-numeric: tabular-nums;
        }
        .badge-soft {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 8.5px;
            font-weight: bold;
            background: #f0f7f2;
            color: #2e5c43;
            border: 1px solid #d5e7dc;
        }
        .obs-box {
            background: #fafbfd;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #3d7a5c;
            padding: 8px 10px;
            border-radius: 4px;
            font-size: 9.5px;
            color: #475569;
            margin-top: 6px;
        }
        .footer-note {
            margin-top: 18px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            font-size: 8.5px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>
    @php
        $montoReserva = (float) ($reserva->monto_total ?? 0);
        $fechaIniFmt  = $reserva->fecha_inicio ? $reserva->fecha_inicio->format('d/m/Y') : '—';
        $fechaFinFmt  = $reserva->fecha_fin ? $reserva->fecha_fin->format('d/m/Y') : '—';
        $estadoNombre = optional($reserva->status)->nombre ?? optional($reserva->status)->name ?? 'Registrada';
        $inmuebleNom  = optional($reserva->inmueble)->name ?? '—';
        $titularNom   = optional($reserva->user)->name ?? '—';
        $origenNom    = optional($reserva->origen)->nombre ?? '—';
        $listaHuesp   = collect($huespedes ?? []);
        $listaTrx     = collect($transacciones ?? []);
    @endphp

    <div class="top-banner">
        <table>
            <tr>
                <td>
                    <span class="brand-tag">Comprobante Oficial de Reserva · RSV</span>
                    <p class="doc-title">Código {{ $reserva->codigo_reserva ?? ('#' . $reserva->id) }}</p>
                    <p class="doc-sub">Inmueble: {{ $inmuebleNom }} · Emitido el {{ now()->format('d/m/Y H:i') }}</p>
                </td>
                <td style="width: 170px;">
                    <div class="total-box">
                        <span class="lbl">Total Reserva</span>
                        <span class="amt">${{ number_format($montoReserva, 2) }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="sec-title">Información General y Titular</div>
    <table class="data-table">
        <tr>
            <th class="meta-label">Titular</th>
            <td class="meta-val"><strong>{{ $titularNom }}</strong></td>
            <th class="meta-label">Estado</th>
            <td class="meta-val"><span class="badge-soft">{{ $estadoNombre }}</span></td>
        </tr>
        <tr>
            <th class="meta-label">Ingreso</th>
            <td class="meta-val">{{ $fechaIniFmt }}</td>
            <th class="meta-label">Salida</th>
            <td class="meta-val">{{ $fechaFinFmt }}</td>
        </tr>
        <tr>
            <th class="meta-label">Total Reserva</th>
            <td class="meta-val num"><strong>${{ number_format($montoReserva, 2) }}</strong></td>
            <th class="meta-label">Origen / Huéspedes</th>
            <td class="meta-val">{{ $origenNom }} · {{ $listaHuesp->count() }} registrado(s)</td>
        </tr>
    </table>

    @if(!empty($reserva->comentario_reserva))
        <div class="obs-box">
            <strong>Observaciones de la reserva:</strong> {{ $reserva->comentario_reserva }}
        </div>
    @endif

    <div class="sec-title">Huéspedes Registrados</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Nombre Completo</th>
                <th>Documento</th>
                <th>Rol en Reserva</th>
            </tr>
        </thead>
        <tbody>
            @forelse($listaHuesp as $h)
                @php
                    $nomH = trim((string) ($h->nombre_completo ?? (($h->nombre ?? '') . ' ' . ($h->apellidos ?? ''))));
                    $docH = $h->numero_documento ?? $h->documento ?? '—';
                    $rolH = !empty($h->es_titular) ? 'Titular' : ($h->tipo_huesped ?? 'Acompañante');
                @endphp
                <tr>
                    <td>{{ $nomH !== '' ? $nomH : '—' }}</td>
                    <td>{{ $docH }}</td>
                    <td>{{ $rolH }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="color: #94a3b8; text-align: center;">Sin huéspedes registrados en esta reserva.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="sec-title">Transacciones y Pagos</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Referencia / ID</th>
                <th>Pasarela / Medio</th>
                <th>Estado</th>
                <th class="num">Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($listaTrx as $trx)
                @php
                    $refT = $trx->referencia_externa ?? $trx->referencia ?? $trx->id;
                    $pasT = optional($trx->pasarela)->nombre ?? ($trx->metodo_pago ?? '—');
                    $estT = $trx->estado_pago ?? $trx->estado ?? '—';
                    $valT = (float) ($trx->monto ?? $trx->valor ?? 0);
                @endphp
                <tr>
                    <td>{{ $refT }}</td>
                    <td>{{ $pasT }}</td>
                    <td>{{ $estT }}</td>
                    <td class="num">${{ number_format($valT, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="color: #94a3b8; text-align: center;">Sin transacciones registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-note">
        Documento generado automáticamente por el sistema RSV · Total Reserva: ${{ number_format($montoReserva, 2) }}
    </div>
</body>
</html>
