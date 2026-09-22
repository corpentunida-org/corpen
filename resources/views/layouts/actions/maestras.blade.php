<li class="nxl-item nxl-hasmenu {{ request()->routeIs('maestras.terceros.*', 'maestras.congregacion.*', 'maestras.tipos.*', 'maestras.distrito.*', 'demograficos.*') ? 'active' : '' }}">
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-stack"></i></span>
        <span class="nxl-mtext">Maestras</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        {{-- ====================================================================== --}}
        {{-- SUBMENÚ ANIDADO: TERCEROS --}}
        {{-- ====================================================================== --}}
        <li class="nxl-item nxl-hasmenu {{ request()->routeIs('maestras.terceros.*') ? 'active' : '' }}">
            <a href="javascript:void(0);" class="nxl-link">
                <span class="nxl-mtext">Terceros</span>
                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
            </a>
            <ul class="nxl-submenu">
                <li class="nxl-item {{ request()->routeIs('maestras.terceros.index', 'maestras.terceros.create', 'maestras.terceros.edit', 'maestras.terceros.show') ? 'active' : '' }}">
                    <a class="nxl-link" href="{{ route('maestras.terceros.index') }}">Listado</a>
                </li>
                @candirect('maestras.terceros.importar')
                <li class="nxl-item {{ request()->routeIs('maestras.terceros.importar.*') ? 'active' : '' }}">
                    <a class="nxl-link" href="{{ route('maestras.terceros.importar.index') }}">Importar Pastores IPUC</a>
                </li>
                @endcandirect
                @candirect('maestras.comaeter.importar')
                <li class="nxl-item {{ request()->routeIs('maestras.comaeter.importar.*') ? 'active' : '' }}">
                    <a class="nxl-link" href="{{ route('maestras.comaeter.importar.index') }}">Actualizar Terceros (CoMae_ter)</a>
                </li>
                @endcandirect
            </ul>
        </li>
        {{-- ====================================================================== --}}
        {{-- SUBMENÚ ANIDADO: CONGREGACIONES --}}
        {{-- ====================================================================== --}}
        <li class="nxl-item nxl-hasmenu {{ request()->routeIs('maestras.congregacion.*') ? 'active' : '' }}">
            <a href="javascript:void(0);" class="nxl-link">
                <span class="nxl-mtext">Congregaciones</span>
                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
            </a>
            <ul class="nxl-submenu">
                <li class="nxl-item {{ request()->routeIs('maestras.congregacion.index', 'maestras.congregacion.create', 'maestras.congregacion.edit', 'maestras.congregacion.show') ? 'active' : '' }}">
                    <a class="nxl-link" href="{{ route('maestras.congregacion.index') }}">Listado</a>
                </li>
                @candirect('maestras.congregaciones.importar')
                <li class="nxl-item {{ request()->routeIs('maestras.congregacion.importar.*') ? 'active' : '' }}">
                    <a class="nxl-link" href="{{ route('maestras.congregacion.importar.index') }}">Actualizar (Excel)</a>
                </li>
                @endcandirect
            </ul>
        </li>
        {{-- ====================================================================== --}}
        <li class="nxl-item {{ request()->routeIs('maestras.tipos.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('maestras.tipos.index') }}">Tipos</a>
        </li>
        <li class="nxl-item {{ request()->routeIs('maestras.distrito.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('maestras.distrito.index') }}">Distritos</a>
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