<li class="nxl-item nxl-hasmenu {{ request()->routeIs('archivo.cargo.*', 'archivo.area.*', 'archivo.empleado.*', 'archivo.gdotipodocumento.*', 'archivo.gdodocsempleados.*') ? 'active' : '' }}">
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-collection"></i></span>
        <span class="nxl-mtext">Gestión Documental</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        {{-- Submenú para Empleados y Documentos --}}
        <li class="nxl-item nxl-hasmenu {{ request()->routeIs('archivo.empleado.*', 'archivo.cargo.*', 'archivo.area.*', 'archivo.gdotipodocumento.*', 'archivo.gdodocsempleados.*') ? 'active' : '' }}">
            <a href="javascript:void(0);" class="nxl-link">
                Empleados & Documentos
                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
            </a>
            <ul class="nxl-submenu">
                <li class="nxl-item {{ request()->routeIs('archivo.empleado.index') ? 'active' : '' }}">
                    <a class="nxl-link" href="{{ route('archivo.empleado.index') }}" style="text-decoration: none;">
                        Empleados
                    </a>
                </li>
                
                <li class="nxl-item {{ request()->routeIs('archivo.cargo.index') ? 'active' : '' }}">
                    <a class="nxl-link" href="{{ route('archivo.cargo.index') }}" style="text-decoration: none;">
                        Cargos
                    </a>
                </li>

                <li class="nxl-item {{ request()->routeIs('archivo.area.index') ? 'active' : '' }}">
                    <a class="nxl-link" href="{{ route('archivo.area.index') }}" style="text-decoration: none;">
                        Áreas
                    </a>
                </li>

                <li class="nxl-item {{ request()->routeIs('archivo.gdotipodocumento.index') ? 'active' : '' }}">
                    <a class="nxl-link" href="{{ route('archivo.gdotipodocumento.index') }}" style="text-decoration: none;">
                        Tipo de Documento
                    </a>
                </li>

                <li class="nxl-item {{ request()->routeIs('archivo.gdodocsempleados.index') ? 'active' : '' }}">
                    <a class="nxl-link" href="{{ route('archivo.gdodocsempleados.index') }}" style="text-decoration: none;">
                        Documentos
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</li>
