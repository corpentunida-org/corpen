{{-- DENTRO DEL MENU (ul) --}}
<li class="nxl-item nxl-hasmenu active nxl-trigger">
    <a class="nxl-link" href="javascript:void(0)">
        <span class="nxl-micon"><i class="bi bi-wallet2"></i></span>
        <span class="nxl-mtext">SIA Cartera</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        {{-- SECCIÓN OPERATIVA (BACKOFFICE Y FRONT DESK) --}}
        <li class="nxl-item">
            <a class="nxl-link" href="#">
                <i class="bi bi-file-earmark-text me-2"></i> Motor de Operaciones
            </a>
        </li>

        <li class="nxl-item">
            <a class="nxl-link" href="#">
                <i class="bi bi-person-badge me-2"></i> Portal de Atención (Front Desk)
            </a>
        </li>

        {{-- SECCIÓN DE CONFIGURACIÓN --}}
        <div class="dropdown-divider border-gray-200 my-2"></div>
        <li class="px-4 py-1">
            <span class="fs-10 fw-bolder text-muted text-uppercase tracking-wider">Reglas de Negocio</span>
        </li>

        <li class="nxl-item">
            <a class="nxl-link" href="#">
                <i class="bi bi-sliders me-2"></i> Parámetros Core
            </a>
        </li>

        {{-- SECCIÓN DE SISTEMA (BACKSTAGE) --}}
        <div class="dropdown-divider border-gray-200 my-2"></div>
        <li class="px-4 py-1">
            <span class="fs-10 fw-bolder text-muted text-uppercase tracking-wider">Sistema y Auditoría</span>
        </li>

        <li class="nxl-item">
            {{-- Disparador del Modal --}}
            <a class="nxl-link" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalConfigMantenimientoSia">
                <i class="bi bi-shield-exclamation me-2 text-danger"></i>
                <span class="nxl-mtext">Estado del Motor SIA</span>
                {{-- Validamos si es TRUE o "true" en Cache (Omitido en plano, se simula visualmente) --}}
                <span class="badge bg-danger fs-10 ms-auto animate-pulse">ON</span>
            </a>
        </li>

        <li class="nxl-item">
            <a class="nxl-link" href="#">
                <i class="bi bi-database-gear me-2 text-primary"></i>
                <span class="nxl-mtext">Ingesta ERP (Staging Area)</span>
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
                    <strong>¡Atención!</strong> Al activar el modo de mantenimiento, todos los operarios y analistas perderán acceso al Motor de Operaciones y Front Desk inmediatamente. Las colas del CRON se pausarán.
                </p>

                <div class="d-flex align-items-center justify-content-between p-4 bg-light rounded-3 border border-gray-300 border-dashed">
                    <div>
                        <span class="fw-bolder text-dark d-block">Bloquear Accesos y Procesos</span>
                        <span class="text-muted fs-9">Activar modo offline para el módulo SIA</span>
                    </div>
                    <div class="form-check form-switch m-0">
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

{{-- SCRIPT CORREGIDO --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // 1. EL TRUCO MÁGICO: Movemos el modal directo al <body> para evitar el bloqueo opaco
        const modalElement = document.getElementById('modalConfigMantenimientoSia');
        if(modalElement) {
            document.body.appendChild(modalElement);
        }

        // 2. Lógica de guardado del Switch
        const checkSwitch = document.getElementById('switchMantenimientoSia');
        if(checkSwitch) {
            checkSwitch.addEventListener('change', function() {
                const estadoActivo = this.checked;

                // Endpoint simulado para ajustar cuando tengas el controlador
                const fetchUrl = "/api/sia/mantenimiento/toggle";

                fetch(fetchUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ estado: estadoActivo })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // Recargar para que los cambios visuales y el middleware apliquen
                        window.location.reload();
                    }
                })
                .catch(error => console.error('Error al cambiar el estado del Motor SIA:', error));
            });
        }
    });
</script>
