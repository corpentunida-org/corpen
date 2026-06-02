<li class="nxl-item nxl-hasmenu {{ request()->routeIs('maestras.terceros.*', 'maestras.congregacion.*', 'maestras.tipos.*', 'demograficos.*') ? 'active' : '' }}">
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-stack"></i></span>
        <span class="nxl-mtext">Maestras</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        <li class="nxl-item {{ request()->routeIs('maestras.terceros.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('maestras.terceros.index') }}">Terceros</a>
        </li>
        <li class="nxl-item {{ request()->routeIs('maestras.congregacion.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('maestras.congregacion.index') }}">Congregaciones</a>
        </li>
        <li class="nxl-item {{ request()->routeIs('maestras.tipos.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('maestras.tipos.index') }}">Tipos</a>
        </li>
        
        {{-- ====================================================================== --}}
        {{-- SUBMENÚ ANIDADO: DEMOGRAFÍA --}}
        {{-- ====================================================================== --}}
        <li class="nxl-item nxl-hasmenu {{ request()->routeIs('demograficos.*') ? 'active' : '' }}">
            <a href="javascript:void(0);" class="nxl-link">
                <span class="nxl-mtext">Demografía</span>
                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
            </a>
            <ul class="nxl-submenu">
                <li class="nxl-item {{ request()->routeIs('demograficos.dashboard') ? 'active' : '' }}">
                    <a class="nxl-link" href="{{ route('demograficos.dashboard') }}">Tablero Geográfico</a>
                </li>
                <li class="nxl-item {{ request()->routeIs('demograficos.sincronizar.*') ? 'active' : '' }}">
                    <a class="nxl-link" href="{{ route('demograficos.sincronizar.index') }}">Sincronización Masiva</a>
                </li>
                <li class="nxl-item {{ request()->routeIs('demograficos.maestro.*') ? 'active' : '' }}">
                    <a class="nxl-link" href="{{ route('demograficos.maestro.index') }}">Directorio Maestro</a>
                </li>
            </ul>
        </li>
        {{-- ====================================================================== --}}
    </ul>
</li>