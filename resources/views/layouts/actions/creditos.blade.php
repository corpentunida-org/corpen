<li class="nxl-item nxl-hasmenu {{ request()->routeIs('creditos.documentacion.*', 'creditos.analisis.*', 'creditos.desembolso.*', 'creditos.tesoreria.*', 'creditos.cartera.*', 'creditos.recaudo.*') ? 'active' : '' }}">
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-credit-card-2-front"></i></span>
        <span class="nxl-mtext">Créditos</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        <li class="nxl-item">
            <a class="nxl-link" href="{{route('creditos.credito.index')}}">Créditos</a>
        </li>
        <li class="nxl-item">
            <a class="nxl-link" href="#">Análisis</a>
        </li>
        <li class="nxl-item">
            <a class="nxl-link" href="#">Desembolso</a>
        </li>
        <li class="nxl-item">
            <a class="nxl-link" href="#">Tesorería</a>
        </li>
        <li class="nxl-item">
            <a class="nxl-link" href="#">Cartera</a>
        </li>
        <li class="nxl-item">
            <a class="nxl-link" href="#">Recaudo</a>
        </li>
        {{-- <li class="nxl-item"><a class="nxl-link" href="">Solicitud</a></li>
        <li class="nxl-item"><a class="nxl-link" href="">Analisis</a></li>
        <li class="nxl-item"><a class="nxl-link" href="">Desembolsos</a></li> --}}
    </ul>
</li>