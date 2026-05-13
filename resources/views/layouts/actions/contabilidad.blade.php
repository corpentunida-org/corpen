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

        {{-- SECCIÓN DE SISTEMA / MANTENIMIENTO --}}
        <div class="dropdown-divider border-gray-200 my-2"></div>
        <li class="px-4 py-1 d-flex align-items-center justify-content-between">
            <span class="fs-10 fw-bolder text-muted text-uppercase tracking-wider">Mantenimiento</span>
            
            {{-- Switch Profesional --}}
            <div class="form-check form-switch">
                <input class="form-check-input h-15px w-30px" type="checkbox" role="switch" id="switchMantenimientoKernel" 
                    {{ \Illuminate\Support\Facades\Cache::get('contabilidad_mantenimiento_active') ? 'checked' : '' }}>
            </div>
        </li>

        <li class="nxl-item {{ request()->routeIs('contabilidad.sincronizar.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('contabilidad.sincronizar.index') }}">
                <i class="bi bi-database-gear me-2 text-primary"></i> 
                <span class="nxl-mtext">Sincronización Maestra</span>
                <span class="badge bg-light-primary text-primary fs-10 ms-auto">AWS</span>
            </a>
        </li>
    </ul>
</li>

{{-- Script para que el Switch funcione sin recargar la página --}}
<script>
    document.getElementById('switchMantenimientoKernel').addEventListener('change', function() {
        const estadoActivo = this.checked;
        
        fetch("{{ route('contabilidad.mantenimiento.toggle') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ estado: estadoActivo })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Opcional: Recargar para aplicar cambios inmediatamente
                window.location.reload();
            }
        });
    });
</script>