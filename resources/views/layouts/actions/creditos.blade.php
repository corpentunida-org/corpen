<li class="nxl-item nxl-hasmenu {{ request()->routeIs('creditos.estado*') ? 'active' : '' }}">
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-currency-dollar"></i></span>
        <span class="nxl-mtext">Créditos</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">

        {{-- Etapa 1 --}}
        <li class="nxl-item nxl-hasmenu {{ request()->routeIs('creditos.estado1.*') ? 'active' : '' }}">
            <a href="javascript:void(0);" class="nxl-link">
                Etapa 1 - Documentación
                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
            </a>
            <ul class="nxl-submenu">
                <li class="nxl-item">
                    <a class="nxl-link" href="{{ route('creditos.estado1.form') }}">Formulario</a>
                </li>
            </ul>
        </li>

        {{-- Etapa 2 --}}
        <li class="nxl-item nxl-hasmenu {{ request()->routeIs('creditos.estado2.*') ? 'active' : '' }}">
            <a href="javascript:void(0);" class="nxl-link">
                Etapa 2 - Solicitud
                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
            </a>
            <ul class="nxl-submenu">
                <li class="nxl-item">
                    <a class="nxl-link" href="{{ route('creditos.estado2.form') }}">Formulario</a>
                </li>
            </ul>
        </li>

        {{-- Etapa 3 --}}
        <li class="nxl-item nxl-hasmenu {{ request()->routeIs('creditos.estado3.*') ? 'active' : '' }}">
            <a href="javascript:void(0);" class="nxl-link">
                Etapa 3 - Análisis
                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
            </a>
            <ul class="nxl-submenu">
                <li class="nxl-item">
                    <a class="nxl-link" href="{{ route('creditos.estado3.form') }}">Formulario</a>
                </li>
            </ul>
        </li>

        {{-- Etapa 4 --}}
        <li class="nxl-item nxl-hasmenu {{ request()->routeIs('creditos.estado4.*') ? 'active' : '' }}">
            <a href="javascript:void(0);" class="nxl-link">
                Etapa 4 - Aprobación
                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
            </a>
            <ul class="nxl-submenu">
                <li class="nxl-item">
                    <a class="nxl-link" href="{{ route('creditos.estado4.form') }}">Formulario</a>
                </li>
            </ul>
        </li>

        {{-- Etapa 5 --}}
        <li class="nxl-item nxl-hasmenu {{ request()->routeIs('creditos.estado5.*') ? 'active' : '' }}">
            <a href="javascript:void(0);" class="nxl-link">
                Etapa 5 - Notificación
                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
            </a>
            <ul class="nxl-submenu">
                <li class="nxl-item">
                    <a class="nxl-link" href="{{ route('creditos.estado5.form') }}">Formulario</a>
                </li>
            </ul>
        </li>

        {{-- Etapa 6 --}}
        <li class="nxl-item nxl-hasmenu {{ request()->routeIs('creditos.estado6.*') ? 'active' : '' }}">
            <a href="javascript:void(0);" class="nxl-link">
                Etapa 6 - Desembolsos
                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
            </a>
            <ul class="nxl-submenu">
                <li class="nxl-item">
                    <a class="nxl-link" href="{{ route('creditos.estado6.form') }}">Formulario</a>
                </li>
            </ul>
        </li>

        {{-- Etapa 7 --}}
        <li class="nxl-item nxl-hasmenu {{ request()->routeIs('creditos.estado7.*') ? 'active' : '' }}">
            <a href="javascript:void(0);" class="nxl-link">
                Etapa 7 - Cartera
                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
            </a>
            <ul class="nxl-submenu">
                <li class="nxl-item">
                    <a class="nxl-link" href="{{ route('creditos.estado7.form') }}">Formulario</a>
                </li>
            </ul>
        </li>

    </ul>
</li>
