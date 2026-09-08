<div style="font-family: Arial, sans-serif; max-width: 560px; margin: 0 auto; color: #333;">
    <h2 style="color: #1976d2;">Nueva solicitud de vacaciones</h2>
    <p>
        <strong>{{ $solicitud->empleado->nombre_completo }}</strong> solicitó vacaciones y queda pendiente de tu aprobación.
    </p>
    <table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
        <tr>
            <td style="padding: 6px 0; color: #666;">Fechas</td>
            <td style="padding: 6px 0;"><strong>{{ $solicitud->fecha_inicio->format('d/m/Y') }} a {{ $solicitud->fecha_fin->format('d/m/Y') }}</strong></td>
        </tr>
        <tr>
            <td style="padding: 6px 0; color: #666;">Días hábiles</td>
            <td style="padding: 6px 0;">{{ $solicitud->dias_habiles }}</td>
        </tr>
        @if ($solicitud->es_adelantada)
            <tr>
                <td style="padding: 6px 0; color: #666;">Tipo</td>
                <td style="padding: 6px 0;">Vacaciones adelantadas</td>
            </tr>
        @endif
        @if ($solicitud->observaciones)
            <tr>
                <td style="padding: 6px 0; color: #666;">Observaciones</td>
                <td style="padding: 6px 0;">{{ $solicitud->observaciones }}</td>
            </tr>
        @endif
    </table>
    <p>
        <a href="{{ route('sgrh.vacacion.solicitud.show', $solicitud) }}" style="background: #1976d2; color: #fff; padding: 10px 18px; border-radius: 20px; text-decoration: none;">
            Ver y resolver solicitud
        </a>
    </p>
</div>
