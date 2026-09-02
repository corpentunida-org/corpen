{{-- BARRA DE ACCIÓN --}}
@if($totalPendientes > 0 && $bloqueActivo)
@php
    $totalBloque   = $kpi['procesados'] + $kpi['pendientes'] + $kpi['anulados'];
    $pctProcesado  = $totalBloque > 0 ? round(($kpi['procesados'] / $totalBloque) * 100) : 0;
@endphp
{{-- BARRA DE ACCIÓN UNIFICADA (Contexto + Formulario) --}}
<div class="action-bar mb-4 position-relative shadow-sm" style="background: var(--c-surface); border: 1px solid var(--c-border); border-radius: var(--r-xl); z-index: 50;">

    {{-- Barra de progreso superior --}}
    <div class="action-bar-progress" style="height: 3px; border-radius: var(--r-xl) var(--r-xl) 0 0;">
        <div class="action-bar-progress-fill" id="progressFill"
             style="width: {{ $pctProcesado }}%;"
             title="{{ $pctProcesado }}% procesado"></div>
    </div>

    <div class="p-3 p-md-4 pb-5 d-flex flex-column gap-4">

        {{-- 1. Explicación Integrada (Lógica de Negocio) --}}
        <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background-color: var(--c-primary-soft); border: 1px dashed #c7d2fe;">
            <i class="fas fa-info-circle mt-1" style="color: var(--c-primary); font-size: 1.1rem;"></i>
            <div>
                <h6 class="fw-bold mb-1" style="color: var(--c-primary-h); font-size: .85rem;">
                    ¿Por qué es necesario procesar este lote?
                </h6>
                <p class="mb-0" style="font-size: .75rem; color: #3730a3; line-height: 1.5;">
                    Las facturas que subiste están en una <strong>zona de prueba temporal (Staging)</strong>. Para que afecten la cartera del ERP, debes asignarles su modelo contable. El sistema validará los terceros y trasladará los datos para convertirlos en <strong>operaciones oficiales</strong>.
                </p>
            </div>
        </div>

        {{-- 2. Resumen y Formulario de Acción --}}
        <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-4">

            {{-- Estado Listo --}}
            <div class="d-flex align-items-start gap-3">
                <div class="pulse-dot mt-2" style="width: 10px; height: 10px;"></div>
                <div>
                    <h3 class="fw-bold text-dark mb-1" style="font-size: 1.15rem; letter-spacing: -0.02em;">
                        Listo para inyectar <span style="color: var(--c-primary);">{{ number_format($totalPendientes, 0, ',', '.') }}</span> registros
                    </h3>
                    <p class="mb-0 text-warning fw-semibold" style="font-size: .75rem;">
                        <i class="fas fa-lock me-1"></i> Acción irreversible en API Cartera.
                    </p>
                </div>
            </div>

            {{-- Formulario Minimalista (Alineado obligatoriamente a la derecha) --}}
            <form action="{{ route('certificados.ingesta.inyectar') }}"
                method="POST" id="formInyeccion"
                class="d-flex flex-wrap align-items-center justify-content-end gap-2 m-0 ms-auto">
                @csrf
                <input type="hidden" name="bloque_origen" value="{{ $bloqueActivo }}">

                {{-- Selector Estado --}}
                <div data-tooltip="1. Estado de la operación">
                    <select name="id_car_sia_estados"
                            class="select-chip"
                            required
                            aria-label="Estado"
                            style="padding-top: 0.45rem; padding-bottom: 0.45rem;"
                            onchange="alert('En esta etapa de inyección solo se permite la opción: ' + this.options[0].text); this.selectedIndex = 0;">
                        @foreach($estados as $estado)
                            <option value="{{ $estado->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $estado->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Selector Tipo --}}
                <div data-tooltip="2. Tipo de operación">
                    <select name="id_car_sia_tipos"
                            class="select-chip"
                            required
                            aria-label="Tipo"
                            style="padding-top: 0.45rem; padding-bottom: 0.45rem;"
                            onchange="alert('En esta etapa de inyección solo se permite la opción: ' + this.options[0].text); this.selectedIndex = 0;">
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Botón que abre el modal en lugar de enviar el form directamente --}}
                <button type="button" class="btn-g btn-primary-g rounded-pill ms-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalConfirmarInyeccion" style="padding: 0.45rem 1.4rem;">
                    <i class="fas fa-paper-plane btn-ico" style="font-size: .85rem;"></i>
                    <span class="btn-label">Procesar Lote</span>
                </button>
            </form>
        </div>

        <!-- ============================================== -->
        <!-- MODAL DE CONFIRMACIÓN DE INYECCIÓN -->
        <!-- ============================================== -->
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
                            <i class="fas fa-database fa-3x"></i>
                        </div>
                        <h4 class="fw-bold mb-2">¿Procesar Lote #{{ $bloqueActivo }}?</h4>
                        <p class="text-muted mb-0">
                            Estás a punto de inyectar <strong>{{ number_format($totalPendientes, 0, ',', '.') }}</strong> registros pendientes al sistema central (SIA Cartera).
                        </p>
                        <div class="alert alert-danger mt-3 mb-0 text-start" role="alert">
                            <i class="fas fa-lock me-2"></i> Esta acción es oficial y no se puede deshacer.
                        </div>
                    </div>
                    <div class="modal-footer bg-light justify-content-center">
                        <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary px-4 rounded-pill shadow-sm" id="btnEjecutarInyeccion" onclick="enviarFormularioInyeccion()">
                            <i class="fas fa-check"></i> Sí, Procesar Registros
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- SCRIPT PARA GESTIONAR EL ENVÍO DESDE EL MODAL -->
        <script>
            function enviarFormularioInyeccion() {
                let btn = document.getElementById('btnEjecutarInyeccion');
                // Bloquear botón y mostrar spinner
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Inyectando...';

                // Enviar el formulario por debajo
                document.getElementById('formInyeccion').submit();
            }
        </script>
    </div>
</div>
@endif
