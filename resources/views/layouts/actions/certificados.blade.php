{{-- DENTRO DEL MENU (ul) --}}
<li class="nxl-item nxl-hasmenu active nxl-trigger">
    <a class="nxl-link" href="javascript:void(0)">
        <span class="nxl-micon"><i class="bi bi-wallet2"></i></span>
        <span class="nxl-mtext">SIA Cartera</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        {{-- ========================================================= --}}
        {{-- 3. GESTIÓN DE CARTERA / BACKOFFICE [OperacionController]  --}}
        {{-- ========================================================= --}}
        <li class="px-4 py-1 mt-2">
            <span class="fs-10 fw-bolder text-primary text-uppercase tracking-wider">Backoffice (Motor)</span>
        </li>

        <li class="nxl-item">
            <a class="nxl-link" href="#" {{-- href="{{ route('sia.operaciones.index') }}" --}}>
                <i class="bi bi-file-earmark-text me-2"></i>
                <span class="nxl-mtext">Motor de Operaciones</span>
                {{-- Tablas: car_sia_operaciones, car_sia_estados_operacion --}}
            </a>
        </li>
        <li class="nxl-item">
            <a class="nxl-link" href="#" {{-- href="{{ route('sia.lineas.index') }}" --}}>
                <i class="bi bi-diagram-3 me-2"></i>
                <span class="nxl-mtext">Líneas y Créditos</span>
                {{-- Tablas: car_sia_operaciones_lineas --}}
            </a>
        </li>
        <li class="nxl-item">
            <a class="nxl-link" href="#" {{-- href="{{ route('sia.alertas.index') }}" --}}>
                <i class="bi bi-bell me-2"></i>
                <span class="nxl-mtext">Monitor de Alertas</span>
                {{-- Tablas: car_sia_operaciones_alertas, car_sia_operaciones_config --}}
            </a>
        </li>

        {{-- ========================================================= --}}
        {{-- 4. PORTAL DE ATENCIÓN / FRONT DESK [PortalClienteController] --}}
        {{-- ========================================================= --}}
        <div class="dropdown-divider border-gray-200 my-2"></div>
        <li class="px-4 py-1">
            <span class="fs-10 fw-bolder text-muted text-uppercase tracking-wider">Front Desk</span>
        </li>

        <li class="nxl-item">
            <a class="nxl-link" href="#" {{-- href="{{ route('sia.frontdesk.index') }}" --}}>
                <i class="bi bi-person-badge me-2"></i>
                <span class="nxl-mtext">Portal de Atención</span>
                {{-- Tablas: MaeTerceros --}}
            </a>
        </li>

        {{-- ========================================================= --}}
        {{-- 2. CONFIGURACIÓN CENTRAL [ConfiguracionController]        --}}
        {{-- ========================================================= --}}
        <div class="dropdown-divider border-gray-200 my-2"></div>
        <li class="px-4 py-1">
            <span class="fs-10 fw-bolder text-muted text-uppercase tracking-wider">Configuración Central</span>
        </li>

        <li class="nxl-item">
            <a class="nxl-link" href="#" {{-- href="{{ route('sia.config.index') }}" --}}>
                <i class="bi bi-sliders me-2"></i>
                <span class="nxl-mtext">Parámetros Core (JSON)</span>
                {{-- Tablas: car_sia_config --}}
            </a>
        </li>
        <li class="nxl-item">
            <a class="nxl-link" href="#" {{-- href="{{ route('sia.catalogos.index') }}" --}}>
                <i class="bi bi-list-check me-2"></i>
                <span class="nxl-mtext">Catálogos y Acciones</span>
                {{-- Tablas: car_sia_acciones_vencimiento, car_sia_estados, car_sia_tipos --}}
            </a>
        </li>

        {{-- ========================================================= --}}
        {{-- 1. ÁREA TÉCNICA / BACKSTAGE [IngestaController / AuditoriaController] --}}
        {{-- ========================================================= --}}
        <div class="dropdown-divider border-gray-200 my-2"></div>
        <li class="px-4 py-1">
            <span class="fs-10 fw-bolder text-muted text-uppercase tracking-wider">Sistema y Auditoría</span>
        </li>

        <li class="nxl-item">
            <a class="nxl-link" href="#" {{-- href="{{ route('sia.ingesta.index') }}" --}}>
                <i class="bi bi-database-gear me-2 text-primary"></i>
                <span class="nxl-mtext">Ingesta ERP (Staging)</span>
                {{-- Tablas: car_siasoft_api --}}
            </a>
        </li>
        <li class="nxl-item">
            <a class="nxl-link" href="#" {{-- href="{{ route('sia.auditoria.index') }}" --}}>
                <i class="bi bi-terminal me-2"></i>
                <span class="nxl-mtext">Bitácora de Auditoría</span>
                {{-- Tablas: car_sia_operaciones_logs, car_sia_origenes_evento --}}
            </a>
        </li>

        <li class="nxl-item">
            {{-- Disparador del Modal --}}
            <a class="nxl-link" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalConfigMantenimientoSia">
                <i class="bi bi-shield-exclamation me-2 text-danger"></i>
                <span class="nxl-mtext">Estado del Motor SIA</span>
                {{-- Validamos si es TRUE o "true" en Cache (Omitido temporalmente en vivo para evitar fallos si no existe la llave) --}}
                {{-- @if(filter_var(\Illuminate\Support\Facades\Cache::get('sia_mantenimiento_active'), FILTER_VALIDATE_BOOLEAN)) --}}
                    <span class="badge bg-danger fs-10 ms-auto animate-pulse">ON</span>
                {{-- @endif --}}
            </a>
        </li>
    </ul>
</li>

{{-- ================================================================= --}}
{{-- TODO LO SIGUIENTE MOVERLO AL FINAL DEL ARCHIVO (FUERA DEL MENU) --}}
{{-- ================================================================= --}}

{{-- MODAL DE MANTENIMIENTO SIA --}}
<div class="modal fade" id="modalConfigMantenimientoSia" tabindex="-1" aria-labelledby="modalConfigMantenimientoSiaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light-danger border-bottom border-danger border-opacity-25">
                <h5 class="modal-title fw-bold text-danger" id="modalConfigMantenimientoSiaLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Control de Mantenimiento Motor SIA
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5">
                <p class="text-muted mb-4 fs-7">
                    <strong>¡Atención!</strong> Al activar el modo de mantenimiento, todos los operarios y analistas perderán acceso al Motor de Operaciones y Front Desk inmediatamente. Las colas del CRON y la ingesta se pausarán.
                </p>

                <div class="d-flex align-items-center justify-content-between p-4 bg-light rounded-3 border border-gray-300 border-dashed">
                    <div>
                        <span class="fw-bolder text-dark d-block">Bloquear Accesos y Procesos</span>
                        <span class="text-muted fs-9">Activar modo offline para el módulo SIA</span>
                    </div>
                    <div class="form-check form-switch m-0">
                        {{-- Atributo checked forzado para previsualizar --}}
                        <input class="form-check-input cursor-pointer" style="height: 25px; width: 50px;" type="checkbox" role="switch" id="switchMantenimientoSia" checked>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // 1. EL TRUCO MÁGICO: Movemos el modal directo al <body> para evitar el bloqueo opaco
        const modalElement = document.getElementById('modalConfigMantenimientoSia');
        if(modalElement) {
            document.body.appendChild(modalElement);
        }

        // 2. Lógica de guardado del Switch (Desactivado el Fetch real hasta tener ruta)
        const checkSwitch = document.getElementById('switchMantenimientoSia');
        if(checkSwitch) {
            checkSwitch.addEventListener('change', function() {
                const estadoActivo = this.checked;

                // Endpoint simulado (La variable Blade comentada para evitar error)
                // const fetchUrl = "{{-- route('sia.mantenimiento.toggle') --}}";
                console.log('Se enviará petición a la ruta cuando se cree. Estado:', estadoActivo);
                alert("Simulación: Cambio de estado a " + (estadoActivo ? "ACTIVO" : "INACTIVO"));
            });
        }
    });
</script>
