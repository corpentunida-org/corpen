<li class="nxl-item nxl-hasmenu {{ request()->routeIs('flujo.*') ? 'active' : '' }}">
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-grid-1x2-fill"></i></span>
        <span class="nxl-mtext">Flujo de Trabajo</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        {{-- TABLERO --}}
        <li class="nxl-item {{ request()->routeIs('flujo.tablero') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('flujo.tablero') }}">
                <i class="bi bi-speedometer2 me-2"></i> Tablero Principal
            </a>
        </li>

        {{-- SECCIÓN: CREACIÓN --}}
        <li class="nxl-item-separator" style="height: 1px; background: rgba(0,0,0,0.04); margin: 5px 20px;"></li>

        <li class="nxl-item {{ request()->routeIs('flujo.workflows.create') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('flujo.workflows.create') }}" style="color: var(--primary-indigo); font-weight: 600;">
                <i class="bi bi-plus-square me-2"></i> Nuevo Proyecto
            </a>
        </li>

        <li class="nxl-item {{ request()->routeIs('flujo.tasks.create') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('flujo.tasks.create') }}" style="color: var(--primary-indigo); font-weight: 600;">
                <i class="bi bi-plus-circle me-2"></i> Nueva Tarea
            </a>
        </li>

        {{-- SECCIÓN: GESTIÓN --}}
        <li class="nxl-item-separator" style="height: 1px; background: rgba(0,0,0,0.04); margin: 10px 20px;"></li>

        <li class="nxl-item {{ request()->routeIs('flujo.workflows.index') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('flujo.workflows.index') }}">
                <i class="bi bi-folder2-open me-2"></i> Centro de Proyectos
            </a>
        </li>

        <li class="nxl-item {{ request()->routeIs('flujo.tasks.index') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('flujo.tasks.index') }}">
                <i class="bi bi-check2-square me-2"></i> Centro de Tareas
            </a>
        </li>

        {{-- NUEVA SECCIÓN: REPORTES / AUDITORÍA --}}
        <li class="nxl-item {{ request()->routeIs('flujo.auditoria.index') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('flujo.auditoria.index') }}">
                <i class="bi bi-clock-history me-2"></i> Auditoría Global
            </a>
        </li>
    </ul>
</li>