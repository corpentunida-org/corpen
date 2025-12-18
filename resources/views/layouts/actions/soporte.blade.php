<!-- ===== Menú: Soporte ===== -->
<li class="nxl-item nxl-hasmenu">
    <a class="nxl-link" href="javascript:void(0)">
        <span class="nxl-micon"><i class="bi bi-headset"></i></span>
        <span class="nxl-mtext">Soporte</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        <!-- Crear Soporte -->
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('soportes.soportes.create') }}">
                <i class="bi bi-plus-circle me-2"></i> Crear Soporte
            </a>
        </li>

        <!-- Mi Lista -->
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('soportes.soportes.index') }}">
                <i class="bi bi-list-ul me-2"></i> Mi Lista
            </a>
        </li>

        <!-- Opciones solo para Administrador -->
        @candirect('soporte.lista.administrador')

        <!-- Parámetros -->
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('soportes.tablero') }}">
                <i class="bi bi-gear me-2"></i> Parámetros de Soportes
            </a>
        </li>

        <!-- Estadísticas -->
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('soportes.estadisticas') }}">
                <i class="bi bi-bar-chart-fill me-2"></i> Estadísticas
            </a>
        </li>

        @endcandirect
    </ul>
</li>
