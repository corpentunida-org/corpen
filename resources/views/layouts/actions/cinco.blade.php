<li class="nxl-item nxl-hasmenu">
    <a class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-bar-chart-line-fill"></i></span>
        <span class="nxl-mtext">Beneficio Antiguedad</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>
    <ul class="nxl-submenu">
        @candirect('cinco.movcontables.index')
            <li class="nxl-item"><a class="nxl-link" href="{{ route('cinco.movcontables.index')}}">Mov Contables CINCO</a></li>
        @endcandirect
        @candirect('cinco.tercero.index')
            <li class="nxl-item"><a class="nxl-link" href="{{ route('cinco.tercero.index')}}">Fecha Aporte - Ministerio</a></li>
        @endcandirect
        @candirect('cinco.retiros.index')
        <li class="nxl-item"><a class="nxl-link" href="{{ route('cinco.retiros.index')}}">Cálculo Retiros</a></li>
        @endcandirect
    </ul>
</li>
