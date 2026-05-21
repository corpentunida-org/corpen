<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Indicadores TIC</title>
    <style>
        /* Configuraciones generales preparadas para DomPDF */
        @page {
            margin: 40px 40px;
        }
        
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* Encabezado del documento */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .header-table td {
            border: none;
            padding: 0;
        }

        .header-title {
            font-size: 20px;
            font-weight: bold;
            color: #1a1a1a;
            text-transform: uppercase;
        }

        .header-meta {
            text-align: right;
            font-size: 10px;
            color: #6c757d;
        }

        /* Tarjeta de Resumen (Promedio) */
        .summary-box {
            background-color: #f8f9fa;
            border-left: 4px solid #0d6efd;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .summary-title {
            font-size: 12px;
            font-weight: bold;
            color: #495057;
            margin: 0 0 5px 0;
        }
        
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #0d6efd;
        }

        /* Tabla Principal */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th {
            background-color: #f4f6f9;
            color: #495057;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 10px 8px;
            border-bottom: 2px solid #dee2e6;
            text-align: left;
        }

        .data-table td {
            padding: 8px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
            color: #212529;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        /* Utilidades de texto */
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .fw-bold { font-weight: bold !important; }
        
        /* Badges de Frecuencia (Simulando los de tu vista web) */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
            text-align: center;
        }
        .badge-mensual { background-color: #cce5ff; color: #004085; }
        .badge-trimestral { background-color: #fff3cd; color: #856404; }
        .badge-semestral { background-color: #d1ecf1; color: #0c5460; }
        .badge-anual { background-color: #d4edda; color: #155724; }
        .badge-default { background-color: #e2e3e5; color: #383d41; }

        /* Pie de página */
        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 30px;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
            font-size: 9px;
            color: #adb5bd;
            text-align: center;
        }
        
        /* Para mostrar números de página en DomPDF */
        .page-number:before {
            content: counter(page);
        }
    </style>
</head>
<body>

    {{-- PIE DE PÁGINA (Se declara arriba para que DomPDF lo repita en cada página) --}}
    <div class="footer">
        Documento generado automáticamente por el sistema de Indicadores TIC | Página <span class="page-number"></span>
    </div>

    {{-- ENCABEZADO CORPORATIVO --}}
    <table class="header-table">
        <tr>
            <td>
                {{-- Puedes agregar un logo aquí descomentando la siguiente línea --}}
                {{-- <img src="{{ public_path('img/logo.png') }}" alt="Logo" width="120"> --}}
                <div class="header-title">Informe de Indicadores</div>
                <div style="color: #6c757d; font-size: 11px; margin-top: 4px;">Área de Tecnología (TIC)</div>
            </td>
            <td class="header-meta">
                <strong>Generado por:</strong> {{ auth()->user()->name ?? 'Sistema' }}<br>
                <strong>Fecha de emisión:</strong> {{ now()->format('d/m/Y') }}<br>
                <strong>Hora:</strong> {{ now()->format('H:i') }}
            </td>
        </tr>
    </table>

    {{-- RESUMEN EJECUTIVO (Integrado de la vista principal) --}}
    @if(isset($promedioAlcanzados))
    <div class="summary-box">
        <p class="summary-title">PROMEDIO DE INDICADORES ALCANZADOS</p>
        <div class="summary-value">{{ number_format($promedioAlcanzados, 0) }}%</div>
    </div>
    @endif

    {{-- TABLA DE DATOS --}}
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">ID</th>
                <th width="25%">Nombre del Indicador</th>
                <th width="30%">Fórmula / Cálculo</th>
                <th width="10%" class="text-center">Meta</th>
                <th width="15%" class="text-center">Frecuencia</th>
                <th width="15%" class="text-right">Resultado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($indicators as $ind)
                <tr>
                    <td class="text-center fw-bold">{{ $ind->id }}</td>
                    <td>{{ $ind->nombre }}</td>
                    <td style="font-size: 10px; color: #555;">{{ $ind->calculo }}</td>
                    <td class="text-center fw-bold">{{ $ind->meta }}</td>
                    <td class="text-center">
                        {{-- Lógica de Badges reflejada para el PDF --}}
                        @php
                            $badgeClass = match($ind->frecuencia) {
                                'Mensual' => 'badge-mensual',
                                'Trimestral' => 'badge-trimestral',
                                'Semestral' => 'badge-semestral',
                                'Anual' => 'badge-anual',
                                default => 'badge-default',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $ind->frecuencia }}</span>
                    </td>
                    <td class="text-right fw-bold">
                        {{ is_numeric($ind->indicador_calculado) 
                            ? number_format($ind->indicador_calculado, 1) . ' %' 
                            : '—' 
                        }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #6c757d;">
                        No se encontraron indicadores registrados para este informe.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>