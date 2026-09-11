<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Paz y Salvo - CORPENTUNIDA</title>
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
        <div style="font-size: 18px; margin-top: 10px;">CERTIFICA QUE:</div>
    </div>

    <div class="content">
        El(la) hermano(a) <strong>{{ strtoupper($operacion->tercero->nom_ter ?? '') }} {{ strtoupper($operacion->tercero->apl1 ?? '') }} {{ strtoupper($operacion->tercero->apl2 ?? '') }}</strong>,
        identificado(a) con cédula de ciudadanía No. <strong>{{ $operacion->tercero->cod_ter ?? 'N/A' }}</strong>;
    </div>

    <div class="content" style="text-align: center; font-size: 16px; margin: 40px 0;">
        Se encuentra a <strong style="font-size: 18px; color: #047857;">PAZ Y SALVO</strong> por todo concepto en sus obligaciones financieras y aportes con la Asociación.
    </div>

    @php
        $lineasAgrupadas = $lineas->groupBy(fn($l) => $l->lineaSia->nombre ?? 'Línea Desconocida');
    @endphp

    <table class="obligaciones-table">
        <tbody>
            @foreach($lineasAgrupadas as $nombreLinea => $grupoLineas)
                <tr>
                    <td class="label">{{ mb_strtoupper($nombreLinea, 'UTF-8') }}</td>
                    <td><span class="estado-ok">=> CANCELADO / AL DÍA</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="content">
        Para constancia, se firma y expide a los <strong>{{ now()->format('d') }}</strong> días del mes de <strong>{{ ucfirst(now()->locale('es')->monthName) }}</strong> de <strong>{{ now()->format('Y') }}</strong>.
    </div>

    <div class="signature">
        <div class="signature-line"></div>
        <strong>Área de Cartera</strong><br>
        CORPENTUNIDA
    </div>
</body>
</html>