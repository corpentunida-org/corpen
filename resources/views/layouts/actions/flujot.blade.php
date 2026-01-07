<li class="nxl-item nxl-hasmenu {{ request()->routeIs('flujo.*') ? 'active' : '' }}">
    {{-- Mantenemos la estructura original EXACTA para que el CSS de la plantilla alinee todo --}}
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-grid-1x2-fill"></i></span>
        <span class="nxl-mtext">Flujo de Trabajo</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
        
        {{-- ESTE ES EL TRUCO: Un link absoluto que solo cubre el icono y el texto, dejando la flecha libre --}}
        <object>
            <a href="{{ route('flujo.tablero') }}" 
               style="position: absolute; top: 0; left: 0; width: 85%; height: 100%; z-index: 5;">
            </a>
        </object>
    </a>

    <ul class="nxl-submenu">
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

        <li class="nxl-item-separator" style="height: 1px; background: rgba(0,0,0,0.04); margin: 10px 20px;"></li>

        <li class="nxl-item {{ request()->routeIs('flujo.workflows.index') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('flujo.workflows.index') }}">
                <i class="bi bi-folder2-open me-2"></i> Proyectos Activos
            </a>
        </li>

        <li class="nxl-item {{ request()->routeIs('flujo.tasks.index') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('flujo.tasks.index') }}">
                <i class="bi bi-check2-square me-2"></i> Centro de Tareas
            </a>
        </li>
    </ul>
</li>