<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado de Cuenta - CORPENTUNIDA</title>
    <style>
        @page { margin: 4cm 2.5cm 3cm 2.5cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; line-height: 1.6; color: #333; position: relative; }
        #fondo-plantilla { position: fixed; top: -4cm; left: -2.5cm; width: 21cm; height: 29.7cm; z-index: -2000; }
        #fondo-plantilla img { width: 100%; height: 100%; }
        .header { text-align: center; font-weight: bold; margin-bottom: 40px; }
        .title { font-size: 16px; margin-bottom: 20px; color: #1e293b; }
        .content { text-align: justify; margin-bottom: 25px; }
        .obligaciones-table { width: 85%; margin: 0 auto 30px auto; border-collapse: collapse; }
        .obligaciones-table td { padding: 8px 10px; border-bottom: 1px dashed #cbd5e1; }
        .obligaciones-table td.label { font-weight: bold; width: 60%; color: #0f172a; }
        .estado-ok { color: #047857; font-weight: bold; }
        .estado-mora { color: #b91c1c; font-weight: bold; }
        .calificaciones { margin-bottom: 30px; background-color: rgba(248, 250, 252, 0.85); padding: 15px 20px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 13px; }
        .calificaciones-item { margin-bottom: 8px; }
        .signature { margin-top: 80px; page-break-inside: avoid; }
        .signature-line { width: 250px; border-top: 1px solid #333; margin-bottom: 5px; }
        .footer { position: fixed; bottom: -1cm; left: 0px; right: 0px; text-align: center; font-size: 10px; border-top: 1px solid #cbd5e1; padding-top: 10px; color: #64748b; }
    </style>
</head>
<body>
    <div id="fondo-plantilla">
        <img src="{{ resource_path('views/certificados/pdf/fondo_pdf.jpg') }}" alt="Fondo">
    </div>

    <div class="header">
        <div class="title">LA ASOCIACIÓN GREMIAL DE MINISTROS DE LA IGLESIA PENTECOSTAL UNIDA DE COLOMBIA - CORPENTUNIDA -</div>
        <div style="font-size: 18px; margin-top: 10px;">ESTADO DE CUENTA DE CARTERA</div>
    </div>

    <div class="content">
        El presente documento informa que el(la) asociado(a) <strong>{{ strtoupper($operacion->tercero->nom_ter ?? '') }} {{ strtoupper($operacion->tercero->apl1 ?? '') }} {{ strtoupper($operacion->tercero->apl2 ?? '') }}</strong>,
        identificado(a) con cédula de ciudadanía No. <strong>{{ $operacion->tercero->cod_ter ?? 'N/A' }}</strong>;
        registra el siguiente estado en sus obligaciones financieras con la Asociación a la fecha de corte:
    </div>

    @php
        $lineasAgrupadas = $lineas->groupBy(fn($l) => $l->lineaSia->nombre ?? 'Línea Desconocida');
    @endphp

    <table class="obligaciones-table">
        <tbody>
            @forelse($lineasAgrupadas as $nombreLinea => $grupoLineas)
                @php
                    $peorMora = $grupoLineas->max('dias_mora_automaticos');
                    $esAlDia = $peorMora <= 0;
                @endphp
                <tr>
                    <td class="label">{{ mb_strtoupper($nombreLinea, 'UTF-8') }}</td>
                    <td>
                        <span class="{{ $esAlDia ? 'estado-ok' : 'estado-mora' }}">
                            => {{ $esAlDia ? 'AL DÍA' : "EN MORA ($peorMora días)" }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="2" style="text-align: center;">No registra obligaciones activas.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="content">
        Este documento es de carácter informativo. Si presenta alguna inconsistencia, por favor comuníquese con el área de cartera de CORPENTUNIDA. Expedido a los <strong>{{ now()->format('d') }}</strong> días del mes de <strong>{{ ucfirst(now()->locale('es')->monthName) }}</strong> de <strong>{{ now()->format('Y') }}</strong>.
    </div>

    <div class="signature">
        <div class="signature-line"></div>
        <strong>Área de Cartera</strong><br>
        CORPENTUNIDA
    </div>
</body>
</html>