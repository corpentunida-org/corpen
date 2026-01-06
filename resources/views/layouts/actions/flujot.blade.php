<li class="nxl-item nxl-hasmenu">
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-kanban"></i></span>
        <span class="nxl-mtext">Flujo de trabajo</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">

        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('flujo.workflows.create') }}" style="color: var(--primary-indigo); font-weight: 600;">
                <i class="bi bi-plus-circle-dotted me-2"></i> Nuevo Proyecto
            </a>
        </li>

        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('flujo.tablero') }}">
                <i class="bi bi-grid-1x2 me-2"></i> Tablero General
            </a>
        </li>

        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('flujo.workflows.index') }}">
                <i class="bi bi-stack me-2"></i> Lista de Proyectos
            </a>
        </li>
    </ul>
</li>