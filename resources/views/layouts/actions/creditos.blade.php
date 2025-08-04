<li class="nxl-item nxl-hasmenu {{ request()->routeIs('creditos.estado1.*', 'creditos.estado2.*', 'creditos.estado3.*', 'creditos.estado4.*', 'creditos.estado5.*', 'creditos.estado6.*', 'creditos.estado7.*') ? 'active' : '' }}">
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-credit-card-2-front"></i></span>
        <span class="nxl-mtext">Créditos</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        {{-- Etapa 1 --}}
        <li class="nxl-item {{ request()->routeIs('estado1.index') ? 'active' : '' }}">
            <a href="javascript:void(0);" class="nxl-link">
                Maestras
                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
            </a>
            <ul class="nxl-submenu">
                <li class="nxl-item">
                    <a class="nxl-link" href="{{ route('estado1.index') }}" style="text-decoration: none;">
                        Lista de Fábricas
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</li>