<li class="nxl-item nxl-hasmenu {{ request()->routeIs('sgrh.*') ? 'active' : '' }}">
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-collection"></i></span>
        <span class="nxl-mtext">Recursos Humanos</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        @can('sgrh.empleado.index')
            <li class="nxl-item {{ request()->routeIs('sgrh.empleado.*') ? 'active' : '' }}">
                <a class="nxl-link" href="{{ route('sgrh.empleado.index') }}">
                    <span class="nxl-micon"><i class="bi bi-person"></i></span>
                    Colaboradores
                </a>
            </li>
        @endcan
        <li class="nxl-item {{ request()->routeIs('maestras.terceros.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('maestras.terceros.index') }}">
                <span class="nxl-micon"><i class="bi bi-person-vcard"></i></span>
                Terceros (maestro)
            </a>
        </li>
        <li class="nxl-item {{ request()->routeIs('sgrh.area.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('sgrh.area.index') }}">
                <span class="nxl-micon"><i class="bi bi-diagram-3"></i></span>
                Áreas
            </a>
        </li>
        <li class="nxl-item {{ request()->routeIs('sgrh.cargo.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('sgrh.cargo.index') }}">
                <span class="nxl-micon"><i class="bi bi-briefcase"></i></span>
                Cargos
            </a>
        </li>
        @can('sgrh.contrato.index')
            <li class="nxl-item {{ request()->routeIs('sgrh.contrato.*') ? 'active' : '' }}">
                <a class="nxl-link" href="{{ route('sgrh.contrato.index') }}">
                    <span class="nxl-micon"><i class="bi bi-file-earmark-text"></i></span>
                    Contratos
                </a>
            </li>
        @endcan
        <li class="nxl-item {{ request()->routeIs('sgrh.tipo-contrato.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('sgrh.tipo-contrato.index') }}">
                <span class="nxl-micon"><i class="bi bi-tags"></i></span>
                Tipos de contrato
            </a>
        </li>
        @can('sgrh.vacacion.solicitud.index')
            <li class="nxl-item {{ request()->routeIs('sgrh.vacacion.solicitud.mis') ? 'active' : '' }}">
                <a class="nxl-link" href="{{ route('sgrh.vacacion.solicitud.mis') }}">
                    <span class="nxl-micon"><i class="bi bi-sun"></i></span>
                    Mis vacaciones
                </a>
            </li>
            <li class="nxl-item {{ request()->routeIs('sgrh.vacacion.solicitud.aprobaciones') || request()->routeIs('sgrh.vacacion.solicitud.show') ? 'active' : '' }}">
                <a class="nxl-link" href="{{ route('sgrh.vacacion.solicitud.aprobaciones') }}">
                    <span class="nxl-micon"><i class="bi bi-check2-square"></i></span>
                    Aprobar vacaciones
                </a>
            </li>
        @endcan
        @can('sgrh.vacacion.calendario.index')
            <li class="nxl-item {{ request()->routeIs('sgrh.vacacion.calendario.*') ? 'active' : '' }}">
                <a class="nxl-link" href="{{ route('sgrh.vacacion.calendario.index') }}">
                    <span class="nxl-micon"><i class="bi bi-calendar3"></i></span>
                    Calendario de vacaciones
                </a>
            </li>
        @endcan
        @can('sgrh.vacacion.saldo.index')
            <li class="nxl-item {{ request()->routeIs('sgrh.vacacion.saldo.*') ? 'active' : '' }}">
                <a class="nxl-link" href="{{ route('sgrh.vacacion.saldo.index') }}">
                    <span class="nxl-micon"><i class="bi bi-wallet2"></i></span>
                    Saldos de vacaciones
                </a>
            </li>
        @endcan
        @can('sgrh.vacacion.colectiva.index')
            <li class="nxl-item {{ request()->routeIs('sgrh.vacacion.colectiva.*') ? 'active' : '' }}">
                <a class="nxl-link" href="{{ route('sgrh.vacacion.colectiva.index') }}">
                    <span class="nxl-micon"><i class="bi bi-people"></i></span>
                    Vacaciones colectivas
                </a>
            </li>
        @endcan
        @can('sgrh.vacacion.politica.index')
            <li class="nxl-item {{ request()->routeIs('sgrh.vacacion.politica.*') ? 'active' : '' }}">
                <a class="nxl-link" href="{{ route('sgrh.vacacion.politica.index') }}">
                    <span class="nxl-micon"><i class="bi bi-sliders"></i></span>
                    Políticas de vacaciones
                </a>
            </li>
        @endcan
        @can('sgrh.vacacion.alertas.index')
            <li class="nxl-item {{ request()->routeIs('sgrh.vacacion.alertas.*') ? 'active' : '' }}">
                <a class="nxl-link" href="{{ route('sgrh.vacacion.alertas.index') }}">
                    <span class="nxl-micon"><i class="bi bi-bell"></i></span>
                    Alertas de vacaciones
                </a>
            </li>
        @endcan
        @can('sgrh.vacacion.festivo.index')
            <li class="nxl-item {{ request()->routeIs('sgrh.vacacion.festivo.*') ? 'active' : '' }}">
                <a class="nxl-link" href="{{ route('sgrh.vacacion.festivo.index') }}">
                    <span class="nxl-micon"><i class="bi bi-calendar-x"></i></span>
                    Festivos
                </a>
            </li>
        @endcan
    </ul>
</li>
