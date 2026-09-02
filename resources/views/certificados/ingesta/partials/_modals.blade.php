<!-- ============================================== -->
<!-- MODAL DE CONFIRMACIÓN DE INYECCIÓN             -->
<!-- ============================================== -->
@if(isset($bloqueActivo) && $bloqueActivo)
<div class="modal fade" id="modalConfirmarInyeccion" tabindex="-1" aria-labelledby="modalConfirmarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalConfirmarLabel">
                    <i class="fas fa-exclamation-circle me-2"></i> Confirmar Inyección
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3 text-warning">
                    <i class="fas fa-users-cog fa-3x"></i>
                </div>
                <h4 class="fw-bold mb-2">¿Procesar Lote #{{ $bloqueActivo }}?</h4>
                <p class="text-muted mb-0">
                    Se analizarán <strong>{{ number_format($totalPendientes ?? 0, 0, ',', '.') }}</strong> registros base. El sistema los <strong>agrupará por cliente único</strong> (cédula/NIT) para generar sus operaciones oficiales en Matriz de Cartera y Cobros.
                </p>
                <div class="alert alert-info mt-3 mb-0 text-start" role="alert" style="font-size: .85rem;">
                    <i class="fas fa-info-circle me-2"></i> Solo se inyectarán los clientes que existan en la Maestra de Terceros.
                </div>
            </div>
            <div class="modal-footer bg-light justify-content-center">
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4 rounded-pill shadow-sm" id="btnEjecutarInyeccion" onclick="enviarFormularioInyeccion()">
                    <i class="fas fa-check"></i> Sí, Agrupar e Inyectar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT PARA GESTIONAR EL ENVÍO DESDE EL MODAL -->
<script>
    function enviarFormularioInyeccion() {
        let btn = document.getElementById('btnEjecutarInyeccion');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Procesando clientes...';
        document.getElementById('formInyeccion').submit();
    }
</script>

<!-- SCRIPT PARA MOVER EL MODAL AL BODY (Solución z-index) -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modalInyeccion = document.getElementById('modalConfirmarInyeccion');
        if (modalInyeccion) {
            document.body.appendChild(modalInyeccion);
        }
    });
</script>
@endif
