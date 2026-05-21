<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Informe de Indicadores TIC</title>
    
    <style>
        /* =========================================
           CONFIGURACIÓN GLOBAL Y DOMPDF
           ========================================= */
        @page {
            margin: 40px 45px;
        }
        
        body {
            /* VOLVEMOS A LA FUENTE SEGURA QUE NO ROMPE EL TEXTO */
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 10px;
            color: #2c3e50;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        /* =========================================
           ENCABEZADO (Estilo Membrete Corporativo)
           ========================================= */
        .header-table {
            width: 100%;
            margin-bottom: 25px;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 15px;
        }
        
        .header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .header-title-box {
            border-left: 4px solid #0d6efd;
            padding-left: 12px;
        }

        .header-title {
            font-size: 22px;
            font-weight: bold;
            color: #1a252f;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .header-subtitle {
            color: #7f8c8d;
            font-size: 11px;
            margin-top: 3px;
            font-weight: normal;
        }

        .header-meta {
            text-align: right;
            font-size: 9.5px;
            color: #555555;
            line-height: 1.6;
        }

        .header-meta strong {
            color: #2c3e50;
        }

        /* =========================================
           WIDGET DE RESUMEN (KPI)
           ========================================= */
        .summary-container {
            width: 100%;
            margin-bottom: 25px;
        }

        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-top: 4px solid #0d6efd;
            padding: 15px 20px;
            text-align: center;
            border-radius: 6px;
        }
        
        .summary-title {
            font-size: 10px;
            font-weight: bold;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 8px 0;
        }
        
        .summary-value {
            font-size: 26px;
            font-weight: bold;
            color: #0d6efd;
            margin: 0;
        }

        /* =========================================
           TABLA DE DATOS PRINCIPAL
           ========================================= */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th {
            background-color: #0d6efd;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 10px;
            text-align: left;
        }

        .data-table td {
            padding: 10px;
            border-bottom: 1px solid #ecf0f1;
            vertical-align: middle;
            font-size: 10px;
            color: #34495e;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #fdfdfe;
        }

        .formula-text {
            font-size: 9px;
            color: #7f8c8d;
            font-style: italic;
        }

        /* =========================================
           BADGES (Etiquetas de Frecuencia)
           ========================================= */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            font-size: 8.5px;
            font-weight: bold;
            border-radius: 12px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-primary { background-color: #e3f2fd; color: #0d47a1; border: 1px solid #bbdefb; }   
        .badge-warning { background-color: #fff8e1; color: #f57f17; border: 1px solid #ffecb3; }   
        .badge-info    { background-color: #e0f7fa; color: #006064; border: 1px solid #b2ebf2; }      
        .badge-success { background-color: #e8f5e9; color: #1b5e20; border: 1px solid #c8e6c9; }   

        /* =========================================
           UTILIDADES Y FOOTER
           ========================================= */
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .fw-bold { font-weight: bold !important; }
        
        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 30px;
            border-top: 1px solid #ecf0f1;
            padding-top: 10px;
            font-size: 8px;
            color: #95a5a6;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .page-number:before {
            content: counter(page);
        }
    </style>
</head>
<body>

    <div class="footer">
        Documento confidencial generado por el Sistema de Indicadores TIC &nbsp;|&nbsp; Página <span class="page-number"></span>
    </div>

    <table class="header-table">
        <tr>
            <td>
                <div class="header-title-box">
                    <h1 class="header-title">Informe de Indicadores</h1>
                    <div class="header-subtitle">Área de Tecnología de la Información (TIC)</div>
                </div>
            </td>
            <td class="header-meta">
                <strong>Generado por:</strong> {{ auth()->user()->name ?? 'Sistema' }}<br>
                <strong>Fecha de emisión:</strong> {{ now()->format('d/m/Y') }}<br>
                <strong>Hora:</strong> {{ now()->format('H:i') }}
            </td>
        </tr>
    </table>

    @if(isset($promedioAlcanzados))
    <div class="summary-container">
        <table style="width: 100%; border:none;">
            <tr>
                <td style="width: 30%;"></td>
                <td style="width: 40%;">
                    <div class="summary-box">
                        <p class="summary-title">Promedio General Alcanzado</p>
                        <p class="summary-value">{{ number_format($promedioAlcanzados, 0) }}%</p>
                    </div>
                </td>
                <td style="width: 30%;"></td>
            </tr>
        </table>
    </div>
    @endif

    <table class="data-table">
        <thead>
            <tr>
                <th width="6%" class="text-center">ID</th>
                <th width="24%">Nombre del Indicador</th>
                <th width="30%">Fórmula / Cálculo</th>
                <th width="10%" class="text-center">Meta</th>
                <th width="15%" class="text-center">Frecuencia</th>
                <th width="15%" class="text-right">Resultado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($indicators as $ind)
                <tr>
                    <td class="text-center fw-bold" style="color: #0d6efd;">#{{ $ind->id }}</td>
                    <td class="fw-bold">{{ $ind->nombre }}</td>
                    
                    {{-- LIMPIEZA DE CÁLCULO: Por si acaso también hay símbolos raros aquí --}}
                    <td class="formula-text">{{ str_replace(['≥', '≤'], ['>=', '<='], $ind->calculo) }}</td>
                    
                    {{-- LA SOLUCIÓN MÁGICA: Convertimos ≥ a >= usando PHP --}}
                    <td class="text-center fw-bold">{{ str_replace(['≥', '≤'], ['>=', '<='], $ind->meta) }}</td>
                    
                    <td class="text-center">
                        @php
                            $badgeClass = match($ind->frecuencia) {
                                'Mensual' => 'badge-primary',
                                'Trimestral' => 'badge-warning',
                                'Semestral' => 'badge-info',
                                default => 'badge-success',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $ind->frecuencia }}</span>
                    </td>
                    <td class="text-right fw-bold" style="font-size: 11px;">
                        {{ $ind->indicador_calculado !== null 
                            ? number_format($ind->indicador_calculado, 1) . ' %' 
                            : '—' 
                        }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 30px; color: #7f8c8d; font-style: italic;">
                        No se encontraron indicadores registrados para este informe.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>