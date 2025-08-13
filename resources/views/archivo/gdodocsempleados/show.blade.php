<x-base-layout>
    <h4>Detalles del Documento</h4>
    <table class="table table-bordered">
        <tr>
            <th>Empleado</th>
            <td>{{ $doc->empleado->nombre ?? '—' }}</td>
        </tr>
        <tr>
            <th>Tipo Documento</th>
            <td>{{ $doc->tipoDocumento->nombre ?? '—' }}</td>
        </tr>
        <tr>
            <th>Archivo</th>
            <td>
                @if($doc->archivo)
                    <a href="{{ asset('storage/' . $doc->archivo) }}" target="_blank">Ver archivo</a>
                @else
                    —
                @endif
            </td>
        </tr>
        <tr>
            <th>Fecha</th>
            <td>{{ $doc->fecha ?? '—' }}</td>
        </tr>
    </table>
    <a href="{{ route('archivo.gdodocsempleados.index') }}" class="btn btn-secondary">Volver</a>
</x-base-layout>
