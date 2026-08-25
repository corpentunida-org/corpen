{{-- DENTRO DEL MENU (ul) --}}
<li class="nxl-item nxl-hasmenu active nxl-trigger">
    <a class="nxl-link" href="javascript:void(0)">
        <span class="nxl-micon"><i class="bi bi-wallet2"></i></span>
        <span class="nxl-mtext">Certificado</span>
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
            <a class="nxl-link" href="{{ route('certificados.operaciones.index') }}">
                <i class="bi bi-file-earmark-text me-2"></i>
                <span class="nxl-mtext">Motor de Operaciones</span>
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
            <a class="nxl-link" href="{{ route('certificados.frontdesk.index') }}">
                <i class="bi bi-person-badge me-2"></i>
                <span class="nxl-mtext">Portal de Atención</span>
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
            <a class="nxl-link" href="{{ route('certificados.catalogos.index') }}">
                <i class="bi bi-list-check me-2"></i>
                <span class="nxl-mtext">Parámetros, Acciones y Catálogos</span>
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
            <a class="nxl-link" href="{{ route('certificados.ingesta.index') }}">
                <i class="bi bi-database-gear me-2 text-primary"></i>
                <span class="nxl-mtext">Ingesta ERP (Staging)</span>
            </a>
        </li>
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('certificados.auditoria.index') }}">
                <i class="bi bi-terminal me-2"></i>
                <span class="nxl-mtext">Bitácora de Auditoría</span>
            </a>
        </li>

        <li class="nxl-item">
            {{-- Disparador del Modal --}}
            <a class="nxl-link" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalConfigMantenimientoSia">
                <i class="bi bi-shield-exclamation me-2 text-danger"></i>
                <span class="nxl-mtext">Estado del Motor SIA</span>
                {{-- Validamos si es TRUE o "true" en Cache --}}
                @if(filter_var(\Illuminate\Support\Facades\Cache::get('sia_mantenimiento_active'), FILTER_VALIDATE_BOOLEAN))
                    <span class="badge bg-danger fs-10 ms-auto animate-pulse">ON</span>
                @endif
            </a>
        </li>
    </ul>
</li>

{{-- EL HTML DEL MODAL Y EL SCRIPT SE MANTIENEN EXACTAMENTE IGUAL AL FINAL DE TU ARCHIVO --}}

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
