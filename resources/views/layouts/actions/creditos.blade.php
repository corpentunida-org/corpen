<li class="nxl-item nxl-hasmenu {{ request()->routeIs('creditos.estado1.*', 'creditos.estado2.*', 'creditos.estado3.*', 'creditos.estado4.*', 'creditos.estado5.*', 'creditos.estado6.*', 'creditos.estado7.*') ? 'active' : '' }}">
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-database"></i></span>
        <span class="nxl-mtext">Créditos</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        <li class="nxl-item {{ request()->routeIs('creditos.estado1.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('creditos.estado1.form') }}">Etapa 1 - Documentación</a>
        </li>
    </ul>
</li>