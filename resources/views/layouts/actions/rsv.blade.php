<li class="nxl-item nxl-hasmenu">
    <a class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-building-fill"></i></span>
        <span class="nxl-mtext">RSV</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        {{-- Panel solo para perfiles con rsv.panel.acceder (admin, contabilidad, soporte); el asociado ve solo el portal --}}
        @if (\App\Services\Rsv\RsvAcceso::puede(\App\Services\Rsv\RsvAcceso::PANEL))
            <li class="nxl-item">
                <a class="nxl-link" href="{{ route('rsv.admin.dashboard') }}">
                    <span class="nxl-micon"><i class="bi bi-building-fill"></i></span>
                    <span class="nxl-mtext">Panel RSV (Admin)</span>
                </a>
            </li>
        @endif
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('rsv.cliente.portal') }}">
                <span class="nxl-micon"><i class="bi bi-person-badge"></i></span>
                <span class="nxl-mtext">Portal Asociado</span>
            </a>
        </li>
    </ul>
</li>
