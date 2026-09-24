{{--
    Gráfico de barras horizontales para el PDF (dompdf no ejecuta JS, así que Chart.js no sirve
    aquí) — se dibuja con una tabla simple (label | barra | valor), nada de flexbox: dompdf 2.x
    es solo CSS 2.1 y el flex que tenía antes esta pantalla (justify-content: space-between en la
    barra de Efectividad) es exactamente lo que producía el texto montado/solapado al imprimir.

    Variables esperadas:
    - $titulo: string
    - $labels / $data: arrays paralelos (igual que $chartX['labels']/['data'] del controlador)
    - $color: color hexadecimal de las barras
    - $nota (opcional): texto corto debajo del título, ej. el conteo de días hábiles sin registrar
    - $notaColor (opcional): color del texto de $nota (por defecto rojo, para alertas)
--}}
@php
    $max = count($data ?? []) ? max($data) : 0;
@endphp
<div class="section-title">{{ $titulo }}</div>
@isset($nota)
    <p style="margin: -6px 0 10px 0; font-size: 10.5px; color: {{ $notaColor ?? '#b91c1c' }}; font-weight: bold;">{{ $nota }}</p>
@endisset
@if (empty($labels))
    <p class="sin-datos">Sin datos para este periodo.</p>
@else
    <table class="bar-chart">
        @foreach ($labels as $i => $label)
            @php
                $valor = $data[$i] ?? 0;
                $pct = $max > 0 ? round(($valor / $max) * 100) : 0;
            @endphp
            <tr>
                <td class="bar-label">{{ $label }}</td>
                <td class="bar-track">
                    <div class="bar-track-bg">
                        <div class="bar-track-fill" style="width: {{ max($pct, 4) }}%; background-color: {{ $color }};"></div>
                    </div>
                </td>
                <td class="bar-value">{{ $valor }}</td>
            </tr>
        @endforeach
    </table>
@endif
