<div style="font-family: Arial, sans-serif; max-width: 560px; margin: 0 auto; color: #333;">
    @if ($solicitud->estado === 'aprobada')
        <h2 style="color: #2e7d32;">Tu solicitud de vacaciones fue aprobada</h2>
    @else
        <h2 style="color: #c62828;">Tu solicitud de vacaciones fue rechazada</h2>
    @endif
    <table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
        <tr>
            <td style="padding: 6px 0; color: #666;">Fechas</td>
            <td style="padding: 6px 0;"><strong>{{ $solicitud->fecha_inicio->format('d/m/Y') }} a {{ $solicitud->fecha_fin->format('d/m/Y') }}</strong></td>
        </tr>
        <tr>
            <td style="padding: 6px 0; color: #666;">Días hábiles</td>
            <td style="padding: 6px 0;">{{ $solicitud->dias_habiles }}</td>
        </tr>
        @if ($solicitud->estado === 'rechazada' && $solicitud->motivo_rechazo)
            <tr>
                <td style="padding: 6px 0; color: #666;">Motivo</td>
                <td style="padding: 6px 0;">{{ $solicitud->motivo_rechazo }}</td>
            </tr>
        @endif
    </table>
    <p>
        <a href="{{ route('sgrh.vacacion.solicitud.show', $solicitud) }}" style="background: #1976d2; color: #fff; padding: 10px 18px; border-radius: 20px; text-decoration: none;">
            Ver detalle
        </a>
    </p>
</div>
