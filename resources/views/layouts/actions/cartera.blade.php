<li class="nxl-item nxl-hasmenu {{ request()->routeIs('cartera.*') ? 'active nxl-trigger' : '' }}">
    <a class="nxl-link" href="javascript:void(0)">
        <span class="nxl-micon"><i class="bi bi-wallet2"></i></span>
        <span class="nxl-mtext">Cartera</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        {{-- Submenú de Cartas de Morosos --}}
        {{-- <li class="nxl-item {{ request()->routeIs('cartera.morosos.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('cartera.morosos.index') }}">
                <i class="bi bi-envelope-paper me-2"></i> Cartas Morosos
            </a>
        </li> --}}
        
        {{-- Submenú de Comprobantes de Pago --}}
        <li class="nxl-item {{ request()->routeIs('cartera.comprobantes.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('cartera.comprobantes.index') }}">
                <i class="bi bi-receipt-cutoff me-2"></i> Comprobantes Pago
            </a>
        </li>
    </ul>
</li>