<li class="nxl-item nxl-hasmenu {{ request()->routeIs('sgrh.*') ? 'active' : '' }}">
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-collection"></i></span>
        <span class="nxl-mtext">Recursos Humanos</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        <li class="nxl-item {{ request()->routeIs('sgrh.empleado.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('sgrh.empleado.index') }}">
                <span class="nxl-micon"><i class="bi bi-person"></i></span>
                Colaboradores
            </a>
        </li>
        <li class="nxl-item {{ request()->routeIs('maestras.terceros.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('maestras.terceros.index') }}">
                <span class="nxl-micon"><i class="bi bi-person-vcard"></i></span>
                Terceros (maestro)
            </a>
        </li>
    </ul>
</li>
