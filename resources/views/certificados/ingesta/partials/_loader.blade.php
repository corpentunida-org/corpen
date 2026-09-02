{{-- OVERLAY DE CARGA (MODAL MINIMALISTA) --}}
<div id="loaderLoteOverlay" class="d-none position-fixed w-100 h-100 top-0 start-0 d-flex flex-column justify-content-center align-items-center" style="background: rgba(255, 255, 255, 0.85); z-index: 9999; backdrop-filter: blur(5px);">
    <div class="p-4 rounded-4 shadow-sm bg-white d-flex flex-column align-items-center" style="border: 1px solid var(--c-border); min-width: 250px;">
        <div class="spinner-border text-primary mb-3" role="status" style="width: 2.5rem; height: 2.5rem;">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <h6 class="fw-bold text-dark mb-1">Cargando Lote...</h6>
        <p class="text-muted mb-0" style="font-size: .8rem;">Preparando los registros para revisión</p>
    </div>
</div>

<script>
    function mostrarCargandoLote() {
        // Quitamos el display:none para mostrar la pantalla de carga suavemente
        document.getElementById('loaderLoteOverlay').classList.remove('d-none');
    }
</script>
