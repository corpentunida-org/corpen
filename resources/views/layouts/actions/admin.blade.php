<li class="nxl-item nxl-hasmenu">
    <a class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-people-fill"></i></span>
        <span class="nxl-mtext">Admin</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>
    <ul class="nxl-submenu">
        <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.users.index')}}">Usuarios</a></li>        
        <li class="nxl-item"><a href="{{route('admin.roles.index')}}" class="nxl-link">Roles y Permisos</a></li>
        <li class="nxl-item"><a href="{{route('admin.auditoria.index')}}" class="nxl-link">Auditoria</a></li>
    </ul>
</li>
