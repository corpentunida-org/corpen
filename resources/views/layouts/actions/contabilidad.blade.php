<li class="nxl-item nxl-hasmenu {{ request()->routeIs('contabilidad.*') ? 'active nxl-trigger' : '' }}">
    <a class="nxl-link" href="javascript:void(0)">
        <span class="nxl-micon"><i class="bi bi-bank"></i></span>
        <span class="nxl-mtext">Contabilidad</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        {{-- Submenú de Configuración de Cuentas --}}
        <li class="nxl-item {{ request()->routeIs('contabilidad.cuentas-bancarias.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('contabilidad.cuentas-bancarias.index') }}">
                <i class="bi bi-credit-card-2-back me-2"></i> Cuentas Bancarias
            </a>
        </li>
        
        {{-- Submenú de Extractos y Conciliación --}}
        <li class="nxl-item {{ request()->routeIs('contabilidad.extractos.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('contabilidad.extractos.index') }}">
                <i class="bi bi-file-earmark-ruled me-2"></i> Conciliación (Extractos)
            </a>
        </li>

        {{-- Submenú de Sincronización Excel --}}
        <li class="nxl-item {{ request()->routeIs('contabilidad.sincronizar.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('contabilidad.sincronizar.index') }}">
                <i class="bi bi-cloud-sync me-2"></i> Sincronizar Excel
            </a>
        </li>
    </ul>
</li>