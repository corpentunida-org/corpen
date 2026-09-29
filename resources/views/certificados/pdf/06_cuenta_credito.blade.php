<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificación de Saldos Créditos - CORPENTUNIDA</title>
    <style>
        /* 1. MÁRGENES AJUSTADOS PARA UNA HOJA LIMPIA */
        @page { margin: 2.5cm 2cm 2.5cm 2cm; }

        body {
            font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #1e293b;
            text-align: justify;
        }

        #fondo-plantilla {
            position: fixed; top: -2.5cm; left: -2cm; width: 21.5cm; height: 29.7cm; z-index: -2000;
        }
        #fondo-plantilla img { width: 100%; height: 100%; }

        /* 2. PIE DE PÁGINA (Paginador) */
        .footer {
            position: fixed; bottom: -2cm; right: 0cm; text-align: right;
            font-size: 8.5pt; color: #475569; font-weight: bold;
        }
        .page-number:before { content: "Página " counter(page) " de " counter(pages); }

        /* 3. ENCABEZADO CORPORATIVO */
        .header { text-align: center; margin-bottom: 10px; }
        .title {
            font-size: 13pt; color: #0f172a; font-weight: bold;
            text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;
        }

        .content { margin-bottom: 10px; font-size: 10pt; color: #334155; }
        strong { color: #0f172a; }

        /* ========================================================
            ESTILOS DE LA TABLA IDÉNTICOS A LA REFERENCIA
            ======================================================== */
        .table-cert {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9.5pt;
            border: 1px solid #0f172a;
        }
        .table-cert td {
            border: 1px solid #0f172a;
            padding: 6px 10px;
            color: #1e293b;
            vertical-align: middle;
        }

        /* 4. BLOQUE PRINCIPAL ASEGURADO EN HOJA 1 */
        .bloque-pagina-1 {
            page-break-after: always;
        }

        /* 5. ESTILOS MANUAL PRO (Pagos - Página 2) */
        .manual-pro {
            background-color: #f8fafc; border: 1px solid #cbd5e1;
            border-top: 4px solid #0284c7; border-radius: 6px;
            padding: 16px 20px; margin-top: 5px; margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .manual-header {
            font-size: 11pt; color: #0f172a; font-weight: bold; margin-bottom: 12px;
            border-bottom: 1px dashed #cbd5e1; padding-bottom: 8px;
        }
        .step-table { width: 100%; border-collapse: collapse; }
        .step-table td { padding: 5px 0; vertical-align: top; border: none; }
        .step-num-container { width: 28px; text-align: center; }
        .step-num {
            width: 20px; height: 20px; background-color: #0284c7; color: #ffffff;
            text-align: center; border-radius: 50%; font-weight: bold; font-size: 8.5pt;
            display: inline-block; line-height: 20px;
        }
        .step-text { padding-left: 8px; font-size: 9.5pt; color: #475569; line-height: 1.35; text-align: left; }
        .step-text strong { color: #0f172a; }
        .highlight-box {
            background-color: #e0f2fe; color: #0369a1; padding: 2px 6px;
            border-radius: 3px; font-weight: bold; font-size: 9pt; border: 1px solid #bae6fd;
        }
        .btn-portal {
            background-color: #0f172a; color: #ffffff !important; text-decoration: none;
            padding: 2px 8px; border-radius: 3px; font-size: 8.5pt; font-weight: bold; display: inline-block;
        }
        .opcion-pago-title {
            font-size: 10pt; color: #0284c7; font-weight: bold; margin-top: 12px; margin-bottom: 6px;
            background-color: #e0f2fe; padding: 6px 10px; border-left: 4px solid #0284c7;
            border-radius: 0 4px 4px 0;
        }
        .img-banco {
            width: 100%;
            max-width: 450px; /* <--- Aumenta o disminuye este valor según el tamaño que desees */
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            margin-top: 4px;
            display: block;
            margin-left: auto;
            margin-right: auto;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    @php
        $payload = [];
        if(isset($lineas) && $lineas->count() > 0) {
            $primeraLinea = $lineas->first();
            $payload = is_string($primeraLinea->payload_documento) ? json_decode($primeraLinea->payload_documento, true) : (array) ($primeraLinea->payload_documento ?? []);
        }

        $quien_solicita       = $payload['quien_solicita'] ?? '_________________';
        $cco                  = $payload['cco'] ?? 'N/A';
        $congregacion         = $payload['congregacion'] ?? 'N/A';
        $distrito             = $payload['distrito'] ?? 'N/A';
        $fecha_corte          = !empty($payload['fecha_corte']) ? \Carbon\Carbon::parse($payload['fecha_corte'])->format('d/m/Y') : now()->format('d/m/Y');

        $saldo_capital        = isset($payload['saldo_capital']) ? number_format((float)$payload['saldo_capital'], 2, ',', '.') : '0,00';
        $valor_total          = isset($payload['valor_total']) ? number_format((float)$payload['valor_total'], 2, ',', '.') : '0,00';

        $intereses_vencidos   = isset($payload['intereses_vencidos']) ? number_format((float)$payload['intereses_vencidos'], 2, ',', '.') : '0,00';
        $seguro_vencido       = isset($payload['seguro_vencido']) ? number_format((float)$payload['seguro_vencido'], 2, ',', '.') : '0,00';
        $seguro_hogar_vencido = isset($payload['seguro_hogar_vencido']) ? number_format((float)$payload['seguro_hogar_vencido'], 2, ',', '.') : '0,00';

        $intereses_acuerdo    = isset($payload['intereses_acuerdo']) ? number_format((float)$payload['intereses_acuerdo'], 2, ',', '.') : '0,00';
        $seguro_acuerdo       = isset($payload['seguro_acuerdo']) ? number_format((float)$payload['seguro_acuerdo'], 2, ',', '.') : '0,00';
        $seguro_hogar_acuerdo = isset($payload['seguro_hogar_acuerdo']) ? number_format((float)$payload['seguro_hogar_acuerdo'], 2, ',', '.') : '0,00';

        $userAuth = auth()->user();
        $nombreUsuario = $userAuth->name ?? 'Funcionario Corpentunida';
        $emailUsuario = $userAuth->email ?? 'archivo@corpentunida.org.co';
    @endphp

    <div id="fondo-plantilla">
        <img src="{{ resource_path('views/certificados/pdf/fondo_pdf.jpg') }}" alt="Fondo">
    </div>

    <div class="footer">
        <span class="page-number"></span>
    </div>
    <br><br>
    {{-- =========================================================
         CONTENEDOR DE LA PÁGINA 1: CERTIFICACIÓN Y FIRMA (FORZADO)
         ========================================================= --}}
         <br><br><br>
    <div class="bloque-pagina-1">
        <div class="header">
            <div class="title">Certificación</div>
        </div>

        <div class="content">
            Fecha: Bogotá D.C., <strong>{{ now()->format('d \d\e F \d\e Y') }}</strong><br><br>
            <strong>Para:</strong> {{ mb_strtoupper($quien_solicita) }}<br>
            <strong>Asunto:</strong> Certificación de saldos<br><br>

            <div style="text-align: center; font-weight: bold;">
                {{ mb_strtoupper($operacion->linea_credito ?? $lineas->first()?->lineaSia?->nombre ?? 'CRÉDITO ASOCIADO') }}<br>
                {{ $cco }} {{ mb_strtoupper($congregacion) }}
            </div><br>

            Reciba un cordial saludo<br><br>
            En respuesta a la solicitud presentada, se certifica que el crédito, correspondiente al pastor <strong>{{ mb_strtoupper($operacion->tercero->nom_ter ?? '') }} {{ mb_strtoupper($operacion->tercero->apl1 ?? '') }} {{ mb_strtoupper($operacion->tercero->apl2 ?? '') }}</strong>, identificado con cédula de ciudadanía No. <strong>{{ $operacion->tercero->cod_ter ?? 'N/A' }}</strong>, del Distrito <strong>#{{ $distrito }}</strong>, presenta a la fecha la siguiente información:
        </div>

        {{-- TABLA DE VALORES EXACTA A LA IMAGEN --}}
        <table class="table-cert">
            <tr>
                <td><strong>SALDO DEL CRÉDITO (Capital)</strong></td>
                <td align="right"><strong>${{ $saldo_capital }}</strong></td>
            </tr>
            <tr>
                <td>Interés {{ $fecha_corte }}</td>
                <td align="right">${{ $intereses_vencidos }}</td>
            </tr>
            <tr>
                <td>Seguro {{ $fecha_corte }}</td>
                <td align="right">${{ $seguro_vencido }}</td>
            </tr>
            <tr>
                <td>Seguro todo riesgo {{ $fecha_corte }}</td>
                <td align="right">${{ $seguro_hogar_vencido }}</td>
            </tr>
            <tr style="background-color: #f0f9ff;">
                <td>Interés Acuerdo de pago</td>
                <td align="right">${{ $intereses_acuerdo }}</td>
            </tr>
            <tr style="background-color: #f0f9ff;">
                <td>Seguro Acuerdo de pago</td>
                <td align="right">${{ $seguro_acuerdo }}</td>
            </tr>
            <tr style="background-color: #f0f9ff;">
                <td>Seguro todo riesgo Acuerdo de pago</td>
                <td align="right">${{ $seguro_hogar_acuerdo }}</td>
            </tr>
            <tr style="background-color: #f1f5f9;">
                <td><strong>TOTAL, DEUDA {{ $fecha_corte }}</strong></td>
                <td align="right"><strong>${{ $valor_total }}</strong></td>
            </tr>
        </table>

        <div class="content" style="margin-top: 6px; margin-bottom: 8px;">
            La presente certificación se expide como soporte interno
        </div>
        <br>
        <div style="font-size: 10pt; color: #0f172a; margin-bottom: 4px;">
            Cordialmente,
        </div>
        <br>

        {{-- SECCIÓN DE FIRMA MÁS PEQUEÑA Y MÁS CURSIVA --}}
        <div style="margin-top: 2px;">
            <div style="font-family: 'Brush Script MT', 'Segoe Script', 'Lucida Handwriting', cursive; font-style: italic; font-size: 16pt; color: #0f172a; margin-bottom: -6px; line-height: 1;">
                {{ $nombreUsuario }}
            </div>
            <div style="border-top: 1px solid #0f172a; width: 260px; padding-top: 4px; margin-top: 2px;">
                <strong style="font-size: 9.5pt; text-transform: uppercase;">
                    {{ $nombreUsuario }}
                </strong><br>
                <span style="font-size: 9pt; color: #475569;">Analista Gestión Documental - Corpentunida</span><br>
                <span style="font-size: 8.5pt; color: #475569;">Celular: 3208382029</span><br>
                <span style="font-size: 8.5pt; color: #475569;">Fijo: 60 1 208 71 71 (Ext. 11)</span><br>
                <span style="font-size: 8.5pt; color: #475569;">Correo: {{ $emailUsuario }}</span>
            </div>
        </div>
    </div>


    {{-- =========================================================
         PÁGINA 2: GUÍA RÁPIDA DE PAGOS
         ========================================================= --}}

    <br><br><br><br>
    <div class="manual-pro">
        <div class="manual-header">
            Guía Rápida de Pagos
        </div>

        <div>
            <div class="opcion-pago-title">Opción 1: Pago en Línea (AvalPay Center)</div>
            <table class="step-table">
                <tr>
                    <td class="step-num-container"><span class="step-num">1</span></td>
                    <td class="step-text"><strong>Inicie su pago:</strong> Ingrese al portal oficial <a href="https://corpentunida.org.co/" target="_blank" class="btn-portal">corpentunida.org.co</a>.</td>
                </tr>
                <tr>
                    <td class="step-num-container"><span class="step-num">2</span></td>
                    <td class="step-text"><strong>Valide el destinatario:</strong> Asegúrese de que el portal indique: <span class="highlight-box">Corpentunida Nit 8605094515</span>.</td>
                </tr>
                <tr>
                    <td class="step-num-container"><span class="step-num">3</span></td>
                    <td class="step-text"><strong>Identifique su obligación:</strong> En el campo <strong>Número referencia de pago</strong>, digite su cédula o referencia.</td>
                </tr>
                <tr>
                    <td class="step-num-container"><span class="step-num">4</span></td>
                    <td class="step-text"><strong>Confirme y pague:</strong> El sistema validará la estructura y desplegará el <strong>Valor a pagar</strong>.</td>
                </tr>
            </table>
        </div>

        <div style="margin-top: 10px;">
            <div class="opcion-pago-title">Opción 2: Consignación Presencial (Banco de Bogotá)</div>
            <table class="step-table">
                <tr>
                    <td class="step-num-container"><span class="step-num">1</span></td>
                    <td class="step-text"><strong>Solicite el formato:</strong> Pida un "Comprobante de Pago Universal Individual" en el Banco de Bogotá.</td>
                </tr>
                <tr>
                    <td class="step-num-container"><span class="step-num">2</span></td>
                    <td class="step-text"><strong>Diligencie los datos:</strong> Marque <strong>Cuenta Corriente</strong> No. <strong>019134618</strong>. Convenio: <strong>ASOCIACIÓN GREMIAL DE MINISTROS IPUC</strong>.</td>
                </tr>
                <tr>
                    <td class="step-num-container"><span class="step-num">3</span></td>
                    <td class="step-text"><strong>Referencias:</strong> • Ref. 1: Cédula Pastor. • Ref. 2: Número de Referencia de Pago.</td>
                </tr>
            </table>
        </div>

        <div style="text-align: center; margin-top: 8px;">
            <img src="{{ resource_path('views/certificados/pdf/model_pago.png') }}" class="img-banco" style="max-width: 350px;" alt="Modelo Consignación Banco de Bogotá">
        </div>
    </div>

</body>
</html>
