<x-base-layout>

    {{-- Se asume que los estilos están en un archivo CSS externo --}}

    <div class="row justify-content-center animate-on-load">
        <div class="col-lg-8">
            <div class="card card-friendly card-friendly-primary h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <i class="bi bi-file-earmark-text fs-1 text-primary me-3"></i>
                        <div>
                            <div class="d-flex align-items-center">
                                <h4 class="card-title mb-0" id="documento-nombre">{{ $tipoDocumento->nombre }}</h4>
                                <i class="bi bi-clipboard ms-2 copy-icon" 
                                   onclick="copyToClipboard('documento-nombre', this)"
                                   data-bs-toggle="tooltip" 
                                   data-bs-title="Copiar nombre"></i>
                            </div>
                            <small class="text-muted">Detalles del Tipo de Documento</small>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex flex-column gap-3 mt-4">
                        <div class="detail-item">
                            <i class="bi bi-hash text-secondary"></i>
                            <div>
                                <small class="text-muted">ID del Registro</small>
                                <p class="fw-medium mb-0">{{ $tipoDocumento->id }}</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN DE BOTONES CON CORRECCIÓN FINAL --}}
    <div class="row mt-4 justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-end gap-3">
                <a href="{{ route('archivo.gdotipodocumento.index') }}" 
                   class="btn btn-outline-secondary rounded-pill px-4 btn-hover-lift" 
                   data-bs-toggle="tooltip" 
                   data-bs-title="Regresar al listado">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
                
                {{-- MÉTODO A PRUEBA DE BALAS PARA COMENTAR EL BOTÓN OPCIONAL --}}
                @if (false)
                    <a href="{{ route('archivo.gdotipodocumento.edit', $tipoDocumento->id) }}" 
                       class="btn btn-primary rounded-pill px-4 btn-hover-lift" 
                       data-bs-toggle="tooltip" 
                       data-bs-title="Modificar este tipo">
                        <i class="bi bi-pencil-square me-1"></i> Editar
                    </a>
                @endif
                
            </div>
        </div>
    </div>

</x-base-layout>

@push('scripts')
<script>
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Función de Copiar
    function copyToClipboard(elementId, iconElement) {
        const textToCopy = document.getElementById(elementId).innerText;
        navigator.clipboard.writeText(textToCopy).then(() => {
            const originalIcon = iconElement.className;
            const originalTitle = iconElement.getAttribute('data-bs-title');
            const tooltip = bootstrap.Tooltip.getInstance(iconElement);
            if (tooltip) tooltip.dispose();
            
            iconElement.className = 'bi bi-check-lg text-success ms-2';
            iconElement.setAttribute('data-bs-title', '¡Copiado!');
            const successTooltip = new bootstrap.Tooltip(iconElement);
            successTooltip.show();

            setTimeout(() => {
                successTooltip.dispose();
                iconElement.className = originalIcon;
                iconElement.setAttribute('data-bs-title', originalTitle);
                new bootstrap.Tooltip(iconElement);
            }, 2000);
        }).catch(err => console.error('Error al copiar: ', err));
    }
</script>
@endpush