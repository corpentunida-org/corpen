<li class="nxl-item nxl-hasmenu {{ request()->routeIs('archivo.cargo.*', 'archivo.area.*', 'archivo.empleado.*', 'archivo.categoria.*', 'archivo.gdotipodocumento.*', 'archivo.gdodocsempleados.*', 'archivo.funcion.*', 'visitas.corpen.*') ? 'active' : '' }}">
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-collection"></i></span>
        <span class="nxl-mtext">Gestión Documental</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        <li class="nxl-item {{ request()->routeIs('archivo.empleado.index') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('archivo.empleado.index') }}">
                <span class="nxl-micon"><i class="bi bi-person"></i></span>
                Empleados
            </a>
        </li>
        <li class="nxl-item {{ request()->routeIs('archivo.cargo.index') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('archivo.cargo.index') }}">
                <span class="nxl-micon"><i class="bi bi-briefcase"></i></span>
                Cargos
            </a>
        </li>
        <li class="nxl-item {{ request()->routeIs('archivo.funcion.*') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('archivo.funcion.index') }}">
                <span class="nxl-micon"><i class="bi bi-list-check"></i></span>
                Funciones
            </a>
        </li>
        <li class="nxl-item {{ request()->routeIs('archivo.area.index') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('archivo.area.index') }}">
                <span class="nxl-micon"><i class="bi bi-grid"></i></span>
                Áreas
            </a>
        </li>
        <li class="nxl-item {{ request()->routeIs('archivo.categorias.index') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('archivo.categorias.index') }}">
                <span class="nxl-micon"><i class="bi bi-tags"></i></span>
                Categorías de Documentos
            </a>
        </li>
        <li class="nxl-item {{ request()->routeIs('archivo.gdotipodocumento.index') ? 'active' : '' }}">
            <a class="nxl-link" href="{{ route('archivo.gdotipodocumento.index') }}">
                <span class="nxl-micon"><i class="bi bi-file-earmark-text"></i></span>
                Tipo de Documento
            </a>
        </li>
    </ul>
</li>