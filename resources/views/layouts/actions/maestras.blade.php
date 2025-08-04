<li class="nxl-item nxl-hasmenu {{ request()->routeIs('maestras.terceros.*', 'maestras.congregacion.*') ? 'active' : '' }}">
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-stack"></i></span>
        <span class="nxl-mtext">Maestras</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        <li class="nxl-item {{ request()->routeIs('maestras.terceros.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('maestras.terceros.index') }}">Terceros</a>
        </li>
        <li class="nxl-item {{ request()->routeIs('maestras.congregacion.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('maestras.congregacion.index') }}">Congregaciones</a>
        </li>
    </ul>
</li>