<li class="nxl-item nxl-hasmenu {{ request()->routeIs('sgrh.*') ? 'active' : '' }}">
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-collection"></i></span>
        <span class="nxl-mtext">Recursos Humanos</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        @can('sgrh.empleado.index')
            <li class="nxl-item {{ request()->routeIs('sgrh.empleado.*') ? 'active' : '' }}">
                <a class="nxl-link" href="{{ route('sgrh.empleado.index') }}">
                    <span class="nxl-micon"><i class="bi bi-person"></i></span>
                    Colaboradores
                </a>
            </li>
        @endcan
        <li class="nxl-item {{ request()->routeIs('maestras.terceros.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('maestras.terceros.index') }}">
                <span class="nxl-micon"><i class="bi bi-person-vcard"></i></span>
                Terceros (maestro)
            </a>
        </li>
        <li class="nxl-item {{ request()->routeIs('sgrh.area.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('sgrh.area.index') }}">
                <span class="nxl-micon"><i class="bi bi-diagram-3"></i></span>
                Áreas
            </a>
        </li>
        <li class="nxl-item {{ request()->routeIs('sgrh.cargo.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('sgrh.cargo.index') }}">
                <span class="nxl-micon"><i class="bi bi-briefcase"></i></span>
                Cargos
            </a>
        </li>
        @can('sgrh.contrato.index')
            <li class="nxl-item {{ request()->routeIs('sgrh.contrato.*') ? 'active' : '' }}">
                <a class="nxl-link" href="{{ route('sgrh.contrato.index') }}">
                    <span class="nxl-micon"><i class="bi bi-file-earmark-text"></i></span>
                    Contratos
                </a>
            </li>
        @endcan
        <li class="nxl-item {{ request()->routeIs('sgrh.tipo-contrato.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('sgrh.tipo-contrato.index') }}">
                <span class="nxl-micon"><i class="bi bi-tags"></i></span>
                Tipos de contrato
            </a>
        </li>
    </ul>
</li>
