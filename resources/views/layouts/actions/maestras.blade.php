{{-- <li class="nxl-item nxl-hasmenu">
    <a class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-currency-dollar"></i></span>
        <span class="nxl-mtext">Créditos</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>
    <ul class="nxl-submenu">
        <li class="nxl-item"><a class="nxl-link" href="">Congregaciones</a></li>
    </ul>
</li>  --}}


{{-- Versión mejorada con el menú activo --}}
<li class="nxl-item nxl-hasmenu {{ request()->routeIs('creditos.congregaciones.*') ? 'active' : '' }}">
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-database"></i></span>
        <span class="nxl-mtext">Maestras</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>
    <ul class="nxl-submenu">
        <li class="nxl-item">
            <a class="nxl-link" href="#">Terceros</a>
        </li>
    </ul>
    <ul class="nxl-submenu">
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('maestras.congregacion.index') }}">Congregaciones</a>
        </li>
    </ul>
</li>