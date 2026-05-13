{{-- DENTRO DEL MENU (ul) --}}
<li class="nxl-item nxl-hasmenu {{ request()->routeIs('contabilidad.*') ? 'active nxl-trigger' : '' }}">
    <a class="nxl-link" href="javascript:void(0)">
        <span class="nxl-micon"><i class="bi bi-bank"></i></span>
        <span class="nxl-mtext">Contabilidad</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        {{-- SECCIÓN OPERATIVA --}}
        <li class="nxl-item {{ request()->routeIs('contabilidad.extractos.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('contabilidad.extractos.index') }}">
                <i class="bi bi-file-earmark-ruled me-2"></i> Conciliación (Extractos)
            </a>
        </li>
        
        <li class="nxl-item {{ request()->routeIs('contabilidad.cuentas-bancarias.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('contabilidad.cuentas-bancarias.index') }}">
                <i class="bi bi-credit-card-2-back me-2"></i> Cuentas Bancarias
            </a>
        </li>

        {{-- SECCIÓN DE SISTEMA --}}
        <div class="dropdown-divider border-gray-200 my-2"></div>
        <li class="px-4 py-1">
            <span class="fs-10 fw-bolder text-muted text-uppercase tracking-wider">Mantenimiento</span>
        </li>

        <li class="nxl-item">
            {{-- Disparador del Modal --}}
            <a class="nxl-link" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalConfigMantenimiento">
                <i class="bi bi-shield-exclamation me-2 text-danger"></i> 
                <span class="nxl-mtext">Estado del Kernel</span>
                {{-- Validamos si es TRUE o "true" --}}
                @if(filter_var(\Illuminate\Support\Facades\Cache::get('contabilidad_mantenimiento_active'), FILTER_VALIDATE_BOOLEAN))
                    <span class="badge bg-danger fs-10 ms-auto animate-pulse">ON</span>
                @endif
            </a>
        </li>

        <li class="nxl-item {{ request()->routeIs('contabilidad.sincronizar.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('contabilidad.sincronizar.index') }}">
                <i class="bi bi-database-gear me-2 text-primary"></i> 
                <span class="nxl-mtext">Sincronización Maestra</span>
            </a>
        </li>
    </ul>
</li>

{{-- ================================================================= --}}
{{-- TODO LO SIGUIENTE MOVERLO AL FINAL DEL ARCHIVO (FUERA DEL MENU) --}}
{{-- ================================================================= --}}

{{-- MODAL DE MANTENIMIENTO --}}
<div class="modal fade" id="modalConfigMantenimiento" tabindex="-1" aria-labelledby="modalConfigMantenimientoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light-danger border-bottom border-danger border-opacity-25">
                <h5 class="modal-title fw-bold text-danger" id="modalConfigMantenimientoLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Control de Mantenimiento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5">
                <p class="text-muted mb-4 fs-7">
                    <strong>¡Atención!</strong> Al activar el modo de mantenimiento, todos los usuarios perderán acceso a Extractos y Cuentas Bancarias inmediatamente.
                </p>

                <div class="d-flex align-items-center justify-content-between p-4 bg-light rounded-3 border border-gray-300 border-dashed">
                    <div>
                        <span class="fw-bolder text-dark d-block">Bloquear Accesos Operativos</span>
                        <span class="text-muted fs-9">Activar modo offline para usuarios</span>
                    </div>
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input cursor-pointer" style="height: 25px; width: 50px;" type="checkbox" role="switch" id="switchMantenimientoKernel" 
                            {{ filter_var(\Illuminate\Support\Facades\Cache::get('contabilidad_mantenimiento_active'), FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
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
        const modalElement = document.getElementById('modalConfigMantenimiento');
        if(modalElement) {
            document.body.appendChild(modalElement);
        }

        // 2. Lógica de guardado del Switch
        const checkSwitch = document.getElementById('switchMantenimientoKernel');
        if(checkSwitch) {
            checkSwitch.addEventListener('change', function() {
                const estadoActivo = this.checked;
                
                fetch("{{ route('contabilidad.mantenimiento.toggle') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
                .catch(error => console.error('Error al cambiar el estado:', error));
            });
        }
    });
</script>