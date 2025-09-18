<li class="nxl-item nxl-hasmenu">
    <a class="nxl-link" href="javascript:void(0)">
        <span class="nxl-micon"><i class="bi bi-headset"></i></span>
        <span class="nxl-mtext">Soporte</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>
    <ul class="nxl-submenu">
        {{-- Enlace al Tablero de Soporte --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('soportes.soportes.index') }}">
                Tablero de Soportes
            </a>
        </li>

        {{-- Enlace a Parámetros de Soporte --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('soportes.tablero') }}">
                Parámetros de Soportes
            </a>
        </li>
    </ul>
</li>
