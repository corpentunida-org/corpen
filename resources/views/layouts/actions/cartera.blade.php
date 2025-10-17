<li class="nxl-item nxl-hasmenu">
    <a class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-wallet2"></i></span>
        <span class="nxl-mtext">Cartera</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        {{-- Sección de Interacciones --}}
        <li class="nxl-item nxl-hasmenu">
            <a class="nxl-link">
                <i class="bi bi-chat-dots me-2"></i>
                <span>Interacciones</span>
                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
            </a>

            <ul class="nxl-submenu">
                <li class="nxl-item">
                    <a class="nxl-link" href="{{ route('interactions.create') }}">
                        <i class="bi bi-plus-circle me-2"></i> Nueva Interacción
                    </a>
                </li>
                <li class="nxl-item">
                    <a class="nxl-link" href="{{ route('interactions.index') }}">
                        <i class="bi bi-list-check me-2"></i> Listado de Interacciones
                    </a>
                </li>
                <li class="nxl-item">
                    <a class="nxl-link" href="#">
                        <i class="bi bi-bar-chart-line me-2"></i> Reportes
                    </a>
                </li>
                <li class="nxl-item">
                    <a class="nxl-link" href="#">
                        <i class="bi bi-search me-2"></i> Consultas
                    </a>
                </li>
                <li class="nxl-item">
                    <a class="nxl-link" href="#">
                        <i class="bi bi-gear me-2"></i> Administración
                    </a>
                </li>

                {{-- Submenú de Parámetros --}}
                <li class="nxl-item nxl-hasmenu">
                    <a class="nxl-link">
                        <i class="bi bi-sliders me-2"></i>
                        <span>Parámetros</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('interactions.channels.index') }}">
                                <i class="bi bi-broadcast-pin me-2"></i> Canales
                            </a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="#">
                                <i class="bi bi-tags me-2"></i> Tipos
                            </a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="#">
                                <i class="bi bi-check2-circle me-2"></i> Resultados
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>

        {{-- Sección de Cartas --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('cartera.morosos.index') }}">
                <i class="bi bi-envelope-paper me-2"></i> Cartas
            </a>
        </li>
    </ul>
</li>
