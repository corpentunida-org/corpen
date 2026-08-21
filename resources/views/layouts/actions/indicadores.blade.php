@candirect('indicators.indicadores.index')
<li
    class="nxl-item nxl-hasmenu {{ request()->routeIs('indicators.indicadores.*', 'indicators.encuestas.*', 'indicators.quizes.*') ? 'active' : '' }}">

    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon">
            <i class="bi bi-bar-chart-line"></i>
        </span>
        <span class="nxl-mtext">Indicadores</span>
        <span class="nxl-arrow">
            <i class="feather-chevron-right"></i>
        </span>
    </a>

    <ul class="nxl-submenu">

        @candirect('indicators.indicadores.index')
        <li class="nxl-item {{ request()->routeIs('indicators.indicadores.index') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('indicators.indicadores.index') }}">
                <span class="nxl-micon">
                    <i class="bi bi-speedometer2"></i>
                </span>
                Indicadores
            </a>
        </li>
        @endcandirect

        @candirect('indicators.indicadores.create')
        <li class="nxl-item {{ request()->routeIs('indicators.indicadores.create') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('indicators.indicadores.create') }}">
                <span class="nxl-micon">
                    <i class="bi bi-sliders"></i>
                </span>
                Parámetros Indicadores
            </a>
        </li>
        @endcandirect

        @candirect('indicators.encuestas.dashboard')
        <li class="nxl-item {{ request()->routeIs('indicators.quizes.*', 'indicators.encuestas.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('indicators.quizes.index') }}">
                <span class="nxl-micon">
                    <i class="bi bi-clipboard-data"></i>
                </span>
                Encuestas Dashboard
            </a>
        </li>
        @endcandirect

    </ul>
</li>
@endcandirect