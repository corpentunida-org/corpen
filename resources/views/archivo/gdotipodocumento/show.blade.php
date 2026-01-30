<x-base-layout>
    @push('styles')
        <style>
            /* Contenedor principal de la ficha */
            .profile-header {
                background: linear-gradient(to right, #f8fafc, #ffffff);
                border-radius: 20px;
                padding: 3rem;
                border: 1px solid #e2e8f0;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            }

            /* Estilo para los ítems de detalle */
            .detail-box {
                background-color: #ffffff;
                border: 1px solid #f1f5f9;
                border-radius: 12px;
                padding: 1.25rem;
                transition: all 0.2s;
            }
            .detail-box:hover { border-color: #e2e8f0; background-color: #fbfcfd; }

            /* Icono lateral de la ficha */
            .icon-shape {
                width: 64px;
                height: 64px;
                background-color: #eef2ff;
                color: #4f46e5;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* Icono de copiar minimalista */
            .copy-trigger {
                cursor: pointer;
                color: #94a3b8;
                transition: color 0.2s;
            }
            .copy-trigger:hover { color: #6366f1; }
        </style>
    @endpush

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                
                {{-- Navegación superior --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('archivo.gdotipodocumento.index') }}" class="btn btn-link text-muted text-decoration-none p-0 small">
                        <i class="bi bi-chevron-left"></i> Volver al listado
                    </a>
                    <div class="badge bg-light text-muted border px-3 py-2 fw-normal">
                        Registro Activo
                    </div>
                </div>

                {{-- Ficha Principal --}}
                <div class="profile-header animate-on-load">
                    <div class="d-flex align-items-start gap-4">
                        <div class="icon-shape shadow-sm">
                            <i class="bi bi-file-earmark-richtext fs-2"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h2 class="fw-bold text-dark m-0" id="documento-nombre" style="letter-spacing: -1px;">
                                    {{ $tipoDocumento->nombre }}
                                </h2>
                                <i class="bi bi-clipboard2-check copy-trigger fs-5" 
                                   onclick="copyToClipboard('documento-nombre', this)"
                                   data-bs-toggle="tooltip" 
                                   data-bs-title="Copiar nombre"></i>
                            </div>
                            <p class="text-muted mb-0">Especificaciones técnicas del tipo de archivo</p>
                        </div>
                        <a href="{{ route('archivo.gdotipodocumento.edit', $tipoDocumento->id) }}" class="btn btn-dark rounded-pill px-4 shadow-sm">
                            <i class="bi bi-pencil-square me-2"></i>Editar
                        </a>
                    </div>

                    <hr class="my-5 opacity-50">

                    {{-- Grilla de detalles --}}
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="detail-box">
                                <small class="text-uppercase text-muted fw-bold ls-1 d-block mb-2" style="font-size: 0.65rem;">
                                    <i class="bi bi-layers me-1 text-primary"></i> Categoría Vinculada
                                </small>
                                <span class="fw-bold text-dark fs-5">
                                    {{ $tipoDocumento->categoria->nombre ?? 'Sin Clasificación' }}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="detail-box">
                                <small class="text-uppercase text-muted fw-bold ls-1 d-block mb-2" style="font-size: 0.65rem;">
                                    <i class="bi bi-database me-1 text-primary"></i> Uso en el Sistema
                                </small>
                                <span class="fw-bold text-dark fs-5">
                                    {{ $tipoDocumento->documentos_count ?? 0 }} 
                                    <span class="fw-normal text-muted fs-6">archivos asociados</span>
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="detail-box">
                                <small class="text-uppercase text-muted fw-bold ls-1 d-block mb-2" style="font-size: 0.65rem;">
                                    <i class="bi bi-hash me-1 text-primary"></i> Identificador Único
                                </small>
                                <code class="fw-bold text-primary fs-5">#REF-{{ str_pad($tipoDocumento->id, 4, '0', STR_PAD_LEFT) }}</code>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="detail-box">
                                <small class="text-uppercase text-muted fw-bold ls-1 d-block mb-2" style="font-size: 0.65rem;">
                                    <i class="bi bi-calendar-check me-1 text-primary"></i> Fecha de Registro
                                </small>
                                <span class="fw-bold text-dark fs-5">
                                    {{ $tipoDocumento->created_at->format('d M, Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer de auditoría --}}
                <div class="text-center mt-5">
                    <p class="small text-muted italic">
                        <i class="bi bi-info-circle me-1"></i> 
                        Última actualización: {{ $tipoDocumento->updated_at->diffForHumans() }}
                    </p>
                </div>

            </div>
        </div>
    </div>
</x-base-layout>

@push('scripts')
<script>
    // Tu función de copiar mejorada visualmente
    function copyToClipboard(elementId, iconElement) {
        const textToCopy = document.getElementById(elementId).innerText;
        navigator.clipboard.writeText(textToCopy).then(() => {
            const originalIcon = iconElement.className;
            iconElement.className = 'bi bi-check2-all text-success fs-5';
            
            // Feedback con Toastr si lo tienes cargado
            if (typeof toastr !== 'undefined') {
                toastr.success('Copiado al portapapeles');
            }

            setTimeout(() => {
                iconElement.className = originalIcon;
            }, 2000);
        });
    }

    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (el) {
        return new bootstrap.Tooltip(el)
    });
</script>
@endpush