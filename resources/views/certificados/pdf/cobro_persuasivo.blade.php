<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cobro Persuasivo - CORPENTUNIDA</title>
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
        .estado-mora { color: #b91c1c; font-weight: bold; }
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
        <div style="font-size: 18px; margin-top: 10px; color: #b91c1c;">NOTIFICACIÓN DE COBRO PERSUASIVO</div>
    </div>

    <div class="content">
        Respetado(a) <strong>{{ strtoupper($operacion->tercero->nom_ter ?? '') }} {{ strtoupper($operacion->tercero->apl1 ?? '') }} {{ strtoupper($operacion->tercero->apl2 ?? '') }}</strong>,
        (C.C. <strong>{{ $operacion->tercero->cod_ter ?? 'N/A' }}</strong>):<br><br>
        De manera fraterna nos dirigimos a usted para informarle que, al revisar nuestro sistema, hemos evidenciado que a la fecha presenta obligaciones en mora con la Asociación en las siguientes carteras:
    </div>

    @php
        $lineasConMora = $lineas->filter(fn($l) => $l->dias_mora_automaticos > 0)
                                ->groupBy(fn($l) => $l->lineaSia->nombre ?? 'Línea Desconocida');
    @endphp

    <table class="obligaciones-table">
        <tbody>
            @forelse($lineasConMora as $nombreLinea => $grupoLineas)
                @php $peorMora = $grupoLineas->max('dias_mora_automaticos'); @endphp
                <tr>
                    <td class="label">{{ mb_strtoupper($nombreLinea, 'UTF-8') }}</td>
                    <td><span class="estado-mora">=> VENCIDO ({{ $peorMora }} días de mora)</span></td>
                </tr>
            @empty
                <tr><td colspan="2" style="text-align: center;">No se encontraron obligaciones vencidas en este bloque.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="content">
        Le invitamos cordialmente a normalizar el estado de su cuenta a la mayor brevedad posible para evitar el paso a etapas de cobro pre-jurídico o jurídico. Si ya realizó el pago, le agradecemos hacer caso omiso a esta comunicación y enviar el soporte correspondiente.<br><br>
        Emitido a los <strong>{{ now()->format('d') }}</strong> días de <strong>{{ ucfirst(now()->locale('es')->monthName) }}</strong> de <strong>{{ now()->format('Y') }}</strong>.
    </div>

    <div class="signature">
        <div class="signature-line"></div>
        <strong>Área de Cartera</strong><br>
        CORPENTUNIDA
    </div>
</body>
</html>