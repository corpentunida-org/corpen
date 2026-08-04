<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
        <h5 class="fw-bold">Control de Recaudos y Pasarelas</h5>
        <p class="text-muted small">Monitoreo de pagos (rsv_transacciones_financieras) y configuración de métodos de cobro (rsv_pasarelas).</p>
        <!-- Tabla de transacciones globales -->

        @include('rsv.components.pagination', ['paginator' => $transacciones ?? null])
    </div>
</div>
