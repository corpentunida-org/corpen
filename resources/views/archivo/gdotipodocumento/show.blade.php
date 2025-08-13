<x-base-layout>
    <div class="card">
        <div class="card-body">
            <h5 class="fw-bold mb-4">Detalle del Tipo de Documento</h5>
            <div class="mb-3">
                <strong>ID:</strong> {{ $tipoDocumento->id }}
            </div>
            <div class="mb-3">
                <strong>Nombre:</strong> {{ $tipoDocumento->nombre }}
            </div>
            <a href="{{ route('archivo.gdotipodocumento.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</x-base-layout>
