<li class="nxl-item nxl-hasmenu">
    <a class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-house-heart-fill"></i></span>
        <span class="nxl-mtext">Exequiales</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>
    <ul class="nxl-submenu">
        <li class="nxl-item"><a class="nxl-link" href="{{ route('exequial.asociados.index')}}">Titulares</a></li>
        @can('exequial.prestarServicio.index')
        <li class="nxl-item"><a class="nxl-link" href="{{ route('exequial.prestarServicio.index')}}" >Prestar Servicio</a></li>
        @endcan
    </ul>
</li>
