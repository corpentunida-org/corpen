<li class="nxl-item nxl-hasmenu">
    <a class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-building-fill"></i></span>
        <span class="nxl-mtext">RSV</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        <li class="nxl-item">
            <!-- Asume que tienes una ruta nombrada para tu dashboard -->
            <a class="nxl-link" href="{{ route('rsv.admin.dashboard') }}">
                <span class="nxl-micon"><i class="bi bi-building-fill"></i></span>
                <span class="nxl-mtext">Panel RSV (Admin)</span>
            </a>
        </li>
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('rsv.cliente.portal') }}">
                <span class="nxl-micon"><i class="bi bi-person-badge"></i></span>
                <span class="nxl-mtext">Portal Cliente</span>
            </a>
        </li>
    </ul>
</li>
