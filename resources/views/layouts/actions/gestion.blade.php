<li class="nxl-item nxl-hasmenu {{ request()->routeIs('archivo.cargo.*', 'archivo.area.*') ? 'active' : '' }}">
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-database"></i></span>
        <span class="nxl-mtext">Gestión Documental</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        {{-- Submenú para Empleados --}}
        <li class="nxl-item nxl-hasmenu {{ request()->routeIs('archivo.cargo.*') ? 'active' : '' }}">
            <a href="javascript:void(0);" class="nxl-link">
                Empleados
                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
            </a>
            <ul class="nxl-submenu">
                <li class="nxl-item {{ request()->routeIs('archivo.cargo.index') ? 'active' : '' }}">
                    <a class="nxl-link" href="{{ route('archivo.cargo.index') }}" style="text-decoration: none;">
                        Cargos
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</li>
