<li class="nxl-item nxl-hasmenu">
    <a class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-diagram-3-fill"></i></span>
        <span class="nxl-mtext">Cartera</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>
    <ul class="nxl-submenu">
        {{-- Menú de Interacciones con submenú --}}
        <li class="nxl-item nxl-hasmenu">
            <a class="nxl-link">
                Interacciones
                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
            </a>
            <ul class="nxl-submenu">
                <li class="nxl-item">
                    <a class="nxl-link" href="{{ route('interactions.create') }}">
                        2.1. Crear
                    </a>
                </li>
                <li class="nxl-item">
                    <a class="nxl-link" href="{{ route('interactions.store') }}">
                        2.2. Mis Interacción
                    </a>
                </li>
                <li class="nxl-item">
                    <a class="nxl-link" href="{{ route('interactions.store') }}">
                        2.3. Informe
                    </a>
                </li>
                <li class="nxl-item">
                    <a class="nxl-link" href="#">
                        2.4. Consultas
                    </a>
                </li>
                <li class="nxl-item">
                    <a class="nxl-link" href="#">
                        2.5. Administrador
                    </a>
                </li>
            </ul>
        </li>

        {{-- Cartas --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('cartera.morosos.index') }}">
                Cartas
            </a>
        </li>
    </ul>
</li>
