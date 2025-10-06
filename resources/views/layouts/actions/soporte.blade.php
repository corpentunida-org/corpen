<li class="nxl-item nxl-hasmenu">
    <a class="nxl-link" href="javascript:void(0)">
        <span class="nxl-micon"><i class="bi bi-headset"></i></span>
        <span class="nxl-mtext">Soporte</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>
    <ul class="nxl-submenu">
        {{-- Crear Soporte --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('soportes.soportes.create') }}">
                Crear Soporte
            </a>
        </li>

        {{-- Lista de Mis Soportes --}}
        <li class="nxl-item nxl-hasmenu">
            <a class="nxl-link" href="{{ route('soportes.soportes.index', ['estado' => 'resuelto']) }}">
                Mi Lista
            </a>
        </li>

        {{-- Lista de Mis Soportes --}}
{{--         <li class="nxl-item nxl-hasmenu">
            <a class="nxl-link" href="{{ route('soportes.pendientes', ['estado' => 'resuelto']) }}">
                Lista Soportes
            </a>
        </li>
 --}}
        {{-- Parámetros de Soportes --}}
@candirect('soporte.lista.administrador')
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('soportes.tablero') }}">
                Parámetros de Soportes
            </a>
        </li>
@endcandirect
    </ul>
</li>
