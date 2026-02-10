<x-base-layout>
    <div class="app-container py-4">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-header bg-white p-4 border-0 d-flex justify-content-between">
                        <h4 class="fw-bold m-0">Vista Previa del Contenido</h4>
                        <span class="badge bg-primary px-3 py-2">Oficio: {{ $comunicacionSalida->nro_oficio_salida }}</span>
                    </div>
                    <div class="card-body p-5 border-top bg-light text-center">
                        {{-- Simulación de hoja física --}}
                        <div class="bg-white mx-auto shadow-sm p-5 text-start" style="max-width: 600px; min-height: 800px; font-family: 'Times New Roman', serif;">
                            {!! nl2br(e($comunicacionSalida->cuerpo_carta)) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 15px;">
                    <h5 class="fw-bold mb-3">Metadatos de Salida</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><strong>Radicado Origen:</strong> #{{ $comunicacionSalida->correspondencia->nro_radicado }}</li>
                        <li class="mb-2"><strong>Generado por:</strong> {{ $comunicacionSalida->usuario->name }}</li>
                        <li class="mb-2"><strong>Fecha:</strong> {{ $comunicacionSalida->fecha_generacion->format('d/m/Y') }}</li>
                        <li class="mb-2"><strong>Plantilla:</strong> {{ $comunicacionSalida->plantilla->nombre_plantilla ?? 'Manual' }}</li>
                    </ul>
                    <hr>
                    @if($comunicacionSalida->ruta_pdf)
                        <a href="{{ route('correspondencia.comunicaciones-salida.descargarPdf', $comunicacionSalida->id_respuesta) }}" class="btn btn-danger w-100 rounded-pill mb-2">
                            <i class="fas fa-file-pdf me-2"></i> Descargar PDF Firmado
                        </a>
                    @endif
                    <a href="{{ route('correspondencia.comunicaciones-salida.edit', $comunicacionSalida) }}" class="btn btn-outline-primary w-100 rounded-pill">
                        <i class="fas fa-edit me-2"></i> Editar Datos
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>